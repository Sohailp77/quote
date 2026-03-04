<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Revenue;
use App\Models\StockAdjustment;
use App\Models\TaxRate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class HardwareDistributorSeeder extends Seeder
{
    public function run(): void
    {
        $boss = User::where('role', 'boss')->first();
        if (!$boss) {
            $boss = User::create([
                'name' => 'Boss',
                'email' => 'boss@hardware.com',
                'password' => bcrypt('password'),
                'role' => 'boss',
            ]);
        }

        // 1. Tax Rates
        $taxStandard = TaxRate::firstOrCreate(['name' => 'GST 18%'], ['rate' => 18.00, 'is_active' => true]);

        // 2. Categories
        $categories = [
            'Power Tools' => ['unit' => 'Unit', 'prefix' => 'PW'],
            'Hand Tools' => ['unit' => 'Set', 'prefix' => 'HT'],
            'Fasteners & Fixing' => ['unit' => 'Box', 'prefix' => 'FF'],
            'Safety Gear' => ['unit' => 'Piece', 'prefix' => 'SG'],
            'Plumbing & HVAC' => ['unit' => 'Set', 'prefix' => 'PH'],
        ];

        $categoryModels = [];
        foreach ($categories as $name => $data) {
            $categoryModels[$name] = Category::firstOrCreate(
                ['name' => $name],
                [
                    'unit_name' => $data['unit'],
                    'metric_type' => 'fixed',
                    'description' => "Professional grade $name for industrial and domestic use.",
                ]
            );
        }

        // 3. Products (50 items)
        $hardwareProducts = [
            'Power Tools' => [
                'Cordless Drill Driver 18V', 'Angle Grinder 115mm', 'Rotary Hammer Drill', 'Circular Saw 190mm',
                'Jigsaw 650W', 'Impact Wrench 1/2"', 'Belt Sander 75mm', 'Heat Gun 2000W', 'Router 1200W', 'Demolition Hammer'
            ],
            'Hand Tools' => [
                'Socket Set 40pc', 'Combination Spanner Set', 'Adjustable Wrench 12"', 'Claw Hammer 16oz',
                'Spirit Level 600mm', 'Screwdriver Set 8pc', 'Pipe Wrench 18"', 'Hacksaw Frame', 'Pliers Set 3pc', 'Utility Knife'
            ],
            'Fasteners & Fixing' => [
                'Wood Screws 4x40mm (200pc)', 'Drywall Screws 3.5x25mm (500pc)', 'M8 Hex Bolts (50pc)', 'Wall Plugs 6mm (100pc)',
                'Masonry Anchors M10', 'Self-Tapping Screws', 'Nylon Cable Ties', 'Washer Set (100pc)', 'Stainless Steel Nuts M6', 'Pop Rivets 4mm'
            ],
            'Safety Gear' => [
                'Hard Hat Yellow', 'Safety Goggles Clear', 'Kevlar Work Gloves', 'High-Vis Vest Orange',
                'Ear Defenders', 'Dust Masks FFP2', 'Face Shield', 'Knee Pads', 'First Aid Kit Small', 'Safety Boots Steel Toe'
            ],
            'Plumbing & HVAC' => [
                'Copper Pipe 15mm (3m)', 'Compression Elbow 15mm', 'Ball Valve 1/2"', 'PTFE Thread Tape',
                'PVC Glue 250ml', 'Pipe Cutter', 'Radiator Valve Set', 'Plumber\'s Putty', 'U-Trap 40mm', 'Basin Waste Kit'
            ],
        ];

        $allProducts = [];
        foreach ($hardwareProducts as $catName => $items) {
            $catId = $categoryModels[$catName]->id;
            $prefix = $categories[$catName]['prefix'];
            foreach ($items as $index => $name) {
                $p = Product::create([
                    'category_id' => $catId,
                    'tax_rate_id' => $taxStandard->id,
                    'name' => $name,
                    'sku' => $prefix . '-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'price' => rand(200, 5000),
                    'stock_quantity' => 100, // Initial stock
                    'description' => "High-quality $name for professional distribution.",
                ]);
                $allProducts[] = $p;

                // Record initial stock adjustment
                StockAdjustment::create([
                    'product_id' => $p->id,
                    'user_id' => $boss->id,
                    'quantity_change' => 100,
                    'type' => 'initial_stock',
                    'reason' => 'Initial inventory for demo.',
                    'stock_after' => 100,
                ]);
            }
        }

        // 4. Customers (10 items)
        $customers = [
            ['name' => 'Metro Builders & Co', 'email' => 'metro@example.com', 'phone' => '9876543210', 'company' => 'Metro Builders', 'address' => '123 Construction Hub'],
            ['name' => 'Elite Interior Solutions', 'email' => 'elite@example.com', 'phone' => '9888877777', 'company' => 'Elite Interiors', 'address' => '45 Design Street'],
            ['name' => 'City Hardware Mart', 'email' => 'citymart@example.com', 'phone' => '9991112223', 'company' => 'City Hardware', 'address' => 'Market Square'],
            ['name' => 'Prime Plumbers Ltd', 'email' => 'prime@example.com', 'phone' => '9112233445', 'company' => 'Prime Plumbing', 'address' => 'Service Lane'],
            ['name' => 'Safety First Supplies', 'email' => 'safety@example.com', 'phone' => '9000011111', 'company' => 'Safety First', 'address' => 'Industrial Zone'],
            ['name' => 'Global Contractors', 'email' => 'global@example.com', 'phone' => '9445566778', 'company' => 'Global Contractors', 'address' => 'Global Tower'],
            ['name' => 'Bright Home Renovations', 'email' => 'bright@example.com', 'phone' => '9334455667', 'company' => 'Bright Homes', 'address' => 'Residential Block B'],
            ['name' => 'Structural Engineers Pvt', 'email' => 'structural@example.com', 'phone' => '9223344556', 'company' => 'Structural Eng', 'address' => 'Civil Lines'],
            ['name' => 'Speedy Electricals', 'email' => 'speedy@example.com', 'phone' => '9556677889', 'company' => 'Speedy Elec', 'address' => 'Power House'],
            ['name' => 'Zenith Facility Management', 'email' => 'zenith@example.com', 'phone' => '9667788990', 'company' => 'Zenith FM', 'address' => 'Corporate Park'],
        ];

        $customerModels = [];
        foreach ($customers as $c) {
            $customerModels[] = Customer::create($c);
        }

        // 5. Transactions (20 items)
        for ($i = 0; $i < 20; $i++) {
            $date = Carbon::now()->subDays(rand(0, 30));
            $customer = $customerModels[array_rand($customerModels)];
            
            $quote = Quote::create([
                'user_id' => $boss->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'customer_address' => $customer->address,
                'reference_id' => 'HW-' . strtoupper(Str::random(8)),
                'status' => 'accepted',
                'tax_mode' => 'global',
                'gst_rate' => 18,
                'valid_until' => $date->copy()->addDays(30),
                'total_amount' => 0, // Calculated later
            ]);

            $subtotal = 0;
            $itemsCount = rand(1, 5);
            $selectedProducts = (array) array_rand($allProducts, $itemsCount);
            if (!is_array($selectedProducts)) $selectedProducts = [$selectedProducts];

            foreach ($selectedProducts as $pKey) {
                $p = $allProducts[$pKey];
                $qty = rand(1, 10);
                $price = $p->price;
                $lineTotal = $price * $qty;
                $subtotal += $lineTotal;

                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $p->id,
                    'quantity' => $qty,
                    'price' => $price,
                    'tax_rate' => 18,
                    'tax_amount' => $lineTotal * 0.18,
                ]);

                // Create stock adjustment
                $p->decrement('stock_quantity', $qty);
                StockAdjustment::create([
                    'product_id' => $p->id,
                    'user_id' => $boss->id,
                    'quantity_change' => -$qty,
                    'type' => 'quote',
                    'reason' => "Quote {$quote->reference_id} sale.",
                    'stock_after' => $p->stock_quantity,
                    'quote_id' => $quote->id,
                ]);
            }

            $taxAmount = $subtotal * 0.18;
            $total = $subtotal + $taxAmount;

            $quote->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $total,
            ]);

            // Create Revenue
            Revenue::create([
                'quote_id' => $quote->id,
                'amount' => $total,
                'recorded_at' => $date,
            ]);

            // Update timestamps for seeder logic
            $quote->created_at = $date;
            $quote->save();
        }
    }
}
