@extends('layouts.admin')

@section('title', 'Layanan & Produk - Panel Admin')

@section('breadcrumb', 'Layanan & Produk')

@section('styles')
    <style>
        /* Typography Inheritance */
        input, select, textarea, button {
            font-family: inherit;
        }

        /* 1. Layout Utama & Tabs */
        .wrapper-tabs {
            background: #F1F5F9;
            padding: 4px;
            border-radius: 0.75rem; /* rounded-xl */
            display: inline-flex;
            gap: 4px;
            margin-bottom: 24px;
        }

        .tab-button {
            padding: 8px 16px;
            font-size: 0.88rem;
            font-weight: 600;
            color: #64748B;
            border: none;
            cursor: pointer;
            background: transparent;
            border-radius: 0.5rem; /* rounded-lg */
            transition: all 0.2s ease;
        }

        .tab-button.active {
            background: #FFFFFF;
            color: #DC2626;
            font-weight: 650;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .tab-block {
            display: none;
        }

        .tab-block.active {
            display: block;
        }

        /* 2. Baris Kontrol */
        .category-pill {
            background: transparent;
            color: #64748B;
            border: none;
            padding: 6px 16px;
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 9999px;
            transition: all 0.2s ease;
        }

        .category-pill.active {
            background-color: #7F1D1D;
            color: #FFFFFF;
            font-weight: 700;
        }

        .search-input {
            border: 1px solid #E2E8F0;
            border-radius: 0.5rem; /* rounded-lg */
            padding: 8px 12px;
            font-size: 0.88rem;
            outline: none;
            width: 220px;
            box-sizing: border-box;
            background: #FFFFFF;
        }

        .search-input:focus {
            border-color: #7F1D1D;
        }

        .btn-tambah {
            background-color: #DC2626;
            color: #FFFFFF;
            border: none;
            font-size: 0.88rem;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 8.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background 0.2s;
        }

        .btn-tambah:hover {
            background-color: #B91C1C;
        }

        /* 3. Horizontal Scroll Table Container */
        .table-card-wrapper {
            background-color: #FFFFFF;
            border-radius: 1rem; /* rounded-2xl */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03); /* shadow-sm */
            border: 1px solid #E2E8F0;
            overflow-x: auto;
            width: 100%;
            margin-top: 4px;
        }

        .clean-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px; /* WAJIB property for horizontal screen side scroll */
        }

        .clean-table th {
            padding: 12px 18px;
            text-align: left;
            font-weight: 500; /* font-medium */
            color: #64748B; /* Medium gray */
            font-size: 0.88rem; /* text-sm */
            border-bottom: 2px solid #F1F5F9;
        }

        .clean-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
        }

        .clean-table tr:last-child td {
            border-bottom: none;
        }

        .clean-table tr:hover td {
            background-color: #FAFAFA;
        }

        /* Badge and checkboxes */
        .kategori-badge {
            background-color: #FFF1F2; /* light red transparan */
            color: #BE123C;
            font-size: 0.75rem; /* text-xs */
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px; /* rounded-full */
            display: inline-block;
        }

        .status-badge {
            background-color: #FFF1F2; /* light red transparan */
            color: #BE123C;
            font-size: 0.75rem; /* text-xs */
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 9999px; /* rounded-full */
            display: inline-block;
        }

        .status-badge.nonaktif {
            background-color: #FEE2E2;
            color: #B91C1C;
        }

        .custom-check {
            accent-color: #DC2626; /* red check when selected */
            width: 16px;
            height: 16px;
            cursor: pointer;
        }



        /* Modal Structure */
        .custom-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .custom-overlay.active {
            display: flex;
        }

        .modal-body {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            width: 90%;
            max-width: 480px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            max-height: 90vh;
            overflow-y: auto;
        }

        .form-row {
            margin-bottom: 16px;
        }

        .form-row label {
            display: block;
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-row input, .form-row select, .form-row textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            box-sizing: border-box;
            background-color: #FFFFFF;
        }

        .form-row input:focus, .form-row select:focus, .form-row textarea:focus {
            border-color: #7F1D1D;
            background-color: #FFFFFF;
        }

        .modal-buttons {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 24px;
            border-top: 1px solid #F1F5F9;
            padding-top: 16px;
        }

        .btn-cancel-custom {
            flex: 1;
            background: #FFFFFF;
            color: #475569;
            border: 1.5px solid #E2E8F0;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.88rem;
            transition: all 0.2s;
            text-align: center;
        }

        .btn-cancel-custom:hover {
            background: #FAFAFA;
            border-color: #CBD5E1;
        }

        .btn-submit-custom {
            flex: 1;
            background: #DC2626;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.88rem;
            transition: background 0.2s;
            text-align: center;
        }

        .btn-submit-custom:hover {
            background: #B91C1C;
        }

        /* Layout Grid Cards for Tab 3 & Tab 2 */
        .category-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #E2E8F0;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .featured-item-card {
            background: #FFFFFF;
            border-radius: 12px;
            border: 1.5px solid #F1F5F9;
            padding: 20px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            box-shadow: none;
            opacity: 0.7;
        }

        .featured-item-card:hover {
            opacity: 0.9;
            border-color: #CBD5E1;
        }

        .featured-item-card.active {
            border: 1.5px solid #DC2626 !important;
            background: #FFF1F2 !important;
            opacity: 1 !important;
        }

        .featured-item-card .item-icon {
            color: #CBD5E1;
            transition: color 0.2s;
        }

        .featured-item-card.active .item-icon {
            color: #DC2626;
        }

        .featured-item-card .card-title-text {
            color: #94A3B8;
            font-weight: 700;
            font-size: 0.92rem;
            margin: 12px 0 3px 0;
            transition: color 0.2s;
        }

        .featured-item-card.active .card-title-text {
            color: #1E293B;
        }

        .featured-item-card .card-category-text {
            font-size: 0.8rem;
            color: #CBD5E1;
            margin-bottom: 8px;
            transition: color 0.2s;
        }

        .featured-item-card.active .card-category-text {
            color: #64748B;
        }

        .featured-item-card .card-price-text {
            font-size: 0.88rem;
            font-weight: 700;
            color: #94A3B8;
            transition: color 0.2s;
        }

        .featured-item-card.active .card-price-text {
            color: #DC2626;
        }

        .featured-item-card .chk-bubble {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 1.5px solid #E2E8F0;
            color: transparent;
            transition: all 0.2s;
            font-size: 0.72rem;
            font-weight: 900;
        }

        .featured-item-card.active .chk-bubble {
            border-color: #DC2626;
            color: #DC2626;
        }
    </style>
