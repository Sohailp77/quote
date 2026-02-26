<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'name', 'sku', 'image_path', 'stock_quantity', 'variant_price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Helpers ───────────────────────────────────────────────────
    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock_quantity <= $threshold;
    }

    public function adjustStock(int $change, string $type, string $reason, int $userId, ?int $quoteId = null, ?float $unitCost = null): \App\Models\StockAdjustment
    {
        $this->increment('stock_quantity', $change);
        $this->refresh();

        return \App\Models\StockAdjustment::create([
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
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
