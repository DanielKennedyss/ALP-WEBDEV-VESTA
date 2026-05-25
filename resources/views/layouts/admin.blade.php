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
        :root {
            --vesta-black: #1a1a1a;
            --vesta-gray: #7a7a7a;
            --vesta-light-gray: #b0b0b0;
            --vesta-bg: #f4f4f2;
            --sidebar-width: 280px;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: var(--vesta-bg); 
            color: var(--vesta-black); 
            overflow-x: hidden;
        }
        
        /* 2. Sidebar Refinement */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            background: #ffffff;
            border-right: 1px solid rgba(0,0,0,0.05);
            padding: 40px 24px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            font-family: Georgia, Cambria, "Times New Roman", Times, serif;
            font-weight: 400;
            font-size: 1.5rem;
            letter-spacing: 0.35em;
            display: block;
            text-decoration: none;
            color: #000;
            text-transform: uppercase;
            transition: opacity 0.3s ease;
        }

        .sidebar-brand:hover { opacity: 0.7; color: #000; }

        .nav-label {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--vesta-light-gray);
            margin: 25px 0 15px 18px;
            display: block;
        }

        /* 3. Luxury Animated Nav Links */
        .nav-link-admin {
            display: flex;
            align-items: center;
            padding: 12px 18px;
            color: var(--vesta-gray);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-radius: 12px;
            margin-bottom: 4px;
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .nav-link-admin i {
            margin-right: 14px;
            font-size: 1.2rem;
            transition: transform 0.4s ease;
        }

        .nav-link-admin:hover {
            color: #000;
            background: rgba(0,0,0,0.03);
            transform: translateX(5px);
        }

        .nav-link-admin.active {
            color: #fff;
            background: var(--vesta-black);
            box-shadow: 0 10px 20px rgba(0,0,0,0.12);
        }

        /* 4. Main Content Transition */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 60px 80px;
            min-height: 100vh;
            animation: slideUp 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        /* 5. User Profile Section */
        .profile-card {
            background: #f9f9f9;
            border-radius: 16px;
            padding: 15px;
            margin-bottom: 15px;
            transition: background 0.3s ease;
        }

        .avatar-box {
            width: 40px;
            height: 40px;
            background: var(--vesta-black);
            color: #fff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .btn-logout {
            color: #dc3545 !important;
            background: #fff5f5;
            justify-content: center;
            border: 1px solid transparent;
        }

        .btn-logout:hover {
            background: #dc3545 !important;
            color: #fff !important;
            border-color: #dc3545;
        }

        /* Responsive Mobile */
        @media (max-width: 992px) {
            .sidebar { width: 85px; padding: 40px 12px; }
            .sidebar-brand, .nav-label, .nav-link-admin span, .profile-text, .home-btn, .sidebar-header-wrapper { display: none !important; }
            .main-content { margin-left: 85px; padding: 40px 20px; }
            .nav-link-admin { justify-content: center; padding: 15px 0; }
            .nav-link-admin i { margin-right: 0; font-size: 1.4rem; }
            .avatar-box { margin-right: 0 !important; }
        }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="flex-grow-1">
        <div class="d-flex align-items-center justify-content-between mb-5 sidebar-header-wrapper">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand mb-0">VESTA</a>
            <a href="/" class="btn btn-outline-dark btn-sm rounded-circle d-flex align-items-center justify-content-center home-btn" style="width: 32px; height: 32px;" title="Back to Home">
                <i class="bi bi-house"></i>
            </a>
        </div>
        
        <nav>
            <span class="nav-label">General</span>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-admin {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
            </a>

            <span class="nav-label">Operations</span>
            <a href="{{ route('admin.inventory') }}" class="nav-link-admin {{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> <span>Inventory</span>
            </a>
            
            <a href="{{ route('admin.transactions.index') }}" class="nav-link-admin {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> <span>Transactions</span>
            </a>

            {{-- MENU VOUCHERS BARU BERSTANDAR LUXURY OPERATIONS --}}
            <a href="{{ route('admin.vouchers.index') }}" class="nav-link-admin {{ request()->routeIs('admin.vouchers*') ? 'active' : '' }}">
                <i class="bi bi-ticket-perforated"></i> <span>Vouchers</span>
            </a>

            {{-- Logic Role: Manager & Owner can manage staff --}}
            @if(in_array(auth()->user()->role, ['owner', 'manager']))
                <span class="nav-label">Administration</span>
                <a href="{{ route('admin.staff.index') }}" class="nav-link-admin {{ request()->routeIs('admin.staff*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> <span>Staff Management</span>
                </a>
            @endif
        </nav>
    </div>

    <div class="mt-auto border-top pt-4">
        <div class="profile-card d-flex align-items-center">
            <div class="avatar-box me-3 shadow-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="profile-text overflow-hidden">
                <p class="mb-0 fw-bold small text-truncate" style="letter-spacing: 0.3px;">{{ auth()->user()->name }}</p>
                <span class="text-muted text-uppercase" style="font-size: 9px; font-weight: 700;">{{ auth()->user()->role }}</span>
            </div>
        </div>
        
        <a href="#" class="nav-link-admin btn-logout" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>

<main class="main-content">
    @yield('admin_content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>