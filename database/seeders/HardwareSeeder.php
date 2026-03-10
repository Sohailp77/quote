<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class HardwareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the user boss@company.com
        $user = User::where('email', 'boss@company.com')->first();

        if (!$user) {
            $this->command->error("User boss@company.com not found. Please run DatabaseSeeder or create the user first.");
            return;
        }

        $tenantId = $user->tenant_id;

        // Define Categories
        $categoriesData = [
            'Power Tools' => 'High-performance tools for professional use.',
            'Hand Tools' => 'Essential hand tools for construction and repair.',
            'Fasteners' => 'Screws, nails, bolts, and anchors.',
            'Plumbing' => 'Pipes, fittings, and plumbing accessories.',
            'Electrical' => 'Wires, switches, sockets, and electrical fittings.',
        ];

        $categories = [];

        foreach ($categoriesData as $name => $desc) {
            $categories[$name] = Category::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'name' => $name,
                ],
                [
                    'unit_name' => 'Pcs',
                    'metric_type' => 'fixed',
                    'description' => $desc,
                ]
            );
        }

        // Define Products
        $productsData = [
            [
                'category' => 'Power Tools',
                'name' => 'DeWalt 20V Max Cordless Drill',
                'description' => 'Compact cordless drill/driver kit with 2 batteries.',
                'sku' => 'DW20VCD',
                'price' => 129.00,
                'cost_price' => 100.00,
                'stock_quantity' => 45,
                'specifications' => [
                    'Voltage' => '20V',
                    'Chuck Size' => '1/2 inch',
                    'Battery' => 'Lithium-Ion'
                ],
            ],
            [
                'category' => 'Power Tools',
                'name' => 'Makita 18V LXT Circular Saw',
                'description' => '6-1/2 inch cordless circular saw for wood cutting.',
                'sku' => 'MAK18VCS',
                'price' => 149.00,
                'cost_price' => 120.00,
                'stock_quantity' => 30,
                'specifications' => [
                    'Blade Size' => '6-1/2 inch',
                    'Speed' => '3,700 RPM',
                ],
            ],
            [
                'category' => 'Hand Tools',
                'name' => 'Stanley 100-Piece Mechanics Tool Set',
                'description' => 'Comprehensive tool set with sockets, wrenches, and hex keys.',
                'sku' => 'STAN100TS',
                'price' => 89.99,
                'cost_price' => 60.00,
                'stock_quantity' => 100,
                'specifications' => [
                    'Pieces' => '100',
                    'Material' => 'Chrome Vanadium Steel'
                ],
            ],
            [
                'category' => 'Hand Tools',
                'name' => 'Estwing 16 oz Rip Claw Hammer',
                'description' => 'Solid steel hammer with shock reduction grip.',
                'sku' => 'EST16RCH',
                'price' => 29.50,
                'cost_price' => 20.00,
                'stock_quantity' => 150,
                'specifications' => [
                    'Weight' => '16 oz',
                    'Handle Material' => 'Leather/Nylon Vinyl'
                ],
            ],
            [
                'category' => 'Fasteners',
                'name' => 'Grip-Rite 2-1/2 in. Construction Screws (1lb box)',
                'description' => 'Multi-purpose interior construction screws.',
                'sku' => 'GR25CS1LB',
                'price' => 8.98,
                'cost_price' => 5.00,
                'stock_quantity' => 500,
                'specifications' => [
                    'Size' => '2-1/2 inch',
                    'Drive Type' => 'Star/Torx',
                    'Quantity' => '~110 pcs'
                ],
            ],
            [
                'category' => 'Fasteners',
                'name' => 'Simpson Strong-Tie Joist Hangers (Pack of 10)',
                'description' => 'Galvanized face-mount joist hangers for 2x4 wood framing.',
                'sku' => 'SST2X4JH',
                'price' => 15.50,
                'cost_price' => 10.00,
                'stock_quantity' => 200,
                'specifications' => [
                    'Material' => '18-Gauge Galvanized Steel',
                    'Size' => '2x4'
                ],
            ],
            [
                'category' => 'Plumbing',
                'name' => 'SharkBite 1/2 in. Push-to-Connect Fitting',
                'description' => 'Brass push coupling for connecting copper, PEX, CPVC, or PE-RT pipes.',
                'sku' => 'SB12PTC',
                'price' => 7.85,
                'cost_price' => 5.00,
                'stock_quantity' => 300,
                'specifications' => [
                    'Size' => '1/2 inch',
                    'Material' => 'Lead-Free Brass'
                ],
            ],
            [
                'category' => 'Plumbing',
                'name' => 'Oatey 8 oz. PVC Cement & Primer Pack',
                'description' => 'Regular clear PVC cement and purple primer handy pack.',
                'sku' => 'OAT8OZCEMENT',
                'price' => 11.20,
                'cost_price' => 8.00,
                'stock_quantity' => 80,
                'specifications' => [
                    'Volume' => '8 oz each',
                    'Type' => 'Regular Clear'
                ],
            ],
            [
                'category' => 'Electrical',
                'name' => 'Southwire 250 ft. 14/2 Romex Wire',
                'description' => 'Non-metallic sheathed cable for indoor residential wiring.',
                'sku' => 'SW142250R',
                'price' => 75.00,
                'cost_price' => 50.00,
                'stock_quantity' => 60,
                'specifications' => [
                    'Length' => '250 ft',
                    'Gauge' => '14 AWG',
                    'Conductors' => '2 with bare ground'
                ],
            ],
            [
                'category' => 'Electrical',
                'name' => 'Leviton 15 Amp Duplex Receptacle (10-Pack)',
                'description' => 'Tamper-resistant residential grade outlets, white.',
                'sku' => 'LEV15AMP10P',
                'price' => 19.98,
                'cost_price' => 15.00,
                'stock_quantity' => 120,
                'specifications' => [
                    'Amperage' => '15A',
                    'Voltage' => '125V',
                    'Color' => 'White'
                ],
            ],
        ];

        $count = 0;
        foreach ($productsData as $data) {
            $cat = $categories[$data['category']];
            unset($data['category']);

            $data['tenant_id'] = $tenantId;
            $data['category_id'] = $cat->id;

            Product::firstOrCreate(
                ['tenant_id' => $tenantId, 'sku' => $data['sku']],
                $data
            );
            $count++;
        }

        $this->command->info("Hardware catalog seeded! {$count} products created for boss@company.com");
    }
}
