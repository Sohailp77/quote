<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PurchaseOrder;
use App\Models\Product;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        return view('purchaseorders.index', [
            'orders' => PurchaseOrder::with(['product', 'variant'])->latest()->get(),
            'products' => Product::with('variants')->get(),
            'appSettings' => \App\Models\CompanySetting::getCompanyProfile(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'estimated_arrival' => 'nullable|date',
        ]);

        PurchaseOrder::create($validated);

        return back()->with('success', 'Reorder placed successfully.');
    }

    public function updateStatus(Request $request, PurchaseOrder $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,transit,received',
            'estimated_arrival' => 'nullable|date',
        ]);

        $order->update($validated);

        return back()->with('success', 'Order status updated.');
    }

    public function confirmReceived(Request $request, PurchaseOrder $order)
    {
        if ($order->status === 'received') {
            return back()->with('error', 'Order already received.');
        }

        $order->status = 'received';
        $order->received_at = now();
        $order->save();

        // Update product stock
        if ($order->variant) {
            $order->variant->adjustStock(
                change: $order->quantity,
                type: 'purchase_order',
                reason: "Reorder received (PO #{$order->id})",
                userId: $request->user()->id,
                unitCost: (float) $order->unit_cost,
            );
        } elseif ($order->product) {
            $order->product->adjustStock(
                change: $order->quantity,
                type: 'purchase_order',
                reason: "Reorder received (PO #{$order->id})",
                userId: $request->user()->id,
                unitCost: (float) $order->unit_cost,
            );
        }

        return back()->with('success', 'Stock updated from reorder.');
    }
}
