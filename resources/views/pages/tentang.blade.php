@php
    $activePage = 'tentang';
@endphp
@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/tentang.css') }}?v={{ time() }}">
@endsection

@section('content')
    <!-- SEKSI BANNER UTAMA (HERO) -->
    <section class="about-hero-section">
        <div class="container hero-container">
            <h1 class="about-hero-title">Tentang {{ $settings['nama_koperasi'] ?? 'Koperasi Desa Merah Putih' }}</h1>
            @php
                $addr = $settings['alamat'] ?? 'Jl. Pariwisata RT 04 Dusun II Desa Sidorejo, Kecamatan Penajam, Kabupaten Penajam Paser Utara, Provinsi Kalimantan Timur';
                if ($addr === 'Jl. Pariwisata RT 04 Dusun II Desa Sidorejo, Kec. Penajam, Kab. Penajam Paser Utara, Kalimantan Timur' || $addr === 'Jl. Pariwisata RT 04 Dusun II Desa Sidorejo, Kecamatan Penajam, Kabupaten Penajam Paser Utara, Provinsi Kalimantan Timur') {
                    $formattedAddr = 'Jl. Pariwisata RT 04 Dusun II<br class="desktop-br">Desa Sidorejo, Kecamatan Penajam, Kabupaten Penajam Paser Utara, Provinsi<br class="desktop-br">Kalimantan Timur.';
                } else {
                    $formattedAddr = e($addr);
                }
            @endphp
            <p class="about-hero-subtitle">Badan usaha koperasi berbasis desa yang berdomisili di {!! $formattedAddr !!}</p>
        </div>
    </section>

    <!-- KARTU PROFIL MELAYANG (OVERLAP) -->
    <section class="about-profile-section">
        <div class="container">
            <div class="profile-card">
                <h2 class="profile-title">{{ $settings['nama_koperasi'] ?? 'Koperasi Desa Merah Putih Sidorejo' }}</h2>
                <p class="profile-description">Didirikan oleh dan untuk warga Desa Sidorejo, koperasi ini mengusung semangat gotong royong, kemandirian<br class="desktop-br">ekonomi, dan pemberdayaan masyarakat desa sesuai prinsip koperasi Indonesia.</p>
            </div>
        </div>
    </section>

    <!-- SEKSI VISI 2025 -->
    <section class="about-vision-section">
        <div class="container">
            <div class="vision-card">
                <div class="vision-icon-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1 .4 2.5 1.5 3.5.7.8 1.3 1.5 1.5 2.5"></path>
                        <line x1="9" y1="18" x2="15" y2="18"></line>
                        <line x1="10" y1="22" x2="14" y2="22"></line>
                    </svg>
                </div>
                <h2 class="vision-title">Visi 2025</h2>
                <blockquote class="vision-text">
                    "{{ !empty($settings['visi']) ? $settings['visi'] : 'Menjadi koperasi agribisnis terdepan yang berbasis masyarakat, berkelanjutan, berdaya saing dan memberikan manfaat bagi masyarakat luas, dengan mengintegrasikan ekonomi, pendidikan, dan keberlanjutan lingkungan.' }}"
                </blockquote>
            </div>
        </div>
    </section>

    <!-- SEKSI MISI 2025 -->
    <section class="about-misi-section" id="misi">
        <div class="container">
            <div class="section-header-centered">
                <div class="section-badge-circle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="6"></circle>
                        <circle cx="12" cy="12" r="2"></circle>
                    </svg>
                </div>
                <h2 class="section-main-title">Misi 2025</h2>
            </div>
            
            <div class="misi-grid">
                @if (!empty($settings['misi']))
                    @php
                        $misiData = json_decode($settings['misi'], true);
                        if (!is_array($misiData)) {
                            // Support old pipe-separated format
                            $oldMisi = array_filter(array_map('trim', explode('|', $settings['misi'])));
                            $misiData = [];
                            foreach ($oldMisi as $m) {
                                $misiData[] = [
                                    'title' => $m,
                                    'items' => [$m]
                                ];
                            }
                        }
                    @endphp
                    @foreach ($misiData as $index => $mItem)
                        @php
                            $num = $index + 1;
                            $isWide = ($num === 4 && count($misiData) === 5) ? 'card-wide' : '';
                        @endphp
                        <div class="misi-card {{ $isWide }}">
                            <h3 class="misi-card-title">{{ $num }}. {{ $mItem['title'] }}</h3>
                            <ul class="misi-list">
                                @if (!empty($mItem['items']))
                                    @foreach ($mItem['items'] as $subItem)
                                        <li class="misi-item">
                                            <span class="check-icon-wrapper">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </span>
                                            <span class="misi-text-content">{{ $subItem }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @endforeach
                @else
                    <!-- Kartu 1 -->
                    <div class="misi-card">
                        <h3 class="misi-card-title">1. Memperluas Kemitraan dan Pasar</h3>
                        <ul class="misi-list">
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Memperluas jaringan kemitraan dengan kelompok tani dan pelaku usaha (UMKM) dan koperasi lain untuk menciptakan ekosistem ekonomi yang kokoh.</span>
                            </li>
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Memperluas jaringan pasar untuk memastikan hasil pertanian memiliki akses ke pasar yg lebih luas, sehingga dapat meningkatkan penjualan bagi koperasi serta para mitra petani, pelaku UMKM dan koperasi lainnya.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Kartu 2 -->
                    <div class="misi-card">
                        <h3 class="misi-card-title">2. Penguatan Kelembagaan</h3>
                        <ul class="misi-list">
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Mengembangkan tata kelola koperasi yg transparan, profesional, terpercaya dan terintegrasi.</span>
                            </li>
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Memperkuat kemandirian koperasi dalam meningkatkan kesejahteraan anggota dan masyarakat sekitar.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Kartu 3 -->
                    <div class="misi-card">
                        <h3 class="misi-card-title">3. Agrowisata &amp; Pemancingan</h3>
                        <ul class="misi-list">
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Menyediakan program agrowisata yg inovatif dan edukatif.</span>
                            </li>
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Mengembangkan fasilitas dan kegiatan wisata berbasis agribisnis yg menarik dan mendukung pembelajaran praktis.</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Kartu 4 (Mencakup 2 Kolom) -->
                    <div class="misi-card card-wide">
                        <h3 class="misi-card-title">4. Pengelolaan Sampah Berkelanjutan</h3>
                        <div class="card-inner-split">
                            <ul class="misi-list">
                                <li class="misi-item">
                                    <span class="check-icon-wrapper">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                    <span class="misi-text-content">Mewujudkan masyarakat yang sadar lingkungan dan peduli terhadap pengelolaan Sampah.</span>
                                </li>
                                <li class="misi-item">
                                    <span class="check-icon-wrapper">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                    <span class="misi-text-content">Menciptakan sistem pengelolaan Sampah yang efisien, terintegrasi dan berkelanjutan. Mulai dari pemilahan dari Rumah, Pengumpulan, Pengolahan, hingga pemanfaatan kembali.</span>
                                </li>
                                <li class="misi-item">
                                    <span class="check-icon-wrapper">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                    <span class="misi-text-content">Meningkatkan nilai ekonomi sampah untuk mengurangi jumlah sampah, misal daur ulang kerajinan atau kompos.</span>
                                </li>
                            </ul>
                            <ul class="misi-list">
                                <li class="misi-item">
                                    <span class="check-icon-wrapper">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                    <span class="misi-text-content">Menekankan pentingnya kerja sama antara pemerintah, masyarakat, dunia usaha, swadaya masyarakat dalam pengelolaan Sampah.</span>
                                </li>
                                <li class="misi-item">
                                    <span class="check-icon-wrapper">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                    <span class="misi-text-content">Pemantauan dan evaluasi kinerja pengolahan sampah untuk memastikan sistem berjalan sesuai target dan dilakukan perbaikan terus menerus.</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Kartu 5 -->
                    <div class="misi-card">
                        <h3 class="misi-card-title">5. Klinik Bisnis &amp; Magang</h3>
                        <ul class="misi-list">
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Menyelenggarakan pelatihan agribisnis dan UMKM untuk menciptakan sumber daya manusia yang kompeten and berdaya saing.</span>
                            </li>
                            <li class="misi-item">
                                <span class="check-icon-wrapper">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content">Membuka program magang bagi generasi muda yang ingin mendalami agribisnis dan UMKM.</span>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- SEKSI PERSEBARAN ANGGOTA -->
    <section class="about-persebaran-section" id="persebaran">
        <div class="container">
            <div class="section-header-centered">
                <div class="section-badge-circle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h2 class="section-main-title">Persebaran Anggota</h2>
                <p class="section-subtitle">Jumlah dan persebaran anggota Koperasi Desa Merah Putih Sidorejo berdasarkan wilayah RT.</p>
            </div>

            <!-- Ringkasan Metrik Statistik -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">{{ $totalAnggota }}</div>
                    <div class="metric-label">Total Anggota</div>
                    <div class="metric-subtext">Terdaftar aktif</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $jumlahRt }}</div>
                    <div class="metric-label">Jumlah RT</div>
                    <div class="metric-subtext">Wilayah cakupan</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">{{ $anggotaBaru }}</div>
                    <div class="metric-label">Anggota Baru</div>
                    <div class="metric-subtext">Tahun {{ $currentYear }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">89%</div>
                    <div class="metric-label">Keaktifan</div>
                    <div class="metric-subtext">Partisipasi rapat</div>
                </div>
            </div>

            <!-- Kisi-kisi Batang Kemajuan RT -->
            <div class="rt-grid">
                @foreach($rtData as $rt)
                <div class="rt-card">
                    <div class="rt-card-header">
                        <div class="rt-label-group">
                            <span class="rt-name">{{ $rt['name'] }}</span>
                            <span class="rt-dusun">{{ $rt['dusun'] }}</span>
                        </div>
                        <span class="rt-members-count">{{ $rt['count'] }}</span>
                    </div>
                    <div class="progress-container">
                        <div class="progress-track">
                            <div class="progress-bar" style="width: {{ $rt['pct'] }}%;"></div>
                        </div>
                        <div class="progress-status-row">
                            <span class="progress-percentage">{{ $rt['pct'] }}%</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SEKSI STRUKTUR ORGANISASI -->
    <section class="about-struktur-section" id="struktur">
        <div class="container">
            <div class="struktur-card-container">
                <div class="section-header-centered">
                    <span class="struktur-badge-subtitle">STRUKTUR ORGANISASI</span>
                    <h2 class="section-main-title">KOPERASI DESA MERAH PUTIH SIDOREJO</h2>
                    <p class="section-teal-subtitle">KECAMATAN PENAJAM</p>
                </div>

                <!-- Tombol Sakelar Rincian -->
                @if (empty($settings['org_chart']))
                <div class="org-toggle-container">
                    <button id="org-toggle-btn" class="org-toggle-btn">PENGURUS</button>
                </div>
                @endif

                @if (!empty($settings['org_chart']))
                @php
                    $orgChartUrl = str_starts_with($settings['org_chart'], 'http') ? $settings['org_chart'] : asset('uploads/' . $settings['org_chart']);
                @endphp
                <div style="display: flex; justify-content: center; align-items: center; padding: 20px; background: #FFFFFF; border-radius: 12px; border: 1.5px solid #F1F5F9; box-shadow: 0 1px 3px rgba(0,0,0,0.05); margin-top: 24px; position: relative; overflow: auto; max-height: 800px;">
                    <img src="{{ $orgChartUrl }}" alt="Struktur Organisasi KOPDES" style="max-width: 100%; height: auto; border-radius: 8px;">
                </div>
                @else
                <!-- Bagan Hirarki Pengurus -->
                <div id="tree-pengurus" class="org-tree-view">
                    <!-- Chairman Tier -->
                    <div class="tree-tier tier-chairman">
                        <div class="tree-node-card chairman-node">
                            <div class="tree-avatar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="node-info">
                                <h4 class="node-name">JUMAIR</h4>
                                <div class="node-divider"></div>
                                <p class="node-role">KETUA</p>
                            </div>
                        </div>
                    </div>

                    <!-- Subordinates Tier -->
                    <div class="tree-subordinates">
                        <!-- Pargito -->
                        <div class="tree-child-wrapper">
                            <div class="tree-node-card">
                                <div class="tree-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="node-info">
                                    <h4 class="node-name">PARGITO</h4>
                                    <div class="node-divider"></div>
                                    <p class="node-role">WAKIL KETUA</p>
                                </div>
                            </div>
                        </div>
                        <!-- Darti -->
                        <div class="tree-child-wrapper">
                            <div class="tree-node-card">
                                <div class="tree-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="node-info">
                                    <h4 class="node-name">DARTI</h4>
                                    <div class="node-divider"></div>
                                    <p class="node-role">BENDAHARA</p>
                                </div>
                            </div>
                        </div>
                        <!-- Sutris -->
                        <div class="tree-child-wrapper">
                            <div class="tree-node-card">
                                <div class="tree-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="node-info">
                                    <h4 class="node-name">SUTRIS</h4>
                                    <div class="node-divider"></div>
                                    <p class="node-role">PENASIHAT</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tree Pengawas -->
                <div id="tree-pengawas" class="org-tree-view" style="display: none;">
                    <!-- Chairman Tier -->
                    <div class="tree-tier tier-chairman">
                        <div class="tree-node-card chairman-node">
                            <div class="tree-avatar">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                </svg>
                            </div>
                            <div class="node-info">
                                <h4 class="node-name">SUPRI</h4>
                                <div class="node-divider"></div>
                                <p class="node-role">KETUA</p>
                            </div>
                        </div>
                    </div>

                    <!-- Subordinates Tier -->
                    <div class="tree-subordinates">
                        <!-- Siti -->
                        <div class="tree-child-wrapper">
                            <div class="tree-node-card">
                                <div class="tree-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="node-info">
                                    <h4 class="node-name">SITI</h4>
                                    <div class="node-divider"></div>
                                    <p class="node-role">ANGGOTA</p>
                                </div>
                            </div>
                        </div>
                        <!-- Empty/Dash -->
                        <div class="tree-child-wrapper">
                            <div class="tree-node-card">
                                <div class="tree-avatar">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </div>
                                <div class="node-info">
                                    <h4 class="node-name">—</h4>
                                    <div class="node-divider"></div>
                                    <p class="node-role">ANGGOTA</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('org-toggle-btn');
            const pengurusTree = document.getElementById('tree-pengurus');
            const pengawasTree = document.getElementById('tree-pengawas');

            if (toggleBtn && pengurusTree && pengawasTree) {
                toggleBtn.addEventListener('click', () => {
                    if (pengurusTree.style.display !== 'none') {
                        pengurusTree.style.display = 'none';
                        pengawasTree.style.display = 'block';
                        toggleBtn.textContent = 'PENGAWAS';
                    } else {
                        pengurusTree.style.display = 'block';
                        pengawasTree.style.display = 'none';
                        toggleBtn.textContent = 'PENGURUS';
                    }
                });
            }
        });
    </script>
@endsection
