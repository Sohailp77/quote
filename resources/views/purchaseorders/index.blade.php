<x-app-layout>
    <div
        class="bg-slate-50/50 dark:bg-slate-800/50 min-h-screen rounded-[40px] p-6 lg:p-8 font-sans text-slate-800 dark:text-slate-200">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}"
                    class="w-10 h-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-400 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 transition shadow-sm">
                    <x-lucide-arrow-left class="w-5 h-5" /></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-900 dark:text-white">Purchase Orders</h1>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-medium">Manage product reorders and stock
                        arrivals</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <x-search-bar placeholder="Search reorders..." />
                <!-- Livewire Create Order Button + Modal -->
                <livewire:purchase-orders.create-order :products="$products" :appSettings="$appSettings" />
            </div>
        </div>

        <!-- Content Table -->
        <div
            class="bg-white dark:bg-slate-900 rounded-[32px] overflow-hidden shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-100 dark:border-slate-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
                            <th
                                class="px-6 py-4 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Product</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Qty</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Cost</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Total</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                                Estimated Arrival</th>
                            <th
                                class="px-6 py-4 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        @forelse($orders as $order)
                            @php
                                $statusIcons = [
                                    'pending' => 'lucide-clock',
                                    'transit' => 'lucide-truck',
                                    'received' => 'lucide-check-circle-2',
                                ];
                                $statusColors = [
                                    'pending' => 'text-amber-600 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-400',
                                    'transit' => 'text-sky-600 bg-sky-50 dark:bg-sky-900/30 dark:text-sky-400',
                                    'received' =>
                                        'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30',
                                ];
                                $statusLabels = [
                                    'pending' => 'Pending',
                                    'transit' => 'In Transit',
                                    'received' => 'Received',
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-slate-100 dark:bg-slate-950 rounded-xl flex items-center justify-center p-1 overflow-hidden border border-slate-200 dark:border-slate-700">
                                            @if (optional($order->product)->image_path)
                                                <img src="{{ $order->product->image_path }}" alt=""
                                                    class="w-full h-full object-cover rounded-lg" />
                                            @else
                                                <x-lucide-package
                                                    class="w-5 h-5 text-slate-400 dark:text-slate-500" /></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                                {{ optional($order->product)->name }}
                                                @if ($order->variant)
                                                    <span
                                                        class="text-slate-500 dark:text-slate-400 font-medium">({{ $order->variant->name }})</span>
                                                @endif
                                            </p>
                                            <p
                                                class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                                <x-lucide-hash class="w-3 h-3" /></i>
                                                PI-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="text-sm font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-950 px-3 py-1 rounded-lg">
                                        {{ $order->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sm font-medium text-slate-600 dark:text-slate-400">
                                    {{ $order->unit_cost ? ($appSettings['currency_symbol'] ?? '₹') . number_format($order->unit_cost, 2) : '—' }}
                                </td>
                                <td class="px-6 py-5 text-sm font-black text-slate-900 dark:text-white">
                                    {{ $order->unit_cost ? ($appSettings['currency_symbol'] ?? '₹') . number_format($order->unit_cost * $order->quantity, 2) : '—' }}
                                </td>
                                <td class="px-6 py-5">
                                    <div
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-tight {{ $statusColors[$order->status] }}">
                                        @svg($statusIcons[$order->status], 'w-3.5 h-3.5')
                                        {{ $statusLabels[$order->status] }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div
                                        class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium">
                                        <x-lucide-calendar class="w-4 h-4 text-slate-300 dark:text-slate-600" /></i>
                                        @if ($order->status === 'received' && $order->received_at)
                                            {{ \Carbon\Carbon::parse($order->received_at)->format('n/j/Y') }}
                                        @elseif($order->estimated_arrival)
                                            {{ \Carbon\Carbon::parse($order->estimated_arrival)->format('n/j/Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($order->status === 'pending')
                                            <form action="{{ route('purchase-orders.updateStatus', $order->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="transit">
                                                <button type="submit"
                                                    class="text-xs font-bold text-sky-700 dark:text-sky-400 bg-sky-50 dark:bg-sky-900/30 border border-sky-100 dark:border-sky-800 px-4 py-2 rounded-xl hover:bg-sky-100 dark:hover:bg-sky-900 transition shadow-sm">
                                                    Mark in Transit
                                                </button>
                                            </form>
                                        @endif
                                        @if ($order->status === 'transit')
                                            <form action="{{ route('purchase-orders.confirm-received', $order->id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900 border border-emerald-100 dark:border-emerald-800 px-4 py-2 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-800 transition shadow-sm flex items-center gap-1.5">
                                                    <x-lucide-check-circle-2 class="w-3.5 h-3.5" /></i> Confirm Receipt
                                                </button>
                                            </form>
                                        @endif
                                        @if ($order->status === 'received')
                                            <div
                                                class="text-xs font-bold text-slate-400 dark:text-slate-500 flex items-center gap-1.5 justify-end">
                                                <x-lucide-check-circle-2
                                                    class="w-3.5 h-3.5 text-emerald-500 dark:text-emerald-400" /></i>
                                                Completed
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-2 opacity-20">
                                        <x-lucide-package class="w-12 h-12" /></i>
                                        <p class="text-sm font-bold tracking-tight">No reorders found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $incomingCount = $orders->where('status', '!=', 'received')->count();
            $incomingQuantity = $orders->where('status', '!=', 'received')->sum('quantity');

            $lowStockItems = collect();
            foreach($products as $p) {
                if ($p->variants && $p->variants->isNotEmpty()) {
                    foreach($p->variants as $v) {
                        if ($v->isLowStock()) {
                            $lowStockItems->push((object)[
                                'name' => $p->name . ' - ' . $v->name,
                                'stock' => $v->stock_quantity,
                                'threshold' => $v->low_stock_threshold ?? 5,
                                'cost' => $v->cost_price ?? null
                            ]);
                        }
                    }
                } else {
                    if ($p->isLowStock()) {
                        $lowStockItems->push((object)[
                            'name' => $p->name,
                            'stock' => $p->stock_quantity,
                            'threshold' => $p->low_stock_threshold ?? 5,
                            'cost' => $p->cost_price ?? null
                        ]);
                    }
                }
            }
            $lowStockItems = $lowStockItems->sortBy('stock')->take(5); // Show top 5 lowest
        @endphp

        <!-- KPI Sidebar Suggestions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
                class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[32px] p-8 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
                    <x-lucide-trending-up class="w-24 h-24" /></i>
                </div>
                <h3 class="text-lg font-bold mb-2 flex items-center gap-2">
                    Stock Projection
                </h3>
                <p class="text-slate-400 text-sm mb-6 max-w-xs leading-relaxed">
                    Currently tracking {{ $incomingCount }} incoming shipments. Total units in transit:
                    {{ $incomingQuantity }}.
                </p>
                <div class="flex gap-4">
                    <div class="bg-white/10 px-4 py-3 rounded-2xl backdrop-blur-md border border-white/5">
                        <p class="text-[10px] text-slate-400 uppercase font-black tracking-widest mb-1">Incoming</p>
                        <p class="text-2xl font-black">{{ $incomingQuantity }}</p>
                    </div>
                </div>
            </div>

            <!-- Low Stock Widgets -->
            <div class="bg-white dark:bg-slate-900 rounded-[32px] p-6 lg:p-8 shadow-[0_4px_24px_rgba(0,0,0,0.06)] border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <x-lucide-alert-triangle class="w-5 h-5 text-red-500" /> Action Required (Low Stock)
                    </h3>
                    <span class="bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 py-1 px-3 rounded-full text-xs font-black">{{ $lowStockItems->count() }} items</span>
                </div>

                @if($lowStockItems->isEmpty())
                    <div class="text-center py-6">
                        <x-lucide-check-circle-2 class="w-10 h-10 text-emerald-400 mx-auto mb-2 opacity-50" />
                        <p class="text-sm font-bold text-slate-500">All products are well stocked!</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($lowStockItems as $item)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $item->name }}</h4>
                                <p class="text-xs text-slate-500 mt-1">
                                    Current Stock: <span class="font-bold {{ $item->stock < 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $item->stock }}</span> 
                                    <span class="text-slate-300 dark:text-slate-600 mx-1">|</span> Threshold: {{ $item->threshold }}
                                </p>
                            </div>
                            <!-- Livewire button could go here or jump to top modal -->
                            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="text-xs font-bold bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 px-3 py-1.5 rounded-xl transition">
                                Reorder
                            </button>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
