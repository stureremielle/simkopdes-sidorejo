@extends('layouts.admin')

@section('title', 'Penyimpanan File - Panel Admin')

@section('breadcrumb', 'Penyimpanan File')

@section('styles')
    <style>
        /* Modern Layout Details matching Irjul styles */
        .pill-btn {
            background-color: transparent;
            color: #475569;
            border: none;
            padding: 8px 18px;
            border-radius: 9999px;
            cursor: pointer;
            font-size: 0.88rem;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .pill-btn.active {
            background-color: #DC2626; /* Red theme active */
            color: #FFFFFF;
        }

        .search-input {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px 14px 10px 38px;
            font-size: 0.88rem;
            outline: none;
            width: 220px;
            box-sizing: border-box;
            background: #FFFFFF;
            transition: border-color 0.2s;
        }

        .search-input:focus {
            border-color: #DC2626;
        }

        .btn-upload-file {
            background-color: #DC2626;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.88rem;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-upload-file:hover {
            background-color: #B91C1C;
        }

        .file-table-card-wrapper {
            background-color: #FFFFFF;
            border-radius: 1rem; /* rounded-2xl */
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03); /* shadow-sm */
            border: 1px solid #E2E8F0;
            overflow: hidden;
            width: 100%;
        }

        .clean-file-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
            table-layout: fixed;
        }

        .clean-file-table th {
            padding: 14px 20px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #F1F5F9;
        }

        .clean-file-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
            color: #475569;
        }

        .clean-file-table tr:last-child td {
            border-bottom: none;
        }

        .clean-file-table tr:hover td {
            background-color: #FAFAFA;
        }

        /* Category badge style: gray pill */
        .category-item-badge {
            background-color: #F1F5F9;
            color: #475569;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 9999px;
            display: inline-block;
        }



        /* Modals style */
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
            width: 100%;
            max-width: 520px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        /* Drag & Drop Zone */
        .drag-drop-zone {
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            padding: 24px 16px;
            text-align: center;
            background: #FFFFFF;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-bottom: 20px;
        }

        .drag-drop-zone svg {
            color: #94A3B8;
        }

        .drag-drop-zone:hover, .drag-drop-zone.dragover {
            border-color: #DC2626;
            background: #FFF1F2;
        }

        .drag-drop-zone:hover svg {
            color: #DC2626;
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
            border: 1.5px solid #E2E8F0;
            border-radius: 8px;
            font-size: 0.88rem;
            outline: none;
            box-sizing: border-box;
            background-color: #FFFFFF;
            color: #1E293B;
            transition: border-color 0.2s;
        }

        .form-row input:focus, .form-row select:focus, .form-row textarea:focus {
            border-color: #DC2626;
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
            background: #FFFFFF;
            color: #475569;
            border: 1.5px solid #E2E8F0;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.88rem;
            transition: background 0.2s;
        }

        .btn-cancel-custom:hover {
            background: #F8FAFC;
        }

        .btn-submit-custom {
            background: #DC2626;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.88rem;
            transition: background 0.2s;
        }

        .btn-submit-custom:hover {
            background: #B91C1C;
        }
    </style>
@endsection

