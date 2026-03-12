<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTenant;

class Product extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'description',
        'price',
        'sku',
        'image_path',
        'stock_quantity',
        'low_stock_threshold',
        'unit_size',
        'cost_price',
        'specifications',
        'tax_rate_id',
    ];

    protected $casts = [
        'specifications' => 'array',
        'unit_size' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
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
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function adjustStock(int $change, string $type, string $reason, int $userId, ?int $quoteId = null, ?float $unitCost = null): StockAdjustment
    {
        $oldStock = $this->stock_quantity;
        
        $this->increment('stock_quantity', $change);
        $this->refresh();

        $newStock = $this->stock_quantity;
        $threshold = $this->low_stock_threshold ?? 5;

        // Check if stock just dropped to or below threshold
        if ($oldStock > $threshold && $newStock <= $threshold) {
            $tenant = $this->tenant;
            
            // UI Session Notification
            if (request()->hasSession()) {
                $alerts = session()->get('low_stock_alerts', []);
                $alerts[] = "{$this->name} has dropped to {$newStock} units.";
                session()->put('low_stock_alerts', $alerts);
            }

            // Email notifications were removed here in favor of a consolidated daily summary report.
        }

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
