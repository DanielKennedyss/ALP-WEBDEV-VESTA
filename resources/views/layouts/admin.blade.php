<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VESTA ADMIN — Luxury Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* 1. Base Setup */
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f4f4f2; 
            color: #1a1a1a; 
            overflow-x: hidden;
        }
        
        /* 2. Sidebar Refinement */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.05);
            padding: 40px 24px;
            z-index: 1000;
        }

        .sidebar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: -0.04em;
            margin-bottom: 60px;
            display: block;
            text-decoration: none;
            color: #000;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: #b0b0b0;
            margin-bottom: 25px;
            display: block;
        }

        /* 3. Luxury Animated Nav Links */
        .nav-link-admin {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            color: #7a7a7a;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-radius: 8px;
            margin-bottom: 8px;
            position: relative;
            /* Transisi halus untuk semua perubahan */
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* Ikon Navigasi */
        .nav-link-admin i {
            margin-right: 12px;
            font-size: 1.1rem;
            transition: transform 0.4s ease;
        }

        /* Efek Hover: Geser sedikit dan berikan background tipis */
        .nav-link-admin:hover {
            color: #000;
            background: rgba(0,0,0,0.03);
            padding-left: 24px; /* Efek menggeser teks ke kanan */
        }

        .nav-link-admin:hover i {
            transform: scale(1.2);
        }

        /* Efek Active: Solid Black dengan Glow */
        .nav-link-admin.active {
            color: #fff;
            background: #1a1a1a;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            transform: translateX(5px);
        }

        /* 4. Main Content Transition */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .main-content {
            margin-left: 280px;
            padding: 60px 80px;
            min-height: 100vh;
            /* Halaman akan muncul dengan efek slide up saat dimuat */
            animation: slideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        /* 5. Luxury Card Styling */
        .admin-card {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.02);
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .admin-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.06);
        }

        /* Profile & Logout Section */
        .btn-logout {
            color: #ff4d4d;
            background: #fff5f5;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .btn-logout:hover {
            background: #ff4d4d;
            color: #fff !important;
            transform: scale(0.98);
        }

        /* Responsive Mobile */
        @media (max-width: 992px) {
            .sidebar { width: 80px; padding: 40px 15px; }
            .sidebar-brand, .nav-label, .nav-link-admin span, .profile-text { display: none; }
            .main-content { margin-left: 80px; padding: 40px; }
            .nav-link-admin { justify-content: center; padding: 15px 0; }
            .nav-link-admin:hover { padding-left: 0; }
            .nav-link-admin i { margin-right: 0; }
        }

        
    </style>
</head>
<body>

<aside class="sidebar d-flex flex-column">
    <div class="flex-grow-1">
        <a href="#" class="sidebar-brand text-uppercase">Vesta</a>
        <span class="nav-label">Management</span>
        
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-admin {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.inventory') }}" class="nav-link-admin {{ Route::is('admin.inventory*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> <span>Inventory</span>
            </a>
            
            @if(auth()->user()->role === 'owner')
                <a href="{{ route('admin.staff.index') }}" class="nav-link-admin {{ Route::is('admin.staff*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> <span>Staff Management</span>
                </a>
            @endif
            <div class="mb-2">
    <!-- Transaction Management Link -->
<a href="{{ route('admin.transactions.index') }}" 
   class="nav-link-admin {{ Route::is('admin.transactions*') ? 'active' : '' }}">
    <i class="bi bi-receipt"></i> <span>Transactions</span>
</a>
</div>  
        </nav>
    </div>
    

    {{-- User Profile Card --}}
    <div class="mt-auto pt-4 border-top">
        <div class="d-flex align-items-center mb-4 px-2">
            <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 38px; height: 38px; font-size: 14px; font-weight: 700;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="profile-text" style="line-height: 1.2;">
                <p class="mb-0 fw-bold small text-uppercase" style="letter-spacing: 0.5px;">{{ auth()->user()->name }}</p>
                <span class="text-muted" style="font-size: 9px; text-transform: uppercase;">{{ auth()->user()->role }}</span>
            </div>
        </div>
        
        <a href="#" class="nav-link-admin btn-logout text-decoration-none small fw-bold text-uppercase" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           style="font-size: 11px;">
           <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
    
</aside>

<main class="main-content">
    {{-- Animasi transisi antar konten --}}
    @yield('admin_content')
</main>

</body>
</html>