<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'referral_code',
        'referred_by',
        'role',
        'status',
        'ban_reason',
        'banned_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'banned_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    // Relations
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(Deposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    public function userPackages(): HasMany
    {
        return $this->hasMany(UserPackage::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'user_packages');
    }

    public function referralTree(): HasOne
    {
        return $this->hasOne(ReferralTree::class);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(ReferralTree::class, 'referrer_id');
    }

    public function otpRequests(): HasMany
    {
        return $this->hasMany(OtpRequest::class);
    }

    public function adminLogs(): HasMany
    {
        return $this->hasMany(AdminLog::class, 'admin_id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }
}
