@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Performance Overview</h6>
        <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">Admin | Sales Intelligence</h1>
    </div>
    <div class="d-flex gap-3">
        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3" style="font-size: 11px;">
            <option>Last 30 Days</option>
        </select>
        <button class="btn btn-dark rounded-pill px-4 py-2" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em;">EXPORT REPORT</button>
    </div>
</div>

<!-- Statistik Utama -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="admin-card">
            <span class="stat-label">Total Revenue</span>
            <h2 class="stat-value">IDR 1.248.500</h2>
            <p class="text-success small mb-0">+12.5% <span class="text-muted">vs last period</span></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <span class="stat-label">Total Orders</span>
            <h2 class="stat-value">3.492</h2>
            <p class="text-success small mb-0">+8.2% <span class="text-muted">vs last period</span></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <span class="stat-label">Avg. Order Value</span>
            <h2 class="stat-value">IDR 357.530</h2>
            <p class="text-danger small mb-0">-1.6% <span class="text-muted">vs last period</span></p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Placeholder (Sales Trend) -->
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="d-flex justify-content-between mb-4">
                <h6 class="fw-bold small text-uppercase">Daily Revenue</h6>
                <span class="text-muted small">Net sales over the last 30 days</span>
            </div>
            <!-- Area untuk Chart (Bisa pakai Chart.js nanti) -->
            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px; border-radius: 4px;">
                <p class="text-muted small italic">[ Sales Intelligence Chart Placeholder ]</p>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-4">
        <div class="admin-card">
            <h6 class="fw-bold small text-uppercase mb-4">Top Products</h6>
            @foreach($top_products ?? [] as $product)
            <div class="d-flex align-items-center mb-3">
                <div class="bg-light rounded" style="width: 40px; height: 40px;"></div>
                <div class="ms-3 flex-grow-1">
                    <p class="mb-0 fw-bold small" style="font-size: 11px;">{{ $product->name }}</p>
                    <p class="text-muted mb-0" style="font-size: 10px;">{{ $product->category }}</p>
                </div>
                <div class="text-end">
                    <p class="mb-0 fw-bold small" style="font-size: 11px;">{{ $product->sales_count }}</p>
                    <p class="text-muted mb-0" style="font-size: 10px;">UNITS</p>
                </div>
            </div>
            @endforeach
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold small text-uppercase mb-2" style="font-size: 10px;">Intelligence Insight</h6>
                <p class="text-muted small lh-base">
                    The "Merino Collection" has seen a <span class="text-dark fw-bold">24% surge</span> in organic checkout volume over the last 72 hours.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection