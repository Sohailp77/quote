<x-guest-layout>
    <div class="space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-black tracking-tight mb-2">Welcome Back</h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Log in to manage your quotes and catalog.</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block ml-1">Email Address</label>
                <div class="relative">
                    <x-lucide-mail class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="name@company.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="space-y-2">
                <div class="flex items-center justify-between ml-1">
                    <label for="password" class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-[10px] font-black uppercase tracking-widest text-brand-600 hover:text-brand-700 transition-colors">
                            Forgot?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <x-lucide-lock class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="••••••••">
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                    <div class="relative flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" 
                            class="rounded-lg h-5 w-5 border-slate-200 dark:border-slate-800 dark:bg-slate-800 text-brand-600 focus:ring-brand-500/20 transition-colors">
                    </div>
                    <span class="ms-3 text-sm font-bold text-slate-500 dark:text-slate-400 group-hover:text-slate-700 dark:group-hover:text-slate-300 transition-colors">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl font-bold text-sm shadow-xl shadow-brand-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    {{ __('Log in') }}
                </button>
            </div>

            @if (Route::has('register'))
                <div class="text-center pt-4">
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="text-brand-600 font-black hover:underline underline-offset-4">Sign up</a>
                    </p>
                </div>
            @endif
        </form>
    </div>
</x-guest-layout>
