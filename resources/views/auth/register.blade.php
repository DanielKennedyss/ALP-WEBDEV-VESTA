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

    .form-minimal input::placeholder {
        color: #adb5bd;
        font-size: 0.9rem;
        font-weight: 400;
        letter-spacing: 0.02em;
        text-transform: none;
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
            <p>Welcome to the club. Elevate your curation.</p>
        </div>
    </div>

    <!-- SISI KANAN: Form Registration -->
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
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Email Address</label>
                    <input type="email" name="email" placeholder="email@example.com" required>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Phone Number</label>
                    <input type="text" name="phone_number" placeholder="0812..." required>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Password</label>
                    <input type="password" name="password" placeholder="Minimum 8 characters" required>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label class="label-caps" style="display: block; margin-bottom: 0.5rem;">Confirm Password</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat password" required>
                </div>

                <button type="submit" class="btn btn-vesta">
                    Create Account &rarr;
                </button>
            </form>

            <p style="margin-top: 2rem; text-align: center;">
                <span class="label-caps">Already a member?</span>
                <a href="{{ route('login') }}" class="text-dark ms-1" style="text-decoration: underline;">Sign In Here</a>
            </p>
        </div>
    </div>
</div>
@endsection