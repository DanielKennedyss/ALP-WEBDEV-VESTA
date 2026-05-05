@extends('base.base')

@section('content')
<!-- Link Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container py-5 mt-5">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-md-8">
            <h6 class="text-muted text-uppercase tracking-widest small mb-2" style="letter-spacing: 0.3em;">Welcome Back</h6>
            <h1 class="display-5 fw-bold tracking-tighter uppercase">{{ Auth::user()->name }}</h1>
        </div>
        <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
            <div class="border border-dark px-4 py-2">
                <span class="text-uppercase small fw-bold" style="letter-spacing: 0.2em;">Tier: {{ Auth::user()->membership_level }}</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-5">
        <!-- Loyalty Points -->
        <div class="col-md-4">
            <div class="p-5 border bg-light d-flex flex-column h-100">
                <span class="label-caps text-muted mb-4" style="font-size: 10px; font-weight: 700; letter-spacing: 0.2em;">Loyalty Points</span>
                <h2 class="display-6 fw-light mb-0">{{ Auth::user()->loyalty_points ?? 0 }} <small class="fs-6 text-muted">PTS</small></h2>
            </div>
        </div>

        <!-- Order History Placeholder -->
        <div class="col-md-4">
            <div class="p-5 border d-flex flex-column h-100">
                <span class="label-caps text-muted mb-4" style="font-size: 10px; font-weight: 700; letter-spacing: 0.2em;">Active Orders</span>
                <h2 class="display-6 fw-light mb-0">0</h2>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="p-5 border d-flex flex-column h-100 justify-content-between">
                <span class="label-caps text-muted mb-4" style="font-size: 10px; font-weight: 700; letter-spacing: 0.2em;">Account Action</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-link text-dark p-0 text-decoration-none fw-bold text-uppercase small" style="letter-spacing: 0.2em;">
                        Logout &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Banner / Information -->
    <div class="row">
        <div class="col-12">
            <div class="bg-dark text-white p-5 text-center">
                <h3 class="fw-light mb-3">VESTA Winter Collection '26 is coming.</h3>
                <p class="text-white-50 small text-uppercase tracking-widest">Exclusively for {{ Auth::user()->membership_level }} members.</p>
            </div>
        </div>
    </div>
</div>
@endsection