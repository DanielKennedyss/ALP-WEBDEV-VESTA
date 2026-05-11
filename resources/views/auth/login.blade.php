@extends('base.base')

@section('content')
<style>
    html, body { overflow: hidden; height: 100vh; width: 100vw; margin: 0; padding: 0; }
    body { padding-top: 0 !important; }

    /* Hapus styling dari header.blade.php */
    nav, footer { display: none !important; }

    .login-wrapper {
        display: flex;
        height: 100vh;
        width: 100vw;
    }

    .editorial-panel {
        flex: 0 0 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: flex-end;
        padding: 3rem;
    }

    .editorial-panel h1 {
        font-size: 5rem;
        font-weight: 300;
        letter-spacing: -0.03em;
        margin-bottom: 1rem;
    }

    .editorial-panel p {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.25em;
        color: #6c757d;
    }

    .form-panel {
        flex: 0 0 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
    }

    .form-minimal input {
        border: none;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0;
        padding-left: 0;
        font-size: 0.9rem;
        background-color: transparent;
        box-shadow: none !important;
    }

    .form-minimal input:focus {
        border-bottom: 1px solid #000;
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
        padding: 1rem;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        border: none;
        width: 100%;
        transition: all 0.3s ease;
    }

    .btn-vesta:hover {
        background: #333;
        color: #fff;
    }

    .form-control::placeholder {
        color: #adb5bd;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.1em;
    }

    @media (max-width: 991px) {
        .editorial-panel { display: none; }
        .form-panel { flex: 0 0 100%; }
        html, body { overflow: auto; }
    }
</style>

<div class="login-wrapper">
    <!-- SISI KIRI: Editorial Space -->
    <div class="editorial-panel">
        <div>
            <h1>VESTA</h1>
            <p>Welcome back. Elevate your curation once again.</p>
        </div>
    </div>

    <!-- SISI KANAN: Form Login -->
    <div class="form-panel">
        <div style="width: 100%; max-width: 380px;">
            <div class="mb-5">
                <h2 style="font-weight: 300; margin-bottom: 0.5rem; font-size: 1.75rem;">Welcome back.</h2>
                <p class="label-caps">Please enter your details to sign in.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="form-minimal">
                @csrf

                <div class="mb-4">
                    <div>
                        <label class="label-caps d-block mb-2">Email Address</label>
                    </div>
                    
                    <input type="email" name="email" class="form-control" placeholder="enter your email" required autofocus>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="label-caps mb-0">Password</label>
                        <a href="#" class="label-caps text-decoration-none text-dark" style="opacity: 0.6;">Forgot?</a>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="enter your password" required>
                </div>

                <div class="mb-5 d-flex align-items-center">
                    <input type="checkbox" name="remember" id="remember" class="me-2">
                    <label for="remember" class="label-caps mb-0" style="cursor: pointer;">Remember me</label>
                </div>

                <button type="submit" class="btn btn-vesta">
                    Sign In &rarr;
                </button>
            </form>

            <p class="mt-5 text-center">
                <span class="label-caps">Don't have an account?</span>
                <a href="{{ route('register') }}" class="text-dark text-decoration-none fw-bold ms-1">Sign up</a>
            </p>
        </div>
    </div>
</div>
@endsection