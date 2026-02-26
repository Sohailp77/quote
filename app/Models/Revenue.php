<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;
    protected $fillable = [
        'quote_id',
        'stock_adjustment_id',
        'amount',
        'currency',
        'recorded_at',
        'reverted_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'reverted_at' => 'datetime',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function stockAdjustment()
    {
        return $this->belongsTo(StockAdjustment::class);
    }
}
