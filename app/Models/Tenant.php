<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'plan_id',
        'trial_ends_at',
        'subscription_ends_at',
        'is_active',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function hasReachedLimit(string $limitType): bool
    {
        if (!$this->plan) {
            return false;
        }

        return match ($limitType) {
            'quotes' => $this->quotes()->count() >= $this->plan->max_quotes,
            'products' => $this->products()->count() >= $this->plan->max_products,
            'users' => $this->users()->count() >= $this->plan->max_users,
            default => false,
        };
    }
}
