<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PANEL ADMIN') - Kop. Merah Putih Sidorejo</title>
    <meta name="description" content="Halaman Panel Admin Sistem Informasi Manajemen Koperasi Desa Merah Putih Sidorejo.">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin.css') }}">
    @yield('styles')
</head>
<body>

    @include('partials.sidebar', ['activeSidebar' => $activeSidebar ?? 'dashboard'])

    <div class="main-wrapper">
        <!-- Top Nav -->
        <header class="top-nav">
            <div class="breadcrumbs">
                <span>Admin</span> &gt; <span class="current">@yield('breadcrumb', 'Dashboard')</span>
            </div>

        </header>

        <!-- Dashboard Content -->
        <main class="dashboard-content">
            @yield('content')
        </main>
    </div>


    @yield('scripts')
</body>
</html>
