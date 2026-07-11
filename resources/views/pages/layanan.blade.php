@php
    $activePage = 'layanan';
@endphp

@extends('layouts.app')

@section('title', 'Layanan - Simkopdes')
@section('meta_description', 'Produk Unggulan Daerah Desa Sidorejo.')

@section('styles')
    <style>
        /* Modern font and custom styling overrides for Layanan */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body {
            background-color: #FAFAFA;
            color: #2D3748;
            overflow-x: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .layanan-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 60px 20px; /* Accounts for fixed navbar-header height */
        }

        /* Pure White Hero Section Block layout with no banner images */
        .layanan-hero {
            background-color: #FFFFFF;
            text-align: center;
            padding: 60px 20px 40px 20px;
            margin-bottom: 20px;
        }

        .layanan-hero-title {
            font-size: 2.75rem;
            font-weight: 800;
            color: #1A202C; /* large charcoal font */
            margin-bottom: 16px;
            letter-spacing: -0.5px;
        }

        .layanan-hero-subtitle {
            font-size: 1.1rem;
            color: #718096; /* muted soft text */
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .filter-search-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }

        .category-pills {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 12px 24px;
            border-radius: 9999px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 700;
            transition: all 0.25s ease;
            cursor: pointer;
            box-sizing: border-box;
        }

        .pill svg {
            margin-right: 8px;
            flex-shrink: 0;
        }

        .pill-active {
            background-color: #DC2626; /* Solid Red */
            color: #FFFFFF;
            border: 1px solid #DC2626;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25);
        }

        .pill-inactive {
            background-color: #FFFFFF; /* Pure white */
            border: 1px solid #E2E8F0; /* thin gray outline border */
            color: #4A5568; /* dark gray text */
        }

        .pill-inactive:hover {
            border-color: #DC2626;
            color: #DC2626;
        }

        .search-container {
            position: relative;
            max-width: 320px;
            width: 100%;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 50px;
            border-radius: 9999px; /* extra wide rounded pill corners */
            border: 1.5px solid #E2E8F0;
            outline: none;
            background-color: #FFFFFF;
            font-size: 0.95rem;
            color: #2D3748;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .search-input:focus {
            border-color: #DC2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #A0AEC0;
            display: flex;
            align-items: center;
        }

        @media (max-width: 768px) {
            .controls-row {
                flex-direction: column;
                align-items: stretch;
            }
            .search-container {
                max-width: 100%;
            }
        }

        /* Product Grid */
        .grid-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 80px 20px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 30px;
        }

        @media(min-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(min-width: 1024px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Product Card */
        .product-card {
            background-color: #FFFFFF;
            border-radius: 20px; /* rounded-2xl curves */
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); /* subtle shadow */
            border: 1px solid #F0F0F0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.07);
        }

        .card-img-wrapper {
            position: relative;
            width: 100%;
            height: 220px;
            overflow: hidden;
            background-color: #F7FAFC;
        }

        .card-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .card-img-wrapper img {
            transform: scale(1.04);
        }

        .card-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background-color: #FFFFFF; /* white background capsule */
            color: #B91C1C; /* deep red text */
            font-weight: 700;
            font-size: 0.75rem;
            padding: 6px 14px;
            border-radius: 9999px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            z-index: 5;
        }

        .card-body {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .card-title {
            font-size: 1.3rem;
            font-weight: 800; /* bold left-aligned black title */
            color: #1A202C;
            margin-bottom: 10px;
        }

        .card-desc {
            font-size: 0.95rem;
            color: #4A5568; /* specific text description block */
            line-height: 1.5;
            margin-bottom: 24px;
            flex-grow: 1;
        }

        .card-footer {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            border-top: 1px solid #F7FAFC;
            padding-top: 16px;
        }

        /* Specific classes for price styling */
        .text-\[\#DC2626\] {
            color: #DC2626;
        }
        .font-bold {
            font-weight: 700;
        }
        .text-gray-500 {
            color: #718096;
        }
        .text-sm {
            font-size: 0.875rem;
        }
    </style>
@endsection

@section('content')
    <div class="layanan-container">
        <!-- 2. HERO TITLE BLOCK (Clean pure white background, no banner images) -->
        <section class="layanan-hero">
            <h1 class="layanan-hero-title">{{ \App\Models\Pengaturan::getValue('judul_halaman', 'Produk Unggulan Daerah Kami') }}</h1>
            <p class="layanan-hero-subtitle">{{ \App\Models\Pengaturan::getValue('deskripsi_halaman', 'Temukan berbagai hasil pertanian dan peternakan berkualitas terbaik yang dihasilkan langsung dari Desa Sidorejo.') }}</p>
        </section>

        <!-- 3. CONTROLS BAR ROW -->
        <div class="filter-search-row">
            <div class="category-pills">
                <!-- Semua (Active) -->
                <div class="pill {{ !$kategori ? 'pill-active' : 'pill-inactive' }}" data-category="semua">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    <span>Semua</span>
                </div>
                <!-- Dynamic categories from database -->
                @foreach ($kategoriList as $kat)
                <div class="pill {{ strtolower($kategori) === strtolower($kat) ? 'pill-active' : 'pill-inactive' }}" data-category="{{ strtolower($kat) }}">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 2L12 12"></path>
                        <path d="M22 2s-5.5 0-9.5 4.5S6.5 17 6.5 17H2s0-4.5 4.5-8.5S22 2 22 2z"></path>
                    </svg>
                    <span>{{ $kat }}</span>
                </div>
                @endforeach
            </div>
            <!-- Search Input -->
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

        <!-- 4. DYNAMIC PRODUCTS GRID -->
        <div class="product-grid" id="productGrid">
            @forelse ($layananList as $item)
                @php
                    $imgUrl = $item->gambar_url ? (str_starts_with($item->gambar_url, 'http') ? $item->gambar_url : asset($item->gambar_url)) : 'https://images.unsplash.com/photo-1628157582853-a796fa650a6a?w=600&fit=crop&q=80';
                @endphp
                <div class="product-card" data-cat="{{ strtolower($item->kategori) }}">
                    <div class="card-img-wrapper">
                        <img src="{{ $imgUrl }}" alt="{{ $item->nama }}">
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
    <!-- Interactive Filtering and Search Logic -->
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

            // Run initial filter in case page reloads with active class or query parameter
            filterProducts();

            // Category Pill Clicking
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

            // Search input handler
            searchInput.addEventListener('input', function (e) {
                searchQuery = e.target.value.toLowerCase().trim();
                filterProducts();
            });
        });
    </script>
@endsection
