@extends('layouts.admin') {{-- Pastikan kamu punya layout admin --}}

@section('admin_content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-uppercase" style="letter-spacing: 2px;">Internal Staff Management</h2>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-dark px-4 py-2 fw-bold">
            + ADD NEW ADMIN
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined Date</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffs as $staff)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3">{{ substr($staff->name, 0, 1) }}</div>
                                    <span class="fw-bold">{{ $staff->name }}</span>
                                </div>
                            </td>
                            <td>{{ $staff->email }}</td>
                            <td>
                                <span class="badge bg-secondary text-uppercase" style="font-size: 10px;">{{ $staff->role }}</span>
                            </td>
                            <td>{{ $staff->created_at->format('d M Y') }}</td>
                            <td class="text-end pe-4">
                                {{-- Daniel bisa tambah tombol Delete atau Edit nanti --}}
                                <button class="btn btn-sm btn-outline-danger">Remove Staff</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                No admin staff found.
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
    .avatar-circle {
        width: 35px;
        height: 35px;
        background-color: #333;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .table thead th {
        font-weight: 600;
        letter-spacing: 1px;
    }
</style>
@endsection