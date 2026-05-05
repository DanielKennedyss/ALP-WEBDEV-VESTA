<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (auth()->check()) {
            $role = auth()->user()->role;

            // Owner dan Admin diizinkan masuk ke area /admin
            if ($role === 'owner' || $role === 'admin') {
                return $next($request);
            }
        }


return back()->with('error', 'Akses ditolak.');
    }
}