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
            padding: 6px 12px;
            border-radius: 9999px;
            cursor: pointer;
            font-size: 0.82rem;
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
            overflow-x: auto;
            width: 100%;
        }

        .clean-file-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
            table-layout: fixed;
            min-width: 800px;
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

        /* Description column expand/collapse styling */
        .desc-cell {
            cursor: pointer;
            user-select: none;
            transition: max-height 0.25s ease;
        }
        .desc-cell.collapsed {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .desc-cell.expanded {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: clip !important;
            word-break: break-word;
        }

        /* Date column styling */
        .date-cell {
            white-space: nowrap;
            padding-left: 8px !important;
            padding-right: 8px !important;
            font-size: 0.82rem;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }

        /* Slightly smaller action icons for Aksi column */
        .clean-file-table .btn-icon-action svg {
            width: 15px;
            height: 15px;
        }
        .clean-file-table .btn-icon-action {
            padding: 5px;
        }
    </style>
@endsection

@section('content')
    <!-- 1. Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="page-title" style="margin: 0 0 4px 0; font-size: 1.6rem; font-weight: 800; color: #0F172A;">Penyimpanan File</h1>
            <p style="margin: 0; color: #64748B; font-size: 0.9rem; font-weight: 500;">Kelola dokumen dan arsip koperasi</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <!-- Category Management Button -->
            <button onclick="openCategoriesModal()" style="background-color: #FFFFFF; color: #475569; border: 1.5px solid #E2E8F0; border-radius: 8px; padding: 10px 20px; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; height: 42px; box-sizing: border-box;" onmouseover="this.style.backgroundColor='#F8FAFC'; this.style.borderColor='#CBD5E1';" onmouseout="this.style.backgroundColor='#FFFFFF'; this.style.borderColor='#E2E8F0';">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <span>Kategori</span>
            </button>

            <!-- Upload File button with upload icon -->
            <button onclick="openUploadModal()" class="btn-upload-file" style="height: 42px; box-sizing: border-box; display: inline-flex; align-items: center; gap: 8px; padding: 0 20px;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span>Upload File</span>
            </button>
        </div>
    </div>




    @if ($errors->any())
        <div style="background-color: #FDE8E8; border: 1px solid #F8B4B4; color: #9B1C1C; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.88rem; font-weight: 600;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>Terjadi kesalahan:</span>
            </div>
            <ul style="margin: 0; padding-left: 20px; font-size: 0.85rem; font-weight: 500;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif    <!-- 2. Kategori Tab Pills & Search -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div style="display: flex; gap: 4px; align-items: center; flex-wrap: wrap; flex: 1; min-width: 0;">
            <a href="{{ route('admin.penyimpanan') }}" class="pill-btn {{ empty($filterKat) ? 'active' : '' }}" style="text-decoration: none;">Semua</a>
            @foreach ($kategoriList as $k)
                <a href="{{ route('admin.penyimpanan', ['kat' => $k]) }}" class="pill-btn {{ ($filterKat === $k) ? 'active' : '' }}" style="text-decoration: none;">{{ $k }}</a>
            @endforeach
        </div>
        
        <div style="position: relative; flex-shrink: 0;">
            <input type="text" id="fileSearch" class="search-input" placeholder="Cari file..." onkeyup="filterFiles()">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2.2" fill="none" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%);">
                <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </div>
    </div>    <!-- 3. Files table in horizontal scroll wrapper -->
    <div class="file-table-card-wrapper">
        <table class="clean-file-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Nama File</th>
                    <th style="width: 12%; padding-left: 8px; padding-right: 8px;">Kategori</th>
                    <th style="width: 25%;">Deskripsi</th>
                    <th style="width: 10%; padding-left: 8px; padding-right: 8px;">Ukuran</th>
                    <th style="width: 10%; padding-left: 8px; padding-right: 8px;">Tanggal</th>
                    <th style="text-align: center; width: 13%; padding-left: 8px; padding-right: 8px;">Aksi</th>
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
                        <td style="white-space: nowrap; padding-left: 8px !important; padding-right: 8px !important;">
                            <span class="category-item-badge">{{ $f->kategori }}</span>
                        </td>
                        <!-- Description -->
                        <td class="desc-cell collapsed" onclick="toggleDesc(this)" style="color: #64748B;" title="{{ $f->keterangan }}">
                            {{ $f->keterangan ?: '-' }}
                        </td>
                        <!-- Format Size standard format -->
                        <td style="font-weight: 500; color: #1E293B; white-space: nowrap; padding-left: 8px !important; padding-right: 8px !important;">
                            {{ \App\Helpers\Helper::formatFileSize($f->ukuran) }}
                        </td>
                        <!-- Formatted Date inside list -->
                        <td class="date-cell" style="color: #64748B;">
                            {{ \Carbon\Carbon::parse($f->uploaded_at)->translatedFormat('d M Y') }}
                        </td>
                        <!-- Actions -->
                        <td style="text-align: center; white-space: nowrap; padding: 16px 4px;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                <!-- Download icon Link -->
                                <a href="{{ asset('uploads/' . $f->nama_file) }}" download="{{ $f->nama_asli }}" class="btn-icon-action" title="Download">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </a>
                                <!-- Lihat File (Preview) icon trigger -->
                                <button type="button" class="btn-icon-action" onclick="previewFile('{{ asset('uploads/' . $f->nama_file) }}', '{{ addslashes($f->nama_asli) }}')" title="Lihat File">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                                <!-- Pencil edit icon trigger -->
                                <button class="btn-icon-action btn-icon-edit" onclick="openEditModal({{ json_encode($f) }})" title="Edit">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </button>
                                <!-- Trash delete form trigger -->
                                <button type="button" class="btn-icon-action btn-icon-delete" onclick="openHapusModal({{ $f->id }}, '{{ addslashes($f->nama_asli) }}')" title="Hapus">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                                <form id="deleteForm-{{ $f->id }}" method="POST" action="{{ route('admin.penyimpanan.destroy', $f->id) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
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
            
            <form method="POST" action="{{ route('admin.penyimpanan.upload') }}" enctype="multipart/form-data" onsubmit="return validatePenyimpananUploadForm(this)" style="margin: 0; display: flex; flex-direction: column; gap: 16px;">
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
                    
                    <input type="text" name="nama_file" id="namaFileDragInput" placeholder="Nama file (mis. dokumen.pdf)" 
                           onclick="event.stopPropagation();" 
                           onmousedown="event.stopPropagation();" 
                           onmouseup="event.stopPropagation();" 
                           onpointerdown="event.stopPropagation();" 
                           onpointerup="event.stopPropagation();" 
                           style="margin-top: 12px; display: inline-block; width: 90%; padding: 8px 12px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.88rem; outline: none; background: #FFFFFF; text-align: left; box-sizing: border-box; text-overflow: ellipsis; overflow: hidden;">
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Kategori</label>
                    <div style="position: relative; width: 100%;">
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
                    <div style="position: relative; width: 100%;">
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

    <!-- MODAL 3: KELOLA KATEGORI -->
    <div class="custom-overlay" id="categoriesModal">
        <div class="modal-body" style="position: relative; max-width: 440px; padding: 24px; border-radius: 20px;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Red Tag Icon -->
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <h3 style="font-weight: 800; font-size: 1.2rem; color: #1E293B; margin: 0;">Kategori File</h3>
                </div>
                <button onclick="closeCategoriesModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; transition: color 0.2s;" onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94A3B8'">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Form Tambah Kategori -->
            <div style="margin-bottom: 20px;">
                <form id="addCategoryForm" onsubmit="submitNewCategory(event)">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" id="newCategoryInput" placeholder="Nama kategori baru..." required style="flex: 1; padding: 12px 16px; border: 1.5px solid #F1F5F9; border-radius: 12px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; height: 46px;">
                        <button type="submit" style="width: 46px; height: 46px; border-radius: 12px; background-color: #DC2626; border: none; color: white; font-size: 1.5rem; font-weight: 500; cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background 0.2s; padding: 0;" onmouseover="this.style.backgroundColor='#B91C1C'" onmouseout="this.style.backgroundColor='#DC2626'">
                            +
                        </button>
                    </div>
                </form>
            </div>

            <!-- Daftar Kategori dengan Tombol Hapus -->
            <div id="categoriesListContainer" style="display: flex; flex-direction: column; gap: 12px; max-height: 280px; overflow-y: auto; padding-right: 4px; margin-bottom: 24px;">
                @foreach ($kategoriList as $k)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #FAFAFA; border-radius: 12px; border: 1px solid #F8FAFC;">
                        <span style="font-weight: 600; color: #1E293B; font-size: 0.93rem;">{{ $k }}</span>
                        <button type="button" onclick="deleteCategory('{{ $k }}')" style="background: none; border: none; color: #D1D5DB; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 4px; transition: color 0.15s;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#D1D5DB'">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>

            <!-- Selesai Button -->
            <div style="border-top: 1px solid #F1F5F9; padding-top: 16px;">
                <button type="button" onclick="closeCategoriesModal()" style="width: 100%; height: 46px; background-color: #B91C1C; color: white; border: none; border-radius: 12px; font-weight: 700; font-size: 0.95rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#991B1B'" onmouseout="this.style.backgroundColor='#B91C1C'">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: HAPUS FILE -->
    <div class="custom-overlay" id="hapusModal">
        <div class="modal-body" style="max-width: 420px; border-radius: 20px; padding: 28px 28px 24px;">
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
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Hapus File?</h3>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.6;">
                        Anda akan menghapus file <strong id="hapusNama"></strong>.<br>
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
            <!-- Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button onclick="closeHapusModal()" class="btn-cancel-custom" style="flex: none; width: auto; padding: 9px 22px;">Batal</button>
                <button onclick="submitHapus()" class="btn-submit-custom" style="flex: none; width: auto; padding: 9px 22px;">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- MODAL 4: PRATINJAU FILE -->
    <div class="custom-overlay" id="previewModal">
        <div class="modal-body" style="position: relative; max-width: 800px; width: 90%; max-height: 90vh; display: flex; flex-direction: column; padding: 24px; border-radius: 20px;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px; flex-shrink: 0;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <!-- Eye Icon -->
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <h3 style="font-weight: 800; font-size: 1.2rem; color: #1E293B; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 600px;" id="previewModalTitle">Pratinjau File</h3>
                </div>
                <button onclick="closePreviewModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; transition: color 0.2s;" onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94A3B8'">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Content Area -->
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0; min-height: 300px; padding: 12px; box-sizing: border-box;">
                <img id="previewImage" src="" alt="Pratinjau Gambar" style="max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 8px; display: none;">
                <div id="previewFallbackMessage" style="text-align: center; color: #64748B; font-size: 0.95rem; font-weight: 500; display: none;">
                    <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 12px; display: block;">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span id="previewFallbackText">Pratinjau tidak tersedia untuk format berkas ini. Silakan unduh dokumen untuk melihat.</span>
                </div>
            </div>

            <!-- Footer -->
            <div style="border-top: 1px solid #F1F5F9; padding-top: 16px; margin-top: 20px; display: flex; justify-content: flex-end; flex-shrink: 0;">
                <button type="button" onclick="closePreviewModal()" class="btn-cancel-custom" style="padding: 10px 32px; width: auto; flex: none; margin: 0;">Tutup</button>
            </div>
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

        function handleFileUploadChange(input) {
            if (input.files && input.files[0]) {
                document.getElementById('namaFileDragInput').value = input.files[0].name;
            }
        }

        function addNewCategoryPrompt(selectId) {
            const newCat = prompt("Masukkan nama Kategori baru:");
            if (newCat && newCat.trim() !== "") {
                const trimmedCat = newCat.trim();
                
                fetch("{{ route('admin.penyimpanan.kategori.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ kategori: trimmedCat })
                })
                .then(async response => {
                    const selectElement = document.getElementById(selectId);
                    if (response.ok) {
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
                    } else {
                        const data = await response.json();
                        let exists = false;
                        for (let i = 0; i < selectElement.options.length; i++) {
                            if (selectElement.options[i].value.toLowerCase() === trimmedCat.toLowerCase()) {
                                selectElement.selectedIndex = i;
                                exists = true;
                                break;
                            }
                        }
                        if (!exists) {
                            alert(data.message || "Gagal menambahkan kategori.");
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Terjadi kesalahan jaringan.");
                });
            }
        }

        function openCategoriesModal() {
            document.getElementById('categoriesModal').classList.add('active');
        }

        function closeCategoriesModal() {
            document.getElementById('categoriesModal').classList.remove('active');
        }

        function submitNewCategory(event) {
            event.preventDefault();
            const input = document.getElementById('newCategoryInput');
            const catName = input.value.trim();
            if (catName === "") return;

            fetch("{{ route('admin.penyimpanan.kategori.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ kategori: catName })
            })
            .then(async response => {
                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.message || "Gagal menambahkan kategori.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Terjadi kesalahan jaringan.");
            });
        }

        function deleteCategory(catName) {
            if (!confirm(`Hapus kategori "${catName}"?`)) return;

            fetch("{{ url('/admin/penyimpanan/kategori') }}/" + encodeURIComponent(catName), {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(async response => {
                if (response.ok) {
                    location.reload();
                } else {
                    const data = await response.json();
                    alert(data.message || "Gagal menghapus kategori.");
                }
            })
            .catch(err => {
                console.error(err);
                alert("Terjadi kesalahan jaringan.");
            });
        }

        // Close when clicking layout overlays
        document.getElementById('uploadModal').addEventListener('click', e => {
            if (e.target === document.getElementById('uploadModal')) closeUploadModal();
        });
        document.getElementById('editModal').addEventListener('click', e => {
            if (e.target === document.getElementById('editModal')) closeEditModal();
        });
        document.getElementById('categoriesModal').addEventListener('click', e => {
            if (e.target === document.getElementById('categoriesModal')) closeCategoriesModal();
        });
        document.getElementById('hapusModal').addEventListener('click', e => {
            if (e.target === document.getElementById('hapusModal')) closeHapusModal();
        });
        document.getElementById('previewModal').addEventListener('click', e => {
            if (e.target === document.getElementById('previewModal')) closePreviewModal();
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

        function validatePenyimpananUploadForm(form) {
            const input = document.getElementById('fileUploadInput');
            if (input && input.files && input.files[0]) {
                const fileSize = input.files[0].size;
                const maxBytes = 20 * 1024 * 1024; // 20 MB limit
                if (fileSize > maxBytes) {
                    alert('Ukuran file terlalu besar! Maksimal 20 MB.');
                    return false;
                }
            }
            return true;
        }

        // Hapus Modal
        var _hapusTargetId = null;
        function openHapusModal(id, nama) {
            _hapusTargetId = id;
            document.getElementById('hapusNama').innerText = nama;
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

        function previewFile(url, fileName) {
            const ext = fileName.split('.').pop().toLowerCase();
            const imgExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            
            if (ext === 'pdf') {
                window.open(url, '_blank');
            } else if (imgExtensions.includes(ext)) {
                // Show Image modal
                document.getElementById('previewModalTitle').innerText = fileName;
                const img = document.getElementById('previewImage');
                img.src = url;
                img.style.display = 'block';
                document.getElementById('previewFallbackMessage').style.display = 'none';
                
                document.getElementById('previewModal').classList.add('active');
            } else {
                // Fallback for unsupported files
                document.getElementById('previewModalTitle').innerText = fileName;
                document.getElementById('previewImage').style.display = 'none';
                document.getElementById('previewFallbackText').innerText = 'Pratinjau tidak tersedia untuk format berkas ini. Silakan unduh dokumen untuk melihat.';
                document.getElementById('previewFallbackMessage').style.display = 'block';
                
                document.getElementById('previewModal').classList.add('active');
            }
        }

        function closePreviewModal() {
            document.getElementById('previewModal').classList.remove('active');
            // Clear image source to avoid loading lag next time
            document.getElementById('previewImage').src = '';
        }

        function toggleDesc(el) {
            el.classList.toggle('collapsed');
            el.classList.toggle('expanded');
        }
    </script>
@endsection
