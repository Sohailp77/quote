<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Tenant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.tenants.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tenant Info -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">Business Information</h3>
                                
                                <div>
                                    <x-input-label for="company_name" :value="__('Company Name')" />
                                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                                </div>

                                <div>
                                    <x-input-label for="plan_id" :value="__('Select Plan')" />
                                    <select id="plan_id" name="plan_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }} - {{ $plan->currency === 'INR' ? '₹' : '$' }}{{ number_format($plan->price, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('plan_id')" />
                                </div>
                            </div>

                            <!-- Owner Info -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium">Initial Owner Details</h3>

                                <div>
                                    <x-input-label for="owner_name" :value="__('Owner Name')" />
                                    <x-text-input id="owner_name" name="owner_name" type="text" class="mt-1 block w-full" :value="old('owner_name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('owner_name')" />
                                </div>

                                <div>
                                    <x-input-label for="owner_email" :value="__('Owner Email')" />
                                    <x-text-input id="owner_email" name="owner_email" type="email" class="mt-1 block w-full" :value="old('owner_email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('owner_email')" />
                                </div>

                                <div>
                                    <x-input-label for="owner_password" :value="__('Default Password')" />
                                    <x-text-input id="owner_password" name="owner_password" type="password" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('owner_password')" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <x-primary-button>{{ __('Create Tenant') }}</x-primary-button>
                            <a href="{{ route('admin.tenants.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline decoration-gray-300 dark:decoration-gray-700 underline-offset-4">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
