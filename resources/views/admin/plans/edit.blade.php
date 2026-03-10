<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.plans.index') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                <x-lucide-arrow-left class="w-4 h-4" /> Back to Plans
            </a>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Edit Plan: {{ $plan->name }}</h2>
                    <span class="text-xs font-mono text-slate-400">ID: {{ $plan->id }}</span>
                </div>
            </div>

            <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full md:col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Plan
                            Name</label>
                        <input type="text" name="name" value="{{ $plan->name }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    </div>
                    <div class="col-span-full md:col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Price
                            (Monthly)</label>
                        <input type="number" step="0.01" name="price" value="{{ $plan->price }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    </div>

                    <div class="col-span-full">
                        <label
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Description</label>
                        <input type="text" name="description" value="{{ $plan->description }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Max Users (Null
                            = Unlimited)</label>
                        <input type="number" name="max_users" value="{{ $plan->max_users }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Max
                            Products</label>
                        <input type="number" name="max_products" value="{{ $plan->max_products }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    </div>

                    <div class="col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Max
                            Quotes</label>
                        <input type="number" name="max_quotes" value="{{ $plan->max_quotes }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    </div>

                    <div class="col-span-1 flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer pb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Plan is Active</span>
                        </label>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>