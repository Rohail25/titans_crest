<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\WalletController;
use App\Http\Controllers\User\DepositController;
use App\Http\Controllers\User\WithdrawalController;
use App\Http\Controllers\User\ReferralController;
use App\Http\Controllers\User\AnalyticsController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PackageController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\FundManagementController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\EmailLogController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ReferralCommissionController;
use App\Http\Controllers\Admin\ProfitSharingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/terms-and-conditions', function () {
    return view('terms-and-conditions');
})->name('terms-and-conditions');

// Authentication Routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');

Route::get('/register', function () {
    return view('auth.register', ['referralCode' => request('ref')]);
})->name('register')->middleware('guest');

Route::get('/ref/{code}', function (string $code) {
    return redirect()->route('register', ['ref' => strtoupper($code)]);
})->middleware('guest')->name('referral.landing');

Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register'])->middleware('guest');

Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/logout', function () {
    return redirect('/');
})->middleware('guest');

// User Dashboard Routes (Authenticated)
Route::middleware(['auth'])->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Wallet Management
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet');
    
    // Deposits
    Route::get('deposits', [DepositController::class, 'index'])->name('deposit.index');
    Route::post('deposits', [DepositController::class, 'store'])->name('deposit.store');
    
    // Withdrawals
    Route::get('withdrawals', [WithdrawalController::class, 'index'])->name('withdrawal.index');
    Route::post('withdrawals/initiate', [WithdrawalController::class, 'initiate'])->name('withdrawal.initiate');
    Route::post('withdrawals/verify-otp', [WithdrawalController::class, 'verifyOTP'])->name('withdrawal.verify-otp');
    Route::delete('withdrawals/{withdrawal}/cancel', [WithdrawalController::class, 'cancel'])->name('withdrawal.cancel');
    Route::get('withdrawals/calculator', [WithdrawalController::class, 'calculator'])->name('withdrawal.calculator');
    
    // Referrals
    Route::get('referrals', [ReferralController::class, 'index'])->name('referral');
    Route::get('team', [ReferralController::class, 'team'])->name('team');
    
    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');
    
    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Package Subscription
    Route::post('packages/{package}/subscribe', [PackageController::class, 'subscribe'])->name('package.subscribe');
});

// Admin Routes (Authenticated & Admin Role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Withdrawals Management
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
        Route::get('{id}', [AdminWithdrawalController::class, 'show'])->name('show');
        Route::post('{id}/approve', [AdminWithdrawalController::class, 'approve'])->name('approve');
        Route::post('{id}/reject', [AdminWithdrawalController::class, 'reject'])->name('reject');
    });
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('{id}', [UserManagementController::class, 'show'])->name('show');
        Route::post('{id}/ban', [UserManagementController::class, 'ban'])->name('ban');
        Route::post('{id}/activate', [UserManagementController::class, 'activate'])->name('activate');
        Route::post('{id}/add-credit', [UserManagementController::class, 'addCredit'])->name('add-credit');
    });
    
    // Fund Management
    Route::prefix('fund-management')->name('fund-management.')->group(function () {
        Route::get('/', [FundManagementController::class, 'index'])->name('index');
        Route::post('add', [FundManagementController::class, 'addFunds'])->name('add');
        Route::post('deduct', [FundManagementController::class, 'deductFunds'])->name('deduct');
        Route::post('convert', [FundManagementController::class, 'convertSuspicious'])->name('convert');
        Route::get('ledger/{userId}', [FundManagementController::class, 'showLedger'])->name('ledger');
    });
    
    // Settings & Configuration
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [ConfigurationController::class, 'index'])->name('index');
        Route::post('update', [ConfigurationController::class, 'updateSettings'])->name('update');
        Route::post('packages/{id}/update', [ConfigurationController::class, 'updatePackage'])->name('update-package');
    });
    
    // Deposits Management
    Route::prefix('deposits')->name('deposits.')->group(function () {
        Route::patch('{deposit}/confirm', [AdminDepositController::class, 'confirmDeposit'])->name('confirm');
        Route::patch('{deposit}/reject', [AdminDepositController::class, 'rejectDeposit'])->name('reject');
    });
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('users', [ReportController::class, 'users'])->name('users');
        Route::get('deposits', [ReportController::class, 'deposits'])->name('deposits');
        Route::get('withdrawals', [ReportController::class, 'withdrawals'])->name('withdrawals');
        Route::get('earnings', [ReportController::class, 'earnings'])->name('earnings');
        Route::get('daily', [ReportController::class, 'daily'])->name('daily');
    });
    
    // Email Logs
    Route::prefix('logs/email')->name('email-logs.')->group(function () {
        Route::get('/', [EmailLogController::class, 'index'])->name('index');
        Route::get('{id}', [EmailLogController::class, 'show'])->name('show');
        Route::get('/failed', [EmailLogController::class, 'failed'])->name('failed');
    });
    
    // Audit Logs
    Route::prefix('logs/audit')->name('audit-logs.')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::get('{id}', [AuditLogController::class, 'show'])->name('show');
        Route::get('admin/{adminId}', [AuditLogController::class, 'byAdmin'])->name('by-admin');
    });
    
    // Commission Management
    Route::prefix('commissions')->group(function () {
        Route::prefix('referral')->name('referral-commissions.')->group(function () {
            Route::get('/', [ReferralCommissionController::class, 'index'])->name('index');
            Route::put('/', [ReferralCommissionController::class, 'update'])->name('update');
        });
        
        Route::prefix('profit-sharing')->name('profit-sharing.')->group(function () {
            Route::get('/', [ProfitSharingController::class, 'index'])->name('index');
            Route::put('/', [ProfitSharingController::class, 'update'])->name('update');
        });
    });
    
    // Admin Profile Routes
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update', [ProfileController::class, 'update'])->name('profile.update');
});
