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
        {{-- Dropdown Filter Periode Dashboard --}}
        <form id="filterForm" action="{{ route('admin.dashboard') }}" method="GET" class="mb-0">
            <div class="d-flex align-items-center gap-2">
                <select id="periodSelect" name="period" class="saas-select" onchange="document.getElementById('filterForm').submit()">
                    <option value="30" {{ request('period', '30') == '30' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="7" {{ request('period') == '7' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="1" {{ request('period') == '1' ? 'selected' : '' }}>Today (Live)</option>
                </select>
            </div>
        </form>

        {{-- Tombol Unduh Excel Langsung (Bawaan) --}}
        <button onclick="triggerExport()" class="btn btn-dark rounded shadow-sm px-4 py-2" style="font-size: 13px; font-weight: 500; background-color: #121212; border: none;">
            Download Excel
        </button>
    </div>
</div>

{{-- Notifikasi Sukses / Gagal Antrean Email --}}
@if(session('success'))
    <div class="alert alert-dark text-white border-0 rounded-3 mb-4 p-3" style="background-color: #121212; font-size: 13px;">
        ⚡ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger border-0 rounded-3 mb-4 p-3" style="font-size: 13px;">
        ⚠️ {{ session('error') }}
    </div>
@endif

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

    {{-- Side Insight: Dynamic AI Engine Matrix --}}
    <div class="col-lg-4">
        <div class="saas-card h-100 d-flex flex-column justify-content-between" style="padding: 32px;">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="card-title-text mb-0" style="font-size: 11px; letter-spacing: 0.15em;">Intelligence Insight</h6>
                    <span class="badge bg-dark text-white rounded-pill px-2 py-1" style="font-size: 9px; font-weight: 700; letter-spacing: 0.05em;">ENGINE V1.0</span>
                </div>
                
                {{-- Progress Ring --}}
                @if(isset($contributionPercentage) && $contributionPercentage > 0)
                    <div class="my-4 py-2 d-flex align-items-center gap-3 border-bottom pb-4" style="border-color: #f1f5f9 !important;">
                        <div class="position-relative d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <div class="rounded-circle position-absolute w-100 h-100" style="border: 4px solid #f3f4f6;"></div>
                            <div class="rounded-circle position-absolute w-100 h-100" style="border: 4px solid #121212; border-top-color: transparent; border-left-color: transparent; transform: rotate({{ ($contributionPercentage / 100) * 360 }}deg);"></div>
                            <span class="fw-bold text-dark" style="font-size: 13px; font-family: ui-serif, Georgia, serif;">{{ number_format($contributionPercentage, 0) }}%</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px;">Product Concentration</h6>
                            <span class="card-subtitle-text">Kontribusi produk terlaris</span>
                        </div>
                    </div>
                @endif

                {{-- Slot Teks Insight Dinamis --}}
                <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.7; text-align: justify; color: #475569 !important;">
                    {!! $insightText !!}
                </p>
            </div>

            {{-- Timestamp Pemrosesan Riil --}}
            <div class="pt-3 border-t mt-4" style="border-color: #f1f5f9 !important;">
                <span class="text-muted d-flex align-items-center gap-1" style="font-size: 10px; font-family: monospace; letter-spacing: 0.02em;">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="me-1"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                    DATA PROCESSED SECURELY AT {{ now()->format('H:i:s') }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Baris 3: Integrasi Modul Baru - Form Dispatch Financial Report & Top Products --}}
<div class="row g-4 mb-5">
    {{-- Form Kirim Laporan via Email (Luxury Component) --}}
    <div class="col-md-5">
        <div class="saas-card h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <h6 class="card-title-text mb-0" style="font-size: 13px;">Transmit Intelligence Report</h6>
                </div>

                <form action="{{ route('admin.transactions.export_email') }}" method="POST" class="needs-validation">
                    @csrf
                    
                    {{-- Target Email --}}
                    <div class="mb-3">
                        <label class="form-label card-subtitle-text text-uppercase fw-bold tracking-wider mb-1" style="font-size: 10px;">Destination Email</label>
                        <input type="email" name="email_target" value="vestaclothingg@gmail.com" required
                            class="form-control px-3 py-2 text-dark" 
                            style="font-size: 13px; border-color: #e2e8f0; border-radius: 6px; background-color: #f8fafc;">
                    </div>

                    <div class="row g-3 mb-4">
                        {{-- Batas Periode Laporan --}}
                        <div class="col-6">
                            <label class="form-label card-subtitle-text text-uppercase fw-bold tracking-wider mb-1" style="font-size: 10px;">Archive Period</label>
                            <select name="period" required class="form-select text-dark" style="font-size: 13px; border-color: #e2e8f0; border-radius: 6px; background-color: #f8fafc;">
                                <option value="1">Today (Live)</option>
                                <option value="7">Past 7 Days</option>
                                <option value="30" selected>Past 30 Days</option>
                            </select>
                        </div>

                        {{-- Format Ekspor --}}
                        <div class="col-6">
                            <label class="form-label card-subtitle-text text-uppercase fw-bold tracking-wider mb-1" style="font-size: 10px;">Document Format</label>
                            <select name="format_choice" required class="form-select text-dark" style="font-size: 13px; border-color: #e2e8f0; border-radius: 6px; background-color: #f8fafc;">
                                <option value="xlsx" selected>Excel (.xlsx)</option>
                                <option value="html">HTML Table</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-dark w-full uppercase tracking-widest py-2.5 rounded shadow-sm w-100" 
                        style="font-size: 11px; font-weight: 600; background-color: #121212; border: none; letter-spacing: 0.1em;">
                        Queue & Transmit Report
                    </button>
                </form>
            </div>
            
            <div class="pt-3 border-t mt-3" style="border-color: #f1f5f9 !important;">
                <p class="mb-0 text-muted" style="font-size: 10.5px; line-height: 1.4;">
                    *Proses kompilasi file dikerjakan secara asinkronus di background server agar performa sistem tetap ringan.
                </p>
            </div>
        </div>
    </div>

    {{-- Top Selling Products --}}
    <div class="col-md-7">
        <div class="saas-card h-100">
            <div class="mb-4">
                <h6 class="card-title-text mb-0">Top Selling Products</h6>
            </div>

            <div class="row g-3">
                @forelse($topProducts as $item)
                <div class="col-6 col-md-4">
                    <div class="product-grid-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="product-img-box" style="height: 120px;">
                                <img src="{{ asset('product_image/' . ($item->product->image_path ?? 'default.jpg')) }}" 
                                     class="img-fluid" style="max-height: 100%; object-fit: contain;" 
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=V&background=f1f5f9&color=121212';">
                            </div>
                            <h6 class="fw-bold text-truncate mb-0" style="font-size: 13px; color: #121212;">{{ $item->product->name }}</h6>
                            <p class="card-subtitle-text mb-2 text-truncate" style="font-size: 10.5px;">{{ $item->product->category->name ?? 'Clothing' }}</p>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top" style="border-color: #f1f5f9 !important;">
                            <span class="text-dark fw-bold" style="font-size: 11.5px;">{{ $item->units_sold }} Sold</span>
                            <span class="badge bg-dark text-white rounded-pill" style="font-size: 9px; padding: 4px 6px;">#{{ $loop->iteration }}</span>
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function triggerExport() {
    const activePeriod = document.getElementById('periodSelect').value;
    window.location.href = `{{ route('admin.dashboard.export') }}?period=${activePeriod}`;
}

document.addEventListener('DOMContentLoaded', function () {
    const canvasElement = document.getElementById('revenueChart');
    
    if (canvasElement) {
        const ctx = canvasElement.getContext('2d');
        
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
                    borderColor: '#121212',
                    borderWidth: 2.5, 
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.45,
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