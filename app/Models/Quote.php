<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class Quote extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'reference_id',
        'status',
        'subtotal',
        'tax_mode', // NEW
        'tax_config_snapshot', // NEW
        'tax_amount',
        'total_amount',
        'valid_until',
        'notes',
        'discount_percentage',
        'discount_amount',
        'delivery_date',
        'delivery_time',
        'delivery_partner',
        'tracking_number',
        'delivery_status',
        'delivery_note',
        'template_name',
        'display_settings',
        'custom_fields',
        'terms',
        'total_cost',
        'profit_amount',
        'delivery_charge',
        'additional_charge',
        'additional_charge_label',
        'accepted_at',
        'payment_status',
        'payment_method',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'tax_config_snapshot' => 'array',
        'delivery_date' => 'date',
        'delivery_time' => 'string',
        'display_settings' => 'array',
        'custom_fields' => 'array',
        'total_cost' => 'decimal:2',
        'profit_amount' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'additional_charge' => 'decimal:2',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($quote) {
            if (empty($quote->reference_id)) {
                $quote->reference_id = 'QT-' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }
}
