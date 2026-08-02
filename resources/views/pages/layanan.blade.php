@php
    $activePage = 'layanan';
@endphp

@extends('layouts.app')

@section('title', 'Layanan - Simkopdes')
@section('meta_description', 'Produk Unggulan Daerah Desa Sidorejo.')

    <link rel="stylesheet" href="{{ asset('assets/css/layanan.css') }}">

@section('content')
    <div class="layanan-container">
        <!-- 2. BLOK JUDUL UTAMA (HERO) -->
        <section class="layanan-hero">
            <h1 class="layanan-hero-title">Produk Unggulan Daerah Kami</h1>
            <p class="layanan-hero-subtitle">Temukan berbagai hasil pertanian dan peternakan berkualitas terbaik yang dihasilkan langsung dari Desa Sidorejo.</p>
        </section>

        <!-- 3. BARIS KONTROL KATEGORI & PENCARIAN -->
        <div class="filter-search-row">
            <div class="category-pills">
                <!-- Semua (Aktif) -->
                <div class="pill {{ !$kategori ? 'pill-active' : 'pill-inactive' }}" data-category="semua">
                    <span>Semua</span>
                </div>
                <!-- Kategori dinamis dari database -->
                @foreach ($kategoriList as $kat)
                <div class="pill {{ strtolower($kategori) === strtolower($kat) ? 'pill-active' : 'pill-inactive' }}" data-category="{{ strtolower($kat) }}">
                    <span>{{ $kat }}</span>
                </div>
                @endforeach
            </div>
            <!-- Input Pencarian -->
            <div class="search-container">
                <div class="search-icon">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
                <input type="text" id="searchInput" class="search-input" placeholder="Cari produk...">
            </div>
        </div>

        <!-- 4. KISI-KISI PRODUK DINAMIS -->
        <div class="product-grid" id="productGrid">
            @forelse ($layananList as $item)
                @php
                    $imgSrc = $item->gambar
                        ? (str_starts_with($item->gambar, 'http') ? $item->gambar : asset($item->gambar))
                        : null;
                @endphp
                <div class="product-card" data-cat="{{ strtolower($item->kategori) }}">
                    <div class="card-img-wrapper">
                        @if($imgSrc)
                            <img src="{{ $imgSrc }}" alt="{{ $item->nama }}">
                        @endif
                        <span class="card-badge">{{ $item->kategori }}</span>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">{{ $item->nama }}</h3>
                        <p class="card-desc">{{ $item->deskripsi }}</p>
                        <div class="card-footer">
                            <div class="card-price">
                                <span class="text-[#DC2626] font-bold">Rp {{ number_format($item->harga, 0, ',', '.') }}</span> <span class="text-gray-500 text-sm">per {{ $item->satuan }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #718096; background: #FFFFFF; border-radius: 12px; border: 1.5px solid #E2E8F0;">
                    <div style="font-size: 3rem; margin-bottom: 12px;">📦</div>
                    <h3 style="font-weight: 700; color: #2D3748; margin-bottom: 6px;">Produk Kosong</h3>
                    <p style="font-size: 0.9rem; color: #A0AEC0;">Belum ada produk aktif yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Logika Pencarian dan Penyaringan Interaktif -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pills = document.querySelectorAll('.pill');
            const searchInput = document.getElementById('searchInput');
            const cards = document.querySelectorAll('.product-card');
            
            let activeCategory = '{{ $kategori ? strtolower($kategori) : "semua" }}';
            let searchQuery = '';

            function filterProducts() {
                cards.forEach(card => {
                    const category = card.getAttribute('data-cat');
                    const title = card.querySelector('.card-title').textContent.toLowerCase();
                    const desc = card.querySelector('.card-desc').textContent.toLowerCase();
                    
                    const matchesCategory = (activeCategory === 'semua' || category === activeCategory);
                    const matchesSearch = (title.includes(searchQuery) || desc.includes(searchQuery));

                    if (matchesCategory && matchesSearch) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Jalankan penyaringan awal saat halaman dimuat
            filterProducts();

            // Penanganan klik pada tombol pilihan kategori
            pills.forEach(pill => {
                pill.addEventListener('click', function () {
                    pills.forEach(p => {
                        p.classList.remove('pill-active');
                        p.classList.add('pill-inactive');
                    });
                    this.classList.remove('pill-inactive');
                    this.classList.add('pill-active');
                    
                    activeCategory = this.getAttribute('data-category');
                    filterProducts();
                });
            });

            // Penanganan input pencarian
            searchInput.addEventListener('input', function (e) {
                searchQuery = e.target.value.toLowerCase().trim();
                filterProducts();
            });
        });
    </script>
@endsection
