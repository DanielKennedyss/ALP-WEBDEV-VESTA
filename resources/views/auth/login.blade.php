@extends('base.base')

@section('content')
<!-- Link Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .vh-100-custom { min-height: 100vh; }
    .bg-editorial { background-color: #f8f9fa; }
    
    .form-minimal input {
        border: none;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0;
        padding-left: 0;
        font-size: 0.9rem;
        background-color: transparent;
    }
    
    .form-minimal input:focus {
        box-shadow: none;
        border-bottom: 1px solid #000;
        background-color: transparent;
    }
    
    .label-caps {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        color: #adb5bd;
    }
    
    .btn-vesta {
        background: #000;
        color: #fff;
        border-radius: 0;
        padding: 15px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    .btn-vesta:hover {
        background: #333;
        color: #fff;
    }

    /* Menghilangkan navigasi standar jika ada di base.base */
    nav, footer { display: none !important; }
</style>

<div class="container-fluid p-0 overflow-hidden">
    <div class="row g-0 vh-100-custom">
        
        <!-- SISI KIRI: Editorial Space (Sama dengan Register) -->
        <div class="col-lg-6 bg-editorial d-none d-lg-flex align-items-end p-5">
            <div>
                <h1 class="display-3 fw-light tracking-tighter mb-4">VESTA</h1>
                <p class="text-muted text-uppercase small tracking-widest">
                    Welcome back. Elevate your curation once again.
                </p>
            </div>
        </div>

        <!-- SISI KANAN: Form Login -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white p-5">
            <div class="w-100" style="max-width: 380px;">
                <div class="mb-5">
                    <h2 class="fw-normal mb-1">Welcome back.</h2>
                    <p class="text-muted small text-uppercase tracking-wider">Please enter your details to sign in.</p>
                </div>

                <form action="{{ route('login') }}" method="POST" class="form-minimal">
                    @csrf
                    
                    <!-- Email Address -->
                    <div class="mb-4">
                        <label class="label-caps">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="enter your email" required autofocus>
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <label class="label-caps">Password</label>
                            <a href="#" class="label-caps text-decoration-none text-dark" style="opacity: 0.6;">Forgot?</a>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="enter your password" required>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-5 d-flex align-items-center">
                        <input type="checkbox" name="remember" id="remember" class="me-2">
                        <label for="remember" class="label-caps mb-0" style="cursor: pointer;">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-vesta w-100 border-0">
                        Sign In &rarr;
                    </button>
                </form>

                <p class="mt-5 text-center label-caps">
                    Don't have an account? <a href="{{ route('register') }}" class="text-dark text-decoration-none fw-bold">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection