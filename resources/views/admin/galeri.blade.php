@extends('layouts.admin', ['activeSidebar' => 'galeri'])

@section('title', 'Galeri Foto')
@section('breadcrumb', 'Galeri')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/galeri.css') }}?v={{ filemtime(public_path('assets/css/admin/galeri.css')) }}">
@endsection

@section('content')


{{-- Header Halaman --}}
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
            Tambah Galeri
        </button>
    </div>
</div>

{{-- Bilah Pencarian --}}
<form method="GET" action="{{ route('admin.galeri') }}">
    @if($kategoriFilter)
        <input type="hidden" name="kategori" value="{{ $kategoriFilter }}">
    @endif
    <div class="search-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari foto...">
    </div>
</form>

{{-- Tombol Filter --}}
<div class="filter-scroller">
    <div class="filter-row" id="filterPillsContainer">
        <a href="{{ route('admin.galeri', ['search' => $search]) }}" class="filter-pill {{ empty($kategoriFilter) ? 'active' : '' }}">Semua</a>
        @foreach($kategoriList as $kat)
            <a href="{{ route('admin.galeri', ['kategori' => $kat, 'search' => $search]) }}"
               class="filter-pill {{ $kategoriFilter === $kat ? 'active' : '' }}">{{ $kat }}</a>
        @endforeach
    </div>
</div>

