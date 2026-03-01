<x-app-layout>
    {{-- Page Header --}}
    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Quotations</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Manage and view all generated quotes.</p>
        </div>
        <a href="{{ route('quotes.create') }}"
            class="inline-flex items-center gap-2 bg-brand-600 dark:bg-brand-500 text-white px-4 py-2 hover:bg-brand-700 dark:hover:bg-brand-400 rounded-2xl text-sm font-semibold shadow-sm transition h-[42px] flex-shrink-0">
            <x-lucide-plus class="w-4 h-4" /> <span class="hidden sm:inline">New Quote</span>
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('quotes.index') }}"
        class="mb-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-4"
        x-data="{ showDates: {{ ($filters['date_from'] ?? false) || ($filters['date_to'] ?? false) ? 'true' : 'false' }} }">

        <div class="flex flex-wrap items-end gap-3">

            {{-- Search --}}
            <div class="flex-1 min-w-[180px]">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Search</label>
                <div class="relative">
                    <x-lucide-search
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                        placeholder="Name, ref, phone…"
                        class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                </div>
            </div>

            {{-- Status --}}
            <div class="min-w-[140px]">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Status</label>
                <select name="status"
                    class="w-full py-2 px-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <option value="">All Statuses</option>
                    @foreach (['draft', 'sent', 'accepted', 'rejected', 'expired'] as $s)
                        <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Delivery Status --}}
            <div class="min-w-[150px]">
                <label
                    class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Delivery</label>
                <select name="delivery_status"
                    class="w-full py-2 px-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                    <option value="">All Deliveries</option>
                    <option value="pending" {{ ($filters['delivery_status'] ?? '') === 'pending' ? 'selected' : '' }}>
                        Pending</option>
                    <option value="shipped" {{ ($filters['delivery_status'] ?? '') === 'shipped' ? 'selected' : '' }}>
                        Shipped</option>
                    <option value="delivered"
                        {{ ($filters['delivery_status'] ?? '') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>

            {{-- Date Range Toggle --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Date
                    Range</label>
                <button type="button" @click="showDates = !showDates"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-xl border transition-all"
                    :class="showDates ?
                        'bg-brand-50 dark:bg-brand-900/30 border-brand-300 dark:border-brand-700 text-brand-700 dark:text-brand-400' :
                        'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'">
                    <x-lucide-calendar class="w-3.5 h-3.5" />
                    <span x-text="showDates ? 'Hide Dates' : 'Custom Date'"></span>
                </button>
            </div>

            {{-- Overdue Quick Filter --}}
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Quick</label>
                <a href="{{ route('quotes.index', array_merge(request()->except(['overdue', 'page']), $filters['overdue'] ?? false ? [] : ['overdue' => 1])) }}"
                    class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-xl border transition-all {{ $filters['overdue'] ?? false ? 'bg-red-50 dark:bg-red-900/30 border-red-300 dark:border-red-700 text-red-700 dark:text-red-400' : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                    <x-lucide-alert-triangle class="w-3.5 h-3.5" />
                    Overdue
                </a>
            </div>

            {{-- Apply --}}
            <div>
                <label
                    class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 opacity-0">Go</label>
                <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition-all flex items-center gap-1.5">
                    <x-lucide-filter class="w-3.5 h-3.5" /> Apply
                </button>
            </div>

            {{-- Clear --}}
            @if (array_filter($filters ?? []))
                <div>
                    <label
                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 opacity-0">Clear</label>
                    <a href="{{ route('quotes.index') }}"
                        class="px-3 py-2 text-sm font-semibold text-slate-500 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl transition-all flex items-center gap-1.5">
                        <x-lucide-x class="w-3.5 h-3.5" /> Clear
                    </a>
                </div>
            @endif
        </div>

        {{-- Date Range Fields (collapsible) --}}
        <div x-show="showDates" x-collapse
            class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-3">
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">From</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                    class="py-2 px-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">To</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                    class="py-2 px-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
            </div>
            {{-- Quick presets --}}
            <div class="flex items-end gap-2">
                @php
                    $presets = [
                        'Today' => [now()->toDateString(), now()->toDateString()],
                        'This Week' => [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
                        'This Month' => [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()],
                        'Last Month' => [
                            now()->subMonth()->startOfMonth()->toDateString(),
                            now()->subMonth()->endOfMonth()->toDateString(),
                        ],
                        'This Year' => [now()->startOfYear()->toDateString(), now()->endOfYear()->toDateString()],
                    ];
                @endphp
                @foreach ($presets as $label => [$from, $to])
                    <a href="{{ route('quotes.index', array_merge(request()->except(['date_from', 'date_to', 'page']), ['date_from' => $from, 'date_to' => $to])) }}"
                        class="px-3 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-brand-50 dark:hover:bg-brand-900/30 hover:border-brand-300 dark:hover:border-brand-700 hover:text-brand-700 dark:hover:text-brand-400 transition-all {{ ($filters['date_from'] ?? '') === $from && ($filters['date_to'] ?? '') === $to ? 'bg-brand-50 dark:bg-brand-900/30 border-brand-300 dark:border-brand-700 text-brand-700 dark:text-brand-400' : '' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Active filter badges --}}
        @php
            $activeFilters = array_filter($filters ?? []);
        @endphp
        @if (count($activeFilters))
            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-1.5 items-center">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mr-1">Active:</span>
                @foreach ($activeFilters as $key => $val)
                    @if ($val)
                        @php
                            $label = match ($key) {
                                'search' => 'Search: "' . $val . '"',
                                'status' => 'Status: ' . ucfirst($val),
                                'delivery_status' => 'Delivery: ' . ucfirst($val),
                                'date_from' => 'From: ' . $val,
                                'date_to' => 'To: ' . $val,
                                'overdue' => 'Overdue Only',
                                default => "$key: $val",
                            };
                        @endphp
                        <a href="{{ route('quotes.index', array_merge(request()->except([$key, 'page']))) }}"
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-100 dark:bg-brand-900/30 text-brand-700 dark:text-brand-400 border border-brand-200 dark:border-brand-800 hover:bg-red-100 dark:hover:bg-red-900/30 hover:text-red-700 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-800 transition-all">
                            {{ $label }} <x-lucide-x class="w-2.5 h-2.5" />
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </form>


    <!-- Data Table Card -->
    <div
        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-slate-700 dark:text-slate-300 font-semibold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Quote Ref</th>
                        <th class="px-6 py-4 whitespace-nowrap">Date</th>
                        <th class="px-6 py-4">Customer</th>
                        @if (auth()->user()->isBoss())
                            <th class="px-6 py-4">Created By</th>
                        @endif
                        <th class="px-6 py-4 whitespace-nowrap">Total Amount</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Delivery</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($quotes as $quote)
                        @php
                            $currency =
                                \App\Models\CompanySetting::where('key', 'currency_symbol')->value('value') ?? '₹';

                            $statusColors = [
                                'draft' =>
                                    'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                                'sent' =>
                                    'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400 border-sky-200 dark:border-sky-800/50',
                                'accepted' =>
                                    'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/50',
                                'rejected' =>
                                    'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800/50',
                                'expired' =>
                                    'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800/50',
                            ];
                            $statusColor = $statusColors[$quote->status] ?? $statusColors['draft'];
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $quote->reference_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $quote->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="font-medium text-slate-900 dark:text-white">{{ $quote->customer_name }}
                                    </div>
                                    @if ($quote->customer_phone || $quote->customer_email)
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            {{ $quote->customer_phone ?? $quote->customer_email }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            @if (auth()->user()->isBoss())
                                <td class="px-6 py-4">
                                    {{ $quote->user->name ?? 'Unknown' }}
                                </td>
                            @endif
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                {{ $currency }}{{ number_format($quote->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $statusColor }} capitalize">
                                    {{ $quote->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4" x-data="{ editOpen: false, deliveredOpen: false }">
                                @if ($quote->status === 'accepted' && ($quote->delivery_date || $quote->delivery_partner || $quote->tracking_number))
                                    @php
                                        $isOverdue =
                                            $quote->delivery_date &&
                                            $quote->delivery_date->isPast() &&
                                            !in_array($quote->delivery_status, ['delivered']);
                                    @endphp
                                    <div class="space-y-0.5">
                                        @if ($quote->delivery_date)
                                            <div
                                                class="flex items-center gap-1.5 text-xs {{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-slate-700 dark:text-slate-300' }}">
                                                <x-lucide-calendar
                                                    class="w-3 h-3 {{ $isOverdue ? 'text-red-500' : 'text-emerald-500' }} flex-shrink-0" />
                                                <span
                                                    class="font-semibold">{{ $quote->delivery_date->format('M d, Y') }}</span>
                                                @if ($quote->delivery_time)
                                                    <span
                                                        class="text-slate-400">{{ \Carbon\Carbon::parse($quote->delivery_time)->format('g:i A') }}</span>
                                                @endif
                                                @if ($isOverdue)
                                                    <span
                                                        class="px-1.5 py-0.5 rounded-full text-[9px] font-black bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 animate-pulse">OVERDUE</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($quote->delivery_partner)
                                            <div
                                                class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                                <x-lucide-truck class="w-3 h-3 text-slate-400 flex-shrink-0" />
                                                {{ $quote->delivery_partner }}
                                            </div>
                                        @endif
                                        @if ($quote->tracking_number)
                                            <div class="text-[10px] font-mono text-slate-400 dark:text-slate-500">
                                                # {{ $quote->tracking_number }}
                                            </div>
                                        @endif
                                        @if ($quote->delivery_note)
                                            <p class="text-[10px] text-slate-400 dark:text-slate-500 italic">📝
                                                {{ Str::limit($quote->delivery_note, 50) }}</p>
                                        @endif
                                        @if ($quote->delivery_status)
                                            @php
                                                $dsBadge = match ($quote->delivery_status) {
                                                    'pending'
                                                        => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                                    'shipped'
                                                        => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                                                    'delivered'
                                                        => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                                    default
                                                        => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                                };
                                            @endphp
                                            <span
                                                class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize {{ $dsBadge }}">{{ $quote->delivery_status }}</span>
                                        @endif

                                        @if (auth()->user()->isBoss() && $quote->delivery_status !== 'delivered')
                                            <div class="flex items-center gap-1.5 pt-1">
                                                <button @click="editOpen = true" type="button"
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-1">
                                                    <x-lucide-pencil class="w-2.5 h-2.5" /> Edit
                                                </button>
                                                <button @click="deliveredOpen = true" type="button"
                                                    class="text-[10px] font-bold px-2 py-0.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-all flex items-center gap-1">
                                                    <x-lucide-check class="w-2.5 h-2.5" /> Delivered
                                                </button>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Edit Delivery Modal --}}
                                    <div x-show="editOpen" style="display:none"
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
                                        <div x-show="editOpen" x-transition.opacity
                                            class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                                            @click="editOpen = false"></div>
                                        <div x-show="editOpen" x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-md overflow-hidden z-10">
                                            <form action="{{ route('quotes.updateDelivery', $quote->id) }}"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <div class="p-6">
                                                    <h3 class="text-lg font-black text-slate-900 dark:text-white mb-1">
                                                        Edit Delivery — <span
                                                            class="text-brand-500">{{ $quote->reference_id }}</span>
                                                    </h3>
                                                    <p class="text-xs text-slate-500 mb-5">Update logistics details.
                                                        Leave blank to clear a field.</p>
                                                    <div class="space-y-4">
                                                        <div class="grid grid-cols-2 gap-4">
                                                            <div>
                                                                <label
                                                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                                    Date</label>
                                                                <input type="date" name="delivery_date"
                                                                    value="{{ $quote->delivery_date?->format('Y-m-d') }}"
                                                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Time</label>
                                                                <input type="time" name="delivery_time"
                                                                    value="{{ $quote->delivery_time ? \Carbon\Carbon::parse($quote->delivery_time)->format('H:i') : '' }}"
                                                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                                Partner</label>
                                                            <input type="text" name="delivery_partner"
                                                                value="{{ $quote->delivery_partner }}"
                                                                placeholder="e.g. BlueDart"
                                                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Tracking
                                                                / Vehicle #</label>
                                                            <input type="text" name="tracking_number"
                                                                value="{{ $quote->tracking_number }}"
                                                                placeholder="Optional"
                                                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Status</label>
                                                            <select name="delivery_status"
                                                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                                <option value="pending"
                                                                    {{ $quote->delivery_status === 'pending' ? 'selected' : '' }}>
                                                                    Pending</option>
                                                                <option value="shipped"
                                                                    {{ $quote->delivery_status === 'shipped' ? 'selected' : '' }}>
                                                                    Shipped</option>
                                                                <option value="delivered"
                                                                    {{ $quote->delivery_status === 'delivered' ? 'selected' : '' }}>
                                                                    Delivered</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Note
                                                                <span
                                                                    class="text-slate-400 font-normal normal-case">(optional)</span></label>
                                                            <textarea name="delivery_note" rows="2" placeholder="e.g. Rescheduled due to weather"
                                                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all resize-none">{{ $quote->delivery_note }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="px-6 py-4 bg-slate-50 border-t border-slate-100 dark:bg-slate-800/50 dark:border-slate-800 flex justify-end gap-3">
                                                    <button type="button" @click="editOpen = false"
                                                        class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">Cancel</button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition-all">Save
                                                        Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Mark Delivered Modal --}}
                                    <div x-show="deliveredOpen" style="display:none"
                                        class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
                                        <div x-show="deliveredOpen" x-transition.opacity
                                            class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                                            @click="deliveredOpen = false"></div>
                                        <div x-show="deliveredOpen"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-sm overflow-hidden z-10">
                                            <form action="{{ route('quotes.updateDelivery', $quote->id) }}"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="delivery_status" value="delivered">
                                                <div class="p-6">
                                                    <div
                                                        class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mb-4">
                                                        <x-lucide-check-circle
                                                            class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                                    </div>
                                                    <h3 class="text-lg font-black text-slate-900 dark:text-white mb-1">
                                                        Mark as Delivered</h3>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">
                                                        {{ $quote->reference_id }} — {{ $quote->customer_name }}</p>
                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Note
                                                            <span
                                                                class="text-slate-400 font-normal normal-case">(optional)</span></label>
                                                        <textarea name="delivery_note" rows="3" placeholder="e.g. Delivered early. Customer confirmed receipt."
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all resize-none"></textarea>
                                                    </div>
                                                </div>
                                                <div
                                                    class="px-6 py-4 bg-slate-50 border-t border-slate-100 dark:bg-slate-800/50 dark:border-slate-800 flex justify-end gap-3">
                                                    <button type="button" @click="deliveredOpen = false"
                                                        class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">Cancel</button>
                                                    <button type="submit"
                                                        class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all">🎉
                                                        Confirm Delivery</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            {{-- change 1 <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2"> --}}

                            <td class="px-6 py-4 text-right">
                                <div x-data="{ open: false }" class="flex items-center justify-end gap-2">

                                    @if ($quote->status === 'draft')
                                        <!-- Edit Action (Only Drafts) -->
                                        <a href="{{ route('quotes.edit', $quote->id) }}"
                                            class="p-2 text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                            title="Edit Quote">
                                            <x-lucide-edit class="w-4 h-4" />
                                        </a>

                                        <!-- Mark as Sent (Only Drafts) -->
                                        <form action="{{ route('quotes.updateStatus', $quote->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="sent">
                                            <button type="submit"
                                                class="p-2 text-slate-400 hover:text-sky-600 dark:hover:text-sky-400 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                                title="Mark as Sent to Customer">
                                                <x-lucide-send class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif

                                    @if (auth()->user()->isBoss() && $quote->status !== 'accepted' && $quote->status !== 'draft')
                                        <!-- Custom Delivery Modal Trigger -->
                                        <button type="button" @click="open = true"
                                            class="p-2 text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 rounded-lg transition-colors"
                                            title="Accept & Schedule Delivery">
                                            <x-lucide-check-circle class="w-4 h-4" />
                                        </button>

                                        <!-- Mark as Rejected -->
                                        <form action="{{ route('quotes.updateStatus', $quote->id) }}" method="POST"
                                            class="inline">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit"
                                                class="p-2 text-rose-600 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-lg transition-colors"
                                                title="Mark as Rejected"><x-lucide-x-circle
                                                    class="w-4 h-4" /></button>
                                        </form>

                                        <!-- The Modal -->
                                        <div x-show="open" style="display: none;"
                                            class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
                                            <div x-show="open" x-transition.opacity
                                                class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                                                @click="open = false"></div>
                                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-md overflow-hidden z-10">
                                                <form action="{{ route('quotes.updateStatus', $quote->id) }}"
                                                    method="POST">
                                                    @csrf @method('PATCH') <input type="hidden" name="status"
                                                        value="accepted">
                                                    <div class="p-6">
                                                        <h3
                                                            class="text-lg font-black text-slate-900 dark:text-white mb-1">
                                                            <span class="text-emerald-500">Accept</span>
                                                            {{ $quote->reference_id }}
                                                        </h3>
                                                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                                                            Optionally add delivery tracking details below before
                                                            saving.</p>
                                                        <div class="space-y-4">
                                                            <div class="grid grid-cols-2 gap-4">
                                                                <div>
                                                                    <label
                                                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                                        Date</label>
                                                                    <input type="date" name="delivery_date"
                                                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                                </div>
                                                                <div>
                                                                    <label
                                                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Time</label>
                                                                    <input type="time" name="delivery_time"
                                                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                                    Partner</label>
                                                                <input type="text" name="delivery_partner"
                                                                    placeholder="e.g. FedEx"
                                                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                            </div>
                                                            <div>
                                                                <label
                                                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Tracking
                                                                    / Vehicle #</label>
                                                                <input type="text" name="tracking_number"
                                                                    placeholder="Optional"
                                                                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div
                                                        class="px-6 py-4 bg-slate-50 border-t border-slate-100 dark:bg-slate-800/50 dark:border-slate-800 flex justify-end gap-3">
                                                        <button type="button" @click="open = false"
                                                            class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">Cancel</button>
                                                        <button type="submit"
                                                            class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all">Save
                                                            & Accept</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- View / Print Action (Always Available) -->
                                    <a href="{{ route('quotes.pdf', $quote->id) }}" target="_blank"
                                        class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                        title="View PDF">
                                        <x-lucide-printer class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isBoss() ? '7' : '6' }}"
                                class="px-6 py-12 text-center text-slate-500">
                                <x-lucide-file-text
                                    class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" />
                                <p class="text-base font-medium text-slate-900 dark:text-white">No quotes found</p>
                                <p class="text-sm mt-1">Get started by creating your first quotation.</p>
                                <a href="{{ route('quotes.create') }}"
                                    class="inline-block mt-4 text-brand-600 dark:text-brand-400 font-medium hover:underline">
                                    Create New Quote
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($quotes->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $quotes->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
