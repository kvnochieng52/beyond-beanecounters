<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    public function showVerifyForm()
    {
        return view('auth.two_factor');
    }

    public function verifyTwoFactorCode(Request $request)
    {
        $request->validate(['code' => 'required|integer']);

        $user = Auth::user();

        if ($user->two_factor_code == $request->code && now()->lt($user->two_factor_expires_at)) {
            $user->update(['two_factor_code' => null, 'two_factor_expires_at' => null]); // Clear the code
            session(['2fa_verified' => true]);

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['code' => 'Invalid or expired code.']);
    }

    public function resend()
    {
        $user = Auth::user();
        $user->generateTwoFactorCode(); // Implement this method to regenerate a code
        //  Mail::to($user->email)->send(new TwoFactorCodeMail($user->two_factor_code));

        Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($user->two_factor_code));
        // Send email or SMS here
        return back()->with('message', 'A new verification code has been sent.');
    }
}
