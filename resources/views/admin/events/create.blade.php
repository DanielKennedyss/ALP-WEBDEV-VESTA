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
    
    .form-select-modern {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.25em 1.25em;
        padding-right: 2.5rem;
        
        border-radius: 0.375rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        padding-left: 1rem;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        font-size: 13px;
        color: #1a1a1a;
        width: 100%;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-select-modern:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.5);
    }
    
    #collection_preview,
    #home_preview {
        background-size: cover !important;
        background-position: center !important;
        background-repeat: no-repeat !important;
    }
    #collection_preview {
        background-blend-mode: overlay !important;
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

        <div class="row">
            {{-- Left Column: Form Inputs --}}
            <div class="col-lg-7">
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
                                       oninput="document.getElementById('theme_color_hex').value = this.value; updatePreviewColor();">
                                <input type="text" id="theme_color_hex" placeholder="#0d9488"
                                       value="{{ old('theme_color', '#0d9488') }}"
                                       class="form-control-luxury" style="flex:1;"
                                       oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){ document.getElementById('theme_color').value=this.value; updatePreviewColor(); }">
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
                                       oninput="document.getElementById('text_color_hex').value = this.value; updatePreviewColor();">
                                <input type="text" id="text_color_hex" placeholder="#fef08a"
                                       value="{{ old('text_color', '#fef08a') }}"
                                       class="form-control-luxury" style="flex:1;"
                                       oninput="if(/^#[0-9A-Fa-f]{6}$/.test(this.value)){ document.getElementById('text_color').value=this.value; updatePreviewColor(); }">
                            </div>
                            @error('text_color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Background Image Section --}}
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="background_image_source" class="form-label-luxury">Background Source</label>
                                    <select name="background_image_source" id="background_image_source" class="form-select-modern" onchange="toggleBackgroundImageSource()">
                                        <option value="file" {{ old('background_image_source', 'file') === 'file' ? 'selected' : '' }}>Upload File</option>
                                        <option value="url" {{ old('background_image_source') === 'url' ? 'selected' : '' }}>Image URL</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div id="background_image_file_wrapper">
                                        <label for="bg_file_input" class="form-label-luxury">Background Image File <span class="text-muted fw-normal">(optional)</span></label>
                                        <input type="file" name="background_image_file" id="bg_file_input"
                                               accept="image/*"
                                               class="form-control-luxury @error('background_image_file') is-invalid @enderror"
                                               onchange="handleBackgroundFileChange(this)">
                                        @error('background_image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div id="background_image_url_wrapper" style="display: none;">
                                        <label for="bg_url_input" class="form-label-luxury">Background Image URL <span class="text-muted fw-normal">(optional)</span></label>
                                        <input type="url" name="background_image_url" id="bg_url_input"
                                               value="{{ old('background_image_url') }}"
                                               placeholder="https://images.unsplash.com/photo-…"
                                               class="form-control-luxury @error('background_image_url') is-invalid @enderror"
                                               oninput="updatePreviewImages()">
                                        @error('background_image_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Main Image Section --}}
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="main_image_source" class="form-label-luxury">Main Image Source</label>
                                    <select name="main_image_source" id="main_image_source" class="form-select-modern" onchange="toggleMainImageSource()">
                                        <option value="file" {{ old('main_image_source', 'file') === 'file' ? 'selected' : '' }}>Upload File</option>
                                        <option value="url" {{ old('main_image_source') === 'url' ? 'selected' : '' }}>Image URL</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div id="main_image_file_wrapper">
                                        <label for="main_image_file" class="form-label-luxury">Main Image File <span class="text-muted fw-normal">(optional)</span></label>
                                        <input type="file" name="main_image_file" id="main_image_file"
                                               accept="image/*"
                                               class="form-control-luxury @error('main_image_file') is-invalid @enderror"
                                               onchange="handleMainFileChange(this)">
                                        @error('main_image_file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div id="main_image_url_wrapper" style="display: none;">
                                        <label for="main_image_url" class="form-label-luxury">Main Image URL <span class="text-muted fw-normal">(optional)</span></label>
                                        <input type="url" name="main_image_url" id="main_image_url"
                                               value="{{ old('main_image_url') }}"
                                               placeholder="https://images.unsplash.com/photo-…"
                                               class="form-control-luxury @error('main_image_url') is-invalid @enderror"
                                               oninput="updatePreviewImages()">
                                        @error('main_image_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Display Title --}}
                        <div class="col-md-12">
                            <label for="display_title" class="form-label-luxury">Display Title <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="display_title" id="display_title"
                                   value="{{ old('display_title') }}"
                                   placeholder="e.g. HALLOWEEN SPOOKTACULAR"
                                   class="form-control-luxury @error('display_title') is-invalid @enderror"
                                   oninput="updatePreviewText()">
                            @error('display_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <p class="text-muted mt-2 mb-0" style="font-size: 11px;">Custom banner main heading displayed on frontend hero section.</p>
                        </div>

                        {{-- Display Description --}}
                        <div class="col-md-12">
                            <label for="display_description" class="form-label-luxury">Display Description <span class="text-muted fw-normal">(optional)</span></label>
                            <textarea name="display_description" id="display_description" rows="3"
                                      placeholder="e.g. EXCLUSIVE OFFERS FROM..."
                                      class="form-control-luxury @error('display_description') is-invalid @enderror"
                                      oninput="updatePreviewText()">{{ old('display_description') }}</textarea>
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
                <div class="d-flex justify-content-end gap-3 mb-4">
                    <a href="{{ route('admin.events.index') }}" class="btn-luxury-secondary">Cancel</a>
                    <button type="submit" class="btn-luxury-black" id="submitBtn">
                        <i class="bi bi-check-lg"></i> Publish Collection
                    </button>
                </div>
            </div>

            {{-- Right Column: Live Preview --}}
            <div class="col-lg-5">
                <div class="form-luxury-card shadow-sm mb-4" style="position: sticky; top: 30px;">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="fw-bold mb-0">Live Banner Preview</h5>
                            <span class="badge bg-dark text-uppercase font-monospace" style="font-size: 9px; letter-spacing: 0.1em; padding: 5px 8px;">Real-time</span>
                        </div>
                        <p class="text-muted small mb-4">See how this event banner looks under both main layout formats.</p>

                        <!-- COLLECTION PAGE PREVIEW -->
                        <div class="text-uppercase font-semibold text-muted mb-2" style="font-size: 10px; letter-spacing: 0.1em;">COLLECTION PAGE PREVIEW</div>
                        <div id="collection_preview" class="border overflow-hidden rounded-3 shadow-sm relative mb-4 transition-all duration-300" 
                             style="background-color: #0d9488; color: #fef08a; border-color: rgba(254, 240, 138, 0.25); width: 100%; aspect-ratio: 21/9; max-height: 400px; display: flex; align-items: center; justify-content: space-between; padding: 2rem; background-size: cover; background-position: center; border-radius: 8px; overflow: hidden; font-family: 'Montserrat', sans-serif; background-image: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80');">
                            
                            <!-- Left Side (Text) -->
                            <div class="d-flex flex-column justify-content-center align-items-start text-start" style="max-width: 50%; z-index: 10;">
                                <div class="inline-flex align-items-center gap-1 border px-2 py-0.5 mb-2 text-uppercase font-semibold text-start" style="font-size: 7px; border-color: currentColor; letter-spacing: 0.15em;">
                                    Limited Time Event
                                </div>
                                <h2 class="h6 font-serif text-uppercase mb-2 tracking-wide text-start" id="prev-col-title" style="font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 16px; word-break: break-word; color: inherit; margin-bottom: 8px;">MID-YEAR SUMMER CARNIVAL</h2>
                                <p class="opacity-90 mb-3 text-start" id="prev-col-desc" style="font-size: 9px; line-height: 1.4; letter-spacing: 0.05em; font-weight: 300; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; color: inherit; margin-bottom: 16px;">EXCLUSIVE OFFERS FOR A LIMITED TIME ONLY.</p>
                                <a href="javascript:void(0)" class="btn btn-sm text-uppercase font-semibold" id="prev-col-btn" style="font-size: 7px; border: 1px solid currentColor; background-color: #fef08a; color: #0d9488; letter-spacing: 0.1em; padding: 5px 10px; pointer-events: none;">EXPLORE COLLECTION</a>
                            </div>

                            <!-- Right Side (Main Image) -->
                            <img id="preview_main_image" src="" alt="" style="max-width: 50%; max-height: 100%; object-fit: contain; z-index: 10; border-radius: 4px;">
                        </div>

                        <!-- HOME PAGE PREVIEW -->
                        <div class="text-uppercase font-semibold text-muted mb-2" style="font-size: 10px; letter-spacing: 0.1em;">HOME PAGE PREVIEW</div>
                        <div id="home_preview" class="border overflow-hidden rounded-3 shadow-sm relative transition-all duration-300" 
                             style="background-color: #000; color: #fff; min-height: 400px; width: 100%; font-family: 'Montserrat', sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; background-size: cover; background-position: center; background-image: url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80');">
                            
                            <div class="absolute inset-0 bg-black bg-opacity-40 pointer-events-none" style="z-index: 1;"></div>
                            
                            <div class="relative p-4 d-flex flex-column justify-content-center align-items-center text-center h-100" style="min-height: 400px; width: 100%; z-index: 10;">
                                <span class="text-light opacity-75 text-uppercase mb-2 font-light" style="font-size: 8px; letter-spacing: 0.25em;">Limited Event</span>
                                <h2 class="h5 font-serif text-uppercase mb-2 tracking-wide text-white" id="prev-home-title" style="font-family: 'Cormorant Garamond', serif; font-weight: 600; font-size: 18px; word-break: break-word;">MID-YEAR SUMMER CARNIVAL</h2>
                                <p class="text-light opacity-90 mb-3 max-w-sm" id="prev-home-desc" style="font-size: 9px; line-height: 1.4; letter-spacing: 0.05em; font-weight: 300; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 16px;">EXCLUSIVE OFFERS FOR A LIMITED TIME ONLY.</p>
                                <a href="javascript:void(0)" class="btn btn-sm text-uppercase font-semibold text-white border-white" id="prev-home-btn" style="font-size: 7px; border: 1px solid #fff; letter-spacing: 0.15em; padding: 5px 10px; background: transparent; pointer-events: none;">Explore Collection</a>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </form>
</div>

<script>
// ─── Live Preview Logic ──────────────────────────────────────────────────────
let localBgObjectURL = null;
let localMainObjectURL = null;

function toggleBackgroundImageSource() {
    const source = document.getElementById('background_image_source').value;
    const fileWrapper = document.getElementById('background_image_file_wrapper');
    const urlWrapper = document.getElementById('background_image_url_wrapper');
    const fileInput = document.getElementById('bg_file_input');
    const urlInput = document.getElementById('bg_url_input');
    
    if (source === 'file') {
        fileWrapper.style.display = 'block';
        urlWrapper.style.display = 'none';
        urlInput.value = '';
    } else {
        fileWrapper.style.display = 'none';
        urlWrapper.style.display = 'block';
        fileInput.value = '';
        if (localBgObjectURL) {
            URL.revokeObjectURL(localBgObjectURL);
            localBgObjectURL = null;
        }
    }
    updatePreviewImages();
}

function toggleMainImageSource() {
    const source = document.getElementById('main_image_source').value;
    const fileWrapper = document.getElementById('main_image_file_wrapper');
    const urlWrapper = document.getElementById('main_image_url_wrapper');
    const fileInput = document.getElementById('main_image_file');
    const urlInput = document.getElementById('main_image_url');
    
    if (source === 'file') {
        fileWrapper.style.display = 'block';
        urlWrapper.style.display = 'none';
        urlInput.value = '';
    } else {
        fileWrapper.style.display = 'none';
        urlWrapper.style.display = 'block';
        fileInput.value = '';
        if (localMainObjectURL) {
            URL.revokeObjectURL(localMainObjectURL);
            localMainObjectURL = null;
        }
    }
    updatePreviewImages();
}

function handleBackgroundFileChange(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const result = e.target.result;
            localBgObjectURL = result;
            updatePreviewImages();
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        localBgObjectURL = null;
        updatePreviewImages();
    }
}

function handleMainFileChange(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const result = e.target.result;
            localMainObjectURL = result;
            updatePreviewImages();
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        localMainObjectURL = null;
        updatePreviewImages();
    }
}

function updatePreviewImages() {
    const bgSource = document.getElementById('background_image_source').value;
    const bgUrlInput = document.getElementById('bg_url_input').value;
    const defaultPlaceholder = 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80';
    
    let bgUrl = defaultPlaceholder;
    if (bgSource === 'file' && localBgObjectURL) {
        bgUrl = localBgObjectURL;
    } else if (bgSource === 'url' && bgUrlInput.trim() !== '') {
        bgUrl = bgUrlInput;
    } else if (bgSource === 'url') {
        bgUrl = '';
    }
    
    // Update background layers
    const prevCol = document.getElementById('collection_preview');
    const prevHome = document.getElementById('home_preview');
    
    if (bgUrl !== '') {
        if (prevCol) { prevCol.style.backgroundImage = `url('${bgUrl}')`; }
        if (prevHome) { prevHome.style.backgroundImage = `url('${bgUrl}')`; }
    } else {
        if (prevCol) { prevCol.style.backgroundImage = 'none'; }
        if (prevHome) { prevHome.style.backgroundImage = 'none'; }
    }
    
    const mainSource = document.getElementById('main_image_source').value;
    const mainUrlInput = document.getElementById('main_image_url').value;
    
    let mainUrl = '';
    if (mainSource === 'file' && localMainObjectURL) {
        mainUrl = localMainObjectURL;
    } else if (mainSource === 'url' && mainUrlInput.trim() !== '') {
        mainUrl = mainUrlInput;
    }
    
    const previewMainImage = document.getElementById('preview_main_image');
    if (previewMainImage) {
        if (mainUrl !== '') {
            previewMainImage.src = mainUrl;
            previewMainImage.style.display = 'block';
        } else {
            previewMainImage.src = '';
            previewMainImage.style.display = 'none';
        }
    }
}

function updatePreviewText() {
    const name = document.getElementById('name').value;
    const displayTitle = document.getElementById('display_title').value;
    const displayDesc = document.getElementById('display_description').value;
    
    const finalTitle = displayTitle.trim() !== '' ? displayTitle : (name.trim() !== '' ? name : 'MID-YEAR SUMMER CARNIVAL');
    const finalDesc = displayDesc.trim() !== '' ? displayDesc : 'EXCLUSIVE OFFERS FOR A LIMITED TIME ONLY.';
    
    document.getElementById('prev-col-title').textContent = finalTitle.toUpperCase();
    document.getElementById('prev-home-title').textContent = finalTitle.toUpperCase();
    
    document.getElementById('prev-col-desc').textContent = finalDesc;
    document.getElementById('prev-home-desc').textContent = finalDesc;
}

function updatePreviewColor() {
    const themeBg = document.getElementById('theme_color').value;
    const themeText = document.getElementById('text_color').value;
    
    const colPreview = document.getElementById('collection_preview');
    if (colPreview) {
        colPreview.style.backgroundColor = themeBg;
        colPreview.style.color = themeText;
        colPreview.style.borderColor = themeText + '44';
    }
    
    const colBtn = document.getElementById('prev-col-btn');
    if (colBtn) {
        colBtn.style.backgroundColor = themeText;
        colBtn.style.color = themeBg;
        colBtn.style.borderColor = themeText;
    }
}

// switchPreviewTab removed as split previews display simultaneously

// ─── Dual Listbox Logic ───────────────────────────────────────────────────────

function updateCounts() {
    document.getElementById('availableCount').textContent =
        document.querySelectorAll('#availableList .listbox-item').length;
    document.getElementById('assignedCount').textContent =
        document.querySelectorAll('#assignedList .listbox-item').length;
}

function syncHiddenSelect() {
    const select = document.getElementById('productIdsSelect');
    Array.from(select.options).forEach(o => o.selected = false);
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

// Sync on submit and disable empty file inputs to prevent validation errors
document.getElementById('eventForm').addEventListener('submit', function() {
    syncHiddenSelect();

    // Disable empty file inputs so they are not sent with the form at all
    const bgFileInput = document.getElementById('bg_file_input');
    if (bgFileInput && bgFileInput.files.length === 0) {
        bgFileInput.setAttribute('disabled', 'disabled');
    }
    const mainFileInput = document.getElementById('main_image_file');
    if (mainFileInput && mainFileInput.files.length === 0) {
        mainFileInput.setAttribute('disabled', 'disabled');
    }
});

// Setup event listeners for Live Preview
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('name').addEventListener('input', updatePreviewText);
    
    const displayTitle = document.getElementById('display_title');
    if (displayTitle) displayTitle.addEventListener('input', updatePreviewText);
    
    const displayDesc = document.getElementById('display_description');
    if (displayDesc) displayDesc.addEventListener('input', updatePreviewText);

    const bgFileInput = document.getElementById('bg_file_input');
    const bgUrlInput = document.getElementById('bg_url_input');
    const collectionPreview = document.getElementById('collection_preview');
    const homePreview = document.getElementById('home_preview');

    // Helper function to update both preview containers
    function updateBackgrounds(url) {
        if (collectionPreview) collectionPreview.style.backgroundImage = `url('${url}')`;
        if (homePreview) homePreview.style.backgroundImage = `url('${url}')`;
    }

    // Handle File Upload
    if (bgFileInput) {
        bgFileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    updateBackgrounds(event.target.result);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Handle URL Input
    if (bgUrlInput) {
        bgUrlInput.addEventListener('input', function(e) {
            if (this.value.trim() !== '') {
                updateBackgrounds(this.value);
            } else {
                if (collectionPreview) collectionPreview.style.backgroundImage = 'none';
                if (homePreview) homePreview.style.backgroundImage = 'none';
            }
        });
    }

    const mainUrlInput = document.getElementById('main_image_url');
    if (mainUrlInput) mainUrlInput.addEventListener('input', updatePreviewImages);

    const mainFileInput = document.getElementById('main_image_file');
    if (mainFileInput) mainFileInput.addEventListener('change', function(e) {
        handleMainFileChange(this);
    });
    
    toggleBackgroundImageSource();
    toggleMainImageSource();
    updatePreviewText();
    updatePreviewColor();
    
    updateCounts();
    showEmpty('availableList');
    showEmpty('assignedList');
});
</script>
@endsection
