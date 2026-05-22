<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Pastikan user sudah login
        if (Auth::check()) {
            $user = Auth::user();
            
            // 2. Izinkan masuk jika rolenya adalah internal staff VESTA
            if (in_array($user->role, ['owner', 'manager', 'staff'])) {
                return $next($request);
            }
            
            // 3. Jika customer biasa yang nyasar ke rute admin, lempar ke profil
            return redirect()->route('profile');
        }

        // 4. Jika belum login sama sekali, lempar ke login
        return redirect()->route('login');
    }
}