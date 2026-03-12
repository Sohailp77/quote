@if(session()->has('low_stock_alerts'))
    <div x-data="{ 
            open: false,
            init() {
                // Check if already dismissed today
                const lastDismissed = localStorage.getItem('lowStockAlertDismissed');
                const today = new Date().toDateString();
                
                if (lastDismissed !== today) {
                    this.open = true;
                }
            },
            dismiss() {
                this.open = false;
                localStorage.setItem('lowStockAlertDismissed', new Date().toDateString());
            }
        }" x-show="open" style="display: none;"
        class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="bg-white dark:bg-slate-900 rounded-[32px] w-full max-w-md p-8 shadow-2xl border border-slate-100 dark:border-slate-800"
            @click.away="dismiss()" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="w-16 h-16 bg-red-100/50 dark:bg-red-900/40 rounded-2xl flex items-center justify-center mb-6">
                <x-lucide-bell-ring class="w-8 h-8 text-red-600 dark:text-red-500 animate-pulse" />
            </div>

            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Low Stock Alert</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">The following items have dropped to a low stock level. A summary report will be sent to the owner daily.</p>

            <div
                class="space-y-3 mb-8 bg-slate-50 dark:bg-slate-800 rounded-2xl p-4 border border-slate-100 dark:border-slate-800 dark:border-slate-700">
                @foreach(session('low_stock_alerts') as $alert)
                    <div class="flex items-center text-sm gap-2">
                        <x-lucide-alert-circle class="w-4 h-4 text-red-500 flex-shrink-0" />
                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $alert }}</span>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-3">
                <button type="button" @click="dismiss()"
                    class="flex-1 px-6 py-3 rounded-2xl bg-brand-600 text-white font-bold hover:bg-brand-700 transition shadow-sm">
                    Got it
                </button>
            </div>
        </div>
    </div>
@endif
