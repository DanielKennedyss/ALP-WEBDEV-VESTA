<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-hidden">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VESTA - Luxury Fashion Redefined</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Montserrat:wght@200;300;400;500;600&display=swap" rel="stylesheet">

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Confine scroll exclusively to the Hero container */
        html, body {
            overflow: hidden !important;
            height: 100vh !important;
            width: 100vw !important;
            margin: 0;
            padding: 0;
            background-color: #000;
        }

        /* Scroll-Snap Container */
        .hero-scroll-container {
            width: 100vw;
            height: 100vh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        .hero-scroll-container::-webkit-scrollbar {
            display: none; /* Chrome, Safari and Opera */
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
        .hero-slide.active .slide-bg-media {
            transform: scale(1.05);
        }

        /* Luxury Gradient Overlay */
        .slide-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.6) 100%);
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

        .hero-slide.active .slide-content {
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
            0%, 100% { transform: translateY(0); opacity: 0.6; }
            50% { transform: translateY(10px); opacity: 1; }
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
    <div class="vertical-nav hidden md:flex flex-col">
        <div class="dot-wrapper active" data-slide-target="0" onclick="scrollToSlide(0)">
            <span class="nav-dot"></span>
        </div>
        <div class="dot-wrapper" data-slide-target="1" onclick="scrollToSlide(1)">
            <span class="nav-dot"></span>
        </div>
        <div class="dot-wrapper" data-slide-target="2" onclick="scrollToSlide(2)">
            <span class="nav-dot"></span>
        </div>
        <div class="dot-wrapper" data-slide-target="3" onclick="scrollToSlide(3)">
            <span class="nav-dot"></span>
        </div>
    </div>

    <!-- MAIN SCROLL SNAP HERO CONTAINER -->
    <main class="hero-scroll-container">

        <!-- SLIDE 1: Brand Introduction (Original Video) -->
        <section class="hero-slide active" data-slide-index="0">
            <video
                class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                autoplay
                muted
                loop
                playsinline
                poster="https://images.unsplash.com/photo-1558171813-4c088753af8f?w=1920&q=80"
            >
                <source src="/assets/video/hero.mp4" type="video/mp4">
            </video>
            <div class="slide-overlay"></div>
            
            <div class="slide-content flex flex-col items-center justify-center">
                <h1 class="text-white text-5xl md:text-7xl lg:text-9xl font-serif tracking-[0.25em] mb-4">VESTA</h1>
                <div class="w-24 h-px bg-white/40 my-8"></div>
                <p class="text-white/80 text-[10px] md:text-xs tracking-[0.5em] uppercase font-light">Luxury Fashion Redefined</p>
                <a href="#slide-1" onclick="event.preventDefault(); scrollToSlide(1);" class="cta-btn mt-8">
                    Discover More
                </a>
            </div>

            <!-- Down Arrow Hint -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer" onclick="scrollToSlide(1)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
        </section>

        <!-- SLIDE 2: Spring / Summer '26 (High Fashion Image Ad) -->
        <section class="hero-slide" data-slide-index="1">
            <img
                src="https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1920&q=80"
                alt="Spring/Summer '26 Campaign"
                class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                loading="lazy"
            >
            <div class="slide-overlay"></div>

            <div class="slide-content flex flex-col items-center justify-center">
                <span class="text-white/60 text-[10px] tracking-[0.3em] uppercase mb-4 font-light">New Campaign</span>
                <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif tracking-[0.2em] mb-6 leading-tight">SPRING / SUMMER '26</h2>
                <p class="text-white/70 text-xs md:text-sm tracking-[0.3em] uppercase max-w-xl leading-relaxed font-light">Elegant Silhouettes &amp; Timeless Textures</p>
                <a href="{{ route('collection') }}" class="cta-btn">
                    Explore Collection
                </a>
            </div>

            <!-- Down Arrow Hint -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer" onclick="scrollToSlide(2)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
        </section>

        <!-- SLIDE 3: Men's Tailoring (Haute Couture Image Ad) -->
        <section class="hero-slide" data-slide-index="2">
            <img
                src="https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=1920&q=80"
                alt="Men's Tailoring Campaign"
                class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                loading="lazy"
            >
            <div class="slide-overlay"></div>

            <div class="slide-content flex flex-col items-center justify-center">
                <span class="text-white/60 text-[10px] tracking-[0.3em] uppercase mb-4 font-light">The Tailoring Edit</span>
                <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif tracking-[0.2em] mb-6 leading-tight">HAUTE COUTURE</h2>
                <p class="text-white/70 text-xs md:text-sm tracking-[0.3em] uppercase max-w-xl leading-relaxed font-light">Meticulously Crafted for the Modern Icon</p>
                <a href="{{ route('collection') }}" class="cta-btn">
                    Shop The Edit
                </a>
            </div>

            <!-- Down Arrow Hint -->
            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 scroll-indicator cursor-pointer" onclick="scrollToSlide(3)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
            </div>
        </section>

        <!-- SLIDE 4: Prestige Line (Women's Image Ad) -->
        <section class="hero-slide" data-slide-index="3">
            <img
                src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1920&q=80"
                alt="The Prestige Line Campaign"
                class="slide-bg-media absolute inset-0 w-full h-full object-cover"
                loading="lazy"
            >
            <div class="slide-overlay"></div>

            <div class="slide-content flex flex-col items-center justify-center">
                <span class="text-white/60 text-[10px] tracking-[0.3em] uppercase mb-4 font-light">Prestige Collection</span>
                <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-serif tracking-[0.2em] mb-6 leading-tight">THE PRESTIGE LINE</h2>
                <p class="text-white/70 text-xs md:text-sm tracking-[0.3em] uppercase max-w-xl leading-relaxed font-light">Sophistication in Every Single Thread</p>
                <a href="{{ route('collection') }}" class="cta-btn">
                    View Selection
                </a>
            </div>
        </section>

    </main>

    <!-- INTERACTION SCRIPTS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('.hero-scroll-container');
            const slides = document.querySelectorAll('.hero-slide');
            const dotWrappers = document.querySelectorAll('.dot-wrapper');

            // Scroll indicator dots updater
            const observerOptions = {
                root: container,
                threshold: 0.5
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const activeIndex = entry.target.dataset.slideIndex;
                        
                        // Set active class on slides for text animation trigger
                        slides.forEach((slide, idx) => {
                            if (idx == activeIndex) {
                                slide.classList.add('active');
                            } else {
                                slide.classList.remove('active');
                            }
                        });

                        // Set active dot
                        dotWrappers.forEach(wrapper => {
                            if (wrapper.dataset.slideTarget == activeIndex) {
                                wrapper.classList.add('active');
                            } else {
                                wrapper.classList.remove('active');
                            }
                        });
                    }
                });
            }, observerOptions);

            slides.forEach(slide => observer.observe(slide));

            // Smooth scroll click handler
            window.scrollToSlide = function(index) {
                if (slides[index]) {
                    slides[index].scrollIntoView({ behavior: 'smooth' });
                }
            };
        });
    </script>
</body>
</html>