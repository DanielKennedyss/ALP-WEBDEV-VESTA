<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top py-3">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bold tracking-tighter" href="/" style="font-size: 1.5rem; letter-spacing: -0.05em;">
            VESTA
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link label-caps mx-3" href="#">Collections</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link label-caps mx-3" href="#">Lookbook</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link label-caps mx-3" href="#">Studio</a>
                </li>
            </ul>

<!-- Right Icons -->
<div class="d-flex align-items-center">
    @auth
        <!-- Link ke Dashboard (Bisa dibedakan role-nya nanti) -->
        <a href="{{ route('admin.dashboard') }}" class="text-dark text-decoration-none label-caps fw-bold me-4">
            ACCOUNT
        </a>

        <!-- Ikon Cart (Tetap sebagai link, bukan form logout) -->
        <a href="#" class="text-dark me-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
            </svg>
        </a>

        <!-- Tombol Logout yang sebenarnya -->
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-danger fw-bold label-caps text-decoration-none" style="border: none;">
                LOGOUT
            </button>
        </form>
    @else
        <a href="{{ route('login') }}" class="text-dark text-decoration-none label-caps fw-bold">
            SIGN IN
        </a>
    @endauth
</div>
        </div>
    </div>
</nav>

<style>
    .label-caps {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #000;
    }
    .navbar-brand {
        letter-spacing: -0.05em;
    }
    /* Memberikan jarak agar konten dashboard tidak tertutup fixed-navbar */
    body {
        padding-top: 80px;
    }
</style>