<x-app-layout>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.tenants.index') }}"
                class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition">
                <x-lucide-arrow-left class="w-4 h-4" /> Back to Tenants
            </a>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Edit Tenant</h2>
                    <span class="text-xs font-mono text-slate-400">ID: {{ $tenant->id }}</span>
                </div>
            </div>

            <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full md:col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Company
                            Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                    </div>

                    <div class="col-span-full md:col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Subscription
                            Plan</label>
                        <select name="plan_id"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-white focus:ring-brand-500 focus:border-brand-500">
                            <option value="">No Plan (Free/Manual)</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id) == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} ({{ $plan->currency === 'INR' ? '₹' : '$' }}{{ number_format($plan->price, 2) }}/mo)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-1 flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer pb-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Tenant is Active</span>
                        </label>
                    </div>

                    <div class="col-span-full mt-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <p class="text-xs text-slate-500 mb-4"><x-lucide-info
                                class="inline-block w-4 h-4 mr-1 text-blue-500" /> Tenant status toggles login access
                            for all their users.</p>
                    </div>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <button type="submit"
                        class="px-5 py-2.5 bg-brand-600 text-white font-medium rounded-lg hover:bg-brand-700 transition shadow-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>