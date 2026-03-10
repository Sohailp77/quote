<x-app-layout>
    <div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 shadow rounded-xl p-8 border border-slate-200 dark:border-slate-800">
                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">Create SMTP Account</h2>
                
                <form action="{{ route('admin.smtp.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Account Display Name</label>
                            <input type="text" name="name" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required placeholder="e.g. Primary Gmail">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Host</label>
                            <input type="text" name="host" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required placeholder="smtp.gmail.com">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Port</label>
                            <input type="number" name="port" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required placeholder="465">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Username</label>
                            <input type="text" name="username" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
                            <input type="password" name="password" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Encryption</label>
                            <select name="encryption" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                                <option value="tls">TLS (Port 587)</option>
                                <option value="ssl">SSL (Port 465)</option>
                                <option value="none">None</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Priority (Low = First)</label>
                            <input type="number" name="priority" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required value="0">
                        </div>

                        <div class="col-span-2 grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">From Name</label>
                                <input type="text" name="from_name" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required placeholder="CatalogApp Notifications">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">From Address</label>
                                <input type="email" name="from_address" class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white" required placeholder="noreply@domain.com">
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_active" checked class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">Account is Active</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t dark:border-slate-800">
                        <a href="{{ route('admin.smtp.index') }}" class="px-4 py-2 text-slate-600 dark:text-slate-400 font-bold">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
