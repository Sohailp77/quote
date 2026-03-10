<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TwoFactorChallengeController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show the two factor authentication challenge view.
     */
    public function create(Request $request)
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => ['nullable', 'string'],
            'recovery_code' => ['nullable', 'string'],
        ]);

        $userId = $request->session()->get('login.id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::findOrFail($userId);

        if ($code = $request->code) {
            if ($this->twoFactorService->verifyCode($user->two_factor_secret, $code)) {
                $this->login($request, $user);
                $request->session()->put('auth.two_factor_confirmed', true);
                return redirect()->intended(route('dashboard', absolute: false));
            }
        } elseif ($recoveryCode = $request->recovery_code) {
            $recoveryCodes = $user->two_factor_recovery_codes;
            
            if (($key = array_search($recoveryCode, $recoveryCodes)) !== false) {
                unset($recoveryCodes[$key]);
                $user->forceFill(['two_factor_recovery_codes' => array_values($recoveryCodes)])->save();
                
                $this->login($request, $user);
                $request->session()->put('auth.two_factor_confirmed', true);
                return redirect()->intended(route('dashboard', absolute: false));
            }
        }

        throw ValidationException::withMessages([
            'code' => [__('The provided two factor authentication code was invalid.')],
        ]);
    }

    /**
     * Log the user in.
     */
    protected function login(Request $request, $user)
    {
        \Illuminate\Support\Facades\Auth::login($user, $request->session()->get('login.remember', false));
        $request->session()->forget(['login.id', 'login.remember']);
    }
}
