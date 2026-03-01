<x-app-layout>
    <div class="max-w-4xl mx-auto py-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <a href="{{ route('customers.index') }}"
                    class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300 flex items-center gap-1 mb-2">
                    <x-lucide-arrow-left class="w-4 h-4" /> Back to Customers
                </a>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                    {{ isset($customer) ? 'Edit Customer' : 'Add New Customer' }}
                </h1>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm p-6 lg:p-8">
            <form action="{{ isset($customer) ? route('customers.update', $customer->id) : route('customers.store') }}"
                method="POST" class="space-y-6">
                @csrf
                @if (isset($customer))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Full Name
                            *</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}" required
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition-all dark:text-white"
                            placeholder="John Doe">
                        @error('name')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Company
                            Name</label>
                        <input type="text" name="company" value="{{ old('company', $customer->company ?? '') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition-all dark:text-white"
                            placeholder="Acme Corp">
                        @error('company')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email
                            Address</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email ?? '') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition-all dark:text-white"
                            placeholder="john@example.com">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Phone
                            Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition-all dark:text-white"
                            placeholder="+1 (555) 000-0000">
                        @error('phone')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Internal
                        Notes</label>
                    <textarea name="notes" rows="4"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-500 outline-none transition-all resize-none dark:text-white"
                        placeholder="Any special requirements or history...">{{ old('notes', $customer->notes ?? '') }}</textarea>
                    @error('notes')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('customers.index') }}"
                        class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-bold bg-brand-600 dark:bg-brand-500 text-white hover:bg-brand-700 dark:hover:bg-brand-400 rounded-xl shadow-sm transition-colors">
                        {{ isset($customer) ? 'Save Changes' : 'Create Customer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
