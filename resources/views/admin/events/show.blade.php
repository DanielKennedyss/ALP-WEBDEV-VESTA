@extends('layouts.admin')

@section('admin_content')
<style>
    .detail-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.04);
        padding: 40px;
    }
    .detail-label {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #7a7a7a;
        margin-bottom: 6px;
        display: block;
    }
    .detail-value {
        font-size: 14px;
        font-weight: 500;
        color: #1a1a1a;
    }
    .color-chip {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 6px 14px 6px 6px;
        background: #fafafa;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }
    .color-dot {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: 1px solid rgba(0,0,0,0.08);
    }
    .readonly-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f3f4;
        color: #5f6368;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 5px 12px;
        border-radius: 6px;
    }

    /* Product list */
    .product-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 0;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .product-row:last-child { border-bottom: none; }
    .product-thumb {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        object-fit: cover;
        background: #f4f4f2;
        flex-shrink: 0;
    }
    .product-info { flex: 1; min-width: 0; }
    .product-name {
        font-size: 13px;
        font-weight: 600;
        color: #1a1a1a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .product-cat {
        font-size: 11px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .btn-back {
        background: #fff; color: #7a7a7a;
        font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.07em;
        padding: 10px 22px; border-radius: 10px;
        border: 1px solid #d1d5db; transition: all 0.25s;
        text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-back:hover { background: #f4f4f2; color: #1a1a1a; }
</style>

<div class="container-fluid p-0">

    {{-- Breadcrumb --}}
    <div class="mb-5">
        <a href="{{ route('admin.events.index') }}"
           class="text-decoration-none text-muted small d-inline-flex align-items-center gap-2 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Collections
        </a>
        <div class="d-flex align-items-center gap-3 mt-2">
            <div>
                <span class="text-uppercase text-muted d-block" style="font-size: 10px; font-weight: 600; letter-spacing: 0.2em;">Operations</span>
                <h1 class="h2 fw-bold mt-1 mb-0" style="letter-spacing: -0.02em;">Collection Details</h1>
            </div>
            <span class="readonly-badge ms-2">
                <i class="bi bi-lock"></i> Read-only — Event Ended
            </span>
        </div>
    </div>

    {{-- ── EVENT INFO CARD ── --}}
    <div class="detail-card shadow-sm mb-4">
        <div class="d-flex align-items-start justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="letter-spacing: -0.01em;">{{ $event->name }}</h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;">
                    {{ $event->short_name }}
                </span>
            </div>
            {{-- Live theme preview --}}
            <div class="d-flex align-items-center gap-2 p-3 rounded-3"
                 style="background: {{ $event->theme_color }}; color: {{ $event->text_color }}; font-size: 11px; font-weight: 700; letter-spacing: 0.1em;">
                PREVIEW
            </div>
        </div>

        <hr style="border-color: rgba(0,0,0,0.05); margin: 0 0 28px;">

        <div class="row g-4">
            {{-- Start Date --}}
            <div class="col-md-3">
                <span class="detail-label">Start Date</span>
                <div class="detail-value">{{ $event->start_date->format('M d, Y') }}</div>
                <div class="text-muted" style="font-size: 11px;">{{ $event->start_date->format('H:i') }}</div>
            </div>

            {{-- End Date --}}
            <div class="col-md-3">
                <span class="detail-label">End Date</span>
                <div class="detail-value">{{ $event->end_date->format('M d, Y') }}</div>
                <div class="text-muted" style="font-size: 11px;">{{ $event->end_date->format('H:i') }}</div>
            </div>

            {{-- Duration --}}
            <div class="col-md-3">
                <span class="detail-label">Duration</span>
                <div class="detail-value">
                    {{ $event->start_date->copy()->startOfDay()->diffInDays($event->end_date->copy()->startOfDay()) + 1 }} days
                </div>
                <div class="text-muted" style="font-size: 11px;">
                    Ended {{ $event->end_date->diffForHumans() }}
                </div>
            </div>

            {{-- Product Count --}}
            <div class="col-md-3">
                <span class="detail-label">Assigned Products</span>
                <div class="detail-value">{{ $event->products->count() }}</div>
            </div>

            {{-- Theme Color --}}
            <div class="col-md-4">
                <span class="detail-label">Theme Background</span>
                <div class="color-chip">
                    <div class="color-dot" style="background: {{ $event->theme_color }};"></div>
                    <span style="font-family: monospace; font-size: 13px; font-weight: 600;">{{ $event->theme_color }}</span>
                </div>
            </div>

            {{-- Text Color --}}
            <div class="col-md-4">
                <span class="detail-label">Text / Accent Color</span>
                <div class="color-chip">
                    <div class="color-dot" style="background: {{ $event->text_color }};"></div>
                    <span style="font-family: monospace; font-size: 13px; font-weight: 600;">{{ $event->text_color }}</span>
                </div>
            </div>

            {{-- Banner URL --}}
            @if($event->banner_image)
            <div class="col-12">
                <span class="detail-label">Banner Image</span>
                <div class="detail-value" style="font-size: 12px; word-break: break-all; color: #5f6368;">
                    {{ $event->banner_image }}
                </div>
                <img src="{{ $event->banner_image }}" alt="{{ $event->name }}"
                     class="mt-3 rounded-3" style="max-height: 180px; object-fit: cover; width: 100%;"
                     onerror="this.style.display='none'">
            </div>
            @endif
        </div>
    </div>

    {{-- ── ASSIGNED PRODUCTS CARD ── --}}
    <div class="detail-card shadow-sm mb-5">
        <h5 class="fw-bold mb-1" style="letter-spacing: -0.01em;">Assigned Products</h5>
        <p class="text-muted small mb-4">
            {{ $event->products->count() }} {{ Str::plural('product', $event->products->count()) }} were part of this collection.
        </p>

        @if($event->products->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="bi bi-bag-x d-block mb-2" style="font-size: 2rem; opacity: 0.25;"></i>
                <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.1em;">No products were assigned to this collection.</span>
            </div>
        @else
            <div>
                @foreach($event->products as $product)
                    <div class="product-row">
                        <img src="{{ $product->image_path }}" alt="{{ $product->name }}"
                             class="product-thumb"
                             onerror="this.src=''; this.style.background='#e5e7eb';">
                        <div class="product-info">
                            <div class="product-name">{{ $product->name }}</div>
                            <div class="product-cat">{{ $product->category->name ?? '—' }}</div>
                        </div>
                        <div style="font-size: 13px; font-weight: 600; color: #1a1a1a; white-space: nowrap;">
                            IDR {{ number_format($product->price, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Footer Action --}}
    <div class="d-flex justify-content-start">
        <a href="{{ route('admin.events.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Back to Collections
        </a>
    </div>

</div>
@endsection
