@extends('layouts.app')

@section('title', $pengumuman->judul)

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/berita.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/detail-pengumuman.css') }}">
@endsection

@section('content')
    <div class="detail-page-overlay-wrapper">
        {{-- ===== BACKGROUND LIST REPLICA (Dimmed) ===== --}}
        <div class="ambient-news-bg">
            <section class="berita-header-section" style="padding-top: 50px;">
                <div class="container">
                    <h1 class="berita-page-title">Pengumuman</h1>
                    <p class="berita-page-subtitle">Informasi penting untuk anggota dan masyarakat.</p>
                </div>
            </section>
        </div>

        {{-- ===== TRANSLUCENT OVERLAY ===== --}}
        <div class="detail-modal-backdrop"></div>

        {{-- ===== MODAL VIEWPORT CONTAINER ===== --}}
        <div class="detail-modal-viewport">
            <div class="detail-modal-card">
                
                {{-- Banner Area --}}
                <div class="modal-hero-banner-announcement">
                    <div class="announcement-icon-ring">
                        <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </div>
                    <div>
                        <span class="modal-category-badge-announcement">Pengumuman</span>
                        <div style="font-size: 0.85rem; color: #4A4A4A; margin-top: 5px;">Kop. Merah Putih Sidorejo</div>
                    </div>

                    {{-- Close Button links back to Home page since page was opened from Home --}}
                    <a href="{{ route('home') }}" class="modal-close-x-btn" title="Tutup detail berita">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </a>
                </div>

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
                            <span>{{ $pengumuman->tanggal }}</span>
                        </div>
                    </div>

                    {{-- Title --}}
                    <h1 class="modal-article-title">{{ $pengumuman->judul }}</h1>

                    {{-- HTML Body content --}}
                    <div class="detail-body">
                        {!! $pengumuman->isi !!}
                    </div>


                </div>

            </div>
        </div>
    </div>
@endsection
