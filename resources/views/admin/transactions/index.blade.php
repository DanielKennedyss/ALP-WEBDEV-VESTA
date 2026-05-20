@extends('layouts.admin')

@section('admin_content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Custom Styling untuk Keselarasan Luxury UI */
    .admin-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
    .table-luxury th {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #7a7a7a;
        padding: 20px 16px;
        background: #fafafa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    .table-luxury td {
        padding: 18px 16px;
        font-size: 13px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    }
    
    /* FIX: Melebarkan dropdown dan menambah padding kanan agar teks tidak menabrak panah */
    .select-luxury-sm {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 8px 36px 8px 16px; /* Ditambah padding-right agar panah aman */
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #1a1a1a;
        cursor: pointer;
        min-width: 160px; /* Memastikan lebar minimal aman untuk kata PROCESSING */
        transition: all 0.2s ease;
    }
    .select-luxury-sm:focus {
        border-color: #1a1a1a;
        box-shadow: none;
    }
    
    .btn-cancel-luxury {
        background: #fff5f5;
        color: #dc3545;
        border: 1px solid #fecdd3;
        font-size: 11px;
        padding: 8px 12px; /* Disamakan tingginya dengan select */
        border-radius: 8px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 33px;
    }
    .btn-cancel-luxury:hover {
        background: #dc3545;
        color: #ffffff;
        border-color: #dc3545;
    }

    /* Kustomisasi SweetAlert2 agar senada dengan Luxury Hitam Putih VESTA */
    .vesta-swal-popup {
        border-radius: 16px !important;
        font-family: 'Inter', sans-serif !important;
    }
    .vesta-swal-confirm {
        background-color: #1a1a1a !important;
        color: #fff !important;
        padding: 10px 24px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        border-radius: 8px !important;
    }
    .vesta-swal-cancel {
        background-color: #fff !important;
        color: #7a7a7a !important;
        border: 1px solid #d1d5db !important;
        padding: 10px 24px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        border-radius: 8px !important;
    }
</style>

<div class="container-fluid p-0">
    {{-- Header Section --}}
    <div class="mb-5">
        <span class="text-uppercase text-muted tracking-widest" style="font-size: 10px; font-weight: 600; letter-spacing: 0.2em;">Management</span>
        <h1 class="h2 fw-bold mt-1 mb-0" style="letter-spacing: -0.02em;">Order Management</h1>
    </div>

    {{-- Alert Global Notification System --}}
    @if(session('success'))
        <div class="alert alert-dark alert-dismissible fade show border-0 mb-4 p-3 rounded-3" role="alert" style="background: #1a1a1a; color: #fff;">
            <span class="small tracking-wide"><i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Data Table Card --}}
    <div class="admin-card shadow-sm bg-white overflow-hidden">
        <div class="table-responsive">
            <table class="table border-0 table-luxury align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 18%;">Order ID</th>
                        <th style="width: 32%;">Product / Collection</th>
                        <th style="width: 15%;">Total Price</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 20%;" class="text-end">Action Logistics</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        {{-- 1. Invoice Number --}}
                        <td class="fw-bold font-mono text-dark" style="font-size: 12px;">
                            {{ $trx->invoice_number ?? '#TRX-'.$trx->id }}
                        </td>
                        
                        {{-- 2. Product Information --}}
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-3" style="width: 44px; height: 55px; margin-right: 14px; overflow: hidden; border: 1px solid rgba(0,0,0,0.03);">
                                    <img src="{{ asset('product_image/' . ($trx->product->image_path ?? 'default.jpg')) }}" 
                                         class="w-100 h-100 object-fit-cover" 
                                         onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($trx->product->name ?? 'NA') }}&background=1a1a1a&color=fff';">
                                </div>
                                <div class="overflow-hidden">
                                    <p class="mb-0 fw-semibold text-dark text-truncate" style="font-size: 13px;">
                                        {{ $trx->product->name ?? 'Product Deleted' }}
                                    </p>
                                    <p class="text-muted mb-0 uppercase tracking-wider" style="font-size: 10px; font-weight: 500;">
                                        @if(isset($trx->product->category) && is_object($trx->product->category))
                                            {{ $trx->product->category->name ?? 'Uncategorized' }}
                                        @else
                                            {{ $trx->product->category ?? 'Uncategorized' }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        
                        {{-- 3. Total Price --}}
                        <td class="fw-medium text-dark">
                            IDR {{ number_format($trx->total_price, 0, ',', '.') }}
                        </td>
                        
                        {{-- 4. Dynamic Badges Status --}}
                        <td>
                            @php
                                $statusColor = match(strtolower($trx->status)) {
                                    'pending'    => 'bg-warning-subtle text-warning border border-warning-subtle',
                                    'processing' => 'bg-info-subtle text-info border border-info-subtle',
                                    'shipped'    => 'bg-primary-subtle text-primary border border-primary-subtle',
                                    'delivered'  => 'bg-success-subtle text-success border border-success-subtle',
                                    'cancelled', 'failed' => 'bg-danger-subtle text-danger border border-danger-subtle',
                                    'expired'    => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                    default      => 'bg-secondary-subtle text-secondary'
                                };
                            @endphp
                            <span class="badge rounded-pill {{ $statusColor }} uppercase font-bold tracking-widest" style="font-size: 9px; padding: 6px 12px;">
                                {{ $trx->status }}
                            </span>
                        </td>
                        
                        {{-- 5. Hybrid Action Controls --}}
                        <td class="text-end">
                            @if(in_array(strtolower($trx->status), ['cancelled', 'expired', 'failed', 'delivered']))
                                <span class="text-muted small fst-italic" style="font-size: 11px;">No actions available</span>
                            @else
                                <div class="d-inline-flex align-items-center gap-2">
                                    
                                    {{-- Dropdown Logistik Maju (Interseptasi via JS SweetAlert) --}}
                                    <form id="form-logistics-{{ $trx->id }}" action="{{ route('admin.transactions.updateStatus', $trx->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <select name="status" data-old-value="{{ strtolower($trx->status) }}" onchange="triggerLogisticsAlert(this, '{{ $trx->invoice_number }}')" class="form-select form-select-sm select-luxury-sm shadow-sm">
                                            
                                            {{-- SAFEGUARD OPTION: Mencegah kotak blank jika data DB berisi status di luar opsi logistik utama --}}
                                            @if(!in_array(strtolower($trx->status), ['pending', 'processing', 'shipped', 'delivered']))
                                                <option value="{{ strtolower($trx->status) }}" selected disabled>{{ ucfirst($trx->status) }}</option>
                                            @endif

                                            <option value="pending" {{ $trx->status === 'pending' ? 'selected' : '' }} disabled>Pending</option>
                                            <option value="processing" {{ $trx->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $trx->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered" {{ $trx->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        </select>
                                    </form>

                                    {{-- Tombol Cancel Terpisah (Interseptasi via JS SweetAlert) --}}
                                    <form id="form-cancel-{{ $trx->id }}" action="{{ route('admin.transactions.updateStatus', $trx->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="button" onclick="triggerCancelAlert('{{ $trx->id }}', '{{ $trx->invoice_number }}')" class="btn btn-cancel-luxury shadow-sm" title="Cancel Order">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>

                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt d-block display-6 mb-3 text-secondary"></i>
                            <span class="text-uppercase tracking-widest font-medium" style="font-size: 11px;">No transactions recorded in system.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Luxury Pagination Layout --}}
        @if($transactions->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-0 p-4 border-top bg-light-subtle">
            <span class="text-muted small">Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} ledger entries</span>
            <div>
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    /**
     * 1. SweetAlert2 untuk Konfirmasi Perubahan Status Logistik
     */
    function triggerLogisticsAlert(selectElement, invoiceNumber) {
        const newValue = selectElement.value.toUpperCase();
        const oldValue = selectElement.getAttribute('data-old-value');

        Swal.fire({
            title: 'Update Logistics?',
            text: `Are you sure you want to change the status of ${invoiceNumber} to ${newValue}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Update Status',
            cancelButtonText: 'Discard',
            customClass: {
                popup: 'vesta-swal-popup',
                confirmButton: 'vesta-swal-confirm',
                cancelButton: 'vesta-swal-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                selectElement.form.submit();
            } else {
                // Kembalikan seleksi dropdown ke nilai semula jika batal (mencegah teks menghilang/blank)
                selectElement.value = oldValue;
            }
        });
    }

    /**
     * 2. SweetAlert2 untuk Proteksi Destruktif Pembatalan Pesanan (Cancel Order)
     */
    function triggerCancelAlert(transactionId, invoiceNumber) {
        Swal.fire({
            title: 'Cancel This Order?',
            text: `CRITICAL WARNING: Cancelling order ${invoiceNumber} will automatically void this invoice and fully refund any redeemed loyalty points back to the customer's ledger. This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Void Invoice',
            cancelButtonText: 'Keep Active',
            customClass: {
                popup: 'vesta-swal-popup',
                confirmButton: 'btn btn-danger px-4 py-2 me-2 font-semibold text-uppercase tracking-wider rounded-3', 
                cancelButton: 'vesta-swal-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-cancel-' + transactionId).submit();
            }
        });
    }
</script>
@endsection