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
    <div class="admin-card shadow-sm bg-white overflow-hidden" id="main-transactions-table-card">
        <div class="table-responsive">
            <table class="table border-0 table-luxury align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width: 15%;">Order ID</th>
                        <th style="width: 25%;">Product / Collection</th>
                        <th style="width: 15%;">Total Price</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 10%;" class="text-center">Details</th>
                        <th style="width: 20%;" class="text-end">Action Logistics</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    @php
                        // Map order status to tracker category group for filtering
                        $statusGroup = 'other';
                        if ($trx->status == 'pending') {
                            $statusGroup = 'pending';
                        } elseif (in_array($trx->status, ['success', 'processing', 'settlement', 'paid'])) {
                            $statusGroup = 'packed';
                        } elseif ($trx->status == 'shipped') {
                            $statusGroup = 'shipped';
                        } elseif (in_array($trx->status, ['delivered', 'completed'])) {
                            $statusGroup = 'rate';
                        }
                    @endphp
                    <tr class="order-row" data-status-group="{{ $statusGroup }}">
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
                        
                        {{-- 5. Details button --}}
                        <td class="text-center">
                            <button type="button" onclick="showTransactionDetails({{ $trx->id }})" class="btn btn-outline-dark btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; transition: all 0.2s;" title="View Details">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>

                        {{-- 6. Hybrid Action Controls --}}
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
                        <td colspan="6" class="text-center py-5 text-muted">
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

    {{-- Transaction Detail Cards --}}
    @foreach($transactions as $trx)
        <div class="admin-card shadow-sm bg-white p-5 d-none transaction-details-card mb-5" id="trxDetailCard-{{ $trx->id }}">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div>
                    <span class="text-uppercase text-muted tracking-widest" style="font-size: 10px; font-weight: 600; letter-spacing: 0.15em;">Transaction Details</span>
                    <h3 class="h4 fw-bold mt-1 text-dark mb-0">
                        Invoice {{ $trx->invoice_number ?? '#TRX-'.$trx->id }}
                    </h3>
                </div>
                <button type="button" onclick="hideTransactionDetails({{ $trx->id }})" class="btn btn-outline-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Back to List">
                    <i class="bi bi-arrow-left"></i>
                </button>
            </div>
            
            <div class="row g-4 text-start">
                <!-- Customer info card -->
                <div class="col-md-6">
                    <div class="bg-light p-4 rounded-3 border border-light h-100">
                        <h6 class="text-uppercase text-muted tracking-wide mb-3" style="font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">Customer Information</h6>
                        <p class="mb-1 text-dark fw-bold" style="font-size: 15px;">{{ $trx->customer_name }}</p>
                        <p class="mb-3 text-muted" style="font-size: 13px;">{{ $trx->customer_email }}</p>
                        
                        @if($trx->user)
                            <div class="d-inline-block bg-black text-white px-3 py-1 text-[10px] tracking-wider uppercase font-bold rounded-1" style="font-size: 10px; letter-spacing: 0.08em;">
                                Status: {{ $trx->user->membership_tier_badge }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Order Metadata card -->
                <div class="col-md-6">
                    <div class="bg-light p-4 rounded-3 border border-light h-100">
                        <h6 class="text-uppercase text-muted tracking-wide mb-3" style="font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">Order Metadata</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Order Date:</span>
                            <span class="fw-semibold small text-dark">{{ $trx->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Payment URL / Token:</span>
                            <span class="font-monospace text-truncate ms-3 small text-dark" style="max-width: 250px;">{{ $trx->payment_url ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Paid At:</span>
                            <span class="fw-semibold small text-dark">{{ $trx->paid_at ? $trx->paid_at->format('M d, Y H:i') : 'Unpaid' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Items breakdown -->
                <div class="col-12">
                    <div class="bg-white p-4 rounded-3 border border-light shadow-sm">
                        <h6 class="text-uppercase text-muted tracking-wide mb-3" style="font-size: 11px; font-weight: 700; letter-spacing: 0.05em;">Items Purchased</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="border-bottom border-light">
                                        <th class="text-muted small py-2 px-0">Product Details</th>
                                        <th class="text-muted small py-2 text-center">Size</th>
                                        <th class="text-muted small py-2 text-center" style="width: 10%;">Qty</th>
                                        <th class="text-muted small py-2 text-end" style="width: 25%;">Price</th>
                                        <th class="text-muted small py-2 text-end" style="width: 25%;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($trx->cart_items && is_array($trx->cart_items) && count($trx->cart_items) > 0)
                                        @foreach($trx->cart_items as $item)
                                            <tr class="border-bottom border-light-subtle">
                                                <td class="py-3 px-0">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded overflow-hidden me-3" style="width: 44px; height: 56px;">
                                                            <img src="{{ $item['image_path'] ?? asset('product_image/default.jpg') }}" 
                                                                 class="w-100 h-100 object-fit-cover" 
                                                                 onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($item['name'] ?? 'NA') }}&background=1a1a1a&color=fff';">
                                                        </div>
                                                        <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $item['name'] }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-center py-3">
                                                    <span class="badge bg-secondary-subtle text-secondary-emphasis text-uppercase" style="font-size: 10px;">{{ $item['size'] ?? '—' }}</span>
                                                </td>
                                                <td class="text-center py-3 fw-medium text-dark">{{ $item['quantity'] }}</td>
                                                <td class="text-end py-3 text-muted">IDR {{ number_format($item['price'], 0, ',', '.') }}</td>
                                                <td class="text-end py-3 fw-bold text-dark">IDR {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="border-bottom border-light-subtle">
                                            <td class="py-3 px-0">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light rounded overflow-hidden me-3" style="width: 44px; height: 56px;">
                                                        <img src="{{ asset('product_image/' . ($trx->product->image_path ?? 'default.jpg')) }}" 
                                                             class="w-100 h-100 object-fit-cover"
                                                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($trx->product->name ?? 'NA') }}&background=1a1a1a&color=fff';">
                                                    </div>
                                                    <span class="fw-semibold text-dark" style="font-size: 13px;">{{ $trx->product->name ?? 'Product Deleted' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis" style="font-size: 10px;">—</span>
                                            </td>
                                            <td class="text-center py-3 fw-medium text-dark">{{ $trx->quantity }}</td>
                                            <td class="text-end py-3 text-muted">IDR {{ number_format(($trx->product->price ?? 0), 0, ',', '.') }}</td>
                                            <td class="text-end py-3 fw-bold text-dark">IDR {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Cost summary breakdown -->
                <div class="col-12">
                    <div class="bg-light p-4 rounded-3 border border-light">
                        <div class="row justify-content-end">
                            <div class="col-md-6 col-lg-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Subtotal:</span>
                                    <span class="text-dark small">IDR {{ number_format($trx->subtotal ?? $trx->total_price, 0, ',', '.') }}</span>
                                </div>
                                
                                @if(($trx->discount_points ?? 0) > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span class="small">Points Discount ({{ number_format($trx->points_redeemed ?? 0) }} PTS):</span>
                                        <span class="small">-IDR {{ number_format($trx->discount_points, 0, ',', '.') }}</span>
                                    </div>
                                
                                @endif
                                @if(($trx->discount_voucher ?? 0) > 0)
                                    <div class="d-flex justify-content-between mb-2 text-success">
                                        <span class="small">Voucher Discount:</span>
                                        <span class="small">-IDR {{ number_format($trx->discount_voucher, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                
                                <hr class="my-2 border-light-subtle">
                                
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-dark" style="font-size: 15px;">Total Price:</span>
                                    <span class="fw-bold text-dark" style="font-size: 15px;">IDR {{ number_format($trx->total_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end mt-4">
                <button type="button" onclick="hideTransactionDetails({{ $trx->id }})" class="btn btn-dark text-uppercase tracking-wider font-semibold text-white px-5 py-3" style="font-size: 11px; border-radius: 8px;">
                    Close Details
                </button>
            </div>
        </div>
    @endforeach

</div> {{-- End of container-fluid --}}

<script>
    // 1. Snappy Inline Transaction Detail Toggle
    function showTransactionDetails(trxId) {
        // Hide the main table card
        document.getElementById('main-transactions-table-card').classList.add('d-none');
        
        // Hide any open detail cards just in case
        document.querySelectorAll('.transaction-details-card').forEach(card => {
            card.classList.add('d-none');
        });
        
        // Show the targeted detail card
        document.getElementById('trxDetailCard-' + trxId).classList.remove('d-none');
        
        // Smooth scroll to top of workspace content
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function hideTransactionDetails(trxId) {
        // Hide the detail card
        document.getElementById('trxDetailCard-' + trxId).classList.add('d-none');
        
        // Show the main table card
        document.getElementById('main-transactions-table-card').classList.remove('d-none');
    }

    /**
     * 2. SweetAlert2 untuk Konfirmasi Perubahan Status Logistik
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
     * 3. SweetAlert2 untuk Proteksi Destruktif Pembatalan Pesanan (Cancel Order)
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