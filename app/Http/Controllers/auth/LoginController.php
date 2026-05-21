<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show() {
        return view('auth.login');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // --- PERUBAHAN DI SINI ---
            $user = Auth::user();

            // Jika yang login adalah Owner atau Admin
            if ($user->role === 'owner' || $user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // Jika customer biasa
            return redirect()->intended('dashboard');
            // -------------------------
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    }
public function logout(Request $request)
{
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Ubah redirect ke route login
    return redirect()->route('login');
}
}