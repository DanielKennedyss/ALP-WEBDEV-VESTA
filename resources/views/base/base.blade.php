<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VESTA — Premium Minimalist Fashion</title>

    <!-- Scripts & Styles - Use built assets for consistent styling -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js untuk interaksi ringan (seperti mobile menu atau dropdown) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        /* Critical CSS for above-the-fold content */
        html, body {
            overflow: hidden !important;
            height: 100vh !important;
            margin: 0;
            padding: 0;
        }

        .app-scroll-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            overflow-y: scroll;
            overflow-x: hidden;
            overscroll-behavior: contain;
            padding-top: 5rem;
            
            /* Hide default scrollbars */
            scrollbar-width: none !important; /* Firefox */
            -ms-overflow-style: none !important; /* IE/Edge */
        }
        .app-scroll-container::-webkit-scrollbar {
            display: none !important; /* Webkit (Chrome, Safari, etc.) */
        }

        /* Custom Elegant Floating Scrollbar */
        .custom-scroll-track {
            position: fixed;
            top: 100px; /* Offset to start below the 80px navbar */
            bottom: 40px; /* Gap from screen bottom */
            right: 32px; /* Perfect distance from right edge, matching home slide dots */
            width: 12px; /* Hover target area width */
            z-index: 1000;
            display: flex;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: none;
        }

        /* Show track when scrolling, hover, or dragging */
        .custom-scroll-track.visible,
        .custom-scroll-track:hover,
        .custom-scroll-track.active {
            opacity: 1;
            pointer-events: auto;
        }

        .custom-scroll-track::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: rgba(0, 0, 0, 0.03);
            border-radius: 99px;
            transition: background-color 0.3s ease;
        }

        .custom-scroll-track:hover::before,
        .custom-scroll-track.active::before {
            background-color: rgba(0, 0, 0, 0.08);
        }

        .custom-scroll-thumb {
            position: absolute;
            top: 0;
            width: 4px;
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 99px;
            cursor: pointer;
            transition: width 0.2s ease, background-color 0.2s ease;
            will-change: transform;
        }

        .custom-scroll-track:hover .custom-scroll-thumb,
        .custom-scroll-thumb.dragging {
            width: 8px;
            background-color: rgba(0, 0, 0, 0.6);
        }

        /* Responsive Mobile - let native overlay scroll work */
        @media (max-width: 768px) {
            .custom-scroll-track {
                display: none !important;
            }
            .app-scroll-container {
                scrollbar-width: thin !important;
            }
            .app-scroll-container::-webkit-scrollbar {
                display: block !important;
                width: 4px !important;
            }
        }
    </style>