@endsection

@section('content')
    <!-- Header: Judul (Bold, Gelap) and sub-judul -->
    <div style="margin-bottom: 24px;">
        <h1 class="page-title" style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 800; color: #0F172A;">Layanan &amp; Produk</h1>
        <p style="margin: 0; color: #64748B; font-size: 0.88rem;">Kelola produk, kategori, dan tampilan halaman layanan</p>
    </div>



    <!-- Wrapper Tabs (Background abu-abu tipis memanjang, rounded-xl, padding kecil) -->
    <div class="wrapper-tabs">
        <button class="tab-button active" id="btn-daftar-produk" onclick="switchBlock('daftar-produk')">Daftar Produk</button>
        <button class="tab-button" id="btn-kategori" onclick="switchBlock('kategori')">Kategori</button>
        <button class="tab-button" id="btn-produk-unggulan" onclick="switchBlock('produk-unggulan')">Produk Unggulan Beranda</button>
        <button class="tab-button" id="btn-teks-halaman" onclick="switchBlock('teks-halaman')">Teks Halaman</button>
    </div>

    <!-- TAB 1: DAFTAR PRODUK -->
    <div id="block-daftar-produk" class="tab-block active">
        <!-- Baris Kontrol -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
            <!-- Sebelah Kiri: Pill buttons filter kategori -->
            <div style="display: flex; gap: 8px; align-items: center;">
                <button class="category-pill active" id="pill-Semua" onclick="clickFilterPill('Semua')">Semua</button>
                <button class="category-pill" id="pill-Pertanian" onclick="clickFilterPill('Pertanian')">Pertanian</button>
                <button class="category-pill" id="pill-Peternakan" onclick="clickFilterPill('Peternakan')">Peternakan</button>
            </div>
            <!-- Sebelah Kanan: Flex gap-3 for search input and Add button -->
            <div style="display: flex; gap: 12px; align-items: center;">
                <input type="text" id="filterSearch" class="search-input" placeholder="Cari produk..." onkeyup="performSearch()">
                <button onclick="openAddModal()" class="btn-tambah">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Tambah
                </button>
            </div>
        </div>

        <!-- Horizontal Scroll Table wrapper card -->
        <div class="table-card-wrapper">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th style="width: 45%;">Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Satuan</th>
                        <th>Status</th>
                        <th style="text-align: center;">Unggulan</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTbody">
                    @forelse ($layananList as $l)
                        <tr class="product-row" data-kategori="{{ $l->kategori }}" data-nama="{{ strtolower($l->nama) }}">
                            <!-- Kolom PRODUK: NO image, langsung teks nama dan deskripsi -->
                            <td>
                                <div style="font-weight: 700; color: #1E293B; font-size: 1rem;">{{ $l->nama }}</div>
                                <div style="font-size: 0.8rem; color: #64748B; margin-top: 3px; line-height: 1.35;">{{ $l->deskripsi }}</div>
                            </td>
                            <!-- Kolom KATEGORI: badge hijau muda transparan text-xs rounded-full -->
                            <td>
                                <span class="kategori-badge">{{ $l->kategori }}</span>
                            </td>
                            <!-- Kolom HARGA: teks biasa gelap align-left -->
                            <td style="color: #1E293B; font-weight: 500;">
                                Rp {{ number_format($l->harga, 0, ',', '.') }}
                            </td>
                            <!-- Kolom SATUAN: teks biasa gelap align-left -->
                            <td style="color: #1E293B; font-weight: 500;">
                                {{ $l->satuan }}
                            </td>
                            <!-- Kolom STATUS: Badge hijau muda bertuliskan "Aktif" -->
                            <td>
                                <span class="status-badge {{ $l->status === 'aktif' ? '' : 'nonaktif' }}">
                                    {{ $l->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <!-- Kolom UNGGULAN: Custom checkbox warna hijau jika dicentang -->
                            <td style="text-align: center;">
                                @php
                                    $isFeaturedSim = in_array($l->id, [1, 2, 3]);
                                @endphp
                                <input type="checkbox" class="custom-check" {{ $isFeaturedSim ? 'checked' : '' }} disabled>
                            </td>
                            <!-- Kolom AKSI: Pencil and trash slate icons, minimal, no colorful backgrounds -->
                            <td style="text-align: center; white-space: nowrap;">
                                <div style="display: flex; gap: 12px; justify-content: center; align-items: center;">
                                    <button class="btn-icon-action btn-icon-edit" title="Edit" onclick="openEditModal({{ json_encode($l) }})">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.layanan.destroy', $l->id) }}" onsubmit="return confirm('Hapus produk ini?')" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon-action btn-icon-delete" title="Hapus">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94A3B8; font-weight: 500;">
                                Belum ada produk.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: KATEGORI -->
    <div id="block-kategori" class="tab-block">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <span style="font-size: 0.88rem; color: #64748B; font-weight: 500;">2 kategori terdaftar</span>
            <button onclick="openCategoryModal()" class="btn-tambah">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Kategori
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Category 1 -->
            <div class="category-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <h4 style="font-weight: 700; color: #1E293B; font-size: 1.05rem; margin: 0;">Pertanian</h4>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 0.82rem; color: #94A3B8; font-weight: 500;">4/4 aktif</span>
                        <button onclick="alert('Tidak dapat dihapus karena kategori ini masih digunakan oleh produk.')" class="icon-action-btn" style="color: #CBD5E1; padding: 2px;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#CBD5E1'">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div style="height: 5px; background: #DC2626; border-radius: 99px; margin-bottom: 14px;"></div>
                <div style="color: #94A3B8; font-size: 0.8rem; font-weight: 500; display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #94A3B8; flex-shrink: 0;">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>Tidak dapat dihapus karena masih memiliki produk</span>
                </div>
            </div>

            <!-- Category 2 -->
            <div class="category-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                        <h4 style="font-weight: 700; color: #1E293B; font-size: 1.05rem; margin: 0;">Peternakan</h4>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 0.82rem; color: #94A3B8; font-weight: 500;">4/4 aktif</span>
                        <button onclick="alert('Tidak dapat dihapus karena kategori ini masih digunakan oleh produk.')" class="icon-action-btn" style="color: #CBD5E1; padding: 2px;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#CBD5E1'">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div style="height: 5px; background: #DC2626; border-radius: 99px; margin-bottom: 14px;"></div>
                <div style="color: #94A3B8; font-size: 0.8rem; font-weight: 500; display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                    <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #94A3B8; flex-shrink: 0;">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>Tidak dapat dihapus karena masih memiliki produk</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: PRODUK UNGGULAN -->
    <div id="block-produk-unggulan" class="tab-block">
        <div style="background-color: #FFFBEB; border: 1.5px solid #FDE68A; color: #D97706; padding: 12px 18px; border-radius: 8.5px; margin-bottom: 24px; font-size: 0.88rem; font-weight: 550; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: #D97706;">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Pilih maksimal 3 produk untuk ditampilkan di halaman beranda.</span>
            </div>
            <span id="activeFeaturedLabel" style="color: #D97706; font-size: 0.88rem; font-weight: 800;">3/3</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
            @php
                $featuredSimArray = [1, 2, 3];
            @endphp
            @foreach($layananList as $l)
                @php
                    $isFeaturedNode = in_array($l->id, $featuredSimArray);
                @endphp
                <div class="featured-item-card {{ $isFeaturedNode ? 'active' : '' }}" data-id="{{ $l->id }}" onclick="clickFeaturedCard(this)">
                    <!-- Icon checklist pojok kanan atas -->
                    <div class="chk-bubble">✓</div>
                    
                    <!-- SVG Shopping Bag Icon top-left -->
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="item-icon">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>

                    <h4 class="card-title-text">{{ $l->nama }}</h4>
                    <div class="card-category-text">{{ $l->kategori }}</div>
                    <div class="card-price-text">Rp {{ number_format($l->harga, 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB 4: TEKS HALAMAN -->
    <div id="block-teks-halaman" class="tab-block">
        <div style="background: white; border-radius: 1rem; border: 1px solid #E2E8F0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 32px;">
            <h3 style="font-weight: 700; font-size: 1.25rem; color: #1E293B; margin: 0 0 24px 0; font-family: inherit;">Teks Halaman Layanan</h3>
            <form action="{{ route('admin.pengaturan.save') }}" method="POST">
                @csrf
                <div class="form-row">
                    <label>Judul Halaman</label>
                    <input type="text" name="judul_halaman" value="Produk & Layanan Koperasi">
                </div>
                <div class="form-row">
                    <label>Deskripsi Halaman</label>
                    <textarea name="deskripsi_halaman" style="min-height: 100px;">Temukan berbagai produk unggulan dari anggota koperasi kami.</textarea>
                </div>
                <div class="form-row">
                    <label>Judul Seksi Beranda</label>
                    <input type="text" name="judul_seksi_beranda" value="Produk Unggulan">
                </div>
                <div class="form-row" style="margin-bottom: 24px;">
                    <label>Deskripsi Seksi Beranda</label>
                    <textarea name="deskripsi_seksi_beranda" style="min-height: 100px;">Pilihan terbaik dari hasil produksi anggota koperasi.</textarea>
                </div>
                <button type="submit" style="background-color: #DC2626; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.88rem; font-weight: 650; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#B91C1C'" onmouseout="this.style.backgroundColor='#DC2626'">Simpan</button>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH & EDIT PRODUCT -->
    <div class="custom-overlay" id="productModal">
        <div class="modal-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 id="modalTitleLabel" style="font-weight: 700; font-size: 1.15rem; color: #1E293B; margin: 0; font-family: inherit;">Tambah Produk</h3>
                <button type="button" onclick="closeProductModal()" style="background: none; border: none; cursor: pointer; color: #94A3B8; padding: 4px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: color 0.15s;" onmouseover="this.style.color='#1E293B'" onmouseout="this.style.color='#94A3B8'">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.layanan.store') }}" id="productForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="modalMethod" value="POST">
                <input type="hidden" name="gambar_url" id="formGambar">

                <div class="form-row">
                    <label>Nama Produk</label>
                    <input type="text" name="nama" id="formNama" required placeholder="Nama produk...">
                </div>

                <div class="form-row">
                    <label style="font-weight: 600; color: #475569; font-size: 0.85rem; margin-bottom: 6px; display: block;">Foto Produk (opsional)</label>
                    <div onclick="document.getElementById('formGambarFile').click()" style="border: 2px dashed #CBD5E1; border-radius: 12px; padding: 20px; text-align: center; cursor: pointer; transition: all 0.2s; background: #FAFAFA; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;" onmouseover="this.style.borderColor='#DC2626'; this.style.background='#FFF1F2';" onmouseout="this.style.borderColor='#CBD5E1'; this.style.background='#FAFAFA';">
                        <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="pictureOutlineIcon">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span id="uploadInstructionText" style="font-size: 0.88rem; font-weight: 600; color: #475569;">Klik untuk upload foto</span>
                        <span id="uploadSubtextParagraph" style="font-size: 0.76rem; color: #94A3B8;">JPG, PNG (maks. 3 MB)</span>
                    </div>
                    <input type="file" name="gambar_file" id="formGambarFile" accept="image/*" style="display:none;" onchange="handleFileSelect(this)">
                </div>

                <div class="form-row">
                    <label>Kategori</label>
                    <select name="kategori" id="formKategori" required>
                        @foreach (['Pertanian', 'Peternakan', 'Perikanan', 'Kerajinan', 'Keuangan', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <label>Status</label>
                    <select name="status" id="formStatus">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <!-- Form vertikal 2 kolom untuk Harga dan Satuan -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px;">
                    <div class="form-row" style="margin-bottom: 0;">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" id="formHarga" min="0" required value="0">
                    </div>
                    <div class="form-row" style="margin-bottom: 0;">
                        <label>Satuan</label>
                        <input type="text" name="satuan" id="formSatuan" required value="kg" placeholder="e.g. kg, ekor">
                    </div>
                </div>

                <div class="form-row">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="formDesc" required placeholder="Tulis deskripsi produk..." style="min-height: 80px;"></textarea>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel-custom" onclick="closeProductModal()">Batal</button>
                    <button type="submit" id="btnSubmitLabel" class="btn-submit-custom">Tambah Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL TAMBAH KATEGORI -->
    <div class="custom-overlay" id="categoryAddModal">
        <div class="modal-body" style="max-width: 440px;">
            <h3 style="font-weight: 800; font-size: 1.2rem; color: #1E293B; margin: 0 0 16px 0;">Tambah Kategori</h3>
            <form onsubmit="event.preventDefault(); alert('Kategori berhasil dibuat (simulasi).'); closeCategoryModal();">
                <div class="form-row" style="margin-bottom: 24px;">
                    <label>Nama Kategori *</label>
                    <input type="text" required placeholder="Hortikultura">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel-custom" onclick="closeCategoryModal()">Batal</button>
                    <button type="submit" class="btn-submit-custom">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Swapping Active block
        function switchBlock(tab) {
            document.querySelectorAll('.tab-block').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));

            document.getElementById('block-' + tab).classList.add('active');
            document.getElementById('btn-' + tab).classList.add('active');
        }

        // Pill categories selection
        function clickFilterPill(cat) {
            document.querySelectorAll('.category-pill').forEach(btn => btn.classList.remove('active'));
            document.getElementById('pill-' + cat).classList.add('active');

            performSearch();
        }

        // Combining search + pill category filter logic
        function performSearch() {
            const query = document.getElementById('filterSearch').value.toLowerCase();
            const activePill = document.querySelector('.category-pill.active').id.replace('pill-', '');
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const rowNama = row.getAttribute('data-nama');
                const rowKategori = row.getAttribute('data-kategori');

                const searchMatch = rowNama.includes(query);
                const categoryMatch = (activePill === 'Semua' || rowKategori === activePill);

                if (searchMatch && categoryMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function handleFileSelect(input) {
            const file = input.files[0];
            if (file) {
                setUploadAreaFile(file.name);
                document.getElementById('formGambar').value = 'uploads/layanan/' + file.name;
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
            document.getElementById('formGambarFile').value = '';
            document.getElementById('formGambar').value = '';
            
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

        // Modals control
        function openAddModal() {
            document.getElementById('modalTitleLabel').textContent = 'Tambah Produk';
            document.getElementById('modalMethod').value = 'POST';
            document.getElementById('productForm').action = "{{ route('admin.layanan.store') }}";
            document.getElementById('productForm').reset();
            resetUploadArea();
            document.getElementById('btnSubmitLabel').textContent = 'Tambah Produk';
            document.getElementById('productModal').classList.add('active');
        }

        function openEditModal(data) {
            document.getElementById('modalTitleLabel').textContent = 'Edit Produk';
            document.getElementById('modalMethod').value = 'PUT';
            document.getElementById('productForm').action = "{{ url('/admin/layanan') }}/" + data.id;

            document.getElementById('formNama').value = data.nama;
            document.getElementById('formKategori').value = data.kategori;
            document.getElementById('formDesc').value = data.deskripsi || '';
            document.getElementById('formHarga').value = Math.round(data.harga) || 0;
            document.getElementById('formSatuan').value = data.satuan || 'kg';
            document.getElementById('formGambar').value = data.gambar_url || '';
            document.getElementById('formStatus').value = data.status;

            if (data.gambar_url) {
                setUploadAreaFile(data.gambar_url);
            } else {
                resetUploadArea();
            }

            document.getElementById('btnSubmitLabel').textContent = 'Simpan Perubahan';
            document.getElementById('productModal').classList.add('active');
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.remove('active');
        }

        function openCategoryModal() {
            document.getElementById('categoryAddModal').classList.add('active');
        }

        function closeCategoryModal() {
            document.getElementById('categoryAddModal').classList.remove('active');
        }

        // Featured card checklist toggle
        function clickFeaturedCard(card) {
            const isActive = card.classList.contains('active');
            let total = document.querySelectorAll('.featured-item-card.active').length;

            if (!isActive) {
                if (total >= 3) {
                    alert('Maksimal hanya dapat memilih 3 produk unggulan.');
                    return;
                }
                card.classList.add('active');
                total++;
            } else {
                card.classList.remove('active');
                total--;
            }

            document.getElementById('activeFeaturedLabel').textContent = total + '/3';
        }

        // Close when clicking outside modal body
        document.getElementById('productModal').addEventListener('click', e => {
            if (e.target === document.getElementById('productModal')) closeProductModal();
        });
        document.getElementById('categoryAddModal').addEventListener('click', e => {
            if (e.target === document.getElementById('categoryAddModal')) closeCategoryModal();
        });
    </script>
@endsection
