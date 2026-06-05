@extends('base.base')

@section('content')
    <!-- Swiper.js CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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

    <div class="pt-28 pb-24 px-4 lg:px-8 bg-stone-50 min-h-[80vh] flex items-center justify-center">
        <div class="max-w-[1400px] mx-auto w-full">
            
            {{-- Dynamic Event Banner / Fallback Section --}}
            <div class="mb-8 w-full relative">
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
                                <a href="{{ route('collections.index') }}"
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

        </div>
    </div>

    <script>
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
