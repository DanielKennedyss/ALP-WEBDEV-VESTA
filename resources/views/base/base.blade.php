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
        body { 
            margin: 0; 
            padding-top: 5rem !important; /* Account for fixed transparent header */
        }
    </style>
</head>
<body class="antialiased font-sans bg-white selection:bg-gray-900 selection:text-white">

    <!-- Header Section -->
    @include('layouts.header')

    <!-- Main Content Area -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer Section -->
    @include('layouts.footer')

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
    </script></body>
</html>