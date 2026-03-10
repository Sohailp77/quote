<x-app-layout>
    <div
        class="mb-6 flex justify-between items-center bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition">SuperAdmin</a>
                <x-lucide-chevron-right class="w-4 h-4" />
                <span>Plans</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Manage Plans</h1>
        </div>
        <div>
            <a href="{{ route('admin.plans.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition shadow-sm">
                <x-lucide-plus class="w-4 h-4" /> Create Plan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div
                class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border <?= $plan->is_active ? 'border-brand-200 dark:border-brand-900/50' : 'border-slate-100 dark:border-slate-800 opacity-75' ?> p-6 relative flex flex-col">
                @if(!$plan->is_active)
                    <div
                        class="absolute top-4 right-4 px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 text-xs font-semibold rounded-full">
                        Inactive</div>
                @endif
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $plan->name }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 flex-1">
                    {{ $plan->description ?? 'No description provided.' }}</p>

                <div class="my-6">
                    <span
                        class="text-3xl font-extrabold text-slate-900 dark:text-white">${{ number_format($plan->price, 2) }}</span>
                    <span class="text-sm text-slate-500 dark:text-slate-400">/mo</span>
                </div>

                <ul class="space-y-3 mb-8 text-sm text-slate-600 dark:text-slate-400">
                    <li class="flex items-center gap-2"><x-lucide-check class="w-4 h-4 text-brand-500" />
                        {{ $plan->max_users ? $plan->max_users . ' Users limit' : 'Unlimited Users' }}</li>
                    <li class="flex items-center gap-2"><x-lucide-check class="w-4 h-4 text-brand-500" />
                        {{ $plan->max_products ? $plan->max_products . ' Products' : 'Unlimited Products' }}</li>
                    <li class="flex items-center gap-2"><x-lucide-check class="w-4 h-4 text-brand-500" />
                        {{ $plan->max_quotes ? $plan->max_quotes . ' Quotes/mo' : 'Unlimited Quotes' }}</li>
                </ul>

                <div class="mt-auto grid grid-cols-2 gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.plans.edit', $plan) }}"
                        class="flex justify-center flex-1 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition">Edit</a>
                    <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="flex-1"
                        onsubmit="return confirm('Delete this plan? Tenants using this plan might be affected.');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-full px-4 py-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-lg text-sm font-medium hover:bg-red-100 dark:hover:bg-red-500/20 transition">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>