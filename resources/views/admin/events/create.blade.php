@extends('layouts.admin')

@section('admin_content')
<style>
    .form-luxury-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.04);
        padding: 40px;
    }
    .form-label-luxury {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #7a7a7a;
        margin-bottom: 8px;
        display: block;
    }
    .form-control-luxury,
    .form-select-luxury {
        border: 1px solid #d1d5db;
        padding: 12px 16px;
        font-size: 13px;
        border-radius: 10px;
        color: #1a1a1a;
        width: 100%;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .form-control-luxury:focus,
    .form-select-luxury:focus {
        border-color: #1a1a1a;
        box-shadow: 0 0 0 3px rgba(0,0,0,0.04);
    }
    .form-control-luxury.is-invalid { border-color: #dc3545; }
    .invalid-feedback { font-size: 11px; color: #dc3545; margin-top: 5px; }

    .btn-luxury-black {
        background: #1a1a1a; color: #fff;
        font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.07em;
        padding: 12px 26px; border-radius: 10px;
        border: 1px solid #1a1a1a; transition: all 0.3s ease;
        cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-luxury-black:hover { background: #fff; color: #1a1a1a; }

    .btn-luxury-secondary {
        background: #fff; color: #7a7a7a;
        font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.07em;
        padding: 12px 26px; border-radius: 10px;
        border: 1px solid #d1d5db; transition: all 0.3s ease;
        text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
    }
    .btn-luxury-secondary:hover { background: #f4f4f2; color: #1a1a1a; }

    /* ─── Dual Listbox ─── */
    .dual-listbox-wrapper {
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        gap: 16px;
        align-items: center;
    }
    .listbox-panel {
        background: #fafafa;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
    }
    .listbox-panel-header {
        background: #f4f4f2;
        padding: 10px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #7a7a7a;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .listbox-count {
        background: #1a1a1a;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 20px;
    }
    .listbox-search {
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
        position: relative;
    }
    .listbox-search input {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 7px 12px 7px 32px;
        font-size: 12px;
        outline: none;
        background: #fff;
        transition: border-color 0.2s;
    }
    .listbox-search input:focus { border-color: #1a1a1a; }
    .listbox-search::before {
        content: '\F52A';
        font-family: 'bootstrap-icons';
        position: absolute;
        left: 24px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 13px;
        pointer-events: none;
    }
    .listbox-list {
        list-style: none;
        margin: 0;
        padding: 6px 0;
        max-height: 280px;
        overflow-y: auto;
    }
    .listbox-list::-webkit-scrollbar { width: 4px; }
    .listbox-list::-webkit-scrollbar-track { background: transparent; }
    .listbox-list::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }

    .listbox-item {
        padding: 9px 16px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: background 0.15s;
        user-select: none;
        border-left: 3px solid transparent;
    }
    .listbox-item:hover { background: #f0f0f0; }
    .listbox-item.selected {
        background: #f0faf0;
        border-left-color: #137333;
    }
    .listbox-item .item-img {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
        background: #e5e7eb;
        flex-shrink: 0;
    }
    .listbox-item .item-info { flex: 1; min-width: 0; }
    .listbox-item .item-name {
        font-weight: 600;
        color: #1a1a1a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .listbox-item .item-cat {
        font-size: 10px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .listbox-empty {
        padding: 32px 16px;
        text-align: center;
        color: #b0b0b0;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .transfer-btns {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .transfer-btn {
        width: 40px; height: 40px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        background: #fff;
        color: #1a1a1a;
        font-size: 1rem;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
    }
    .transfer-btn:hover { background: #1a1a1a; color: #fff; border-color: #1a1a1a; }

    .section-divider {
        border: none;
        border-top: 1px solid rgba(0,0,0,0.06);
        margin: 32px 0;
    }

    .color-preview-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .color-preview-box {
        width: 40px; height: 40px;
        border-radius: 8px;
        border: 1px solid rgba(0,0,0,0.08);
        flex-shrink: 0;
        transition: background 0.2s;
    }
</style>

<div class="container-fluid p-0">
    {{-- Breadcrumb --}}
    <div class="mb-5">
        <a href="{{ route('admin.events.index') }}"
           class="text-decoration-none text-muted small d-inline-flex align-items-center gap-2 mb-2">
            <i class="bi bi-arrow-left"></i> Back to Collections
        </a>
        <span class="text-uppercase text-muted d-block mt-2" style="font-size: 10px; font-weight: 600; letter-spacing: 0.2em;">Operations</span>
        <h1 class="h2 fw-bold mt-1 mb-0" style="letter-spacing: -0.02em;">Create New Collection</h1>
    </div>

    {{-- Error Alert --}}
    @if(session('error'))
        <div class="alert alert-danger border-0 mb-4 p-3 rounded-3" role="alert" style="font-size: 13px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.events.store') }}" method="POST" id="eventForm" enctype="multipart/form-data">
        @csrf

        {{-- ── SECTION 1: Event Details ── --}}
        <div class="form-luxury-card shadow-sm mb-4">
            <h5 class="fw-bold mb-1" style="letter-spacing: -0.01em;">Collection Details</h5>
            <p class="text-muted small mb-4">Define the name, dates, and visual branding for this event.</p>

            <div class="row g-4">
                {{-- Name --}}
                <div class="col-md-8">
                    <label for="name" class="form-label-luxury">Collection Name</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="e.g. MID-YEAR SUMMER CARNIVAL"
                           class="form-control-luxury @error('name') is-invalid @enderror" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Short Name --}}
                <div class="col-md-4">
                    <label for="short_name" class="form-label-luxury">Short Badge Name</label>
                    <input type="text" name="short_name" id="short_name"
                           value="{{ old('short_name') }}"
                           placeholder="e.g. Summer"
                           class="form-control-luxury @error('short_name') is-invalid @enderror" required>
                    @error('short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <p class="text-muted mt-2 mb-0" style="font-size: 11px;">Shown as the compact badge label on product cards.</p>
                </div>

                {{-- Start Date --}}
                <div class="col-md-6">
                    <label for="start_date" class="form-label-luxury">Start Date</label>
                    <input type="datetime-local" name="start_date" id="start_date"
                           value="{{ old('start_date') }}"
                           class="form-control-luxury @error('start_date') is-invalid @enderror" required>
                    @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- End Date --}}
                <div class="col-md-6">
                    <label for="end_date" class="form-label-luxury">End Date</label>
                    <input type="datetime-local" name="end_date" id="end_date"
                           value="{{ old('end_date') }}"
                           class="form-control-luxury @error('end_date') is-invalid @enderror" required>
                    @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Theme Color --}}
                <div class="col-md-6">
                    <label for="theme_color" class="form-label-luxury">Theme Background Color</label>
                    <div class="color-preview-row">
                        <input type="color" name="theme_color" id="theme_color"
                               value="{{ old('theme_color', '#0d9488') }}"
                               class="form-control form-control-color @error('theme_color') is-invalid @enderror"
                               style="width: 56px; height: 44px; padding: 4px; border-radius: 10px; border: 1px solid #d1d5db; cursor: pointer;"
                               oninput="document.getElementById('theme_color_hex').value = this.value; document.getElementById('theme_preview').style.background = this.value;">
                        <input type="text" id="theme_color_hex" placeholder="#0d9488"
                               value="{{ old('theme_color', '#0d9488') }}"
                               class="form-control-luxury" style="flex:1;"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){ document.getElementById('theme_color').value=this.value; document.getElementById('theme_preview').style.background=this.value; }">
                        <div class="color-preview-box" id="theme_preview"
                             style="background: {{ old('theme_color', '#0d9488') }};"></div>
                    </div>
                    @error('theme_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Text Color --}}
                <div class="col-md-6">
                    <label for="text_color" class="form-label-luxury">Text / Accent Color</label>
                    <div class="color-preview-row">
                        <input type="color" name="text_color" id="text_color"
                               value="{{ old('text_color', '#fef08a') }}"
                               class="form-control form-control-color @error('text_color') is-invalid @enderror"
                               style="width: 56px; height: 44px; padding: 4px; border-radius: 10px; border: 1px solid #d1d5db; cursor: pointer;"
                               oninput="document.getElementById('text_color_hex').value = this.value; document.getElementById('text_preview').style.background = this.value;">
                        <input type="text" id="text_color_hex" placeholder="#fef08a"
                               value="{{ old('text_color', '#fef08a') }}"
                               class="form-control-luxury" style="flex:1;"
                               oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){ document.getElementById('text_color').value=this.value; document.getElementById('text_preview').style.background=this.value; }">
                        <div class="color-preview-box" id="text_preview"
                             style="background: {{ old('text_color', '#fef08a') }};"></div>
                    </div>
                    @error('text_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Banner Image URL --}}
                <div class="col-12">
                    <label for="banner_image" class="form-label-luxury">Banner Image URL <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="url" name="banner_image" id="banner_image"
                           value="{{ old('banner_image') }}"
                           placeholder="https://images.unsplash.com/photo-…"
                           class="form-control-luxury @error('banner_image') is-invalid @enderror">
                    @error('banner_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Background Image --}}
                <div class="col-md-6">
                    <label for="background_image" class="form-label-luxury">Background Image / Texture <span class="text-muted fw-normal">(optional, file)</span></label>
                    <input type="file" name="background_image" id="background_image"
                           accept="image/*"
                           class="form-control-luxury @error('background_image') is-invalid @enderror">
                    @error('background_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <p class="text-muted mt-2 mb-0" style="font-size: 11px;">Upload a full-width background image/texture.</p>
                </div>

                {{-- Main Image --}}
                <div class="col-md-6">
                    <label for="main_image" class="form-label-luxury">Main Image / Overlay <span class="text-muted fw-normal">(optional, file)</span></label>
                    <input type="file" name="main_image" id="main_image"
                           accept="image/*"
                           class="form-control-luxury @error('main_image') is-invalid @enderror">
                    @error('main_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <p class="text-muted mt-2 mb-0" style="font-size: 11px;">Upload a smaller overlay/product banner image.</p>
                </div>

                {{-- Display Title --}}
                <div class="col-md-12">
                    <label for="display_title" class="form-label-luxury">Display Title <span class="text-muted fw-normal">(optional)</span></label>
                    <input type="text" name="display_title" id="display_title"
                           value="{{ old('display_title') }}"
                           placeholder="e.g. HALLOWEEN SPOOKTACULAR"
                           class="form-control-luxury @error('display_title') is-invalid @enderror">
                    @error('display_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <p class="text-muted mt-2 mb-0" style="font-size: 11px;">Custom banner main heading displayed on frontend hero section.</p>
                </div>

                {{-- Display Description --}}
                <div class="col-md-12">
                    <label for="display_description" class="form-label-luxury">Display Description <span class="text-muted fw-normal">(optional)</span></label>
                    <textarea name="display_description" id="display_description" rows="3"
                              placeholder="e.g. EXCLUSIVE OFFERS FROM..."
                              class="form-control-luxury @error('display_description') is-invalid @enderror">{{ old('display_description') }}</textarea>
                    @error('display_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <p class="text-muted mt-2 mb-0" style="font-size: 11px;">Custom banner sub-heading/paragraph displayed on frontend hero section.</p>
                </div>
            </div>
        </div>

        {{-- ── SECTION 2: Product Assignment ── --}}
        <div class="form-luxury-card shadow-sm mb-4">
            <h5 class="fw-bold mb-1" style="letter-spacing: -0.01em;">Product Assignment</h5>
            <p class="text-muted small mb-4">
                Move products from <strong>Available</strong> to <strong>Assigned</strong> to include them in this collection.
                You can search within each panel.
            </p>

            {{-- Hidden multi-select — submitted with the form --}}
            <select name="product_ids[]" id="productIdsSelect" multiple hidden>
                @foreach($products as $product)
                    <option value="{{ $product->id }}"
                        {{ in_array($product->id, old('product_ids', [])) ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>

            <div class="dual-listbox-wrapper">
                {{-- Left: Available --}}
                <div class="listbox-panel">
                    <div class="listbox-panel-header">
                        <span>Available Products</span>
                        <span class="listbox-count" id="availableCount">0</span>
                    </div>
                    <div class="listbox-search">
                        <input type="text" id="searchAvailable" placeholder="Search available…" autocomplete="off">
                    </div>
                    <ul class="listbox-list" id="availableList">
                        @foreach($products as $product)
                            @if(!in_array($product->id, old('product_ids', [])))
                                <li class="listbox-item"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ strtolower($product->name) }}"
                                    data-cat="{{ strtolower($product->category->name ?? '') }}"
                                    onclick="toggleSelect(this, 'available')">
                                    <img src="{{ $product->image_path }}" alt="" class="item-img"
                                         onerror="this.style.display='none'">
                                    <div class="item-info">
                                        <div class="item-name">{{ $product->name }}</div>
                                        <div class="item-cat">{{ $product->category->name ?? '—' }}</div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                {{-- Transfer Buttons --}}
                <div class="transfer-btns">
                    <button type="button" class="transfer-btn" onclick="transferSelected('available', 'assigned')" title="Assign selected →">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <button type="button" class="transfer-btn" onclick="transferAll('available', 'assigned')" title="Assign all →">
                        <i class="bi bi-chevron-double-right"></i>
                    </button>
                    <button type="button" class="transfer-btn" onclick="transferAll('assigned', 'available')" title="← Remove all">
                        <i class="bi bi-chevron-double-left"></i>
                    </button>
                    <button type="button" class="transfer-btn" onclick="transferSelected('assigned', 'available')" title="← Remove selected">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>

                {{-- Right: Assigned --}}
                <div class="listbox-panel">
                    <div class="listbox-panel-header">
                        <span>Assigned to Collection</span>
                        <span class="listbox-count" id="assignedCount">0</span>
                    </div>
                    <div class="listbox-search">
                        <input type="text" id="searchAssigned" placeholder="Search assigned…" autocomplete="off">
                    </div>
                    <ul class="listbox-list" id="assignedList">
                        @foreach($products as $product)
                            @if(in_array($product->id, old('product_ids', [])))
                                <li class="listbox-item"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ strtolower($product->name) }}"
                                    data-cat="{{ strtolower($product->category->name ?? '') }}"
                                    onclick="toggleSelect(this, 'assigned')">
                                    <img src="{{ $product->image_path }}" alt="" class="item-img"
                                         onerror="this.style.display='none'">
                                    <div class="item-info">
                                        <div class="item-name">{{ $product->name }}</div>
                                        <div class="item-cat">{{ $product->category->name ?? '—' }}</div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="d-flex justify-content-end gap-3">
            <a href="{{ route('admin.events.index') }}" class="btn-luxury-secondary">Cancel</a>
            <button type="submit" class="btn-luxury-black" id="submitBtn">
                <i class="bi bi-check-lg"></i> Publish Collection
            </button>
        </div>
    </form>
</div>

<script>
// ─── Dual Listbox Logic ───────────────────────────────────────────────────────

function updateCounts() {
    document.getElementById('availableCount').textContent =
        document.querySelectorAll('#availableList .listbox-item').length;
    document.getElementById('assignedCount').textContent =
        document.querySelectorAll('#assignedList .listbox-item').length;
}

function syncHiddenSelect() {
    const select = document.getElementById('productIdsSelect');
    // Clear all
    Array.from(select.options).forEach(o => o.selected = false);
    // Select IDs in assigned panel
    document.querySelectorAll('#assignedList .listbox-item').forEach(item => {
        const opt = select.querySelector(`option[value="${item.dataset.id}"]`);
        if (opt) opt.selected = true;
    });
}

function showEmpty(listId) {
    const list = document.getElementById(listId);
    const items = list.querySelectorAll('.listbox-item');
    let emptyEl = list.querySelector('.listbox-empty');
    if (items.length === 0) {
        if (!emptyEl) {
            emptyEl = document.createElement('li');
            emptyEl.className = 'listbox-empty';
            emptyEl.textContent = 'No products';
            list.appendChild(emptyEl);
        }
    } else if (emptyEl) {
        emptyEl.remove();
    }
}

function toggleSelect(el, panel) {
    el.classList.toggle('selected');
}

function transferSelected(fromPanelId, toPanelId) {
    const fromList = document.getElementById(fromPanelId + 'List');
    const toList   = document.getElementById(toPanelId   + 'List');
    const selected = fromList.querySelectorAll('.listbox-item.selected');
    selected.forEach(item => {
        item.classList.remove('selected');
        toList.appendChild(item);
    });
    syncHiddenSelect();
    updateCounts();
    showEmpty(fromPanelId + 'List');
    showEmpty(toPanelId   + 'List');
}

function transferAll(fromPanelId, toPanelId) {
    const fromList = document.getElementById(fromPanelId + 'List');
    const toList   = document.getElementById(toPanelId   + 'List');
    Array.from(fromList.querySelectorAll('.listbox-item')).forEach(item => {
        item.classList.remove('selected');
        toList.appendChild(item);
    });
    syncHiddenSelect();
    updateCounts();
    showEmpty(fromPanelId + 'List');
    showEmpty(toPanelId   + 'List');
}

// Search filter
function bindSearch(inputId, listId) {
    document.getElementById(inputId).addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#' + listId + ' .listbox-item').forEach(item => {
            const match = item.dataset.name.includes(q) || item.dataset.cat.includes(q);
            item.style.display = match ? '' : 'none';
        });
    });
}

bindSearch('searchAvailable', 'availableList');
bindSearch('searchAssigned',  'assignedList');

// Sync on submit — ensure all assigned items are selected in the hidden <select>
document.getElementById('eventForm').addEventListener('submit', function() {
    syncHiddenSelect();
});

// Init counters
updateCounts();
showEmpty('availableList');
showEmpty('assignedList');
</script>
@endsection
