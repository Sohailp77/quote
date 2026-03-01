<x-app-layout>
    <div class="bg-slate-50/50 dark:bg-slate-800/50 min-h-[calc(100vh-8rem)] rounded-[40px] p-6 lg:p-8 font-sans">
        <div class="max-w-lg mx-auto">
            <a href="{{ route('employees.index') }}"
                class="inline-flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 mb-6 transition">
                <x-lucide-arrow-left class="w-4 h-4" /></i> Back to Team
            </a>

            <div class="bg-white dark:bg-slate-900 rounded-[28px] p-8 shadow-[0_2px_16px_-2px_rgba(0,0,0,0.06)]"
                x-data="{ showPwd: false }">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-slate-900 dark:bg-white rounded-2xl flex items-center justify-center">
                        <x-lucide-user-plus class="w-5 h-5 text-white dark:text-slate-900" /></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 dark:text-white">Add Employee</h1>
                        <p class="text-xs text-slate-400 dark:text-slate-500">They will log in with these credentials
                        </p>
                    </div>
                </div>

                <form action="{{ route('employees.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Full
                            Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Ravi Kumar"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400 dark:focus:border-slate-500 min-h-[42px]"
                            required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Email
                            Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="ravi@yourcompany.com"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400 dark:focus:border-slate-500 min-h-[42px]"
                            required />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password -->
                    <div>
                        <label
                            class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Password</label>
                        <div class="relative">
                            <input :type="showPwd ? 'text' : 'password'" name="password" placeholder="Min. 8 characters"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400 dark:focus:border-slate-500 min-h-[42px]"
                                required />
                            <button type="button" @click="showPwd = !showPwd"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                                <template x-if="showPwd">
                                    <x-lucide-eye-off class="w-4 h-4" /></i>
                                </template>
                                <template x-if="!showPwd">
                                    <x-lucide-eye class="w-4 h-4" /></i>
                                </template>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Confirm
                            Password</label>
                        <input type="password" name="password_confirmation" placeholder="Repeat password"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900/20 focus:border-slate-400 dark:focus:border-slate-500 min-h-[42px]"
                            required />
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 py-3 rounded-2xl text-sm font-bold hover:bg-slate-700 dark:hover:bg-slate-200 transition focus:outline-none focus:ring-2 focus:ring-slate-900/20 dark:focus:ring-white/20 min-h-[42px]">
                            Create Employee Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>