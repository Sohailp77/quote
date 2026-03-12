@props(['quote'])

<div x-data="{ open: false }"
    @open-accept-modal-{{ $quote->id }}.window="open = true"
    x-show="open" style="display: none;"
    class="fixed inset-0 z-[100] flex items-center justify-center p-4">

    <!-- Backdrop -->
    <div x-show="open" x-transition.opacity
        class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
        @click="open = false"></div>

    <!-- Modal Content -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-200 dark:border-slate-800 w-full max-w-2xl overflow-hidden z-[101] text-left">

        <form action="{{ route('quotes.updateStatus', $quote->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="accepted">

            <div class="p-6">
                <h3 class="text-lg font-black text-slate-900 dark:text-white mb-1">
                    <span class="text-emerald-500">Accept</span> {{ $quote->reference_id }}
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    Optionally add delivery tracking and payment details below before saving.
                </p>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Payment Status <span class="text-rose-500">*</span></label>
                            <select name="payment_status" required class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                <option value="pending">Pending</option>
                                <option value="partial">Partial Payment</option>
                                <option value="paid">Fully Paid</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Payment Method</label>
                            <select name="payment_method" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                                <option value="">Select Method</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="UPI">UPI</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="h-px bg-slate-100 dark:bg-slate-800 w-full my-4"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery Date</label>
                            <input type="date" name="delivery_date" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery Time</label>
                            <input type="time" name="delivery_time" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Delivery Partner / Courier</label>
                        <input type="text" name="delivery_partner" placeholder="e.g. FedEx, BlueDart, Own Fleet" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Tracking / Vehicle Number</label>
                        <input type="text" name="tracking_number" placeholder="Optional" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 dark:bg-slate-800/50 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" @click="open = false" class="px-4 py-2 text-sm font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all focus:ring-2 focus:ring-emerald-500/50">Save & Accept</button>
            </div>
        </form>
    </div>
</div>
