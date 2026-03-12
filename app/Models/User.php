<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasTenant;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role',
        'is_superadmin',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_superadmin' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'encrypted:array',
        ];
    }

    /**
     * Override the password reset notification to use MultiSmtpMailer.
     */
    public function sendPasswordResetNotification($token)
    {
        $notification = new \Illuminate\Auth\Notifications\ResetPassword($token);
        app(\App\Services\Mail\MultiSmtpMailer::class)->send($this, $notification);
    }

    /**
     * Override the email verification notification to use MultiSmtpMailer.
     */
    public function sendEmailVerificationNotification()
    {
        $notification = new \Illuminate\Auth\Notifications\VerifyEmail;
        app(\App\Services\Mail\MultiSmtpMailer::class)->send($this, $notification);
    }

    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_secret) && !is_null($this->two_factor_confirmed_at);
    }

    // ── Role helpers ──────────────────────────────────────────────
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_superadmin;
    }

    public function isBoss(): bool
    {
        return $this->role === 'boss';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    // ── Relationships ─────────────────────────────────────────────
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }
}
