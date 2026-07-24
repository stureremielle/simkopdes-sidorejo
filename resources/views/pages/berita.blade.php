@php
    $activePage = 'berita';
@endphp
@extends('layouts.app')

@section('title', 'Berita & Informasi')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/berita.css') }}">
@endsection

@section('content')
    {{-- ===== PAGE HEADER ===== --}}
    <section class="berita-header-section">
        <div class="container">
            <h1 class="berita-page-title">Berita &amp; Informasi</h1>
            <p class="berita-page-subtitle">Tetap dapatkan informasi terbaru tentang kegiatan, pencapaian, dan pengumuman dari Koperasi Merah Putih.</p>
        </div>
    </section>

    {{-- ===== FEATURED ARTICLE CARD ===== --}}
    @if ($featured)
    <section class="featured-section">
        <div class="container">
            <div class="featured-card">
                <div class="featured-img-wrapper">
                    @if ($featured->gambar_url)
                        @php
                            $featImg = $featured->gambar_url;
                            $featImgUrl = Str::startsWith($featImg, 'http') ? $featImg : (Str::startsWith($featImg, 'uploads/') || Str::startsWith($featImg, 'storage/') || Str::startsWith($featImg, '/') ? asset(ltrim($featImg, '/')) : asset('assets/images/' . $featImg));
                        @endphp
                        <img src="{{ $featImgUrl }}" alt="{{ $featured->judul }}">
                    @else
                        <img src="https://images.unsplash.com/photo-1508962914676-134849a727f0?q=80&w=650" alt="{{ $featured->judul }}">
                    @endif
                    <span class="featured-badge">PILIHAN</span>
                </div>
                <div class="featured-content">
                    <div class="post-meta">
                        <div class="post-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>{{ \App\Helpers\Helper::formatTanggal($featured->tanggal_publikasi ?? ($featured->created_at ? $featured->created_at->toDateString() : date('Y-m-d'))) }}</span>
                        </div>
                        <div class="post-meta-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                            <span>{{ $featured->penulis }}</span>
                        </div>
                    </div>
                    <h2 class="featured-title">{{ $featured->judul }}</h2>
                    <p class="featured-desc">{{ Str::limit(strip_tags($featured->isi), 220) }}</p>
                    <a href="{{ route('berita.detail', $featured->id) }}" class="featured-link">Baca Selengkapnya &rarr;</a>
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ===== ARTIKEL TERBARU GRID ===== --}}
    <section class="recent-section">
        <div class="container">
            <h2 class="recent-title">Artikel Terbaru</h2>
            <div class="berita-grid" id="beritaGrid">
                @forelse ($artikel as $a)
                    <article class="berita-card">
                         <div class="card-img-wrapper">
                             @if ($a->gambar_url)
                                 @php
                                     $cardImg = $a->gambar_url;
                                     $cardImgUrl = Str::startsWith($cardImg, 'http') ? $cardImg : (Str::startsWith($cardImg, 'uploads/') || Str::startsWith($cardImg, 'storage/') || Str::startsWith($cardImg, '/') ? asset(ltrim($cardImg, '/')) : asset('assets/images/' . $cardImg));
                                 @endphp
                                 <img src="{{ $cardImgUrl }}" alt="{{ $a->judul }}">
                             @else
                                 <img src="https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&fit=crop&q=80" alt="{{ $a->judul }}">
                             @endif
                             <span class="card-badge">{{ $a->kategori }}</span>
                         </div>
                        <div class="card-body">
                            <div class="card-meta">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <span>{{ \App\Helpers\Helper::formatTanggal($a->tanggal_publikasi ?? ($a->created_at ? $a->created_at->toDateString() : date('Y-m-d'))) }}</span>
                            </div>
                            <h3 class="card-title">{{ $a->judul }}</h3>
                            <p class="card-desc">{{ Str::limit(strip_tags($a->isi), 130) }}</p>
                            <a href="{{ route('berita.detail', $a->id) }}" class="card-footer-link">Baca selengkapnya &rarr;</a>
                        </div>
                    </article>
                @empty
                    <p style="color:#666;grid-column:1/-1;text-align:center;padding:40px 0;">Belum ada artikel tersedia.</p>
                @endforelse
            </div>

            {{-- Muat Lebih Banyak Artikel Button --}}
            @if (count($artikel) >= 6)
            <div class="load-more-wrapper">
                <button class="btn-load-more" id="btnLoadMore">Muat Lebih Banyak Artikel</button>
            </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnLoadMore = document.getElementById('btnLoadMore');
        const beritaGrid = document.getElementById('beritaGrid');
        
        if (btnLoadMore && beritaGrid) {
            let offset = 6;
            
            btnLoadMore.addEventListener('click', function () {
                btnLoadMore.disabled = true;
                btnLoadMore.textContent = 'Memuat...';
                
                fetch('{{ route("berita") }}?offset=' + offset, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network error occurred.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.html) {
                        beritaGrid.insertAdjacentHTML('beforeend', data.html);
                        offset += 6;
                    }
                    
                    if (data.has_more) {
                        btnLoadMore.disabled = false;
                        btnLoadMore.textContent = 'Muat Lebih Banyak Artikel';
                    } else {
                        const wrapper = btnLoadMore.parentElement;
                        if (wrapper) {
                            wrapper.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading more articles:', error);
                    btnLoadMore.disabled = false;
                    btnLoadMore.textContent = 'Muat Lebih Banyak Artikel';
                });
            });
        }
    });
</script>
@endsection
