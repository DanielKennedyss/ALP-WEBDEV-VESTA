@extends('layouts.admin')

@section('admin_content')

{{-- Style Modern Minimalist Luxury Dashboard --}}
<style>
    /* Reset ke palet warna Monokrom/Luxury */
    :root {
        --accent-color: #121212; /* Charcoal Black */
        --accent-light: #f3f4f6; /* Sangat muda abu-abu */
        --bg-color: #fafafa;     /* Latar belakang putih gading */
        --card-shadow: 0 4px 30px rgba(0, 0, 0, 0.03); /* Shadow sangat lembut */
    }

    body, .admin-content {
        background-color: var(--bg-color);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Card Style */
    .saas-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.02);
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        padding: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .saas-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06);
    }

    .card-title-text {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .card-subtitle-text {
        font-size: 11px;
        color: #94a3b8;
    }

    .metric-value {
        font-family: ui-serif, Georgia, serif; /* Angka utama sedikit klasik */
        font-size: 28px;
        font-weight: 500;
        color: #121212;
        letter-spacing: -0.01em;
    }

    /* Lingkaran Ikon (Monokrom) */
    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--accent-light);
        color: #121212;
    }

    /* Indikator Growth (Lebih halus, tidak terlalu mencolok) */
    .growth-indicator {
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .growth-up { background-color: #ecfdf5; color: #059669; }
    .growth-down { background-color: #fef2f2; color: #dc2626; }

    /* Layout Horizontal Product */
    .product-grid-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    .product-grid-card:hover {
        border-color: #e2e8f0;
    }
    .product-img-box {
        background: #f8fafc;
        border-radius: 8px;
        height: 160px;
        width: 100%;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .product-img-box img {
        transition: transform 0.5s ease, filter 0.5s ease;
        filter: grayscale(20%);
    }
    .product-grid-card:hover .product-img-box img {
        transform: scale(1.05);
        filter: grayscale(0%);
    }

    /* Filter Dropdown Modern */
    .saas-select {
        appearance: none;
        background: #fff url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23121212%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.4-12.8z%22%2F%3E%3C%2Fsvg%3E") no-repeat right 12px top 50%;
        background-size: 8px auto;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 8px 30px 8px 12px;
        font-size: 13px;
        font-weight: 500;
        color: #121212;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .saas-select:hover {
        border-color: #cbd5e1;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 pt-2">
    <div>
        <h3 class="fw-bold text-dark mb-0" style="letter-spacing: -0.02em;">OVERVIEW</h3>
        <p class="card-subtitle-text mt-1 mb-0">{{ now()->format('l, d F Y') }}</p>
    </div>
    
    <div class="d-flex gap-3 align-items-center">
        {{-- Dropdown Filter dipindah ke atas --}}
        <form id="filterForm" action="{{ route('admin.dashboard') }}" method="GET" class="mb-0">
            <div class="d-flex align-items-center gap-2">
                <select id="periodSelect" name="period" class="saas-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="30" {{ request('period', '30') == '30' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="1" {{ request('period') == '1' ? 'selected' : '' }}>Today (Live)</option>
                </select>
            </div>
        </form>

        {{-- REVISI: Tambahkan action triggerExport() saat tombol ditekan --}}
        <button onclick="triggerExport()" class="btn btn-dark rounded shadow-sm px-4 py-2" style="font-size: 13px; font-weight: 500; background-color: #121212; border: none;">
            Export
        </button>
    </div>
</div>

{{-- Baris 1: 3 Metrik Utama (Monokrom) --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="saas-card d-flex flex-column justify-content-between" style="height: 140px;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="card-title-text">Total Revenue</h6>
                    <span class="card-subtitle-text">Periode aktif</span>
                </div>
                <div class="icon-circle">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-auto">
                <h3 class="metric-value mb-0">IDR {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <span class="growth-indicator {{ $revenueGrowth >= 0 ? 'growth-up' : 'growth-down' }}">
                    {!! $revenueGrowth >= 0 ? '↗' : '↘' !!} {{ number_format(abs($revenueGrowth), 1) }}%
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="saas-card d-flex flex-column justify-content-between" style="height: 140px;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="card-title-text">Total Order</h6>
                    <span class="card-subtitle-text">Periode aktif</span>
                </div>
                <div class="icon-circle">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-auto">
                <h3 class="metric-value mb-0">{{ number_format($totalOrders, 0, ',', '.') }}</h3>
                <span class="growth-indicator {{ $orderGrowth >= 0 ? 'growth-up' : 'growth-down' }}">
                    {!! $orderGrowth >= 0 ? '↗' : '↘' !!} {{ number_format(abs($orderGrowth), 1) }}%
                </span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="saas-card d-flex flex-column justify-content-between" style="height: 140px;">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="card-title-text">Avg. Order Value</h6>
                    <span class="card-subtitle-text">Periode aktif</span>
                </div>
                <div class="icon-circle">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-end mt-auto">
                <h3 class="metric-value mb-0">IDR {{ number_format($avgOrderValue, 0, ',', '.') }}</h3>
                <span class="growth-indicator {{ $aovGrowth >= 0 ? 'growth-up' : 'growth-down' }}">
                    {!! $aovGrowth >= 0 ? '↗' : '↘' !!} {{ number_format(abs($aovGrowth), 1) }}%
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Baris 2: Chart & Insight --}}
<div class="row g-4 mb-4">
    {{-- Main Chart --}}
    <div class="col-lg-8">
        <div class="saas-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="card-title-text mb-0">Sales Analytic</h6>
                <span class="card-subtitle-text">
                    @if(request('period') == '1')
                        Real-time Hari Ini
                    @elseif(request('period') == '7')
                        7 Hari Terakhir
                    @else
                        30 Hari Terakhir
                    @endif
                </span>
            </div>
            <div style="height: 300px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Side Insight (AI Recommendation saja) --}}
    <div class="col-lg-4">
        <div class="saas-card h-100 d-flex flex-column">
            <h6 class="card-title-text mb-4">Intelligence Insight</h6>
            
            <div class="flex-grow-1 d-flex flex-column justify-content-center">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background-color: var(--accent-light);">
                        <svg width="32" height="32" fill="none" stroke="#121212" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <h6 class="fw-bold text-dark mb-2">AI RECOMMENDATION</h6>
                    <p class="text-muted mb-0 mx-auto" style="font-size: 13px; line-height: 1.6; max-width: 250px;">
                        Berdasarkan data penjualan terakhir, kategori <strong>{{ $topProducts->first()->product->category->name ?? 'Produk Utama' }}</strong> sangat mendominasi. Pertimbangkan untuk mengatur ulang strategi inventaris minggu depan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Baris 3: Top Selling Products (Layout Horizontal) --}}
<div class="saas-card mb-5">
    <div class="mb-4">
        <h6 class="card-title-text mb-0">Top Selling Products</h6>
    </div>

    <div class="row g-4">
        @forelse($topProducts as $item)
        <div class="col-md-4">
            <div class="product-grid-card h-100">
                <div class="product-img-box">
                    <img src="{{ asset('product_image/' . ($item->product->image_path ?? 'default.jpg')) }}" 
                         class="img-fluid" style="max-height: 100%; object-fit: contain;" 
                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=V&background=f1f5f9&color=121212';">
                </div>
                <h6 class="fw-bold text-truncate mb-1" style="font-size: 14px; color: #1e293b;">{{ $item->product->name }}</h6>
                <p class="card-subtitle-text mb-3">{{ $item->product->category->name ?? 'Clothing' }}</p>
                
                <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: #f1f5f9 !important;">
                    <span class="text-dark fw-bold" style="font-size: 13px;">{{ $item->units_sold }} Pcs Sold</span>
                    <span class="badge bg-dark text-white rounded-pill px-2 py-1" style="font-size: 11px;">#{{ $loop->iteration }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted mb-0">No sales data available yet.</p>
        </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// REVISI: Penambahan fungsi pengalih rute export agar dinamis menangkap isi dropdown
function triggerExport() {
    const activePeriod = document.getElementById('periodSelect').value;
    window.location.href = `{{ route('admin.dashboard.export') }}?period=${activePeriod}`;
}

document.addEventListener('DOMContentLoaded', function () {
    const canvasElement = document.getElementById('revenueChart');
    
    if (canvasElement) {
        const ctx = canvasElement.getContext('2d');
        
        // Gradien Aksen Monokrom (Hitam ke Transparan)
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(18, 18, 18, 0.1)');
        gradient.addColorStop(1, 'rgba(18, 18, 18, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Income',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#121212', /* Charcoal Solid */
                    borderWidth: 2.5, 
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.45, /* Bergelombang halus */
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#121212',
                    pointBorderWidth: 2,
                    pointRadius: 0, 
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#121212',
                        titleFont: { size: 12 },
                        bodyFont: { size: 14, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 6,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'IDR ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        grid: { color: '#f1f5f9', drawTicks: false },
                        ticks: {
                            font: { size: 11 },
                            color: '#94a3b8',
                            padding: 10,
                            callback: function(value) {
                                if (value >= 1000000) return (value/1000000) + 'M';
                                if (value >= 1000) return (value/1000) + 'k';
                                return value;
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }
});
</script>
@endsection