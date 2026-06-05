<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthOtpController extends Controller
{
    /**
     * Step 1: Show the "Forgot Password" form (email input).
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Step 2: Validate email, generate OTP, store it, and send it via email.
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We could not find an account with that email address.'])->withInput();
        }

        // Generate a 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP and expiry (10 minutes) on the user record
        $user->update([
            'otp_code'       => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send the OTP via email
        Mail::to($user->email)->send(new ResetPasswordOtpMail($otpCode, $user->name));

        // Store email in session to carry it to the next step
        session(['reset_email' => $user->email]);

        return redirect()->route('password.reset.form')->with('success', 'A 6-digit OTP has been sent to your email.');
    }

    /**
     * Step 3: Show the "Reset Password" form (OTP + new password).
     */
    public function showResetPasswordForm()
    {
        // If no email in session, redirect back to forgot-password
        if (!session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Please enter your email first.']);
        }

        return view('auth.reset-password');
    }

    /**
     * Step 3b: Verify OTP via AJAX (returns JSON).
     */
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('reset_email');

        if (!$email) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please start again.'], 422);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Account not found.'], 422);
        }

        // Check OTP validity
        if ($user->otp_code !== $request->otp) {
            return response()->json(['success' => false, 'message' => 'The OTP code you entered is incorrect.'], 422);
        }

        // Check OTP expiration
        if (!$user->otp_expires_at || Carbon::now()->isAfter($user->otp_expires_at)) {
            $user->update(['otp_code' => null, 'otp_expires_at' => null]);
            return response()->json(['success' => false, 'message' => 'This OTP has expired. Please request a new one.'], 422);
        }

        // Mark OTP as verified in session so resetPassword doesn't re-check
        session(['otp_verified' => true]);

        return response()->json(['success' => true, 'message' => 'OTP verified successfully.']);
    }

    /**
     * Step 4: Reset the password (OTP already verified via Step 3b).
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('reset_email');

        if (!$email || !session('otp_verified')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Account not found.']);
        }

        // Reset the password
        $user->update([
            'password'       => Hash::make($request->password),
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);

        // Clear session
        session()->forget(['reset_email', 'otp_verified']);

        return redirect()->route('login')->with('status', 'Your password has been reset successfully. Please sign in.');
    }

    /**
     * Resend OTP (from reset-password page).
     */
    public function resendResetOtp(Request $request)
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Account not found.']);
        }

        // Generate a new 6-digit OTP
        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'       => $otpCode,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new ResetPasswordOtpMail($otpCode, $user->name));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    // =====================================================================
    // Account Verification OTP (existing stubs — kept for route compatibility)
    // =====================================================================

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