@section('content')
    @if (session('success'))
        <div style="background-color: #DCFCE7; border: 1px solid #BBF7D0; color: #16A34A; padding: 12px 16px; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"></polyline></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="background-color: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 12px 16px; border-radius: 8px; font-weight: 600; margin-bottom: 20px; font-size: 0.88rem; display: flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- 1. Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="page-title" style="margin: 0 0 4px 0; font-size: 1.6rem; font-weight: 800; color: #0F172A;">Penyimpanan File</h1>
            <p style="margin: 0; color: #64748B; font-size: 0.9rem; font-weight: 500;">Kelola dokumen dan arsip koperasi</p>
        </div>
        <!-- Upload File button with upload icon -->
        <button onclick="openUploadModal()" class="btn-upload-file">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
            </svg>
            Upload File
        </button>
    </div>



    <!-- 2. Kategori Tab Pills & Search -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; gap: 4px; overflow-x: auto; padding-bottom: 4px;">
            <a href="{{ route('admin.penyimpanan') }}" class="pill-btn {{ empty($filterKat) ? 'active' : '' }}" style="text-decoration: none;">Semua</a>
            @foreach ($kategoriList as $k)
                <a href="{{ route('admin.penyimpanan', ['kat' => $k]) }}" class="pill-btn {{ ($filterKat === $k) ? 'active' : '' }}" style="text-decoration: none;">{{ $k }}</a>
            @endforeach
        </div>
        
        <div style="position: relative;">
            <input type="text" id="fileSearch" class="search-input" placeholder="Cari file..." onkeyup="filterFiles()">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2.2" fill="none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);">
                <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
    </div>

    <!-- 3. Files table in horizontal scroll wrapper -->
    <div class="file-table-card-wrapper">
        <table class="clean-file-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Nama File</th>
                    <th style="width: 15%;">Kategori</th>
                    <th style="width: 25%;">Deskripsi</th>
                    <th style="width: 10%;">Ukuran</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="text-align: center; width: 10%;">Aksi</th>
                </tr>
            </thead>
            <tbody id="filesTbody">
                @forelse ($fileList as $f)
                    @php
                        $ext = strtolower(pathinfo($f->nama_asli, PATHINFO_EXTENSION));
                    @endphp
                    <tr class="file-row" data-nama="{{ strtolower($f->nama_asli) }}">
                        <!-- File type specific icon + name -->
                        <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <span style="display: inline-flex; align-items: center; gap: 10px; max-width: 100%;">
                                @if ($ext === 'pdf')
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                @elseif (in_array($ext, ['xlsx', 'xls']))
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="8" y1="13" x2="16" y2="13"></line>
                                        <line x1="8" y1="17" x2="16" y2="17"></line>
                                        <line x1="8" y1="9" x2="10" y2="9"></line>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                @endif
                                <span style="font-weight: 700; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $f->nama_asli }}">{{ $f->nama_asli }}</span>
                            </span>
                        </td>
                        <!-- Category Badge -->
                        <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <span class="category-item-badge">{{ $f->kategori }}</span>
                        </td>
                        <!-- Description -->
                        <td style="color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $f->keterangan }}">
                            {{ $f->keterangan ?: '-' }}
                        </td>
                        <!-- Format Size standard format -->
                        <td style="font-weight: 500; color: #1E293B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ \App\Helpers\Helper::formatFileSize($f->ukuran) }}
                        </td>
                        <!-- Formatted Date inside list -->
                        <td style="color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ \Carbon\Carbon::parse($f->uploaded_at)->translatedFormat('M Y') }}
                        </td>
                        <!-- Actions -->
                        <td style="text-align: center; white-space: nowrap;">
                            <div style="display: flex; gap: 10px; justify-content: center; align-items: center;">
                                <!-- Download icon Link -->
                                <a href="{{ route('admin.penyimpanan.download', $f->id) }}" class="btn-icon-action" title="Download">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </a>
                                <!-- Pencil edit icon trigger -->
                                <button class="btn-icon-action btn-icon-edit" onclick="openEditModal({{ json_encode($f) }})" title="Edit">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </button>
                                <!-- Custom delete button -->
                                <a href="javascript:void(0)" class="btn-icon-action btn-icon-delete" onclick="openDeleteModal('{{ route('admin.penyimpanan.destroy', $f->id) }}', '{{ addslashes($f->nama_asli) }}')" title="Hapus" style="text-decoration: none;">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94A3B8; font-weight: 500;">
                            Belum ada file terarsip dalam kategori ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- MODAL 1: UPLOAD FILE -->
    <div class="custom-overlay" id="uploadModal">
        <div class="modal-body" style="position: relative; max-width: 550px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-weight: 800; font-size: 1.25rem; color: #0F172A; margin: 0;">Upload File</h3>
                <button onclick="closeUploadModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; transition: color 0.2s;" onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94A3B8'">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form method="POST" action="{{ route('admin.penyimpanan.upload') }}" enctype="multipart/form-data" style="margin: 0; display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div class="drag-drop-zone" onclick="document.getElementById('fileUploadInput').click()" id="dropZone" style="border: 2px dashed #CBD5E1; border-radius: 12px; padding: 24px 16px; text-align: center; background: #FFFFFF; cursor: pointer; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; margin: 0;">
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <div style="font-weight: 600; color: #64748B; font-size: 0.88rem; margin-top: 4px;">Klik atau seret file ke sini</div>
                    <div style="color: #94A3B8; font-size: 0.78rem;">PDF, XLSX, DOCX, dsb.</div>
                    
                    <input type="file" name="file_upload" id="fileUploadInput" required style="display: none;" onchange="handleFileUploadChange(this)">
                    
                    <input type="text" name="nama_file" id="namaFileDragInput" placeholder="Nama file (mis. dokumen.pdf)" onclick="event.stopPropagation();" style="margin-top: 12px; display: inline-block; width: 90%; padding: 8px 12px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.88rem; outline: none; background: #FFFFFF; text-align: left; box-sizing: border-box; text-overflow: ellipsis; overflow: hidden;">
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Kategori</label>
                    <div style="display: flex; gap: 8px; align-items: center; width: 100%;">
                        <div style="flex: 1; position: relative;">
                            <select name="kategori" id="uploadKategoriSelect" required style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%; appearance: none; -webkit-appearance: none;">
                                @foreach ($kategoriList as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                            <div style="position: absolute; right: 14px; bottom: 12px; pointer-events: none; color: #64748B;">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <button type="button" onclick="addNewCategoryPrompt('uploadKategoriSelect')" style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 8px; padding: 0 16px; color: #475569; font-weight: 700; font-size: 0.88rem; cursor: pointer; white-space: nowrap; height: 42px; display: flex; align-items: center; justify-content: center; gap: 4px; box-sizing: border-box;">
                            + Baru
                        </button>
                    </div>
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Deskripsi</label>
                    <input type="text" name="keterangan" placeholder="Deskripsi singkat file..." style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%;">
                </div>

                <div style="display: flex; gap: 12px; border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 8px; justify-content: flex-end; align-items: center;">
                    <button type="button" class="btn-cancel-custom" onclick="closeUploadModal()" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; border: 1.5px solid #E2E8F0; background: #FFFFFF; color: #475569; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">Batal</button>
                    <button type="submit" class="btn-submit-custom" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; background-color: #DC2626; border: none; color: white; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: EDIT KETERANGAN FILE -->
    <div class="custom-overlay" id="editModal">
        <div class="modal-body" style="position: relative; max-width: 550px; padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-weight: 800; font-size: 1.25rem; color: #0F172A; margin: 0;">Edit Informasi File</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; transition: color 0.2s;" onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94A3B8'">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            
            <form id="editForm" method="POST" action="" style="margin: 0; display: flex; flex-direction: column; gap: 16px;">
                @csrf
                @method('PUT')
                
                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Nama File</label>
                    <input type="text" name="nama_asli" id="editNamaAsli" required placeholder="Masukkan nama file..." style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%;">
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Kategori</label>
                    <div style="display: flex; gap: 8px; align-items: center; width: 100%;">
                        <div style="flex: 1; position: relative;">
                            <select name="kategori" id="editKategori" required style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%; appearance: none; -webkit-appearance: none;">
                                @foreach ($kategoriList as $k)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endforeach
                            </select>
                            <div style="position: absolute; right: 14px; bottom: 12px; pointer-events: none; color: #64748B;">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <button type="button" onclick="addNewCategoryPrompt('editKategori')" style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 8px; padding: 0 16px; color: #475569; font-weight: 700; font-size: 0.88rem; cursor: pointer; white-space: nowrap; height: 42px; display: flex; align-items: center; justify-content: center; gap: 4px; box-sizing: border-box;">
                            + Baru
                        </button>
                    </div>
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Deskripsi</label>
                    <input type="text" name="keterangan" id="editKeterangan" placeholder="Deskripsi singkat file..." style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%;">
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Tanggal Upload</label>
                    <input type="text" id="editTanggalField" readonly style="background-color: #F8FAFC; color: #64748B; cursor: not-allowed; border: 1.5px solid #F1F5F9; border-radius: 8px; padding: 10px 14px; font-size: 0.9rem; width: 100%; box-sizing: border-box;" value="-">
                </div>

                <div style="display: flex; gap: 12px; border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 8px; justify-content: flex-end; align-items: center;">
                    <button type="button" class="btn-cancel-custom" onclick="closeEditModal()" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; border: 1.5px solid #E2E8F0; background: #FFFFFF; color: #475569; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">Batal</button>
                    <button type="submit" class="btn-submit-custom" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; background-color: #DC2626; border: none; color: white; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: KONFIRMASI HAPUS FILE -->
    <div class="custom-overlay" id="deleteModal">
        <div class="modal-body" style="position: relative; max-width: 440px; padding: 24px; text-align: center;">
            <div style="background-color: #FEE2E2; color: #DC2626; width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px auto;">
                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            
            <h3 style="font-weight: 800; font-size: 1.25rem; color: #0F172A; margin: 0 0 8px 0;">Hapus Dokumen?</h3>
            <p style="margin: 0 0 24px 0; color: #64748B; font-size: 0.9rem; line-height: 1.5; font-weight: 500;">
                Apakah Anda yakin ingin menghapus berkas <strong id="deleteFileName" style="color: #0F172A; word-break: break-all;">-</strong> secara permanen? Tindakan ini tidak dapat dibatalkan.
            </p>
            
            <form id="deleteForm" method="POST" action="" style="margin: 0;">
                @csrf
                @method('DELETE')
                
                <div style="display: flex; gap: 12px; align-items: center;">
                    <button type="button" class="btn-cancel-custom" onclick="closeDeleteModal()" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; border: 1.5px solid #E2E8F0; background: #FFFFFF; color: #475569; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#FFFFFF'">Batal</button>
                    <button type="submit" class="btn-submit-custom" style="flex: 1; padding: 11px 24px; font-size: 0.9rem; border-radius: 8px; background-color: #DC2626; border: none; color: white; font-weight: 600; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Real-time search filter function
        function filterFiles() {
            const query = document.getElementById('fileSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.file-row');

            rows.forEach(row => {
                const name = row.getAttribute('data-nama');
                if (name.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Modals control
        function openUploadModal() {
            document.getElementById('uploadModal').classList.add('active');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.remove('active');
        }

        function openEditModal(file) {
            document.getElementById('editForm').action = "{{ url('/admin/penyimpanan') }}/" + file.id;
            document.getElementById('editNamaAsli').value = file.nama_asli;
            
            const selectElement = document.getElementById('editKategori');
            let categoryFound = false;
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === file.kategori) {
                    categoryFound = true;
                    break;
                }
            }
            if (!categoryFound) {
                const opt = document.createElement("option");
                opt.value = file.kategori;
                opt.text = file.kategori;
                selectElement.add(opt);
            }
            selectElement.value = file.kategori;
            
            document.getElementById('editKeterangan').value = file.keterangan || '';
            
            if (file.uploaded_at) {
                const date = new Date(file.uploaded_at);
                const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];
                const formattedDate = months[date.getMonth()] + " " + date.getFullYear();
                document.getElementById('editTanggalField').value = formattedDate;
            } else {
                document.getElementById('editTanggalField').value = '-';
            }
            
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Delete Modal control
        function openDeleteModal(actionUrl, fileName) {
            document.getElementById('deleteForm').action = actionUrl;
            document.getElementById('deleteFileName').textContent = fileName;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        function handleFileUploadChange(input) {
            if (input.files && input.files[0]) {
                document.getElementById('namaFileDragInput').value = input.files[0].name;
            }
        }

        function addNewCategoryPrompt(selectId) {
            const newCat = prompt("Masukkan nama Kategori baru:");
            if (newCat && newCat.trim() !== "") {
                const trimmedCat = newCat.trim();
                const selectElement = document.getElementById(selectId);
                let exists = false;
                for (let i = 0; i < selectElement.options.length; i++) {
                    if (selectElement.options[i].value.toLowerCase() === trimmedCat.toLowerCase()) {
                        selectElement.selectedIndex = i;
                        exists = true;
                        break;
                    }
                }
                if (!exists) {
                    const option = document.createElement("option");
                    option.value = trimmedCat;
                    option.text = trimmedCat;
                    selectElement.add(option);
                    selectElement.value = trimmedCat;
                }
            }
        }

        // Close when clicking layout overlays
        document.getElementById('uploadModal').addEventListener('click', e => {
            if (e.target === document.getElementById('uploadModal')) closeUploadModal();
        });
        document.getElementById('editModal').addEventListener('click', e => {
            if (e.target === document.getElementById('editModal')) closeEditModal();
        });
        document.getElementById('deleteModal').addEventListener('click', e => {
            if (e.target === document.getElementById('deleteModal')) closeDeleteModal();
        });

        // Setup Drag & Drop
        const dropZone = document.getElementById('dropZone');
        if (dropZone) {
            dropZone.addEventListener('dragover', e => {
                e.preventDefault();
                dropZone.classList.add('dragover');
            });
            dropZone.addEventListener('dragleave', () => {
                dropZone.classList.remove('dragover');
            });
            dropZone.addEventListener('drop', e => {
                e.preventDefault();
                dropZone.classList.remove('dragover');
                if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                    const fileInput = document.getElementById('fileUploadInput');
                    fileInput.files = e.dataTransfer.files;
                    handleFileUploadChange(fileInput);
                }
            });
        }
    </script>
@endsection
