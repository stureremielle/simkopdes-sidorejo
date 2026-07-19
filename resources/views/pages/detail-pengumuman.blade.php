@extends('layouts.app')

@section('title', $pengumuman->judul)

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/berita.css') }}">
    <style>
        /* Ambient/Blurred background replica */
        .detail-page-overlay-wrapper {
            position: relative;
            width: 100%;
            min-height: 100vh;
            background-color: #FFFFFF;
        }

        .ambient-news-bg {
            filter: blur(3px) brightness(65%);
            pointer-events: none;
            user-select: none;
            padding-top: 90px;
        }

        .detail-modal-backdrop {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.4);
            z-index: 900;
        }

        .detail-modal-viewport {
            position: fixed;
            inset: 0;
            z-index: 950;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 100px 16px 40px 16px;
            overflow-y: auto;
        }

        .detail-modal-card {
            background-color: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 680px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            pointer-events: auto;
        }

        .modal-hero-banner-announcement {
            position: relative;
            width: 100%;
            padding: 40px 30px;
            background: linear-gradient(135deg, #FFF1F2 0%, #FFF5F5 100%);
            border-bottom: 1px solid #FDEAEA;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .announcement-icon-ring {
            width: 64px;
            height: 64px;
            background-color: #DC2626;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);
            flex-shrink: 0;
        }

        .announcement-icon-ring svg {
            width: 32px;
            height: 32px;
            stroke: currentColor;
            fill: none;
        }

        .modal-category-badge-announcement {
            background-color: #B91C1C;
            color: #FFFFFF;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .modal-close-x-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(15, 23, 42, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            text-decoration: none;
            border: none;
            cursor: pointer;
            z-index: 15;
            transition: background-color 0.2s, transform 0.15s;
        }
        .modal-close-x-btn:hover {
            background-color: rgba(15, 23, 42, 0.75);
            transform: scale(1.05);
        }
        .modal-close-x-btn svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            stroke-width: 2.8;
            fill: none;
        }

        .modal-content-inner {
            padding: 30px 36px 36px 36px;
        }

        .modal-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            font-size: 0.82rem;
            color: #64748B;
            margin-bottom: 16px;
        }
        .modal-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .modal-meta-item svg {
            width: 14px;
            height: 14px;
            stroke: #DC2626;
            stroke-width: 2.2;
            fill: none;
            flex-shrink: 0;
        }

        .modal-article-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.35;
            margin: 0 0 16px 0;
        }

        .detail-body {
            font-size: 0.95rem;
            line-height: 1.7;
            color: #334155;
        }
        .detail-body p {
            margin-top: 0;
            margin-bottom: 16px;
        }

        .modal-share-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid #F1F5F9;
            padding-top: 20px;
            margin-top: 32px;
            flex-wrap: wrap;
            gap: 14px;
        }
        .modal-share-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.88rem;
            font-weight: 700;
            color: #0F172A;
        }
        .modal-share-label svg {
            width: 16px;
            height: 16px;
            stroke: #DC2626;
            stroke-width: 2.2;
            fill: none;
        }
        .modal-share-buttons {
            display: flex;
            gap: 8px;
        }
        .share-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #FFFFFF;
            text-decoration: none;
            transition: opacity 0.2s, transform 0.1s;
        }
        .share-pill-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .share-pill-fb { background-color: #1877F2; }
        .share-pill-tw { background-color: #1DA1F2; }
        .share-pill-wa { background-color: #25D366; }

        @media (max-width: 768px) {
            .detail-modal-viewport {
                padding-top: 80px;
            }
            .modal-content-inner {
                padding: 24px 20px;
            }
            .modal-article-title {
                font-size: 1.35rem;
            }
            .modal-hero-banner-announcement {
                padding: 30px 20px;
            }
        }
    </style>
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
