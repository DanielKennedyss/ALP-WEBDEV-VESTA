@extends('base.base')

@section('content')
<style>
    /* Reset & Base Layout */
    html, body { overflow: hidden; height: 100vh; width: 100vw; margin: 0; padding: 0; }
    body { padding-top: 0 !important; }
    nav, footer { display: none !important; }

    .login-wrapper { display: flex; height: 100vh; width: 100vw; }

    /* Editorial Panel */
    .editorial-panel {
        flex: 0 0 50%;
        background-color: #f8f9fa;
        background-image: url('https://images.unsplash.com/photo-1445205170230-053b83016050?w=1200&q=80');
        background-size: cover;
        background-position: center;
        position: relative;
        display: flex;
        align-items: flex-end;
        padding: 4rem;
    }
    .editorial-panel::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), rgba(0,0,0,0.1));
        z-index: 1;
    }
    .editorial-panel > div { position: relative; z-index: 2; color: white; }
    .editorial-panel h1 { font-family: ui-serif, serif; font-size: 5rem; font-weight: 300; letter-spacing: 0.1em; margin-bottom: 1rem; }
    .editorial-panel p { text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.25em; color: #e0e0e0; }

    /* Form Panel */
    .form-panel { flex: 0 0 50%; display: flex; align-items: center; justify-content: center; padding: 3rem; background: #fff; }
    
    .form-minimal input {
        border: none; border-bottom: 1px solid #dee2e6; border-radius: 0;
        padding: 0.75rem 0; font-size: 1rem; color: #333; background: transparent; width: 100%;
    }
    .form-minimal input:focus { border-bottom: 2px solid #000; outline: none; }
    
    .btn-vesta {
        background: #000; color: #fff; padding: 1rem; font-size: 10px; font-weight: 700;
        letter-spacing: 0.3em; text-transform: uppercase; width: 100%; transition: all 0.3s; cursor: pointer; border: none;
    }
    .btn-vesta:hover { background: #333; }

    .btn-google {
        display: flex; align-items: center; justify-content: center; width: 100%;
        border: 1px solid #dee2e6; padding: 1rem; color: #333; font-size: 10px;
        font-weight: 700; letter-spacing: 0.3em; text-transform: uppercase; transition: all 0.3s;
        margin-top: 1rem; text-decoration: none;
    }
    .btn-google:hover { background: #f8f9fa; border-color: #000; }

    .back-btn {
        position: absolute; top: 2rem; left: 2rem; z-index: 100;
        display: flex; align-items: center; gap: 0.5rem; color: #fff;
        text-transform: uppercase; letter-spacing: 0.15em; font-size: 0.75rem; text-decoration: none;
    }

    /* Error Styling */
    .error-text {
        color: #dc3545; font-size: 11px; display: block; margin-top: 5px; font-weight: 500; text-transform: none; letter-spacing: normal;
    }

    @media (max-width: 991px) {
        .editorial-panel { display: none; }
        .form-panel { flex: 0 0 100%; }
        html, body { overflow: auto; }
    }
</style>

<div class="login-wrapper">
    <a href="{{ route('home') }}" class="back-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back
    </a>

    <div class="editorial-panel">
        <div>
            <h1>VESTA</h1>
            <p>Welcome back. Elevate your curation once again.</p>
        </div>
    </div>

    <div class="form-panel">
        <div style="width: 100%; max-width: 400px;">
            <div class="mb-8">
                <h2 style="font-size: 1.5rem; font-weight: 500;">Sign In</h2>
            </div>

            <form action="{{ route('login') }}" method="POST" class="form-minimal">
                @csrf

                {{-- Menampilkan pesan error global --}}
                @if (session('error'))
                    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 1rem; font-size: 12px; text-align: center;">
                        {{ session('error') }}
                    </div>
                @endif

                <div style="margin-bottom: 1.5rem;">
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus>
                    {{-- Menampilkan error khusus input email --}}
                    @error('email')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                <div style="margin-bottom: 2rem;">
                    <input type="password" name="password" placeholder="Password" required>
                    {{-- Menampilkan error khusus input password --}}
                    @error('password')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
                
                <button type="submit" class="btn-vesta">Sign In &rarr;</button>
            </form>

            <div style="display: flex; align-items: center; margin: 2rem 0;">
                <div style="flex: 1; height: 1px; background: #eee;"></div>
                <span style="padding: 0 1rem; color: #adb5bd; font-size: 10px; text-transform: uppercase;">Or continue with</span>
                <div style="flex: 1; height: 1px; background: #eee;"></div>
            </div>

            <a href="{{ route('google.login') }}" class="btn-google">
                <svg width="16" height="16" style="margin-right: 10px;" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Google
            </a>

            <p style="margin-top: 2rem; font-size: 11px; color: #666; text-align: center;">
                Don't have an account? <a href="{{ route('register') }}" style="text-decoration: underline; color: #000;">Sign Up</a>
            </p>
        </div>
    </div>
</div>
@endsection