@extends('layouts.admin')

@section('title', 'Layanan & Produk - Panel Admin')

@section('breadcrumb', 'Layanan & Produk')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/layanan.css') }}?v={{ filemtime(public_path('assets/css/admin/layanan.css')) }}">
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
    </div>

    <!-- TAB 1: DAFTAR PRODUK -->
    <div id="block-daftar-produk" class="tab-block active">
        @php
            $uniqueCategories = $categories;
        @endphp
        <!-- Baris Kontrol -->
        <div class="controls-row">
            <!-- Sebelah Kiri: Search input, Category select dynamic dropdown, and Status Pills -->
            <div class="left-filters">
                <div class="search-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="filterSearch" placeholder="Cari produk..." onkeyup="performSearch()">
                </div>
                
                <div class="category-select-wrapper">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <select id="filterCategorySelect" onchange="performSearch()">
                        <option value="Semua">Semua Kategori</option>
                        @foreach($uniqueCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                    <svg class="icon-right" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </div>

                <div class="status-pills">
                    <button class="status-pill active" id="btn-status-Semua" onclick="clickStatusFilter('Semua')">Semua</button>
                    <button class="status-pill" id="btn-status-aktif" onclick="clickStatusFilter('aktif')">Aktif</button>
                    <button class="status-pill" id="btn-status-nonaktif" onclick="clickStatusFilter('nonaktif')">Nonaktif</button>
                </div>
            </div>
            
            <!-- Sebelah Kanan: Add Product Button -->
            <button onclick="openAddModal()" class="btn-tambah-new">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Produk
            </button>
        </div>

        <!-- Horizontal Scroll Table wrapper card -->
        <div class="table-card-wrapper" style="overflow-x: visible;">
            <table class="clean-table" style="background-color: #FFFFFF; width: 100%; table-layout: fixed;">
                <thead>
                    <tr>
                        <th style="width: 40%;">Produk</th>
                        <th style="width: 15%;">Kategori</th>
                        <th style="width: 15%;">Harga</th>
                        <th style="width: 12%;">Satuan</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 8%; text-align: center;">Unggulan</th>
                        <th style="width: 10%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="productTbody">
                    @forelse ($layananList as $l)
                        <tr class="product-row" data-kategori="{{ $l->kategori }}" data-nama="{{ strtolower($l->nama) }}" data-status="{{ strtolower($l->status) }}">
                            <!-- Kolom PRODUK: NO image, langsung teks nama dan deskripsi -->
                            <td style="word-wrap: break-word; white-space: normal;">
                                <div style="font-weight: 700; color: #1E293B; font-size: 0.95rem;">{{ $l->nama }}</div>
                                <div style="font-size: 0.8rem; color: #64748B; margin-top: 3px; line-height: 1.35; white-space: normal; word-break: break-word;">{{ $l->deskripsi }}</div>
                            </td>
                            <!-- Kolom KATEGORI: plain text gray bold-ish to match screenshot -->
                            <td style="color: #475569; font-weight: 500; font-size: 0.88rem;">
                                {{ $l->kategori }}
                            </td>
                            <!-- Kolom HARGA: teks biasa gelap align-left -->
                            <td style="color: #1E293B; font-weight: 500; font-size: 0.88rem;">
                                Rp {{ number_format($l->harga, 0, ',', '.') }}
                            </td>
                            <!-- Kolom SATUAN: teks biasa gelap dengan prefix 'per ' -->
                            <td style="color: #64748B; font-weight: 500; font-size: 0.88rem;">
                                @php
                                    $displaySatuan = strtolower($l->satuan);
                                    if ($displaySatuan === 'kg') {
                                        $displaySatuan = 'kilogram';
                                    }
                                    if (!str_starts_with($displaySatuan, 'per ')) {
                                        $displaySatuan = 'per ' . $displaySatuan;
                                    }
                                @endphp
                                {{ $displaySatuan }}
                            </td>
                            <!-- Kolom STATUS: Badge merah/pink bertuliskan "Aktif" atau abu-abu "Nonaktif" -->
                            <td>
                                <span class="status-badge {{ $l->status === 'aktif' ? '' : 'nonaktif' }}">
                                    {{ $l->status === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <!-- Kolom UNGGULAN: Custom checkbox warna merah jika dicentang -->
                            <td style="text-align: center;">
                                @php
                                    $isFeaturedSim = (bool) $l->is_featured;
                                @endphp
                                <span id="unggulanBox-{{ $l->id }}" class="unggulan-box {{ $isFeaturedSim ? 'checked' : '' }}" style="cursor: pointer;" onclick="toggleUnggulanFromTable({{ $l->id }})"></span>
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
                                    <form id="deleteForm-{{ $l->id }}" method="POST" action="{{ route('admin.layanan.destroy', $l->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="openHapusModal({{ $l->id }}, '{{ addslashes($l->nama) }}')" class="btn-icon-action btn-icon-delete" title="Hapus">
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
            <span style="font-size: 0.88rem; color: #64748B; font-weight: 500;">{{ count($categoriesWithStats) }} kategori terdaftar</span>
            <button onclick="openCategoryModal()" class="btn-tambah">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Tambah Kategori
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach ($categoriesWithStats as $catStats)
                <div class="category-card" data-category="{{ $catStats['nama'] }}">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#DC2626" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                <line x1="7" y1="7" x2="7.01" y2="7"></line>
                            </svg>
                            <h4 style="font-weight: 700; color: #1E293B; font-size: 1.05rem; margin: 0;">{{ $catStats['nama'] }}</h4>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 0.82rem; color: #94A3B8; font-weight: 500;">{{ $catStats['active'] }}/{{ $catStats['total'] }} aktif</span>
                            @if ($catStats['total'] > 0)
                                <button onclick="alert('Tidak dapat dihapus karena kategori ini masih digunakan oleh produk.')" class="icon-action-btn" style="color: #CBD5E1; padding: 2px;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#CBD5E1'">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                            @else
                                <form action="{{ route('admin.kategori.destroy', $catStats['nama']) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hapus kategori {{ $catStats['nama'] }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="icon-action-btn" style="color: #CBD5E1; padding: 2px;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#CBD5E1'">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @php
                        $pct = $catStats['total'] > 0 ? round(($catStats['active'] / $catStats['total']) * 100) : 0;
                        $barColor = $catStats['total'] === 0 ? '#E2E8F0' : ($pct === 100 ? '#DC2626' : ($pct > 0 ? '#EF4444' : '#CBD5E1'));
                    @endphp
                    <div style="height: 5px; background: #F1F5F9; border-radius: 99px; margin-bottom: 14px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $pct }}%; background: {{ $barColor }}; border-radius: 99px; transition: width 0.4s ease;"></div>
                    </div>
                    @if ($catStats['total'] > 0)
                        <div style="color: #94A3B8; font-size: 0.8rem; font-weight: 500; display: flex; align-items: center; gap: 6px; margin-top: 4px;">
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #94A3B8; flex-shrink: 0;">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <span>Tidak dapat dihapus karena masih memiliki produk</span>
                        </div>
                    @endif
                </div>
            @endforeach
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
                <span>Pilih maksimal 3 produk untuk ditampilkan di halaman beranda. <em id="featuredSaveStatus" style="font-style: normal; font-size: 0.82rem;"></em></span>
            </div>
            <span id="activeFeaturedLabel" style="color: #D97706; font-size: 0.88rem; font-weight: 800;">0/3</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
            @php
                $featuredIds = $layananList->where('is_featured', true)->pluck('id')->toArray();
            @endphp
            @foreach($layananList as $l)
                @php
                    $isFeaturedNode = (bool) $l->is_featured;
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
                        @foreach ($categories as $cat)
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
            <form method="POST" action="{{ route('admin.kategori.store') }}">
                @csrf
                <div class="form-row" style="margin-bottom: 24px;">
                    <label>Nama Kategori *</label>
                    <input type="text" name="kategori" required placeholder="Hortikultura">
                </div>
                <div class="modal-buttons">
                    <button type="button" class="btn-cancel-custom" onclick="closeCategoryModal()">Batal</button>
                    <button type="submit" class="btn-submit-custom">Tambah Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: HAPUS PRODUK -->
    <div class="custom-overlay" id="hapusModal">
        <div style="background: white; border-radius: 20px; padding: 28px 28px 24px; width: 90%; max-width: 440px; box-sizing: border-box; display: flex; flex-direction: column;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <div style="flex-shrink: 0; width: 48px; height: 48px; border-radius: 50%; background: #FEE2E2; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0F172A; margin: 0 0 8px; font-family: inherit;">Hapus Produk?</h3>
                    <p style="font-size: 0.9rem; color: #475569; margin: 0; line-height: 1.6; font-family: inherit;">
                        Anda akan menghapus data anggota <strong id="hapusNama"></strong>.
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" onclick="closeHapusModal()" style="border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer; font-family: inherit; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">Batal</button>
                <button type="button" onclick="submitHapus()" style="background: #DC2626; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600; font-size: 0.88rem; cursor: pointer; font-family: inherit; transition: background 0.2s;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Ya, Hapus</button>
            </div>
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

        // Status filter selection
        let activeStatusFilter = 'Semua';

        function clickStatusFilter(status) {
            activeStatusFilter = status;
            document.querySelectorAll('.status-pill').forEach(btn => btn.classList.remove('active'));
            document.getElementById('btn-status-' + status).classList.add('active');
            performSearch();
        }

        // Combining search + category select dropdown + status filter logic
        function performSearch() {
            const query = document.getElementById('filterSearch').value.toLowerCase().trim();
            const selectedCategory = document.getElementById('filterCategorySelect').value;
            const rows = document.querySelectorAll('.product-row');

            rows.forEach(row => {
                const rowNama = row.getAttribute('data-nama') || '';
                const rowKategori = row.getAttribute('data-kategori') || '';
                const rowStatus = row.getAttribute('data-status') || '';

                const searchMatch = rowNama.includes(query);
                const categoryMatch = (selectedCategory === 'Semua' || rowKategori === selectedCategory);
                const statusMatch = (activeStatusFilter === 'Semua' || rowStatus === activeStatusFilter);

                if (searchMatch && categoryMatch && statusMatch) {
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

        // Hapus Modal controller
        let _hapusTargetId = null;
        function openHapusModal(id, nama) {
            _hapusTargetId = id;
            document.getElementById('hapusNama').textContent = nama;
            document.getElementById('hapusModal').classList.add('active');
        }

        function closeHapusModal() {
            document.getElementById('hapusModal').classList.remove('active');
            _hapusTargetId = null;
        }

        function submitHapus() {
            if (_hapusTargetId !== null) {
                document.getElementById('deleteForm-' + _hapusTargetId).submit();
            }
        }

        // Featured card checklist toggle — auto-save via AJAX
        const featuredSaveUrl = "{{ route('admin.layanan.featured') }}";
        const featuredCsrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            || "{{ csrf_token() }}";
        function clickFeaturedCard(card) {
            const isActive = card.classList.contains('active');
            const targetId = card.getAttribute('data-id');
            const box = document.getElementById('unggulanBox-' + targetId);
            let total = document.querySelectorAll('.featured-item-card.active').length;

            if (!isActive) {
                if (total >= 3) {
                    alert('Maksimal hanya dapat memilih 3 produk unggulan.');
                    return;
                }
                card.classList.add('active');
                if (box) box.classList.add('checked');
                total++;
            } else {
                card.classList.remove('active');
                if (box) box.classList.remove('checked');
                total--;
            }

            document.getElementById('activeFeaturedLabel').textContent = total + '/3';
            autoSaveFeatured();
        }

        function autoSaveFeatured() {
            const ids = [];
            document.querySelectorAll('.featured-item-card.active').forEach(c => ids.push(c.getAttribute('data-id')));

            const status = document.getElementById('featuredSaveStatus');
            if (status) { status.textContent = '— Menyimpan...'; status.style.color = '#D97706'; }

            const body = new FormData();
            body.append('_token', featuredCsrfToken);
            ids.forEach(id => body.append('featured_ids[]', id));

            fetch(featuredSaveUrl, { method: 'POST', body })
                .then(res => res.json())
                .then(data => {
                    if (status) {
                        status.textContent = '— ✓ Tersimpan';
                        status.style.color = '#16A34A';
                        setTimeout(() => { status.textContent = ''; }, 2500);
                    }
                })
                .catch(() => {
                    if (status) { status.textContent = '— Gagal menyimpan'; status.style.color = '#EF4444'; }
                });
        }

        // Init counter on load
        document.addEventListener('DOMContentLoaded', function() {
            const total = document.querySelectorAll('.featured-item-card.active').length;
            document.getElementById('activeFeaturedLabel').textContent = total + '/3';
        });

        // Table checkbox clicked toggle handler
        function toggleUnggulanFromTable(id) {
            const card = document.querySelector(`.featured-item-card[data-id="${id}"]`);
            if (card) {
                clickFeaturedCard(card);
            }
        }

        // Table checkbox clicked toggle handler
        function toggleUnggulanFromTable(id) {
            const card = document.querySelector(`.featured-item-card[data-id="${id}"]`);
            if (card) {
                clickFeaturedCard(card);
            }
        }

        // Close when clicking outside modal body
        document.getElementById('productModal').addEventListener('click', e => {
            if (e.target === document.getElementById('productModal')) closeProductModal();
        });
        document.getElementById('categoryAddModal').addEventListener('click', e => {
            if (e.target === document.getElementById('categoryAddModal')) closeCategoryModal();
        });
        document.getElementById('hapusModal').addEventListener('click', e => {
            if (e.target === document.getElementById('hapusModal')) closeHapusModal();
        });
    </script>
@endsection
