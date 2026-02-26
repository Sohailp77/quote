<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'quantity',
        'unit_cost',
        'status',
        'estimated_arrival',
        'received_at',
    ];

    protected $casts = [
        'estimated_arrival' => 'date',
        'received_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
