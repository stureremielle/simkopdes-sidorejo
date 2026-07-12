@php
    $activeKategoriIds = \App\Models\Layanan::where('status', 'aktif')
        ->distinct()
        ->pluck('kategori_id')
        ->filter();

    $footerLayanan = \App\Models\KategoriLayanan::whereIn('id', $activeKategoriIds)
        ->orderBy('nama')
        ->pluck('nama');

    $namaKoperasi = \App\Models\Pengaturan::getValue('nama_koperasi', 'Koperasi Desa Merah Putih Sidorejo');
    $alamat       = \App\Models\Pengaturan::getValue('alamat', 'Jl. Pariwisata RT 04 Dusun II Desa Sidorejo, Kec. Penajam, Kab. Penajam Paser Utara, Kalimantan Timur');
    $telepon      = \App\Models\Pengaturan::getValue('telepon', '+62 812 3456 7890');
    $email        = \App\Models\Pengaturan::getValue('email', 'info@merahputih.co.id');
    $facebook     = \App\Models\Pengaturan::getValue('facebook', 'https://facebook.com');
    $twitter      = \App\Models\Pengaturan::getValue('twitter', 'https://twitter.com');
    $instagram    = \App\Models\Pengaturan::getValue('instagram', 'https://instagram.com');
    $phoneClean   = preg_replace('/[^0-9+]/', '', $telepon);
@endphp

<footer id="footer" class="footer">
    <div class="container">
        <div class="footer-grid">

            <!-- Column 1: Brand dengan Logo Asli -->
            <div class="footer-brand fade-up-element">
                <div class="footer-logo-wrap">
                    <img
                        src="{{ asset('assets/images/logo-kopdes.jpg') }}"
                        alt="Logo KOPDES Merah Putih Sidorejo"
                        class="footer-logo-img"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    {{-- Fallback SVG jika gambar gagal load --}}
                    <svg viewBox="0 0 60 60" width="52" height="52" xmlns="http://www.w3.org/2000/svg"
                        style="display:none; border-radius:8px; background:#DC2626; flex-shrink:0;">
                        <rect x="0" y="44" width="60" height="16" fill="#B91C1C"/>
                        <text x="30" y="55" font-family="'Inter',sans-serif" font-size="7.5" font-weight="900" fill="white" text-anchor="middle">SIDOREJO</text>
                        <path d="M30 8 L12 24 L12 42 L48 42 L48 24 Z" fill="none" stroke="white" stroke-width="3" stroke-linejoin="round" stroke-linecap="round"/>
                        <text x="30" y="23" font-family="'Inter',sans-serif" font-size="9" font-weight="900" fill="white" text-anchor="middle">KOP</text>
                        <text x="30" y="33" font-family="'Inter',sans-serif" font-size="9" font-weight="900" fill="white" text-anchor="middle">DES</text>
                    </svg>
                    <span class="footer-brand-name">{{ $namaKoperasi }}</span>
                </div>
                <p>Badan usaha koperasi berbasis desa untuk kesejahteraan warga Desa Sidorejo dan kemandirian ekonomi.</p>
                <div class="social-links">
                    <a href="{{ $facebook }}" class="social-link" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" fill="currentColor"/></svg>
                    </a>
                    <a href="{{ $twitter }}" class="social-link" aria-label="Twitter / X" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="{{ $instagram }}" class="social-link" aria-label="Instagram" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" fill="currentColor"/></svg>
                    </a>
                </div>
            </div>

            <!-- Column 2: Tautan Cepat -->
            <div class="footer-col fade-up-element">
                <h4>Tautan Cepat</h4>
                <ul class="footer-links">
                    <li class="footer-link"><a href="{{ route('tentang') }}">Tentang Kami</a></li>
                    <li class="footer-link"><a href="{{ route('layanan') }}">Layanan Kami</a></li>
                    <li class="footer-link"><a href="{{ route('berita') }}">Berita &amp; Info</a></li>
                    <li class="footer-link"><a href="{{ route('kontak') }}">Kontak</a></li>
                </ul>
            </div>

            <!-- Column 3: Layanan (Dinamis dari DB) -->
            <div class="footer-col fade-up-element">
                <h4>Layanan</h4>
                <ul class="footer-links">
                    @forelse($footerLayanan as $kat)
                        <li class="footer-link">
                            <a href="{{ route('layanan', ['category' => $kat]) }}">{{ $kat }}</a>
                        </li>
                    @empty
                        <li class="footer-link"><a href="{{ route('layanan', ['category' => 'Pertanian']) }}">Pertanian</a></li>
                        <li class="footer-link"><a href="{{ route('layanan', ['category' => 'Peternakan']) }}">Peternakan</a></li>
                        <li class="footer-link"><a href="{{ route('layanan', ['category' => 'Perikanan']) }}">Perikanan</a></li>
                    @endforelse
                </ul>
            </div>

            <!-- Column 4: Informasi Kontak -->
            <div class="footer-col fade-up-element">
                <h4>Informasi Kontak</h4>
                <div class="footer-contact-details">

                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                        </div>
                        <div class="footer-contact-text">{{ $alamat }}</div>
                    </div>

                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </div>
                        <div class="footer-contact-text">
                            <a href="tel:{{ $phoneClean }}">{{ $telepon }}</a>
                        </div>
                    </div>

                    <div class="footer-contact-item">
                        <div class="footer-contact-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </div>
                        <div class="footer-contact-text">
                            <a href="mailto:{{ $email }}">{{ $email }}</a>
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Bottom Row -->
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ $namaKoperasi }}. Hak cipta dilindungi undang-undang.</p>
            <div class="footer-legal-links">
                <a href="#">Kebijakan Privasi</a>
                <span>|</span>
                <a href="#">Syarat &amp; Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
