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

</body>
</html>