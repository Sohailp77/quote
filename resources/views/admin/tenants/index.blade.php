<x-app-layout>
    <div
        class="mb-6 flex justify-between items-center bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition">SuperAdmin</a>
                <x-lucide-chevron-right class="w-4 h-4" />
                <span>Tenants</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Manage Tenants</h1>
        </div>
        <a href="{{ route('admin.tenants.create') }}"
            class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition shadow-sm flex items-center gap-2">
            <x-lucide-plus class="w-4 h-4" />
            Create Tenant
        </a>
    </div>

    <div
        class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                <thead
                    class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 font-medium border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Tenant ID</th>
                        <th class="px-6 py-4">Company Name</th>
                        <th class="px-6 py-4">Plan & Status</th>
                        <th class="px-6 py-4">Users</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($tenants as $tenant)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs">{{ $tenant->id }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                {{ $tenant->company_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-medium {{ $tenant->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400' }}">
                                        {{ $tenant->is_active ? 'Active' : 'Suspended' }}
                                    </span>
                                    <span
                                        class="text-xs text-slate-500">{{ $tenant->plan ? $tenant->plan->name : 'No Plan' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $tenant->users_count }}</td>
                            <td class="px-6 py-4 text-right flex justify-end gap-2">
                                <a href="{{ route('admin.tenants.edit', $tenant) }}"
                                    class="p-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-brand-50 hover:text-brand-600 rounded-md transition">
                                    <x-lucide-edit class="w-4 h-4" />
                                </a>
                                <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST"
                                    onsubmit="return confirm('Delete this tenant and ALL associated data? This cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-1.5 bg-slate-100 dark:bg-slate-800 text-red-500 hover:bg-red-50 rounded-md transition">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>