@extends('layouts.admin')

@section('admin_content')
<style>
    .luxury-title {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        font-weight: 300;
        letter-spacing: 0.05em;
        color: #121212;
    }
    .luxury-meta {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: #888;
    }
    .ticket-container {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.04);
        border-radius: 12px;
        padding: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .ticket-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
    }
    .status-badge {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 4px 10px;
        border-radius: 4px;
    }
    .btn-luxury-outline {
        border: 1px solid #121212;
        background: transparent;
        color: #121212;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .btn-luxury-outline:hover {
        background: #121212;
        color: #fff;
    }
</style>

<div class="mb-5">
    <h6 class="luxury-meta mb-1">CUSTOMER CARE</h6>
    <h1 class="luxury-title" style="font-size: 2.2rem;">INBOX INQUIRIES</h1>
</div>

<div class="row g-4">
    @forelse($inquiries as $inquiry)
        <div class="col-12">
            <div class="ticket-container shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 16px;">{{ $inquiry->first_name }} {{ $inquiry->last_name }}</h5>
                        <small class="text-muted" style="font-family: monospace;">{{ $inquiry->email }}</small>
                    </div>
                    <div>
                        @if($inquiry->status === 'PENDING')
                            <span class="status-badge bg-danger bg-opacity-10 text-danger text-uppercase">PENDING</span>
                        @else
                            <span class="status-badge bg-success bg-opacity-10 text-success text-uppercase">RESOLVED</span>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-end pt-2 border-top" style="border-color: #f8fafc !important;">
                    <div class="overflow-hidden me-3">
                        <h6 class="text-uppercase fw-bold text-secondary mb-1" style="font-size: 11px; letter-spacing: 0.05em;">SUBJECT: {{ $inquiry->subject }}</h6>
                        <p class="text-muted small text-truncate mb-0" style="max-width: 600px;">{{ $inquiry->message }}</p>
                    </div>
                    <a href="{{ route('admin.support.show', $inquiry->id) }}" class="btn btn-dark btn-luxury-outline px-3 py-2">
                        View & Reply
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-3 border border-dashed">
                <i class="bi bi-chat-left-dots text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-3 mb-0">No customer inquiries found at the moment.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection