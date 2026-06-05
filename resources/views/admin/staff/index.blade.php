@extends('layouts.admin')

@section('admin_content')
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid py-4 px-md-5">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h6 class="text-muted text-uppercase small tracking-widest mb-1">Human Resources</h6>
            <h2 class="fw-bold text-uppercase" style="letter-spacing: -1px; font-size: 2rem;">Staff Directory</h2>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-dark px-4 py-2 rounded-pill fw-bold shadow-sm">
            <i class="bi bi-person-plus-fill me-2"></i> RECRUIT STAFF
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters Row -->
    <div class="row g-2 align-items-center mb-4">
        <!-- Name Live Search Input -->
        <div class="col-md-6 col-12">
            <input type="text" id="filter-name" class="form-control filter-pill" placeholder="Live search by name...">
        </div>
        
        <!-- Position Select -->
        <div class="col-md-6 col-12">
            <select id="filter-position" class="form-select filter-pill">
                <option value="">All Positions</option>
                <option value="owner">Owner</option>
                <option value="manager">Manager</option>
                <option value="staff">Store Staff</option>
            </select>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3 border-0">Identity</th>
                            <th class="border-0">Email Address</th>
                            <th class="border-0">Position</th>
                            <th class="border-0">Joined Since</th>
                            <th class="text-end pe-4 border-0">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="staff-table-body">
                        @include('admin.staff.table_rows')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Reset & Base Action Button */
    .btn-action {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 1px solid #eee;
        background-color: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        cursor: pointer;
    }

    .btn-edit { color: #333; }
    .btn-edit:hover {
        background-color: #1a1a1a;
        color: #ffffff;
        border-color: #1a1a1a;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-delete {
        color: #dc3545;
        border-color: #f8d7da;
    }
    .btn-delete:hover {
        background-color: #dc3545;
        color: #ffffff !important;
        border-color: #dc3545;
        transform: translateY(-3px) rotate(8deg);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.25);
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #1a1a1a 0%, #333 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        border: 2px solid #fff;
    }

    /* SweetAlert2 VESTA Luxury Theme */
    .swal2-popup { border-radius: 20px !important; padding: 2rem !important; font-family: 'Inter', sans-serif !important; }
    .swal2-title { font-weight: 800 !important; letter-spacing: -1px !important; text-transform: uppercase; }
    .swal2-styled.swal2-confirm { background-color: #1a1a1a !important; border-radius: 50px !important; padding: 0.7rem 2rem !important; font-weight: 600 !important; font-size: 13px !important; text-transform: uppercase !important; letter-spacing: 1px !important; }
    .swal2-styled.swal2-cancel { background-color: #f8f9fa !important; color: #333 !important; border-radius: 50px !important; padding: 0.7rem 2rem !important; font-weight: 600 !important; font-size: 13px !important; text-transform: uppercase !important; letter-spacing: 1px !important; border: 1px solid #eee !important; }

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

<script>
    // SweetAlert2 delete confirmation binding
    function bindDeleteButtons() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            // Remove previous event listeners
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
        });
        
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const staffName = this.getAttribute('data-name');
                const formId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: 'TERMINATE ACCESS?',
                    html: `You are about to revoke all administrative privileges for <b>${staffName}</b>. This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'CONFIRM DELETE',
                    cancelButtonText: 'CANCEL',
                    reverseButtons: true,
                    focusCancel: true,
                    buttonsStyling: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + formId).submit();
                    }
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('filter-name');
        const positionSelect = document.getElementById('filter-position');
        const tableBody = document.getElementById('staff-table-body');
        
        let debounceTimer;
        
        function fetchFilteredStaff() {
            const name = nameInput.value;
            const position = positionSelect.value;
            
            tableBody.style.opacity = '0.5';
            
            const params = new URLSearchParams({
                name: name,
                position: position
            });
            
            fetch(`{{ route('admin.staff.index') }}?${params.toString()}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                tableBody.innerHTML = data.html;
                tableBody.style.opacity = '1';
                bindDeleteButtons();
            })
            .catch(error => {
                console.error('Error fetching staff list:', error);
                tableBody.style.opacity = '1';
            });
        }
        
        if (nameInput) {
            nameInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchFilteredStaff, 300);
            });
        }
        
        if (positionSelect) {
            positionSelect.addEventListener('change', fetchFilteredStaff);
        }
        
        bindDeleteButtons();
    });
</script>
@endsection