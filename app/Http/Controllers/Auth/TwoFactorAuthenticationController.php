<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TwoFactorAuthenticationController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Enable 2FA for the user.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        $user->forceFill([
            'two_factor_secret' => $this->twoFactorService->generateSecret(),
            'two_factor_recovery_codes' => $this->twoFactorService->generateRecoveryCodes(),
        ])->save();

        return back()->with('status', 'two-factor-authentication-enabled');
    }

    /**
     * Confirm 2FA for the user.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();

        if ($this->twoFactorService->verifyCode($user->two_factor_secret, $request->code)) {
            $user->forceFill([
                'two_factor_confirmed_at' => now(),
            ])->save();

            $request->session()->put('auth.two_factor_confirmed', true);

            return back()->with('status', 'two-factor-authentication-confirmed');
        }

        throw ValidationException::withMessages([
            'code' => [__('The provided two factor authentication code was invalid.')],
        ]);
    }

    /**
     * Disable 2FA for the user.
     */
    public function destroy(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $user = $request->user();

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return back()->with('status', 'two-factor-authentication-disabled');
    }

    /**
     * Show recovery codes.
     */
    public function recoveryCodes(Request $request)
    {
        return $request->user()->two_factor_recovery_codes;
    }
}
