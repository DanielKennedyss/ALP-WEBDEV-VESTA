<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendOtpMail; // REVISI: Dipastikan mengunci ke Mailable milikmu
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class AuthOtpController extends Controller
{
    /**
     * Menampilkan Form Lupa Password
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Memproses Pengiriman OTP Lupa Password
     */
    public function sendResetOtp(Request $request)
    {
        // Validasi: Email wajib diisi dan harus ada di tabel users
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'This email address is not registered in our system.'
        ]);

        // 1. Cari data user berdasarkan input email
        $user = User::where('email', $request->email)->first();

        // 2. Buat 6 digit angka acak OTP
        $otp = rand(100000, 999999);

        // 3. Simpan OTP dan batas kadaluarsa ke kolom user di database
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(15) // Masa aktif 15 menit
        ]);

        // 4. TEMBAK EMAIL NYATA menggunakan Mailable SendOtpMail milikmu
        // Mengirimkan variabel $otp ke constructor SendOtpMail($otp)
        Mail::to($user->email)->send(new SendOtpMail($otp));

        // 5. Simpan email di session agar form verifikasi berikutnya tahu akun mana yang di-reset
        session(['reset_email' => $user->email]);

        return redirect()->route('password.reset.form')->with('success', 'OTP sent successfully.');
    }

    /**
     * Menampilkan Form Penginputan Kode OTP & Password Baru
     */
    public function showResetPasswordForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request')->with('error', 'Please request an OTP token first.');
        }
        return view('auth.reset-password');
    }

    /**
     * Memproses Eksekusi Perubahan Password Baru
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email = session('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->with('error', 'Session expired. Please try again.');
        }

        // Validasi 1: Kecocokan kode OTP
        if ($user->otp_code != $request->otp_code) {
            return redirect()->back()->withErrors(['otp_code' => 'The OTP code you entered is invalid.']);
        }

        // Validasi 2: Cek apakah kode OTP sudah expired
        if (now()->isAfter($user->otp_expires_at)) {
            return redirect()->back()->withErrors(['otp_code' => 'The OTP code has expired. Please request a new one.']);
        }

        // Update password baru dan bersihkan sisa token OTP di database
        $user->update([
            'password' => Hash::make($request->password),
            'otp_code' => null,
            'otp_expires_at' => null
        ]);

        // Bersihkan session penampung email
        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. Please log in.');
    }
}