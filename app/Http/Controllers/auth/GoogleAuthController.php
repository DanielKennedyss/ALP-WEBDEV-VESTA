<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
<<<<<<< HEAD
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // 1. Validasi pastikan email dari Google didapatkan
            if (!$googleUser->getEmail()) {
                return redirect()->route('login')->with('error', 'Gagal mendapatkan data email dari akun Google Anda.');
            }

            // 2. Cari user berdasarkan email di database
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Jika user sudah ada, update google_id nya
                $user->update([
                    'google_id' => $googleUser->getId()
                ]);
            } else {
                // Jika user belum ada, buat data baru dan tampung ke dalam variabel $user
                $user = User::create([
                    'name'              => $googleUser->getName() ?? 'Google User',
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'role'              => 'customer',
                    'status'            => 'active',
                    'membership_level'  => 'bronze',
                    'loyalty_points'    => 0,
                    'total_spending'    => 0.00,
                    'password'          => uniqid(), // Model User otomatis meng-hash lewat $casts
                ]);
            }

            // 3. JAMINAN UTAMA: Cek apakah variabel $user benar-benar ada dan tidak null
            if (!$user) {
                return redirect()->route('login')->with('error', 'Gagal membuat atau menemukan akun pengguna di database.');
            }

            // 4. Daftarkan session login menggunakan objek $user yang sudah valid
            Auth::login($user, true);

            // 5. Regenerate session demi keamanan HTTPS ngrok
            request()->session()->regenerate();

            // 6. Alihkan ke halaman profile kustom VESTA kamu
            return redirect()->route('profile');

        } catch (Exception $e) {
            // Tangkap jika ada error database (misal kolom kurang atau belum dimigrasi)
            return redirect()->route('login')->with('error', 'Google Auth Error: ' . $e->getMessage());
        }
    }
}
=======
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return redirect()->back()->with('error', 'Google login not configured.');
    }

    public function handleGoogleCallback()
    {
        return redirect()->route('login')->with('error', 'Google login callback error.');
    }
}
>>>>>>> 6107b4d483095e8bb7002a1725d7ee9f0bc9b499
