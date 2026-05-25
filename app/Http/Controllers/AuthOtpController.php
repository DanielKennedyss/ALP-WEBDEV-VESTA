<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthOtpController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        return redirect()->back()->with('success', 'OTP sent successfully.');
    }

    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        return redirect()->route('login')->with('success', 'Password reset successfully.');
    }

    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        return redirect()->route('home')->with('success', 'Account verified successfully.');
    }

    public function sendVerificationOtp(Request $request)
    {
        return redirect()->back()->with('success', 'Verification OTP sent successfully.');
    }
}
