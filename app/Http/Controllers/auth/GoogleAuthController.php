<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
