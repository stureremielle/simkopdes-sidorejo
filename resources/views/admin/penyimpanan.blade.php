@extends('layouts.admin')

@section('title', 'Penyimpanan File - Panel Admin')

@section('breadcrumb', 'Penyimpanan File')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/penyimpanan.css') }}?v={{ filemtime(public_path('assets/css/admin/penyimpanan.css')) }}">
    <style>
        .upload-progress-container {
            width: 100%;
            background-color: #E2E8F0;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 6px;
            border: 1px solid #CBD5E1;
        }
        .upload-progress-bar {
            height: 10px;
            background-color: #10B981;
            width: 0%;
            transition: width 0.1s ease;
        }
        .uploading-row {
            background-color: #F8FAFC !important;
            border-left: 4px solid #3B82F6 !important;
            transition: all 0.3s ease;
        }
        .download-progress-panel {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 10000;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 16px 20px;
            width: 320px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all 0.3s ease;
            transform: translateY(120%);
            opacity: 0;
            box-sizing: border-box;
        }
        .download-progress-panel.active {
            transform: translateY(0);
            opacity: 1;
        }
        .download-progress-container {
            width: 100%;
            background-color: #E2E8F0;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #CBD5E1;
        }
        .download-progress-bar {
            height: 10px;
            background-color: #3B82F6;
            width: 0%;
            transition: width 0.1s ease;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1.5s linear infinite;
        }
    </style>
@endsection

