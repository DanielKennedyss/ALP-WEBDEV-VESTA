<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VESTA - Luxury Fashion Redefined</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@200;300;400;500;600&display=swap"
        rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Swiper.js CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        /* Confine scroll exclusively to the Hero container */
        html,
        body {
            overflow: hidden !important;
            height: 100vh !important;
            width: 100vw !important;
            margin: 0;
            padding: 0;
            background-color: #000;
        }

        /* Swiper Container */
        .hero-scroll-container {
            width: 100vw;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        /* Individual Slides */
        .hero-slide {
            width: 100%;
            height: 100vh;
            position: relative;
            scroll-snap-align: start;
            scroll-snap-stop: always;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Background Media Cover */
        .slide-bg-media {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            transition: transform 1.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Subtle Ken Burns zoom effect when slide is active */
        .hero-slide.swiper-slide-active .slide-bg-media {
            transform: scale(1.05);
        }

        /* Luxury Gradient Overlay */
        .slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.2) 50%, rgba(0, 0, 0, 0.6) 100%);
            z-index: 2;
        }

        /* Text/Content Micro-animations */
        .slide-content {
            position: relative;
            z-index: 10;
            text-align: center;
            color: #fff;
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 1.4s cubic-bezier(0.16, 1, 0.3, 1), transform 1.4s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 0 20px;
            max-width: 900px;
        }

        .hero-slide.swiper-slide-active .slide-content {
            opacity: 1;
            transform: translateY(0);
        }

        /* Premium Vertical Indicator Dots */
        .vertical-nav {
            position: fixed;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 40;
            display: flex;
            flex-direction: column;
            gap: 20px;
            align-items: center;
        }

        .dot-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .nav-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .dot-wrapper:hover .nav-dot {
            background-color: rgba(255, 255, 255, 0.8);
            transform: scale(1.3);
        }

        /* Active dot style */
        .dot-wrapper.active .nav-dot {
            background-color: #fff;
            transform: scale(1.6);
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.6);
        }

        /* Luxurious CTA button styles */
        .cta-btn {
            display: inline-block;
            margin-top: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.8);
            color: #fff;
            background: transparent;
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            padding: 14px 36px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            z-index: 50; /* Ensure clickable */
        }

        .cta-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: -1;
        }

        .cta-btn:hover {
            color: #000;
            border-color: #fff;
        }

        .cta-btn:hover::before {
            transform: translateY(0);
        }

        /* Animated Down Arrow */
        @keyframes scrollHint {

            0%,
            100% {
                transform: translateY(0);
                opacity: 0.6;
            }

            50% {
                transform: translateY(10px);
                opacity: 1;
            }
        }

        .scroll-indicator {
            animation: scrollHint 2.5s infinite;
        }
    </style>
</head>

