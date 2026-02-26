@if(session('stock_warning'))
    @php
        $warning = session('stock_warning');
    @endphp
    <div x-data="{ open: true }" x-show="open" style="display: none;"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="bg-white dark:bg-slate-900 rounded-[32px] w-full max-w-md p-8 shadow-2xl border border-slate-100 dark:border-slate-800"
            @click.away="open = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="w-16 h-16 bg-amber-100/30 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center mb-6">
                <x-lucide-alert-triangle class="w-8 h-8 text-amber-600 dark:text-amber-500" />
            </div>

            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Stock Warning</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">{{ $warning['message'] }}</p>

            <div
                class="space-y-3 mb-8 bg-slate-50 dark:bg-slate-800 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 dark:border-slate-700">
                @foreach($warning['items'] as $item)
                    <div class="flex justify-between items-center text-sm">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $item['name'] }}</span>
                        <span class="text-red-500 dark:text-red-400 font-bold">{{ $item['available'] }} available /
                            {{ $item['requested'] }} needed</span>
                    </div>
                @endforeach
            </div>

            <form action="{{ route('quotes.updateStatus', $warning['quote_id']) }}" method="POST" class="flex gap-3">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="accepted">
                <input type="hidden" name="force" value="1">

                <button type="button" @click="open = false"
                    class="flex-1 px-6 py-3 rounded-2xl bg-slate-100 dark:bg-slate-950 dark:bg-slate-800 text-slate-600 dark:text-slate-400 dark:text-slate-300 font-bold hover:bg-slate-200 dark:hover:bg-slate-800 dark:hover:bg-slate-700 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 px-6 py-3 rounded-2xl bg-slate-900 dark:bg-brand-500 text-white font-bold hover:bg-slate-800 dark:hover:bg-brand-600 transition">
                    Continue Anyway
                </button>
            </form>
        </div>
    </div>
@endif