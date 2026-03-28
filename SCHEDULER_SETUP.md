# Scheduler & Cron Job Setup Guide

This document explains how the automated reward distribution system works and how to set up cron jobs for production deployment.

---

## 📅 Scheduler Overview

Titans Crest uses Laravel's built-in task scheduler to run three automated distribution commands. The scheduler is defined in `bootstrap/app.php` and requires a single system cron job to trigger it.

### Commands & Schedules

| Command | Frequency | Purpose | Timezone |
|---------|-----------|---------|----------|
| `profits:distribute` | Every minute | Processes due profit distributions for active packages | Asia/Karachi |
| `leadership-performance:distribute` | Every minute | Processes and pays due leadership performance bonuses | Asia/Karachi |
| `monthly-performance:distribute` | Day 1, 00:00 | Processes and pays monthly performance excellence bonuses | Asia/Karachi |

---

## ⏰ Detailed Schedule Breakdown

### 1. Leadership Performance Rewards (Minute-Based)
- **Runs:** Every minute
- **Logic:**
  - Checks all users with a `next_payout_at` due date
  - If due date ≤ now, calculates leadership bonus and credits wallet
  - Updates `next_payout_at` to 30 days from now
  - Idempotent (safe to run multiple times; won't double-pay)
- **Example:** If a user's next_payout_at is March 25, 2026 10:00 AM, the reward will be paid on the next minute-run after 10:00 AM

### 2. Monthly Performance Excellence Rewards
- **Runs:** Day 1 of every month at 00:00 AM (Asia/Karachi time / Midnight)
- **Logic:**
  - **NOT at end of month** — runs on the FIRST day to process the CLOSED previous month
  - Example: April 1 at 00:00 (midnight) processes all qualifying leaders for the March month
  - Calculates total volumes and qualified direct legs for entire month
  - Matches against 7-tier reward matrix (4K to 300K+ volume tiers)
  - Credits wallet for verified qualifying tiers
  - Marks records as `paid`, `not_qualified`, or `qualified_skipped` (earning cap hit)
  - Idempotent: re-running same month won't double-pay (unique constraint prevents duplicates)

### 3. Profit Distribution (Minute-Based)
- **Runs:** Every minute
- **Note:** Covered by existing system; included here for reference

---

## 🔧 Manual Command Testing

You can test each command manually without waiting for the scheduler:

```bash
# Test leadership performance (runs for all due rewards)
php artisan leadership-performance:distribute

# Test monthly performance for a specific month
php artisan monthly-performance:distribute --month=2026-03

# Test monthly performance for last closed month (auto-calculated)
php artisan monthly-performance:distribute

# Test profit distribution
php artisan profits:distribute
```

---

## 🖥️ Production Cron Setup

To enable the scheduler in production, add **one single cron job** to the server's crontab. This one entry triggers the Laravel scheduler every minute, which then dispatches commands according to their defined schedules.

### Step 1: SSH into Production Server
```bash
ssh user@your-production-server.com
```

### Step 2: Open Crontab Editor
```bash
crontab -e
```

### Step 3: Add the Scheduler Cron Entry

Add this single line to the crontab (adjust the path `/var/www/titans_crest` to match your project root):

```cron
* * * * * cd /var/www/titans_crest && php artisan schedule:run >> /dev/null 2>&1
```

**Breakdown:**
- `* * * * *` = Every minute (5 asterisks = minute, hour, day of month, month, day of week)
- `cd /var/www/titans_crest` = Navigate to project root
- `php artisan schedule:run` = Trigger the Laravel scheduler
- `>> /dev/null 2>&1` = Suppress output (optional; omit to log to file)

### Step 4: Verify Crontab Entry
```bash
crontab -l
```

You should see your entry listed.

---

## 📊 Execution Timeline Example

For a system starting March 28, 2026:

| Date/Time | Command | Action |
|-----------|---------|--------|
| March 28 10:15 AM | `leadership-performance:distribute` | Checks for due rewards (minute-based) |
| March 28 10:16 AM | `leadership-performance:distribute` | Checks for due rewards (minute-based) |
| ... (every minute) | ... | ... |
| March 29 00:00 AM | `monthly-performance:distribute` | **No action** (day 1 not reached) |
| March 31 11:59 PM | `leadership-performance:distribute` | Still checking minute-based rewards |
| **April 1 00:00 AM** | **`monthly-performance:distribute`** | **PROCESSES ENTIRE MARCH** — calculates volumes, matches tiers, credits wallets |
| April 1 00:11 AM | `profits:distribute` | Regular minute check |
| April 1 00:11 AM | `leadership-performance:distribute` | Regular minute check |
| ... (every minute) | ... | ... |

---

## 📝 Logging & Monitoring

All scheduler commands log their results automatically:

### Log Location
```
storage/logs/laravel.log
```

### Example Log Entries
```
[2026-04-01 00:00:15] local.INFO: Monthly performance excellence distribution cycle completed successfully
[2026-04-01 00:00:15] local.INFO: 9 leaders scanned, 2 paid, 6 not_qualified, 1 qualified_skipped, 0 errors

[2026-03-28 10:15:30] local.INFO: Leadership performance distribution cycle completed successfully
```

### Monitoring Commands

Check last 50 lines of logs:
```bash
tail -50 storage/logs/laravel.log
```

Search for monthly performance runs:
```bash
grep "Monthly performance excellence" storage/logs/laravel.log
```

---

## ✅ Verification Checklist

After deploying to production:

- [ ] **Migrations run:** `php artisan migrate` completed successfully
- [ ] **Cron job added:** `crontab -l` shows the scheduler entry
- [ ] **Timezone correct:** Verify `config/app.php` has `'timezone' => 'Asia/Karachi'`
- [ ] **Log directory writable:** `chmod 775 storage/logs/`
- [ ] **Manual test passed:** Run `php artisan monthly-performance:distribute --month=2026-02` (if data exists)
- [ ] **Scheduler verified:** After 1 minute, check `storage/logs/laravel.log` for "schedule:run" entries
- [ ] **First month-close monitored:** April 1 at 00:00-00:05, check logs for monthly distribution results

---

## 🚨 Troubleshooting

### Scheduler Not Running
**Problem:** Commands not executing at scheduled times.

**Solution:**
1. Verify cron job exists: `crontab -l`
2. Check cron daemon is running: `ps aux | grep crond`
3. Check system mail for cron errors: `mail` command
4. Manually test: `php artisan schedule:run`

### Wrong Timezone
**Problem:** Commands running at unexpected times.

**Solution:**
1. Verify server timezone: `date` or `timedatectl status`
2. Confirm Laravel config: `php artisan tinker` → `config('app.timezone')`
3. Update `.env` if needed and restart services

### Missing Dependencies
**Problem:** "Class not found" or "Call to undefined method" errors.

**Solution:**
```bash
composer install --no-dev
composer optimize-autoloader
php artisan route:clear
php artisan optimize:clear
```

### Earning Cap Prevents Payment
**Problem:** Command shows `qualified_skipped` status in logs.

**Explanation:** User hit 3x earnings cap. System correctly marked record as `qualified_skipped` instead of paying (prevents cap violation). Check wallet ledger for user's earning total.

---

## 📚 Related Files

- `bootstrap/app.php` — Scheduler configuration (withSchedule closure)
- `app/Console/Commands/DistributeMonthlyPerformanceExcellence.php` — Monthly bonus command
- `app/Console/Commands/DistributeLeadershipPerformance.php` — Leadership reward command
- `app/Services/MonthlyPerformanceExcellenceService.php` — Monthly bonus business logic
- `config/app.php` — Timezone setting (Asia/Karachi)

---

## 🔗 Quick Links

- [Laravel Scheduler Docs](https://laravel.com/docs/11.x/scheduling)
- [Linux Crontab Syntax](https://crontab.guru/)
- [Timezone List](https://en.wikipedia.org/wiki/List_of_tz_database_time_zones)

