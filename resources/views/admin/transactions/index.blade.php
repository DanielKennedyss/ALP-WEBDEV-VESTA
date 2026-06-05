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

    /* Pill-Shape design for inputs and select */
    .filter-pill {
        border-radius: 9999px !important;
        background-color: #fff !important;
        border: 1px solid #dee2e6 !important;
        padding: 0.5rem 1.25rem !important;
        font-size: 12px !important;
        font-weight: 500 !important;
        color: #212529 !important;
        outline: none !important;
        box-shadow: none !important;
        height: 38px !important;
        transition: all 0.2s ease !important;
    }
    .filter-pill:focus {
        border-color: #000 !important;
        background-color: #fff !important;
    }
    select.filter-pill {
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 1rem center !important;
        background-size: 10px 10px !important;
        padding-right: 2.25rem !important;
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
        <!-- Filters Row -->
        <div class="p-4 border-bottom bg-light-subtle">
            <form id="transactions-filter-form" action="{{ route('admin.transactions.index') }}" method="GET" class="m-0">
                <div class="row g-2 align-items-center">
                    <!-- Product/Invoice Search -->
                    <div class="col-md-6 col-12">
                        <input type="text" id="filter-product" name="product" value="{{ request('product') }}" class="form-control filter-pill" placeholder="Search product name, SKU, or Invoice ID...">
                    </div>
                    
                    <!-- Status Dropdown -->
                    <div class="col-md-4 col-sm-6 col-12">
                        <select id="filter-status" name="status" class="form-select filter-pill">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <!-- Filter & Reset Buttons -->
                    <div class="col-md-2 col-sm-6 col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-dark rounded-pill px-3 fw-bold text-uppercase w-100" style="font-size: 10px; height: 38px; letter-spacing: 0.05em;">FILTER</button>
                        <a href="{{ route('admin.transactions.index') }}" id="btn-reset-filters" class="btn btn-light rounded-pill px-3 fw-bold text-uppercase border d-flex align-items-center justify-center {{ request()->anyFilled(['product', 'status']) ? '' : 'd-none' }}" style="font-size: 10px; height: 38px; min-width: 38px;" title="Reset Filters">✕</a>
                    </div>
                </div>
            </form>
        </div>

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
                <tbody id="transactions-table-body">
                    @include('admin.transactions.table_rows')
                </tbody>
            </table>
        </div>

        <!-- Pagination Wrapper -->
        <div id="transactions-pagination-container">
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

    {{-- Transaction Detail Cards Container --}}
    <div id="transaction-details-container">
        @include('admin.transactions.detail_cards')
    </div>

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

    /**
     * 4. SweetAlert2 untuk Konfirmasi Refund Pesanan (Refund Order)
     */
    function triggerRefundAlert(transactionId, invoiceNumber) {
        Swal.fire({
            title: 'Refund This Order?',
            text: `Are you sure you want to refund order ${invoiceNumber}? This will mark the order as Refunded and return any redeemed loyalty points back to the customer's ledger. This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Refund Order',
            cancelButtonText: 'Keep Active',
            customClass: {
                popup: 'vesta-swal-popup',
                confirmButton: 'btn btn-danger px-4 py-2 me-2 font-semibold text-uppercase tracking-wider rounded-3', 
                cancelButton: 'vesta-swal-cancel'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-refund-' + transactionId).submit();
            }
        });
    }

    // 5. AJAX Live Search & Status Filter Implementation
    (function() {
        const filterProduct = document.getElementById('filter-product');
        const filterStatus = document.getElementById('filter-status');
        const resetBtn = document.getElementById('btn-reset-filters');
        const filterForm = document.getElementById('transactions-filter-form');
        
        let debounceTimer;

        function fetchTransactions(url) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update table body
                const tbody = document.getElementById('transactions-table-body');
                if (tbody) tbody.innerHTML = data.html;

                // Update pagination
                const paginationContainer = document.getElementById('transactions-pagination-container');
                if (paginationContainer) paginationContainer.innerHTML = data.pagination;

                // Update detail cards
                const detailsContainer = document.getElementById('transaction-details-container');
                if (detailsContainer) detailsContainer.innerHTML = data.details;

                // Update reset button visibility
                const productVal = filterProduct.value.trim();
                const statusVal = filterStatus.value;
                if (productVal !== '' || statusVal !== '') {
                    resetBtn.classList.remove('d-none');
                } else {
                    resetBtn.classList.add('d-none');
                }
            })
            .catch(error => console.error('Error loading transaction data:', error));
        }

        function triggerSearch() {
            const productVal = encodeURIComponent(filterProduct.value.trim());
            const statusVal = encodeURIComponent(filterStatus.value);
            const baseUrl = "{{ route('admin.transactions.index') }}";
            const url = `${baseUrl}?product=${productVal}&status=${statusVal}`;
            fetchTransactions(url);
        }

        // Live Search Input event with debounce
        if (filterProduct) {
            filterProduct.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(triggerSearch, 400);
            });
        }

        // Status filter Change event
        if (filterStatus) {
            filterStatus.addEventListener('change', function() {
                triggerSearch();
            });
        }

        // Reset button Click event
        if (resetBtn) {
            resetBtn.addEventListener('click', function(e) {
                e.preventDefault();
                filterProduct.value = '';
                filterStatus.value = '';
                triggerSearch();
            });
        }

        // Form Submit interception
        if (filterForm) {
            filterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                triggerSearch();
            });
        }

        // Intercept pagination clicks (delegated)
        document.addEventListener('click', function(e) {
            const pageLink = e.target.closest('#transactions-pagination-container a');
            if (pageLink) {
                e.preventDefault();
                const url = pageLink.getAttribute('href');
                if (url) {
                    fetchTransactions(url);
                }
            }
        });
    })();
</script>
@endsection