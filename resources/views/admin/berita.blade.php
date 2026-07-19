@extends('layouts.admin')

@section('title', 'Berita & Artikel - Panel Admin')

@section('breadcrumb', 'Berita & Artikel')

@section('styles')
    <style>
        /* 1. Global Font Overrides */
        input, select, textarea, button {
            font-family: inherit;
        }
        .filter-controls-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
        }
        .left-filters {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .search-input-wrapper {
            position: relative;
            min-width: 240px;
        }
        .search-input-wrapper input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            border: 1.5px solid #E2E8F0;
            border-radius: 8px;
            font-size: 0.88rem;
            outline: none;
            color: #1E293B;
            transition: border-color 0.2s;
        }
        .search-input-wrapper input:focus {
            border-color: #CBD5E1;
        }
        .search-icon-svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            stroke: #94a3b8;
            stroke-width: 2.2;
            fill: none;
        }
        .status-tabs-pill {
            display: flex;
            background: transparent;
            gap: 8px;
        }
        .status-pill-link {
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 550;
            color: #64748B;
            text-decoration: none;
            transition: all 0.2s;
            background: #FFFFFF;
            border: 1.5px solid #F1F5F9;
        }
        .status-pill-link:hover {
            background: #F8FAFC;
            color: #1E293B;
        }
        .status-pill-link.active {
            background: #DC2626 !important;
            color: #ffffff !important;
            border-color: #DC2626 !important;
        }
        .berita-table-wrapper {
            border-radius: 12px;
            border: 1.5px solid #F1F5F9;
            margin-top: 10px;
            background: #FFFFFF;
        }
        .berita-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }
        .berita-table th {
            background: #FFFFFF;
            padding: 11px 14px;
            font-weight: 600;
            color: #64748B;
            border-bottom: 1.5px solid #F1F5F9;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-align: left;
        }
        .berita-table td {
            padding: 11px 14px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: #475569;
        }
        .berita-table tr:last-child td {
            border-bottom: none;
        }
        .berita-table tr:hover td {
            background: #FAFAFA;
        }
        .article-title-text {
            color: #1a2e24;
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            transition: color 0.15s;
        }
        .featured-star {
            color: #fbbf24;
            font-size: 1rem;
            margin-left: 4px;
        }
        .badge-pill {
            font-size: 0.72rem;
            font-weight: 650;
            padding: 3px 8px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-tayang { background: #FFF1F2; color: #DC2626; }
        .badge-draf { background: #F1F5F9; color: #475569; }
        .action-columns {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .modal-overlay {
            display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.4); z-index: 1000; align-items: center; justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }
        .form-modal {
            background: white; border-radius: 16px; padding: 24px; width: 90%; max-width: 520px; max-height: 90vh; overflow-y: auto;
        }
        .form-field { margin-bottom: 14px; }
        .form-field label { display: block; font-size: 0.85rem; font-weight: 550; color: #475569; margin-bottom: 6px; }
        .form-field input, .form-field select, .form-field textarea {
            width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box; background-color: #FFFFFF;
        }
        .form-field textarea { min-height: 120px; resize: vertical; font-family: inherit; }
        .modal-footer { display: flex; gap: 16px; justify-content: space-between; margin-top: 24px; }
        .btn-cancel { flex: 1; text-align: center; background: #FFFFFF; color: #1E293B; border: 1.5px solid #E2E8F0; padding: 12px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 0.9rem; transition: all 0.2s; }
        .btn-cancel:hover { background: #FAFAFA; border-color: #CBD5E1; }
        .btn-save { flex: 1; text-align: center; background: #B91C1C; color: white; border: none; padding: 12px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; font-size: 0.9rem; transition: background 0.2s; }
        .btn-save:hover { background: #991B1B; }

        /* Custom scrollbar for categories list */
        #categoriesListContainer::-webkit-scrollbar {
            width: 6px;
        }
        #categoriesListContainer::-webkit-scrollbar-track {
            background: #F1F5F9;
            border-radius: 8px;
        }
        #categoriesListContainer::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 8px;
        }
        #categoriesListContainer::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>
@endsection

@section('content')
    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 class="page-title" style="margin:0; font-size:1.75rem; font-weight:700; color:#1E293B;">Berita &amp; Artikel</h1>
            <div style="font-size:0.88rem; color:#64748B; margin-top:4px; font-weight:500;">
                {{ $beritaList->where('status', 'tayang')->count() }} tayang &middot; {{ $beritaList->where('status', 'draft')->count() }} draft
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <button type="button" onclick="openKategoriModal()" style="font-size: 0.88rem; font-weight: 600; padding: 10px 20px; border-radius: 8px; border: 1.5px solid #E2E8F0; background: #FFFFFF; color: #475569; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <span>Kategori</span>
            </button>
            <button onclick="openModal()" style="font-size:0.9rem; padding:10px 20px; border-radius:8px; border:none; background-color:#DC2626; color:#FFFFFF; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; transition:background 0.2s;" onmouseover="this.style.backgroundColor='#B91C1C'" onmouseout="this.style.backgroundColor='#DC2626'">
                + Tambah Artikel
            </button>
        </div>
    </div>



    @if ($errors->any())
    <div style="background:#fee2e2;color:#dc2626;padding:12px 18px;border-radius:10px;margin-bottom:16px;font-weight:600;font-size:0.9rem;">
        @foreach ($errors->all() as $error)
            <div>✗ {{ $error }}</div>
        @endforeach
    </div>
    @endif

    <!-- Search and Filter Controls -->
    <div class="filter-controls-row">
        <form method="GET" action="{{ route('admin.berita') }}" class="left-filters" id="filterForm">
            <!-- Hidden status filter -->
            <input type="hidden" name="status" id="statusFilterVal" value="{{ $statusFilter ?? 'semua' }}">

            <div class="search-input-wrapper">
                <svg class="search-icon-svg" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" name="search" placeholder="Cari artikel..." value="{{ $search }}">
            </div>

            <div style="position: relative; display: inline-block;">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; pointer-events: none;">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <select name="kategori" style="padding: 10px 36px 10px 36px; border: 1.5px solid #E2E8F0; border-radius: 8px; font-size: 0.88rem; font-weight: 550; color: #1E293B; outline: none; cursor: pointer; background: #FFFFFF; font-family: inherit; -webkit-appearance: none; -moz-appearance: none; appearance: none;" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriList as $kat)
                    <option value="{{ $kat }}" {{ $kategoriFilter === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; pointer-events: none;">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </form>

        <div class="status-tabs-pill">
            <a href="#" onclick="setStatusFilter('semua')" class="status-pill-link {{ ($statusFilter ?? 'semua') === 'semua' ? 'active' : '' }}">Semua</a>
            <a href="#" onclick="setStatusFilter('tayang')" class="status-pill-link {{ ($statusFilter ?? '') === 'tayang' ? 'active' : '' }}">Tayang</a>
            <a href="#" onclick="setStatusFilter('draf')" class="status-pill-link {{ ($statusFilter ?? '') === 'draf' ? 'active' : '' }}">Draft</a>
        </div>
    </div>

    <!-- Articles Table -->
    <div class="berita-table-wrapper">
        <table class="berita-table">
            <thead>
                <tr>
                    <th style="width: 48%;">Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($beritaList as $b)
                <tr>
                    <td>
                        <div>
                            <span class="article-title-text" style="font-weight: 700; color: #0f172a; font-size: 0.95rem; text-decoration: none;">{{ $b->judul }}</span>
                            @if ($b->is_featured)
                                <span class="featured-star" style="color: #fbbf24; margin-left: 6px;" title="Artikel Pilihan">★</span>
                            @endif
                            <div style="font-size: 0.76rem; color: #94A3B8; margin-top: 4px; font-weight: 400; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ Str::limit(strip_tags($b->isi), 50) }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-pill" style="background: #FFF1F2; color: #DC2626;">
                            {{ $b->kategori }}
                        </span>
                    </td>
                    <td>{{ $b->penulis }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 4px; color: #64748B; font-size: 0.8rem;">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #94A3B8;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <span>{{ \App\Helpers\Helper::formatTanggal($b->created_at) }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge-pill badge-{{ $b->status }}">
                            {{ $b->status === 'tayang' ? 'Tayang' : 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <div class="action-columns">
                            <button type="button" onclick="openDetailModal({{ json_encode($b) }})" class="btn-icon-action" title="Lihat Artikel">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                            <button onclick="openEditModal({{ json_encode($b) }})" class="btn-icon-action btn-icon-edit" title="Edit Artikel">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </button>
                            <button type="button" onclick="openHapusModal({{ $b->id }}, '{{ addslashes($b->judul) }}')" class="btn-icon-action btn-icon-delete" title="Hapus Artikel">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                            <form id="deleteForm-{{ $b->id }}" method="POST" action="{{ route('admin.berita.destroy', $b->id) }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; color:#aaa; padding:40px;">Belum ada artikel.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah/Edit -->
    <div class="modal-overlay" id="beritaModal">
        <div class="form-modal" style="max-width: 650px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 id="modalTitle" style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1E293B; font-family: inherit;">Tambah Artikel Baru</h3>
                <button type="button" onclick="closeModal()" style="background: none; border: none; cursor: pointer; color: #94A3B8; padding: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: color 0.15s;" onmouseover="this.style.color='#1E293B'" onmouseout="this.style.color='#94A3B8'">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.berita.store') }}" id="beritaForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-field">
                    <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Judul Artikel <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="judul" id="fJudul" required placeholder="Judul artikel yang menarik..." style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 10px 14px; font-size: 0.88rem; width: 100%; box-sizing: border-box; outline: none; transition: border-color 0.15s;">
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-field">
                        <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Kategori <span style="color: #EF4444;">*</span></label>
                        <div style="position: relative;">
                            <select name="kategori" id="fKategori" required style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 10px 14px; font-size: 0.88rem; width: 100%; box-sizing: border-box; outline: none; appearance: none; -webkit-appearance: none; padding-right: 36px;">
                                @foreach ($kategoriList as $kat)
                                <option value="{{ $kat }}">{{ $kat }}</option>
                                @endforeach
                            </select>
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #64748B;">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                    <div class="form-field">
                        <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Status</label>
                        <div style="position: relative;">
                            <select name="status" id="fStatus" required style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 10px 14px; font-size: 0.88rem; width: 100%; box-sizing: border-box; outline: none; appearance: none; -webkit-appearance: none; padding-right: 36px;">
                                <option value="draft">Draft</option>
                                <option value="tayang">Tayang</option>
                            </select>
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #64748B;">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-field">
                        <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Penulis <span style="color: #EF4444;">*</span></label>
                        <input type="text" name="penulis" id="fPenulis" required value="Admin" placeholder="Nama penulis" style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 10px 14px; font-size: 0.88rem; width: 100%; box-sizing: border-box; outline: none;">
                    </div>
                    <div class="form-field">
                        <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Tanggal Publikasi</label>
                        <div style="position: relative;">
                            <input type="text" id="fTanggal" readonly value="{{ date('d/m/Y') }}" style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 10px 14px; font-size: 0.88rem; width: 100%; box-sizing: border-box; outline: none; cursor: default; padding-right: 40px;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #64748B; pointer-events: none;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="form-field">
                    <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Foto Artikel (opsional)</label>
                    <div onclick="document.getElementById('fGambarFile').click()" style="border: 2px dashed #CBD5E1; border-radius: 12px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.2s; background: #FAFAFA; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;" onmouseover="this.style.borderColor='#DC2626'; this.style.background='#FFF1F2';" onmouseout="this.style.borderColor='#CBD5E1'; this.style.background='#FAFAFA';">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="pictureOutlineIcon">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span id="uploadInstructionText" style="font-size: 0.88rem; font-weight: 600; color: #475569;">Klik untuk upload foto</span>
                        <span id="uploadSubtextParagraph" style="font-size: 0.76rem; color: #94A3B8;">JPG, PNG (maks. 3 MB)</span>
                    </div>
                    <input type="file" name="gambar_file" id="fGambarFile" accept="image/*" style="display:none;" onchange="handleFileSelect(this)">
                    <input type="hidden" name="gambar_url" id="fGambar" value="">
                </div>

                <div class="form-field">
                    <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Ringkasan / Excerpt</label>
                    <input type="text" name="excerpt_temp" id="fExcerpt" placeholder="Ringkasan singkat (tampil di daftar artikel)..." style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 10px 14px; font-size: 0.88rem; width: 100%; box-sizing: border-box; outline: none;">
                </div>

                <div class="form-field">
                    <label style="font-weight: 600; color: #334155; font-size: 0.85rem; margin-bottom: 6px; display: block;">Isi Artikel</label>
                    <textarea name="isi" id="fIsi" required placeholder="Tulis isi artikel di sini..." style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 12px 14px; font-size: 0.88rem; width: 100%; box-sizing: border-box; outline: none; min-height: 140px; resize: vertical; font-family: inherit;"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn-save" id="btnSubmitArticle">Terbitkan Artikel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Artikel -->
    <div class="modal-overlay" id="detailModal">
        <div class="form-modal" style="width: 90%; max-width: 480px; padding: 28px; display: flex; flex-direction: column; border-radius: 16px;">
            <!-- Badges Row -->
            <div style="display: flex; gap: 8px; align-items: center;">
                <span id="detKategori" style="background: #FFF1F2; color: #DC2626; padding: 4px 12px; border-radius: 9999px; font-weight: 600; font-size: 0.78rem;"></span>
                <span id="detStatus" style="background: #EFF6FF; color: #1D4ED8; padding: 4px 12px; border-radius: 9999px; font-weight: 600; font-size: 0.78rem;"></span>
            </div>
            
            <!-- Title -->
            <h2 id="detJudul" style="margin-top: 16px; margin-bottom: 8px; font-size: 1.25rem; font-weight: 800; color: #0F172A; line-height: 1.4; font-family: inherit;"></h2>
            
            <!-- Metadata -->
            <div id="detMeta" style="font-size: 0.8rem; color: #94A3B8; margin-bottom: 20px; font-family: inherit;"></div>
            
            <!-- Content Body -->
            <div id="detIsi" style="font-size: 0.9rem; color: #475569; line-height: 1.6; font-family: inherit; margin-bottom: 28px; white-space: pre-wrap; word-break: break-word;"></div>
            
            <!-- Bottom Button -->
            <button type="button" onclick="closeDetailModal()" style="width: 100%; text-align: center; background: #FFFFFF; color: #475569; border: 1.5px solid #E2E8F0; padding: 11px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.9rem; transition: all 0.15s; font-family: inherit;" onmouseover="this.style.background='#F8FAFC'; this.style.borderColor='#CBD5E1';" onmouseout="this.style.background='#FFFFFF'; this.style.borderColor='#E2E8F0';">
                Tutup
            </button>
        </div>
    </div>

    <!-- Modal Kategori Artikel -->
    <div class="modal-overlay" id="kategoriModal">
        <div class="form-modal" style="width: 90%; max-width: 440px; padding: 24px; display: flex; flex-direction: column;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 8px; color: #DC2626;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; color: #1E293B;">Kategori Artikel</h3>
                </div>
                <button type="button" onclick="closeKategoriModal()" style="background: none; border: none; cursor: pointer; color: #94A3B8; padding: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: color 0.15s;" onmouseover="this.style.color='#1E293B'" onmouseout="this.style.color='#94A3B8'">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <!-- Input Row -->
            <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="text" id="newCategoryInput" placeholder="Nama kategori baru..." style="flex: 1; padding: 10px 14px; border: 1.5px solid #DC2626; border-radius: 8px; font-size: 0.9rem; outline: none; box-sizing: border-box; background-color: #FFFFFF;">
                <button type="button" onclick="addNewCategory()" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: none; background-color: #FFF1F2; color: #DC2626; font-size: 1.25rem; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='#FFF1F2'">
                    +
                </button>
            </div>
            
            <!-- Categories List Container -->
            <div id="categoriesListContainer" style="max-height: 240px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; padding-right: 4px;">
                <!-- Category items will be rendered here dynamically -->
            </div>
            
            <!-- Bottom Button -->
            <button type="button" onclick="closeKategoriModal()" style="width: 100%; text-align: center; background: #DC2626; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.95rem; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#B91C1C'" onmouseout="this.style.backgroundColor='#DC2626'">
                Selesai
            </button>
        </div>
    </div>

    <!-- MODAL: HAPUS BERITA -->
    <div class="modal-overlay" id="hapusModal">
        <div style="background: white; border-radius: 20px; padding: 28px 28px 24px; width: 90%; max-width: 440px; box-sizing: border-box; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <!-- Icon -->
                <div style="flex-shrink: 0; width: 48px; height: 48px; border-radius: 50%; background: #FEE2E2; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <!-- Text -->
                <div style="flex: 1;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 8px; font-family: inherit;">Hapus Berita?</h3>
                    <p style="font-size: 0.9rem; color: #475569; margin: 0; line-height: 1.6; font-family: inherit;">
                        Anda akan menghapus data anggota <strong id="hapusJudul"></strong>.
                    </p>
                </div>
            </div>
            <!-- Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" onclick="closeHapusModal()" style="border: 1px solid #CBD5E1; background: #FFFFFF; color: #475569; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer; font-family: inherit; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">Batal</button>
                <button type="button" onclick="submitHapus()" style="background: #DC2626; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer; font-family: inherit; transition: background 0.2s;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Ya, Hapus</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function setStatusFilter(status) {
            document.getElementById('statusFilterVal').value = status;
            document.getElementById('filterForm').submit();
        }

        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Artikel Baru';
            const btnSave = document.getElementById('btnSubmitArticle');
            if (btnSave) btnSave.textContent = 'Terbitkan Artikel';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('beritaForm').action = "{{ route('admin.berita.store') }}";
            document.getElementById('beritaForm').reset();
            document.getElementById('fPenulis').value = 'Admin';
            document.getElementById('fKategori').value = categories[0] || '';
            document.getElementById('fExcerpt').value = '';
            document.getElementById('fTanggal').value = "{{ date('d/m/Y') }}";
            resetUploadArea();
            document.getElementById('beritaModal').classList.add('active');
        }

        function openEditModal(data) {
            document.getElementById('modalTitle').textContent = 'Edit Artikel';
            const btnSave = document.getElementById('btnSubmitArticle');
            if (btnSave) btnSave.textContent = 'Simpan Perubahan';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('beritaForm').action = "{{ url('/admin/berita') }}/" + data.id;
            document.getElementById('fJudul').value = data.judul;
            document.getElementById('fKategori').value = data.kategori;
            document.getElementById('fPenulis').value = data.penulis;
            document.getElementById('fIsi').value = data.isi;
            document.getElementById('fGambar').value = data.gambar_url || '';
            document.getElementById('fStatus').value = data.status;
            
            if (data.created_at) {
                const d = new Date(data.created_at);
                if (!isNaN(d.getTime())) {
                    const day = String(d.getDate()).padStart(2, '0');
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const year = d.getFullYear();
                    document.getElementById('fTanggal').value = `${day}/${month}/${year}`;
                } else {
                    const parts = data.created_at.split(' ')[0].split('-');
                    if (parts.length === 3) {
                        document.getElementById('fTanggal').value = `${parts[2]}/${parts[1]}/${parts[0]}`;
                    } else {
                        document.getElementById('fTanggal').value = "{{ date('d/m/Y') }}";
                    }
                }
            } else {
                document.getElementById('fTanggal').value = "{{ date('d/m/Y') }}";
            }
            
            if (data.gambar_url) {
                setUploadAreaFile(data.gambar_url);
            } else {
                resetUploadArea();
            }
            
            document.getElementById('fExcerpt').value = stripHtml(data.isi).substring(0, 70);
            document.getElementById('beritaModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('beritaModal').classList.remove('active');
        }

        document.getElementById('beritaModal').addEventListener('click', e => {
            if (e.target === document.getElementById('beritaModal')) closeModal();
        });

        function openDetailModal(data) {
            document.getElementById('detKategori').textContent = data.kategori || 'Umum';
            
            const statusSpan = document.getElementById('detStatus');
            
            let formattedDate = 'Baru saja';
            if (data.created_at) {
                const d = new Date(data.created_at);
                if (!isNaN(d.getTime())) {
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                    const day = d.getDate();
                    const monthStr = months[d.getMonth()];
                    const year = d.getFullYear();
                    formattedDate = `${day} ${monthStr} ${year}`;
                } else {
                    const parts = data.created_at.split(' ')[0].split('-');
                    if (parts.length === 3) {
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                        const mIdx = parseInt(parts[1], 10) - 1;
                        formattedDate = `${parts[2]} ${months[mIdx]} ${parts[0]}`;
                    }
                }
            }
            document.getElementById('detMeta').textContent = `${data.penulis || 'Admin'} - ${formattedDate}`;
            document.getElementById('detIsi').textContent = stripHtml(data.isi);
            
            document.getElementById('detailModal').classList.add('active');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
        }

        document.getElementById('detailModal').addEventListener('click', e => {
            if (e.target === document.getElementById('detailModal')) closeDetailModal();
        });

        function handleFileSelect(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                setUploadAreaFile(file.name);
                document.getElementById('fGambar').value = '/assets/images/upload/' + file.name;
            }
        }

        function setUploadAreaFile(filename) {
            const insText = document.getElementById('uploadInstructionText');
            const subText = document.getElementById('uploadSubtextParagraph');
            const icon = document.getElementById('pictureOutlineIcon');
            
            if (insText) insText.textContent = filename.split('/').pop();
            if (subText) subText.innerHTML = '<span style="color: #EF4444; font-weight:600; text-decoration: underline;" onclick="resetUploadArea(event)">Hapus file</span>';
            if (icon) {
                icon.innerHTML = `<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>`;
                icon.style.stroke = '#DC2626';
            }
        }

        function resetUploadArea(event) {
            if (event) {
                event.stopPropagation();
            }
            document.getElementById('fGambarFile').value = '';
            document.getElementById('fGambar').value = '';
            
            const insText = document.getElementById('uploadInstructionText');
            const subText = document.getElementById('uploadSubtextParagraph');
            const icon = document.getElementById('pictureOutlineIcon');
            
            if (insText) insText.textContent = 'Klik untuk upload foto';
            if (subText) subText.textContent = 'JPG, PNG (maks. 3 MB)';
            if (icon) {
                icon.innerHTML = `<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline>`;
                icon.style.stroke = '#64748B';
            }
        }

        function stripHtml(html) {
            let doc = new DOMParser().parseFromString(html, 'text/html');
            return doc.body.textContent || "";
        }

        // Category Management
        let categories = @json($kategoriList);
        
        // Initializer
        function initCategories() {
            renderCategories();
            populateDropdowns();
        }
        
        function renderCategories() {
            const container = document.getElementById('categoriesListContainer');
            container.innerHTML = '';
            
            categories.forEach(cat => {
                const item = document.createElement('div');
                item.style.display = 'flex';
                item.style.justifyContent = 'space-between';
                item.style.alignItems = 'center';
                item.style.padding = '12px 14px';
                item.style.background = '#FFFFFF';
                item.style.border = '1.5px solid #F1F5F9';
                item.style.borderRadius = '8px';
                
                item.innerHTML = `
                    <span style="font-size: 0.88rem; font-weight: 550; color: #1E293B;">${cat}</span>
                    <button type="button" onclick="deleteCategory('${cat}')" style="background: none; border: none; cursor: pointer; color: #94A3B8; display: inline-flex; align-items: center; justify-content: center; transition: color 0.15s;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#94A3B8'">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(item);
            });
        }
        
        function populateDropdowns() {
            // 1. Selector Kategori Filter (header)
            const filterSelect = document.querySelector('select[name="kategori"]');
            const currentFilterVal = filterSelect.value;
            filterSelect.innerHTML = '<option value="">Semua Kategori</option>';
            categories.forEach(cat => {
                const opt = document.createElement('option');
                opt.value = cat;
                opt.textContent = cat;
                if (cat === currentFilterVal) {
                    opt.selected = true;
                }
                filterSelect.appendChild(opt);
            });
            
            // 2. Selector Kategori Form Tambah/Edit
            const formSelect = document.getElementById('fKategori');
            if (formSelect) {
                const currentFormVal = formSelect.value;
                formSelect.innerHTML = '';
                categories.forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value = cat;
                    opt.textContent = cat;
                    if (cat === currentFormVal) {
                        opt.selected = true;
                    }
                    formSelect.appendChild(opt);
                });
            }
        }
        
        function addNewCategory() {
            const input = document.getElementById('newCategoryInput');
            const val = input.value.trim();
            if (!val) return;
            if (categories.includes(val)) {
                alert('Kategori tersebut sudah terdaftar.');
                return;
            }
            
            fetch("{{ route('admin.berita.kategori.store') }}", {
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
                    populateDropdowns();
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
                fetch("{{ url('/admin/berita/kategori') }}/" + encodeURIComponent(catName), {
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
                        populateDropdowns();
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
        
        function openKategoriModal() {
            document.getElementById('kategoriModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('beritaModal').classList.remove('active');
        }

        function closeKategoriModal() {
            document.getElementById('kategoriModal').classList.remove('active');
        }

        document.getElementById('kategoriModal').addEventListener('click', e => {
            if (e.target === document.getElementById('kategoriModal')) closeKategoriModal();
        });

        // Hapus Modal
        let _hapusTargetId = null;
        function openHapusModal(id, judul) {
            _hapusTargetId = id;
            document.getElementById('hapusJudul').innerText = judul;
            document.getElementById('hapusModal').classList.add('active');
        }
        function closeHapusModal() {
            document.getElementById('hapusModal').classList.remove('active');
        }
        function submitHapus() {
            if (_hapusTargetId !== null) {
                document.getElementById('deleteForm-' + _hapusTargetId).submit();
            }
        }

        document.getElementById('hapusModal').addEventListener('click', e => {
            if (e.target === document.getElementById('hapusModal')) closeHapusModal();
        });

        // Initialize categories on load
        window.addEventListener('DOMContentLoaded', initCategories);
    </script>
@endsection
