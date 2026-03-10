<x-guest-layout>
    <div class="space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-black tracking-tight mb-2">Create Account</h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Join us to start creating professional quotes.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div class="space-y-2">
                <label for="name"
                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block ml-1">Full
                    Name</label>
                <div class="relative">
                    <x-lucide-user class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="John Doe">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Company Name -->
            <div class="space-y-2">
                <label for="company_name"
                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block ml-1">Company
                    Name</label>
                <div class="relative">
                    <x-lucide-building class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="company_name" type="text" name="company_name" :value="old('company_name')" required
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="Acme Inc.">
                </div>
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email"
                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block ml-1">Email
                    Address</label>
                <div class="relative">
                    <x-lucide-mail class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="name@company.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <label for="password"
                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block ml-1">Password</label>
                <div class="relative">
                    <x-lucide-lock class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label for="password_confirmation"
                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block ml-1">Confirm
                    Password</label>
                <div class="relative">
                    <x-lucide-check-circle-2 class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        autocomplete="new-password"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl font-bold text-sm shadow-xl shadow-brand-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    {{ __('Register') }}
                </button>
            </div>

            <div class="text-center pt-4">
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                    Already have an account?
                    <a href="{{ route('login') }}"
                        class="text-brand-600 font-black hover:underline underline-offset-4">Log in</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>