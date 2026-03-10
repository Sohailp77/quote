<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Customers CRM</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Manage client relationships and track Lifetime
                Value.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <x-search-bar placeholder="Search customers..." />
            <a href="{{ route('customers.create') }}"
                class="inline-flex items-center justify-center gap-2 bg-brand-600 dark:bg-brand-500 text-white px-4 py-2 hover:bg-brand-700 dark:hover:bg-brand-400 rounded-2xl text-sm font-semibold shadow-sm transition h-[42px]">
                <x-lucide-plus class="w-4 h-4" /> <span class="hidden sm:inline">New Customer</span>
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
                        <th class="px-6 py-4 whitespace-nowrap">Name</th>
                        <th class="px-6 py-4 whitespace-nowrap">Company</th>
                        <th class="px-6 py-4 whitespace-nowrap">Email & Phone</th>
                        <th class="px-6 py-4 whitespace-nowrap">Payment Health</th>
                        <th class="px-6 py-4 whitespace-nowrap">Added On</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('customers.show', $customer->id) }}"
                                    class="font-bold text-slate-900 dark:text-white hover:text-brand-600 dark:hover:text-brand-400">
                                    {{ $customer->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $customer->company ?: '—' }}</div>
                                @if($customer->address)
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 max-w-[150px] truncate" title="{{ $customer->address }}">
                                        {{ $customer->address }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-medium">
                                <div class="flex flex-col gap-1 text-slate-500 dark:text-slate-400">
                                    @if ($customer->email)
                                        <div class="flex items-center gap-1.5"><x-lucide-mail class="w-3.5 h-3.5" />
                                            {{ $customer->email }}</div>
                                    @endif
                                    @if ($customer->phone)
                                        <div class="flex items-center gap-1.5"><x-lucide-phone class="w-3.5 h-3.5" />
                                            {{ $customer->phone }}</div>
                                    @endif
                                    @if (!$customer->email && !$customer->phone)
                                        —
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="flex flex-col gap-1">
                                    @php
                                        $healthBadge = match ($customer->payment_health) {
                                            'good' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                            'new' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                            default => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                        };
                                        $healthLabel = match ($customer->payment_health) {
                                            'good' => 'Paid Up',
                                            'pending' => 'Pending Balance',
                                            'new' => 'No Orders',
                                            default => 'Unknown',
                                        };
                                        $currency = \App\Models\CompanySetting::getCurrencySymbol();
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $healthBadge }}">
                                        {{ $healthLabel }}
                                    </span>
                                    @if($customer->pending_balance > 0)
                                        <span class="text-[10px] font-black text-rose-600 dark:text-rose-400 px-1">
                                            {{ $currency }}{{ number_format($customer->pending_balance, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                {{ $customer->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('customers.show', $customer->id) }}"
                                        class="p-2 text-slate-400 hover:text-brand-600 dark:hover:text-brand-400 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                        title="View Customer Profile">
                                        <x-lucide-eye class="w-4 h-4" />
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}"
                                        class="p-2 text-slate-400 hover:text-sky-600 dark:hover:text-sky-400 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors"
                                        title="Edit Customer">
                                        <x-lucide-edit class="w-4 h-4" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <x-lucide-users class="w-12 h-12 mx-auto mb-3 text-slate-300 dark:text-slate-600" />
                                <p class="text-base font-medium text-slate-900 dark:text-white">No customers found</p>
                                <p class="text-sm mt-1">Start building your client pipeline.</p>
                                <a href="{{ route('customers.create') }}"
                                    class="inline-block mt-4 text-brand-600 dark:text-brand-400 font-medium hover:underline">
                                    Add New Customer
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
