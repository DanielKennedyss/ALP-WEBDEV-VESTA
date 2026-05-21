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
        background-image: url('https://images.unsplash.com/photo-1445205170230-053b83016050?w=1200&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: flex-end;
        padding: 3rem;
    }

    .editorial-panel::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.2));
        z-index: 1;
    }

    .editorial-panel > div {
        position: relative;
        z-index: 2;
        color: white;
    }

    .editorial-panel h1 {
        font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
        font-size: 5rem;
        font-weight: 300;
        letter-spacing: 0.1em;
        margin-bottom: 1rem;
    }

    .editorial-panel p {
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.25em;
        color: #e0e0e0;
    }

    .form-panel {
        flex: 0 0 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem;
    }

    .form-minimal input[type="email"],
    .form-minimal input[type="password"],
    .form-minimal input[type="text"] {
        border: none;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0;
        padding: 0.5rem 0;
        font-size: 1rem;
        font-weight: 500;
        color: #333;
        background-color: transparent;
        box-shadow: none !important;
        width: 100%;
        display: block;
    }

    .form-minimal input[type="checkbox"] {
        width: 14px !important;
        height: 14px !important;
        display: inline-block !important;
        appearance: auto !important;
        -webkit-appearance: checkbox !important;
        border: 1px solid #adb5bd !important;
        cursor: pointer;
        vertical-align: middle;
    }

    .form-minimal input[type="email"]:focus,
    .form-minimal input[type="password"]:focus,
    .form-minimal input[type="text"]:focus {
        border-bottom: 2px solid #000;
        outline: none;
    }

    .label-caps {
        font-size: 14px;
        font-weight: 400;
        color: #333;
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

    .form-minimal input::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
        font-weight: 400;
        letter-spacing: 0.02em;
        text-transform: none;
    }

    .password-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .forgot-link {
        font-size: 14px;
        font-weight: 400;
        color: #0d6efd;
        text-decoration: none;
    }

    .forgot-link:hover {
        color: #333;
    }

    .back-btn {
        position: absolute;
        top: 2rem;
        left: 2rem;
        z-index: 100;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #fff;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        transition: opacity 0.3s ease;
        text-shadow: 0 1px 3px rgba(0,0,0,0.5);
    }
    .back-btn:hover {
        opacity: 0.8;
    }

    /* Hide default browser password reveal icon */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    @media (max-width: 991px) {
        .editorial-panel { display: none; }
        .form-panel { flex: 0 0 100%; }
        html, body { overflow: auto; }
        .back-btn {
            color: #333;
            text-shadow: none;
        }
    }
</style>

<div class="login-wrapper">
    <a href="{{ route('home') }}" class="back-btn">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        Back to Home
    </a>
    <!-- SISI KIRI: Editorial Space -->
    <div class="editorial-panel">
        <div>
            <h1>VESTA</h1>
            <p>Welcome back. Elevate your curation once again.</p>
        </div>
    </div>

    <!-- SISI KANAN: Form Login -->
    <div class="form-panel">
        <div style="width: 100%; max-width: 420px;">
            <div class="mb-5">
                <h2 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.75rem;">Welcome back.</h2>
                <p class="label-caps">Please enter your details to sign in.</p>
            </div>

            <form action="{{ route('login') }}" method="POST" class="form-minimal">
                @csrf

                <div style="margin-bottom: 1.5rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="enter your email" required autofocus>
                    @error('email')
                        <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <div class="password-row">
                        <label class="label-caps" style="margin-bottom: 0;">Password</label>
                        <a href="#" class="forgot-link">Forgot?</a>
                    </div>
                    <div style="position: relative;">
                        <input type="password" name="password" id="passwordInput" placeholder="enter your password" required style="padding-right: 2.5rem;">
                        <button type="button" id="togglePassword" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #adb5bd; padding: 0.5rem; display: flex; align-items: center; justify-content: center;">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeOffIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; align-items: center; margin-bottom: 2rem;">
                    <input type="checkbox" name="remember" id="remember" style="margin-right: 0.5rem; width: auto;">
                    <label for="remember" class="label-caps" style="cursor: pointer; margin-bottom: 0;">Remember me</label>
                </div>

                <button type="submit" class="btn btn-vesta">
                    Sign In &rarr;
                </button>
            </form>

            <p class="mt-5 text-center">
                <span class="label-caps">Don't have an account?</span>
                <a href="{{ route('register') }}" class="text-dark ms-1" style="text-decoration: underline;">Sign Up</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        if(togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if(type === 'text') {
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                    togglePassword.style.color = '#333';
                } else {
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                    togglePassword.style.color = '#adb5bd';
                }
            });
        }
    });
</script>
@endsection