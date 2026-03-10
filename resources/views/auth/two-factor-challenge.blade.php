<x-guest-layout>
    <div class="mb-4 text-sm text-slate-600 dark:text-slate-400">
        {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
    </div>

    <div x-data="{ recovery: false }">
        <div class="mb-4 text-sm text-slate-600 dark:text-slate-400" x-show="! recovery">
            {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
        </div>

        <div class="mb-4 text-sm text-slate-600 dark:text-slate-400" x-cloak x-show="recovery">
            {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf

            <div class="mt-4" x-show="! recovery">
                <x-input-label for="code" :value="__('Code')" />
                <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" autofocus x-ref="code" autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div class="mt-4" x-cloak x-show="recovery">
                <x-input-label for="recovery_code" :value="__('Recovery Code')" />
                <x-text-input id="recovery_code" class="block mt-1 w-full" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" />
                <x-input-error :messages="$errors->get('recovery_code')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="button" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 underline cursor-pointer"
                                x-show="! recovery"
                                x-on:click="
                                    recovery = true;
                                    $nextTick(() => { $refs.recovery_code.focus() })
                                ">
                    {{ __('Use a recovery code') }}
                </button>

                <button type="button" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 underline cursor-pointer"
                                x-cloak
                                x-show="recovery"
                                x-on:click="
                                    recovery = false;
                                    $nextTick(() => { $refs.code.focus() })
                                ">
                    {{ __('Use an authentication code') }}
                </button>

                <x-primary-button class="ms-4">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
