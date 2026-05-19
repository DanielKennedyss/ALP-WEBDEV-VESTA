@extends('layouts.admin')

@section('admin_content')

{{-- Style Animasi   dashboard lebih "Hidup" dan Premium --}}
<style>
    .admin-card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0; /* Mulai dari tak terlihat sebelum animasi jalan */
    }
    
    /* Animasi saat kursor diarahkan ke kotak */
    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Staggered Delay: Agar kotak muncul bergantian dari kiri ke kanan */
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }
</style>

<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h6 class="text-muted text-uppercase small tracking-widest mb-2" style="font-size: 10px; font-weight: 700;">Performance Overview</h6>
        <h1 class="fw-normal tracking-tighter" style="font-size: 2.5rem;">ADMIN | SALES INTELLIGENCE</h1>
    </div>
    <div class="d-flex gap-3">
        <select class="form-select form-select-sm border-0 bg-white shadow-sm rounded-pill px-3 delay-1" style="font-size: 11px; animation: fadeInUp 0.6s ease-out forwards; opacity: 0;">
            <option>Last 30 Days</option>
        </select>
        <button class="btn btn-dark rounded-pill px-4 py-2 delay-2" style="font-size: 11px; font-weight: 700; letter-spacing: 0.1em; animation: fadeInUp 0.6s ease-out forwards; opacity: 0;">EXPORT REPORT</button>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        {{-- Dikembalikan menggunakan class asli (stat-label & stat-value) --}}
        <div class="admin-card delay-1 bg-white shadow-sm rounded-4 p-4 h-100 border-0">
            <span class="stat-label">Total Revenue</span>
            <h2 class="stat-value">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
            <p class="{{ $revenueGrowth >= 0 ? 'text-success' : 'text-danger' }} small mb-0">
                {{ $revenueGrowth >= 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}% 
                <span class="text-muted">vs last period</span>
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="admin-card delay-2 bg-white shadow-sm rounded-4 p-4 h-100 border-0">
            <span class="stat-label">Total Orders</span>
            <h2 class="stat-value">{{ number_format($totalOrders, 0, ',', '.') }}</h2>
            <p class="{{ $orderGrowth >= 0 ? 'text-success' : 'text-danger' }} small mb-0">
                {{ $orderGrowth >= 0 ? '+' : '' }}{{ number_format($orderGrowth, 1) }}% 
                <span class="text-muted">vs last period</span>
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="admin-card delay-3 bg-white shadow-sm rounded-4 p-4 h-100 border-0">
            <span class="stat-label">Avg. Order Value</span>
            <h2 class="stat-value">IDR {{ number_format($avgOrderValue, 0, ',', '.') }}</h2>
            <p class="{{ $aovGrowth >= 0 ? 'text-success' : 'text-danger' }} small mb-0">
                {{ $aovGrowth >= 0 ? '+' : '' }}{{ number_format($aovGrowth, 1) }}% 
                <span class="text-muted">vs last period</span>
            </p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card delay-4 bg-white shadow-sm rounded-4 p-4 h-100 border-0">
            <div class="d-flex justify-content-between mb-4">
                <h6 class="fw-bold small text-uppercase mb-0">DAILY REVENUE</h6>
                <span class="text-muted small">Net sales over the last 7 days</span>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card delay-5 bg-white shadow-sm rounded-4 p-4 h-100 border-0">
            <h6 class="fw-bold small text-uppercase mb-4">Top Products</h6>
            
            @forelse($topProducts as $item)
            <div class="d-flex align-items-center mb-3">
                <div class="bg-light rounded overflow-hidden shadow-sm" style="width: 40px; height: 40px; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
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
                <p class="text-muted small lh-base mb-0">
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
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#1a1a1a',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a1a',
                        titleFont: { size: 13 },
                        bodyFont: { size: 14, weight: 'bold' },
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'IDR ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        border: { dash: [4, 4] },
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            callback: function(value) {
                                return 'IDR ' + value.toLocaleString();
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }
});
</script>
@endsection