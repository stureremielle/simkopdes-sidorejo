@extends('layouts.admin', ['activeSidebar' => 'galeri'])

@section('title', 'Galeri Foto')
@section('breadcrumb', 'Galeri')

@section('styles')
<style>
    /* ========== PAGE HEADER ========== */
    .page-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .page-heading { margin: 0; }
    .page-heading h1 {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0 0 4px 0;
        letter-spacing: -0.5px;
    }
    .page-heading p {
        font-size: 0.88rem;
        color: #64748B;
        margin: 0;
        font-weight: 500;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }

    .btn-kategori {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 18px;
        background: #FFFFFF;
        color: #475569;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
    }
    .btn-kategori:hover { background: #F8FAFC; border-color: #CBD5E1; }

    .btn-tambah {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 10px 20px;
        background: #DC2626;
        color: #FFFFFF;
        border: none;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        font-family: inherit;
    }
    .btn-tambah:hover { background: #B91C1C; }

    /* ========== SEARCH BAR ========== */
    .search-box {
        position: relative;
        max-width: 360px;
        margin-bottom: 16px;
    }
    .search-box svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        width: 16px;
        height: 16px;
    }
    .search-box input {
        width: 100%;
        padding: 10px 14px 10px 40px;
        border: 1.5px solid #E2E8F0;
        border-radius: 10px;
        font-size: 0.88rem;
        font-family: inherit;
        color: #1E293B;
        outline: none;
        background: #FFFFFF;
        transition: border-color 0.2s;
    }
    .search-box input:focus { border-color: #DC2626; }
    .search-box input::placeholder { color: #94A3B8; }

    /* ========== FILTER PILLS ========== */
    .filter-scroller {
        overflow-x: auto;
        white-space: nowrap;
        margin-bottom: 28px;
        padding-bottom: 4px;
        -webkit-overflow-scrolling: touch;
    }
    .filter-row { display: inline-flex; gap: 8px; }
    .filter-pill {
        display: inline-block;
        padding: 8px 20px;
        background: #FFFFFF;
        color: #475569;
        border: 1.5px solid #E2E8F0;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        font-family: inherit;
    }
    .filter-pill:hover { background: #F8FAFC; border-color: #CBD5E1; }
    .filter-pill.active { background: #DC2626; color: #fff; border-color: #DC2626; }

    /* ========== GALLERY GRID ========== */
    .galeri-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        margin-bottom: 40px;
    }

    /* ========== PHOTO CARD ========== */
    .photo-card {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .photo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.07);
    }

    /* Photo area */
    .card-photo {
        position: relative;
        height: 188px;
        overflow: hidden;
        flex-shrink: 0;
        background: #F1F5F9;
    }
    .card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .card-photo-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        font-weight: 800;
        color: #FFFFFF;
        opacity: 0.85;
    }

    /* Category badge */
    .card-badge-kat {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #FFFFFF;
        z-index: 2;
        backdrop-filter: blur(4px);
    }

    /* File attachment icon (top-right) */
    .card-badge-file {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.35);
        border-radius: 8px;
        padding: 5px 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        z-index: 2;
    }

    /* Card body */
    .card-body {
        padding: 16px 18px 18px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .card-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0F172A;
        margin: 0 0 6px 0;
        line-height: 1.4;
    }
    .card-meta-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .card-date {
        font-size: 0.8rem;
        color: #94A3B8;
        font-weight: 500;
    }
    .card-materi-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #D97706;
    }

    /* Card actions row */
    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: auto;
    }
    .btn-card {
        flex: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 9px 12px;
        border-radius: 8px;
        font-size: 0.83rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        font-family: inherit;
        transition: all 0.2s ease;
    }
    .btn-card svg { width: 14px; height: 14px; flex-shrink: 0; }
    .btn-edit {
        background: #FFF1F2;
        color: #B91C1C;
        border: none;
    }
    .btn-edit:hover { background: #FEE2E2; }
    .btn-hapus {
        background: #FFF1F2;
        color: #B91C1C;
        border: none;
    }
    .btn-hapus:hover { background: #FEE2E2; }

    /* ========== EMPTY STATE ========== */
    .empty-state {
        grid-column: 1/-1;
        text-align: center;
        padding: 64px 24px;
        color: #94A3B8;
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* ========== MODALS ========== */
    .custom-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.42);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .custom-overlay.active { display: flex; }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        padding: 28px;
        width: 90%;
        max-width: 550px;
        max-height: 92vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        box-sizing: border-box;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-shrink: 0;
    }
    .modal-header h3 {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0F172A;
        margin: 0;
    }
    .btn-close-modal {
        background: none;
        border: none;
        color: #94A3B8;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 4px;
        border-radius: 6px;
        transition: color 0.15s;
    }
    .btn-close-modal:hover { color: #1E293B; }

    .modal-scroll { flex: 1; overflow-y: auto; padding-right: 6px; }

    .form-group {
        margin-bottom: 16px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-group label {
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
    }
    .form-group input[type="text"],
    .form-group select,
    .form-group textarea {
        padding: 10px 14px;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        font-size: 0.88rem;
        color: #0F172A;
        font-family: inherit;
        outline: none;
        background: #FAFAFA;
        transition: border-color 0.2s;
    }
    .form-group input:focus,
    .form-group select:focus { border-color: #DC2626; background: #fff; }

    .upload-zone {
        border: 2px dashed #CBD5E1;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        background: #FAFAFA;
        transition: all 0.2s;
    }
    .upload-zone:hover { border-color: #DC2626; background: #FFF1F2; }

    .materi-box {
        background: #FFFDF5;
        border: 1.5px solid #FFEACC;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .materi-collapsible {
        border-left: 3px solid #FACC15;
        padding-left: 14px;
        margin-left: 4px;
        display: none;
        flex-direction: column;
        gap: 10px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        border-top: 1px solid #F1F5F9;
        padding-top: 16px;
        margin-top: 16px;
        flex-shrink: 0;
    }
    .btn-cancel {
        background: #fff;
        color: #1E293B;
        border: 1.5px solid #CBD5E1;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        font-family: inherit;
        transition: background 0.2s;
    }
    .btn-cancel:hover { background: #F8FAFC; }
    .btn-submit {
        background: #DC2626;
        color: white;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.9rem;
        font-family: inherit;
        transition: background 0.2s;
    }
    .btn-submit:hover { background: #B91C1C; }

    /* ===== EDIT PHOTO MODAL CUSTOM STYLES ===== */
    .edit-photo-preview-container {
        position: relative;
        width: 100%;
        height: 220px;
        border-radius: 12px;
        overflow: hidden;
        background: #F1F5F9;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
    }
    .edit-photo-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .btn-remove-preview-photo {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(15, 23, 42, 0.6);
        color: #FFFFFF;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.15s;
        z-index: 5;
        padding: 0;
    }
    .btn-remove-preview-photo:hover {
        background: rgba(15, 23, 42, 0.84);
    }
    .btn-ganti-foto {
        width: 100%;
        padding: 10px 14px;
        background: #FFF1F2;
        color: #DC2626;
        border: 1.5px dashed #FCA5A5;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        transition: all 0.2s;
        margin-bottom: 20px;
        box-sizing: border-box;
        font-family: inherit;
    }
    .btn-ganti-foto:hover {
        background: #FEE2E2;
        border-color: #EF4444;
        color: #B91C1C;
    }
    .edit-photo-preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94A3B8;
        font-size: 0.85rem;
        gap: 6px;
    }
    
    /* Document/materi yellow-orange box */
    .materi-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        margin-bottom: 4px;
    }
    .materi-title-container {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #854D0E;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .materi-checkbox-container {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.83rem;
        font-weight: 600;
        color: #854D0E;
        cursor: pointer;
        user-select: none;
    }
    .materi-checkbox-container input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #D97706;
        cursor: pointer;
    }
    .materi-box input[type="text"].materi-input-style {
        background: #FFFFFF !important;
        border: 1.5px solid #FFEDD5 !important;
        color: #0F172A;
        font-weight: 500;
    }
    .materi-box input[type="text"].materi-input-style::placeholder {
        color: #C2410C;
        opacity: 0.5;
    }
    .materi-box input[type="text"].materi-input-style:focus {
        border-color: #D97706 !important;
        outline: none;
    }
    .materi-box label.materi-label-style {
        font-size: 0.82rem;
        font-weight: 700;
        color: #854D0E;
        margin-bottom: 2px;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 1100px) {
        .galeri-grid { grid-template-columns: repeat(2,1fr); }
    }
    @media (max-width: 640px) {
        .galeri-grid { grid-template-columns: 1fr; }
        .page-header-row { flex-direction: column; }
    }
</style>
@endsection

@section('content')


{{-- Page Header --}}
<div class="page-header-row">
    <div class="page-heading">
        <h1>Galeri Foto</h1>
        <p>Kelola koleksi foto kegiatan koperasi</p>
    </div>
    <div class="header-actions">
        <button onclick="openKategoriModal()" class="btn-kategori">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            Kategori
        </button>
        <button onclick="openTambahModal()" class="btn-tambah">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Gallery
        </button>
    </div>
</div>

{{-- Search Bar --}}
<form method="GET" action="{{ route('admin.galeri') }}">
    @if($kategoriFilter)
        <input type="hidden" name="kategori" value="{{ $kategoriFilter }}">
    @endif
    <div class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari foto...">
    </div>
</form>

{{-- Filter Pills --}}
<div class="filter-scroller">
    <div class="filter-row" id="filterPillsContainer">
        <a href="{{ route('admin.galeri', ['search' => $search]) }}" class="filter-pill {{ empty($kategoriFilter) ? 'active' : '' }}">Semua</a>
        @foreach($kategoriList as $kat)
            <a href="{{ route('admin.galeri', ['kategori' => $kat, 'search' => $search]) }}"
               class="filter-pill {{ $kategoriFilter === $kat ? 'active' : '' }}">{{ $kat }}</a>
        @endforeach
    </div>
</div>

{{-- Gallery Grid --}}
<div class="galeri-grid">
    @forelse($galeriList as $item)
        @php
            // Category color for badge & placeholder bg
            $katLower = strtolower($item->kategori ?? '');
            if (str_contains($katLower, 'rapat') || str_contains($katLower, 'musyawarah')) {
                $badgeColor = '#DC2626'; $bgColor = 'linear-gradient(135deg,#DC2626,#991B1B)';
            } elseif (str_contains($katLower, 'panen') || str_contains($katLower, 'tani') || str_contains($katLower, 'pertanian')) {
                $badgeColor = '#16A34A'; $bgColor = 'linear-gradient(135deg,#16A34A,#14532D)';
            } elseif (str_contains($katLower, 'pelatihan') || str_contains($katLower, 'didik')) {
                $badgeColor = '#7C3AED'; $bgColor = 'linear-gradient(135deg,#7C3AED,#4C1D95)';
            } elseif (str_contains($katLower, 'sosial')) {
                $badgeColor = '#0891B2'; $bgColor = 'linear-gradient(135deg,#0891B2,#164E63)';
            } else {
                $badgeColor = '#EA580C'; $bgColor = 'linear-gradient(135deg,#EA580C,#9A3412)';
            }

            $hasPhoto = !empty($item->gambar_url)
                         && !str_starts_with($item->gambar_url, 'http')
                         && file_exists(public_path($item->gambar_url));
            $hasRemotePhoto = !empty($item->gambar_url) && str_starts_with($item->gambar_url, 'http');

            try {
                $formattedDate = $item->created_at
                    ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('M Y')
                    : '-';
            } catch(\Exception $e) { $formattedDate = '-'; }
        @endphp

        <div class="photo-card">
            {{-- Photo / placeholder --}}
            <div class="card-photo">
                @if($hasPhoto)
                    <img src="{{ asset($item->gambar_url) }}" alt="{{ $item->judul }}" loading="lazy">
                @elseif($hasRemotePhoto)
                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" loading="lazy">
                @else
                    <div class="card-photo-placeholder" style="background: {{ $bgColor }};">
                        {{ strtoupper(substr($item->judul, 0, 1)) }}
                    </div>
                @endif

                {{-- Category badge --}}
                <span class="card-badge-kat" style="background: {{ $badgeColor }};">
                    {{ $item->kategori }}
                </span>

                {{-- File icon if has materi --}}
                @if($item->materi_url)
                <div class="card-badge-file" title="Ada file materi">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                </div>
                @endif
            </div>

            {{-- Card Body --}}
            <div class="card-body">
                <h3 class="card-title">{{ $item->judul }}</h3>
                <div class="card-meta-row">
                    <span class="card-date">{{ $formattedDate }}</span>
                    @if($item->materi_url)
                        <span class="card-materi-label">Ada materi</span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="card-actions">
                    <button onclick="openEditModal({{ json_encode($item) }})" class="btn-card btn-edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        Edit
                    </button>
                    <button type="button" class="btn-card btn-hapus" style="width:100%;" 
                            data-id="{{ $item->id }}" 
                            data-judul="{{ $item->judul }}" 
                            onclick="triggerConfirmDelete(this)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Belum ada data galeri kegiatan.
        </div>
    @endforelse
</div>

{{-- ===== MODAL: KATEGORI ===== --}}
<div class="custom-overlay" id="kategoriModal">
    <div class="modal-box" style="max-width:440px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                <h3>Kategori Galeri</h3>
            </div>
            <button class="btn-close-modal" onclick="closeKategoriModal()">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="display:flex;gap:8px;margin-bottom:16px;">
            <input type="text" id="newCategoryInput" placeholder="Nama kategori baru..."
                style="flex:1;padding:10px 14px;border:1.5px solid #E2E8F0;border-radius:8px;font-size:0.9rem;outline:none;font-family:inherit;background:#FAFAFA;">
            <button onclick="addNewCategory()" type="button"
                style="width:40px;height:40px;background:#FFF1F2;color:#DC2626;border:none;border-radius:8px;font-size:1.3rem;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;transition:background 0.2s;"
                onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='#FFF1F2'">+</button>
        </div>
        <div id="categoriesListContainer" style="max-height:240px;overflow-y:auto;display:flex;flex-direction:column;gap:8px;margin-bottom:20px;"></div>
        <button onclick="closeKategoriModal()" type="button" class="btn-submit" style="width:100%;text-align:center;">Selesai</button>
    </div>
</div>

{{-- ===== MODAL: TAMBAH KEGIATAN ===== --}}
<div class="custom-overlay" id="tambahModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Tambah Foto Baru</h3>
            <button class="btn-close-modal" onclick="closeTambahModal()">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.galeri.store') }}" enctype="multipart/form-data"
              onsubmit="return validateUploadForm(this)" style="display:flex;flex-direction:column;flex:1;overflow:hidden;margin:0;">
            @csrf
            <input type="hidden" name="status" value="aktif">

            <div class="modal-scroll">
                {{-- Photo Upload Zone --}}
                <div class="form-group" style="margin-bottom: 8px;">
                    <label>Foto <span style="color:#EF4444">*</span></label>
                    <div id="tPhotoPreviewContainer" class="edit-photo-preview-container" onclick="document.getElementById('tGambarFileInput').click()" style="cursor: pointer; margin-bottom: 20px;">
                        <div class="edit-photo-preview-placeholder" id="tPhotoPlaceholder">
                            <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#94A3B8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <span style="font-weight:600;color:#334155;font-size:0.88rem;">Belum ada foto dipilih</span>
                            <span style="font-size:0.78rem;color:#94A3B8;">Klik area ini untuk memilih foto</span>
                        </div>
                    </div>
                    <input type="file" name="gambar_file" id="tGambarFileInput" accept="image/*" style="display:none;" onchange="handleTambahPhotoSelect(this)">
                </div>

                {{-- Judul --}}
                <div class="form-group">
                    <label>Judul <span style="color:#EF4444">*</span></label>
                    <input type="text" name="judul" required placeholder="Nama kegiatan...">
                </div>

                {{-- Kategori + Tanggal --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group" style="position:relative;">
                        <label>Kategori</label>
                        <select name="kategori" id="tKategoriSelect" required style="appearance:none;-webkit-appearance:none;"></select>
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;right:12px;bottom:13px;pointer-events:none;color:#64748B;"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <div class="form-group">
                        <label>Tanggal <span style="color:#EF4444">*</span></label>
                        <input type="text" name="periode" id="tPeriode" required placeholder="mis. Des 2024">
                    </div>
                </div>

                {{-- File Materi --}}
                <div class="materi-box" style="margin-top:14px;">
                    <div class="materi-header-row">
                        <div class="materi-title-container">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#D97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            File Materi (opsional)
                        </div>
                        <label class="materi-checkbox-container">
                            <input type="checkbox" id="tHasMateri" onchange="toggleTambahMateri(this.checked)">
                            Ada file
                        </label>
                    </div>

                    <div id="tMateriBox" class="materi-collapsible" style="padding-left:0;margin-left:0;border-left:none;margin-top:8px;">
                        <div class="form-group" style="margin-bottom:10px;">
                            <label class="materi-label-style">Nama File</label>
                            <input type="text" id="tMateriFileNameDisplay" class="materi-input-style" placeholder="Klik untuk upload file..." readonly style="cursor:pointer;" onclick="document.getElementById('tMateriFileInput').click()">
                        </div>
                        <input type="file" name="materi_file" id="tMateriFileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*" style="display:none;" onchange="handleTambahMateriSelect(this)">
                        <div class="form-group">
                            <label class="materi-label-style">Deskripsi File</label>
                            <input type="text" name="keterangan" class="materi-input-style" placeholder="Deskripsi file...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="display: flex; gap: 12px; margin-top: 16px; border-top: 1px solid #F1F5F9; padding-top: 16px; justify-content: space-between; align-items: center; width: 100%;">
                <button type="button" class="btn-cancel" onclick="closeTambahModal()" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; border: 1.5px solid #E2E8F0; background: #FFFFFF; color: #475569; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">Batal</button>
                <button type="submit" class="btn-submit" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; background-color: #B91C1C; border: none; color: white; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#991B1B'" onmouseout="this.style.background='#B91C1C'">Tambah Foto</button>
            </div>
        </form>
    </div>
</div>


{{-- ===== MODAL: EDIT KEGIATAN ===== --}}
<div class="custom-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Foto</h3>
            <button class="btn-close-modal" onclick="closeEditModal()">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form id="editForm" method="POST" action="" enctype="multipart/form-data"
              onsubmit="return validateUploadForm(this)" style="display:flex;flex-direction:column;flex:1;overflow:hidden;margin:0;">
            @csrf
            @method('PUT')
            <input type="hidden" name="status" id="editStatus" value="aktif">

            <!-- Deletion Flags -->
            <input type="hidden" name="remove_gambar" id="editRemoveGambar" value="0">
            <input type="hidden" name="remove_materi" id="editRemoveMateri" value="0">

            <div class="modal-scroll">
                <!-- Foto Preview & Ganti Foto -->
                <div class="form-group" style="margin-bottom: 8px;">
                    <label>Foto <span style="color:#EF4444">*</span></label>
                    <div class="edit-photo-preview-container" id="editPhotoPreviewContainer">
                        <!-- Will be populated dynamically by JS on open or select -->
                    </div>
                </div>
                
                <!-- Ganti Foto Button container -->
                <button type="button" class="btn-ganti-foto" onclick="document.getElementById('editGambarFileInput').click()">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Ganti Foto
                </button>
                <input type="file" name="gambar_file" id="editGambarFileInput" accept="image/*" style="display:none;" onchange="handleEditPhotoSelect(this)">

                <div class="form-group">
                    <label>Judul <span style="color:#EF4444">*</span></label>
                    <input type="text" name="judul" id="editJudul" required placeholder="Judul foto...">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group" style="position:relative;">
                        <label>Kategori</label>
                        <select name="kategori" id="editKategori" required style="appearance:none;-webkit-appearance:none;"></select>
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;right:12px;bottom:13px;pointer-events:none;color:#64748B;"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <div class="form-group">
                        <label>Tanggal <span style="color:#EF4444">*</span></label>
                        <input type="text" name="periode" id="editPeriode" required placeholder="mis. Des 2024">
                    </div>
                </div>

                <div class="materi-box" style="margin-top: 14px;">
                    <div class="materi-header-row">
                        <div class="materi-title-container">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#D97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            File Materi (opsional)
                        </div>
                        <label class="materi-checkbox-container">
                            <input type="checkbox" id="editHasMateri" onchange="toggleEditMateri(this.checked)">
                            Ada file
                        </label>
                    </div>
                    
                    <div id="editMateriBox" class="materi-collapsible" style="padding-left:0; margin-left:0; border-left:none; margin-top:8px;">
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="materi-label-style">Nama File</label>
                            <input type="text" id="editMateriFileNameDisplay" class="materi-input-style" placeholder="Klik untuk upload file..." readonly style="cursor:pointer;" onclick="document.getElementById('editMateriFileInput').click()">
                        </div>
                        <input type="file" name="materi_file" id="editMateriFileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*" style="display:none;" onchange="handleEditMateriSelect(this)">
                        
                        <div class="form-group">
                            <label class="materi-label-style">Deskripsi File</label>
                            <input type="text" name="keterangan" id="editKeterangan" class="materi-input-style" placeholder="Deskripsi file...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit" style="background:#B91C1C;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL: KONFIRMASI HAPUS ===== --}}
<div class="custom-overlay" id="confirmDeleteModal">
    <div class="modal-box" style="max-width: 440px; padding: 24px;">
        <div style="display: flex; gap: 16px; align-items: flex-start; margin-bottom: 24px;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: #FEE2E2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 1.12rem; font-weight: 700; color: #0F172A; margin: 0 0 6px 0; font-family: inherit;">Hapus Foto?</h3>
                <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.5; font-family: inherit;">
                    Anda akan menghapus data anggota <strong id="deleteTargetTitle" style="color: #0F172A;"></strong>.
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" class="btn-cancel" onclick="closeConfirmDeleteModal()" style="flex: 1; padding: 10px 16px; font-weight: 600; text-align: center; border: 1.5px solid #E2E8F0; border-radius: 8px; font-family: inherit; font-size: 0.9rem; background: #fff; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#fff'">
                Batal
            </button>
            <form id="confirmDeleteForm" method="POST" action="" style="flex: 1; margin: 0; display: flex;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-submit" style="width: 100%; padding: 10px 16px; font-weight: 700; text-align: center; background: #DC2626; color: white; border: none; border-radius: 8px; font-family: inherit; font-size: 0.9rem; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">
                    Ya, Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    /* ===== CATEGORY STATE ===== */
    let categories = @json($kategoriList);

    function renderCategories() {
        const c = document.getElementById('categoriesListContainer');
        if (!c) return;
        c.innerHTML = '';
        categories.forEach((cat, i) => {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#FAFAFA;border-radius:8px;font-size:0.88rem;font-weight:600;color:#334155;border:1px solid #F1F5F9;';
            const lbl = document.createElement('span');
            lbl.textContent = cat;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.style.cssText = 'background:none;border:none;cursor:pointer;color:#94A3B8;display:inline-flex;align-items:center;padding:2px;';
            btn.innerHTML = `<svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>`;
            btn.onmouseover = () => btn.style.color = '#EF4444';
            btn.onmouseout  = () => btn.style.color = '#94A3B8';
            btn.onclick = () => deleteCategory(cat);
            row.appendChild(lbl);
            row.appendChild(btn);
            c.appendChild(row);
        });
    }

    function addNewCategory() {
        const input = document.getElementById('newCategoryInput');
        const val = input.value.trim();
        if (!val) return;
        if (categories.includes(val)) {
            alert('Kategori tersebut sudah terdaftar.');
            return;
        }

        fetch("{{ route('admin.galeri.kategori.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ kategori: val })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                categories.push(val);
                input.value = '';
                renderCategories();
                updateSelects();
            } else {
                alert(data.message || 'Gagal menambahkan kategori.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function deleteCategory(catName) {
        if (confirm(`Hapus kategori "${catName}"?`)) {
            fetch("{{ url('/admin/galeri/kategori') }}/" + encodeURIComponent(catName), {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    categories = categories.filter(c => c !== catName);
                    renderCategories();
                    updateSelects();
                } else {
                    alert(data.message || 'Gagal menghapus kategori.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            });
        }
    }

    function updateSelects() {
        ['tKategoriSelect','editKategori'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            const cur = el.value;
            el.innerHTML = '';
            categories.forEach(c => {
                const o = document.createElement('option');
                o.value = c; o.textContent = c;
                el.appendChild(o);
            });
            if (cur && categories.includes(cur)) el.value = cur;
        });
    }

    /* ===== HELPERS ===== */
    function handleFileSelect(input, labelId) {
        if (input.files && input.files[0]) {
            document.getElementById(labelId).textContent = input.files[0].name;
        }
    }

    function toggleMateri(boxId, show) {
        const box = document.getElementById(boxId);
        if (box) box.style.display = show ? 'flex' : 'none';
    }

    /* ===== EDIT MODAL HELPERS ===== */
    function renderPhotoPreview(url) {
        const container = document.getElementById('editPhotoPreviewContainer');
        if (!container) return;
        
        if (url) {
            container.innerHTML = `
                <img src="${url}" alt="Preview Foto" id="editPhotoPreviewImage">
                <button type="button" class="btn-remove-preview-photo" onclick="clearPhotoPreview()" title="Hapus Foto">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            `;
        } else {
            container.innerHTML = `
                <div class="edit-photo-preview-placeholder">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span>Tidak ada foto terpilih. Klik "Ganti Foto".</span>
                </div>
            `;
        }
    }

    function handleEditPhotoSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = e => {
                renderPhotoPreview(e.target.result);
                document.getElementById('editRemoveGambar').value = '0';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearPhotoPreview() {
        renderPhotoPreview(null);
        document.getElementById('editRemoveGambar').value = '1';
        document.getElementById('editGambarFileInput').value = '';
    }

    function toggleEditMateri(show) {
        const box = document.getElementById('editMateriBox');
        if (box) box.style.display = show ? 'flex' : 'none';
        
        if (!show) {
            document.getElementById('editRemoveMateri').value = '1';
            document.getElementById('editMateriFileInput').value = '';
            document.getElementById('editMateriFileNameDisplay').value = '';
            document.getElementById('editKeterangan').value = '';
        } else {
            document.getElementById('editRemoveMateri').value = '0';
        }
    }

    function handleEditMateriSelect(input) {
        if (input.files && input.files[0]) {
            document.getElementById('editMateriFileNameDisplay').value = input.files[0].name;
            document.getElementById('editRemoveMateri').value = '0';
        }
    }

    /* ===== IMAGE COMPRESSION ===== */
    let isCompressing = false;
    function validateUploadForm(form) {
        if (form.dataset.ready === 'true') return true;
        const gambarInput = form.querySelector('input[name="gambar_file"]');
        const materiInput = form.querySelector('input[name="materi_file"]');
        if (materiInput && materiInput.files && materiInput.files[0]) {
            if (materiInput.files[0].size > 10 * 1024 * 1024) {
                alert('Ukuran file materi terlalu besar! Maksimal 10 MB.'); return false;
            }
        }
        if (gambarInput && gambarInput.files && gambarInput.files[0]) {
            const file = gambarInput.files[0];
            if (file.size > 1024 * 1024 && file.type.startsWith('image/')) {
                if (isCompressing) return false;
                isCompressing = true;
                const btn = form.querySelector('button[type="submit"]');
                const txt = btn.textContent; btn.disabled = true; btn.textContent = 'Mengompres...';
                const reader = new FileReader();
                reader.onload = e => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let w = img.width, h = img.height, max = 1600;
                        if (w > h) { if (w > max) { h *= max/w; w = max; } }
                        else { if (h > max) { w *= max/h; h = max; } }
                        canvas.width = w; canvas.height = h;
                        canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                        canvas.toBlob(blob => {
                            const dt = new DataTransfer();
                            dt.items.add(new File([blob], file.name, {type:'image/jpeg', lastModified:Date.now()}));
                            gambarInput.files = dt.files;
                            form.dataset.ready = 'true';
                            isCompressing = false;
                            btn.disabled = false; btn.textContent = txt;
                            form.submit();
                        }, 'image/jpeg', 0.82);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
                return false;
            } else if (file.size > 3 * 1024 * 1024) {
                alert('Ukuran foto terlalu besar! Maksimal 3 MB.'); return false;
            }
        }
        return true;
    }

    /* ===== MODAL CONTROLS ===== */
    function openKategoriModal()  { document.getElementById('kategoriModal').classList.add('active'); }
    function closeKategoriModal() { document.getElementById('kategoriModal').classList.remove('active'); }

    function openTambahModal() {
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const now = new Date();
        const pInput = document.getElementById('tPeriode');
        if (pInput) pInput.value = `${months[now.getMonth()]} ${now.getFullYear()}`;
        
        // Reset photo preview to placeholder
        renderTambahPhotoPreview(null);
        
        document.getElementById('tGambarFileInput').value = '';
        document.getElementById('tHasMateri').checked = false;
        document.getElementById('tMateriFileInput').value = '';
        document.getElementById('tMateriFileNameDisplay').value = '';
        toggleTambahMateri(false);
        updateSelects();
        document.getElementById('tambahModal').classList.add('active');
    }
    function closeTambahModal() { document.getElementById('tambahModal').classList.remove('active'); }

    function renderTambahPhotoPreview(url) {
        const container = document.getElementById('tPhotoPreviewContainer');
        if (!container) return;
        if (url) {
            container.innerHTML = `
                <img src="${url}" alt="Preview Foto" id="tPhotoPreviewImage">
                <button type="button" class="btn-remove-preview-photo" onclick="clearTambahPhotoPreview(); event.stopPropagation();" title="Hapus Foto">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            `;
        } else {
            container.innerHTML = `
                <div class="edit-photo-preview-placeholder">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span>Belum ada foto dipilih. Klik area ini untuk memilih foto.</span>
                </div>
            `;
        }
    }

    function clearTambahPhotoPreview() {
        renderTambahPhotoPreview(null);
        document.getElementById('tGambarFileInput').value = '';
    }

    function handleTambahPhotoSelect(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = e => renderTambahPhotoPreview(e.target.result);
            reader.readAsDataURL(file);
        }
    }

    function handleTambahMateriSelect(input) {
        if (input.files && input.files[0]) {
            const display = document.getElementById('tMateriFileNameDisplay');
            if (display) display.value = input.files[0].name;
        }
    }

    function toggleTambahMateri(show) {
        const box = document.getElementById('tMateriBox');
        if (box) box.style.display = show ? 'flex' : 'none';
        if (!show) {
            document.getElementById('tMateriFileInput').value = '';
            const display = document.getElementById('tMateriFileNameDisplay');
            if (display) display.value = '';
        }
    }

    function openEditModal(item) {
        const form = document.getElementById('editForm');
        form.action = `{{ url('/admin/galeri') }}/${item.id}`;
        document.getElementById('editJudul').value = item.judul || '';
        document.getElementById('editStatus').value = item.status || 'aktif';
        document.getElementById('editKeterangan').value = item.keterangan || '';
        document.getElementById('editRemoveGambar').value = '0';
        document.getElementById('editRemoveMateri').value = '0';
        document.getElementById('editGambarFileInput').value = '';
        document.getElementById('editMateriFileInput').value = '';

        if (item.created_at) {
            const d = new Date(item.created_at);
            const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            document.getElementById('editPeriode').value = isNaN(d.getTime()) ? '' : `${months[d.getMonth()]} ${d.getFullYear()}`;
        } else {
            document.getElementById('editPeriode').value = '';
        }

        updateSelects();
        if (item.kategori && categories.includes(item.kategori)) {
            document.getElementById('editKategori').value = item.kategori;
        }

        // Render Photo Preview
        if (item.gambar_url) {
            const assetUrl = item.gambar_url.startsWith('http') ? item.gambar_url : `{{ asset('') }}${item.gambar_url}`;
            renderPhotoPreview(assetUrl);
        } else {
            renderPhotoPreview(null);
        }

        const hasMateri = !!item.materi_url;
        document.getElementById('editHasMateri').checked = hasMateri;
        toggleEditMateri(hasMateri);
        document.getElementById('editMateriFileNameDisplay').value = hasMateri ? item.materi_url : '';

        document.getElementById('editModal').classList.add('active');
    }
    function closeEditModal() { document.getElementById('editModal').classList.remove('active'); }

    function triggerConfirmDelete(button) {
        const id = button.getAttribute('data-id');
        const judul = button.getAttribute('data-judul');
        
        const modal = document.getElementById('confirmDeleteModal');
        const titleSpan = document.getElementById('deleteTargetTitle');
        const form = document.getElementById('confirmDeleteForm');
        
        if (modal && titleSpan && form) {
            titleSpan.textContent = judul;
            form.action = `{{ url('/admin/galeri') }}/${id}`;
            modal.classList.add('active');
        }
    }

    function closeConfirmDeleteModal() {
        const modal = document.getElementById('confirmDeleteModal');
        if (modal) modal.classList.remove('active');
    }

    // Close on overlay click
    window.addEventListener('click', e => {
        if (e.target.classList.contains('custom-overlay')) e.target.classList.remove('active');
    });

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        renderCategories();
        updateSelects();
        const inp = document.getElementById('newCategoryInput');
        if (inp) inp.addEventListener('keypress', e => { if (e.key === 'Enter') addNewCategory(); });
    });
</script>
@endsection
