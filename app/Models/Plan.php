<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'currency',
        'max_users',
        'max_products',
        'max_quotes',
        'is_active',
        'allow_email_notifications',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_email_notifications' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
