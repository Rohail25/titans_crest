# Titans Crest - Investment Platform
## Professional Fintech Dashboard with Referral System

### 🚀 Project Overview

Titans Crest is a complete investment platform built with Laravel featuring:
- Modern financial dashboard with clean UI
- Multi-level referral system
- Automated profit distribution
- Secure wallet management
- Withdrawal requests with OTP verification
- 3x earnings cap enforcement
- Ledger-based transaction tracking (immutable records)

---

## 📋 Setup Instructions

### 1. Install Dependencies
```bash
composer install
npm install
```

### 2. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=titans_crest
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Seed Database (Optional - Creates test user and packages)
```bash
php artisan db:seed
```

Test User Credentials:
- Email: `test@example.com`
- Password: `password123`

### 5. Build Assets
```bash
npm run build
```

For development (with hot reload):
```bash
npm run dev
```

### 6. Start Development Server
```bash
php artisan serve
```

Access dashboard: `http://localhost:8000/dashboard`

---

## 📁 Project Structure

### Controllers
- `app/Http/Controllers/User/`
  - `DashboardController` - Main dashboard overview
  - `WalletController` - Wallet summary
  - `DepositController` - Deposit management
  - `WithdrawalController` - Withdrawal requests
  - `ReferralController` - Referral management
  - `AnalyticsController` - Analytics and reports

### Services (Business Logic)
- `app/Services/`
  - `WalletService` - Wallet operations, 3x cap enforcement
  - `DepositService` - Deposit processing
  - `WithdrawalService` - Withdrawal validation & processing
  - `ReferralService` - Referral tree management
  - `ProfitService` - Profit calculation & distribution
  - `OTPService` - OTP generation & verification

### Models
- `User` - User authentication & relationships
- `Wallet` - User wallet with balances
- `Deposit` - Deposit transactions
- `Withdrawal` - Withdrawal requests
- `Earning` - Immutable ledger entries
- `Package` - Investment packages
- `UserPackage` - User's active packages
- `ReferralTree` - Referral hierarchy
- `OtpRequest` - One-time passwords
- `Setting` - System configuration

### Views
- `resources/views/layouts/user.blade.php` - Master layout with sidebar
- `resources/views/user/dashboard.blade.php` - Dashboard
- `resources/views/user/wallet/` - Wallet pages
- `resources/views/user/deposit/` - Deposit pages
- `resources/views/user/withdrawal/` - Withdrawal pages
- `resources/views/user/referral/` - Referral pages
- `resources/views/user/analytics/` - Analytics pages

### Database
- `database/migrations/` - All table migrations
  - `wallets_table` - User wallets
  - `packages_table` - Investment packages
  - `user_packages_table` - User package assignments
  - `deposits_table` - Deposit records
  - `withdrawals_table` - Withdrawal requests
  - `earnings_table` - Immutable ledger
  - `referral_tree_table` - Referral relationships
  - `otp_requests_table` - OTP storage
  - `settings_table` - System settings

---

## 🔐 Security Features

### Wallet Security
- **Bcrypt Password Hashing** - All passwords hashed
- **DB Transactions** - All financial operations atomic
- **Immutable Ledger** - Earnings entries cannot be updated/deleted
- **OTP Verification** - Required for withdrawals
- **Rate Limiting** - Withdrawal limits enforced
- **3x Cap Enforcement** - Automatic earnings limit

### Input Validation
- **Form Request Validation** - All inputs validated
- **CSRF Protection** - All forms protected
- **Type Casting** - Decimal numbers for financial data
- **Constraint Checks** - Database constraints

---

## 💰 Financial Rules Implementation

### 3x Cap Rule
```
Maximum Earnings = Total Deposit × 3
- Enforced in WalletService::has3xCapReached()
- Checked before every profit credit
- Prevents earnings beyond cap
```

### Daily Profit Distribution
```
Daily Profit = Package Price × Daily Profit Rate
- Default: 1.67% daily (50% monthly)
- Scheduled command: profits:distribute
- Respects 3x cap limit
- Only credits if cap not reached
```

### Withdrawal Rules
```
Minimum: $10
Fee: 5% deduction
Status Flow: pending_otp → pending_approval → approved
OTP: 6-digit, 5-minute expiry
Cannot duplicate pending for same user
```

### Suspicious Funds Logic
```
- Marked when certain conditions met
- Cannot withdraw directly
- Can convert after generating 3x earnings from them
- Tracked separately in ledger
```

### Referral System
```
Commission: 10% of referred user's package price
- Direct referrals only
- Credited immediately
- Stored in ReferralTree
- Multi-level tracking
```

---

## 🤖 Scheduled Tasks

### Profit Distribution (Daily at 1 AM UTC)
```bash
php artisan profits:distribute
```
- Calculates daily profits
- Respects 3x cap
- Creates ledger entries
- Handles failures gracefully

### Manual Command Usage
```bash
# Distribute to all users
php artisan profits:distribute

# Distribute to specific users
php artisan profits:distribute --users=1 --users=2 --users=3
```

---

## 🔄 API Routes

### User Dashboard Routes
All routes require `auth` and `verified` middleware under `/dashboard` prefix:

```
GET  /dashboard              → Dashboard overview
GET  /dashboard/wallet       → Wallet view
GET  /dashboard/deposits     → Deposits page
POST /dashboard/deposits     → Submit deposit
GET  /dashboard/withdrawals  → Withdrawals page
POST /dashboard/withdrawals/initiate      → Start withdrawal
POST /dashboard/withdrawals/verify-otp    → Verify OTP
DELETE /dashboard/withdrawals/{id}/cancel → Cancel withdrawal
GET  /dashboard/withdrawals/calculator    → Calculate fees (AJAX)
GET  /dashboard/referrals    → Referral page
GET  /dashboard/analytics    → Analytics page
```

