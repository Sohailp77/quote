<x-app-layout>
    @php
        $currency = $appSettings['currency_symbol'] ?? '₹';
        if (!function_exists('fmt')) {
            function fmt($n, $c)
            {
                return $c . number_format($n, 2);
            }
        }
    @endphp

    <div x-data="ledgerManager({{ Js::from($ledger) }})" class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto font-sans">
        <div class="mb-10">
            <a href="{{ route('analytics.index') }}"
                class="inline-flex items-center gap-2 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] hover:text-slate-900 dark:hover:text-white transition-colors mb-6 group">
                <x-lucide-arrow-left class="w-3 h-3 transition-transform group-hover:-translate-x-1" /></i> Back to
                Analytics
            </a>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-slate-900 rounded-[22px] flex items-center justify-center text-white shadow-xl shadow-slate-900/20">
                            <x-lucide-briefcase class="w-7 h-7" /></i>
                        </div>
                        Business Ledger
                    </h2>
                    <p class="text-slate-400 dark:text-slate-500 font-bold mt-3 text-sm max-w-lg">
                        Deep audit trail of all financial events, stock movements, and revenue generations.
                        <span class="text-slate-900 dark:text-white"> Precision is power.</span>
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl flex p-1 shadow-sm">
                        <button @click="filterType = 'all'"
                            :class="filterType === 'all' ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-900/10' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white'"
                            class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">All</button>
                        <button @click="filterType = 'revenue'"
                            :class="filterType === 'revenue' ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-900/10' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white'"
                            class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">Revenue</button>
                        <button @click="filterType = 'cost'"
                            :class="filterType === 'cost' ? 'bg-slate-900 dark:bg-slate-700 text-white shadow-lg shadow-slate-900/10' : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-white'"
                            class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">Cost</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Stats Bar -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <div class="lg:col-span-3 relative group">
                <x-lucide-search class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 dark:text-slate-500 group-focus-within:text-slate-900 dark:group-focus-within:text-white transition-colors" /></i>
                <input type="text" x-model="searchQuery"
                    placeholder="Search by product, ID, reference, or description..."
                    class="w-full bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-800 rounded-3xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:outline-none focus:border-slate-900 dark:focus:border-slate-600 focus:ring-0 transition-all shadow-sm group-hover:shadow-md" />
                <button x-show="searchQuery.length > 0" @click="searchQuery = ''"
                    class="absolute right-6 top-1/2 -translate-y-1/2 p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl text-slate-400 dark:text-slate-500 transition"
                    style="display: none;">
                    <x-lucide-x class="w-4 h-4" /></i>
                </button>
            </div>

            <button
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl px-6 py-4 flex items-center justify-center gap-3 text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest hover:border-slate-900 dark:hover:border-slate-500 transition-all shadow-sm group">
                <x-lucide-download class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:text-slate-900 dark:group-hover:text-white" /></i>
                Export Data
            </button>
        </div>

        <!-- Ledger Table -->
        <div
            class="bg-white dark:bg-slate-900 rounded-[44px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.05)] border border-slate-100 dark:border-slate-800 overflow-hidden relative">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                    <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                        <tr>
                            <th
                                class="px-10 py-7 text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                                Transaction ID</th>
                            <th
                                class="px-10 py-7 text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                                Status</th>
                            <th
                                class="px-10 py-7 text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                                Entity / Action</th>
                            <th
                                class="px-10 py-7 text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                                Details</th>
                            <th
                                class="px-10 py-7 text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                                Value</th>
                            <th
                                class="px-10 py-7 text-right text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
                        <template x-if="filteredLedger.length === 0">
                            <tr>
                                <td colspan="6" class="px-10 py-32 text-center text-slate-300 dark:text-slate-600">
                                    <div class="flex flex-col items-center gap-4">
                                        <div
                                            class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-[30px] flex items-center justify-center text-slate-200 dark:text-slate-700">
                                            <x-lucide-layout-grid class="w-10 h-10" /></i>
                                        </div>
                                        <p class="font-bold text-slate-400 dark:text-slate-500">No transactions recorded
                                            for this filter.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <template x-for="(entry, index) in filteredLedger" :key="index">
                            <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all">
                                <td class="px-10 py-8 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 group-hover:bg-slate-900 dark:group-hover:bg-white transition-colors">
                                        </div>
                                        <div>
                                            <div class="text-xs font-black text-slate-900 dark:text-white tabular-nums"
                                                x-text="'#' + (1000 + entry.id)"></div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold mt-0.5"
                                                x-text="formatDate(entry.date)"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8 whitespace-nowrap">
                                    <span
                                        :class="entry.is_revenue ? 'bg-emerald-50 dark:bg-emerald-900 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-800' : 'bg-red-50 dark:bg-red-900 text-red-600 dark:text-red-400 border-red-100 dark:border-red-800'"
                                        class="inline-flex px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] border"
                                        x-text="entry.type"></span>
                                    <template x-if="entry.reverted_at">
                                        <span
                                            class="ml-2 inline-flex px-3 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800">Reverted</span>
                                    </template>
                                </td>
                                <td class="px-10 py-8 whitespace-nowrap">
                                    <div class="text-sm font-black text-slate-900 dark:text-white"
                                        x-text="entry.target_item"></div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div
                                            class="w-4 h-4 bg-slate-100 dark:bg-slate-800 rounded-md flex items-center justify-center text-[8px] font-black text-slate-500 dark:text-slate-400 uppercase">
                                            B</div>
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold"
                                            x-text="entry.user || 'System'"></div>
                                    </div>
                                </td>
                                <td class="px-10 py-8">
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium max-w-sm leading-relaxed"
                                        x-text="entry.description"></p>
                                </td>
                                <td class="px-10 py-8 whitespace-nowrap">
                                    <div :class="entry.is_revenue ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white'"
                                        class="text-lg font-black"
                                        x-text="(entry.is_revenue ? '+' : '-') + '{{ $currency }}' + Number(entry.amount).toLocaleString('en-US', {minimumFractionDigits: 2})">
                                    </div>
                                    <template x-if="entry.quantity">
                                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold mt-1"
                                            x-text="Math.abs(entry.quantity) + ' units @ {{ $currency }}' + Number(entry.unit_cost).toLocaleString('en-US', {minimumFractionDigits: 2})">
                                        </div>
                                    </template>
                                </td>
                                <td class="px-10 py-8 whitespace-nowrap text-right">
                                    <div
                                        class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                                        <template x-if="!entry.reverted_at">
                                            <div class="flex gap-2 items-center">
                                                <!-- Edit Button -->
                                                <button @click="openEdit(entry)"
                                                    class="w-10 h-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-slate-900 dark:hover:text-white hover:border-slate-900 dark:hover:border-slate-600 rounded-xl transition-all shadow-sm flex items-center justify-center"
                                                    title="Edit Entry">
                                                    <x-lucide-edit class="w-4 h-4" /></i>
                                                </button>

                                                <!-- Revert/Delete Button -->
                                                <template x-if="!entry.is_revenue && entry.type !== 'Reversion'">
                                                    <form :action="`/stock-adjustments/${entry.id}/revert`"
                                                        method="POST" @submit="confirmRevert($event)">
                                                        @csrf
                                                        <button type="submit"
                                                            class="w-10 h-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-200 dark:hover:border-indigo-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all shadow-sm flex items-center justify-center"
                                                            title="Revert Change">
                                                            <x-lucide-refresh-ccw class="w-4 h-4" /></i>
                                                        </button>
                                                    </form>
                                                </template>

                                                <template x-if="entry.is_revenue">
                                                    <form :action="`/revenues/${entry.id}`" method="POST"
                                                        @submit="confirmDeleteRevenue($event)">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-10 h-10 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200 dark:hover:border-red-700 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-xl transition-all shadow-sm flex items-center justify-center"
                                                            title="Delete Record">
                                                            <x-lucide-trash-2 class="w-4 h-4" /></i>
                                                        </button>
                                                    </form>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal via Alpine -->
        <div x-show="editingItem !== null" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center px-4 overflow-hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                @click="editingItem = null"></div>

            <div class="relative bg-white dark:bg-slate-900 rounded-[40px] shadow-2xl w-full max-w-lg overflow-hidden border border-slate-100 dark:border-slate-800 transform transition-all"
                x-transition.scale.80>
                <div class="px-10 pt-10 pb-6 border-b border-slate-50 dark:border-slate-800">
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
                        <x-lucide-edit class="w-6 h-6 text-slate-400 dark:text-slate-500" /></i>
                        Edit Ledger Entry
                    </h3>
                    <p class="text-slate-400 dark:text-slate-500 text-xs font-bold uppercase tracking-widest mt-2">
                        Adjusting transaction details for audit accuracy</p>
                </div>

                <form :action="editUrl" method="POST" class="p-10">
                    @csrf
                    @method('PATCH')

                    <template x-if="editingItem?.is_revenue">
                        <div class="mb-6">
                            <label for="amount"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Revenue
                                Amount ({{ $currency }})</label>
                            <input id="amount" name="amount" type="number" step="0.01"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900/10 focus:outline-none min-h-[42px]"
                                x-model="editForm.amount" required />
                        </div>
                    </template>

                    <template x-if="editingItem && !editingItem.is_revenue">
                        <div>
                            <div class="mb-6">
                                <label for="reason"
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Adjustment
                                    Reason</label>
                                <input id="reason" name="reason" type="text"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900/10 focus:outline-none min-h-[42px]"
                                    x-model="editForm.reason" required />
                            </div>
                            <div class="mb-6">
                                <label for="unit_cost"
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-2 block">Unit
                                    Cost ({{ $currency }})</label>
                                <input id="unit_cost" name="unit_cost" type="number" step="0.01"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-slate-900/10 focus:outline-none min-h-[42px]"
                                    x-model="editForm.unit_cost" />
                            </div>
                        </div>
                    </template>

                    <div class="flex gap-4 mt-10">
                        <button type="button" @click="editingItem = null"
                            class="flex-1 bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-400 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-700 transition-all min-h-[42px]">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-[2] bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-800 dark:hover:bg-slate-200 transition-all shadow-lg shadow-slate-900/20 dark:shadow-white/10 min-h-[42px]">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('ledgerManager', (initialLedger) => ({
                    ledger: initialLedger,
                    filterType: 'all',
                    searchQuery: '',
                    editingItem: null,
                    editForm: {
                        amount: '',
                        reason: '',
                        unit_cost: ''
                    },

                    get filteredLedger() {
                        let items = this.ledger;

                        if (this.filterType === 'revenue') {
                            items = items.filter(i => i.is_revenue);
                        } else if (this.filterType === 'cost') {
                            items = items.filter(i => !i.is_revenue);
                        }

                        if (!this.searchQuery) return items;

                        const q = this.searchQuery.toLowerCase();
                        return items.filter(entry =>
                            (entry.target_item || '').toLowerCase().includes(q) ||
                            (entry.description || '').toLowerCase().includes(q) ||
                            (entry.type || '').toLowerCase().includes(q)
                        );
                    },

                    get editUrl() {
                        if (!this.editingItem) return '#';
                        return this.editingItem.is_revenue
                            ? `/revenues/${this.editingItem.id}`
                            : `/stock-adjustments/${this.editingItem.id}`;
                    },

                    formatDate(dateString) {
                        const date = new Date(dateString);
                        return date.toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' });
                    },

                    openEdit(item) {
                        this.editingItem = item;
                        this.editForm.amount = item.amount || '';
                        this.editForm.reason = item.description || '';
                        this.editForm.unit_cost = item.unit_cost || '';
                    },

                    confirmRevert(e) {
                        if (!confirm('Are you sure you want to revert this adjustment? This will undo the stock change and associated records.')) {
                            e.preventDefault();
                        }
                    },

                    confirmDeleteRevenue(e) {
                        if (!confirm('Are you sure you want to delete this revenue record?')) {
                            e.preventDefault();
                        }
                    }
                }));
            });
        </script>
    @endpush
</x-app-layout>