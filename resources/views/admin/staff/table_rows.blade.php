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
    
    {{-- KOLOM ACTIONS --}}
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
