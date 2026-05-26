<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthOtpController extends Controller
{
    // ==========================================================================
    // 1. ALUR OTP VERIFIKASI (REGISTRASI / LOGIN)
    // ==========================================================================

    // Menampilkan halaman input OTP Verifikasi
    public function showVerifyForm()
    {
        return view('auth.verify-otp');
    }

    // Mengirim atau mengirim ulang (Resend) OTP Verifikasi ke email
    public function sendVerificationOtp(Request $request)
    {
        // Ambil user yang sedang aktif atau email dari session registrasi
        $user = Auth::user() ?? User::where('email', $request->session()->get('register_email'))->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi Anda telah berakhir, silakan login kembali.']);
        }

        // Mengacak 6 digit angka
        $otp = rand(100000, 999999);

        // Update database user dan set kedaluwarsa 15 menit ke depan
        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15)
        ]);

        // Tembak API/SMTP Brevo untuk kirim email nyata
        Mail::to($user->email)->send(new SendOtpMail($otp));

        return redirect()->route('otp.verify.form')->with('status', 'Kode OTP baru telah dikirim ke email Anda.');
    }

    // Memproses pencocokan kode OTP dari inputan user
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = Auth::user() ?? User::where('email', $request->session()->get('register_email'))->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Cek apakah kodenya cocok
        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        // Cek apakah kodenya sudah kedaluwarsa
        if (Carbon::now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.']);
        }

        // Sukses verifikasi, bersihkan kembali kolom OTP di DB demi keamanan
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'email_verified_at' => Carbon::now()
        ]);

        // Jika dia datang dari alur registrasi biasa, otomatis login-kan
        if (!Auth::check()) {
            Auth::login($user);
        }

        // Lempar ke dashboard sesuai middleware internal VESTA
        if (in_array($user->role, ['owner', 'manager', 'staff'])) {
            return redirect()->intended('/admin/dashboard');
        }

        return redirect()->route('profile');
    }

    // ==========================================================================
    // 2. ALUR OTP RESET PASSWORD (LUPA PASSWORD)
    // ==========================================================================

    // Menampilkan halaman form memasukkan email lupa password
=======
use Illuminate\Http\Request;

class AuthOtpController extends Controller
{
>>>>>>> 6107b4d483095e8bb7002a1725d7ee9f0bc9b499
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

<<<<<<< HEAD
    // Mengirim OTP Reset Password setelah validasi email
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Email tidak terdaftar di sistem VESTA.'
        ]);

        $user = User::where('email', $request->email)->first();
        $otp = rand(100000, 999999);

        $user->update([
            'otp_code' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(15)
        ]);

        // Tembak Brevo
        Mail::to($user->email)->send(new SendOtpMail($otp));

        // Amankan email di session untuk divalidasi di step ganti password baru
        $request->session()->put('reset_email', $user->email);

        return redirect()->route('password.reset.form')->with('status', 'Kode OTP reset password telah dikirim ke email Anda.');
    }

    // Menampilkan halaman input OTP & Password baru
=======
    public function sendResetOtp(Request $request)
    {
        return redirect()->back()->with('success', 'OTP sent successfully.');
    }

>>>>>>> 6107b4d483095e8bb7002a1725d7ee9f0bc9b499
    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }

<<<<<<< HEAD
    // Mengecek validitas OTP akhir dan mengganti password lama ke baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        $email = $request->session()->get('reset_email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Sesi habis. Silakan masukkan email kembali.']);
        }

        if ($user->otp_code !== $request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP salah.']);
        }

        if (Carbon::now()->isAfter($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP kedaluwarsa.']);
        }

        // Update password baru (Otomatis ter-hash berkat casts 'hashed' di Model User kamu)
        $user->update([
            'password' => $request->password,
            'otp_code' => null,
            'otp_expires_at' => null
        ]);

        $request->session()->forget('reset_email');

        return redirect()->route('login')->with('status', 'Password Anda berhasil diperbarui. Silakan login.');
    }
}
=======
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
>>>>>>> 6107b4d483095e8bb7002a1725d7ee9f0bc9b499