<body class="bg-white text-black antialiased">

    <!-- STICKY NAVBAR -->
    @include('layouts.header')

    <!-- VERTICAL SLIDE DOT INDICATORS -->
    <div class="vertical-nav hidden md:flex flex-col"></div>

    <!-- MAIN HERO CONTAINER (SWIPER) -->
    <main class="swiper-container hero-scroll-container">
        <div class="swiper-wrapper">

            <!-- SLIDE 1: Brand Introduction (Original Video) -->
            <section class="swiper-slide hero-slide">
                <video class="slide-bg-media absolute inset-0 w-full h-full object-cover" autoplay muted loop playsinline
                    poster="https://images.unsplash.com/photo-1558171813-4c088753af8f?w=1920&q=80">
                    <source src="/assets/video/hero.mp4" type="video/mp4">
                </video>
                <div class="slide-overlay"></div>

                <div class="slide-content flex flex-col items-center justify-center">
                    <h1 class="text-white text-5xl md:text-7xl lg:text-9xl font-serif tracking-[0.25em] mb-4">VESTA</h1>
                    <div class="w-24 h-px bg-white/40 my-8"></div>
                    <p class="text-white/80 text-[10px] md:text-xs tracking-[0.5em] uppercase font-light">Luxury Fashion
                        Redefined</p>
                    <a href="{{ route('collections.index') }}" class="cta-btn mt-8 relative z-50">
                        Discover More
                    </a>
                </div>

                <!-- Down Arrow Hint -->
                <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer"
                    onclick="window.swiper.slideNext()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </section>

            <!-- SLIDE 2: Active Events or Fallback Campaign -->
            @if($activeEvents->isNotEmpty())
                @foreach($activeEvents as $event)
                    <section class="swiper-slide hero-slide">
                        @if($event->background_image)
                            <div class="slide-bg-media absolute inset-0 w-full h-full bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $event->background_image) }}');" data-swiper-parallax="50%"></div>
                        @else
                            <img src="{{ $event->banner_image ?? 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1920&q=80' }}"
                                alt="{{ $event->display_title ?? $event->name }} Banner" class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                                loading="lazy">
                        @endif
                        <div class="slide-overlay"></div>

                        <div class="slide-content flex flex-col items-center justify-center">
                            @if($event->main_image)
                                <img src="{{ asset('storage/' . $event->main_image) }}" alt="{{ $event->display_title ?? $event->name }}" class="w-32 h-32 md:w-48 md:h-48 object-cover mb-6 rounded shadow-2xl border border-white/10">
                            @endif
                            <span class="text-white/60 text-[10px] tracking-[0.3em] uppercase mb-4 font-light">Limited Event</span>
                            <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif tracking-[0.2em] mb-6 leading-tight text-center">
                                {{ strtoupper($event->display_title ?? $event->name) }}</h2>
                            <p
                                class="text-white/70 text-xs md:text-sm tracking-[0.3em] uppercase max-w-xl leading-relaxed font-light text-center">
                                @if($event->display_description)
                                    {{ $event->display_description }}
                                @else
                                    EXCLUSIVE OFFERS FROM {{ strtoupper($event->start_date->format('M d, Y')) }} UNTIL {{ strtoupper($event->end_date->format('M d, Y')) }}
                                @endif
                            </p>
                            <a href="{{ route('collections.index', ['filter_event' => $event->id]) }}#product-grid" class="cta-btn mt-8 relative z-50">
                                Explore Collection
                            </a>
                        </div>

                        <!-- Down Arrow Hint -->
                        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer"
                            onclick="window.swiper.slideNext()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                    </section>
                @endforeach
            @else
                <!-- SLIDE 2: Spring / Summer '26 (High Fashion Image Ad) -->
                <section class="swiper-slide hero-slide">
                    <img src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1920&q=80"
                        alt="Spring/Summer '26 Campaign" class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                        loading="lazy">
                    <div class="slide-overlay"></div>

                    <div class="slide-content flex flex-col items-center justify-center">
                        <span class="text-white/60 text-[10px] tracking-[0.3em] uppercase mb-4 font-light">New Campaign</span>
                        <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif tracking-[0.2em] mb-6 leading-tight">
                            SPRING / SUMMER '26</h2>
                        <p
                            class="text-white/70 text-xs md:text-sm tracking-[0.3em] uppercase max-w-xl leading-relaxed font-light">
                            Elegant Silhouettes &amp; Timeless Textures</p>
                        <a href="{{ route('collections.index') }}" class="cta-btn mt-8 relative z-50">
                            Explore Collection
                        </a>
                    </div>

                    <!-- Down Arrow Hint -->
                    <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer"
                        onclick="window.swiper.slideNext()">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        </svg>
                    </div>
                </section>
            @endif

            <!-- SLIDE 3: Men's Tailoring (Haute Couture Image Ad) -->
            <section class="swiper-slide hero-slide">
                <img src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=1920&q=80"
                    alt="Men's Tailoring Campaign" class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                    loading="lazy">
                <div class="slide-overlay"></div>

                <div class="slide-content flex flex-col items-center justify-center">
                    <span class="text-white/60 text-[10px] tracking-[0.3em] uppercase mb-4 font-light">The Tailoring
                        Edit</span>
                    <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif tracking-[0.2em] mb-6 leading-tight">
                        HAUTE COUTURE</h2>
                    <p
                        class="text-white/70 text-xs md:text-sm tracking-[0.3em] uppercase max-w-xl leading-relaxed font-light">
                        Meticulously Crafted for the Modern Icon</p>
                    <a href="{{ route('collections.index') }}" class="cta-btn mt-8 relative z-50">
                        Shop The Edit
                    </a>
                </div>

                <!-- Down Arrow Hint -->
                <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer"
                    onclick="window.swiper.slideNext()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </section>

            <!-- SLIDE 4: Prestige Line (Women's Image Ad) -->
            <section class="swiper-slide hero-slide">
                <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1920&q=80"
                    alt="The Prestige Line Campaign" class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                    loading="lazy">
                <div class="slide-overlay"></div>

                <div class="slide-content flex flex-col items-center justify-center">
                    <span class="text-white/60 text-[10px] tracking-[0.3em] uppercase mb-4 font-light">Prestige
                        Collection</span>
                    <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif tracking-[0.2em] mb-6 leading-tight">
                        THE PRESTIGE LINE</h2>
                    <p
                        class="text-white/70 text-xs md:text-sm tracking-[0.3em] uppercase max-w-xl leading-relaxed font-light">
                        Sophistication in Every Single Thread</p>
                    <a href="{{ route('collections.index') }}" class="cta-btn mt-8 relative z-50">
                        View Selection
                    </a>
                </div>

                <!-- Down Arrow Hint to Loop back to Slide 1 -->
                <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer"
                    onclick="window.swiper.slideNext()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </div>
            </section>

        </div>
    </main>

    <!-- INTERACTION SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const swiper = new Swiper('.swiper-container', {
                direction: 'vertical',
                loop: true,
                slidesPerView: 1,
                mousewheel: {
                    thresholdDelta: 10,
                    forceToAxis: true
                },
                keyboard: {
                    enabled: true,
                },
                pagination: {
                    el: '.vertical-nav',
                    clickable: true,
                    bulletClass: 'dot-wrapper',
                    bulletActiveClass: 'active',
                    renderBullet: function (index, className) {
                        return '<div class="' + className + '"><span class="nav-dot"></span></div>';
                    }
                },
                on: {
                    init: function() {
                        playActiveVideos(this);
                    },
                    slideChange: function() {
                        playActiveVideos(this);
                    }
                }
            });
            window.swiper = swiper;

            function playActiveVideos(swiperInstance) {
                const activeSlide = swiperInstance.slides[swiperInstance.activeIndex];
                if (activeSlide) {
                    const videos = activeSlide.querySelectorAll('video');
                    videos.forEach(video => {
                        video.play().catch(() => {});
                    });
                }
            }
        });
    </script>
</body>

</html>
