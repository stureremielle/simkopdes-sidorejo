<aside class="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-subtitle">PANEL ADMIN</span>
        <div class="sidebar-title">Kop. Merah Putih Sidorejo</div>
    </div>
    <ul class="sidebar-menu">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" style="{{ request()->is('admin/dashboard') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect>
                    <rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect>
                </svg>
                <span>Dashboard</span>
            </a>
        </li>
        <!-- Layanan & Produk -->
        <li class="menu-item {{ request()->is('admin/layanan*') ? 'active' : '' }}">
            <a href="{{ route('admin.layanan') }}" style="{{ request()->is('admin/layanan*') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span>Layanan &amp; Produk</span>
            </a>
        </li>
        <!-- Berita & Artikel -->
        <li class="menu-item {{ request()->is('admin/berita*') ? 'active' : '' }}">
            <a href="{{ route('admin.berita') }}" style="{{ request()->is('admin/berita*') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                <span>Berita &amp; Artikel</span>
            </a>
        </li>
        <!-- Galeri -->
        <li class="menu-item {{ request()->is('admin/galeri*') ? 'active' : '' }}">
            <a href="{{ route('admin.galeri') }}" style="{{ request()->is('admin/galeri*') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <span>Galeri</span>
            </a>
        </li>
        <!-- Data Anggota -->
        <li class="menu-item {{ request()->is('admin/anggota*') ? 'active' : '' }}">
            <a href="{{ route('admin.anggota') }}" style="{{ request()->is('admin/anggota*') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Data Anggota</span>
            </a>
        </li>
        <!-- Pengumuman -->
        <li class="menu-item {{ request()->is('admin/pengumuman*') ? 'active' : '' }}">
            <a href="{{ route('admin.pengumuman.index') }}" style="{{ request()->is('admin/pengumuman*') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 5L6 9H2v6h4l5 4V5z"></path>
                    <path d="M19.07 4.93a10 10 0 0 1 0 14.14M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                </svg>
                <span>Pengumuman</span>
            </a>
        </li>
        <!-- Penyimpanan File -->
        <li class="menu-item {{ request()->is('admin/files*') || request()->is('admin/penyimpanan*') ? 'active' : '' }}">
            <a href="{{ route('admin.penyimpanan') }}" style="{{ request()->is('admin/files*') || request()->is('admin/penyimpanan*') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Penyimpanan File</span>
            </a>
        </li>
        <!-- Pengaturan -->
        <li class="menu-item {{ request()->is('admin/pengaturan*') ? 'active' : '' }}">
            <a href="{{ route('admin.pengaturan') }}" style="{{ request()->is('admin/pengaturan*') ? 'background-color: #B91C1C !important; color: #ffffff !important;' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
                <span>Pengaturan</span>
            </a>
        </li>
    </ul>

    <!-- Logout -->
    <div class="sidebar-footer">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Keluar</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</aside>
