<?php

namespace App\Livewire\Analytics;

use App\Models\Revenue;
use App\Models\StockAdjustment;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Ledger extends Component
{
    use WithPagination;

    public $searchQuery = '';
    public $filterType = 'all'; // all, revenue, cost

    protected $queryString = [
        'searchQuery' => ['except' => ''],
        'filterType' => ['except' => 'all'],
    ];

    public function updatingSearchQuery()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function setFilterType($type)
    {
        $this->filterType = $type;
        $this->resetPage();
    }

    public function render()
    {
        $revenues = collect();
        $costs = collect();

        // 1. Fetch Revenues
        if (in_array($this->filterType, ['all', 'revenue'])) {
            $revenues = Revenue::with(['quote', 'stockAdjustment.variant', 'stockAdjustment.product'])
                ->orderBy('recorded_at', 'desc')
                ->take(500)
                ->get()
                ->map(function ($rev) {
                    return [
                        'id' => 'rev_' . $rev->id,
                        'true_id' => $rev->id,
                        'is_revenue' => true,
                        'type' => $rev->quote_id ? 'Quote Sale' : 'Manual Sale',
                        'amount' => (float) $rev->amount,
                        'date' => clone $rev->recorded_at,
                        'description' => $rev->quote ? "Order #{$rev->quote->reference_id}" : ($rev->stockAdjustment ? $rev->stockAdjustment->reason : 'Direct Sale'),
                        'target_item' => $rev->stockAdjustment ? ($rev->stockAdjustment->variant ? $rev->stockAdjustment->variant->name : $rev->stockAdjustment->product->name) : '-',
                        'adjustment_id' => $rev->stock_adjustment_id,
                        'reverted_at' => $rev->reverted_at,
                        'user' => 'System',
                        'quantity' => null,
                        'unit_cost' => null,
                        'original_type' => 'Revenue',
                    ];
                });
        }

        // 2. Fetch Costs
        if (in_array($this->filterType, ['all', 'cost'])) {
            $costs = StockAdjustment::whereNotNull('unit_cost')
                ->with(['product', 'variant', 'user'])
                ->orderBy('created_at', 'desc')
                ->take(500)
                ->get()
                ->map(function ($adj) {
                    return [
                        'id' => 'adj_' . $adj->id,
                        'true_id' => $adj->id,
                        'is_revenue' => false,
                        'type' => ucfirst($adj->type),
                        'amount' => (float) (abs($adj->quantity_change) * $adj->unit_cost),
                        'date' => clone $adj->created_at,
                        'description' => $adj->reason,
                        'target_item' => $adj->variant ? $adj->variant->name : $adj->product->name,
                        'user' => $adj->user ? $adj->user->name : 'System',
                        'quantity' => $adj->quantity_change,
                        'unit_cost' => (float) $adj->unit_cost,
                        'reverted_at' => $adj->reverted_at,
                        'adjustment_id' => $adj->id,
                        'original_type' => $adj->type,
                    ];
                });
        }

        $merged = $revenues->concat($costs)->sortByDesc('date')->values();

        // 3. Apply Search Filter
        if (!empty($this->searchQuery)) {
            $q = strtolower($this->searchQuery);
            $merged = $merged->filter(function ($item) use ($q) {
                return str_contains(strtolower($item['target_item']), $q) ||
                    str_contains(strtolower($item['description']), $q) ||
                    str_contains(strtolower($item['type']), $q);
            })->values();
        }

        $perPage = 50;
        $page = $this->getPage();

        $ledger = new \Illuminate\Pagination\LengthAwarePaginator(
            $merged->forPage($page, $perPage),
            $merged->count(),
            $perPage,
            $page,
            ['path' => route('analytics.ledger')]
        );

        return view('livewire.analytics.ledger', [
            'ledger' => $ledger
        ]);
    }
}
