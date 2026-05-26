@extends('layouts.admin'){{-- Sesuaikan dengan nama file layout master admin kamu --}}

@section('admin_content')
<style>
    /* Custom style tambahan untuk keselarasan luxury UI */
    .table-luxury {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    .table-luxury th {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
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
    .code-badge {
        font-family: 'SF Mono', SFMono-Regular, Consolas, monospace;
        background: #f4f4f2;
        color: #1a1a1a;
        padding: 6px 12px;
        font-weight: 600;
        letter-spacing: 0.05em;
        font-size: 11px;
    }
    .btn-luxury-black {
        background: #1a1a1a;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 10px 20px;
        border-radius: 8px;
        border: 1px solid #1a1a1a;
        transition: all 0.3s ease;
    }
    .btn-luxury-black:hover {
        background: #ffffff;
        color: #1a1a1a;
    }
    .status-pill {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 4px 10px;
        border-radius: 4px;
        display: inline-block;
    }
    .status-active { background: #e6f4ea; color: #137333; }
    .status-expired { background: #fce8e6; color: #c5221f; }
</style>

<div class="container-fluid p-0">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <span class="text-uppercase text-muted tracking-widest" style="font-size: 10px; font-weight: 600; letter-spacing: 0.2em;">Operations</span>
            <h1 class="h2 fw-bold mt-1 mb-0" style="letter-spacing: -0.02em;">Voucher Promotions</h1>
        </div>
        <div>
            {{-- Tombol tambah voucher baru --}}
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-luxury-black d-flex align-items-center    p-2">
                <i class="bi bi-plus-lg" style="font-size: 1rem;"></i> Create New Voucher
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-dark alert-dismissible fade show border-0 mb-4 p-3 rounded-3" role="alert" style="background: #1a1a1a; color: #fff;">
            <span class="small tracking-wide"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
            <button type="button" class="btn-close btn-close-white small" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Data Table --}}
    <div class="table-luxury shadow-sm bg-white">
        <div class="table-responsive">
            <table class="table table-borderless mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Voucher Code</th>
                        <th>Type</th>
                        <th>Reward Value</th>
                        <th class="text-center">Usage Quota</th>
                        <th>Expiry Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if($vouchers->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-ticket-perforated d-block display-6 mb-3 text-light-gray"></i>
                                <span class="text-uppercase tracking-widest font-medium text-gray" style="font-size: 11px;">No vouchers available at the moment.</span>
                            </td>
                        </tr>
                    @else
                        @foreach($vouchers as $voucher)
                            <tr>
                                {{-- 1. Code --}}
                                <td>
                                    <span class="code-badge rounded-2">{{ $voucher->code }}</span>
                                </td>
                                
                                {{-- 2. Type --}}
                                <td class="text-uppercase font-medium text-secondary" style="font-size: 11px; font-weight: 600;">
                                    {{ $voucher->type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}
                                </td>
                                
                                {{-- 3. Value --}}
                                <td class="fw-bold">
                                    @if($voucher->type === 'percentage')
                                        {{ number_format($voucher->reward_value, 0) }}% Off
                                    @else
                                        IDR {{ number_format($voucher->reward_value, 0, ',', '.') }}
                                    </td>
                                    @endif
                                </td>
                                
                                {{-- 4. Quota Tracking --}}
                                <td class="text-center">
                                    <span class="fw-medium text-dark">{{ $voucher->used_quota }}</span>
                                    <span class="text-muted mx-1">/</span>
                                    <span class="text-muted">{{ $voucher->total_quota }}</span>
                                    <div class="progress mx-auto mt-2" style="height: 4px; width: 80px; border-radius: 2px;">
                                        @php 
                                            $percentage = ($voucher->total_quota > 0) ? ($voucher->used_quota / $voucher->total_quota) * 100 : 0;
                                        @endphp
                                        <div class="progress-bar bg-dark" role="progressbar" style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                
                                {{-- 5. Expiry --}}
                                <td class="text-secondary font-mono">
                                    {{ $voucher->expired_at ? $voucher->expired_at->format('M d, Y') : '—' }}
                                </td>
                                
                                {{-- 6. Status Badge --}}
                                <td class="text-center">
                                    @if($voucher->expired_at && $voucher->expired_at->isPast())
                                        <span class="status-pill status-expired">Expired</span>
                                    @elseif($voucher->used_quota >= $voucher->total_quota && $voucher->total_quota > 0)
                                        <span class="status-pill status-expired">Sold Out</span>
                                    @else
                                        <span class="status-pill status-active">Active</span>
                                    @endif
                                </td>
                                
                                {{-- 7. Action Controls --}}
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        {{-- Edit Button --}}
                                        <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-sm btn-outline-secondary border-0 p-2 rounded-3" title="Edit Voucher">
                                            <i class="bi bi-pencil-square" style="font-size: 1.1rem; color: var(--vesta-black);"></i>
                                        </a>

                                        {{-- Delete Form Action --}}
                                        <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this luxury promotion voucher?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-2 rounded-3" title="Delete Voucher">
                                                <i class="bi bi-trash3" style="font-size: 1.1rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Pagination Area --}}
    @if(!$vouchers->isEmpty() && method_exists($vouchers, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-4 px-2">
            <span class="text-muted small">Showing {{ $vouchers->firstItem() }} to {{ $vouchers->lastItem() }} of {{ $vouchers->total() }} promotions</span>
            <div>
                {{ $vouchers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>
@endsection