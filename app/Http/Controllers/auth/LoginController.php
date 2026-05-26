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

            $user = Auth::user();
            
            // Sync/Merge cart database with session
            \App\Models\CartItem::syncCart($user);

            // Selaraskan dengan role internal VESTA: owner, manager, staff
            if (in_array($user->role, ['owner', 'manager', 'staff'])) {
                return redirect()->route('admin.dashboard');
                
            }

            // Jika customer biasa, bawa ke profile
            return redirect()->route('profile'); 
        }

        // Jika salah password/email, kembalikan dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.'
        ])->onlyInput('email');
    }
<<<<<<< HEAD

    public function logout(Request $request)
    {
=======
    public function logout(Request $request)
    {
        if (Auth::check()) {
            \App\Models\CartItem::saveSessionCartToDb(Auth::user());
        }

>>>>>>> 6107b4d483095e8bb7002a1725d7ee9f0bc9b499
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

<<<<<<< HEAD
=======
        // Ubah redirect ke route login
>>>>>>> 6107b4d483095e8bb7002a1725d7ee9f0bc9b499
        return redirect()->route('login');
    }
}