@extends('base.base')

@section('content')
    <style>
        .filter-chip {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            border: 1px solid #d1d5db;
            transition: all 0.3s;
            cursor: pointer;
            user-select: none;
            background: #fff;
            color: #4b5563;
        }

        .filter-chip:hover {
            border-color: #000;
            color: #000;
        }

        .filter-chip.active {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .size-btn {
            width: 3rem;
            height: 3rem;
            border: 1px solid #d1d5db;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            cursor: pointer;
            background: #fff;
        }

        .size-btn:hover:not(.disabled) {
            border-color: #000;
        }

        .size-btn.active {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .size-btn.disabled {
            border-color: #e5e7eb;
            color: #d1d5db;
            cursor: not-allowed;
            text-decoration: line-through;
        }

        .gender-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.625rem;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .gender-male {
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .gender-female {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .gender-unisex {
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            animation: fadeUp 0.6s ease forwards;
            opacity: 0;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Horizontal Filter Styles */
        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            transition: all 0.2s;
            white-space: nowrap;
            color: #4b5563;
        }

        .filter-btn:hover {
            border-color: #9ca3af;
            color: #000;
        }

        .filter-btn.active {
            border-color: #000;
            color: #000;
            font-weight: 500;
        }

        .dropdown-content {
            position: absolute;
            top: 100%;
            margin-top: 0.5rem;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            min-width: 200px;
            z-index: 50;
            padding: 0.5rem 0;
            overflow: hidden;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            text-align: left;
            padding: 0.6rem 1.25rem;
            font-size: 0.8rem;
            color: #4b5563;
            transition: background 0.2s;
        }

        .dropdown-item:hover {
            background: #f9fafb;
            color: #000;
        }

        .dropdown-item.active {
            background: #f3f4f6;
            font-weight: 600;
            color: #000;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* ===== Review Overlay Widget ===== */
        .review-overlay {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            max-width: 340px;
            background: rgba(15, 15, 15, 0.55);
            backdrop-filter: blur(20px) saturate(1.4);
            -webkit-backdrop-filter: blur(20px) saturate(1.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 16px 18px;
            z-index: 10;
            overflow: hidden;
            opacity: 0;
            transform: translateY(12px);
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .review-overlay.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .review-overlay-header {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .review-overlay-header svg {
            width: 14px;
            height: 14px;
            color: rgba(255, 255, 255, 0.5);
        }

        .review-overlay-header span {
            font-size: 9px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
            font-weight: 500;
        }

        .review-slide-container {
            position: relative;
            min-height: 72px;
            overflow: hidden;
        }

        .review-slide-item {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            opacity: 0;
            transform: translateY(100%);
            transition: all 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform, opacity;
        }

        .review-slide-item.active {
            opacity: 1;
            transform: translateY(0);
        }

        .review-slide-item.exit-up {
            opacity: 0;
            transform: translateY(-100%);
        }

        .review-user-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
        }

        .review-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
            color: #fff;
            flex-shrink: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .review-avatar.av-1 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .review-avatar.av-2 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .review-avatar.av-3 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .review-avatar.av-4 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        .review-user-info {
            flex: 1;
            min-width: 0;
        }

        .review-user-name {
            font-size: 11px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.92);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .review-stars {
            display: flex;
            gap: 2px;
            margin-top: 2px;
        }

        .review-stars svg {
            width: 11px;
            height: 11px;
        }

        .review-stars .star-filled {
            color: #fbbf24;
            fill: #fbbf24;
        }

        .review-stars .star-empty {
            color: rgba(255, 255, 255, 0.15);
            fill: rgba(255, 255, 255, 0.15);
        }

        .review-comment {
            font-size: 12px;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.72);
            font-style: italic;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            letter-spacing: 0.01em;
        }

        .review-comment::before {
            content: '"';
            color: rgba(255, 255, 255, 0.3);
            font-size: 16px;
            font-weight: 700;
            margin-right: 2px;
        }

        .review-comment::after {
            content: '"';
            color: rgba(255, 255, 255, 0.3);
            font-size: 16px;
            font-weight: 700;
            margin-left: 2px;
        }

        .review-progress {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 12px;
            padding-top: 8px;
        }

        .review-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transition: all 0.4s ease;
        }

        .review-dot.active {
            background: rgba(255, 255, 255, 0.7);
            width: 14px;
            border-radius: 2px;
        }

        .review-no-reviews {
            display: none;
        }
    </style>

    <div class="pt-28 pb-24 px-4 lg:px-8 bg-stone-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto">
            {{-- Page Header --}}
            <div class="text-center mb-12">
                <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">Our Selection</span>
                <h1 class="text-4xl md:text-5xl font-serif tracking-[0.15em] mt-4">THE COLLECTION</h1>
                <div class="w-16 h-px bg-black mx-auto mt-6"></div>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-2xl mx-auto mb-10">
                <form id="mainFilterForm" action="{{ route('collection') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            placeholder="Search by name, description, or SKU..."
                            class="w-full border border-gray-300 pl-12 pr-4 py-3.5 text-sm focus:outline-none focus:border-black transition-colors bg-white">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        @if (request('search'))
                            <a href="{{ route('collection') }}"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        @endif
                    </div>

                    {{-- Hidden inputs for all filters --}}
                    <input type="hidden" name="category" id="filterCategory" value="{{ request('category') }}">
                    <input type="hidden" name="gender" id="filterGender" value="{{ request('gender') }}">
                    <input type="hidden" name="size" id="filterSize" value="{{ request('size') }}">
                    <input type="hidden" name="sort" id="filterSort" value="{{ request('sort', 'newest') }}">
                </form>
            </div>

            <div class="flex-1 max-w-[1400px] mx-auto w-full">
                {{-- Horizontal Sticky Filter Bar --}}
                <div class="sticky top-20 z-40 bg-stone-50 border-y border-gray-200 py-3 mb-8 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-2 flex-wrap pb-1 md:pb-0">
                            <div class="flex items-center gap-2 mr-2">
                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                <span class="text-[10px] tracking-widest text-gray-500 font-medium uppercase">Filters</span>
                            </div>

                            {{-- Category Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="filter-btn"
                                    :class="{ 'active': '{{ request('category') }}' }">
                                    Category <span
                                        class="text-black font-semibold ml-1">{{ request('category') ?: '' }}</span>
                                    <svg class="h-3 w-3 transition-transform" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" :class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="dropdown-content left-0" style="display: none;">
                                    <button type="button" onclick="setFilter('category','')"
                                        class="dropdown-item {{ !request('category') ? 'active' : '' }}">All
                                        Categories</button>
                                    @foreach ($categories as $cat)
                                        <button type="button" onclick="setFilter('category','{{ $cat }}')"
                                            class="dropdown-item {{ request('category') == $cat ? 'active' : '' }}">{{ strtoupper($cat) }}</button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Gender Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="filter-btn"
                                    :class="{ 'active': '{{ request('gender') }}' }">
                                    Gender <span class="text-black font-semibold ml-1">{{ request('gender') ?: '' }}</span>
                                    <svg class="h-3 w-3 transition-transform" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" :class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="dropdown-content left-0" style="display: none;">
                                    <button type="button" onclick="setFilter('gender','')"
                                        class="dropdown-item {{ !request('gender') ? 'active' : '' }}">All
                                        Genders</button>
                                    @foreach ($genders as $g)
                                        <button type="button" onclick="setFilter('gender','{{ $g }}')"
                                            class="dropdown-item {{ request('gender') == $g ? 'active' : '' }}">{{ strtoupper($g) }}</button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Size Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false" class="filter-btn"
                                    :class="{ 'active': '{{ request('size') }}' }">
                                    Size <span class="text-black font-semibold ml-1">{{ request('size') ?: '' }}</span>
                                    <svg class="h-3 w-3 transition-transform" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" :class="{ 'rotate-180': open }">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div x-show="open" x-transition class="dropdown-content left-0 min-w-[280px]"
                                    style="display: none;">
                                    <div class="grid grid-cols-3 gap-2 p-4">
                                        <button type="button" onclick="setFilter('size','')"
                                            class="size-btn {{ !request('size') ? 'active' : '' }}">ALL</button>
                                        @foreach ($sizes as $s)
                                            <button type="button" onclick="setFilter('size','{{ $s }}')"
                                                class="size-btn {{ request('size') == $s ? 'active' : '' }}">{{ strtoupper($s) }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            @if (request('category') || request('gender') || request('size') || request('search'))
                                <a href="{{ route('collection') }}"
                                    class="text-[10px] tracking-widest text-gray-500 hover:text-black transition-colors uppercase ml-2 underline underline-offset-4">Clear
                                    Filters</a>
                            @endif
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-6 shrink-0">
                            <span class="text-xs text-gray-500 whitespace-nowrap"><span
                                    class="text-black font-medium">{{ $products->count() }}</span> Results</span>

                            {{-- Sort Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" @click.away="open = false"
                                    class="text-xs tracking-wider font-medium flex items-center gap-1.5 hover:text-gray-600 transition-colors uppercase">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                                    </svg>
                                    Sort By
                                </button>
                                <div x-show="open" x-transition class="dropdown-content right-0 left-auto"
                                    style="display: none;">
                                    <button type="button" onclick="setFilter('sort','newest')"
                                        class="dropdown-item {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}">Newest
                                        First</button>
                                    <button type="button" onclick="setFilter('sort','price_low')"
                                        class="dropdown-item {{ request('sort') == 'price_low' ? 'active' : '' }}">Price:
                                        Low → High</button>
                                    <button type="button" onclick="setFilter('sort','price_high')"
                                        class="dropdown-item {{ request('sort') == 'price_high' ? 'active' : '' }}">Price:
                                        High → Low</button>
                                    <button type="button" onclick="setFilter('sort','name_asc')"
                                        class="dropdown-item {{ request('sort') == 'name_asc' ? 'active' : '' }}">Name: A →
                                        Z</button>
                                    <button type="button" onclick="setFilter('sort','name_desc')"
                                        class="dropdown-item {{ request('sort') == 'name_desc' ? 'active' : '' }}">Name: Z →
                                        A</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Grid --}}
                <div class="w-full">

                    @if ($products->isEmpty())
                        <div class="text-center py-24">
                            <svg class="mx-auto h-16 w-16 text-gray-300 mb-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-sm tracking-widest text-gray-400 uppercase mb-4">No products found</p>
                            <a href="{{ route('collection') }}"
                                class="inline-block border border-black text-xs tracking-widest px-8 py-3 hover:bg-black hover:text-white transition-colors">CLEAR
                                FILTERS</a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            @foreach ($products as $index => $product)
                                <article class="product-card group" style="animation-delay: {{ $index * 0.08 }}s">
                                    <div class="relative overflow-hidden bg-gray-200 aspect-[3/4] mb-5 cursor-pointer"
                                        onclick="openQuickView({{ $product->id }})">
                                        <img src="{{ $product->image_path }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                            loading="lazy">

                                        {{-- Gender Badge --}}
                                        <div class="absolute top-3 left-3 z-10">
                                            @php $gc = strtolower($product->gender ?? ''); @endphp
                                            <span
                                                class="gender-badge {{ $gc == 'male' ? 'gender-male' : ($gc == 'female' ? 'gender-female' : 'gender-unisex') }}">
                                                @if ($gc == 'male')
                                                    ♂
                                                @elseif($gc == 'female')
                                                ♀ @else⚥
                                                @endif
                                                {{ $product->gender }}
                                            </span>
                                        </div>

                                        @if ($product->total_stock == 0)
                                            <div class="absolute inset-0 bg-black/70 flex items-center justify-center">
                                                <span
                                                    class="text-white text-xs tracking-[0.3em] border border-white px-6 py-3">SOLD
                                                    OUT</span>
                                            </div>
                                        @endif

                                        <div
                                            class="absolute inset-x-0 bottom-0 translate-y-full group-hover:translate-y-0 transition-transform duration-500">
                                            <button onclick="openQuickView({{ $product->id }})"
                                                class="w-full bg-white/95 backdrop-blur-sm text-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors duration-300">
                                                QUICK VIEW
                                            </button>
                                        </div>
                                    </div>

                                    <div class="relative w-full flex flex-col items-center text-center pt-2">
                                        <span
                                            class="text-[10px] tracking-[0.2em] text-gray-400 mb-2 uppercase">{{ $product->category->name ?? '' }}</span>
                                        <h3 class="text-sm tracking-wide mb-1.5 group-hover:underline underline-offset-4">
                                            {{ $product->name }}</h3>
                                        
                                        @if ($product->reviews_count > 0)
                                            <div class="flex items-center gap-1 mb-2 text-black">
                                                <div class="flex gap-0.5">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ $i <= round($product->reviews_avg_rating) ? 'fill-current' : 'text-stone-200' }}" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-[9px] text-stone-400 font-mono">({{ $product->reviews_count }})</span>
                                            </div>
                                        @endif

                                        <p class="text-sm font-light text-gray-800">IDR
                                            {{ number_format($product->price, 0, ',', '.') }}</p>

                                        <!-- Heart button at the bottom-right of the card -->
                                        <button
                                            onclick="toggleWishlist(event, {{ json_encode([
                                                'id' => $product->id,
                                                'name' => $product->name,
                                                'price' => $product->price,
                                                'image_path' => $product->image_path,
                                                'category' => $product->category->name ?? '',
                                            ]) }})"
                                            class="absolute right-2 bottom-1.5 p-2 text-gray-400 hover:text-red-500 transition-colors z-20 origin-center"
                                            id="wishlist-heart-{{ $product->id }}" aria-label="Add to Wishlist">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 stroke-current transition-colors duration-300 origin-center"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK VIEW MODAL --}}
    <div id="quickViewModal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeQuickView()"></div>
        <div class="absolute inset-4 md:inset-10 lg:inset-16 bg-white overflow-hidden flex items-center justify-center">
            <button onclick="closeQuickView()" class="absolute top-6 right-6 z-10 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="w-full h-full grid grid-cols-1 md:grid-cols-2">
                <div class="bg-gray-100 overflow-hidden relative">
                    <img id="modalImage" src="" alt="" class="w-full h-full object-cover">

                    {{-- Review Overlay Widget --}}
                    <div id="reviewOverlay" class="review-overlay">
                        <div class="review-overlay-header">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span id="reviewOverlayTitle">Customer Reviews</span>
                        </div>
                        <div class="review-slide-container" id="reviewSlideContainer">
                            {{-- Reviews will be injected here by JS --}}
                        </div>
                        <div class="review-progress" id="reviewProgressDots"></div>
                    </div>
                </div>

                <div class="p-8 md:p-12 lg:p-14 flex flex-col justify-center overflow-y-auto">
                    <span id="modalCategory" class="text-xs tracking-[0.3em] text-gray-400 uppercase mb-3"></span>
                    <h2 id="modalName" class="text-3xl md:text-4xl font-serif tracking-[0.1em] mb-4"></h2>

                    {{-- Rating in Modal --}}
                    <div id="modalRating" class="mb-4 flex items-center gap-1.5" style="display: none;"></div>

                    {{-- Gender Badge in Modal --}}
                    <div id="modalGender" class="mb-4"></div>

                    <p id="modalPrice" class="text-2xl font-light mb-6"></p>
                    <div class="w-16 h-px bg-gray-200 mb-6"></div>
                    <p id="modalDescription" class="text-gray-600 text-sm leading-relaxed mb-6"></p>

                    {{-- Size Selector --}}
                    <div id="modalSizeSection" class="mb-6">
                        <span class="text-xs tracking-[0.2em] block mb-3">SELECT SIZE</span>
                        <div id="modalSizes" class="flex flex-wrap gap-2"></div>
                        <p id="sizeStockInfo" class="text-[10px] tracking-wider text-gray-400 mt-2"></p>
                    </div>

                    <div class="mb-6">
                        <div class="flex items-center gap-4">
                            <span class="text-xs tracking-[0.2em]">QTY</span>
                            <input type="number" id="modalQuantity" value="1" min="1" disabled
                                class="w-20 border border-gray-200 px-3 py-2 text-center disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                        </div>
                        <p id="qtyWarning" class="text-xs text-red-500 mt-2" style="display: none;">Purchase has reached
                            the maximum limit!</p>
                    </div>

                    <form id="modalAddToCartForm" action="" method="POST">
                        @csrf
                        <input type="hidden" name="quantity" id="modalQuantityInput" value="1">
                        <input type="hidden" name="size" id="modalSizeInput" value="">
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" id="addToCartBtn"
                                class="flex-1 bg-black text-white text-xs tracking-[0.2em] py-4 hover:bg-gray-800 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                                ADD TO CART
                            </button>
                            <button type="button" onclick="buyNow()" id="buyNowBtn"
                                class="flex-1 border border-black text-xs tracking-[0.2em] py-4 hover:bg-black hover:text-white transition-colors disabled:border-gray-300 disabled:text-gray-300 disabled:cursor-not-allowed disabled:hover:bg-white">
                                BUY NOW
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="application/json" id="productsData">{!! json_encode($products) !!}</script>

    <script>
        const products = JSON.parse(document.getElementById('productsData').textContent);
        let currentModalProductId = null;
        let selectedSize = null;

        // Filter system
        function setFilter(name, value) {
            document.getElementById('filter' + name.charAt(0).toUpperCase() + name.slice(1)).value = value;
            document.getElementById('mainFilterForm').submit();
        }

        // Debounced search
        let searchTimer;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                document.getElementById('mainFilterForm').submit();
            }, 600);
        });

        // Quantity sync with max stock enforcement
        let prevQtyValue = 1;

        const qtyInput = document.getElementById('modalQuantity');

        qtyInput.addEventListener('focus', function() {
            prevQtyValue = parseInt(this.value) || 1;
        });

        qtyInput.addEventListener('input', function() {
            const max = parseInt(this.max) || 0;
            let val = parseInt(this.value) || 1;
            const warning = document.getElementById('qtyWarning');

            if (val > max) {
                val = max;
                this.value = max;
                warning.style.display = 'block';
            } else if (val >= max && prevQtyValue >= max) {
                warning.style.display = 'block';
            } else if (val < 1) {
                val = 1;
                this.value = 1;
                warning.style.display = 'none';
            } else {
                warning.style.display = 'none';
            }
            prevQtyValue = val;
            document.getElementById('modalQuantityInput').value = val;
        });

        qtyInput.addEventListener('change', function() {
            const max = parseInt(this.max) || 0;
            let val = parseInt(this.value) || 1;
            const warning = document.getElementById('qtyWarning');

            if (val >= max) {
                val = max;
                this.value = max;
                warning.style.display = 'block';
            } else if (val < 1) {
                val = 1;
                this.value = 1;
                warning.style.display = 'none';
            } else {
                warning.style.display = 'none';
            }
            prevQtyValue = val;
            document.getElementById('modalQuantityInput').value = val;
        });

        qtyInput.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowUp') {
                const max = parseInt(this.max) || 0;
                const val = parseInt(this.value) || 1;
                if (val >= max) {
                    e.preventDefault();
                    this.value = max;
                    document.getElementById('qtyWarning').style.display = 'block';
                    document.getElementById('modalQuantityInput').value = max;
                }
            } else if (e.key === 'ArrowDown') {
                document.getElementById('qtyWarning').style.display = 'none';
            }
        });

        function openQuickView(productId) {
            const product = products.find(p => p.id === productId);
            if (!product) return;
            currentModalProductId = productId;
            selectedSize = null;

            document.getElementById('modalAddToCartForm').action = '/cart/add/' + productId;
            document.getElementById('modalImage').src = product.image_path;
            document.getElementById('modalImage').alt = product.name;
            document.getElementById('modalCategory').textContent = product.category ? product.category.name : '';
            document.getElementById('modalName').textContent = product.name;
            document.getElementById('modalPrice').textContent = 'IDR ' + new Intl.NumberFormat('id-ID').format(product
                .price);
            document.getElementById('modalDescription').textContent = product.description || 'No description available.';
            document.getElementById('modalQuantity').value = '1';
            document.getElementById('modalQuantityInput').value = '1';
            document.getElementById('modalSizeInput').value = '';

            // Rating
            const ratingDiv = document.getElementById('modalRating');
            const avgRating = parseFloat(product.reviews_avg_rating) || 0;
            const countReviews = parseInt(product.reviews_count) || 0;

            if (countReviews > 0) {
                ratingDiv.style.display = 'flex';
                let starsHtml = '<div class="flex gap-0.5 text-black">';
                for (let i = 1; i <= 5; i++) {
                    const isFilled = i <= Math.round(avgRating);
                    starsHtml += `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ${isFilled ? 'fill-current text-black' : 'text-stone-200'}" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    `;
                }
                starsHtml += `</div><span class="text-[10px] text-stone-400 font-mono">(${countReviews} ${countReviews === 1 ? 'review' : 'reviews'})</span>`;
                ratingDiv.innerHTML = starsHtml;
            } else {
                ratingDiv.style.display = 'none';
                ratingDiv.innerHTML = '';
            }

            // Gender badge
            const genderDiv = document.getElementById('modalGender');
            const g = (product.gender || '').toLowerCase();
            const symbol = g === 'male' ? '♂' : g === 'female' ? '♀' : '⚥';
            const cls = g === 'male' ? 'gender-male' : g === 'female' ? 'gender-female' : 'gender-unisex';
            genderDiv.innerHTML = '<span class="gender-badge ' + cls + '">' + symbol + ' ' + (product.gender || 'Unisex') +
                '</span>';

            // Size buttons
            const sizesDiv = document.getElementById('modalSizes');
            const sizeSection = document.getElementById('modalSizeSection');
            const variants = product.variants || [];

            if (variants.length > 0) {
                sizeSection.style.display = 'block';
                sizesDiv.innerHTML = '';
                variants.forEach(v => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'size-btn' + (v.stock <= 0 ? ' disabled' : '');
                    btn.textContent = v.size_label;
                    btn.dataset.size = v.size_label;
                    btn.dataset.stock = v.stock;
                    if (v.stock > 0) {
                        btn.onclick = function() {
                            selectSize(this, v.size_label, v.stock);
                        };
                    }
                    sizesDiv.appendChild(btn);
                });
            } else {
                sizeSection.style.display = 'none';
            }

            // Stock info
            const totalStock = variants.reduce((s, v) => s + v.stock, 0);
            document.getElementById('modalQuantity').max = totalStock;
            document.getElementById('qtyWarning').style.display = 'none';

            // Disable QTY input until a size is selected (if product has variants)
            const qtyEl = document.getElementById('modalQuantity');
            if (variants.length > 0) {
                qtyEl.disabled = true;
            } else {
                qtyEl.disabled = totalStock <= 0;
            }

            document.getElementById('sizeStockInfo').textContent = variants.length > 0 ? 'Please select a size' : '';

            // Disable buttons if variants exist but none selected, or if sold out
            updateActionButtons(variants.length > 0 ? false : totalStock > 0);

            document.getElementById('quickViewModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Initialize Review Slider
            initReviewSlider(product);
        }

        function selectSize(btn, sizeLabel, stock) {
            selectedSize = sizeLabel;
            document.querySelectorAll('#modalSizes .size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.getElementById('modalSizeInput').value = sizeLabel;
            document.getElementById('modalQuantity').max = stock;
            document.getElementById('modalQuantity').value = '1';
            document.getElementById('modalQuantity').disabled = false;
            document.getElementById('modalQuantityInput').value = '1';
            document.getElementById('sizeStockInfo').textContent = stock + ' available in size ' + sizeLabel;
            document.getElementById('qtyWarning').style.display = 'none';
            updateActionButtons(true);
        }

        function updateActionButtons(enabled) {
            document.getElementById('addToCartBtn').disabled = !enabled;
            document.getElementById('buyNowBtn').disabled = !enabled;
        }

        function buyNow() {
            if (!currentModalProductId) return;
            document.getElementById('modalQuantityInput').value = document.getElementById('modalQuantity').value;
            const form = document.getElementById('modalAddToCartForm');
            form.action = '/direct-checkout/' + currentModalProductId;
            form.submit();
        }

        function closeQuickView() {
            document.getElementById('quickViewModal').classList.add('hidden');
            document.body.style.overflow = '';
            destroyReviewSlider();
        }

        // ===== REVIEW SLIDER SYSTEM =====
        let reviewSliderInterval = null;
        let reviewCurrentIndex = 0;
        let reviewItems = [];

        function initReviewSlider(product) {
            destroyReviewSlider();

            const overlay = document.getElementById('reviewOverlay');
            const container = document.getElementById('reviewSlideContainer');
            const dotsContainer = document.getElementById('reviewProgressDots');
            const titleEl = document.getElementById('reviewOverlayTitle');

            container.innerHTML = '';
            dotsContainer.innerHTML = '';
            overlay.classList.remove('visible');

            const reviews = (product.reviews || []).filter(r => r.comment && r.comment.trim() !== '');

            if (reviews.length === 0) {
                overlay.style.display = 'none';
                return;
            }

            overlay.style.display = '';
            titleEl.textContent = `Customer Reviews (${reviews.length})`;

            const avatarClasses = ['', 'av-1', 'av-2', 'av-3', 'av-4'];

            reviews.forEach((review, index) => {
                const userName = review.user ? review.user.name : 'Anonymous';
                const initials = userName.split(' ').map(w => w[0]).join('').substring(0, 2);
                const avatarClass = avatarClasses[index % avatarClasses.length];
                const rating = parseInt(review.rating) || 5;

                let starsHtml = '<div class="review-stars">';
                for (let i = 1; i <= 5; i++) {
                    starsHtml += `<svg viewBox="0 0 20 20" class="${i <= rating ? 'star-filled' : 'star-empty'}"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>`;
                }
                starsHtml += '</div>';

                const slide = document.createElement('div');
                slide.className = 'review-slide-item';
                slide.innerHTML = `
                    <div class="review-user-row">
                        <div class="review-avatar ${avatarClass}">${initials}</div>
                        <div class="review-user-info">
                            <div class="review-user-name">${userName}</div>
                            ${starsHtml}
                        </div>
                    </div>
                    <div class="review-comment">${review.comment}</div>
                `;
                container.appendChild(slide);
            });

            // Create progress dots
            reviews.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.className = 'review-dot' + (index === 0 ? ' active' : '');
                dotsContainer.appendChild(dot);
            });

            reviewItems = container.querySelectorAll('.review-slide-item');
            reviewCurrentIndex = 0;

            // Stagger reveal: delay the overlay appearance
            setTimeout(() => {
                overlay.classList.add('visible');
            }, 400);

            // Show first review with a slight delay for the "pop-up" feel
            setTimeout(() => {
                if (reviewItems[0]) {
                    reviewItems[0].classList.add('active');
                }
            }, 700);

            // Auto-cycle if more than 1 review
            if (reviews.length > 1) {
                reviewSliderInterval = setInterval(() => {
                    advanceReviewSlide();
                }, 4000);
            }
        }

        function advanceReviewSlide() {
            if (reviewItems.length === 0) return;

            const currentSlide = reviewItems[reviewCurrentIndex];
            const dots = document.querySelectorAll('#reviewProgressDots .review-dot');

            // Exit current slide upward
            currentSlide.classList.remove('active');
            currentSlide.classList.add('exit-up');

            // Calculate next index
            const nextIndex = (reviewCurrentIndex + 1) % reviewItems.length;

            // After exit transition, reset and show next
            setTimeout(() => {
                currentSlide.classList.remove('exit-up');

                // Activate next slide (slides in from bottom)
                reviewItems[nextIndex].classList.add('active');

                // Update dots
                dots.forEach((d, i) => {
                    d.classList.toggle('active', i === nextIndex);
                });

                reviewCurrentIndex = nextIndex;
            }, 350);
        }

        function destroyReviewSlider() {
            if (reviewSliderInterval) {
                clearInterval(reviewSliderInterval);
                reviewSliderInterval = null;
            }
            reviewCurrentIndex = 0;
            reviewItems = [];
        }

        // Sync card heart icons with the global wishlistItems array
        function syncCardHearts() {
            // Reset all cards first
            document.querySelectorAll('[id^="wishlist-heart-"]').forEach(btn => {
                btn.classList.add('text-gray-400', 'hover:text-red-500');
                btn.classList.remove('text-red-500');
                const svg = btn.querySelector('svg');
                if (svg) {
                    svg.setAttribute('fill', 'none');
                    svg.style.fill = 'none';
                }
            });

            // Check against global wishlistItems array
            if (typeof wishlistItems !== 'undefined' && wishlistItems.length > 0) {
                wishlistItems.forEach(item => {
                    const btn = document.getElementById('wishlist-heart-' + item.id);
                    if (btn) {
                        btn.classList.remove('text-gray-400', 'hover:text-red-500');
                        btn.classList.add('text-red-500');
                        const svg = btn.querySelector('svg');
                        if (svg) {
                            svg.setAttribute('fill', '#ef4444');
                            svg.style.fill = '#ef4444';
                        }
                    }
                });
            }
        }

        // Check query param for quickview on load
        document.addEventListener('DOMContentLoaded', () => {
            // Sync hearts initial call
            syncCardHearts();

            // Check query params
            const urlParams = new URLSearchParams(window.location.search);
            const quickviewId = urlParams.get('quickview');
            if (quickviewId) {
                openQuickView(parseInt(quickviewId));
                // Clean URL
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + (
                    window.location.search.replace(/quickview=\d+&?/, '').replace(/\?$/, ''));
                window.history.replaceState({
                    path: newUrl
                }, '', newUrl);
            }
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeQuickView();
        });
    </script>
@endsection
