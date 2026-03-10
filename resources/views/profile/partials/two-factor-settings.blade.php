<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
            {{ __('Two Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Add additional security to your account using two factor authentication.') }}
        </p>
    </header>

    @php
        $twoFactorService = app(\App\Services\Auth\TwoFactorService::class);
        $user = auth()->user();
    @endphp

    <div class="mt-5">
        @if (! $user->hasTwoFactorEnabled())
            @if (is_null($user->two_factor_secret))
                {{-- Enable 2FA Button --}}
                <form method="POST" action="{{ route('two-factor.enable') }}">
                    @csrf
                    <div class="max-w-xl text-sm text-slate-600 dark:text-slate-400">
                        {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                    </div>
                    
                    <div class="mt-5">
                        <x-primary-button>
                            {{ __('Enable') }}
                        </x-primary-button>
                    </div>
                </form>
            @else
                {{-- Finish setup: Show QR Code and Confirm --}}
                <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                    <p class="font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('Finish enabling two factor authentication.') }}
                    </p>
                    <p class="mt-3">
                        {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application and provide the generated OTP code.') }}
                    </p>
                </div>

                <div class="mt-4 p-4 bg-white inline-block rounded-xl border border-slate-100">
                    {!! $twoFactorService->getQrCodeSvg($user, $user->two_factor_secret) !!}
                </div>

                <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                    <p class="font-semibold">
                        {{ __('Setup Key') }}: {{ $user->two_factor_secret }}
                    </p>
                </div>

                <div class="flex items-center gap-4 mt-6">
                    <form method="POST" action="{{ route('two-factor.confirm') }}">
                        @csrf
                        <div class="max-w-xl">
                            <x-input-label for="code" :value="__('Code')" />
                            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" autocomplete="one-time-code" required />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="mt-4 flex gap-4">
                            <x-primary-button>
                                {{ __('Confirm') }}
                            </x-primary-button>
                        </div>
                    </form>
                    
                    <form method="POST" action="{{ route('two-factor.disable') }}" class="self-end">
                        @csrf
                        @method('DELETE')
                        <x-secondary-button type="submit">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    </form>
                </div>
            @endif
        @else
            {{-- 2FA is Enabled --}}
            <div class="max-w-xl text-sm text-slate-600 dark:text-slate-400">
                <p class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-2">
                    <x-lucide-check-circle class="w-5 h-5" />
                    {{ __('Two factor authentication is enabled.') }}
                </p>
                <p class="mt-3">
                    {{ __('You have enabled two factor authentication. Your account is now more secure.') }}
                </p>
            </div>

            @if (!empty($user->two_factor_recovery_codes))
                <div class="mt-4 max-w-xl text-sm text-slate-600 dark:text-slate-400">
                    <p class="font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('Recovery Codes') }}
                    </p>
                    <p class="mt-2">
                        {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                    </p>

                    <div class="mt-4 grid gap-1 font-mono text-xs bg-slate-50 dark:bg-slate-800 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                        @foreach ($user->two_factor_recovery_codes as $code)
                            <div>{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-two-factor-disabling')">
                    {{ __('Disable 2FA') }}
                </x-danger-button>

                <x-modal name="confirm-two-factor-disabling" focusable>
                    <form method="POST" action="{{ route('two-factor.disable') }}" class="p-6">
                        @csrf
                        @method('DELETE')

                        <h2 class="text-lg font-medium text-slate-900 dark:text-slate-100">
                            {{ __('Are you sure you want to disable 2FA?') }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                            {{ __('Once two factor authentication is disabled, your account will be less secure.') }}
                        </p>

                        <div class="mt-6">
                            <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                            <x-text-input
                                id="password"
                                name="password"
                                type="password"
                                class="mt-1 block w-3/4"
                                placeholder="{{ __('Password') }}"
                                required
                            />

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end">
                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-danger-button class="ms-3">
                                {{ __('Disable 2FA') }}
                            </x-danger-button>
                        </div>
                    </form>
                </x-modal>
            </div>
        @endif
    </div>
</section>