---

## 📊 Dashboard Features

### Overview Widgets
- **Available Balance** - Primary card with current balance
- **Pending Balance** - Awaiting confirmation
- **Total Deposited** - Sum of confirmed deposits
- **Total Earned** - Sum of earnings
- **3x Cap Progress** - Visual progress bar
- **Active Packages** - Current investment packages
- **Team Size** - Direct & total referrals
- **Withdrawal Status** - Pending & approved

### Charts & Analytics
- **Earnings Trend** - Last 6 months line chart
- **Monthly Breakdown** - Bar chart by month
- **ROI Percentage** - Return on investment
- **Profit Summary** - Daily, monthly, yearly

### Tables
- **Recent Earnings** - Last 5 transactions
- **Deposit History** - All deposits with status
- **Withdrawal History** - All withdrawal requests
- **Downline List** - All referrals with details

---

## 🎨 UI/UX Design

### Color Scheme (Professional Fintech)
- **Primary Dark**: `#0f172a` - Sidebar background
- **Primary Blue**: `#1e40af` - Accent elements
- **Gold Accent**: `#fbbf24` - Highlights
- **Success**: `#10b981` - Positive indicators
- **Danger**: `#ef4444` - Alerts
- **Light**: `#f8fafc` - Background

### Components
- **Sidebar Navigation** - Fixed, with active states
- **Top Navigation** - Sticky header with user menu
- **Stat Cards** - KPI displays with icons
- **Progress Bars** - Cap progress visualization
- **Tables** - Responsive with hover effects
- **Forms** - Clean inputs with validation
- **Modals** - OTP verification popup
- **Alerts** - Success/error notifications

### Responsive Design
- Desktop (1200px+) - Full layout with sidebar
- Tablet (768px-1199px) - Adjusted spacing
- Mobile (below 768px) - Hamburger menu, stacked layout

---

## 🧪 Testing

### Manual Testing Checklist

#### Deposit Flow
- [ ] Create deposit with valid BNB amount
- [ ] Verify tx_hash is required and unique
- [ ] Check deposit appears in history
- [ ] Confirm status shows as pending

#### Withdrawal Flow
- [ ] Request withdrawal > $10
- [ ] Verify 5% fee calculation
- [ ] OTP generated and sent
- [ ] OTP verification works
- [ ] Funds deducted from wallet
- [ ] Status changed to pending_approval

#### 3x Cap
- [ ] Create user with $100 deposit
- [ ] Daily profit credits (1.67%)
- [ ] Cap at $300 earnings
- [ ] Extra profits blocked after cap

#### Referral System
- [ ] Generate unique referral code
- [ ] Share code with new user
- [ ] Verify referrer recorded
- [ ] Commission credited (10%)
- [ ] Appears in downline

#### Security
- [ ] Cannot update earnings entries
- [ ] Cannot delete earnings entries
- [ ] Withdrawals require OTP
- [ ] Rate limiting on requests
- [ ] CSRF protection on forms

---

## 🐛 Debugging

### Enable Query Logging
In `.env`:
```
APP_DEBUG=true
DB_LOG=true
```

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Database Inspection
```bash
php artisan tinker
>>> \App\Models\User::with('wallet')->first()
>>> \App\Models\Earning::where('user_id', 1)->get()
```

### Profit Distribution Test
```bash
# Run manually
php artisan profits:distribute

# Run for specific user
php artisan profits:distribute --users=1

# Check logs
tail -f storage/logs/laravel.log
```

---

## 📝 Default Settings (from Seeder)

| Setting | Value | Purpose |
|---------|-------|---------|
| bnb_wallet_address | 0x742d35Cc... | Deposit address |
| referral_commission_percent | 10 | Referral reward |
| withdrawal_fee_percent | 5 | Withdrawal fee |
| otp_expiry_minutes | 5 | OTP validity |
| min_withdrawal_amount | 10 | Minimum withdrawal |

---

## 📦 Default Packages (from Seeder)

| Package | Price | Daily Rate | Category |
|---------|-------|-----------|----------|
| Starter | $100 | 1.67% | Entry level |
| Professional | $500 | 1.67% | Advanced |
| Premium | $1,000 | 1.67% | Premium |
| Elite | $5,000 | 1.67% | Maximum |

All packages are lifetime with no expiry.

---

## 🚨 Important Notes

1. **Production Deployment**
   - Hide admin routes/pages
   - Enable HTTPS only
   - Set `APP_DEBUG=false`
   - Use strong database passwords
   - Enable two-factor authentication
   - Regular security audits
   - Set up automated backups

2. **Blockchain Integration**
   - Currently using placeholder for BNB verification
   - Implement actual BNB Smart Chain verification
   - Use Web3.js or similar library
   - Validate tx_hash against blockchain
   - Implement smart contract if needed

3. **Email Notifications**
   - OTP currently logged to console
   - Implement actual email sending
   - Add SMS notifications for OTP
   - Deposit confirmations
   - Withdrawal updates

4. **Admin Panel**
   - Not included in this package (user side only)
   - Will need separate admin dashboard
   - Admin approval for withdrawals
   - Package management
   - Settings management
   - User verification

---

## 📞 Support

For issues or questions, ensure:
- All migrations have run
- Database seeded with defaults
- .env configuration is correct
- Cache cleared: `php artisan cache:clear`
- Routes cached: `php artisan route:cache`

---

## 📜 License

This project is proprietary. All rights reserved.

---

**Last Updated**: March 3, 2026
**Version**: 1.0.0 (Production-Ready Architecture)
