@extends('layouts.admin')

@section('admin_content')
<style>
    /* Editorial Typography Setup */
    .luxury-meta {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.25em;
        text-transform: uppercase;
        color: #8a8a85;
    }
    
    .luxury-title {
        font-family: ui-serif, Georgia, Cambria, "Times New Roman", Times, serif;
        font-weight: 400;
        letter-spacing: -0.02em;
        color: #111111;
    }

    .back-concierge-link {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #111111;
        transition: opacity 0.2s ease;
    }
    .back-concierge-link:hover {
        opacity: 0.6;
    }

    /* Asymmetric Workspace Split Layout */
    .workspace-editorial-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 48px;
        margin-top: 32px;
    }

    /* Sektor Kiri: Dokumen Keluhan Masuk */
    .inbound-document-panel {
        background: #ffffff;
        border: 1px solid #e9e9e6;
        border-radius: 16px;
        padding: 40px;
    }

    .customer-profile-badge {
        border-bottom: 1px solid #f2f2ef;
        padding-bottom: 24px;
        margin-bottom: 32px;
    }

    .meta-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: #aaaea8;
        text-transform: uppercase;
        margin-bottom: 4px;
        display: block;
    }

    .meta-value-text {
        font-size: 14px;
        font-weight: 600;
        color: #111111;
    }

    .editorial-subject-header {
        font-family: ui-serif, Georgia, serif;
        font-size: 20px;
        color: #111111;
        line-height: 1.4;
        margin-bottom: 16px;
    }

    .message-narrative-box {
        font-size: 14px;
        line-height: 1.7;
        color: #444440;
        background: #fafafa;
        padding: 24px;
        border-radius: 8px;
        border: 1px solid #f2f2ef;
        white-space: pre-line;
    }

    /* Sektor Kanan: Ruang Balasan Komparatif */
    .outbound-response-panel {
        background: #ffffff;
        border: 1px solid #e9e9e6;
        border-radius: 16px;
        padding: 40px;
        display: flex;
        flex-direction: column;
    }

    .reply-editorial-textarea {
        border: 1px solid #e9e9e6;
        border-radius: 8px;
        padding: 20px;
        font-size: 14px;
        line-height: 1.6;
        width: 100%;
        resize: none;
        background: #fafafa;
        transition: all 0.3s ease;
    }

    .reply-editorial-textarea:focus {
        background: #ffffff;
        border-color: #111111;
        outline: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .btn-vesta-dispatch {
        background: #111111;
        color: #ffffff;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        padding: 14px 28px;
        border-radius: 8px;
        border: 1px solid #111111;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-vesta-dispatch:hover {
        background: #ffffff;
        color: #111111;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    /* Status Token Minimalis */
    .luxury-status-indicator {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
    }
    .indicator-pending { background: #fdf2f2; color: #cf3c3c; }
    .indicator-archived { background: #f0fdf4; color: #2e7d43; }

    @media (max-width: 992px) {
        .workspace-editorial-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }
</style>

<div class="mb-4">
    <a href="{{ route('admin.support.index') }}" class="text-decoration-none back-concierge-link">
        <i class="bi bi-arrow-left me-2"></i> Return to Concierge
    </a>
</div>

<div class="d-flex justify-content-between align-items-end mb-2">
    <div>
        <h6 class="luxury-meta mb-1">CONCIERGE OPERATIONS</h6>
        <h1 class="luxury-title mb-0" style="font-size: 2.6rem;">Ticket Interaction</h1>
    </div>
    <div>
        @if($inquiry->status === 'PENDING')
            <span class="luxury-status-indicator indicator-pending">Awaiting Action</span>
        @else
            <span class="luxury-status-indicator indicator-archived">Archived</span>
        @endif
    </div>
</div>

<div class="workspace-editorial-grid">
    
    {{-- Sektor Kiri: Lembar Data Dokumen Pengaduan --}}
    <div class="inbound-document-panel shadow-sm">
        <div class="customer-profile-badge">
            <div class="row g-3">
                <div class="col-6">
                    <span class="meta-label">Sender Account</span>
                    <span class="meta-value-text text-capitalize">{{ $inquiry->first_name }} {{ $inquiry->last_name }}</span>
                </div>
                <div class="col-6">
                    <span class="meta-label">Routing Gateway</span>
                    <span class="meta-value-text" style="font-family: monospace; font-size: 13px;">{{ $inquiry->email }}</span>
                </div>
                <div class="col-6 mt-3">
                    <span class="meta-label">Inbound Timestamp</span>
                    <span class="text-muted small" style="font-family: monospace;">{{ $inquiry->created_at->format('Y-m-d • H:i:s') }}</span>
                </div>
                <div class="col-6 mt-3">
                    <span class="meta-label">Telecom Contact</span>
                    <span class="text-muted small" style="font-family: monospace;">{{ $inquiry->phone ?? 'None Provided' }}</span>
                </div>
            </div>
        </div>

        <div>
            <span class="meta-label">Stated Subject</span>
            <h3 class="editorial-subject-header">“{{ $inquiry->subject }}”</h3>
            
            <span class="meta-label mb-2">Narrative Body</span>
            <div class="message-narrative-box">
                {{ $inquiry->message }}
            </div>
        </div>
    </div>

    {{-- Sektor Rencana Kanan: Konsol Pemrosesan Balasan Resmi --}}
    <div class="outbound-response-panel shadow-sm">
        @if($inquiry->status === 'PENDING')
            <span class="luxury-meta mb-3" style="color: #111111;">Compose Official Dispatch</span>
            
            <form action="{{ route('admin.support.reply', $inquiry->id) }}" method="POST" class="d-flex flex-column flex-grow-1">
                @csrf
                <div class="mb-4 flex-grow-1">
                    <textarea name="reply_message" class="reply-editorial-textarea h-100" rows="10" placeholder="Write a premium, tailored response representing VESTA client standards..."></textarea>
                </div>
                <div class="text-end mt-auto">
                    <button type="submit" class="btn-vesta-dispatch px-5">
                        Dispatch Official Reply
                    </button>
                </div>
            </form>
        @else
            <div class="text-center my-auto py-4">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 48px; height: 48px; background: #f0fdf4;">
                    <i class="bi bi-patch-check text-success" style="font-size: 1.2rem;"></i>
                </div>
                <span class="luxury-meta d-block mb-3" style="color: #2e7d43;">Resolution Dispatch Logged</span>
                
                <div class="text-start border rounded-3 p-4 bg-light text-muted" style="font-size: 13.5px; line-height: 1.6; white-space: pre-line;">
                    <span class="meta-label mb-2" style="color: #111111;">Sent Response Archive:</span>
                    {{ $inquiry->reply_message }}
                </div>
                <span class="text-muted d-block mt-3 text-end" style="font-size: 11px; font-family: monospace;">
                    DISPATCH TIME: {{ $inquiry->updated_at->format('Y-m-d H:i:s') }}
                </span>
            </div>
        @endif
    </div>

</div>
@endsection