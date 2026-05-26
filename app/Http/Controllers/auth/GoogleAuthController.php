<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Mengalihkan jabat tangan otentikasi menuju server Google API
     */
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            Log::error('Google OAuth Redirect Failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Cannot connect to Google Open Authentication services.');
        }
    }

    /**
     * Menangkap respon data user yang sukses login dari Google
     */
public function handleGoogleCallback()
{
    try {
        // WAJIB PAKAI STATELESS agar tidak memicu deteksi manipulasi session di fwd.host
        $googleUser = Socialite::driver('google')->stateless()->user();
        
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'name'              => $googleUser->getName(),
                'email'             => $googleUser->getEmail(),
                'password'          => bcrypt(\Illuminate\Support\Str::random(24)),
                'role'              => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        // Kunci session login
        Auth::login($user, true);

        // Redirect ke profil
        return redirect()->route('profile')->with('success', 'Logged in via Google.');

    } catch (\Exception $e) {
        // Jika gagal, lempar balik ke login dengan membawa pesan error aslinya untuk dilacak
        return redirect()->route('login')->with('error', 'OAuth Error: ' . $e->getMessage());
    }
}
}