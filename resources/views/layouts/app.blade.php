<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Klasifikasi Minyak</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #f8f9fa;
        }
        .content {
            padding: 20px;
        }
        .sub-menu {
            padding-left: 20px;
            display: none;
        }
        .sub-menu.active {
            display: block;
        }
        .nav-item.has-submenu {
            position: relative;
        }
        .nav-item.has-submenu::after {
            content: '▼';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 10px;
        }
        .nav-item.has-submenu.active::after {
            transform: translateY(-50%) rotate(180deg);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-3">
                <h3 class="mb-4">Klasifikasi Minyak</h3>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                    </li>
                    <li class="nav-item mb-2 has-submenu">
                        <a href="#" class="nav-link" onclick="toggleSubMenu(this)">Data Training</a>
                        <ul class="sub-menu">
                            <li class="nav-item mb-2">
                                <a href="{{ route('import.index') }}" class="nav-link">Input Data</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="{{ route('labeled.index') }}" class="nav-link">Labelling</a>
                            </li>
                            <li class="nav-item mb-2">
                                <a href="#" class="nav-link">Dataset</a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('hourly.index') }}" class="nav-link">Data Minyak</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('hasil.klasifikasi') }}" class="nav-link">Hasil Klasifikasi</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('cetak.index') }}" class="nav-link">Cetak Laporan</a>
                    </li>
                    <li class="nav-item mb-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="col-md-10 content">
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSubMenu(element) {
            event.preventDefault();
            const parent = element.parentElement;
            const subMenu = parent.querySelector('.sub-menu');
            
            // Toggle active class on parent
            parent.classList.toggle('active');
            
            // Toggle sub-menu display
            subMenu.classList.toggle('active');
        }
    </script>
    @stack('scripts')
</body>
</html> 