{{-- Kisi Galeri --}}
<div class="galeri-grid">
    @forelse($galeriList as $item)
        @php
            $hasPhoto = !empty($item->gambar)
                         && !str_starts_with($item->gambar, 'http')
                         && file_exists(public_path($item->gambar));
            $hasRemotePhoto = !empty($item->gambar) && str_starts_with($item->gambar, 'http');

            try {
                $formattedDate = $item->created_at
                    ? \App\Helpers\Helper::formatBulanTahun($item->created_at)
                    : '-';
            } catch(\Exception $e) { $formattedDate = '-'; }
        @endphp

        <div class="photo-card">
            {{-- Photo / placeholder --}}
            <div class="card-photo">
                @if($hasPhoto)
                    <img src="{{ asset($item->gambar) }}" alt="{{ $item->judul }}" loading="lazy">
                @elseif($hasRemotePhoto)
                    <img src="{{ $item->gambar }}" alt="{{ $item->judul }}" loading="lazy">
                @else
                    <div class="card-photo-placeholder">
                        {{ strtoupper(substr($item->judul, 0, 1)) }}
                    </div>
                @endif

                {{-- Category badge --}}
                <span class="card-badge-kat">
                    {{ $item->kategori }}
                </span>

                {{-- File icon if has materi --}}
                @if($item->materi)
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
                    @if($item->materi)
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
            <input type="text" id="newCategoryInput" placeholder="Nama kategori baru..." maxlength="20"
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
                @php
                    $topErrors = collect($errors->getBag('default')->getMessages())->except(['materi_file'])->flatten();
                @endphp
                @if ($topErrors->isNotEmpty())
                <div style="background:#fee2e2;color:#dc2626;padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:0.83rem;font-weight:600;">
                    @foreach ($topErrors as $err)
                        <div>✗ {{ $err }}</div>
                    @endforeach
                </div>
                @endif

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
                    <div id="tPhotoError" style="color:#EF4444;font-size:0.78rem;margin-top:4px;margin-bottom:12px;font-weight:600;display:none;">Foto kegiatan wajib diisi.</div>
                    @error('gambar_file')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                </div>

                {{-- Judul --}}
                <div class="form-group">
                    <label>Judul <span style="color:#EF4444">*</span></label>
                    <input type="text" name="judul" required maxlength="80" placeholder="Nama kegiatan..." value="{{ old('judul') }}">
                    @error('judul')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                </div>

                {{-- Kategori + Tanggal --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group" style="position:relative;">
                        <label>Kategori <span style="color:#EF4444">*</span></label>
                        <select name="kategori" id="tKategoriSelect" required style="appearance:none;-webkit-appearance:none;"></select>
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;right:12px;bottom:13px;pointer-events:none;color:#64748B;"><polyline points="6 9 12 15 18 9"/></svg>
                        @error('kategori')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal <span style="color:#EF4444">*</span></label>
                        <input type="text" name="periode" id="tPeriode" required placeholder="mis. Des 2024" value="{{ old('periode') }}">
                        @error('periode')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
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
                            <input type="checkbox" id="tHasMateri" name="has_materi" value="1" onchange="toggleTambahMateri(this.checked)">
                            Ada file
                        </label>
                    </div>

                    <div id="tMateriBox" class="materi-collapsible" style="padding-left:0;margin-left:0;border-left:none;margin-top:8px;">
                        <div class="form-group" style="margin-bottom:10px;">
                            <label class="materi-label-style">Nama File <span style="color:#EF4444">*</span></label>
                            <input type="text" id="tMateriFileNameDisplay" class="materi-input-style" placeholder="Klik untuk upload file..." readonly style="cursor:pointer;" onclick="document.getElementById('tMateriFileInput').click()">
                            <div id="tMateriError" style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;display:none;"></div>
                            @error('materi_file')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <input type="file" name="materi_file" id="tMateriFileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*" style="display:none;" onchange="handleTambahMateriSelect(this)">
                        <div class="form-group">
                            <label class="materi-label-style">Deskripsi File <span style="color:#EF4444">*</span></label>
                            <input type="text" name="keterangan" class="materi-input-style" placeholder="Deskripsi file..." value="{{ old('keterangan') }}">
                            @error('keterangan')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
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
                @php
                    $topErrors = collect($errors->getBag('default')->getMessages())->except(['materi_file'])->flatten();
                @endphp
                @if ($topErrors->isNotEmpty())
                <div style="background:#fee2e2;color:#dc2626;padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:0.83rem;font-weight:600;">
                    @foreach ($topErrors as $err)
                        <div>✗ {{ $err }}</div>
                    @endforeach
                </div>
                @endif

                <!-- Pratinjau Foto & Ganti Foto -->
                <div class="form-group" style="margin-bottom: 8px;">
                    <label>Foto <span style="color:#EF4444">*</span></label>
                    <div class="edit-photo-preview-container" id="editPhotoPreviewContainer">
                        {{-- Akan diisi secara dinamis oleh JavaScript --}}
                        <div id="ePhotoPlaceholder" style="text-align: center;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #F1F5F9; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; color: #94A3B8;">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                            <span style="font-size: 0.85rem; color: #64748B; font-weight: 500;">Pilih gambar foto...</span>
                        </div>
                        <img id="editPhotoPreviewImg" src="" alt="Pratinjau Foto" style="display:none; width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div id="editPhotoError" style="color:#EF4444;font-size:0.78rem;margin-top:4px;margin-bottom:12px;font-weight:600;display:none;">Foto kegiatan wajib diisi.</div>
                    @error('gambar_file')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                </div>
                
                <!-- Pembungkus tombol Ganti Foto -->
                <button type="button" class="btn-ganti-foto" onclick="document.getElementById('editGambarFileInput').click()">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Ganti Foto
                </button>
                <input type="file" name="gambar_file" id="editGambarFileInput" accept="image/*" style="display:none;" onchange="handleEditPhotoSelect(this)">

                <div class="form-group">
                    <label>Judul <span style="color:#EF4444">*</span></label>
                    <input type="text" name="judul" id="editJudul" required maxlength="80" placeholder="Judul foto...">
                    @error('judul')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group" style="position:relative;">
                        <label>Kategori <span style="color:#EF4444">*</span></label>
                        <select name="kategori" id="editKategori" required style="appearance:none;-webkit-appearance:none;"></select>
                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;right:12px;bottom:13px;pointer-events:none;color:#64748B;"><polyline points="6 9 12 15 18 9"/></svg>
                        @error('kategori')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Tanggal <span style="color:#EF4444">*</span></label>
                        <input type="text" name="periode" id="editPeriode" required placeholder="mis. Des 2024">
                        @error('periode')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="materi-box" style="margin-top: 14px;">
                    <div class="materi-header-row">
                        <div class="materi-title-container">
                            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="#D97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            File Materi (opsional)
                        </div>
                        <label class="materi-checkbox-container">
                            <input type="checkbox" id="editHasMateri" name="has_materi" value="1" onchange="toggleEditMateri(this.checked)">
                            Ada file
                        </label>
                    </div>
                    
                    <div id="editMateriBox" class="materi-collapsible" style="padding-left:0; margin-left:0; border-left:none; margin-top:8px;">
                        <div class="form-group" style="margin-bottom: 10px;">
                            <label class="materi-label-style">Nama File <span style="color:#EF4444">*</span></label>
                            <input type="text" id="editMateriFileNameDisplay" class="materi-input-style" placeholder="Klik untuk upload file..." readonly style="cursor:pointer;" onclick="document.getElementById('editMateriFileInput').click()">
                            <div id="editMateriError" style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;display:none;"></div>
                            @error('materi_file')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <input type="file" name="materi_file" id="editMateriFileInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,image/*" style="display:none;" onchange="handleEditMateriSelect(this)">
                        
                        <div class="form-group">
                            <label class="materi-label-style">Deskripsi File <span style="color:#EF4444">*</span></label>
                            <input type="text" name="keterangan" id="editKeterangan" class="materi-input-style" placeholder="Deskripsi file...">
                            @error('keterangan')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
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
                    Anda akan menghapus data foto <strong id="deleteTargetTitle" style="color: #0F172A; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;"></strong>.
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

    /* ===== PENOLONG MODAL EDIT ===== */
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
            const ext = file.name.split('.').pop().toLowerCase();
            const allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            if (!allowed.includes(ext)) {
                showPhotoFieldError('editPhotoPreviewContainer', 'editPhotoError', 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.');
                clearPhotoPreview();
                return;
            }
            clearPhotoFieldError('editPhotoPreviewContainer', 'editPhotoError');
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
        const errEl = document.getElementById('editMateriError');
        if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
        
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
        const errorEl = document.getElementById('editMateriError');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            const allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpeg', 'png', 'jpg', 'gif', 'svg', 'webp'];

            if (!allowed.includes(ext)) {
                if (errorEl) {
                    errorEl.textContent = 'File materi harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, SVG, atau WEBP.';
                    errorEl.style.display = 'block';
                }
                input.value = '';
                document.getElementById('editMateriFileNameDisplay').value = '';
                return;
            } else {
                if (errorEl) {
                    errorEl.textContent = '';
                    errorEl.style.display = 'none';
                }
            }
            document.getElementById('editMateriFileNameDisplay').value = file.name;
            document.getElementById('editRemoveMateri').value = '0';
        }
    }

    /* ===== KOMPRESI GAMBAR & VALIDASI FORMULIR ===== */
    function showPhotoFieldError(containerId, errorId, msg) {
        const container = document.getElementById(containerId);
        const errorEl = document.getElementById(errorId);
        if (container) {
            container.style.border = '2px solid #EF4444';
            container.style.borderRadius = '12px';
        }
        if (errorEl) {
            errorEl.textContent = msg || 'Foto kegiatan wajib diisi.';
            errorEl.style.display = 'block';
        }
    }

    function clearPhotoFieldError(containerId, errorId) {
        const container = document.getElementById(containerId);
        const errorEl = document.getElementById(errorId);
        if (container) {
            container.style.border = '';
        }
        if (errorEl) {
            errorEl.style.display = 'none';
        }
    }

    let isCompressing = false;
    function validateUploadForm(form) {
        if (form.dataset.ready === 'true') return true;

        const isEdit = form.id === 'editForm';
        const gambarInput = form.querySelector('input[name="gambar_file"]');
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];

        // 1. Photo validation
        if (!isEdit) {
            if (!gambarInput || !gambarInput.files || gambarInput.files.length === 0) {
                showPhotoFieldError('tPhotoPreviewContainer', 'tPhotoError', 'Foto kegiatan wajib diisi.');
                return false;
            } else {
                const ext = gambarInput.files[0].name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(ext)) {
                    showPhotoFieldError('tPhotoPreviewContainer', 'tPhotoError', 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.');
                    return false;
                }
                clearPhotoFieldError('tPhotoPreviewContainer', 'tPhotoError');
            }
        } else {
            const removeGambar = document.getElementById('editRemoveGambar')?.value === '1';
            const hasNewPhoto = gambarInput && gambarInput.files && gambarInput.files.length > 0;
            const hasPreviewImg = document.querySelector('#editPhotoPreviewContainer img') !== null;
            if ((removeGambar || !hasPreviewImg) && !hasNewPhoto) {
                showPhotoFieldError('editPhotoPreviewContainer', 'editPhotoError', 'Foto kegiatan wajib diisi.');
                return false;
            } else if (hasNewPhoto) {
                const ext = gambarInput.files[0].name.split('.').pop().toLowerCase();
                if (!allowedExtensions.includes(ext)) {
                    showPhotoFieldError('editPhotoPreviewContainer', 'editPhotoError', 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.');
                    return false;
                }
                clearPhotoFieldError('editPhotoPreviewContainer', 'editPhotoError');
            } else {
                clearPhotoFieldError('editPhotoPreviewContainer', 'editPhotoError');
            }
        }

        // 2. Validate Judul, Kategori, Periode (Tanggal)
        const judulInput = form.querySelector('input[name="judul"]');
        if (!judulInput || !judulInput.value.trim()) {
            if (judulInput) judulInput.reportValidity();
            return false;
        }

        const kategoriSelect = form.querySelector('select[name="kategori"]');
        if (!kategoriSelect || !kategoriSelect.value.trim()) {
            if (kategoriSelect) kategoriSelect.reportValidity();
            return false;
        }

        const periodeInput = form.querySelector('input[name="periode"]');
        if (!periodeInput || !periodeInput.value.trim()) {
            if (periodeInput) periodeInput.reportValidity();
            return false;
        }

        // 3. Validasi bidang Materi jika kotak centang dicentang
        const hasMateriChk = form.querySelector('input[name="has_materi"]');
        if (hasMateriChk && hasMateriChk.checked) {
            const materiInput = form.querySelector('input[name="materi_file"]');
            const displayInput = isEdit ? document.getElementById('editMateriFileNameDisplay') : document.getElementById('tMateriFileNameDisplay');
            const removeMateri = isEdit ? document.getElementById('editRemoveMateri')?.value === '1' : false;

            const hasFileSelected = materiInput && materiInput.files && materiInput.files.length > 0;
            const hasExistingFile = isEdit && displayInput && displayInput.value.trim() !== '' && !removeMateri;

            if (!hasFileSelected && !hasExistingFile) {
                if (displayInput) displayInput.reportValidity();
                return false;
            }

            const ketInput = form.querySelector('input[name="keterangan"]');
            if (!ketInput || !ketInput.value.trim()) {
                if (ketInput) ketInput.reportValidity();
                return false;
            }
        }

        const materiInput = form.querySelector('input[name="materi_file"]');
        if (materiInput && materiInput.files && materiInput.files[0]) {
            const file = materiInput.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            const allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpeg', 'png', 'jpg', 'gif', 'svg', 'webp'];
            const errEl = document.getElementById(isEdit ? 'editMateriError' : 'tMateriError');

            if (!allowed.includes(ext)) {
                if (errEl) {
                    errEl.textContent = 'File materi harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, SVG, atau WEBP.';
                    errEl.style.display = 'block';
                }
                return false;
            }

            if (file.size > 10 * 1024 * 1024) {
                if (errEl) {
                    errEl.textContent = 'Ukuran file materi terlalu besar! Maksimal 10 MB.';
                    errEl.style.display = 'block';
                }
                return false;
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
            } else if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran foto terlalu besar! Maksimal 5 MB.'); return false;
            }
        }
        return true;
    }

    /* ===== KONTROL MODAL ===== */
    function openKategoriModal()  { document.getElementById('kategoriModal').classList.add('active'); }
    function closeKategoriModal() { document.getElementById('kategoriModal').classList.remove('active'); }

    function openTambahModal() {
        clearPhotoFieldError('tPhotoPreviewContainer', 'tPhotoError');
        const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        const now = new Date();
        const pInput = document.getElementById('tPeriode');
        if (pInput) pInput.value = `${months[now.getMonth()]} ${now.getFullYear()}`;
        
        // Setel ulang pratinjau foto ke gambar pengganti
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
            const ext = file.name.split('.').pop().toLowerCase();
            const allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
            if (!allowed.includes(ext)) {
                showPhotoFieldError('tPhotoPreviewContainer', 'tPhotoError', 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.');
                clearTambahPhotoPreview();
                return;
            }
            clearPhotoFieldError('tPhotoPreviewContainer', 'tPhotoError');
            const reader = new FileReader();
            reader.onload = e => renderTambahPhotoPreview(e.target.result);
            reader.readAsDataURL(file);
        }
    }

    function handleTambahMateriSelect(input) {
        const errorEl = document.getElementById('tMateriError');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const ext = file.name.split('.').pop().toLowerCase();
            const allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpeg', 'png', 'jpg', 'gif', 'svg', 'webp'];

            if (!allowed.includes(ext)) {
                if (errorEl) {
                    errorEl.textContent = 'File materi harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, SVG, atau WEBP.';
                    errorEl.style.display = 'block';
                }
                input.value = '';
                const display = document.getElementById('tMateriFileNameDisplay');
                if (display) display.value = '';
                return;
            } else {
                if (errorEl) {
                    errorEl.textContent = '';
                    errorEl.style.display = 'none';
                }
            }
            const display = document.getElementById('tMateriFileNameDisplay');
            if (display) display.value = file.name;
        }
    }

    function toggleTambahMateri(show) {
        const box = document.getElementById('tMateriBox');
        if (box) box.style.display = show ? 'flex' : 'none';
        const errEl = document.getElementById('tMateriError');
        if (errEl) { errEl.textContent = ''; errEl.style.display = 'none'; }
        if (!show) {
            document.getElementById('tMateriFileInput').value = '';
            const display = document.getElementById('tMateriFileNameDisplay');
            if (display) display.value = '';
        }
    }

    function openEditModal(item) {
        clearPhotoFieldError('editPhotoPreviewContainer', 'editPhotoError');
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
        if (item.gambar) {
            const assetUrl = item.gambar.startsWith('http') ? item.gambar : `{{ asset('') }}${item.gambar}`;;
            renderPhotoPreview(assetUrl);
        } else {
            renderPhotoPreview(null);
        }

        const hasMateri = !!item.materi;
        document.getElementById('editHasMateri').checked = hasMateri;
        toggleEditMateri(hasMateri);
        document.getElementById('editMateriFileNameDisplay').value = hasMateri ? item.materi : '';

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

        @if ($errors->any())
            openTambahModal();
        @endif
    });
</script>
@endsection
