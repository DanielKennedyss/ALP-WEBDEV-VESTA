<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function show() {
        return view('auth.register');
    }

public function store(Request $request)
{
    // 1. Validasi Data
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'phone_number' => ['required', 'string', 'max:20'], // Tambahkan validasi HP
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    // 2. Simpan ke Database
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone_number' => $request->phone_number,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'role' => 'customer', // Default role
        'membership_level' => 'bronze', // Default level untuk user baru VESTA
    ]);

    // 3. Langsung Login setelah daftar
    \Illuminate\Support\Facades\Auth::login($user);

    // 4. Redirect ke Dashboard
    return redirect()->route('dashboard');
}
}