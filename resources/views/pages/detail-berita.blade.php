@extends('layouts.app')

@section('title', $berita->judul)

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/berita.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/detail-berita.css') }}">
@endsection

@section('content')
    <div class="detail-page-overlay-wrapper">

        {{-- ===== BACKGROUND LIST REPLICA (Dimmed) ===== --}}
        <div class="ambient-news-bg">
            {{-- ===== PAGE HEADER ===== --}}
            <section class="berita-header-section" style="padding-top: 50px;">
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
                            @if ($featured->gambar)
                                @php
                                    $featGambar = $featured->gambar;
                                    $featGambarSrc = Str::startsWith($featGambar, 'http') ? $featGambar : (Str::startsWith($featGambar, 'uploads/') || Str::startsWith($featGambar, 'storage/') || Str::startsWith($featGambar, '/') ? asset(ltrim($featGambar, '/')) : asset('assets/images/' . $featGambar));
                                @endphp
                                <img src="{{ $featGambarSrc }}" alt="{{ $featured->judul }}">
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
                            <span class="featured-link" style="color: #64748B;">Baca Selengkapnya &rarr;</span>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            {{-- ===== ARTIKEL TERBARU GRID ===== --}}
            <section class="recent-section">
                <div class="container">
                    <h2 class="recent-title">Artikel Terbaru</h2>
                    <div class="berita-grid">
                        @foreach ($artikel as $a)
                            <article class="berita-card">
                                 <div class="card-img-wrapper">
                                     @if ($a->gambar)
                                         @php
                                             $cardGambar = $a->gambar;
                                             $cardGambarSrc = Str::startsWith($cardGambar, 'http') ? $cardGambar : (Str::startsWith($cardGambar, 'uploads/') || Str::startsWith($cardGambar, 'storage/') || Str::startsWith($cardGambar, '/') ? asset(ltrim($cardGambar, '/')) : asset('assets/images/' . $cardGambar));
                                         @endphp
                                         <img src="{{ $cardGambarSrc }}" alt="{{ $a->judul }}">
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
                                    <span class="card-footer-link" style="color: #64748B;">Baca selengkapnya &rarr;</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        {{-- ===== TRANSLUCENT OVERLAY ===== --}}
        <div class="detail-modal-backdrop"></div>

        {{-- ===== MODAL VIEWPORT CONTAINER ===== --}}
        <div class="detail-modal-viewport">
            <div class="detail-modal-card">

                {{-- Image Banner area with overlay close button and category --}}
                <div class="modal-hero-banner">
                    @if ($berita->gambar)
                        @php
                            $bannerImg = $berita->gambar;
                            $bannerImgSrc = Str::startsWith($bannerImg, 'http') ? $bannerImg : (Str::startsWith($bannerImg, 'uploads/') || Str::startsWith($bannerImg, 'storage/') || Str::startsWith($bannerImg, '/') ? asset(ltrim($bannerImg, '/')) : asset('assets/images/' . $bannerImg));
                        @endphp
                        <img src="{{ $bannerImgSrc }}" alt="{{ $berita->judul }}">
                    @endif

                    {{-- Coral Category Badge --}}
                    <span class="modal-category-badge">{{ $berita->kategori }}</span>

                    {{-- Circular close "X" button --}}
                    <a href="{{ route('berita') }}" class="modal-close-x-btn" title="Tutup detail berita">
                        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </a>
                </div>

                {{-- Modal Body Content --}}
                <div class="modal-content-inner">

                    {{-- Metadata row --}}
                    <div class="modal-meta-row">
                        <div class="modal-meta-item">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>{{ \App\Helpers\Helper::formatTanggal($berita->tanggal_publikasi ?? ($berita->created_at ? $berita->created_at->toDateString() : date('Y-m-d'))) }}</span>
                        </div>
                        <div class="modal-meta-item">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>{{ $berita->penulis }}</span>
                        </div>
                        <div class="modal-meta-item">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            @php
                                $wordCount = str_word_count(strip_tags($berita->isi));
                                $readMinutes = max(1, ceil($wordCount / 200));
                            @endphp
                            <span>{{ $readMinutes }} menit baca</span>
                        </div>
                    </div>

                    {{-- Article Title --}}
                    <h1 class="modal-article-title">{{ $berita->judul }}</h1>

                    {{-- HTML Body content --}}
                    <div class="detail-body">
                        {!! $berita->isi !!}
                    </div>

                    {{-- Social Sharing Bar --}}
                    <div class="modal-share-bar">
                        <div class="modal-share-label">
                            <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                            <span>Bagikan artikel ini:</span>
                        </div>
                        <div class="modal-share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank" rel="noopener noreferrer" class="share-pill-btn share-pill-fb">
                                <svg viewBox="0 0 24 24" style="fill:currentColor;stroke:none;width:13px;height:13px;"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                <span>Facebook</span>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($berita->judul) }}&url={{ urlencode(request()->url()) }}"
                               target="_blank" rel="noopener noreferrer" class="share-pill-btn share-pill-tw">
                                <svg viewBox="0 0 24 24" style="fill:currentColor;stroke:none;width:13px;height:13px;"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                <span>Twitter</span>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($berita->judul . ' - ' . request()->url()) }}"
                               target="_blank" rel="noopener noreferrer" class="share-pill-btn share-pill-wa">
                                <svg viewBox="0 0 24 24" style="fill:currentColor;stroke:none;width:13px;height:13px;"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.513 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.733-1.455L0 24zm6.09-3.977c1.649.979 3.26 1.488 4.903 1.49 5.541.002 10.051-4.507 10.055-10.05.002-2.685-1.043-5.209-2.945-7.114C16.3 2.443 13.785 1.397 11.106 1.397 5.565 1.397 1.054 5.908 1.05 11.453c-.002 1.702.457 3.364 1.332 4.887L1.408 22.42l6.23-1.637z"/></svg>
                                <span>WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
