<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateQuoteProfit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:recalculate-profit';
    protected $description = 'Recalculate total_cost and profit_amount for all existing quotes based on current product costs';

    public function handle()
    {
        $quotes = \App\Models\Quote::with('items')->get();
        $this->info("Recalculating profit for {$quotes->count()} quotes...");

        $bar = $this->output->createProgressBar($quotes->count());
        $bar->start();

        foreach ($quotes as $quote) {
            /** @var \App\Models\Quote $quote */
            $totalCost = 0;
            foreach ($quote->items as $item) {
                $costPrice = 0;
                if ($item->product_variant_id) {
                    $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                    $costPrice = $variant ? $variant->cost_price : 0;
                } else {
                    $product = \App\Models\Product::find($item->product_id);
                    $costPrice = $product ? $product->cost_price : 0;
                }
                $totalCost += ($costPrice * $item->quantity);
            }

            $quote->update([
                'total_cost' => $totalCost,
                'profit_amount' => $quote->total_amount - $totalCost,
            ]);
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nProfit recalculation complete!");
    }
}
