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
                    <tbody>
                        @forelse($admins as $staff)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3 shadow-sm">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark">{{ $staff->name }}</span>
                                        @if($staff->role == 'owner')
                                            <small class="text-primary fw-bold" style="font-size: 10px;">Primary Account</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-muted">{{ $staff->email }}</td>
                            <td>
                                @if($staff->role == 'owner')
                                    <span class="badge bg-dark rounded-pill px-3 py-2 text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Owner</span>
                                @elseif($staff->role == 'manager')
                                    <span class="badge bg-secondary rounded-pill px-3 py-2 text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Manager</span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-pill px-3 py-2 text-uppercase" style="font-size: 9px; letter-spacing: 0.5px;">Store Staff</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $staff->created_at->format('d M Y') }}</td>
                            
                            {{-- KOLOM ACTIONS: DIREVISI AGAR RATA SEMPURNA --}}
                            <td class="pe-4">
                                <div class="d-flex justify-content-end align-items-center">
                                    {{-- Wrapper dengan lebar tetap agar tidak geser --}}
                                    <div class="d-flex align-items-center justify-content-end gap-2" style="min-width: 100px;">
                                        
                                        @if($staff->role !== 'owner')
                                            {{-- Tombol Edit --}}
                                            <a href="{{ route('admin.staff.edit', $staff->id) }}" 
                                               class="btn-action btn-edit" 
                                               title="Edit Staff Details">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>

                                            {{-- Tombol Delete atau Label YOU --}}
                                            @if($staff->id !== auth()->id())
                                                <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" id="delete-form-{{ $staff->id }}" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" 
                                                            class="btn-action btn-delete delete-btn" 
                                                            data-name="{{ $staff->name }}" 
                                                            data-id="{{ $staff->id }}"
                                                            title="Terminate Access">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Ikon Pengganti untuk Diri Sendiri agar Perataan Tetap Konsisten --}}
                                                <div class="text-center" style="width: 38px; opacity: 0.5;">
                                                    <i class="bi bi-person-check-fill d-block" style="font-size: 14px;"></i>
                                                    <span style="font-size: 8px; font-weight: 800; text-transform: uppercase; display: block; margin-top: -2px;">YOU</span>
                                                </div>
                                            @endif

                                        @else
                                            {{-- Tampilan untuk Owner: Protected --}}
                                            <div class="text-end" style="opacity: 0.5;">
                                                <i class="bi bi-shield-lock-fill small me-1"></i>
                                                <span style="font-size: 10px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">PROTECTED</span>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-1 mb-3 d-block opacity-25"></i>
                                No administrative staff found in the directory.
                            </td>
                        </tr>
                        @endforelse
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
</style>

<script>
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
</script>
@endsection