@extends('base.base')

@section('content')
    <!-- Swiper.js CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        html {
            scroll-behavior: smooth;
        }

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

        .event-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.625rem;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-radius: 2px;
            font-weight: 600;
            transition: all 0.3s;
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

        /* ===== Review Overlay Widget (Redesigned) ===== */
        .review-badge {
            position: absolute;
            bottom: 16px;
            left: 16px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 2px;
            padding: 8px 12px;
            font-size: 10px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-weight: 600;
            color: #1c1917; /* stone-900 */
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 20;
        }

        .review-badge:hover {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .review-overlay {
            position: absolute;
            bottom: 16px;
            left: 16px;
            width: 280px;
            max-width: calc(100% - 32px);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 2px;
            padding: 16px;
            z-index: 20;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            opacity: 0;
            transform: translateY(8px);
            transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        .review-overlay.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .review-close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #78716c; /* stone-500 */
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 2px;
            transition: color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .review-close-btn:hover {
            color: #000;
        }

        .review-slide-container {
            position: relative;
            min-height: 64px;
            overflow: hidden;
        }

        .review-slide-item {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            opacity: 0;
            transform: translateY(100%);
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
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
            gap: 8px;
            margin-bottom: 6px;
        }

        .review-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            font-weight: 600;
            color: #1c1917; /* stone-900 */
            background: #f5f5f4; /* stone-100 */
            border: 1px solid #e7e5e4; /* stone-200 */
            text-transform: uppercase;
            letter-spacing: 0.02em;
            flex-shrink: 0;
        }

        .review-user-info {
            flex: 1;
            min-width: 0;
        }

        .review-user-name {
            font-size: 11px;
            font-weight: 600;
            color: #1c1917; /* stone-900 */
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .review-stars {
            display: flex;
            gap: 1.5px;
            margin-top: 1px;
        }

        .review-stars svg {
            width: 9.5px;
            height: 9.5px;
        }

        .review-stars .star-filled {
            color: #d97706; /* amber-600 */
            fill: #d97706;
        }

        .review-stars .star-empty {
            color: #e7e5e4; /* stone-200 */
            fill: #e7e5e4;
        }

        .review-comment {
            font-size: 11px;
            line-height: 1.4;
            color: #44403c; /* stone-700 */
            font-style: normal;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            letter-spacing: 0.01em;
            margin-top: 4px;
        }

        .review-view-all-btn {
            font-size: 9px;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            font-weight: 600;
            color: #78716c; /* stone-500 */
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            transition: color 0.2s;
            display: inline-block;
            margin-top: 8px;
        }

        .review-view-all-btn:hover {
            color: #000;
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .review-no-reviews {
            display: none;
        }

        .carousel-container {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .carousel-container::-webkit-scrollbar {
            display: none;
        }
        .carousel-item {
            flex: 0 0 100%;
            scroll-snap-align: center;
        }
        .carousel-nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(0, 0, 0, 0.1);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 20;
            transition: all 0.3s;
            color: #000;
        }
        .carousel-nav-btn:hover {
            background: #000;
            color: #fff;
        }
        .carousel-nav-prev { left: 16px; }
        .carousel-nav-next { right: 16px; }

        /* Progress tracks at the bottom of the carousel */
        .carousel-progress-track {
            position: relative;
            height: 4px;
            background: rgba(0, 0, 0, 0.05);
            cursor: pointer;
            overflow: hidden;
            border-radius: 9999px;
            flex: 1;
            max-width: 120px;
            transition: background 0.3s;
        }
        .carousel-progress-track:hover {
            background: rgba(0, 0, 0, 0.12);
        }
        .carousel-progress-bar {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 0;
        }
    </style>

    <div class="pt-28 pb-24 px-4 lg:px-8 bg-stone-50 min-h-screen">
        <div class="max-w-[1400px] mx-auto">
            
            {{-- Dynamic Event Banner / Fallback Section --}}
            <div class="mb-12 w-full relative">
                @if($activeEvents->isNotEmpty())
                    <div class="swiper swiper-horizontal-event rounded-lg overflow-hidden">
                        <div class="swiper-wrapper">
                            @foreach($activeEvents as $event)
                                <div class="swiper-slide relative overflow-hidden border p-8 md:p-12 transition-all duration-500 shadow-sm"
                                     style="background-color: {{ $event->theme_color ?? '#000000' }}; color: {{ $event->text_color ?? '#ffffff' }}; border-color: {{ ($event->text_color ?? '#ffffff') }}44;">
                                    {{-- Decorative pattern / background image with overlay --}}
                                    @if($event->background_image)
                                        <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-25 pointer-events-none" style="background-image: url('{{ asset('storage/' . $event->background_image) }}')"></div>
                                    @elseif($event->banner_image)
                                        <div class="absolute inset-0 bg-cover bg-center mix-blend-overlay opacity-25 pointer-events-none" style="background-image: url('{{ $event->banner_image }}')"></div>
                                    @endif
                                    
                                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                                        <div>
                                            <div class="inline-flex items-center gap-2 border px-3 py-1 mb-4 text-[9px] tracking-[0.25em] uppercase font-semibold" style="border-color: currentColor;">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                                                Limited Time Event
                                            </div>
                                            <h2 class="text-3xl md:text-5xl font-serif tracking-[0.1em] mb-4 uppercase">
                                                {{ $event->display_title ?? $event->name }}
                                            </h2>
                                            <p class="text-xs md:text-sm tracking-widest font-light mb-6 opacity-90">
                                                @if($event->display_description)
                                                    {{ $event->display_description }}
                                                @else
                                                    EXCLUSIVE OFFERS FROM {{ $event->start_date->format('M d, Y') }} UNTIL {{ $event->end_date->format('M d, Y') }}
                                                @endif
                                            </p>
                                            <a href="{{ route('collections.index', ['filter_event' => $event->id]) }}#product-grid"
                                               class="inline-block transition-all text-[10px] tracking-[0.2em] px-8 py-3.5 font-medium border duration-300"
                                               style="background-color: {{ $event->text_color ?? '#ffffff' }}; color: {{ $event->theme_color ?? '#000000' }}; border-color: {{ $event->text_color ?? '#ffffff' }};"
                                               onmouseover="this.style.backgroundColor='transparent'; this.style.color='{{ $event->text_color ?? '#ffffff' }}'"
                                               onmouseout="this.style.backgroundColor='{{ $event->text_color ?? '#ffffff' }}'; this.style.color='{{ $event->theme_color ?? '#000000' }}'">
                                                EXPLORE COLLECTION
                                            </a>
                                        </div>
                                        
                                        @if($event->main_image || $event->banner_image)
                                            <div class="hidden lg:block relative aspect-[16/9] w-full overflow-hidden border" style="border-color: {{ ($event->text_color ?? '#ffffff') }}22;">
                                                <img src="{{ $event->main_image ? asset('storage/' . $event->main_image) : $event->banner_image }}" alt="{{ $event->display_title ?? $event->name }}" class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-700">
                                                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if($activeEvents->count() > 1)
                        <button type="button" class="carousel-nav-btn carousel-nav-prev" onclick="window.swiperHorizontal.slidePrev()" aria-label="Previous event slide">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                        </button>
                        <button type="button" class="carousel-nav-btn carousel-nav-next" onclick="window.swiperHorizontal.slideNext()" aria-label="Next event slide">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>

                        {{-- Progress bars (tracks) for Valorant Store style --}}
                        <div class="carousel-progress-container flex justify-center items-center gap-4 mt-6">
                            @foreach($activeEvents as $index => $event)
                                <button type="button" class="carousel-progress-track relative h-1 bg-gray-200 cursor-pointer overflow-hidden rounded-full flex-1 max-w-[120px] focus:outline-none" onclick="jumpToSlide({{ $index }})" aria-label="Go to event slide {{ $index + 1 }}">
                                    <div class="carousel-progress-bar absolute left-0 top-0 bottom-0 w-0 transition-all duration-100 ease-linear" id="progressBar-{{ $index }}" style="background-color: {{ $event->theme_color ?? '#000000' }};"></div>
                                </button>
                            @endforeach
                        </div>
                    @endif
                @else
                    {{-- Fallback Default Banner (New Arrivals) --}}
                    <div class="relative overflow-hidden bg-gradient-to-r from-stone-900 via-neutral-900 to-stone-900 text-white border border-stone-800 p-8 md:p-12 rounded-lg shadow-sm">
                        <div class="relative z-10 flex flex-col items-center text-center py-6">
                            <span class="text-xs tracking-[0.4em] text-amber-400/90 uppercase mb-3 font-semibold">Exclusively VESTA</span>
                            <h2 class="text-3xl md:text-5xl font-serif tracking-[0.2em] mb-4 uppercase">NEW ARRIVALS</h2>
                            <p class="text-xs md:text-sm tracking-widest font-light mb-8 max-w-xl opacity-80 leading-relaxed">
                                Discover the latest additions to our luxury apparel collection. Crafted with exceptional detail and timeless aesthetics.
                            </p>
                            <div class="flex gap-4">
                                <a href="#product-grid"
                                   class="bg-white text-stone-900 hover:bg-stone-900 hover:text-white border border-white transition-all text-[10px] tracking-[0.2em] px-8 py-3.5 font-medium">
                                    SHOP NEW DESIGNS
                                </a>
                            </div>
                        </div>
                        {{-- subtle decorative luxury ring --}}
                        <div class="absolute -right-20 -bottom-20 w-96 h-96 border border-white/5 rounded-full pointer-events-none"></div>
                        <div class="absolute -left-20 -top-20 w-96 h-96 border border-white/5 rounded-full pointer-events-none"></div>
                    </div>
                @endif
            </div>

            {{-- Page Header --}}
            <div class="text-center mb-12" id="all-products-section">
                <span class="text-xs tracking-[0.3em] text-gray-400 uppercase">Our Selection</span>
                <h1 class="text-4xl md:text-5xl font-serif tracking-[0.15em] mt-4">THE COLLECTION</h1>
                <div class="w-16 h-px bg-black mx-auto mt-6"></div>
            </div>

            {{-- Search Bar --}}
            <div class="max-w-2xl mx-auto mb-10">
                <form id="mainFilterForm" action="{{ route('catalog') }}" method="GET">
                    <div class="relative">
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            placeholder="Search by name, description, or SKU..."
                            class="w-full border border-gray-300 pl-12 pr-4 py-3.5 text-sm focus:outline-none focus:border-black transition-colors bg-white">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <div id="search-clear-container">
                            @if (request('search'))
                                <a href="{{ route('catalog') }}"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Hidden inputs for all filters --}}
                    <input type="hidden" name="category" id="filterCategory" value="{{ request('category') }}">
                    <input type="hidden" name="gender" id="filterGender" value="{{ request('gender') }}">
                    <input type="hidden" name="size" id="filterSize" value="{{ request('size') }}">
                    <input type="hidden" name="sort" id="filterSort" value="{{ request('sort', 'newest') }}">
                    <input type="hidden" name="filter_event" id="filterEvent" value="{{ request('filter_event') }}">
                </form>
            </div>

            <div class="flex-1 max-w-[1400px] mx-auto w-full">
                {{-- Horizontal Sticky Filter Bar --}}
                <div id="filter-container" class="sticky top-0 z-40 bg-stone-50 border-y border-gray-200 py-3 mb-8 shadow-sm">
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

                            @if (request('category') || request('gender') || request('size') || request('search') || request('filter_event'))
                                <a href="{{ route('collections.index') }}"
                                    class="text-[10px] tracking-widest text-gray-500 hover:text-black transition-colors uppercase ml-2 underline underline-offset-4">Clear
                                    Filters</a>
                            @endif
                        </div>

                        <div class="flex items-center justify-between md:justify-end gap-6 shrink-0">
                            <span id="result-count" class="text-xs text-gray-500 whitespace-nowrap"><span
                                    class="text-black font-medium">{{ $products->total() }}</span> Results</span>

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
                <div class="w-full" id="product-grid">
                    @if($filteredEvent)
                        <div class="mb-8 p-6 bg-white border border-stone-200 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-sm">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full animate-pulse" style="background-color: {{ $filteredEvent->theme_color ?? '#000000' }};"></span>
                                <span class="text-xs tracking-[0.1em] text-stone-600 uppercase">
                                    Viewing items from: <strong class="text-stone-900 font-semibold">{{ $filteredEvent->name }}</strong>
                                </span>
                            </div>
                            <a href="{{ route('collections.index') }}#product-grid" 
                               class="inline-block text-center border border-stone-950 text-[10px] tracking-[0.2em] px-6 py-3 font-semibold text-stone-950 hover:bg-stone-950 hover:text-white transition-all duration-300 uppercase">
                                View All Products
                            </a>
                        </div>
                    @endif

                    @if ($products->isEmpty())
                        <div class="text-center py-24">
                            <svg class="mx-auto h-16 w-16 text-gray-300 mb-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <p class="text-sm tracking-widest text-gray-400 uppercase mb-4">No products found</p>
                            <a href="{{ route('catalog') }}"
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

                                        {{-- Badges Wrapper (Gender & Event) --}}
                                        <div class="absolute top-[10px] left-[10px] z-10 flex flex-row gap-2 items-center">
                                            @php $gc = strtolower($product->gender ?? ''); @endphp
                                            <span class="gender-badge {{ $gc == 'male' ? 'gender-male' : ($gc == 'female' ? 'gender-female' : 'gender-unisex') }}">
                                                @if ($gc == 'male')
                                                    ♂
                                                @elseif($gc == 'female')
                                                    ♀
                                                @else
                                                    ⚥
                                                @endif
                                                {{ $product->gender }}
                                            </span>

                                            @php
                                                $productEvent = null;
                                                if (isset($filteredEvent) && $filteredEvent) {
                                                    $productEvent = $product->events->firstWhere('id', $filteredEvent->id);
                                                }
                                                if (!$productEvent) {
                                                    $productEvent = $product->events->first(function($ev) use ($activeEvents) {
                                                        return $activeEvents->contains('id', $ev->id);
                                                    });
                                                }
                                            @endphp
                                            @if ($productEvent)
                                                <span class="event-badge" 
                                                      style="background-color: {{ $productEvent->theme_color ?? '#0d9488' }}; 
                                                             color: {{ $productEvent->text_color ?? '#ffffff' }};
                                                             border: 1px solid {{ ($productEvent->text_color ?? '#ffffff') }}22;">
                                                    {{ $productEvent->short_name ?? $productEvent->name }}
                                                </span>
                                            @endif
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

                        {{-- Pagination Links --}}
                        @if ($products->hasPages())
                            <nav class="mt-16 flex justify-center items-center gap-4" role="navigation" aria-label="Pagination Navigation">
                                {{-- Previous Page Link --}}
                                @if ($products->onFirstPage())
                                    <span class="px-5 py-3 text-[10px] tracking-widest text-gray-300 border border-gray-100 cursor-not-allowed uppercase font-medium">PREV</span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="px-5 py-3 text-[10px] tracking-widest text-stone-700 hover:text-black border border-gray-200 hover:border-black transition-colors uppercase font-medium">PREV</a>
                                @endif

                                {{-- Page Numbers --}}
                                <div class="flex items-center gap-1.5">
                                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                        @if ($page == $products->currentPage())
                                            <span class="w-10 h-10 flex items-center justify-center text-xs font-semibold bg-stone-900 text-white border border-stone-900">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center text-xs text-stone-600 hover:text-black border border-gray-200 hover:border-stone-400 transition-colors">{{ $page }}</a>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Next Page Link --}}
                                @if ($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="px-5 py-3 text-[10px] tracking-widest text-stone-700 hover:text-black border border-gray-200 hover:border-black transition-colors uppercase font-medium">NEXT</a>
                                @else
                                    <span class="px-5 py-3 text-[10px] tracking-widest text-gray-300 border border-gray-100 cursor-not-allowed uppercase font-medium">NEXT</span>
                                @endif
                            </nav>
                        @endif
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
                <div class="bg-gray-100 overflow-hidden relative flex items-center justify-center">
                    <img id="modalImage" src="" alt="" class="w-full h-full object-cover">

                    {{-- Review Badge (Minimized Mode) --}}
                    <div id="reviewBadge" class="review-badge" onclick="expandReview()">
                        <span class="text-amber-500">★</span>
                        <span id="reviewBadgeText">Reviews (0)</span>
                    </div>

                    {{-- Review Overlay Widget (Expanded Mode) --}}
                    <div id="reviewOverlay" class="review-overlay">
                        <button type="button" onclick="minimizeReview()" class="review-close-btn" aria-label="Minimize Reviews">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="review-slide-container" id="reviewSlideContainer">
                            {{-- Reviews will be injected here by JS --}}
                        </div>
                        <div class="border-t border-stone-100 pt-2 flex justify-between items-center">
                            <button type="button" onclick="scrollToFullReviews()" class="review-view-all-btn">
                                View All Reviews
                            </button>
                        </div>
                    </div>
                </div>

                <div id="modalRightPane" class="p-8 md:p-12 lg:p-14 flex flex-col justify-start overflow-y-auto">
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

                    <!-- Full Reviews Section -->
                    <div id="fullReviewsSection" class="mt-12 border-t border-stone-200 pt-8" style="display: none;">
                        <h3 class="text-sm tracking-[0.2em] font-serif uppercase mb-6">Customer Reviews</h3>
                        
                        <!-- Top rating summary & breakdown grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 mb-6 pb-6 border-b border-stone-100">
                            <div class="sm:col-span-5 flex flex-col items-center justify-center text-center p-5 bg-stone-50 border border-stone-100 rounded-sm">
                                <span id="fullReviewsAvgRating" class="text-4xl font-serif text-stone-900 mb-1">0.0</span>
                                <div id="fullReviewsStars" class="flex gap-0.5 mb-1.5 text-stone-900">
                                    <!-- stars will be populated here -->
                                </div>
                                <span id="fullReviewsCountText" class="text-[10px] tracking-wider text-stone-500 uppercase font-mono">0 Reviews</span>
                            </div>
                            <div class="sm:col-span-7 flex flex-col justify-center">
                                <div id="ratingBreakdownContainer" class="space-y-2">
                                    <!-- Progress bars for 5, 4, 3, 2, 1 stars -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- List of reviews -->
                        <div id="fullReviewsList" class="divide-y divide-stone-100">
                            <!-- populated dynamically -->
                        </div>
                        
                        <!-- Pagination controls -->
                        <div id="fullReviewsPagination" class="mt-6 pt-4 border-t border-stone-100 flex justify-center items-center gap-3">
                            <!-- populated dynamically -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $allPageProducts = collect($products->items())->unique('id')->values();
    @endphp
    <script type="application/json" id="productsData">{!! json_encode($allPageProducts) !!}</script>

    <script>
        let productsData = JSON.parse(document.getElementById('productsData').textContent);
        let products = Array.isArray(productsData) ? productsData : (productsData.data || []);
        let currentModalProductId = null;
        let selectedSize = null;
        let currentReviewsArray = [];
        let currentReviewPage = 1;

        // Filter system
        function setFilter(name, value) {
            document.getElementById('filter' + name.charAt(0).toUpperCase() + name.slice(1)).value = value;
            updateCatalog();
        }

        // Debounced search
        let searchTimer;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                updateCatalog();
            }, 500);
        });

        // AJAX update catalog function
        function updateCatalog(url = null, pushState = true) {
            const form = document.getElementById('mainFilterForm');
            let fetchUrl = url;
            
            if (!fetchUrl) {
                const formData = new FormData(form);
                const params = new URLSearchParams();
                for (const [key, val] of formData.entries()) {
                    if (val) {
                        params.append(key, val);
                    }
                }
                fetchUrl = form.action + '?' + params.toString();
            }

            const grid = document.getElementById('product-grid');
            if (grid) {
                grid.style.opacity = '0.5';
                grid.style.transition = 'opacity 0.2s';
            }

            fetch(fetchUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Update product-grid
                const newGrid = doc.getElementById('product-grid');
                if (newGrid && grid) {
                    grid.innerHTML = newGrid.innerHTML;
                }
                
                // Update result count
                const newCount = doc.getElementById('result-count');
                const currentCount = document.getElementById('result-count');
                if (newCount && currentCount) {
                    currentCount.innerHTML = newCount.innerHTML;
                }
                
                // Update filter-container
                const newFilterContainer = doc.getElementById('filter-container');
                const currentFilterContainer = document.getElementById('filter-container');
                if (newFilterContainer && currentFilterContainer) {
                    currentFilterContainer.innerHTML = newFilterContainer.innerHTML;
                }
                
                // Update search clear container
                const newClear = doc.getElementById('search-clear-container');
                const currentClear = document.getElementById('search-clear-container');
                if (newClear && currentClear) {
                    currentClear.innerHTML = newClear.innerHTML;
                }
                
                // Update products array for Quick View
                const newDataEl = doc.getElementById('productsData');
                if (newDataEl) {
                    const newProductsData = JSON.parse(newDataEl.textContent);
                    products = Array.isArray(newProductsData) ? newProductsData : (newProductsData.data || []);
                }
                
                // Update URL
                if (pushState) {
                    window.history.pushState(null, '', fetchUrl);
                }
                
                // Sync wishlist hearts
                syncCardHearts();
                
                if (grid) grid.style.opacity = '1';
            })
            .catch(err => {
                console.error('Failed to load products:', err);
                if (grid) grid.style.opacity = '1';
            });
        }

        // Intercept form submission
        document.getElementById('mainFilterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            updateCatalog();
        });

        // Intercept pagination clicks
        document.getElementById('product-grid').addEventListener('click', function(e) {
            const anchor = e.target.closest('a');
            if (anchor && anchor.closest('nav[role="navigation"]')) {
                e.preventDefault();
                const url = anchor.getAttribute('href');
                if (url && url !== '#') {
                    updateCatalog(url);
                    const scrollTarget = document.getElementById('all-products-section');
                    if (scrollTarget) {
                        scrollTarget.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            }
        });

        // Intercept clear and clear filters clicks
        document.addEventListener('click', function(e) {
            const clearBtn = e.target.closest('#search-clear-container a');
            if (clearBtn) {
                e.preventDefault();
                document.getElementById('searchInput').value = '';
                document.getElementById('filterCategory').value = '';
                document.getElementById('filterGender').value = '';
                document.getElementById('filterSize').value = '';
                document.getElementById('filterSort').value = 'newest';
                document.getElementById('filterEvent').value = '';
                updateCatalog();
            }

            const clearFiltersBtn = e.target.closest('#filter-container a');
            if (clearFiltersBtn && clearFiltersBtn.textContent.trim().toLowerCase() === 'clear filters') {
                e.preventDefault();
                document.getElementById('searchInput').value = '';
                document.getElementById('filterCategory').value = '';
                document.getElementById('filterGender').value = '';
                document.getElementById('filterSize').value = '';
                document.getElementById('filterSort').value = 'newest';
                document.getElementById('filterEvent').value = '';
                updateCatalog();
            }
        });

        // Listen for history back/forward navigation
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            document.getElementById('searchInput').value = urlParams.get('search') || '';
            document.getElementById('filterCategory').value = urlParams.get('category') || '';
            document.getElementById('filterGender').value = urlParams.get('gender') || '';
            document.getElementById('filterSize').value = urlParams.get('size') || '';
            document.getElementById('filterSort').value = urlParams.get('sort') || 'newest';
            document.getElementById('filterEvent').value = urlParams.get('filter_event') || '';
            
            updateCatalog(window.location.href, false);
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

            const badge = document.getElementById('reviewBadge');
            const overlay = document.getElementById('reviewOverlay');
            const container = document.getElementById('reviewSlideContainer');
            const fullReviewsSection = document.getElementById('fullReviewsSection');

            container.innerHTML = '';
            
            // Set initial visibility states
            badge.style.display = 'none';
            badge.style.opacity = '0';
            overlay.style.display = 'none';
            overlay.classList.remove('visible');
            fullReviewsSection.style.display = 'none';

            const reviews = (product.reviews || []).filter(r => r.comment && r.comment.trim() !== '');

            if (reviews.length === 0) {
                return;
            }

            // Calculate average rating
            const avgRating = parseFloat(product.reviews_avg_rating) || 0;
            const countReviews = reviews.length;

            // Set badge content
            const badgeTextEl = document.getElementById('reviewBadgeText');
            badgeTextEl.textContent = `${countReviews} Reviews (${avgRating.toFixed(1)} ★)`;
            
            // Show minimized badge by default
            badge.style.display = 'flex';
            // Trigger reflow & fade-in
            badge.offsetHeight;
            badge.style.opacity = '1';

            // Populate the slides
            reviews.forEach((review, index) => {
                const userName = review.user ? review.user.name : 'Anonymous';
                const initials = userName.split(' ').map(w => w[0]).join('').substring(0, 2);
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
                        <div class="review-avatar">${initials}</div>
                        <div class="review-user-info">
                            <div class="review-user-name">${userName}</div>
                            ${starsHtml}
                        </div>
                    </div>
                    <div class="review-comment">${review.comment}</div>
                `;
                container.appendChild(slide);
            });

            reviewItems = container.querySelectorAll('.review-slide-item');
            reviewCurrentIndex = 0;

            // Show first review slide instantly inside container
            if (reviewItems[0]) {
                reviewItems[0].classList.add('active');
            }

            // Auto-cycle if more than 1 review
            if (reviews.length > 1) {
                reviewSliderInterval = setInterval(() => {
                    advanceReviewSlide();
                }, 4000);
            }

            // Setup Full Reviews Section
            renderFullReviewsSection(product, reviews);
        }

        function advanceReviewSlide() {
            if (reviewItems.length === 0) return;

            const currentSlide = reviewItems[reviewCurrentIndex];

            // Exit current slide upward
            currentSlide.classList.remove('active');
            currentSlide.classList.add('exit-up');

            // Calculate next index
            const nextIndex = (reviewCurrentIndex + 1) % reviewItems.length;

            // After exit transition, reset and show next
            setTimeout(() => {
                currentSlide.classList.remove('exit-up');

                // Activate next slide (slides in from bottom)
                if (reviewItems[nextIndex]) {
                    reviewItems[nextIndex].classList.add('active');
                }
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

        // Mode toggling functions
        function expandReview() {
            const badge = document.getElementById('reviewBadge');
            const overlay = document.getElementById('reviewOverlay');
            
            badge.style.opacity = '0';
            setTimeout(() => {
                badge.style.display = 'none';
                overlay.style.display = 'block';
                // Force reflow
                overlay.offsetHeight;
                overlay.classList.add('visible');
            }, 200);
        }

        function minimizeReview() {
            const badge = document.getElementById('reviewBadge');
            const overlay = document.getElementById('reviewOverlay');
            
            overlay.classList.remove('visible');
            setTimeout(() => {
                overlay.style.display = 'none';
                badge.style.display = 'flex';
                // Force reflow
                badge.offsetHeight;
                badge.style.opacity = '1';
            }, 300);
        }

        // Helper: Date formatter
        function formatReviewDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // Full reviews scroll helper
        function scrollToFullReviews() {
            const rightPane = document.getElementById('modalRightPane');
            const target = document.getElementById('fullReviewsSection');
            if (target && rightPane) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Render full reviews section breakdown and pagination
        function renderFullReviewsSection(product, reviews) {
            currentReviewsArray = reviews;
            currentReviewPage = 1;

            const fullReviewsSection = document.getElementById('fullReviewsSection');
            fullReviewsSection.style.display = 'block';

            // Overall score
            const avgRating = parseFloat(product.reviews_avg_rating) || 0;
            const total = reviews.length;
            document.getElementById('fullReviewsAvgRating').textContent = avgRating.toFixed(1);
            document.getElementById('fullReviewsCountText').textContent = `${total} ${total === 1 ? 'Review' : 'Reviews'}`;

            // Overall stars representation
            let overallStarsHtml = '';
            for (let i = 1; i <= 5; i++) {
                const isFilled = i <= Math.round(avgRating);
                overallStarsHtml += `
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${isFilled ? 'fill-current text-stone-900' : 'text-stone-200'}" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                `;
            }
            document.getElementById('fullReviewsStars').innerHTML = overallStarsHtml;

            // Breakdown counts
            const counts = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 };
            reviews.forEach(r => {
                const rating = Math.round(r.rating);
                if (counts[rating] !== undefined) {
                    counts[rating]++;
                }
            });

            // Populate Breakdown Bars
            let breakdownHtml = '';
            for (let star = 5; star >= 1; star--) {
                const count = counts[star];
                const pct = total > 0 ? (count / total * 100) : 0;
                breakdownHtml += `
                    <div class="flex items-center gap-4 text-xs">
                        <span class="w-12 text-stone-500 font-mono">${star} Star</span>
                        <div class="flex-1 h-1.5 bg-stone-100 rounded-full overflow-hidden">
                            <div class="h-full bg-stone-900 transition-all duration-500" style="width: ${pct}%"></div>
                        </div>
                        <span class="w-6 text-right text-stone-400 font-mono">${count}</span>
                    </div>
                `;
            }
            document.getElementById('ratingBreakdownContainer').innerHTML = breakdownHtml;

            // Render first page of review list
            renderReviewPage();
        }

        // Render paginated reviews
        function renderReviewPage() {
            const listContainer = document.getElementById('fullReviewsList');
            const reviewsPerPage = 6;
            const total = currentReviewsArray.length;
            const totalPages = Math.ceil(total / reviewsPerPage);

            const startIndex = (currentReviewPage - 1) * reviewsPerPage;
            const pageReviews = currentReviewsArray.slice(startIndex, startIndex + reviewsPerPage);

            let listHtml = '';
            pageReviews.forEach(review => {
                const userName = review.user ? review.user.name : 'Anonymous';
                const initials = userName.split(' ').map(w => w[0]).join('').substring(0, 2);
                const rating = parseInt(review.rating) || 5;
                const formattedDate = formatReviewDate(review.created_at);

                let stars = '<div class="flex gap-0.5">';
                for (let i = 1; i <= 5; i++) {
                    stars += `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ${i <= rating ? 'fill-current text-stone-900' : 'text-stone-200'}" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    `;
                }
                stars += '</div>';

                listHtml += `
                    <div class="py-6 border-b border-stone-100 last:border-0">
                        <div class="flex items-start justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-stone-100 text-stone-700 text-xs font-semibold flex items-center justify-center uppercase">
                                    ${initials}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-stone-900">${userName}</span>
                                        ${review.transaction_id ? `
                                        <span class="inline-flex items-center gap-0.5 text-[9px] tracking-wider text-emerald-700 uppercase bg-emerald-50 px-1.5 py-0.5 border border-emerald-100 font-medium">
                                            <svg class="h-2.5 w-2.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            Verified Purchase
                                        </span>` : ''}
                                    </div>
                                    <div class="mt-0.5">
                                        ${stars}
                                    </div>
                                </div>
                            </div>
                            <span class="text-[10px] text-stone-400 font-mono">${formattedDate}</span>
                        </div>
                        <p class="text-xs text-stone-700 font-light leading-relaxed mt-3 pl-11">
                            ${review.comment}
                        </p>
                    </div>
                `;
            });
            listContainer.innerHTML = listHtml || '<p class="text-xs text-stone-400 py-4">No reviews available.</p>';

            // Pagination UI
            const pagContainer = document.getElementById('fullReviewsPagination');
            let paginationHtml = '';

            if (totalPages > 1) {
                // Prev button
                paginationHtml += `
                    <button type="button" onclick="changeReviewPage(-1)" ${currentReviewPage === 1 ? 'disabled' : ''} 
                        class="px-3 py-1.5 text-[10px] tracking-widest border border-stone-200 text-stone-700 hover:text-black hover:border-black disabled:text-stone-300 disabled:border-stone-100 disabled:cursor-not-allowed uppercase transition-colors">
                        Prev
                    </button>
                `;

                // Page numbers
                paginationHtml += '<div class="flex items-center gap-1">';
                for (let p = 1; p <= totalPages; p++) {
                    if (p === currentReviewPage) {
                        paginationHtml += `
                            <span class="w-8 h-8 flex items-center justify-center text-xs font-semibold bg-stone-900 text-white border border-stone-900">${p}</span>
                        `;
                    } else {
                        paginationHtml += `
                            <button type="button" onclick="setReviewPage(${p})" 
                                class="w-8 h-8 flex items-center justify-center text-xs text-stone-600 hover:text-black border border-stone-200 hover:border-stone-400 transition-colors">
                                ${p}
                            </button>
                        `;
                    }
                }
                paginationHtml += '</div>';

                // Next button
                paginationHtml += `
                    <button type="button" onclick="changeReviewPage(1)" ${currentReviewPage === totalPages ? 'disabled' : ''} 
                        class="px-3 py-1.5 text-[10px] tracking-widest border border-stone-200 text-stone-700 hover:text-black hover:border-black disabled:text-stone-300 disabled:border-stone-100 disabled:cursor-not-allowed uppercase transition-colors">
                        Next
                    </button>
                `;
            }
            pagContainer.innerHTML = paginationHtml;
        }

        function changeReviewPage(direction) {
            const newPage = currentReviewPage + direction;
            const totalPages = Math.ceil(currentReviewsArray.length / 6);
            if (newPage >= 1 && newPage <= totalPages) {
                currentReviewPage = newPage;
                renderReviewPage();
                scrollToFullReviews();
            }
        }

        function setReviewPage(page) {
            currentReviewPage = page;
            renderReviewPage();
            scrollToFullReviews();
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

        let progressInterval;
        const duration = 5000; // 5 seconds
        const step = 50; // update progress every 50ms
        
        function startProgressBar(index) {
            clearInterval(progressInterval);
            
            // Reset all progress bars
            document.querySelectorAll('.carousel-progress-bar').forEach(bar => {
                bar.style.width = '0%';
            });
            
            const activeBar = document.getElementById('progressBar-' + index);
            if (!activeBar) return;
            
            let elapsed = 0;
            progressInterval = setInterval(() => {
                elapsed += step;
                let pct = Math.min((elapsed / duration) * 100, 100);
                activeBar.style.width = pct + '%';
                
                if (elapsed >= duration) {
                    clearInterval(progressInterval);
                    if (window.swiperHorizontal) {
                        window.swiperHorizontal.slideNext();
                    }
                }
            }, step);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const hasMultipleEvents = {{ $activeEvents->count() > 1 ? 'true' : 'false' }};
            if (hasMultipleEvents) {
                const swiperHorizontal = new Swiper('.swiper-horizontal-event', {
                    loop: true,
                    slidesPerView: 1,
                    spaceBetween: 24,
                    on: {
                        init: function() {
                            startProgressBar(this.realIndex);
                        },
                        slideChange: function() {
                            startProgressBar(this.realIndex);
                        }
                    }
                });
                window.swiperHorizontal = swiperHorizontal;
                
                window.jumpToSlide = function(index) {
                    if (window.swiperHorizontal) {
                        window.swiperHorizontal.slideToLoop(index);
                    }
                };
            }
        });
    </script>
@endsection
