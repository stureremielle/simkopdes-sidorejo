@extends('layouts.app')

@section('title', 'Koperasi Desa Merah Putih Sidorejo')

@section('content')
    @php
        $heroBg = \App\Models\Pengaturan::getValue('hero_background', '');
        $heroBgUrl = $heroBg ? (str_starts_with($heroBg, 'http') ? $heroBg : asset('uploads/' . $heroBg)) : '';
    @endphp
    <!-- Hero Section -->
    <section id="home" class="hero-section" @if($heroBgUrl) style="background-image: linear-gradient(rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.32)), url('{{ $heroBgUrl }}');" @endif>
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title" id="heroHeading">
                Tumbuh Bersama Menuju
                <br>
                <span class="title-accent">Masa Depan Berkelanjutan</span>
            </h1>
            <p class="hero-description" id="heroDesc">
                Memberdayakan masyarakat desa melalui koperasi pertanian, perdagangan yang adil, dan pemanfaatan sumber daya bersama.
            </p>
            <div class="hero-actions">
                <a href="{{ route('layanan') }}" class="btn btn-primary" id="heroCtaPrimary">
                    Eksplorasi Produk Kami &rarr;
                </a>
                <a href="{{ route('tentang') }}" class="btn btn-secondary" id="heroCtaSecondary">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </section>

    <!-- Mengapa Memilih Koperasi Kami -->
    <section id="pilihan" class="section why-choose-section">
        <div class="container">
            <div class="section-header fade-up-element">
                <h2 class="section-title">Mengapa Memilih Koperasi Kami?</h2>
                <p>Kami dibangun atas prinsip gotong royong, transparansi, dan pertumbuhan berkelanjutan bagi setiap anggota desa kami.</p>
            </div>
            <div class="why-choose-grid">
                <div class="why-choose-card stagger-item">
                    <div class="why-choose-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <h3 class="why-choose-title">Utamakan Komunitas</h3>
                    <p class="why-choose-desc">Dimiliki dan dikelola oleh komunitas, memastikan keuntungan dan manfaatnya dirasakan langsung oleh masyarakat desa.</p>
                </div>
                <div class="why-choose-card stagger-item">
                    <div class="why-choose-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11 11 13 15 9"/>
                        </svg>
                    </div>
                    <h3 class="why-choose-title">Perdagangan Adil</h3>
                    <p class="why-choose-desc">Menjamin harga yang adil bagi petani dan produk berkualitas tinggi dengan transparansi sumber bagi konsumen.</p>
                </div>
                <div class="why-choose-card stagger-item">
                    <div class="why-choose-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 21 3c-1.5 4-2 5.5-3.1 11.2A7 7 0 0 1 11 20z"/>
                            <line x1="9" y1="11" x2="6" y2="14"/>
                        </svg>
                    </div>
                    <h3 class="why-choose-title">Pertanian Berkelanjutan</h3>
                    <p class="why-choose-desc">Mempromosikan praktik pertanian organik dan ramah lingkungan untuk melindungi alam bagi generasi mendatang.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Produk Unggulan (Dinamis) -->
    <section id="produk" class="section products-section">
        <div class="container">
            <div class="products-header-container fade-up-element">
                <div class="products-header-text">
                    <h2 class="section-title" style="text-align: left; margin-bottom: 5px; padding-bottom: 0;">Segar dari Desa</h2>
                    <p style="margin-top: 10px;">Temukan berbagai produk dan layanan lokal terbaik dari desa kami.</p>
                </div>
                <a href="{{ route('layanan') }}" class="products-header-link">
                    Lihat Semua
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
            <div class="products-grid">
                @forelse ($products as $item)
                <div class="product-card stagger-item">
                    <div class="product-image">
                        @if ($item->gambar_url)
                            <img src="{{ $item->gambar_url }}" alt="{{ $item->nama }}">
                        @else
                            <div style="background:#fff1f2;display:flex;align-items:center;justify-content:center;height:100%;min-height:180px;color:#DC2626;font-size:3rem;">📦</div>
                        @endif
                    </div>
                    <div class="product-content">
                        <h3 class="product-title">{{ $item->nama }}</h3>
                        <p class="product-desc">{{ Str::limit($item->deskripsi, 80) }}</p>
                        <a href="{{ route('layanan') }}" class="product-link">
                            Lihat detail
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
                @empty
                    <p style="grid-column: 1/-1; text-align: center; color: #666; padding: 40px 0;">Belum ada produk unggulan.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="cta" class="cta-section">
        <div class="container cta-container fade-up-element">
            <h2 class="cta-title">Siap bergabung dengan koperasi kami?</h2>
            <p class="cta-desc">Apakah Anda petani lokal yang mencari dukungan, atau pembeli yang mencari produk asli desa, kami menyambut Anda dengan tangan terbuka.</p>
            <div class="cta-buttons">
                <a href="{{ route('daftar') }}" class="btn btn-cta-primary">Daftar Anggota</a>
                <a href="{{ route('kontak') }}" class="btn btn-cta-secondary">Hubungi Kami</a>
            </div>
        </div>
    </section>
@endsection

