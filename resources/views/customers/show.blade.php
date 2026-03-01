<x-app-layout>
    <div class="max-w-7xl mx-auto py-8 lg:py-10">

        <!-- Header & Breadcrumb -->
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <a href="{{ route('customers.index') }}"
                    class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 flex items-center gap-1 mb-2 font-medium transition-colors">
                    <x-lucide-arrow-left class="w-4 h-4" /> Back to Customers
                </a>
                <h1 class="text-3xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                    {{ $customer->name }}
                </h1>

                @if ($customer->company)
                    <div class="flex items-center gap-2 mt-2 text-sm font-semibold text-slate-500 dark:text-slate-400">
                        <x-lucide-building-2 class="w-4 h-4 text-slate-400" />
                        {{ $customer->company }}
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <a href="{{ route('customers.edit', $customer->id) }}"
                    class="inline-flex items-center justify-center gap-2 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-bold px-5 py-2.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm h-[42px] w-full sm:w-auto">
                    <x-lucide-edit class="w-4 h-4" /> Edit Profile
                </a>

                @php
                    $phone = $customer->phone ? preg_replace('/[^0-9]/', '', $customer->phone) : null;
                @endphp
                @if ($phone)
                    <a href="https://wa.me/{{ $phone }}" target="_blank"
                        class="inline-flex items-center justify-center gap-2 bg-[#25D366] hover:bg-[#128C7E] text-white text-sm font-bold px-5 py-2.5 rounded-2xl transition shadow-sm h-[42px] w-full sm:w-auto">
                        <x-lucide-message-circle class="w-4 h-4" /> WhatsApp
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Left Column: Details -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- KPI Card -->
                <div
                    class="bg-gradient-to-br from-brand-600 to-brand-500 rounded-3xl p-6 text-white shadow-xl shadow-brand-500/20 relative overflow-hidden group">
                    <div
                        class="absolute -right-6 -top-6 p-8 opacity-10 group-hover:scale-110 group-hover:-rotate-6 transition-transform duration-500">
                        <x-lucide-trending-up class="w-40 h-40" />
                    </div>
                    <div class="relative z-10">
                        <span
                            class="text-white/80 font-bold uppercase tracking-widest text-[10px] mb-1 flex items-center gap-1.5">
                            Lifetime Value (LTV)
                        </span>
                        <div class="text-4xl font-black mt-1">
                            @php $currency = \App\Models\CompanySetting::where('key', 'currency_symbol')->value('value') ?? '₹'; @endphp
                            <span
                                class="text-xl text-white/60 mr-1">{{ $currency }}</span>{{ number_format($ltv, 2) }}
                        </div>
                        <div
                            class="mt-5 flex gap-4 text-sm font-bold bg-white/10 rounded-2xl p-4 backdrop-blur-md border border-white/10">
                            <div>
                                <span class="block text-white/60 text-[10px] uppercase tracking-widest mb-0.5">Total
                                    Quotes</span>
                                {{ count($quotes) }}
                            </div>
                            <div class="w-px bg-white/20"></div>
                            <div>
                                <span
                                    class="block text-white/60 text-[10px] uppercase tracking-widest mb-0.5">Accepted</span>
                                {{ $quotes->where('status', 'accepted')->count() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Card -->
                <div
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                    <h3
                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                        Contact Information
                    </h3>
                    <div class="space-y-4">
                        @if ($customer->email)
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                                    <x-lucide-mail class="w-4 h-4" />
                                </div>
                                <div class="pt-0.5 min-w-0">
                                    <p
                                        class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                        Email Address</p>
                                    <a href="mailto:{{ $customer->email }}"
                                        class="text-sm font-bold text-slate-900 dark:text-white hover:text-brand-600 transition-colors truncate block mt-0.5">
                                        {{ $customer->email }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($customer->phone)
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                    <x-lucide-phone class="w-4 h-4" />
                                </div>
                                <div class="pt-0.5 min-w-0">
                                    <p
                                        class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                        Phone Number</p>
                                    <a href="tel:{{ preg_replace('/[^0-9\+]/', '', $customer->phone) }}"
                                        class="text-sm font-bold text-slate-900 dark:text-white hover:text-brand-600 transition-colors truncate block mt-0.5">
                                        {{ $customer->phone }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if (!$customer->email && !$customer->phone)
                            <div
                                class="text-sm font-medium text-slate-500 italic text-center py-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                                No contact information added.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Internal Notes -->
                @if ($customer->notes)
                    <div
                        class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 rounded-3xl p-6 relative overflow-hidden">
                        <div class="flex items-center gap-3 mb-3 relative z-10">
                            <x-lucide-sticky-note class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                            <h3 class="text-xs font-black uppercase tracking-widest text-amber-800 dark:text-amber-500">
                                Internal Notes
                            </h3>
                        </div>
                        <p
                            class="text-sm text-amber-900 dark:text-amber-200 leading-relaxed font-medium relative z-10 whitespace-pre-wrap">
                            {{ $customer->notes }}</p>

                        <div
                            class="absolute -right-4 -bottom-4 text-amber-100 dark:text-amber-900/10 pointer-events-none">
                            <x-lucide-quote class="w-24 h-24" />
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Quote History -->
            <div class="lg:col-span-8">
                <div
                    class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                    <div
                        class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <x-lucide-history class="w-5 h-5 text-slate-400" /> Quote History
                        </h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                            <thead
                                class="bg-slate-50/50 dark:bg-slate-800/50 text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-widest font-black">
                                <tr>
                                    <th class="px-6 py-4 whitespace-nowrap">Reference</th>
                                    <th class="px-6 py-4 whitespace-nowrap">Date</th>
                                    <th class="px-6 py-4 whitespace-nowrap">Amount</th>
                                    <th class="px-6 py-4 whitespace-nowrap">Status</th>
                                    <th class="px-6 py-4 text-right whitespace-nowrap">View</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                                @forelse($quotes as $quote)
                                    @php
                                        $statusColors = [
                                            'draft' =>
                                                'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                            'sent' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                                            'accepted' =>
                                                'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'rejected' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                            'expired' =>
                                                'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        ];
                                        $statusColor = $statusColors[$quote->status] ?? $statusColors['draft'];
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                            {{ $quote->reference_id }}
                                        </td>
                                        <td class="px-6 py-4 text-xs font-semibold">
                                            {{ $quote->created_at->format('M d, Y') }}
                                        </td>
                                        <td
                                            class="px-6 py-4 font-black text-slate-900 dark:text-white whitespace-nowrap">
                                            {{ $currency }}{{ number_format($quote->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-lg {{ $statusColor }}">
                                                {{ $quote->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('quotes.pdf', $quote->id) }}" target="_blank"
                                                class="inline-flex p-2 text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors"
                                                title="View PDF">
                                                <x-lucide-printer class="w-4 h-4" />
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center text-slate-500">
                                            <div
                                                class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-700">
                                                <x-lucide-file-text class="w-6 h-6 text-slate-400" />
                                            </div>
                                            <p class="text-base font-bold text-slate-900 dark:text-white">No quotes yet
                                            </p>
                                            <p class="text-sm mt-1">This customer hasn't received any quotes.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
