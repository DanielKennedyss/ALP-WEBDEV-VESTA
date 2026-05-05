@extends('base.base')

@section('content')
<!-- Link Bootstrap CDN jika belum ada di base.base -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Custom Styling untuk kesan VESTA Premium */
    .vh-100-custom { min-height: 100vh; }
    .bg-editorial { background-color: #f8f9fa; } /* Sisi kiri kosong/abu-abu muda */
    .form-minimal input {
        border: none;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0;
        padding-left: 0;
        font-size: 0.9rem;
    }
    .form-minimal input:focus {
        box-shadow: none;
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
        padding: 15px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.3em;
        text-transform: uppercase;
    }
    nav, footer { display: none !important; }
</style>

<div class="container-fluid p-0 overflow-hidden">
    <div class="row g-0 vh-100-custom">
        <!-- SISI KIRI: Editorial Space (Kosong Dahulu) -->
        <div class="col-lg-6 bg-editorial d-none d-lg-flex align-items-end p-5">
            <div>
                <h1 class="display-3 fw-light tracking-tighter mb-4">VESTA</h1>
                <p class="text-muted text-uppercase small tracking-widest">
                    Precision Engineering. Minimalist Utility.
                </p>
            </div>
        </div>

        <!-- SISI KANAN: Form Registration -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white p-5">
            <div class="w-100" style="max-width: 400px;">
                <div class="mb-5">
                    <h2 class="fw-normal mb-1">Join the Club.</h2>
                    <p class="text-muted small">Create an account to access exclusive benefits.</p>
                </div>

                <form action="{{ route('register') }}" method="POST" class="form-minimal">
                    @csrf
                    <div class="mb-4">
                        <label class="label-caps">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                    </div>

                    <div class="mb-4">
                        <label class="label-caps">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
                    </div>

                    <div class="mb-4">
                        <label class="label-caps">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control" placeholder="0812..." required>
                    </div>

                    <div class="mb-4">
                        <label class="label-caps">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    </div>

                    <div class="mb-5">
                        <label class="label-caps">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                    </div>

                    <button type="submit" class="btn btn-vesta w-100 border-0">
                        Create Account &rarr;
                    </button>
                </form>

                <p class="mt-5 text-center label-caps">
                    Already a member? <a href="{{ route('login') }}" class="text-dark text-decoration-none fw-bold">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection