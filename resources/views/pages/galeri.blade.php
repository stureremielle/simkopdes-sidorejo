@php
    $activePage = 'galeri';
@endphp
@extends('layouts.app')

@section('title', 'Galeri Dokumentasi')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/galeri.css') }}">
@endsection

@section('content')
    @php
    $displayCards = [];
    $defaultKategori = ['Rapat & Musyawarah', 'Panen & Pertanian', 'Pelatihan', 'Kegiatan Sosial'];
    foreach ($galeriList as $g) {
        $displayCards[] = [
            'judul' => $g->judul,
            'kategori' => $g->kategori,
            'gambar_url' => str_starts_with($g->gambar_url, 'http') ? $g->gambar_url : asset($g->gambar_url),
            'keterangan' => $g->keterangan,
            'materi' => !empty($g->materi_url),
            'materi_url' => $g->materi_url,
            'tanggal' => \App\Helpers\Helper::formatBulanTahun($g->created_at)
        ];
    }
    $allKategori = array_values(array_unique(array_merge($defaultKategori, $kategoriList->toArray())));
    @endphp

    <section class="gallery-hero-section">
        <div class="container hero-container">
            <h1 class="gallery-hero-title">Galeri Dokumentasi</h1>
            <p class="gallery-hero-subtitle">Dokumentasi kegiatan dan aktivitas Koperasi Desa Merah Putih Sidorejo. Klik foto untuk melihat detail.</p>
        </div>
    </section>

    <section class="gallery-filter-section">
        <div class="container filter-container">
            <div class="filter-tabs" id="filterTabs">
                <button class="filter-tab active" data-filter="Semua">Semua</button>
                @foreach ($allKategori as $kat)
                <button class="filter-tab" data-filter="{{ $kat }}">{{ $kat }}</button>
                @endforeach
            </div>
        </div>
    </section>

    <section class="gallery-grid-section">
        <div class="container">
            <div class="gallery-grid" id="galleryGrid">
                @foreach ($displayCards as $card)
                <article class="gallery-card"
                    data-category="{{ $card['kategori'] }}"
                    data-title="{{ $card['judul'] }}"
                    data-date="{{ $card['tanggal'] }}"
                    data-materi="{{ $card['materi'] ? 'true' : 'false' }}"
                    data-materi-url="{{ $card['materi_url'] ?? '' }}"
                    data-desc="{{ $card['keterangan'] }}">
                    <div class="card-media-wrapper">
                        <img src="{{ $card['gambar_url'] }}" alt="{{ $card['judul'] }}">
                        <span class="category-badge">{{ $card['kategori'] }}</span>
                        @if ($card['materi'])
                        <div class="material-badge" title="Tersedia materi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>
                            </svg>
                        </div>
                        @endif
                        <div class="zoom-overlay">
                            <div class="zoom-icon-circle">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    <line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <h3 class="card-title">{{ $card['judul'] }}</h3>
                        <div class="card-footer-bottom">
                            <span class="card-date">{{ $card['tanggal'] }}</span>
                            @if ($card['materi'])<span class="materi-indicator">Ada materi</span>@endif
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
            <div class="empty-state" id="emptyState" style="display:none;">
                <div class="empty-state-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg></div>
                <h3>Dokumentasi tidak ditemukan</h3><p>Belum ada dokumentasi untuk kategori ini.</p>
            </div>
        </div>
    </section>

    <section class="gallery-legend-section">
        <div class="container legend-container">
            <div class="legend-content">
                <div class="legend-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                </div>
                <span class="legend-text">= Tersedia file materi untuk diunduh</span>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="modal-wrapper" id="lightboxModal">
        <div class="lightbox-box">
            <button class="modal-close" id="closeLightboxModal" aria-label="Tutup modal">&times;</button>
            <button class="modal-expand" id="expandLightboxModal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"></path></svg>
            </button>
            <div class="lightbox-card-container">
                <div class="lightbox-media-section">
                    <img id="lightboxImg" src="" alt="">
                    <span class="category-badge" id="lightboxBadge"></span>
                </div>
                <div class="lightbox-info-section">
                    <h3 class="lightbox-title" id="lightboxTitle"></h3>
                    <div class="lightbox-meta">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" class="calendar-icon"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span id="lightboxDate"></span>
                    </div>
                    <div class="lightbox-desc-section" id="lightboxDesc" style="margin-top:15px; font-size:0.95rem; line-height:1.6; color:#475569;">
                    </div>
                    <div class="lightbox-attachment-box" id="downloadArea" style="margin-top:20px;">
                        <div class="attachment-left"><div class="attachment-icon-box"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></div></div>
                        <div class="attachment-center">
                            <h4 class="attachment-title" id="attachmentTitle">Modul</h4>
                            <span class="attachment-filename" id="attachmentFilename">file.pdf</span>
                        </div>
                        <div class="attachment-right">
                            <button class="btn-download" id="btnDownloadMateri">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="download-arrow"><line x1="12" y1="5" x2="12" y2="19"></line><polyline points="19 12 12 19 5 12"></polyline></svg>
                                <span>Unduh</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterTabs = document.querySelectorAll('#filterTabs .filter-tab');
            const galleryCards = document.querySelectorAll('.gallery-card');
            const emptyState = document.getElementById('emptyState');
            if (emptyState) emptyState.style.display = 'none';
            filterTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    filterTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    const filterValue = tab.getAttribute('data-filter');
                    let visibleCount = 0;
                    galleryCards.forEach(card => {
                        const cardCategory = card.getAttribute('data-category');
                        if (filterValue === 'Semua' || cardCategory === filterValue) {
                            card.style.display = ''; card.style.opacity = '0'; card.style.transform = 'scale(0.95)';
                            setTimeout(() => { card.style.transition = 'all 0.4s cubic-bezier(0.4,0,0.2,1)'; card.style.opacity = '1'; card.style.transform = 'scale(1)'; }, 50);
                            visibleCount++;
                        } else { card.style.display = 'none'; }
                    });
                    if (emptyState) emptyState.style.display = visibleCount === 0 ? 'flex' : 'none';
                });
            });
            const lightboxModal = document.getElementById('lightboxModal');
            const closeLightboxBtn = document.getElementById('closeLightboxModal');
            const lightboxImg = document.getElementById('lightboxImg');
            const lightboxBadge = document.getElementById('lightboxBadge');
            const lightboxTitle = document.getElementById('lightboxTitle');
            const lightboxDate = document.getElementById('lightboxDate');
            const lightboxDesc = document.getElementById('lightboxDesc');
            const downloadArea = document.getElementById('downloadArea');
            function openCardModal(card) {
                lightboxImg.src = card.querySelector('.card-media-wrapper img').src;
                lightboxImg.alt = card.getAttribute('data-title');
                lightboxBadge.textContent = card.getAttribute('data-category');
                lightboxTitle.textContent = card.getAttribute('data-title');
                lightboxDate.textContent = card.getAttribute('data-date');
                lightboxDesc.textContent = card.getAttribute('data-desc');

                const mUrl = card.getAttribute('data-materi-url');
                document.getElementById('attachmentTitle').textContent = card.getAttribute('data-title');
                document.getElementById('attachmentFilename').textContent = mUrl || 'Materi_Kegiatan.pdf';

                const downloadBtn = document.getElementById('btnDownloadMateri');
                if (downloadBtn) {
                    const newDownloadBtn = downloadBtn.cloneNode(true);
                    downloadBtn.parentNode.replaceChild(newDownloadBtn, downloadBtn);
                    newDownloadBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if (mUrl) window.open('/uploads/galeri/materi/' + mUrl, '_blank');
                    });
                }

                downloadArea.style.display = card.getAttribute('data-materi') === 'true' ? 'flex' : 'none';
                lightboxModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            galleryCards.forEach(card => {
                card.addEventListener('click', (e) => {
                    openCardModal(card);
                });

                // Explicit click on the orange material badge icon also opens modal
                const badge = card.querySelector('.material-badge');
                if (badge) {
                    badge.addEventListener('click', (e) => {
                        e.stopPropagation(); // prevent double-fire
                        openCardModal(card);
                    });
                }
            });
            const closeLightbox = () => { lightboxModal.classList.remove('active'); document.body.style.overflow = ''; };
            if (closeLightboxBtn) closeLightboxBtn.addEventListener('click', closeLightbox);
            if (lightboxModal) lightboxModal.addEventListener('click', e => { if (e.target === lightboxModal) closeLightbox(); });
            document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
            const expandBtn = document.getElementById('expandLightboxModal');
            if (expandBtn) expandBtn.addEventListener('click', e => {
                e.stopPropagation();
                const fsOverlay = document.createElement('div');
                fsOverlay.className = 'fullscreen-img-overlay';
                fsOverlay.innerHTML = `<div class="fs-overlay-close">&times;</div><img src="${lightboxImg.src}" alt="">`;
                document.body.appendChild(fsOverlay);
                fsOverlay.addEventListener('click', () => { document.body.removeChild(fsOverlay); });
                fsOverlay.querySelector('.fs-overlay-close').addEventListener('click', e => { e.stopPropagation(); document.body.removeChild(fsOverlay); });
            });
        });
    </script>
@endsection
