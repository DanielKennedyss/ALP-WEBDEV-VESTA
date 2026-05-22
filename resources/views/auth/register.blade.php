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

    .form-minimal input[type="text"],
    .form-minimal input[type="email"],
    .form-minimal input[type="password"] {
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

    .form-minimal input[type="text"]:focus,
    .form-minimal input[type="email"]:focus,
    .form-minimal input[type="password"]:focus {
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

    .btn-google {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        border: 1px solid #dee2e6;
        padding: 1rem;
        color: #333;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.3em;
        text-transform: uppercase;
        transition: all 0.3s ease;
        text-decoration: none;
        background: #fff;
    }

    .btn-google:hover {
        background: #f8f9fa;
        border-color: #000;
    }

    .form-minimal input::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
        font-weight: 400;
        letter-spacing: 0.02em;
        text-transform: none;
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
    <div class="editorial-panel">
        <div>
            <h1>VESTA</h1>
            <p>Welcome to the club. Elevate your curation.</p>
        </div>
    </div>

    <div class="form-panel">
        <div style="width: 100%; max-width: 420px;">
            <div style="margin-bottom: 2rem;">
                <h2 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.75rem;">Join the Club.</h2>
                <p class="label-caps">Create an account to access exclusive benefits.</p>
            </div>

            <form action="{{ route('register') }}" method="POST" class="form-minimal">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter your name" required>
                    @error('name')
                        <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com" required>
                    @error('email')
                        <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Phone Number</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" placeholder="0812..." required>
                    @error('phone_number')
                        <div style="color: #dc3545; font-size: 0.8rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="passwordInput" placeholder="Minimum 8 characters" required style="padding-right: 2.5rem;">
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

                <div style="margin-bottom: 2rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Confirm Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirmation" id="passwordConfirmInput" placeholder="Repeat password" required style="padding-right: 2.5rem;">
                        <button type="button" id="togglePasswordConfirm" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #adb5bd; padding: 0.5rem; display: flex; align-items: center; justify-content: center;">
                            <svg id="eyeIconConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeOffIconConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-vesta">
                    Create Account &rarr;
                </button>
            </form>

            <div style="display: flex; align-items: center; margin: 2rem 0;">
                <div style="flex: 1; height: 1px; background: #dee2e6;"></div>
                <span style="padding: 0 1rem; color: #adb5bd; font-size: 12px; text-transform: uppercase;">Or</span>
                <div style="flex: 1; height: 1px; background: #dee2e6;"></div>
            </div>

            <a href="{{ route('google.login') }}" class="btn-google">
                <svg width="16" height="16" style="margin-right: 10px;" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Continue with Google
            </a>

            <p style="margin-top: 2rem; text-align: center;">
                <span class="label-caps">Already a member?</span>
                <a href="{{ route('login') }}" class="text-dark ms-1" style="text-decoration: underline;">Sign In Here</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Main Password
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

        // Confirm Password
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirmInput = document.getElementById('passwordConfirmInput');
        const eyeIconConfirm = document.getElementById('eyeIconConfirm');
        const eyeOffIconConfirm = document.getElementById('eyeOffIconConfirm');

        if(togglePasswordConfirm && passwordConfirmInput) {
            togglePasswordConfirm.addEventListener('click', function() {
                const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmInput.setAttribute('type', type);
                
                if(type === 'text') {
                    eyeIconConfirm.style.display = 'none';
                    eyeOffIconConfirm.style.display = 'block';
                    togglePasswordConfirm.style.color = '#333';
                } else {
                    eyeIconConfirm.style.display = 'block';
                    eyeOffIconConfirm.style.display = 'none';
                    togglePasswordConfirm.style.color = '#adb5bd';
                }
            });
        }
    });
</script>
@endsection