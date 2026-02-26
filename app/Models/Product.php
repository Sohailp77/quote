<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'sku',
        'image_path',
        'stock_quantity',
        'unit_size',
        'specifications',
        'tax_rate_id',
    ];

    protected $casts = [
        'specifications' => 'array',
        'unit_size' => 'decimal:2',
        'stock_quantity' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function taxRate()
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class)->latest();
    }

    // ── Helpers ───────────────────────────────────────────────────
    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock_quantity <= $threshold;
    }

    public function adjustStock(int $change, string $type, string $reason, int $userId, ?int $quoteId = null, ?float $unitCost = null): StockAdjustment
    {
        $this->increment('stock_quantity', $change);
        $this->refresh();

        return StockAdjustment::create([
            'product_id' => $this->id,
            'user_id' => $userId,
            'quantity_change' => $change,
            'unit_cost' => $unitCost,
            'stock_after' => $this->stock_quantity,
            'type' => $type,
            'reason' => $reason,
            'quote_id' => $quoteId,
        ]);
    }
}
