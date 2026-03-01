<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Revenue;
use App\Models\StockAdjustment;
use App\Models\PurchaseOrder;
use App\Models\TaxRate;
use App\Models\User;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boss = User::where('role', 'boss')->first();
        if (!$boss) {
            $boss = User::create([
                'name' => 'Boss',
                'email' => 'boss@company.com',
                'password' => bcrypt('boss1234'),
                'role' => 'boss',
            ]);
        }

        // 1. Tax Rates
        $taxExempt   = TaxRate::updateOrCreate(['name' => 'Exempt 0%'],    ['rate' => 0.00,  'is_active' => true]);
        $taxStandard = TaxRate::updateOrCreate(['name' => 'Standard 18%'], ['rate' => 18.00, 'is_active' => true]);
        $taxReduced  = TaxRate::updateOrCreate(['name' => 'Reduced 5%'],   ['rate' => 5.00,  'is_active' => true]);

        // 2. Categories  (matches the 6 category images in public/images/categories/)
        $categoriesData = [
            [
                'name'        => 'Tiles',
                'unit_name'   => 'Box',
                'metric_type' => 'area',
                'description' => 'Ceramic, vitrified, and marble tiles for floors and walls',
                'image_path'  => 'images/categories/cat_tiles_1772276539369.png',
            ],
            [
                'name'        => 'Cement',
                'unit_name'   => 'Bag',
                'metric_type' => 'weight',
                'description' => 'OPC, PPC, and white cement for construction and plastering',
                'image_path'  => 'images/categories/cat_cement_1772276554907.png',
            ],
            [
                'name'        => 'Taps & Fittings',
                'unit_name'   => 'Piece',
                'metric_type' => 'fixed',
                'description' => 'Bathroom and kitchen taps, mixers, and plumbing fittings',
                'image_path'  => 'images/categories/cat_taps_1772276573668.png',
            ],
            [
                'name'        => 'Sanitaryware',
                'unit_name'   => 'Piece',
                'metric_type' => 'fixed',
                'description' => 'WC, washbasins, bathtubs, and sanitary fixtures',
                'image_path'  => 'images/categories/cat_sanitaryware_1772276590731.png',
            ],
            [
                'name'        => 'Adhesives & Grout',
                'unit_name'   => 'Bag',
                'metric_type' => 'weight',
                'description' => 'Tile adhesives, waterproof grouts, and epoxy compounds',
                'image_path'  => 'images/categories/cat_adhesives_1772276607208.png',
            ],
            [
                'name'        => 'Paint',
                'unit_name'   => 'Litre',
                'metric_type' => 'fixed',
                'description' => 'Interior emulsion, exterior weather coat, and primer paints',
                'image_path'  => 'images/categories/cat_paint_1772276690509.png',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[] = Category::firstOrCreate(
                ['name' => $catData['name']],
                $catData
            );
        }

        // 3. Products  (matches the 3 product images + additional products per category)
        $productsData = [
            // ── Tiles ──────────────────────────────────────────────────────────────
            [
                'name'         => 'Marble Finish Vitrified Tile 60×60',
                'sku'          => 'TIL-MRB-6060',
                'category_id'  => $categories[0]->id,
                'price'        => 1850.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1.44,   // sqm per box
                'stock_quantity' => 0,
                'description'  => 'High-gloss marble-look vitrified tile, 60×60 cm, suitable for living areas and corridors',
                'image_path'   => 'images/products/prod_marble_tile_1772276714138.png',
            ],
            [
                'name'         => 'Matt Anti-Skid Floor Tile 30×30',
                'sku'          => 'TIL-ANT-3030',
                'category_id'  => $categories[0]->id,
                'price'        => 780.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1.08,
                'stock_quantity' => 0,
                'description'  => 'Anti-skid matt finish tile ideal for bathrooms and outdoor areas',
                'image_path'   => null,
            ],
            [
                'name'         => 'Subway Ceramic Wall Tile 30×60',
                'sku'          => 'TIL-SUB-3060',
                'category_id'  => $categories[0]->id,
                'price'        => 620.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1.08,
                'stock_quantity' => 0,
                'description'  => 'Classic white subway ceramic wall tile, glossy finish',
                'image_path'   => null,
            ],

            // ── Cement ─────────────────────────────────────────────────────────────
            [
                'name'         => 'Ready-Mix Screed Compound 25 kg',
                'sku'          => 'CEM-RDY-25',
                'category_id'  => $categories[1]->id,
                'price'        => 420.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 25,
                'stock_quantity' => 0,
                'description'  => 'Pre-mixed floor screed and levelling compound, 25 kg bag',
                'image_path'   => 'images/products/prod_ready_mix_1772276731928.png',
            ],
            [
                'name'         => 'OPC 43 Grade Cement 50 kg',
                'sku'          => 'CEM-OPC-50',
                'category_id'  => $categories[1]->id,
                'price'        => 380.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 50,
                'stock_quantity' => 0,
                'description'  => 'Ordinary Portland cement 43 grade, for general construction',
                'image_path'   => null,
            ],
            [
                'name'         => 'White Cement 1 kg',
                'sku'          => 'CEM-WHT-01',
                'category_id'  => $categories[1]->id,
                'price'        => 55.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'White cement for tile grouting, plaster finishes, and decorative work',
                'image_path'   => null,
            ],

            // ── Taps & Fittings ────────────────────────────────────────────────────
            [
                'name'         => 'Basin Pillar Tap Chrome',
                'sku'          => 'TAP-BSN-CHR',
                'category_id'  => $categories[2]->id,
                'price'        => 1350.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'Single-lever chrome basin pillar tap with ceramic cartridge',
                'image_path'   => 'images/products/prod_basin_tap_1772276746851.png',
            ],
            [
                'name'         => 'Concealed Shower Mixer',
                'sku'          => 'TAP-SHW-MIX',
                'category_id'  => $categories[2]->id,
                'price'        => 4200.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'Thermostatic concealed shower mixer with diverter',
                'image_path'   => null,
            ],
            [
                'name'         => 'Kitchen Sink Mixer with Spray',
                'sku'          => 'TAP-KIT-SPR',
                'category_id'  => $categories[2]->id,
                'price'        => 2650.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'Pull-out spray kitchen mixer tap, brushed nickel finish',
                'image_path'   => null,
            ],

            // ── Sanitaryware ───────────────────────────────────────────────────────
            [
                'name'         => 'Wall-Hung WC with Soft-Close Seat',
                'sku'          => 'SAN-WHG-WC',
                'category_id'  => $categories[3]->id,
                'price'        => 12500.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'Wall-hung back-to-wall toilet with dual flush and soft-close seat',
                'image_path'   => null,
            ],
            [
                'name'         => 'Oval Counter-Top Washbasin',
                'sku'          => 'SAN-OVL-BSN',
                'category_id'  => $categories[3]->id,
                'price'        => 6800.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'Ceramic oval counter-top washbasin, gloss white',
                'image_path'   => null,
            ],
            [
                'name'         => 'Acrylic Bath Tub 1500 mm',
                'sku'          => 'SAN-BTH-150',
                'category_id'  => $categories[3]->id,
                'price'        => 22000.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'Freestanding acrylic soaking bathtub, 1500 mm',
                'image_path'   => null,
            ],

            // ── Adhesives & Grout ──────────────────────────────────────────────────
            [
                'name'         => 'Premium Tile Adhesive C2 20 kg',
                'sku'          => 'ADH-C2-20',
                'category_id'  => $categories[4]->id,
                'price'        => 540.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 20,
                'stock_quantity' => 0,
                'description'  => 'C2 grade flexible tile adhesive for large-format tiles and wet areas',
                'image_path'   => null,
            ],
            [
                'name'         => 'Epoxy Grout 2 kg (White)',
                'sku'          => 'ADH-EPX-2W',
                'category_id'  => $categories[4]->id,
                'price'        => 850.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 2,
                'stock_quantity' => 0,
                'description'  => 'Two-component epoxy grout, stain and chemical resistant, White shade',
                'image_path'   => null,
            ],
            [
                'name'         => 'Waterproof Tile Grout 1 kg (Beige)',
                'sku'          => 'ADH-WPG-1B',
                'category_id'  => $categories[4]->id,
                'price'        => 180.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 1,
                'stock_quantity' => 0,
                'description'  => 'Waterproof cementitious grout for bathroom and floor joints, Beige',
                'image_path'   => null,
            ],

            // ── Paint ──────────────────────────────────────────────────────────────
            [
                'name'         => 'Premium Acrylic Emulsion 20 L (White)',
                'sku'          => 'PNT-ACR-20W',
                'category_id'  => $categories[5]->id,
                'price'        => 2800.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 20,
                'stock_quantity' => 0,
                'description'  => 'Low-VOC interior emulsion with excellent washability and coverage',
                'image_path'   => null,
            ],
            [
                'name'         => 'Exterior Weather Shield 10 L',
                'sku'          => 'PNT-EXT-10',
                'category_id'  => $categories[5]->id,
                'price'        => 2200.00,
                'tax_rate_id'  => $taxStandard->id,
                'unit_size'    => 10,
                'stock_quantity' => 0,
                'description'  => 'All-weather exterior paint with UV and rain protection',
                'image_path'   => null,
            ],
            [
                'name'         => 'Acrylic Wall Primer 4 L',
                'sku'          => 'PNT-PRM-04',
                'category_id'  => $categories[5]->id,
                'price'        => 650.00,
                'tax_rate_id'  => $taxReduced->id,
                'unit_size'    => 4,
                'stock_quantity' => 0,
                'description'  => 'Water-based acrylic sealer primer for interior and exterior walls',
                'image_path'   => null,
            ],
        ];

        $products = [];
        foreach ($productsData as $prodData) {
            $products[] = Product::firstOrCreate(['sku' => $prodData['sku']], $prodData);
        }

        // Seed 4-5 months of chronological history
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $endDate   = Carbon::now();

        $currentDate = clone $startDate;

        // 4. History (Loop chronologically to build purchase orders + stock, then quotes)
        while ($currentDate <= $endDate) {
            $numPurchases = rand(0, 2);
            $numQuotes    = rand(1, 4);

            // A. Purchase Orders (Stock moving in)
            for ($i = 0; $i < $numPurchases; $i++) {
                $this->seedPurchaseOrder($currentDate, $products, $boss->id);
            }

            // B. Quotes & Sales (Stock moving out if sold)
            for ($i = 0; $i < $numQuotes; $i++) {
                $daysAgo = $currentDate->diffInDays($endDate);

                $statusWeights = [
                    'draft'    => 10,
                    'sent'     => 20,
                    'accepted' => 60,
                    'rejected' => 10,
                ];

                if ($daysAgo < 14) {
                    $statusWeights = ['draft' => 40, 'sent' => 40, 'accepted' => 15, 'rejected' => 5];
                }

                $status = $this->getWeightedRandom($statusWeights);

                $this->seedQuote($currentDate, $products, $boss->id, $status);
            }

            $currentDate->addDay();
        }
    }

    private function seedPurchaseOrder(Carbon $date, array $products, int $bossId)
    {
        $itemsCount  = rand(1, 4);
        $selectedKeys = array_rand($products, min($itemsCount, count($products)));
        if (!is_array($selectedKeys)) {
            $selectedKeys = [$selectedKeys];
        }

        foreach ($selectedKeys as $key) {
            $product = clone $products[$key];

            $productModel = Product::find($product->id);
            if (!$productModel) continue;

            $quantity = rand(10, 50);
            $unitCost = $productModel->price * (rand(60, 85) / 100);

            $stockAdjustment = new StockAdjustment([
                'product_id'      => $productModel->id,
                'user_id'         => $bossId,
                'quantity_change' => $quantity,
                'unit_cost'       => $unitCost,
                'stock_after'     => $productModel->stock_quantity + $quantity,
                'type'            => 'manual',
                'reason'          => 'Inventory Restock via Purchase Order (Demo)',
            ]);

            $productModel->increment('stock_quantity', $quantity);
            $stockAdjustment->save();
            $stockAdjustment->created_at = clone $date;
            $stockAdjustment->updated_at = clone $date;
            $stockAdjustment->saveQuietly();

            $po = PurchaseOrder::create([
                'product_id'        => $productModel->id,
                'quantity'          => $quantity,
                'unit_cost'         => $unitCost,
                'status'            => 'received',
                'estimated_arrival' => clone $date,
                'received_at'       => clone $date,
            ]);

            $po->created_at = clone $date;
            $po->updated_at = clone $date;
            $po->saveQuietly();
        }
    }

    private function seedQuote(Carbon $date, array $products, int $bossId, string $status)
    {
        $customers = [
            ['name' => 'Al Baraka Constructions',  'email' => 'procurement@albaraka.example.com'],
            ['name' => 'Sunrise Builders',          'email' => 'accounts@sunrisebuilders.example.com'],
            ['name' => 'Greenfield Developers',     'email' => 'purchases@greenfield.example.com'],
            ['name' => 'Pinnacle Interiors',        'email' => 'billing@pinnacle.example.com'],
            ['name' => 'BlueSky Infrastructure',    'email' => 'finance@bluesky.example.com'],
            ['name' => 'Heritage Home Renovators',  'email' => 'info@heritagehome.example.com'],
        ];

        $customer = $customers[array_rand($customers)];

        $discountPercentage = rand(0, 10) > 7 ? rand(5, 15) : 0;

        $quoteId = Quote::max('id') + 1;

        $quote = Quote::create([
            'user_id'             => $bossId,
            'customer_name'       => $customer['name'],
            'customer_email'      => $customer['email'],
            'customer_phone'      => '+971 50 ' . rand(1000000, 9999999),
            'reference_id'        => 'QT-' . date('Ym', $date->timestamp) . '-' . str_pad($quoteId, 4, '0', STR_PAD_LEFT),
            'status'              => $status,
            'subtotal'            => 0,
            'tax_mode'            => 'single',
            'tax_amount'          => 0,
            'total_amount'        => 0,
            'valid_until'         => $date->copy()->addDays(30),
            'notes'               => 'Thank you for your business. Prices include material supply only unless otherwise stated.',
            'discount_percentage' => $discountPercentage,
            'discount_amount'     => 0,
        ]);

        $quote->created_at = clone $date;
        $quote->updated_at = clone $date;
        $quote->saveQuietly();

        $itemsCount   = rand(1, 6);
        $selectedKeys = array_rand($products, min($itemsCount, count($products)));
        if (!is_array($selectedKeys)) {
            $selectedKeys = [$selectedKeys];
        }

        $subtotal  = 0;
        $taxAmount = 0;

        foreach ($selectedKeys as $key) {
            $productModel = Product::find($products[$key]->id);
            if (!$productModel) continue;

            $quantity  = rand(1, 20);
            $unitPrice = $productModel->price;
            $lineTotal = $quantity * $unitPrice;

            $taxRate = $productModel->taxRate ? $productModel->taxRate->rate : 0;
            $lineTax = $lineTotal * ($taxRate / 100);

            $subtotal  += $lineTotal;
            $taxAmount += $lineTax;

            $qi = QuoteItem::create([
                'quote_id'   => $quote->id,
                'product_id' => $productModel->id,
                'quantity'   => $quantity,
                'price'      => $unitPrice,
                'tax_rate'   => $taxRate,
                'tax_amount' => $lineTax,
            ]);
            $qi->created_at = clone $date;
            $qi->updated_at = clone $date;
            $qi->saveQuietly();

            if ($status === 'accepted') {
                $stockAdjustment = new StockAdjustment([
                    'product_id'      => $productModel->id,
                    'user_id'         => $bossId,
                    'quantity_change' => -$quantity,
                    'unit_cost'       => null,
                    'stock_after'     => $productModel->stock_quantity - $quantity,
                    'type'            => 'quote',
                    'reason'          => 'Quote Accepted: ' . $quote->reference_id,
                    'quote_id'        => $quote->id,
                ]);

                $productModel->decrement('stock_quantity', $quantity);
                $stockAdjustment->save();
                $stockAdjustment->created_at = clone $date;
                $stockAdjustment->updated_at = clone $date;
                $stockAdjustment->saveQuietly();
            }
        }

        $discountAmount       = $subtotal * ($discountPercentage / 100);
        $subtotalAfterDiscount = $subtotal - $discountAmount;
        $adjustedTaxAmount    = $subtotal > 0 ? $taxAmount * ($subtotalAfterDiscount / $subtotal) : 0;

        $total = $subtotalAfterDiscount + $adjustedTaxAmount;

        $quote->update([
            'subtotal'        => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount'      => $adjustedTaxAmount,
            'total_amount'    => $total,
        ]);
        $quote->created_at = clone $date;
        $quote->updated_at = clone $date;
        $quote->saveQuietly();

        if ($status === 'accepted') {
            $revenue = Revenue::create([
                'quote_id'    => $quote->id,
                'amount'      => $total,
                'recorded_at' => $date->copy()->addDays(rand(0, 5)),
            ]);
            $revenue->created_at = clone $date;
            $revenue->updated_at = clone $date;
            $revenue->saveQuietly();
        }
    }

    private function getWeightedRandom(array $weights)
    {
        $rand = rand(1, (int) array_sum($weights));
        foreach ($weights as $key => $value) {
            $rand -= $value;
            if ($rand <= 0) {
                return $key;
            }
        }
    }
}
