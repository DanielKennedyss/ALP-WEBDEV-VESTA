@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-start mb-5">
    <div>
        <h1 class="fw-normal tracking-tight mb-1" style="font-size: 2.5rem;">Inventory Health</h1>
        <p class="text-muted small">Real-time monitoring of stock levels and critical alerts.</p>
    </div>
    <button class="btn btn-dark rounded-pill px-4 py-2" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em;">EXPORT REPORT</button>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="admin-card">
            <span class="stat-label">Total Active SKUs</span>
            <h2 class="stat-value">12,450</h2>
            <p class="text-success small mb-0">● System capacity optimal</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <span class="stat-label">Low Stock Items</span>
            <h2 class="stat-value">342</h2>
            <p class="text-warning small mb-0">Approaching restock threshold</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card border-danger border-opacity-25">
            <span class="stat-label text-danger">Critical Depletion</span>
            <h2 class="stat-value text-danger">18</h2>
            <p class="text-danger small mb-0">Immediate restock required</p>
        </div>
    </div>
</div>

<!-- Action Required Table -->
<div class="admin-card">
    <div class="d-flex justify-content-between mb-4">
        <h5 class="fw-bold">Action Required</h5>
        <a href="#" class="text-dark small text-decoration-none fw-bold tracking-widest" style="font-size: 10px;">VIEW ALL ALERTS</a>
    </div>
    <table class="table align-middle">
        <thead class="text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em;">
            <tr>
                <th>Product Details</th>
                <th>SKU</th>
                <th>Current Stock</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody style="font-size: 13px;">
            <tr>
                <td class="py-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-light rounded" style="width: 40px; height: 40px;"></div>
                        <div class="ms-3">
                            <p class="mb-0 fw-medium">Cashmere Lounge Set - Charcoal</p>
                        </div>
                    </div>
                </td>
                <td>VS-CSH-002</td>
                <td>0</td>
                <td><span class="status-badge status-out">Out of Stock</span></td>
                <td><a href="#" class="text-dark">●●●</a></td>
            </tr>
            <!-- Tambahkan baris lain sesuai kebutuhan -->
        </tbody>
    </table>
</div>
@endsection