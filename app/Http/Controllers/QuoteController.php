<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with('user');

        if (!auth()->user()->isBoss()) {
            $query->where('user_id', auth()->id());
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Delivery status filter
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->input('delivery_status'));
        }

        // Custom date range (based on created_at)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Overdue filter – accepted + delivery date passed + not delivered
        if ($request->boolean('overdue')) {
            $query->where('status', 'accepted')
                  ->whereNotNull('delivery_date')
                  ->where('delivery_date', '<', now()->toDateString())
                  ->where(function ($q) {
                      $q->where('delivery_status', '!=', 'delivered')
                        ->orWhereNull('delivery_status');
                  });
        }

        $filters = $request->only(['search', 'status', 'delivery_status', 'date_from', 'date_to', 'overdue']);
        $quotes  = $query->latest()->paginate(15)->withQueryString();

        return view('quotes.index', compact('quotes', 'filters'));
    }
    public function create()
    {
        $products = \App\Models\Product::with(['category', 'variants', 'taxRate'])->get();
        $taxRates = \App\Models\TaxRate::where('is_active', true)->get();
        $taxSettings = \App\Models\CompanySetting::getTaxConfiguration();
        $currency = \App\Models\CompanySetting::getCurrencySymbol() ?? '₹';

        return view('quotes.create', compact('products', 'taxRates', 'taxSettings', 'currency'));
    }

    public function edit(Quote $quote)
    {
        if ($quote->status !== 'draft') {
            abort(403, 'Only draft quotes can be edited.');
        }

        $quote->load(['items.product', 'items.variant']);

        $products = \App\Models\Product::with(['category', 'variants', 'taxRate'])->get();
        $taxRates = \App\Models\TaxRate::where('is_active', true)->get();
        $taxSettings = \App\Models\CompanySetting::getTaxConfiguration();
        $currency = \App\Models\CompanySetting::getCurrencySymbol() ?? '₹';

        return view('quotes.create', compact('quote', 'products', 'taxRates', 'taxSettings', 'currency'));
    }

    // Store the complete quote
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'tax_mode' => 'required|in:global,item_level',
            'gst_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tax_rate' => 'nullable|numeric|min:0',
            'items.*.tax_rate_id' => 'nullable|exists:tax_rates,id',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        $taxSettings = \App\Models\CompanySetting::getTaxConfiguration();

        $quote = Quote::create([
            'user_id' => $request->user()->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'tax_mode' => $validated['tax_mode'],
            'tax_config_snapshot' => $taxSettings,
            'notes' => $validated['notes'] ?? null,
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'reference_id' => 'Q-' . strtoupper(Str::random(8)),
            'status' => 'draft',
            'valid_until' => now()->addDays(30),
            'gst_type' => ($taxSettings['strategy'] === 'split') ? 'cgst_sgst' : 'igst',
            'gst_rate' => ($validated['tax_mode'] === 'global') ? ($validated['gst_rate'] ?? 0) : 0,
        ]);

        $subtotal = 0;
        $totalTaxAmount = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['price'] * $item['quantity'];
            $subtotal += $lineTotal;

            $itemTaxRate = $validated['tax_mode'] === 'item_level' ? ($item['tax_rate'] ?? 0) : 0;
            $itemTaxAmount = ($lineTotal * $itemTaxRate) / 100;
            if ($validated['tax_mode'] === 'item_level') {
                $totalTaxAmount += $itemTaxAmount;
            }

            QuoteItem::create([
                'quote_id' => $quote->id,
                'product_id' => $item['product_id'],
                'product_variant_id' => !empty($item['product_variant_id']) ? $item['product_variant_id'] : null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'tax_rate' => $itemTaxRate,
                'tax_amount' => $itemTaxAmount,
            ]);
        }

        $discountAmount = ($subtotal * $quote->discount_percentage) / 100;
        $taxableAmount = $subtotal - $discountAmount;

        if ($validated['tax_mode'] === 'global') {
            $globalRate = $validated['gst_rate'] ?? 0;
            $totalTaxAmount = ($taxableAmount * $globalRate) / 100;
        }

        $quote->subtotal = $subtotal;
        $quote->discount_amount = $discountAmount;
        $quote->tax_amount = $totalTaxAmount;
        $quote->total_amount = $taxableAmount + $totalTaxAmount;
        $quote->save();

        return redirect()->route('quotes.create')
            ->with('success', 'Quote created successfully.')
            ->with('pdf_url', route('quotes.pdf', $quote->id));
    }

    public function updateStatus(Request $request, Quote $quote)
    {
        $user = $request->user();

        // Authorise
        if ($user->isEmployee() && $quote->user_id !== $user->id) {
            abort(403);
        }

        $allowedTransitions = $user->isBoss()
            ? ['draft', 'sent', 'accepted', 'rejected', 'expired']
            : ['sent']; // employees can only mark their draft as sent

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', $allowedTransitions)],
            'delivery_date' => ['nullable', 'date'],
            'delivery_time' => ['nullable', 'string'],
            'delivery_partner' => ['nullable', 'string', 'max:255'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
        ]);

        $oldStatus = $quote->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return back()->with('success', 'Quote status is already ' . $newStatus);
        }

        return DB::transaction(function () use ($request, $quote, $oldStatus, $newStatus, $user, $validated) {
            // Stock Warning Logic: Check if we have enough stock for all items
            if ($newStatus === 'accepted' && $oldStatus !== 'accepted' && !$request->boolean('force')) {
                $quote->load('items.product', 'items.variant');
                $insufficientItems = [];
                foreach ($quote->items as $item) {
                    if ($item->variant) {
                        if ($item->variant->stock_quantity < $item->quantity) {
                            $insufficientItems[] = [
                                'name' => $item->product->name . ' (' . $item->variant->name . ')',
                                'requested' => $item->quantity,
                                'available' => $item->variant->stock_quantity,
                            ];
                        }
                    } elseif ($item->product && $item->product->stock_quantity < $item->quantity) {
                        $insufficientItems[] = [
                            'name' => $item->product->name,
                            'requested' => $item->quantity,
                            'available' => $item->product->stock_quantity,
                        ];
                    }
                }

                if (!empty($insufficientItems)) {
                    // We must throw or return back. inside transaction we shouldn't return back directly if we want rollback, but since we haven't written anything yet, it's fine.
                    // Actually, returning a response from within DB::transaction returns it.
                    return back()->with('stock_warning', [
                        'message' => 'Insufficient stock for some items. Do you want to continue?',
                        'items' => $insufficientItems,
                        'quote_id' => $quote->id,
                    ]);
                }
            }

            $quote->status = $newStatus;
            
            // Save Delivery Information if moving to Accepted
            if ($newStatus === 'accepted') {
                $quote->delivery_date = $validated['delivery_date'] ?? null;
                $quote->delivery_time = $validated['delivery_time'] ?? null;
                $quote->delivery_partner = $validated['delivery_partner'] ?? null;
                $quote->tracking_number = $validated['tracking_number'] ?? null;
                $quote->delivery_status = 'pending'; // Default when just accepted
            }

            $quote->save();

            // 1. Moving TO Accepted (Deduct stock, add Revenue)
            if ($newStatus === 'accepted' && $oldStatus !== 'accepted') {
                $quote->load('items.product', 'items.variant');
                foreach ($quote->items as $item) {
                    $target = $item->variant ?: $item->product;
                    if ($target) {
                        $target->adjustStock(
                            change: -$item->quantity,
                            type: 'quote',
                            reason: "Quote {$quote->reference_id} accepted",
                            userId: $user->id,
                            quoteId: $quote->id,
                        );
                    }
                }

                // Record Revenue
                Revenue::create([
                    'quote_id' => $quote->id,
                    'amount' => $quote->total_amount,
                    'currency' => 'INR', // Default to INR, can be made dynamic later
                    'recorded_at' => now(),
                ]);
            }

            // 2. Moving AWAY from Accepted (Revert stock, Revert Revenue)
            if ($oldStatus === 'accepted' && $newStatus !== 'accepted') {
                $quote->load('items.product', 'items.variant', 'stockAdjustments', 'revenues');

                // Revert stock adjustments
                foreach ($quote->stockAdjustments as $adj) {
                    if (!$adj->reverted_at && $adj->type === 'quote') {
                        // Mark original as reverted
                        $adj->update(['reverted_at' => now()]);

                        // Counter adjustment
                        $target = $adj->variant ?: $adj->product;
                        if ($target) {
                            $target->adjustStock(
                                change: -$adj->quantity_change, // positive since original was negative
                                type: 'return',
                                reason: "Quote {$quote->reference_id} status changed to {$newStatus}, reverted deduction",
                                userId: $user->id,
                                quoteId: $quote->id,
                            );
                        }
                    }
                }

                // Revert revenue
                foreach ($quote->revenues as $rev) {
                    if (!$rev->reverted_at) {
                        $rev->update(['reverted_at' => now()]);
                    }
                }
            }

            return back()->with('success', 'Quote status updated.');
        });
    }

    // Generate PDF
    public function pdf(Quote $quote)
    {
        $quote->load(['items.product.category', 'items.variant']);
        $companyProfile = \App\Models\CompanySetting::where('group', 'company')->pluck('value', 'key')->all();
        $themeSettings = \App\Models\CompanySetting::where('group', 'theme')->pluck('value', 'key')->all();
        $brandColor = $themeSettings['brand_color_primary'] ?? '#0077c0';

        $html = view('pdf.quote', [
            'quote' => $quote,
            'companyProfile' => $companyProfile,
            'brandColor' => $brandColor,
        ])->render();

        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'dejavusans',
            'format' => 'A4',
            'tempDir' => sys_get_temp_dir() . '/mpdf',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
        ]);
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="quote_' . $quote->reference_id . '.pdf"'
        ]);
    }

    /**
     * Update delivery details for an accepted quote.
     * Also handles early delivery completion.
     */
    public function updateDelivery(Request $request, Quote $quote)
    {
        if ($quote->status !== 'accepted') {
            return back()->with('error', 'Delivery details can only be updated for accepted quotes.');
        }

        $validated = $request->validate([
            'delivery_date'    => ['nullable', 'date'],
            'delivery_time'    => ['nullable', 'string'],
            'delivery_partner' => ['nullable', 'string', 'max:255'],
            'tracking_number'  => ['nullable', 'string', 'max:255'],
            'delivery_status'  => ['nullable', 'in:pending,shipped,delivered'],
            'delivery_note'    => ['nullable', 'string', 'max:1000'],
        ]);

        $quote->update($validated);

        $msg = $validated['delivery_status'] === 'delivered'
            ? '🎉 Delivery marked as completed!'
            : 'Delivery details updated successfully.';

        return back()->with('success', $msg);
    }
}
