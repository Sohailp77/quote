<x-app-layout>
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Quotations</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage and view all generated quotes and
                invoices.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('quotes.create') }}"
                class="inline-flex items-center gap-2 bg-brand-600 dark:bg-brand-500 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:bg-brand-700 dark:hover:bg-brand-400 transition">
                <x-lucide-plus class="w-4 h-4" /> New Quote
            </a>
        </div>
    </div>

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
                        @if(auth()->user()->isBoss())
                            <th class="px-6 py-4">Created By</th>
                        @endif
                        <th class="px-6 py-4 whitespace-nowrap">Total Amount</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($quotes as $quote)
                        @php
                            $currency = \App\Models\CompanySetting::where('key', 'currency_symbol')->value('value') ?? '₹';

                            $statusColors = [
                                'draft' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 border-slate-200 dark:border-slate-700',
                                'sent' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400 border-sky-200 dark:border-sky-800/50',
                                'accepted' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800/50',
                                'rejected' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border-red-200 dark:border-red-800/50',
                                'expired' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border-amber-200 dark:border-amber-800/50',
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
                                    @if($quote->customer_phone || $quote->customer_email)
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            {{ $quote->customer_phone ?? $quote->customer_email }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            @if(auth()->user()->isBoss())
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
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">

                                    @if($quote->status === 'draft')
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
                                <x-lucide-file-text class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" />
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

        @if($quotes->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $quotes->links() }}
            </div>
        @endif
    </div>
</x-app-layout>