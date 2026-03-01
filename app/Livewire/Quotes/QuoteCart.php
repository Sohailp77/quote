<?php

namespace App\Livewire\Quotes;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\TaxRate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class QuoteCart extends Component
{
    public $products;
    public $taxRates;
    public $taxSettings;
    public $currency;
    public $customers;

    // Customer Details
    public $customer_id = '';
    public $customer_name = '';
    public $customer_phone = '';
    public $customer_email = '';
    public $notes = '';

    // Cart Settings
    public $tax_mode = 'item_level';
    public $discount_percentage = 0;
    public $gst_rate = 0;

    // Items array
    public $items = [];

    protected function rules()
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'notes' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'tax_mode' => 'required|in:global,item_level',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate_id' => 'nullable|exists:tax_rates,id',
        ];
    }

    public ?Quote $quote = null;

    public function mount(?Quote $quote = null)
    {
        if ($quote && $quote->exists) {
            if ($quote->status !== 'draft') {
                abort(403, 'Only draft quotes can be edited.');
            }
            $this->quote = $quote;
            $this->customer_name = $quote->customer_name;
            $this->customer_phone = $quote->customer_phone;
            $this->customer_email = $quote->customer_email;
            $this->notes = $quote->notes;
            $this->tax_mode = $quote->tax_mode;
            $this->discount_percentage = $quote->discount_percentage;
            $this->gst_rate = $quote->gst_rate;

            foreach ($quote->items as $item) {
                $this->items[] = [
                    'id' => uniqid(),
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id ?? '',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'tax_rate' => $item->tax_rate,
                    'tax_rate_id' => $item->tax_rate_id ?? '',
                    'area' => $item->area ?? '',
                ];
            }
        }

        $products = \App\Models\Product::with(['category', 'variants', 'taxRate'])->get();
        $customers = \App\Models\Customer::orderBy('name')->get();
        $taxRates = \App\Models\TaxRate::where('is_active', true)->get();
        $taxSettings = \App\Models\CompanySetting::getTaxConfiguration();
        $currency = \App\Models\CompanySetting::getCurrencySymbol();

        $this->products = is_object($products) && method_exists($products, 'toArray') ? $products->toArray() : $products;
        $this->customers = is_object($customers) && method_exists($customers, 'toArray') ? $customers->toArray() : $customers;
        $this->taxRates = is_object($taxRates) && method_exists($taxRates, 'toArray') ? $taxRates->toArray() : $taxRates;
        $this->taxSettings = is_object($taxSettings) && method_exists($taxSettings, 'toArray') ? $taxSettings->toArray() : $taxSettings;
        $this->currency = $currency ?? '₹';

        if (empty($this->items)) {
            $this->addItem();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'id' => uniqid(), // For Vue/Alpine-like keyed rendering if needed
            'product_id' => '',
            'product_variant_id' => '',
            'quantity' => 1,
            'price' => 0,
            'area' => '',
            'tax_rate' => 0,
            'tax_rate_id' => ''
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
    }

    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = collect($this->customers)->firstWhere('id', $value);
            if ($customer) {
                $customerArr = (array) $customer;
                $this->customer_name = $customerArr['name'] ?? '';
                $this->customer_phone = $customerArr['phone'] ?? '';
                $this->customer_email = $customerArr['email'] ?? '';
            }
        } else {
            // Optional: clear fields if unselected, or allow them to remain as free-text
        }
    }

    public function updatedCustomerName($value)
    {
        // Case-insensitive search
        $customer = collect($this->customers)->first(function($c) use ($value) {
            return strtolower($c['name'] ?? '') === strtolower($value);
        });
        
        if ($customer) {
            $customerArr = (array) $customer;
            $this->customer_id = $customerArr['id'];
            $this->customer_phone = $customerArr['phone'] ?? '';
            $this->customer_email = $customerArr['email'] ?? '';
        } else {
            $this->customer_id = '';
        }
    }

    // When product_id changes for an item
    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) !== 2)
            return;

        $index = $parts[0];
        $field = $parts[1];

        $item =& $this->items[$index];

        if ($field === 'product_id') {
            $product = collect($this->products)->firstWhere('id', $value);
            if ($product) {
                // Reset variant
                $item['product_variant_id'] = '';
                $item['price'] = $product['price'] ?? 0;
                $item['area'] = '';

                // Set default tax if applicable
                if (!empty($product['tax_rate_id'])) {
                    $item['tax_rate_id'] = $product['tax_rate_id'];
                    $rateObj = collect($this->taxRates)->firstWhere('id', $product['tax_rate_id']);
                    $item['tax_rate'] = $rateObj ? $rateObj['rate'] : 0;
                } else {
                    $item['tax_rate_id'] = '';
                    $item['tax_rate'] = 0;
                }
            } else {
                $item['price'] = 0;
                $item['product_variant_id'] = '';
            }
        } elseif ($field === 'product_variant_id') {
            $product = collect($this->products)->firstWhere('id', $item['product_id']);
            if ($product && !empty($value)) {
                $variant = collect($product['variants'] ?? [])->firstWhere('id', $value);
                if ($variant && !empty($variant['variant_price'])) {
                    $item['price'] = $variant['variant_price'];
                } elseif ($product) {
                    $item['price'] = $product['price'] ?? 0;
                }
            }
        } elseif ($field === 'tax_rate_id') {
            $rateObj = collect($this->taxRates)->firstWhere('id', $value);
            $item['tax_rate'] = $rateObj ? $rateObj['rate'] : 0;
        } elseif ($field === 'area') {
            $product = collect($this->products)->firstWhere('id', $item['product_id']);
            if ($product && isset($product['category']['metric_type']) && $product['category']['metric_type'] === 'area' && floatval($product['unit_size']) > 0) {
                $area = floatval($value);
                if ($area > 0) {
                    $item['quantity'] = ceil($area / floatval($product['unit_size']));
                }
            }
        } elseif ($field === 'quantity') {
            $product = collect($this->products)->firstWhere('id', $item['product_id']);
            if ($product && isset($product['category']['metric_type']) && $product['category']['metric_type'] === 'area' && floatval($product['unit_size']) > 0) {
                $qty = intval($value);
                if ($qty > 0) {
                    $item['area'] = number_format($qty * floatval($product['unit_size']), 2, '.', '');
                }
            }
        }
    }

    public function getSubtotalProperty()
    {
        $sum = 0;
        foreach ($this->items as $item) {
            $sum += (floatval($item['price'] ?? 0) * intval($item['quantity'] ?? 1));
        }
        return $sum;
    }

    public function getDiscountAmountProperty()
    {
        return ($this->subtotal * floatval($this->discount_percentage ?: 0)) / 100;
    }

    public function getTaxableAmountProperty()
    {
        return $this->subtotal - $this->discountAmount;
    }

    public function getTaxAmountProperty()
    {
        if ($this->tax_mode === 'global') {
            return ($this->taxableAmount * floatval($this->gst_rate ?: 0)) / 100;
        } else {
            $sum = 0;
            foreach ($this->items as $item) {
                $sum += (floatval($item['price'] ?? 0) * intval($item['quantity'] ?? 1) * floatval($item['tax_rate'] ?? 0)) / 100;
            }
            return $sum;
        }
    }

    public function getTotalAmountProperty()
    {
        return $this->taxableAmount + $this->taxAmount;
    }

    public function setTaxMode($mode)
    {
        $this->tax_mode = $mode;
    }

    public function save()
    {
        $this->validate();

        if (count($this->items) === 0) {
            session()->flash('error', 'Please add at least one item to the quote.');
            return;
        }

        try {
            DB::beginTransaction();

            // Calculate totals identically to the properties for DB persistence
            $subtotal = $this->subtotal;
            $discount_amount = $this->discountAmount;
            $taxable_amount = $this->taxableAmount;
            $tax_amount = $this->taxAmount;
            $total_amount = $this->totalAmount;

            // Auto-Save / Update Customer to CRM
            if ($this->customer_id) {
                $customer = \App\Models\Customer::find($this->customer_id);
                if ($customer) {
                    $customer->update([
                        'name' => $this->customer_name,
                        'phone' => $this->customer_phone,
                        'email' => $this->customer_email,
                    ]);
                }
            } elseif (!empty($this->customer_name)) {
                $customer = \App\Models\Customer::where('name', $this->customer_name)
                    ->when($this->customer_email, function ($query) {
                        return $query->where('email', $this->customer_email);
                    })
                    ->first();

                if (!$customer) {
                    \App\Models\Customer::create([
                        'name' => $this->customer_name,
                        'phone' => $this->customer_phone,
                        'email' => $this->customer_email,
                    ]);
                } else {
                    $customer->update([
                        'phone' => $this->customer_phone ?: $customer->phone,
                        'email' => $this->customer_email ?: $customer->email,
                    ]);
                }
            }

            if ($this->quote && $this->quote->exists) {
                // UPDATE Existing Quote
                $this->quote->update([
                    'customer_name' => $this->customer_name,
                    'customer_phone' => $this->customer_phone,
                    'customer_email' => $this->customer_email,
                    'subtotal' => $subtotal,
                    'discount_percentage' => $this->discount_percentage ?: 0,
                    'discount_amount' => $discount_amount,
                    'taxable_amount' => $taxable_amount,
                    'tax_mode' => $this->tax_mode,
                    'gst_rate' => $this->tax_mode === 'global' ? ($this->gst_rate ?: 0) : 0,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $total_amount,
                    'notes' => $this->notes,
                ]);

                // Clear old items to rebuild cleanly
                $this->quote->items()->delete();
                $quoteRecord = $this->quote;
            } else {
                // CREATE New Quote
                $quoteRecord = Quote::create([
                    'user_id' => auth()->id(),
                    'reference_id' => 'Q-' . strtoupper(Str::random(8)),
                    'customer_name' => $this->customer_name,
                    'customer_phone' => $this->customer_phone,
                    'customer_email' => $this->customer_email,
                    'subtotal' => $subtotal,
                    'discount_percentage' => $this->discount_percentage ?: 0,
                    'discount_amount' => $discount_amount,
                    'taxable_amount' => $taxable_amount,
                    'tax_mode' => $this->tax_mode,
                    'gst_rate' => $this->tax_mode === 'global' ? ($this->gst_rate ?: 0) : 0,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $total_amount,
                    'notes' => $this->notes,
                    'status' => 'draft',
                    'created_by' => auth()->id(),
                ]);
            }

            foreach ($this->items as $item) {
                $product = collect($this->products)->firstWhere('id', $item['product_id']);
                $subtotal_item = floatval($item['price']) * intval($item['quantity']);
                $tax_amount_item = 0;

                if ($this->tax_mode === 'item_level') {
                    $tax_amount_item = ($subtotal_item * floatval($item['tax_rate'] ?? 0)) / 100;
                }

                QuoteItem::create([
                    'quote_id' => $quoteRecord->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => !empty($item['product_variant_id']) ? $item['product_variant_id'] : null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax_rate' => $this->tax_mode === 'item_level' ? floatval($item['tax_rate'] ?? 0) : 0,
                    'tax_amount' => $tax_amount_item,
                ]);
            }

            DB::commit();

            session()->flash('success', $this->quote ? 'Quotation updated successfully.' : 'Quotation generated successfully.');
            session()->flash('pdf_url', route('quotes.pdf', $quoteRecord->id));

            return redirect()->route('quotes.create');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while creating the quotation: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('quotes.create');
    }
}