</head>
<body class="antialiased font-sans bg-white selection:bg-gray-900 selection:text-white">

    <!-- Header Section -->
    @include('layouts.header')

    <!-- Main Scroll Wrapper -->
    <div class="app-scroll-container">
        <!-- Main Content Area -->
        <main class="min-h-screen">
            @yield('content')
        </main>

        <!-- Footer Section -->
        @include('layouts.footer')
    </div>

    <!-- Custom Elegant Scrollbar Track & Thumb -->
    <div class="custom-scroll-track" id="customScrollTrack">
        <div class="custom-scroll-thumb" id="customScrollThumb"></div>
    </div>

    <!-- Custom Scrollbar Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.querySelector('.app-scroll-container');
            const track = document.getElementById('customScrollTrack');
            const thumb = document.getElementById('customScrollThumb');

            if (!container || !track || !thumb) return;

            let isDragging = false;
            let startY = 0;
            let startScrollTop = 0;
            let scrollHeight = 0;
            let clientHeight = 0;
            let trackHeight = 0;
            let thumbHeight = 0;
            let maxScrollTop = 0;
            let maxThumbTop = 0;
            let updateTicking = false;
            let scrollTimeout = null;

            function calculateMetrics() {
                scrollHeight = container.scrollHeight;
                clientHeight = container.clientHeight;
                trackHeight = track.clientHeight;
                
                if (scrollHeight <= clientHeight) {
                    track.style.display = 'none';
                    return false;
                }
                
                track.style.display = 'flex';

                // Proportional thumb height (min 40px, max 150px)
                thumbHeight = (clientHeight / scrollHeight) * trackHeight;
                thumbHeight = Math.max(40, Math.min(thumbHeight, 150));
                
                thumb.style.height = `${thumbHeight}px`;
                
                maxScrollTop = scrollHeight - clientHeight;
                maxThumbTop = trackHeight - thumbHeight;
                return true;
            }

            function updateThumbPosition() {
                if (!calculateMetrics()) return;
                
                const thumbTop = (container.scrollTop / maxScrollTop) * maxThumbTop;
                thumb.style.transform = `translateY(${thumbTop}px)`;
            }

            function showScrollbarTemporarily() {
                track.classList.add('visible');
                clearTimeout(scrollTimeout);
                if (!isDragging) {
                    scrollTimeout = setTimeout(() => {
                        if (!track.matches(':hover')) {
                            track.classList.remove('visible');
                        }
                    }, 1500); // Hide after 1.5s of inactivity
                }
            }

            function requestTick() {
                if (!updateTicking) {
                    requestAnimationFrame(() => {
                        updateThumbPosition();
                        updateTicking = false;
                    });
                    updateTicking = true;
                }
            }

            // Listen to scroll and resize
            container.addEventListener('scroll', () => {
                requestTick();
                showScrollbarTemporarily();
            }, { passive: true });

            window.addEventListener('resize', () => {
                calculateMetrics();
                updateThumbPosition();
            }, { passive: true });

            // Keep visible on hover
            track.addEventListener('mouseenter', () => {
                track.classList.add('visible');
                clearTimeout(scrollTimeout);
            });

            track.addEventListener('mouseleave', () => {
                if (!isDragging) {
                    scrollTimeout = setTimeout(() => {
                        track.classList.remove('visible');
                    }, 1000);
                }
            });

            // Initial calculation
            setTimeout(() => {
                calculateMetrics();
                updateThumbPosition();
            }, 200);

            // Observe dynamic changes to content height
            const resizeObserver = new ResizeObserver(() => {
                calculateMetrics();
                updateThumbPosition();
            });
            resizeObserver.observe(container);
            
            // Fallback MutationObserver
            const mutationObserver = new MutationObserver(() => {
                calculateMetrics();
                updateThumbPosition();
            });
            mutationObserver.observe(container, { childList: true, subtree: true });

            // Dragging events
            thumb.addEventListener('mousedown', (e) => {
                isDragging = true;
                startY = e.clientY;
                startScrollTop = container.scrollTop;
                
                thumb.classList.add('dragging');
                track.classList.add('active');
                document.body.style.userSelect = 'none';
                e.preventDefault();
            });

            window.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                
                const deltaY = e.clientY - startY;
                const scrollDelta = (deltaY / maxThumbTop) * maxScrollTop;
                
                container.scrollTop = startScrollTop + scrollDelta;
            });

            window.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    thumb.classList.remove('dragging');
                    track.classList.remove('active');
                    document.body.style.userSelect = '';
                }
            });

            // Click on track to jump
            track.addEventListener('mousedown', (e) => {
                if (e.target === thumb) return;
                
                const rect = track.getBoundingClientRect();
                const clickY = e.clientY - rect.top;
                
                // Position center of thumb at click y
                const targetThumbTop = clickY - thumbHeight / 2;
                const targetPercent = Math.max(0, Math.min(targetThumbTop / maxThumbTop, 1));
                
                container.scrollTop = targetPercent * maxScrollTop;
            });
        });
    </script>

    <!-- VESTA Branded Toast Notifications -->
    <style>
        .vesta-global-toast {
            position: fixed; top: 6rem; right: 1.5rem; z-index: 200;
            padding: 1.25rem 1.5rem; min-width: 300px; max-width: 400px;
            border: 1px solid; opacity: 0; transform: translateX(100%);
            animation: vestaToastIn 0.5s ease 0.2s forwards;
            display: flex; align-items: flex-start; gap: 0.75rem;
        }
        .vesta-global-toast .toast-brand { font-size: 9px; letter-spacing: 0.25em; font-weight: 600; opacity: 0.5; }
        .vesta-global-toast .toast-msg { font-size: 0.8rem; letter-spacing: 0.03em; line-height: 1.4; }
        .vesta-global-toast.toast-success { background: #f0fdf4; border-color: #86efac; color: #166534; }
        .vesta-global-toast.toast-error { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }
        .vesta-global-toast .toast-close { cursor: pointer; opacity: 0.4; transition: opacity 0.2s; margin-left: auto; flex-shrink: 0; }
        .vesta-global-toast .toast-close:hover { opacity: 1; }
        @keyframes vestaToastIn { to { opacity: 1; transform: translateX(0); } }
        @keyframes vestaToastOut { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(100%); } }
    </style>

    @if(session('success'))
    <div class="vesta-global-toast toast-success" id="globalToast">
        <div>
            <div class="toast-brand">VESTA</div>
            <div class="toast-msg">{{ session('success') }}</div>
        </div>
        <span class="toast-close" onclick="this.parentElement.style.animation='vestaToastOut 0.4s ease forwards';setTimeout(()=>this.parentElement.remove(),400)">✕</span>
    </div>
    @endif

    @if(session('error'))
    <div class="vesta-global-toast toast-error" id="globalToast">
        <div>
            <div class="toast-brand">VESTA</div>
            <div class="toast-msg">{{ session('error') }}</div>
        </div>
        <span class="toast-close" onclick="this.parentElement.style.animation='vestaToastOut 0.4s ease forwards';setTimeout(()=>this.parentElement.remove(),400)">✕</span>
    </div>
    @endif

    <script>
        // Auto-dismiss toast after 5 seconds
        setTimeout(function() {
            var toast = document.getElementById('globalToast');
            if (toast) {
                toast.style.animation = 'vestaToastOut 0.4s ease forwards';
                setTimeout(function() { toast.remove(); }, 400);
            }
        }, 5000);
    </script>
    
    <!-- Membership Guide Modal -->
    @include('layouts.membership-modal')
</body>
</html>