@section('content')
    <div id="toast-container" style="position: fixed; top: 24px; right: 24px; z-index: 9999; display: flex; flex-direction: column; gap: 12px; max-width: 380px; width: calc(100% - 48px);">
        @if (session('success'))
            <div class="toast-alert success" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; color: #1E293B; padding: 16px 20px; border-radius: 12px; font-weight: 600; font-size: 0.88rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; transform: translateX(120%); opacity: 0; box-sizing: border-box; width: 100%;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <div style="flex: 1; line-height: 1.4; font-weight: 500; word-break: break-word; overflow-wrap: break-word;">
                    {!! session('success') !!}
                </div>
                <button onclick="dismissToast(this)" style="background: none; border: none; color: #94A3B8; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center; outline: none;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-alert error" style="background-color: #FFFFFF; border: 1px solid #E2E8F0; color: #1E293B; padding: 16px 20px; border-radius: 12px; font-weight: 600; font-size: 0.88rem; display: flex; align-items: center; gap: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); transition: all 0.3s ease; transform: translateX(120%); opacity: 0; box-sizing: border-box; width: 100%;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div style="flex: 1; line-height: 1.4; font-weight: 500; word-break: break-word; overflow-wrap: break-word;">
                    {!! session('error') !!}
                </div>
                <button onclick="dismissToast(this)" style="background: none; border: none; color: #94A3B8; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center; outline: none;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
        @endif
    </div>

    <!-- 1. Header Section -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="page-title" style="margin: 0 0 4px 0; font-size: 1.6rem; font-weight: 800; color: #0F172A;">Penyimpanan File</h1>
            <p style="margin: 0; color: #64748B; font-size: 0.9rem; font-weight: 500;">Kelola dokumen dan arsip koperasi</p>
        </div>
        <div style="display: flex; gap: 12px; align-items: center;">
            <!-- Tombol Kelola Kategori -->
            <button onclick="openCategoriesModal()" style="background-color: #FFFFFF; color: #475569; border: 1.5px solid #E2E8F0; border-radius: 8px; padding: 10px 20px; font-size: 0.88rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; height: 42px; box-sizing: border-box;" onmouseover="this.style.backgroundColor='#F8FAFC'; this.style.borderColor='#CBD5E1';" onmouseout="this.style.backgroundColor='#FFFFFF'; this.style.borderColor='#E2E8F0';">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                    <line x1="7" y1="7" x2="7.01" y2="7"></line>
                </svg>
                <span>Kategori</span>
            </button>

            <!-- Tombol Unggah File -->
            <button onclick="openUploadModal()" class="btn-upload-file" style="height: 42px; box-sizing: border-box; display: inline-flex; align-items: center; gap: 8px; padding: 0 20px;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span>Unggah File</span>
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
    @endif    <!-- 2. Tab Kategori & Pencarian -->
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
    </div>    <!-- Tabel daftar berkas dalam pembungkus gulir horizontal -->
    <div class="file-table-card-wrapper">
        <table class="clean-file-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Nama File</th>
                    <th style="width: 12%; padding-left: 8px; padding-right: 8px;">Kategori</th>
                    <th style="width: 25%;">Keterangan</th>
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
                        <!-- Ikon tipe file spesifik + nama -->
                        <td style="white-space: normal; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;">
                            <span style="display: inline-flex; align-items: flex-start; gap: 10px; max-width: 100%;">
                                @if ($ext === 'pdf')
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#EF4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                @elseif (in_array($ext, ['xlsx', 'xls']))
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#22C55E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="8" y1="13" x2="16" y2="13"></line>
                                        <line x1="8" y1="17" x2="16" y2="17"></line>
                                        <line x1="8" y1="9" x2="10" y2="9"></line>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#3B82F6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 2px;">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                @endif
                                <span style="font-weight: 700; color: #1E293B; white-space: normal; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;" title="{{ $f->nama_asli }}">{{ $f->nama_asli }}</span>
                            </span>
                        </td>
                        <!-- Lencana Kategori -->
                        <td style="white-space: nowrap; padding-left: 8px !important; padding-right: 8px !important;">
                            <span class="category-item-badge">{{ $f->kategori }}</span>
                        </td>
                        <!-- Keterangan -->
                        <td class="desc-cell" style="color: #64748B; white-space: normal; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;" title="{{ $f->keterangan }}">
                            {{ $f->keterangan ?: '-' }}
                        </td>
                        <!-- Format ukuran standar -->
                        <td style="font-weight: 500; color: #1E293B; white-space: nowrap; padding-left: 8px !important; padding-right: 8px !important;">
                            {{ \App\Helpers\Helper::formatFileSize($f->ukuran) }}
                        </td>
                        <!-- Tanggal terformat dalam daftar -->
                        <td class="date-cell" style="color: #64748B;">
                            {{ \Carbon\Carbon::parse($f->uploaded_at)->setTimezone('Asia/Makassar')->translatedFormat('d M Y') }}
                        </td>
                        <!-- Aksi -->
                        <td style="text-align: center; white-space: nowrap; padding: 16px 4px;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                <!-- Ikon unduh -->
                                <a href="javascript:void(0)" onclick="triggerSecureDownload(event, '{{ route('admin.penyimpanan.download', $f->id) }}', '{{ addslashes($f->nama_asli) }}')" class="btn-icon-action" title="Download">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </a>
                                <!-- Pemicu pratinjau file -->
                                <button type="button" class="btn-icon-action" onclick="previewFile('{{ route('admin.penyimpanan.preview', $f->id) }}', '{{ addslashes($f->nama_asli) }}')" title="Lihat File">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>
                                <!-- Pemicu sunting -->
                                <button class="btn-icon-action btn-icon-edit" onclick="openEditModal({{ json_encode($f) }})" title="Edit">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </button>
                                <!-- Pemicu formulir hapus -->
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
            
            <form id="uploadFileForm" method="POST" action="{{ route('admin.penyimpanan.upload') }}" enctype="multipart/form-data" onsubmit="return validatePenyimpananUnggahForm(this)" style="margin: 0; display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">File <span style="color: #EF4444;">*</span></label>
                    <div class="drag-drop-zone" onclick="document.getElementById('fileUploadInput').click()" id="dropZone" style="border: 2px dashed #CBD5E1; border-radius: 12px; padding: 24px 16px; text-align: center; background: #FFFFFF; cursor: pointer; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; margin: 0;">
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                        <div style="font-weight: 600; color: #64748B; font-size: 0.88rem; margin-top: 4px;">Klik atau seret file ke sini</div>
                        <div style="color: #94A3B8; font-size: 0.78rem;">PDF, DOCX, XLSX, JPG, PNG, dsb.</div>
                        
                        <input type="file" name="file_upload" id="fileUploadInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp" required style="display: none;" onchange="handleFileUploadChange(this)">
                        
                        <input type="text" name="nama_file" id="namaFileDragInput" placeholder="Nama file (mis. dokumen.pdf)" 
                               maxlength="150"
                               onclick="event.stopPropagation();" 
                               onmousedown="event.stopPropagation();" 
                               onmouseup="event.stopPropagation();" 
                               onpointerdown="event.stopPropagation();" 
                               onpointerup="event.stopPropagation();" 
                               style="margin-top: 12px; display: inline-block; width: 90%; padding: 8px 12px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.88rem; outline: none; background: #FFFFFF; text-align: left; box-sizing: border-box; text-overflow: ellipsis; overflow: hidden;">
                    </div>
                    <span class="js-error-msg" id="error-file_upload" style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">@error('file_upload') {{ $message }} @enderror</span>
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Kategori <span style="color: #EF4444;">*</span></label>
                    <div style="position: relative; width: 100%;">
                        <select name="kategori" id="uploadKategoriSelect" required style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%; appearance: none; -webkit-appearance: none;">
                            <option value="" disabled selected>— Pilih Kategori —</option>
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
                    <span class="js-error-msg" id="error-uploadKategoriSelect" style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">@error('kategori') {{ $message }} @enderror</span>
                </div>

                <div class="form-row" style="margin: 0;">
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Keterangan <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="keterangan" id="uploadKeteranganInput" required placeholder="Keterangan singkat file..." style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%;">
                    <span class="js-error-msg" id="error-uploadKeteranganInput" style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">@error('keterangan') {{ $message }} @enderror</span>
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
                    <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">Keterangan</label>
                    <input type="text" name="keterangan" id="editKeterangan" placeholder="Keterangan singkat file..." style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%;">
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
                    <!-- Ikon Label Merah -->
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

            <!-- Formulir Tambah Kategori -->
            <div style="margin-bottom: 20px;">
                <form id="addCategoryForm" onsubmit="submitNewCategory(event)">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="text" id="newCategoryInput" placeholder="Nama kategori baru..." maxlength="20" required style="flex: 1; padding: 12px 16px; border: 1.5px solid #F1F5F9; border-radius: 12px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; height: 46px;">
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

            <!-- Tombol Selesai -->
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
                <!-- Ikon -->
                <div style="flex-shrink: 0; width: 48px; height: 48px; border-radius: 50%; background: #FEE2E2; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <!-- Teks -->
                <div style="flex: 1; min-width: 0; text-align: left;">
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Hapus File?</h3>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.6;">
                        Anda akan menghapus file:<br>
                        <strong id="hapusNama" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; white-space: normal; word-wrap: break-word; overflow-wrap: anywhere; word-break: break-word; color: #0F172A; font-weight: 700; margin: 4px 0 6px 0; line-height: 1.45; max-height: 4.35em;"></strong>
                        Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
            </div>
            <!-- Tombol -->
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
                    <!-- Ikon Mata -->
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

            <!-- Area Konten -->
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0; min-height: 300px; padding: 12px; box-sizing: border-box;">
                <img id="previewImage" style="display: none; max-width: 100%; max-height: 400px; border-radius: 8px;" src="" alt="Pratinjau Gambar">
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

    <!-- PANEL: UNDUH PROGRESS (FLOATING) -->
    <div class="download-progress-panel" id="downloadProgressPanel">
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #F1F5F9; padding-bottom: 8px; margin-bottom: 4px;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="animate-spin" id="downloadingIcon">
                    <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                </svg>
                <strong style="font-size: 0.88rem; color: #1E293B;" id="downloadPanelTitle">Mengunduh Berkas...</strong>
            </div>
            <button onclick="cancelDownload()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 18px; height: 18px; outline: none;" id="downloadCancelBtn" title="Batalkan">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        <div style="font-size: 0.83rem; font-weight: 700; color: #1E293B; word-break: break-all; overflow-wrap: break-word; line-height: 1.3;" id="downloadFileNameText">nama_file.pdf</div>
        <div class="download-progress-container">
            <div class="download-progress-bar" id="downloadProgressBar" style="width: 0%"></div>
        </div>
        <div style="font-size: 0.73rem; color: #64748B; display: flex; justify-content: space-between;">
            <span id="downloadProgressPercent">0%</span>
            <span id="downloadProgressStats">0 KB/s - Sisa -- detik</span>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Fungsi penyaringan pencarian real-time
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
                const padZero = (n) => String(n).padStart(2, '0');
                const day = padZero(date.getDate());
                const months = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
                const formattedDate = day + " " + months[date.getMonth()] + " " + date.getFullYear();
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
                if (trimmedCat.length > 20) {
                    alert("Nama kategori tidak boleh lebih dari 20 karakter.");
                    return;
                }
                
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

        function handleFileUploadChange(input) {
            const errorFile = document.getElementById('error-file_upload');
            const dropZone = document.getElementById('dropZone');
            const nameInput = document.getElementById('namaFileDragInput');
            const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp'];

            if (input && input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                const fileExt = fileName.split('.').pop().toLowerCase();

                if (nameInput) nameInput.value = fileName;

                if (!allowedExtensions.includes(fileExt)) {
                    if (errorFile) {
                        errorFile.textContent = 'Format file tidak didukung. Hanya file PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, dan WEBP yang diizinkan.';
                        errorFile.style.display = 'block';
                    }
                    if (dropZone) dropZone.style.borderColor = '#EF4444';
                } else {
                    if (errorFile) errorFile.textContent = '';
                    if (dropZone) dropZone.style.borderColor = '#CBD5E1';
                }
            }
        }

        function validatePenyimpananUnggahForm(form) {
            let valid = true;
            const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp'];

            // 1. File Upload validation
            const fileInput = document.getElementById('fileUploadInput');
            const errorFile = document.getElementById('error-file_upload');
            const dropZone = document.getElementById('dropZone');

            if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                valid = false;
                if (errorFile) {
                    errorFile.textContent = 'File wajib diunggah.';
                    errorFile.style.display = 'block';
                }
                if (dropZone) dropZone.style.borderColor = '#EF4444';
            } else {
                const file = fileInput.files[0];
                const fileName = file.name;
                const fileExt = fileName.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExt)) {
                    valid = false;
                    if (errorFile) {
                        errorFile.textContent = 'Format file tidak didukung. Hanya file PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, dan WEBP yang diizinkan.';
                        errorFile.style.display = 'block';
                    }
                    if (dropZone) dropZone.style.borderColor = '#EF4444';
                } else {
                    if (errorFile) errorFile.textContent = '';
                    if (dropZone) dropZone.style.borderColor = '#CBD5E1';
                }
            }

            // 2. Kategori validation
            const katSelect = document.getElementById('uploadKategoriSelect');
            const errorKat = document.getElementById('error-uploadKategoriSelect');
            if (!katSelect || !katSelect.value || katSelect.value.trim() === '') {
                valid = false;
                if (errorKat) {
                    errorKat.textContent = 'Kategori wajib dipilih.';
                    errorKat.style.display = 'block';
                }
                if (katSelect) katSelect.style.borderColor = '#EF4444';
            } else {
                if (errorKat) errorKat.textContent = '';
                if (katSelect) katSelect.style.borderColor = '#F1F5F9';
            }

            // 3. Deskripsi validation
            const ketInput = document.getElementById('uploadKeteranganInput');
            const errorKet = document.getElementById('error-uploadKeteranganInput');
            if (!ketInput || !ketInput.value.trim()) {
                valid = false;
                if (errorKet) {
                    errorKet.textContent = 'Keterangan wajib diisi.';
                    errorKet.style.display = 'block';
                }
                if (ketInput) ketInput.style.borderColor = '#EF4444';
            } else {
                if (errorKet) errorKet.textContent = '';
                if (ketInput) ketInput.style.borderColor = '#F1F5F9';
            }

            return valid;
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

            if (ext === 'pdf') {
                window.open(url, '_blank');
            } else {
                // Fallback untuk DOC, DOCX, XLS, XLSX
                document.getElementById('previewModalTitle').innerText = fileName;
                document.getElementById('previewFallbackText').innerText = 'Pratinjau tidak tersedia untuk format berkas ini. Silakan unduh dokumen untuk melihat.';
                document.getElementById('previewFallbackMessage').style.display = 'block';

                document.getElementById('previewModal').classList.add('active');
            }
        }

        function closePreviewModal() {
            document.getElementById('previewModal').classList.remove('active');
        }

        function toggleDesc(el) {
            el.classList.toggle('collapsed');
            el.classList.toggle('expanded');
        }

        // Toast notifications logic
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast-alert');
            toasts.forEach((toast, idx) => {
                // Slide in with delay
                setTimeout(() => {
                    toast.style.transform = 'translateX(0)';
                    toast.style.opacity = '1';
                }, 100 + (idx * 200));

                // Auto dismiss after 4 seconds
                setTimeout(() => {
                    fadeAndRemoveToast(toast);
                }, 4000 + (idx * 500));
            });
        });

        function dismissToast(button) {
            const toast = button.closest('.toast-alert');
            if (toast) {
                fadeAndRemoveToast(toast);
            }
        }

        function fadeAndRemoveToast(toast) {
            toast.style.transform = 'translateX(120%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }

        // =========================================================================
        // --- FITUR TERBARU: AJAX UPLOAD & DOWNLOAD PROGRESS DENGAN INDIKATOR KECEPATAN ---
        // =========================================================================

        // Format Byte menjadi standar ukuran file (KB, MB, GB)
        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        // Format perkiraan sisa waktu download/upload
        function formatTimeRemaining(seconds) {
            if (seconds === Infinity || isNaN(seconds)) return 'sisa -- detik';
            if (seconds < 1) return 'sisa <1 detik';
            const s = Math.round(seconds);
            if (s >= 60) {
                const m = Math.floor(s / 60);
                const remS = s % 60;
                return `sisa ${m}m ${remS}s`;
            }
            return `sisa ${s} detik`;
        }

        // Tampilkan notifikasi toast dinamis lewat Javascript
        function showDynamicToast(type, message) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `toast-alert ${type}`;
            toast.style.cssText = `
                background-color: #FFFFFF;
                border: 1px solid #E2E8F0;
                color: #1E293B;
                padding: 16px 20px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 0.88rem;
                display: flex;
                align-items: center;
                gap: 12px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                transform: translateX(120%);
                opacity: 0;
                box-sizing: border-box;
                width: 100%;
            `;

            const iconSvg = type === 'success' ? 
                `<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><polyline points="20 6 9 17 4 12"></polyline></svg>` :
                `<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>`;

            toast.innerHTML = `
                ${iconSvg}
                <div style="flex: 1; line-height: 1.4; font-weight: 500; word-break: break-word; overflow-wrap: break-word;">
                    ${message}
                </div>
                <button onclick="dismissToast(this)" style="background: none; border: none; color: #94A3B8; cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center; outline: none;">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            }, 50);

            setTimeout(() => {
                fadeAndRemoveToast(toast);
            }, 4500);
        }

        // Cegah submit normal form upload & jalankan via AJAX
        document.getElementById('uploadFileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validatePenyimpananUnggahForm(this)) {
                return false;
            }

            closeUploadModal();
            performAjaxUpload(this);
        });

        // EKSEKUSI PROSES UPLOAD VIA AJAX (XHR)
        function performAjaxUpload(form) {
            const formData = new FormData(form);
            const fileInput = document.getElementById('fileUploadInput');
            const file = fileInput.files[0];
            if (!file) return;

            const originalName = document.getElementById('namaFileDragInput').value || file.name;
            const sizeStr = formatBytes(file.size);
            const categorySelect = document.getElementById('uploadKategoriSelect');
            const categoryName = categorySelect.options[categorySelect.selectedIndex].text;

            const filesTbody = document.getElementById('filesTbody');
            
            // Hapus baris 'belum ada file' jika tabel kosong
            const emptyRow = filesTbody.querySelector('tr td[colspan="6"]');
            if (emptyRow) {
                emptyRow.parentElement.remove();
            }

            // Sisipkan baris animasi loading upload di paling atas daftar tabel
            const tempRow = document.createElement('tr');
            tempRow.className = 'file-row uploading-row';
            tempRow.innerHTML = `
                <td>
                    <div style="display: flex; flex-direction: column; gap: 4px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="animate-spin" style="flex-shrink: 0;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <span style="font-weight: 700; color: #1E293B;" id="uploadingFileName">${originalName}</span>
                        </div>
                        <div class="upload-progress-container">
                            <div class="upload-progress-bar" id="uploadingProgressBar" style="width: 0%"></div>
                        </div>
                        <div style="font-size: 0.75rem; color: #64748B; display: flex; justify-content: space-between; margin-top: 2px;">
                            <span id="uploadingProgressPercent">0%</span>
                            <span id="uploadingProgressStats">0 KB/s - Sisa -- detik</span>
                        </div>
                    </div>
                </td>
                <td><span class="category-item-badge">${categoryName}</span></td>
                <td style="color: #64748B;">Sedang mengunggah berkas...</td>
                <td>${sizeStr}</td>
                <td style="color: #64748B;">Saat ini</td>
                <td style="text-align: center;">
                    <button type="button" class="btn-icon-action" style="cursor: not-allowed;" title="Sedang mengunggah">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="#94A3B8" stroke-width="2.5" class="animate-spin">
                            <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>
                        </svg>
                    </button>
                </td>
            `;
            filesTbody.insertBefore(tempRow, filesTbody.firstChild);

            // AJAX Request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action, true);
            xhr.setRequestHeader('Accept', 'application/json');

            // Header CSRF
            const csrfToken = document.querySelector('input[name="_token"]').value;
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            const startTime = Date.now();

            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable) {
                    const loaded = event.loaded;
                    const total = event.total;
                    const percent = Math.round((loaded / total) * 100);

                    // Hitung kecepatan transfer rata-rata (Bytes/detik)
                    const elapsedTime = (Date.now() - startTime) / 1000;
                    let speed = 0;
                    let speedStr = '0 KB/s';
                    if (elapsedTime > 0) {
                        speed = loaded / elapsedTime;
                        speedStr = formatBytes(speed) + '/s';
                    }

                    // Sisa waktu upload
                    const remainingBytes = total - loaded;
                    const timeRemaining = speed > 0 ? (remainingBytes / speed) : Infinity;
                    const timeStr = formatTimeRemaining(timeRemaining);

                    // Update UI baris upload
                    document.getElementById('uploadingProgressBar').style.width = percent + '%';
                    document.getElementById('uploadingProgressPercent').textContent = percent + '%';
                    const elapsedStr = Math.round(elapsedTime) + 's';
                    document.getElementById('uploadingProgressStats').textContent = `${speedStr} - ${elapsedStr} berlalu (${timeStr})`;
                }
            };

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        document.getElementById('uploadingProgressBar').style.backgroundColor = '#10B981';
                        document.getElementById('uploadingProgressStats').textContent = 'Selesai diunggah. Menyelaraskan halaman...';
                        
                        showDynamicToast('success', `Berkas "${originalName}" berhasil diunggah ke server NAS.`);
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } catch (err) {
                        handleUploadFailure('Respon dari server tidak valid.');
                    }
                } else {
                    let errMsg = 'Terjadi kesalahan tidak dikenal saat mengunggah.';
                    try {
                        const res = JSON.parse(xhr.responseText);
                        errMsg = res.message || errMsg;
                    } catch (e) {}
                    handleUploadFailure(errMsg);
                }
            };

            xhr.onerror = function() {
                handleUploadFailure('Koneksi jaringan terputus.');
            };

            xhr.send(formData);

            function handleUploadFailure(msg) {
                tempRow.remove();
                if (filesTbody.children.length === 0) {
                    const fallbackRow = document.createElement('tr');
                    fallbackRow.innerHTML = `
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94A3B8; font-weight: 500;">
                            Belum ada file terarsip dalam kategori ini.
                        </td>
                    `;
                    filesTbody.appendChild(fallbackRow);
                }
                showDynamicToast('error', `Gagal mengunggah file: ${msg}`);
                form.reset();
                resetFileDragZone();
            }
        }

        // Bersihkan zona drop file
        function resetFileDragZone() {
            document.getElementById('fileUploadInput').value = '';
            document.getElementById('namaFileDragInput').value = '';
            document.getElementById('namaFileDragInput').style.display = 'none';
            const dropZone = document.getElementById('dropZone');
            if (dropZone) {
                dropZone.innerHTML = `
                    <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <div style="font-weight: 600; color: #64748B; font-size: 0.88rem; margin-top: 4px;">Klik atau seret file ke sini</div>
                    <div style="color: #94A3B8; font-size: 0.78rem;">PDF, DOCX, XLSX, JPG, PNG, dsb.</div>
                    <input type="file" name="file_upload" id="fileUploadInput" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp" required style="display: none;" onchange="handleFileUploadChange(this)">
                    <input type="text" name="nama_file" id="namaFileDragInput" placeholder="Nama file (mis. dokumen.pdf)" maxlength="150" onclick="event.stopPropagation();" onmousedown="event.stopPropagation();" style="display: none; width: 80%; padding: 8px 12px; margin-top: 10px; border: 1.5px solid #E2E8F0; border-radius: 8px; font-size: 0.85rem; outline: none; text-align: center; color: #1E293B; font-weight: 600; background: #FAFAFA;">
                    <div id="error-fileUploadInput" class="custom-invalid-feedback" style="display: none; margin-top: 4px;"></div>
                `;
            }
        }

        // EKSEKUSI PROSES DOWNLOAD VIA AJAX DENGAN PROGRESS PANEL FLOATING
        let activeDownloadXhr = null;

        function triggerSecureDownload(event, url, fileName) {
            event.preventDefault();

            // Batalkan unduhan aktif jika ada
            if (activeDownloadXhr) {
                activeDownloadXhr.abort();
            }

            const panel = document.getElementById('downloadProgressPanel');
            const title = document.getElementById('downloadPanelTitle');
            const fileNameText = document.getElementById('downloadFileNameText');
            const pBar = document.getElementById('downloadProgressBar');
            const pPercent = document.getElementById('downloadProgressPercent');
            const pStats = document.getElementById('downloadProgressStats');
            const cancelBtn = document.getElementById('downloadCancelBtn');
            const icon = document.getElementById('downloadingIcon');

            // Reset status tampilan panel
            title.textContent = 'Mengunduh Berkas...';
            fileNameText.textContent = fileName;
            pBar.style.width = '0%';
            pBar.style.backgroundColor = '#3B82F6';
            pPercent.textContent = '0%';
            pStats.textContent = 'Menghubungkan ke server NAS...';
            cancelBtn.style.display = 'flex';
            icon.className = 'animate-spin';
            icon.innerHTML = '<path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"></path>';
            icon.setAttribute('stroke', '#3B82F6');

            // Tampilkan Panel Progress (Slide up)
            panel.classList.add('active');

            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.responseType = 'blob';
            activeDownloadXhr = xhr;

            const startTime = Date.now();

            xhr.onprogress = function(e) {
                if (e.lengthComputable) {
                    const loaded = e.loaded;
                    const total = e.total;
                    const percent = Math.round((loaded / total) * 100);

                    // Hitung Kecepatan Download
                    const elapsedTime = (Date.now() - startTime) / 1000;
                    let speed = 0;
                    let speedStr = '0 KB/s';
                    if (elapsedTime > 0) {
                        speed = loaded / elapsedTime;
                        speedStr = formatBytes(speed) + '/s';
                    }

                    // Sisa Waktu Download
                    const remainingBytes = total - loaded;
                    const timeRemaining = speed > 0 ? (remainingBytes / speed) : Infinity;
                    const timeStr = formatTimeRemaining(timeRemaining);

                    // Update UI Panel
                    pBar.style.width = percent + '%';
                    pPercent.textContent = percent + '%';
                    const elapsedStr = Math.round(elapsedTime) + 's';
                    pStats.textContent = `${speedStr} - ${elapsedStr} berlalu (${timeStr})`;
                } else {
                    const elapsedTime = (Date.now() - startTime) / 1000;
                    const loadedStr = formatBytes(e.loaded);
                    pStats.textContent = `Mengunduh: ${loadedStr} (${Math.round(elapsedTime)}s)`;
                }
            };

            xhr.onload = function() {
                if (xhr.status === 200) {
                    const blob = xhr.response;
                    const downloadUrl = window.URL.createObjectURL(blob);
                    
                    // Simulasikan anchor tag untuk menyimpan berkas
                    const a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = fileName;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(downloadUrl);

                    // Update UI Sukses
                    title.textContent = 'Unduhan Selesai!';
                    pBar.style.width = '100%';
                    pBar.style.backgroundColor = '#10B981';
                    pPercent.textContent = '100%';
                    
                    const elapsedTime = (Date.now() - startTime) / 1000;
                    const avgSpeed = blob.size / (elapsedTime > 0 ? elapsedTime : 1);
                    pStats.textContent = `Tuntas dalam ${Math.round(elapsedTime)} dtk (${formatBytes(avgSpeed)}/s)`;
                    
                    cancelBtn.style.display = 'none';
                    icon.className = '';
                    icon.setAttribute('stroke', '#10B981');
                    icon.innerHTML = '<polyline points="20 6 9 17 4 12"></polyline>';

                    activeDownloadXhr = null;

                    // Hilangkan panel otomatis setelah 3 detik
                    setTimeout(() => {
                        panel.classList.remove('active');
                    }, 3500);
                } else {
                    handleDownloadFailure('Berkas gagal diunduh.');
                }
            };

            xhr.onerror = function() {
                handleDownloadFailure('Masalah koneksi jaringan.');
            };

            xhr.send();

            function handleDownloadFailure(msg) {
                title.textContent = 'Gagal Mengunduh';
                pStats.textContent = msg;
                pBar.style.backgroundColor = '#EF4444';
                icon.className = '';
                icon.setAttribute('stroke', '#EF4444');
                icon.innerHTML = '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>';
                cancelBtn.style.display = 'none';
                activeDownloadXhr = null;

                setTimeout(() => {
                    panel.classList.remove('active');
                }, 4000);
            }
        }

        // Pembatalan proses download aktif
        function cancelDownload() {
            if (activeDownloadXhr) {
                activeDownloadXhr.abort();
                activeDownloadXhr = null;
                document.getElementById('downloadProgressPanel').classList.remove('active');
                showDynamicToast('error', 'Proses unduhan dibatalkan.');
            }
        }
    </script>
@endsection
