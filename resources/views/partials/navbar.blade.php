@php
    if (!isset($activePage)) $activePage = '';
@endphp
<header class="navbar-header" id="navbar">
    <div class="container navbar-container">
        <a href="{{ route('home') }}" class="logo" id="navLogo">
            <img src="{{ asset('assets/images/logo-simkopdes.jpg') }}" alt="SIMKOPDES Sidorejo" style="height: 68px; max-height: 68px; width: auto; display: block; object-fit: contain; margin: -10px 0;">
        </a>

        <nav class="nav-menu">
            <a href="{{ route('home') }}" class="nav-link {{ ($activePage === 'beranda') ? 'active' : '' }}" id="linkHome">Beranda</a>
            <a href="{{ route('layanan') }}" class="nav-link {{ ($activePage === 'layanan') ? 'active' : '' }}" id="linkLayanan">Layanan</a>
            <a href="{{ route('berita') }}" class="nav-link {{ ($activePage === 'berita') ? 'active' : '' }}" id="linkBerita">Berita</a>
            <a href="{{ route('galeri') }}" class="nav-link {{ ($activePage === 'galeri') ? 'active' : '' }}" id="linkGaleri">Galeri</a>
            <a href="{{ route('tentang') }}" class="nav-link {{ ($activePage === 'tentang') ? 'active' : '' }}" id="linkTentang">Tentang Kami</a>
            <a href="{{ route('kontak') }}" class="nav-link {{ ($activePage === 'kontak') ? 'active' : '' }}" id="linkKontak">Kontak</a>
            <a href="{{ route('daftar') }}" class="btn btn-join" id="btnJoinDesktop">Daftar Anggota</a>
        </nav>

        <button class="mobile-nav-toggle" aria-label="Toggle Navigation Menu" id="mobileMenuBtn">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<div class="mobile-menu-overlay" id="mobileMenuOverlay">
    <a href="{{ route('home') }}" class="nav-link {{ ($activePage === 'beranda') ? 'active' : '' }}">Beranda</a>
    <a href="{{ route('layanan') }}" class="nav-link {{ ($activePage === 'layanan') ? 'active' : '' }}">Layanan</a>
    <a href="{{ route('berita') }}" class="nav-link {{ ($activePage === 'berita') ? 'active' : '' }}">Berita</a>
    <a href="{{ route('galeri') }}" class="nav-link {{ ($activePage === 'galeri') ? 'active' : '' }}">Galeri</a>
    <a href="{{ route('tentang') }}" class="nav-link {{ ($activePage === 'tentang') ? 'active' : '' }}">Tentang Kami</a>
    <a href="{{ route('kontak') }}" class="nav-link {{ ($activePage === 'kontak') ? 'active' : '' }}">Kontak</a>
    <a href="{{ route('daftar') }}" class="btn btn-join">Daftar Anggota</a>
</div>
