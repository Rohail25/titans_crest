# Developer Implementation Guide
## Titans Crest User Platform

### Quick Start for Developers

#### 1. Clone & Setup
```bash
git clone <repo>
cd titans_crest
composer install
npm install
cp .env.example .env
php artisan key:generate
```

#### 2. Database Setup
```bash
# Create database first
mysql -u root -p -e "CREATE DATABASE titans_crest;"

# Run migrations
php artisan migrate

# Seed defaults (packages, settings, test user)
php artisan db:seed
```

#### 3. Run Application
```bash
# Terminal 1: Server
php artisan serve

# Terminal 2: Asset build (optional, for Tailwind/Vite changes)
npm run dev

# Terminal 3: Queue (if implementing async jobs)
php artisan queue:work
```

#### 4. Access Dashboard
- URL: `http://localhost:8000/dashboard`
- Email: `test@example.com`
- Password: `password123`

---

## 🏗️ Architecture Overview

### Layered Architecture
```
Routes (api/web)
    ↓
Controllers (Request handling, Validation)
    ↓
Services (Business Logic, DB Transactions)
    ↓
Models (Database relations, Casting)
    ↓
Database (Migrations, Seeders)
```

### Request Flow Example: Withdrawal
```
Controller (WithdrawalController::initiate)
    ↓ Validates request
    ↓ Calls Service
Service (WithdrawalService::initiateWithdrawal)
    ↓ Validates withdrawal rules
    ↓ Creates DB transaction
    ↓ Calls WalletService
    ↓ Changes Withdrawal status
Model (Withdrawal model)
    ↓ Relationships to User
    ↓ Query builder
Database
    ↓ persistance
```

---

## 💼 Service Layer Responsibilities

### WalletService
- **Get/Create** wallet
- **Check 3x cap** reached
- **Get cap remaining**
- **Add balance** (with ledger)
- **Deduct balance** (with ledger)
- **Manage suspicious** balance
- **Get summary** for dashboard

**Key Methods**:
```php
getOrCreateWallet(User $user)
has3xCapReached(User $user): bool
get3xCapRemaining(User $user): float
addBalance(User $user, float $amount, string $type) // Creates earning
deductBalance(User $user, float $amount, string $reason) // Creates earning
getWalletSummary(User $user): array
```

### DepositService
- **Create deposit** record (pending)
- **Confirm deposit** (credit wallet, update status)
- **Reject deposit**
- **Get deposit** history & stats

**Key Methods**:
```php
createDeposit(User $user, float $amount, string $txHash)
confirmDeposit(Deposit $deposit) // DB transaction
rejectDeposit(Deposit $deposit, string $reason)
getUserDepositHistory(User $user)
getDepositStats(User $user): array
```

### WithdrawalService
- **Validate withdrawal** (amount, balance, rules)
- **Calculate deduction** (5%)
- **Initiate withdrawal** (create record, pending_otp)
- **Verify OTP** and lock funds (deduct from wallet)
- **Approve withdrawal** (admin action)
- **Reject withdrawal** (refund if needed)

**Key Methods**:
```php
validateWithdrawal(User $user, float $amount): array
calculateDeduction(float $amount): float
initiateWithdrawal(User $user, float $amount) // Status: pending_otp
verifyOTPAndLockFunds(Withdrawal $w, string $otp) // Deducts balance
approveWithdrawal(Withdrawal $w, string $address) // Admin only
rejectWithdrawal(Withdrawal $w, string $reason) // Refunds if needed
```

### ReferralService
- **Generate** referral code
- **Initialize** referral tree
- **Add referral** (on registration)
- **Get referral** tree structure
- **Get downline** list
- **Get statistics**

**Key Methods**:
```php
generateReferralCode(): string
initializeReferralTree(User $user, ?string $referrerCode)
addReferral(User $user) // Credit commission
getReferralStats(User $user): array
getDownline(User $user, int $level): array
```

### ProfitService
- **Calculate daily** profit
- **Distribute daily** profit
- **Distribute batch** (for scheduler)
- **Get profit** summary
- **Get active** packages

**Key Methods**:
```php
calculateDailyProfit(User $user): float
distributeDailyProfit(User $user) // Checks 3x cap
distributeProfitBatch(array $userIds) // For scheduler
getProfitSummary(User $user): array
getActivePackages(User $user): array
```

### OTPService
- **Generate OTP**
- **Verify OTP**
- **Get pending** OTP
- **Check if valid**
- **Resend OTP**
- **Get time remaining**

**Key Methods**:
```php
generateOTP(User $user, string $purpose): string
verifyOTP(int $userId, string $otp, string $purpose): bool
getPendingOTP(User $user, string $purpose): ?OtpRequest
isOTPValid(User $user, string $purpose): bool
getOTPTimeRemaining(OtpRequest $otp): ?int
```

---

## 🔑 Key Development Patterns

### 1. Database Transactions
Always use transactions for financial operations:
```php
DB::transaction(function () {
    // All operations here are atomic
    $wallet->decrement('balance', $amount);
    Earning::create([...]);
    // If any operation fails, all rollback
});
```

### 2. Service Injection
Use constructor injection in controllers:
```php
public function __construct(
    protected WalletService $walletService,
    protected DepositService $depositService,
){}
```

### 3. Ledger Pattern (Immutable)
All balance changes create ledger entries:
- Never update earnings entries
- Never delete earnings entries
- All transactions trail from earnings table
- Earning model prevents update/delete

### 4. Middleware Protection
All routes use middleware:
```php
Route::middleware(['auth', 'verified'])->group(function () {
    // User must be logged in and email verified
});
```

