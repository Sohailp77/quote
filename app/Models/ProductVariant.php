<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenant;

class ProductVariant extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = ['product_id', 'name', 'sku', 'image_path', 'stock_quantity', 'low_stock_threshold', 'variant_price', 'cost_price'];

    protected $casts = [
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'variant_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ── Helpers ───────────────────────────────────────────────────
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function adjustStock(int $change, string $type, string $reason, int $userId, ?int $quoteId = null, ?float $unitCost = null): \App\Models\StockAdjustment
    {
        $oldStock = $this->stock_quantity;
        
        $this->increment('stock_quantity', $change);
        $this->refresh();

        $newStock = $this->stock_quantity;
        $threshold = $this->low_stock_threshold ?? 5;

        // Check if stock just dropped to or below threshold
        if ($oldStock > $threshold && $newStock <= $threshold) {
            $tenant = $this->tenant;
            $itemName = "{$this->product->name} - {$this->name}";
            
            // UI Session Notification
            if (request()->hasSession()) {
                $alerts = session()->get('low_stock_alerts', []);
                $alerts[] = "{$itemName} has dropped to {$newStock} units.";
                session()->put('low_stock_alerts', $alerts);
            }

            // Email notifications were removed here in favor of a consolidated daily summary report.
        }

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
