<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VESTA ADMIN — Luxury Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #fcfcfc; color: #1a1a1a; }
        
        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: #fff;
            border-right: 1px solid #f0f0f0;
            padding: 40px 24px;
        }

        .sidebar-brand {
            font-weight: 600;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
            margin-bottom: 60px;
            display: block;
            text-decoration: none;
            color: #000;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #a0a0a0;
            margin-bottom: 20px;
            display: block;
        }

        .nav-link-admin {
            display: flex;
            align-items: center;
            padding: 12px 0;
            color: #666;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .nav-link-admin:hover, .nav-link-admin.active {
            color: #000;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 60px;
        }

        .admin-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 32px;
            height: 100%;
        }

        .stat-label { font-size: 11px; font-weight: 600; color: #a0a0a0; text-transform: uppercase; letter-spacing: 0.1em; }
        .stat-value { font-size: 48px; font-weight: 300; letter-spacing: -0.05em; margin: 15px 0; }
        
        .status-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .status-out { background: #fff5f5; color: #ff4d4d; border: 1px solid #ffebeb; }
        .status-low { background: #fffaf0; color: #f6ad55; border: 1px solid #feebc8; }
    </style>
</head>
<body>

<aside class="sidebar d-flex flex-column">
    <div class="flex-grow-1">
        <a href="#" class="sidebar-brand text-uppercase">Vesta Admin</a>
        <span class="nav-label">Luxury Management</span>
        
        <nav>
            <a href="{{ route('admin.dashboard') }}" class="nav-link-admin {{ Route::is('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('admin.inventory') }}" class="nav-link-admin {{ Route::is('admin.inventory*') ? 'active' : '' }}">Inventory</a>
            
            {{-- Fitur khusus Owner saja --}}
            @if(auth()->user()->role === 'owner')
                <a href="{{ route('admin.staff.index') }}" class="nav-link-admin {{ Route::is('admin.staff*') ? 'active' : '' }}">Staff Management</a>
            @endif
        </nav>
    </div>

    {{-- Bagian Bawah: Profile & Logout --}}
    <div class="mt-auto p-4 border-top">
        <div class="mb-3">
            <p class="mb-0 fw-bold small text-uppercase" style="letter-spacing: 1px;">{{ auth()->user()->name }}</p>
            <span class="text-muted" style="font-size: 10px; text-transform: uppercase;">Role: {{ auth()->user()->role }}</span>
        </div>
        
        <a href="#" class="text-danger small fw-bold text-decoration-none text-uppercase" 
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           style="font-size: 11px; letter-spacing: 1px;">
           Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</aside>

    <main class="main-content">
        @yield('admin_content')
    </main>

</body>
</html>