### 5. Type Casting
Model decimals are automatically cast:
```php
protected $casts = [
    'balance' => 'decimal:2', // Always 2 decimal places
    'amount' => 'decimal:2',
];
```

---

## 🧪 Testing the Features

### Test Deposit Flow
```php
// Create test user
$user = User::factory()->create();
$walletService = app(WalletService::class);
$depositService = app(DepositService::class);

// Create wallet
$wallet = $walletService->getOrCreateWallet($user);

// Create and confirm deposit
$deposit = $depositService->createDeposit($user, 100, 'tx_hash');
$depositService->confirmDeposit($deposit);

// Verify wallet updated
$wallet->refresh();
assert($wallet->balance === 100);
assert($wallet->total_deposit === 100);
```

### Test Withdrawal Flow
```php
$withdrawalService = app(WithdrawalService::class);
$otpService = app(OTPService::class);

// Initiate withdrawal
$withdrawal = $withdrawalService->initiateWithdrawal($user, 50);
assert($withdrawal->status === 'pending_otp');

// Generate and verify OTP
$otp = $otpService->generateOTP($user, 'withdrawal');
$otpService->verifyOTP($user->id, $otp, 'withdrawal');

// Verify and lock funds
$withdrawalService->verifyOTPAndLockFunds($withdrawal, $otp);
assert($withdrawal->status === 'pending_approval');
```

### Test Profit Distribution
```bash
# Run profit distribution manually
php artisan profits:distribute

# Check logs
tail storage/logs/laravel.log

# Verify earnings created
\App\Models\Earning::where('type', 'profit_share')->get();
```

### Test Referral System
```php
// Create referrer
$referrer = User::factory()->create();
$referralService = app(ReferralService::class);
$referralService->initializeReferralTree($referrer);

// Create referred user
$referred = User::factory()->create();
$referralService->initializeReferralTree($referred, $referrer->referralTree->referral_code);

// Verify relationship
assert($referred->referred_by === $referrer->id);
```

---

## 🔒 Security Checklist

### Code Level
- [ ] Use type hints on all methods
- [ ] Validate all inputs with FormRequest
- [ ] Use DB transactions for money
- [ ] Hash sensitive data with bcrypt
- [ ] Never log passwords/tokens
- [ ] Use CSRF protection on forms
- [ ] Escape output in views

### Database Level
- [ ] Use foreign key constraints
- [ ] Add proper indexes
- [ ] Never expose raw queries
- [ ] Use parameterized queries (Eloquent)
- [ ] Validate data types

### API Level
- [ ] Authenticate all endpoints
- [ ] Authorize user data access
- [ ] Rate limit sensitive endpoints
- [ ] Validate HTTP methods
- [ ] Return proper status codes

---

## 📊 Database Keys & Indexes

### Important Indexes to Verify
```
users: id, email
wallets: user_id (unique)
deposits: user_id, status, created_at
withdrawals: user_id, status, created_at
earnings: user_id, type, created_at
referral_tree: user_id (unique), referrer_id
otp_requests: user_id, purpose, status
```

---

## 🐛 Common Issues & Solutions

### Issue: 3x Cap Not Being Enforced
```php
// Check if WalletService is being used
// Verify has3xCapReached() is called before addBalance()
// Review WithdrawalService::validateWithdrawal()
```

### Issue: OTP Not Being Generated
```php
// Check if OTPService injected correctly
// Verify generateOTP() returns plain text OTP
// Check SMS/email service configuration
```

### Issue: Withdrawals Not Being Processed
```php
// Verify OTP verification step completed
// Check withdrawal status after OTP verify
// Ensure admin approval not required for test
```

### Issue: Referral Commission Not Crediting
```php
// Check ReferralService::addReferral() called on registration
// Verify WalletService::addBalance() with type='referral'
// Check referral_tree.commission_earned updated
```

---

## 📈 Performance Tips

1. **Use Eager Loading**
   ```php
   User::with('wallet', 'earnings', 'userPackages.package')->get();
   ```

2. **Cache Settings**
   ```php
   // Instead of DB query every time
   cache()->remember('setting.key', 3600, function () {
       return Setting::get('key');
   });
   ```

3. **Batch Operations**
   ```php
   // Profit distribution for many users
   $profitService->distributeProfitBatch($userIds);
   ```

4. **Index Frequently Queried Columns**
   ```php
   // Already done in migrations: user_id, status, type, created_at
   ```

---

## 🚀 Deployment Checklist

- [ ] Run migrations on server
- [ ] Seed database
- [ ] Set correct environment variables
- [ ] Set app key
- [ ] Enable HTTPS
- [ ] Configure mail for notifications
- [ ] Set up cron/scheduler for profit distribution
- [ ] Enable CSRF protection
- [ ] Test all workflows manually
- [ ] Monitor logs
- [ ] Set up backups
- [ ] Configure rate limiting

---

## 📚 References

### Relevant Laravel Docs
- [Database Transactions](https://laravel.com/docs/transactions)
- [Model Relationships](https://laravel.com/docs/relationships)
- [Form Requests](https://laravel.com/docs/requests#form-requests)
- [Task Scheduling](https://laravel.com/docs/scheduling)
- [Eloquent Casting](https://laravel.com/docs/eloquent-mutators)

### Project Files Quick Reference
- Main Routes: `routes/web.php`
- Services: `app/Services/`
- Controllers: `app/Http/Controllers/User/`
- Views: `resources/views/user/`
- Models: `app/Models/`
- Migrations: `database/migrations/`
- Scheduler: `app/Console/Kernel.php`
- Commands: `app/Console/Commands/`

---

**Version**: 1.0.0  
**Last Updated**: March 3, 2026
