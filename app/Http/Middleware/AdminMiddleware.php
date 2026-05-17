<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (Auth::check()) {
            $role = Auth::user()->role;

            // 2. Izinkan Owner, Manager, dan Staff masuk ke area /admin
            $authorizedRoles = ['owner', 'manager', 'staff'];

            if (in_array($role, $authorizedRoles)) {
                return $next($request);
            }
        }

        // 3. Jika bukan personil internal (misal: Customer), arahkan ke home dengan pesan error
        return redirect('/')->with('error', 'Akses ditolak. Anda tidak memiliki izin administratif.');
    }
}