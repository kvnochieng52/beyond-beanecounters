<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function authenticated(Request $request, $user)
    {
        if ($user->two_factor_code) {
            $user->generateTwoFactorCode();
            $user->sendTwoFactorCode();

            return redirect()->route('2fa.verify');
        }

        return redirect()->intended('/');
    }


    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]);

        Auth::logout();
        session()->forget('2fa_verified'); // Clear 2FA session

        return redirect('/');
    }
}
