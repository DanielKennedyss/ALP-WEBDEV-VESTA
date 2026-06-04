<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Google login config error: ' . $e->getMessage());
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find or create user by Google email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // User exists, log them in
                Auth::login($user);
            } else {
                // User does not exist, register them
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'role' => 'customer',
                    'membership_level' => 'bronze',
                    'loyalty_points' => 0,
                    'total_spending' => 0.00,
                    'status' => 'BRONZE',
                ]);
                Auth::login($user);
            }

            return redirect()->route('profile')->with('success', 'Logged in with Google successfully.');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Google login callback error: ' . $e->getMessage());
        }
    }
}
