@extends('base.base')

@section('content')
<style>
    html, body { overflow: hidden; height: 100vh; width: 100vw; margin: 0; padding: 0; }
    body { padding-top: 0 !important; }
    nav, footer { display: none !important; }

    .verify-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background: #f8f9fa;
    }

    .verify-card {
        background: #fff;
        padding: 3rem;
        max-width: 420px;
        width: 100%;
        text-align: center;
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
        cursor: pointer;
    }
    .btn-vesta:hover { background: #333; color: #fff; }
</style>

<div class="verify-wrapper">
    <div class="verify-card">
        <h2 style="font-weight: 600; margin-bottom: 0.5rem; font-size: 1.75rem;">Verify Your Account</h2>
        <p class="label-caps">Account verification is coming soon.</p>
        <a href="{{ route('home') }}" class="btn btn-vesta" style="display: inline-block; text-decoration: none; margin-top: 1.5rem;">Back to Home</a>
    </div>
</div>
@endsection
