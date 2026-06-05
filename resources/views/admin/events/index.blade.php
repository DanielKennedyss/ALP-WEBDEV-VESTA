@extends('layouts.admin')

@section('admin_content')
<style>
    .table-luxury {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.04);
    }
    .table-luxury th {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #7a7a7a;
        padding: 20px 24px;
        background: #fafafa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    .table-luxury td {
        padding: 18px 24px;
        font-size: 13px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }
    .table-luxury tbody tr:last-child td { border-bottom: none; }
    .table-luxury tbody tr:hover { background: #fafafa; }

    .btn-luxury-black {
        background: #1a1a1a;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        padding: 10px 22px;
        border-radius: 10px;
        border: 1px solid #1a1a1a;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-luxury-black:hover { background: #fff; color: #1a1a1a; }

    .status-pill {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        padding: 4px 12px;
        border-radius: 4px;
        display: inline-block;
    }
    .status-active   { background: #e6f4ea; color: #137333; }
    .status-upcoming { background: #e8f0fe; color: #1a73e8; }
    .status-ended    { background: #f1f3f4; color: #5f6368; }

    .color-swatch {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        border: 2px solid rgba(0,0,0,0.08);
        display: inline-block;
        vertical-align: middle;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }

    .product-count-pill {
        background: #f4f4f2;
        color: #1a1a1a;
        font-weight: 700;
        font-size: 11px;
        padding: 4px 12px;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.06);
        display: inline-block;
    }

    .action-btn {
        border: none;
        background: transparent;
        padding: 7px 10px;
        border-radius: 8px;
        transition: background 0.2s;
        cursor: pointer;
    }
    .action-btn:hover { background: #f4f4f2; }
    .action-btn.danger:hover { background: #fce8e6; }
    .action-btn.view:hover  { background: #e8f0fe; }
</style>

<div class="container-fluid p-0">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <span class="text-uppercase text-muted" style="font-size: 10px; font-weight: 600; letter-spacing: 0.2em;">Operations</span>
            <h1 class="h2 fw-bold mt-1 mb-1" style="letter-spacing: -0.02em;">Event Collections</h1>
            <p class="text-muted small mb-0">Manage promotional campaigns and assign products to each collection.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="btn-luxury-black">
            <i class="bi bi-plus-lg"></i> Create New Collection
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert border-0 mb-4 p-3 rounded-3 d-flex align-items-center gap-2"
             role="alert" style="background: #1a1a1a; color: #fff; font-size: 13px;">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-white ms-auto small" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 mb-4 p-3 rounded-3" role="alert" style="font-size: 13px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close ms-auto small" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Data Table --}}
    <div class="table-luxury shadow-sm">
        <div class="table-responsive">
            <table class="table table-borderless mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Collection Name</th>
                        <th>Theme</th>
                        <th>Duration</th>
                        <th class="text-center">Products</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        @php
                            $now = now();
                            if ($now->lt($event->start_date)) {
                                $statusClass = 'status-upcoming';
                                $statusLabel = 'Upcoming';
                            } elseif ($now->between($event->start_date, $event->end_date)) {
                                $statusClass = 'status-active';
                                $statusLabel = 'Active';
                            } else {
                                $statusClass = 'status-ended';
                                $statusLabel = 'Ended';
                            }
                        @endphp
                        <tr>
                            {{-- Name + Short Name --}}
                            <td>
                                <div class="fw-semibold" style="font-size: 13px; letter-spacing: 0.01em;">{{ $event->name }}</div>
                                <div class="text-muted mt-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em;">
                                    {{ $event->short_name }}
                                </div>
                            </td>

                            {{-- Theme Color Swatches --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="color-swatch" style="background: {{ $event->theme_color }};" title="Theme: {{ $event->theme_color }}"></span>
                                    <span class="color-swatch" style="background: {{ $event->text_color }};" title="Text: {{ $event->text_color }}"></span>
                                </div>
                            </td>

                            {{-- Duration --}}
                            <td>
                                <div style="font-size: 12px;">
                                    <span class="fw-medium">{{ $event->start_date->format('M d, Y') }}</span>
                                    <span class="text-muted mx-1">→</span>
                                    <span class="fw-medium">{{ $event->end_date->format('M d, Y') }}</span>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 11px;">
                                    {{ $event->start_date->copy()->startOfDay()->diffInDays($event->end_date->copy()->startOfDay()) + 1 }} days
                                </div>
                            </td>

                            {{-- Product Count --}}
                            <td class="text-center">
                                <span class="product-count-pill">
                                    <i class="bi bi-bag me-1" style="font-size: 10px;"></i>
                                    {{ $event->products_count }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="text-center">
                                <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>

                            {{-- Actions: Read-write for Active/Upcoming | Read-only for Ended --}}
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if($statusLabel === 'Ended')
                                        {{-- Ended: View-only button --}}
                                        <a href="{{ route('admin.events.show', $event->id) }}"
                                           class="action-btn view" title="View Collection Details">
                                            <i class="bi bi-eye" style="font-size: 1.05rem; color: #1a73e8;"></i>
                                        </a>
                                    @else
                                        {{-- Active / Upcoming: full edit + delete --}}
                                        <a href="{{ route('admin.events.edit', $event->id) }}"
                                           class="action-btn" title="Edit Collection">
                                            <i class="bi bi-pencil-square" style="font-size: 1.05rem; color: #1a1a1a;"></i>
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                                              onsubmit="return confirm('Permanently delete the collection \'{{ addslashes($event->name) }}\'? This will remove all product assignments.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn danger" title="Delete Collection">
                                                <i class="bi bi-trash3" style="font-size: 1.05rem; color: #c5221f;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x d-block mb-3" style="font-size: 2.5rem; opacity: 0.25;"></i>
                                <span style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.12em;">
                                    No collections found. Create your first event campaign.
                                </span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($events->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4 px-1">
            <span class="text-muted small">
                Showing {{ $events->firstItem() }} – {{ $events->lastItem() }} of {{ $events->total() }} collections
            </span>
            {{ $events->links('pagination::bootstrap-5') }}
        </div>
    @endif

</div>
@endsection
