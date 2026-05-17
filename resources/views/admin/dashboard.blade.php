@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Performance Overview</h6>
        <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">ADMIN | SALES INTELLIGENCE</h1>
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
            <h2 class="stat-value">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            <p class="text-success small mb-0">+12.5% <span class="text-muted">vs last period</span></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <span class="stat-label">Total Orders</span>
            <h2 class="stat-value">{{ number_format($totalOrders, 0, ',', '.') }}</h2>
            <p class="text-success small mb-0">+8.2% <span class="text-muted">vs last period</span></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <span class="stat-label">Avg. Order Value</span>
            <h2 class="stat-value">IDR {{ number_format($avgOrderValue, 0, ',', '.') }}</h2>
            <p class="text-danger small mb-0">-1.6% <span class="text-muted">vs last period</span></p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Placeholder -->
<div class="col-lg-8">
    <div class="admin-card">
        <div class="d-flex justify-content-between mb-4">
            <h6 class="fw-bold small text-uppercase">DAILY REVENUE</h6>
            <span class="text-muted small">Net sales over the last 7 days</span>
        </div>
        <div style="height: 300px; position: relative;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

    <!-- Top Products Dinamis -->
<!-- Top Products Dinamis -->
<div class="col-lg-4">
    <div class="admin-card">
        <h6 class="fw-bold small text-uppercase mb-4">Top Products</h6>
        @forelse($topProducts as $item)
        <div class="d-flex align-items-center mb-3">
            <div class="bg-light rounded overflow-hidden" style="width: 40px; height: 40px;">
                <!-- UPDATE BAGIAN INI -->
                <img src="{{ asset('product_image/' . ($item->product->image_path ?? 'default.jpg')) }}" 
                     class="w-100 h-100 object-fit-cover" 
                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=V&background=000&color=fff';">
            </div>
            <div class="ms-3 flex-grow-1">
                <p class="mb-0 fw-bold small" style="font-size: 11px;">{{ $item->product->name }}</p>
                <p class="text-muted mb-0" style="font-size: 10px;">{{ $item->product->category->name ?? '-' }}</p>
            </div>
            <div class="text-end">
                <p class="mb-0 fw-bold small" style="font-size: 11px;">{{ $item->units_sold }}</p>
                <p class="text-muted mb-0" style="font-size: 10px;">UNITS</p>
            </div>
        </div>
        @empty
        <p class="text-muted small">No sales data yet.</p>
        @endforelse

            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold small text-uppercase mb-2" style="font-size: 10px;">Intelligence Insight</h6>
                <p class="text-muted small lh-base">
                    Analisis data menunjukkan tren positif pada kategori <span class="text-dark fw-bold">Clothing</span> minggu ini.
                </p>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvasElement = document.getElementById('revenueChart');
    
    if (canvasElement) {
        const ctx = canvasElement.getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'REVENUE',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#1a1a1a',
                    backgroundColor: 'rgba(26, 26, 26, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'IDR ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection {{-- PASTIKAN SCRIPT ADA SEBELUM ENDSECTION --}}