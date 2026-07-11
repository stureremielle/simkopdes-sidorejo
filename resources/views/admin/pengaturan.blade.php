@extends('layouts.admin')

@section('title', 'Pengaturan - Panel Admin')

@section('styles')
    <style>
        .settings-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .settings-tab {
            padding: 10px 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.88rem;
            color: #64748B;
            border-radius: 8px;
            border: 1px solid transparent;
            background: transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .settings-tab.active {
            background: #FFFFFF;
            border-color: #F1F5F9;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            color: #DC2626;
            font-weight: 700;
        }
        .settings-panel {
            display: none;
        }
        .settings-panel.active {
            display: block;
        }
        .settings-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .settings-card h3 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0F172A;
            margin: 0 0 20px;
        }
        .s-field {
            margin-bottom: 16px;
        }
        .s-field label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }
        .s-field input, .s-field textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #F1F5F9;
            border-radius: 8px;
            font-size: 0.9rem;
            outline: none;
            box-sizing: border-box;
            background-color: #FAFAFA;
            color: #1E293B;
            transition: all 0.2s;
        }
        .s-field input:focus, .s-field textarea:focus {
            border-color: #DC2626;
            background-color: #FFFFFF;
        }
        .btn-save-settings {
            background: #DC2626;
            color: white;
            border: none;
            padding: 11px 24px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-save-settings:hover {
            background: #B91C1C;
        }
        #mission-list-container::-webkit-scrollbar {
            width: 8px;
        }
        #mission-list-container::-webkit-scrollbar-track {
            background: #FAFAFA;
            border-radius: 4px;
        }
        #mission-list-container::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }
        #mission-list-container::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>
@endsection

