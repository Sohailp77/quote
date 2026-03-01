@props(['quotes', 'currency' => '₹', 'isBoss' => false])

@php
    $filters = ['all', 'accepted', 'sent', 'draft', 'rejected'];
    $bossStatuses = ['sent', 'accepted', 'rejected', 'expired'];
@endphp

<div x-data="{ filter: 'all', expandedId: null }">
    <!-- Filters -->
    <div class="flex gap-1.5 mb-4 flex-wrap">
        @foreach ($filters as $f)
            <button @click="filter = '{{ $f }}'"
                class="px-3 py-1 rounded-full text-xs font-semibold capitalize transition"
                :class="filter === '{{ $f }}' ? 'bg-slate-900 dark:bg-brand-500 text-white' :
                    'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700'">
                @if ($f === 'all')
                    All ({{ count($quotes) }})
                @else
                    {{ $f }}
                @endif
            </button>
        @endforeach
    </div>

    <!-- Quotes List -->
    <div class="space-y-2.5">
        @forelse($quotes->take(6) as $q)
            @php
                // Replicate STATUS util logic
                $st = match ($q->status) {
                    'draft' => [
                        'bg' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                        'label' => 'Draft',
                        'iconBg' => 'bg-slate-100 dark:bg-slate-800',
                        'iconColor' => 'text-slate-500 dark:text-slate-400',
                    ],
                    'sent' => [
                        'bg' => 'bg-brand-100 text-brand-700 dark:bg-brand-900 dark:text-brand-300',
                        'label' => 'Sent',
                        'iconBg' => 'bg-brand-100 dark:bg-brand-900',
                        'iconColor' => 'text-brand-600 dark:text-brand-400',
                    ],
                    'accepted' => [
                        'bg' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-400',
                        'label' => 'Accepted',
                        'iconBg' => 'bg-emerald-100 dark:bg-emerald-900',
                        'iconColor' => 'text-emerald-600 dark:text-emerald-500',
                    ],
                    'rejected' => [
                        'bg' => 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-400',
                        'label' => 'Rejected',
                        'iconBg' => 'bg-rose-100 dark:bg-rose-900',
                        'iconColor' => 'text-rose-600 dark:text-rose-500',
                    ],
                    'expired' => [
                        'bg' => 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-400',
                        'label' => 'Expired',
                        'iconBg' => 'bg-orange-100 dark:bg-orange-900',
                        'iconColor' => 'text-orange-600 dark:text-orange-500',
                    ],
                    default => [
                        'bg' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                        'label' => 'Draft',
                        'iconBg' => 'bg-slate-100 dark:bg-slate-800',
                        'iconColor' => 'text-slate-500 dark:text-slate-400',
                    ],
                };
            @endphp
            <div x-show="filter === 'all' || filter === '{{ $q->status }}'"
                class="rounded-2xl border transition-[background,border-color,box-shadow] duration-200 overflow-hidden"
                :class="expandedId === {{ $q->id }} ?
                    'bg-slate-50/50 dark:bg-slate-800/50 border-slate-200 dark:border-slate-700 shadow-sm' :
                    'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800 hover:border-slate-200 dark:hover:border-slate-700 hover:shadow-[0_4px_16px_rgba(0,0,0,0.03)] dark:hover:shadow-none'">

                <button @click="expandedId = expandedId === {{ $q->id }} ? null : {{ $q->id }}"
                    class="w-full flex items-center justify-between p-4 text-left">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors {{ $st['iconBg'] }} {{ $st['iconColor'] }}">
                            <x-lucide-file-text class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                                {{ $q->customer_name }}
                            </p>
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $q->reference_id }} <span class="text-slate-300 dark:text-slate-600 mx-1">•</span>
                                {{ $q->created_at->diffForHumans() }}
                                @if ($isBoss && $q->user)
                                    <span class="text-slate-300 dark:text-slate-600 mx-1">•</span>
                                    <span class="text-slate-600 dark:text-slate-400">{{ $q->user->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 flex-shrink-0">
                        <div class="text-right flex flex-col items-end">
                            <p class="text-[15px] font-black text-slate-900 dark:text-white">
                                {{ $currency }}{{ number_format($q->total_amount, 2) }}
                            </p>
                            <span
                                class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-md mt-1 {{ $st['bg'] }} dark:bg-opacity-20">{{ $st['label'] }}</span>
                        </div>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-colors"
                            :class="expandedId === {{ $q->id }} ? 'bg-white dark:bg-slate-800 shadow-sm' :
                                'bg-slate-50/50 dark:bg-slate-800/50'">
                            <x-lucide-chevron-right
                                class="w-4 h-4 text-slate-400 dark:text-slate-500 transition-transform duration-300"
                                x-bind:class="expandedId === {{ $q->id }} ? 'rotate-90' : ''" />
                        </div>
                    </div>
                </button>

                <div x-show="expandedId === {{ $q->id }}" x-collapse.duration.300ms>
                    <div
                        class="px-4 pb-4 pt-2 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-200/50 dark:border-slate-700/50">

                        @if ($q->status === 'accepted' && ($q->delivery_date || $q->delivery_partner || $q->tracking_number))
                            @php
                                $isOverdue =
                                    $q->delivery_date &&
                                    $q->delivery_date->isPast() &&
                                    !in_array($q->delivery_status, ['delivered']);
                            @endphp
                            <div class="mb-3 mt-2 p-3 {{ $isOverdue ? 'bg-red-50/60 dark:bg-red-900/20 border-red-200/60 dark:border-red-800/40' : 'bg-emerald-50/60 dark:bg-emerald-900/20 border-emerald-200/60 dark:border-emerald-800/40' }} border rounded-2xl"
                                x-data="{ editOpen: false, deliveredOpen: false }">
                                <div class="flex items-center justify-between mb-2.5">
                                    <p
                                        class="text-[10px] font-black uppercase tracking-widest {{ $isOverdue ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }} flex items-center gap-1.5">
                                        <x-lucide-truck class="w-3 h-3" /> Delivery Details
                                        @if ($isOverdue)
                                            <span
                                                class="ml-1 px-1.5 py-0.5 rounded-full text-[9px] font-black bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 animate-pulse">OVERDUE</span>
                                        @endif
                                    </p>
                                    @if ($isBoss && $q->delivery_status !== 'delivered')
                                        <div class="flex items-center gap-1.5">
                                            <button @click="editOpen = true" type="button"
                                                class="text-[10px] font-bold px-2 py-1 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 transition-all flex items-center gap-1">
                                                <x-lucide-pencil class="w-2.5 h-2.5" /> Edit
                                            </button>
                                            <button @click="deliveredOpen = true" type="button"
                                                class="text-[10px] font-bold px-2 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-all flex items-center gap-1">
                                                <x-lucide-check class="w-2.5 h-2.5" /> Mark Delivered
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2">
                                    @if ($q->delivery_date)
                                        <div>
                                            <p
                                                class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase">
                                                Date</p>
                                            <p
                                                class="text-xs font-bold {{ $isOverdue ? 'text-red-700 dark:text-red-400' : 'text-slate-700 dark:text-slate-200' }}">
                                                {{ $q->delivery_date->format('M d, Y') }}</p>
                                        </div>
                                    @endif
                                    @if ($q->delivery_time)
                                        <div>
                                            <p
                                                class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase">
                                                Time</p>
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                                {{ \Carbon\Carbon::parse($q->delivery_time)->format('g:i A') }}</p>
                                        </div>
                                    @endif
                                    @if ($q->delivery_partner)
                                        <div>
                                            <p
                                                class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase">
                                                Partner</p>
                                            <p class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                                {{ $q->delivery_partner }}</p>
                                        </div>
                                    @endif
                                    @if ($q->tracking_number)
                                        <div>
                                            <p
                                                class="text-[10px] text-slate-400 dark:text-slate-500 font-semibold uppercase">
                                                Tracking #</p>
                                            <p class="text-xs font-mono font-bold text-slate-700 dark:text-slate-200">
                                                {{ $q->tracking_number }}</p>
                                        </div>
                                    @endif
                                </div>
                                @if ($q->delivery_note)
                                    <p
                                        class="mt-2 text-xs text-slate-500 dark:text-slate-400 italic border-t border-slate-200/60 dark:border-slate-700/40 pt-2">
                                        📝 {{ $q->delivery_note }}</p>
                                @endif
                                @if ($q->delivery_status)
                                    <div
                                        class="mt-2 pt-2 border-t {{ $isOverdue ? 'border-red-200/60 dark:border-red-800/40' : 'border-emerald-200/60 dark:border-emerald-800/40' }}">
                                        @php
                                            $dsBadge = match ($q->delivery_status) {
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
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full capitalize {{ $dsBadge }}">{{ $q->delivery_status }}</span>
                                    </div>
                                @endif

                                {{-- Edit Delivery Modal --}}
                                <div x-show="editOpen" style="display:none"
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                    <div x-show="editOpen" x-transition.opacity
                                        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                                        @click="editOpen = false"></div>
                                    <div x-show="editOpen" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-md overflow-hidden z-10">
                                        <form action="{{ route('quotes.updateDelivery', $q->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <div class="p-6">
                                                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-1">Edit
                                                    Delivery — <span
                                                        class="text-brand-500">{{ $q->reference_id }}</span></h3>
                                                <p class="text-xs text-slate-500 mb-5">Update logistics details. Leave
                                                    blank to clear a field.</p>
                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                                Date</label>
                                                            <input type="date" name="delivery_date"
                                                                value="{{ $q->delivery_date?->format('Y-m-d') }}"
                                                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                        </div>
                                                        <div>
                                                            <label
                                                                class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Time</label>
                                                            <input type="time" name="delivery_time"
                                                                value="{{ $q->delivery_time ? \Carbon\Carbon::parse($q->delivery_time)->format('H:i') : '' }}"
                                                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                            Partner</label>
                                                        <input type="text" name="delivery_partner"
                                                            value="{{ $q->delivery_partner }}"
                                                            placeholder="e.g. BlueDart"
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Tracking
                                                            / Vehicle #</label>
                                                        <input type="text" name="tracking_number"
                                                            value="{{ $q->tracking_number }}" placeholder="Optional"
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Status</label>
                                                        <select name="delivery_status"
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                                            <option value="pending"
                                                                {{ $q->delivery_status === 'pending' ? 'selected' : '' }}>
                                                                Pending</option>
                                                            <option value="shipped"
                                                                {{ $q->delivery_status === 'shipped' ? 'selected' : '' }}>
                                                                Shipped</option>
                                                            <option value="delivered"
                                                                {{ $q->delivery_status === 'delivered' ? 'selected' : '' }}>
                                                                Delivered</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Note
                                                            <span
                                                                class="text-slate-400 font-normal normal-case">(optional)</span></label>
                                                        <textarea name="delivery_note" rows="2" placeholder="e.g. Rescheduled due to weather delay"
                                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all resize-none">{{ $q->delivery_note }}</textarea>
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
                                    class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                    <div x-show="deliveredOpen" x-transition.opacity
                                        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                                        @click="deliveredOpen = false"></div>
                                    <div x-show="deliveredOpen" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-sm overflow-hidden z-10">
                                        <form action="{{ route('quotes.updateDelivery', $q->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="delivery_status" value="delivered">
                                            <div class="p-6">
                                                <div
                                                    class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mb-4">
                                                    <x-lucide-check-circle
                                                        class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                                                </div>
                                                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-1">Mark
                                                    as Delivered</h3>
                                                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">
                                                    {{ $q->reference_id }} — {{ $q->customer_name }}</p>
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
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <a href="{{ route('quotes.pdf', $q->id) }}" target="_blank"
                                class="flex items-center gap-1.5 text-xs font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3.5 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all shadow-sm">
                                <x-lucide-printer class="w-3.5 h-3.5" /> View PDF
                            </a>

                            @if ($isBoss)
                                @foreach ($bossStatuses as $s)
                                    @if ($s !== $q->status)
                                        @if ($s === 'accepted')
                                            <!-- Custom Delivery Modal Trigger -->
                                            <button type="button"
                                                @click="$dispatch('open-delivery-modal-{{ $q->id }}')"
                                                class="text-xs font-bold px-3.5 py-2 rounded-xl border border-transparent shadow-sm transition-all capitalize flex items-center gap-1.5 text-emerald-700 dark:text-emerald-400 bg-emerald-50/20 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 border-emerald-200/50/50 dark:border-emerald-800/50 hover:border-emerald-300 dark:hover:border-emerald-700">
                                                <x-lucide-check-circle class="w-3 h-3 opacity-50" /> Accept & Schedule
                                            </button>

                                            <!-- The Modal -->
                                            <div x-data="{ open: false }"
                                                @open-delivery-modal-{{ $q->id }}.window="open = true"
                                                x-show="open" style="display: none;"
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4">

                                                <!-- Backdrop -->
                                                <div x-show="open" x-transition.opacity
                                                    class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                                                    @click="open = false"></div>

                                                <!-- Modal Content -->
                                                <div x-show="open"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                                                    class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-md overflow-hidden z-10">

                                                    <form action="{{ route('quotes.updateStatus', $q->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="accepted">

                                                        <div class="p-6">
                                                            <h3
                                                                class="text-lg font-black text-slate-900 dark:text-white mb-1">
                                                                <span class="text-emerald-500">Accept</span>
                                                                {{ $q->reference_id }}
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
                                                                            class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                                            Time</label>
                                                                        <input type="time" name="delivery_time"
                                                                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                                    </div>
                                                                </div>

                                                                <div>
                                                                    <label
                                                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery
                                                                        Partner / Courier</label>
                                                                    <input type="text" name="delivery_partner"
                                                                        placeholder="e.g. FedEx, BlueDart, Own Fleet"
                                                                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                                                </div>

                                                                <div>
                                                                    <label
                                                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Tracking
                                                                        / Vehicle Number</label>
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
                                                                class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-emerald-500/50">Save
                                                                & Accept</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <form action="{{ route('quotes.updateStatus', $q->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $s }}">
                                                <button type="submit"
                                                    class="text-xs font-bold px-3.5 py-2 rounded-xl border border-transparent shadow-sm transition-all capitalize flex items-center gap-1.5
                                                                                                                {{ $s === 'accepted'
                                                                                                                    ? 'text-emerald-700 dark:text-emerald-400 bg-emerald-50/20 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 border-emerald-200/50/50 dark:border-emerald-800/50 hover:border-emerald-300 dark:hover:border-emerald-700'
                                                                                                                    : ($s === 'rejected'
                                                                                                                        ? 'text-rose-700 dark:text-rose-400 bg-rose-50/20 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 border-rose-200/50/50 dark:border-rose-800/50 hover:border-rose-300 dark:hover:border-rose-700'
                                                                                                                        : 'text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border-slate-200 dark:border-slate-700 shadow-sm') }}">
                                                    <x-lucide-arrow-right class="w-3 h-3 opacity-50" /> Mark as
                                                    {{ $s }}
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                @endforeach
                            @elseif($q->status === 'draft')
                                <form action="{{ route('quotes.updateStatus', $q->id) }}" method="POST"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="sent">
                                    <button type="submit"
                                        class="flex items-center gap-1.5 text-xs font-bold text-brand-700 dark:text-brand-300 dark:text-brand-400 bg-brand-50/20 dark:bg-brand-900/20 border border-brand-200/50/50 dark:border-brand-800/50 px-3.5 py-2 rounded-xl hover:bg-brand-100 dark:hover:bg-brand-800 dark:hover:bg-brand-900/40 transition-all shadow-sm">
                                        <x-lucide-send class="w-3.5 h-3.5" /> Mark as Sent
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-slate-400 dark:text-slate-500">
                <x-lucide-file-text class="w-8 h-8 mx-auto mb-2 opacity-30" />
                <p class="text-sm">No quotes found here yet</p>
            </div>
        @endforelse
    </div>
</div>
