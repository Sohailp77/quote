<x-guest-layout>
    <div class="space-y-8">
        <div class="text-center">
            <h1 class="text-3xl font-black tracking-tight mb-2">Reset Password</h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                Forgot your password? No problem. Just let us know your email address and we'll send you a reset link.
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 block ml-1">Email Address</label>
                <div class="relative">
                    <x-lucide-mail class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" />
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-none rounded-2xl pl-12 pr-5 py-4 font-bold text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-brand-500/20 text-sm transition-all"
                        placeholder="name@company.com">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl font-bold text-sm shadow-xl shadow-brand-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>

            <div class="text-center pt-4">
                <a href="{{ route('login') }}" class="text-sm font-black text-slate-400 hover:text-brand-600 transition-colors flex items-center justify-center gap-2 group">
                    <x-lucide-arrow-left class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
                    Back to Log in
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