@section('content')
    <h1 style="font-weight: 800; font-size: 1.8rem; color: #0F172A; margin: 0 0 24px 0;">Pengaturan</h1>

    <!-- Tabs -->
    <div class="settings-tabs">
        <div class="settings-tab active" data-tab="informasi" onclick="showTab('informasi')">Informasi Koperasi</div>
        <div class="settings-tab" data-tab="background" onclick="showTab('background')">Background Beranda</div>
        <div class="settings-tab" data-tab="struktur" onclick="showTab('struktur')">Struktur Organisasi</div>
        <div class="settings-tab" data-tab="visi-misi" onclick="showTab('visi-misi')">Visi &amp; Misi</div>
    </div>

    <!-- Panel: Informasi Koperasi -->
    <div class="settings-panel active" id="tab-informasi">
        <form method="POST" action="{{ route('admin.pengaturan.save') }}">
            @csrf
            <div class="settings-card">
                <div class="s-field">
                    <label>Nama Koperasi</label>
                    <input type="text" name="nama_koperasi" value="{{ $settings['nama_koperasi'] ?? '' }}" required placeholder="Koperasi Desa Merah Putih Sidorejo">
                </div>
                <div class="s-field">
                    <label>No. Telepon</label>
                    <input type="text" name="telepon" value="{{ $settings['telepon'] ?? '' }}" placeholder="+62 812 3456 7890">
                </div>
                <div class="s-field">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $settings['email'] ?? '' }}" placeholder="info@merahputih.co.id">
                </div>
                <div class="s-field">
                    <label>Alamat</label>
                    <input type="text" name="alamat" value="{{ $settings['alamat'] ?? '' }}" placeholder="Jl. Pariwisata RT 04 Dusun II Desa Sidorejo, Kecamatan Penajam">
                </div>
            </div>

            <div class="settings-card">
                <h3>Ubah Password</h3>
                <div class="s-field">
                    <input type="password" name="password_lama" placeholder="Password Lama">
                    @error('password_lama')
                        <div style="color: #DC2626; font-size: 0.78rem; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="s-field">
                    <input type="password" name="password_baru" placeholder="Password baru">
                    @error('password_baru')
                        <div style="color: #DC2626; font-size: 0.78rem; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="s-field">
                    <input type="password" name="password_konfirmasi" placeholder="Konfirmasi Password">
                    @error('password_konfirmasi')
                        <div style="color: #DC2626; font-size: 0.78rem; margin-top: 4px; font-weight: 600;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <input type="hidden" name="visi" value="{{ $settings['visi'] ?? '' }}">
            <input type="hidden" name="misi" value="{{ $settings['misi'] ?? '' }}">

            <div style="display: flex; justify-content: center; margin-top: 16px;"><button type="submit" style="width: 100%; padding: 14px 24px; background: #DC2626; color: #ffffff; border: none; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s; letter-spacing: 0.3px;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Simpan Perubahan</button></div>
            @if (session('success'))
            <div style="display:flex;align-items:center;gap:6px;justify-content:center;margin-top:10px;color:#16A34A;font-size:0.85rem;font-weight:600;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Tersimpan!
            </div>
            @endif
        </form>
    </div>



    <div class="settings-panel" id="tab-background">
        <form method="POST" action="{{ route('admin.pengaturan.hero') }}" enctype="multipart/form-data">
            @csrf
            <div class="settings-card">
                <p style="color: #64748B; font-size: 0.88rem; margin: 0 0 20px 0;">Ganti foto latar belakang pada bagian hero di halaman Beranda. Gunakan foto landscape beresolusi tinggi (min. 1920&times;1080).</p>
                
                <!-- Preview Area -->
                <div style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 12px; height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; margin-bottom: 20px; position: relative; overflow: hidden;">
                    @php
                        $heroBg = \App\Models\Pengaturan::getValue('hero_background', '');
                    @endphp
                    @if ($heroBg)
                        @php
                            $heroBgParsed = str_starts_with($heroBg, 'http') ? $heroBg : asset('uploads/' . $heroBg);
                        @endphp
                        <img src="{{ $heroBgParsed }}" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; opacity: 0.9;">
                        <div style="position: absolute; bottom: 12px; right: 12px; background: rgba(15, 23, 42, 0.8); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                            Aktif
                        </div>
                    @else
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <div style="font-weight: 500; color: #94A3B8; font-size: 0.88rem;">Menggunakan foto default</div>
                    @endif
                </div>

                <!-- Toggle Mode buttons -->
                <div style="display: flex; gap: 8px; margin-bottom: 20px;">
                    <button type="button" id="btn-toggle-upload" onclick="switchHeroMode('upload')" class="btn-toggle-hero active" style="padding: 8px 16px; font-size: 0.85rem; font-weight: 600; border-radius: 9999px; border: 1.5px solid transparent; cursor: pointer; transition: all 0.2s; background: #DC2626; color: #FFFFFF;">
                        Unggah File
                    </button>
                    <button type="button" id="btn-toggle-url" onclick="switchHeroMode('url')" class="btn-toggle-hero" style="padding: 8px 16px; font-size: 0.85rem; font-weight: 600; border-radius: 9999px; border: 1.5px solid #F1F5F9; cursor: pointer; transition: all 0.2s; background: #FAFAFA; color: #475569;">
                        Gunakan URL
                    </button>
                </div>

                <!-- Mode 1: Drag & Drop/Select Box -->
                <div id="hero-upload-container" onclick="document.getElementById('heroFileInput').click()" style="border: 2px dashed #CBD5E1; border-radius: 12px; padding: 32px 16px; text-align: center; background: #FFFFFF; cursor: pointer; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px;">
                    <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <div style="font-weight: 600; color: #64748B; font-size: 0.88rem; margin-top: 4px;" id="heroFileNameText">Klik untuk pilih foto</div>
                    <div style="color: #cbd5e1; font-size: 0.75rem;">JPG, PNG, WebP (maks. 5 MB)</div>
                    <input type="file" name="hero_upload" id="heroFileInput" style="display: none;" onchange="handleHeroChange(this)">
                </div>

                <!-- Mode 2: Input URL -->
                <div id="hero-url-container" style="display: none;">
                    <div class="s-field" style="margin: 0;">
                        <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">URL Gambar</label>
                        <input type="text" name="hero_url" id="heroUrlInput" placeholder="Masukkan URL gambar landscape..." style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%;">
                    </div>
                </div>


            </div>

            <div style="display: flex; gap: 16px; margin-top: 16px; width: 100%;">
                <button type="submit" style="flex: 1; padding: 14px 24px; background: #DC2626; color: #ffffff; border: none; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s; letter-spacing: 0.3px;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Simpan Perubahan</button>
                <button type="submit" name="action" value="reset" style="flex: 1; padding: 14px 24px; background: #ffffff; color: #334155; border: 1.5px solid #E2E8F0; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.2s; letter-spacing: 0.3px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#ffffff'">Kembalikan ke Default</button>
            </div>
            @if (session('success_hero'))
            <div style="display:flex;align-items:center;gap:6px;justify-content:center;margin-top:10px;color:#DC2626;font-size:0.85rem;font-weight:600;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Tersimpan!
            </div>
            @endif

        </form>
    </div>

    <!-- Panel: Struktur Organisasi -->
    <div class="settings-panel" id="tab-struktur">
        <form method="POST" action="{{ route('admin.pengaturan.org_chart') }}" enctype="multipart/form-data">
            @csrf
            
            <!-- Alert/Instruction Info Box -->
            <div style="background: #FFFDF5; border: 1px solid #FDE68A; border-radius: 12px; padding: 14px 18px; display: flex; gap: 10px; margin-bottom: 24px; align-items: flex-start;">
                <span style="color: #D97706; display: flex; align-items: center; justify-content: center; margin-top: 1px;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                    </svg>
                </span>
                <div>
                    <h4 style="color: #9A3412; font-weight: 700; margin: 0 0 4px 0; font-size: 0.88rem;">Upload Bagan Struktur Organisasi</h4>
                    <p style="color: #B45309; margin: 0; font-size: 0.78rem; line-height: 1.5;">
                        Upload gambar bagan org chart secara keseluruhan. Gambar ini langsung ditampilkan di halaman Tentang Kami. Saat struktur berubah, cukup upload gambar baru. <br>
                        Buat bagan di Canva, PowerPoint, atau Figma, lalu export sebagai gambar.
                    </p>
                </div>
            </div>

            <!-- Preview Card Area -->
            <div style="background: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 16px; padding: 24px; margin-bottom: 20px;">
                <!-- Inner Image Preview Container -->
                <div style="background: #FAFAFA; border: 1.5px solid #E2E8F0; border-radius: 12px; height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; margin-bottom: 20px; position: relative; overflow: hidden;">
                    @php
                        $orgChart = \App\Models\Pengaturan::getValue('org_chart', '');
                    @endphp
                    @if ($orgChart)
                        @php
                            $orgChartParsed = str_starts_with($orgChart, 'http') ? $orgChart : asset('uploads/' . $orgChart);
                        @endphp
                        <img src="{{ $orgChartParsed }}" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit:contain; background: #FFFFFF; opacity: 0.95;">
                        <div style="position: absolute; bottom: 12px; right: 12px; background: rgba(15, 23, 42, 0.8); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                            Aktif
                        </div>
                    @else
                        <!-- Mockup Connector Icon -->
                        <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 4px;">
                            <path d="M12 2v8M12 10a4 4 0 0 0-4 4v8M12 10a4 4 0 0 1 4 4v8"></path>
                            <circle cx="12" cy="2" r="1" fill="#94A3B8"></circle>
                            <circle cx="8" cy="22" r="1" fill="#94A3B8"></circle>
                            <circle cx="16" cy="22" r="1" fill="#94A3B8"></circle>
                        </svg>
                        <div style="font-weight: 600; color: #64748B; font-size: 0.9rem;" id="orgPreviewTitle">Belum ada gambar struktur organisasi</div>
                        <div style="color: #94A3B8; font-size: 0.8rem;" id="orgPreviewSubtitle">Upload atau gunakan URL di bawah ini</div>
                    @endif
                </div>

                <!-- Toggle Buttons Row -->
                <div style="display: flex; gap: 24px; align-items: center; margin-bottom: 20px;">
                    <button type="button" id="btn-org-upload" onclick="switchOrgMode('upload')" style="background: #DC2626; color: #FFFFFF; padding: 8px 18px; border-radius: 9999px; border: none; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                        Unggah File
                    </button>
                    <button type="button" id="btn-org-url" onclick="switchOrgMode('url')" style="background: none; color: #475569; border: none; font-size: 0.88rem; font-weight: 600; cursor: pointer; transition: all 0.2s; padding: 8px 0;">
                        Gunakan URL
                    </button>
                </div>

                <!-- Unggah File Container -->
                <div id="org-upload-container" onclick="document.getElementById('orgFileInput').click()" style="border: 2px dashed #E2E8F0; border-radius: 12px; padding: 40px 20px; text-align: center; background: #FFFFFF; cursor: pointer; transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px;">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#94A3B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <div style="font-weight: 600; color: #475569; font-size: 0.88rem;" id="orgFileNameText">Klik untuk pilih gambar bagan</div>
                    <div style="color: #94A3B8; font-size: 0.75rem;">JPG, PNG, WebP &mdash; orientasi landscape disarankan</div>
                    <input type="file" name="org_upload" id="orgFileInput" style="display: none;" onchange="handleOrgChange(this)">
                </div>

                <!-- Gunakan URL Container -->
                <div id="org-url-container" style="display: none;">
                    <div class="s-field" style="margin: 0;">
                        <label style="font-size: 0.88rem; font-weight: 600; color: #334155; display: block; margin-bottom: 6px;">URL Gambar Struktur</label>
                        <input type="text" name="org_url" id="orgUrlInput" placeholder="Masukkan URL gambar bagan struktur..." style="padding: 10px 14px; border: 1.5px solid #F1F5F9; border-radius: 8px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; width: 100%;">
                    </div>
                </div>


            </div>

            <div style="display: flex; gap: 16px; margin-top: 16px; width: 100%;">
                <button type="submit" style="flex: 1; padding: 14px 24px; background: #DC2626; color: #ffffff; border: none; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s; letter-spacing: 0.3px;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Simpan Perubahan</button>
                <button type="submit" name="action" value="reset" style="flex: 1; padding: 14px 24px; background: #ffffff; color: #334155; border: 1.5px solid #E2E8F0; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.2s; letter-spacing: 0.3px;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='#ffffff'">Kembalikan ke Default</button>
            </div>
            @if (session('success_org'))
            <div style="display:flex;align-items:center;gap:6px;justify-content:center;margin-top:10px;color:#DC2626;font-size:0.85rem;font-weight:600;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Tersimpan!
            </div>
            @endif

        </form>
    </div>

    <!-- Panel: Visi & Misi -->
    <div class="settings-panel block" id="tab-visi-misi">
        <form method="POST" action="{{ route('admin.pengaturan.save') }}">
            @csrf
            <input type="hidden" name="nama_koperasi" value="{{ $settings['nama_koperasi'] ?? '' }}">
            <input type="hidden" name="alamat" value="{{ $settings['alamat'] ?? '' }}">
            <input type="hidden" name="telepon" value="{{ $settings['telepon'] ?? '' }}">
            <input type="hidden" name="email" value="{{ $settings['email'] ?? '' }}">
            
            <!-- Hidden input to hold serialized mission points -->
            <input type="hidden" name="misi" id="misiHiddenInput" value="{{ $settings['misi'] ?? '' }}">

            <!-- Visi 2025 Card -->
            <div class="settings-card" style="background: #FFFFFF; border: 1.5px solid #F1F5F9; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: none;">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0 0 20px 0; display: flex; align-items: center;">
                    <span style="width: 8px; height: 8px; background: #DC2626; border-radius: 50%; display: inline-block; margin-right: 10px;"></span>
                    Visi 2025
                </h3>
                <textarea name="visi" rows="4" style="font-family: inherit; background-color: #FAFAFA; border: 1.5px solid #F1F5F9; border-radius: 12px; font-size: 0.9rem; min-height: 90px; resize: vertical; padding: 20px; line-height: 1.6; color: #475569; width: 100%; box-sizing: border-box; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#DC2626'">{{ $settings['visi'] ?? '' }}</textarea>
            </div>
            
            <!-- Misi 2025 Card -->
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0 0 20px 0; display: flex; align-items: center;">
                    <span style="width: 8px; height: 8px; background: #F59E0B; border-radius: 50%; display: inline-block; margin-right: 10px;"></span>
                    Misi 2025
                </h3>

                <!-- Interactive card list container -->
                <div id="mission-list-container">
                    <!-- Loaded dynamically via JavaScript as nested cards -->
                </div>

                <!-- Add new mission point controls -->
                <div style="display: flex; gap: 12px; align-items: center; margin-top: 16px; background: #FFFFFF; border: 1.5px solid #F1F5F9; border-radius: 16px; padding: 24px;">
                    <input type="text" id="new-mission-input" placeholder="Tambah poin misi baru..." style="flex: 1; padding: 14px 20px; border: 1.5px solid #F1F5F9; border-radius: 12px; font-size: 0.9rem; outline: none; background: #FAFAFA; box-sizing: border-box; color: #475569; transition: border-color 0.2s;" onfocus="this.style.borderColor='#DC2626'" onkeypress="if(event.key === 'Enter'){ event.preventDefault(); addMissionItem(); }">
                    <button type="button" onclick="addMissionItem()" style="background: #DC2626; color: #FFFFFF; border: none; padding: 14px 24px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.2s; height: 48px; border: 1.5px solid transparent;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">
                        + Tambah
                    </button>
                </div>
            </div>
            
            <div style="display: flex; justify-content: center; margin-top: 24px; width: 100%;">
                <button type="submit" style="width: 100%; padding: 14px 24px; background: #DC2626; color: #ffffff; border: none; border-radius: 12px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: background 0.2s; letter-spacing: 0.3px;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">Simpan Perubahan</button>
            </div>
            @if (session('success'))
            <div style="display:flex;align-items:center;gap:6px;justify-content:center;margin-top:10px;color:#16A34A;font-size:0.85rem;font-weight:600;">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Tersimpan!
            </div>
            @endif
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        function showTab(id) {
            document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.settings-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + id).classList.add('active');
            
            // Find active tab header and set active class
            document.querySelectorAll('.settings-tab').forEach(t => {
                if (t.getAttribute('data-tab') === id) {
                    t.classList.add('active');
                }
            });
        }

        function switchHeroMode(mode) {
            const btnUpload = document.getElementById('btn-toggle-upload');
            const btnUrl = document.getElementById('btn-toggle-url');
            const uploadCont = document.getElementById('hero-upload-container');
            const urlCont = document.getElementById('hero-url-container');

            if (mode === 'upload') {
                btnUpload.style.background = '#DC2626';
                btnUpload.style.color = '#FFFFFF';
                btnUpload.style.borderColor = 'transparent';

                btnUrl.style.background = '#FAFAFA';
                btnUrl.style.color = '#475569';
                btnUrl.style.borderColor = '#F1F5F9';

                uploadCont.style.display = 'flex';
                urlCont.style.display = 'none';
                document.getElementById('heroUrlInput').value = '';
            } else {
                btnUrl.style.background = '#DC2626';
                btnUrl.style.color = '#FFFFFF';
                btnUrl.style.borderColor = 'transparent';

                btnUpload.style.background = '#FAFAFA';
                btnUpload.style.color = '#475569';
                btnUpload.style.borderColor = '#F1F5F9';

                uploadCont.style.display = 'none';
                urlCont.style.display = 'block';
                document.getElementById('heroFileInput').value = '';
                document.getElementById('heroFileNameText').textContent = 'Klik untuk pilih foto';
            }
        }

        function handleHeroChange(input) {
            if (input.files && input.files[0]) {
                document.getElementById('heroFileNameText').textContent = input.files[0].name;
            } else {
                document.getElementById('heroFileNameText').textContent = 'Klik untuk pilih foto';
            }
        }

        function switchOrgMode(mode) {
            const btnUpload = document.getElementById('btn-org-upload');
            const btnUrl = document.getElementById('btn-org-url');
            const uploadCont = document.getElementById('org-upload-container');
            const urlCont = document.getElementById('org-url-container');

            if (mode === 'upload') {
                btnUpload.style.background = '#DC2626';
                btnUpload.style.color = '#FFFFFF';
                btnUpload.style.padding = '8px 18px';
                btnUpload.style.borderRadius = '9999px';

                btnUrl.style.background = 'none';
                btnUrl.style.color = '#475569';
                btnUrl.style.padding = '8px 0';
                btnUrl.style.borderRadius = '0';

                uploadCont.style.display = 'flex';
                urlCont.style.display = 'none';
                document.getElementById('orgUrlInput').value = '';
            } else {
                btnUrl.style.background = '#DC2626';
                btnUrl.style.color = '#FFFFFF';
                btnUrl.style.padding = '8px 18px';
                btnUrl.style.borderRadius = '9999px';

                btnUpload.style.background = 'none';
                btnUpload.style.color = '#475569';
                btnUpload.style.padding = '8px 0';
                btnUpload.style.borderRadius = '0';

                uploadCont.style.display = 'none';
                urlCont.style.display = 'block';
                document.getElementById('orgFileInput').value = '';
                document.getElementById('orgFileNameText').textContent = 'Klik untuk pilih gambar bagan';
            }
        }

        function handleOrgChange(input) {
            if (input.files && input.files[0]) {
                document.getElementById('orgFileNameText').textContent = input.files[0].name;
            } else {
                document.getElementById('orgFileNameText').textContent = 'Klik untuk pilih gambar bagan';
            }
        }

        // Initialize mission list from PHP. If empty, start with empty array.
        let missionList = [];
        @php
            $misiJson = '[]';
            if (!empty($settings['misi'])) {
                $decoded = json_decode($settings['misi'], true);
                if (is_array($decoded)) {
                    $misiJson = $settings['misi'];
                } else {
                    $oldMisi = array_filter(array_map('trim', explode('|', $settings['misi'])));
                    $structured = [];
                    foreach ($oldMisi as $m) {
                        $structured[] = [
                            'title' => $m,
                            'items' => []
                        ];
                    }
                    $misiJson = json_encode($structured);
                }
            }
        @endphp
        missionList = {!! $misiJson !!};

        function renderMissionList() {
            const container = document.getElementById('mission-list-container');
            container.innerHTML = '';

            if (missionList.length === 0) {
                container.innerHTML = `
                    <div style="background: #FAFAFA; border: 1.5px dashed #F1F5F9; border-radius: 12px; padding: 20px; text-align: center; color: #94A3B8; font-size: 0.88rem;">
                        Belum ada poin misi. Tambahkan poin misi baru di bawah.
                    </div>
                `;
                document.getElementById('misiHiddenInput').value = '';
                return;
            }

            missionList.forEach((mission, index) => {
                const card = document.createElement('div');
                card.className = 'settings-card';
                card.style.background = '#FFFFFF';
                card.style.border = '1.5px solid #F1F5F9';
                card.style.borderRadius = '16px';
                card.style.padding = '24px';
                card.style.marginBottom = '24px';
                card.style.boxShadow = 'none';
                card.style.position = 'relative';

                // Sub-items HTML
                let subItemsHtml = '';
                if (mission.items && mission.items.length > 0) {
                    mission.items.forEach((subPoint, subIndex) => {
                        subItemsHtml += `
                            <li class="misi-item" style="display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; list-style: none;">
                                <span class="check-icon-wrapper" style="display: flex; align-items: center; justify-content: center; width: 18px; height: 18px; border-radius: 50%; border: 1.5px solid #DC2626; color: #DC2626; flex-shrink: 0; margin-top: 2px;">
                                    <svg viewBox="0 0 24 24" width="10" height="10" fill="none" stroke="currentColor" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </span>
                                <span class="misi-text-content" style="color: #475569; font-size: 0.92rem; line-height: 1.5; flex: 1;">${escapeHtml(subPoint)}</span>
                                <button type="button" onclick="deleteSubPoint(${index}, ${subIndex})" style="background: none; border: none; color: #CBD5E1; cursor: pointer; padding: 2px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#CBD5E1'">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </li>
                        `;
                    });
                } else {
                    subItemsHtml = `
                        <div style="background: #FAFAFA; border: 1.5px dashed #F1F5F9; border-radius: 12px; padding: 14px; text-align: center; color: #94A3B8; font-size: 0.82rem; margin-bottom: 14px;">
                            Belum ada sub-poin untuk misi ini.
                        </div>
                    `;
                }

                card.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: #1E293B; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span style="color: #DC2626;">${index + 1}.</span>
                            <span>${escapeHtml(mission.title)}</span>
                        </h4>
                        <button type="button" onclick="deleteMissionItem(${index})" style="background: none; border: none; color: #CBD5E1; cursor: pointer; padding: 4px; display: flex; align-items: center; justify-content: center; transition: color 0.2s;" onmouseover="this.style.color='#EF4444'" onmouseout="this.style.color='#CBD5E1'">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>

                    <ul class="misi-list" style="padding: 0; margin: 0 0 20px 0;">
                        ${subItemsHtml}
                    </ul>

                    <!-- Input block inside card -->
                    <div style="display: flex; gap: 12px; align-items: center; margin-top: 14px;">
                        <input type="text" id="new-subpoint-input-${index}" placeholder="Tambah sub-poin misi baru..." style="flex: 1; padding: 12px 18px; border: 1.5px solid #F1F5F9; border-radius: 12px; font-size: 0.88rem; outline: none; background: #FAFAFA; box-sizing: border-box; color: #475569; transition: border-color 0.2s;" onfocus="this.style.borderColor='#DC2626'" onkeypress="if(event.key === 'Enter'){ event.preventDefault(); addSubPoint(${index}); }">
                        <button type="button" onclick="addSubPoint(${index})" style="background: #DC2626; color: #FFFFFF; border: none; padding: 12px 20px; border-radius: 12px; font-size: 0.88rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.2s; height: 44px; border: 1.5px solid transparent;" onmouseover="this.style.background='#B91C1C'" onmouseout="this.style.background='#DC2626'">
                            + Tambah
                        </button>
                    </div>
                `;
                container.appendChild(card);
            });

            // Update serialized hidden value for form submission
            document.getElementById('misiHiddenInput').value = JSON.stringify(missionList);
        }

        function addMissionItem() {
            const input = document.getElementById('new-mission-input');
            const val = input.value.trim();
            if (val) {
                missionList.push({
                    title: val,
                    items: []
                });
                input.value = '';
                renderMissionList();
            }
        }

        function deleteMissionItem(index) {
            missionList.splice(index, 1);
            renderMissionList();
        }

        function addSubPoint(index) {
            const input = document.getElementById('new-subpoint-input-' + index);
            const val = input.value.trim();
            if (val) {
                if (!missionList[index].items) {
                    missionList[index].items = [];
                }
                missionList[index].items.push(val);
                input.value = '';
                renderMissionList();
            }
        }

        function deleteSubPoint(index, subIndex) {
            missionList[index].items.splice(subIndex, 1);
            renderMissionList();
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Initial render on page load
        document.addEventListener('DOMContentLoaded', () => {
            renderMissionList();

        });

    </script>
@endsection



