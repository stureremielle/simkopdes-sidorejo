@extends('layouts.admin')

@section('title', 'Data Anggota')
@section('breadcrumb', 'Data anggota')

@section('content')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/anggota.css') }}?v={{ filemtime(public_path('assets/css/admin/anggota.css')) }}">
@endsection

    <!-- Seksi Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 class="page-title" style="margin: 0 0 4px 0;">Data Anggota</h1>
            <p style="margin: 0; color: #64748B; font-size: 0.9rem; font-weight: 500;">Kelola anggota koperasi dan verifikasi pendaftaran</p>
        </div>
        <button onclick="openTambahModal()" class="btn-green-action">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Tambah Anggota
        </button>
    </div>



    <!-- 4 Kartu Matriks Statistik -->
    <div class="stats-grid">
        <!-- Kartu 1: Total Anggota -->
        <div class="stat-card">
            <span class="stat-card-title">Total Anggota</span>
            <span class="stat-card-number" style="color: #0F172A;">{{ $statTotal }}</span>
        </div>
        <!-- Kartu 2: Anggota Aktif -->
        <div class="stat-card">
            <span class="stat-card-title">Anggota Aktif</span>
            <span class="stat-card-number" style="color: #DC2626;">{{ $statAktif }}</span>
        </div>
        <!-- Kartu 3: Menunggu Verifikasi -->
        <div class="stat-card">
            <span class="stat-card-title">Menunggu Verifikasi</span>
            <span class="stat-card-number" style="color: #D97706;">{{ $statMenunggu }}</span>
        </div>
        <!-- Kartu 4: Ditolak -->
        <div class="stat-card">
            <span class="stat-card-title">Ditolak</span>
            <span class="stat-card-number" style="color: #EF4444;">{{ $statDitolak }}</span>
        </div>
    </div>

    <!-- Pembungkus Seksi Pencarian & Filter -->
    <div class="filter-section-wrapper">
        <form method="GET" action="{{ route('admin.data-anggota') }}" style="display: flex; flex: 1; align-items: center; gap: 16px; flex-wrap: wrap; width: 100%;">
            <div class="search-container">
                <input type="text" name="search" class="search-input-field" value="{{ $search }}" placeholder="Cari nama atau NIK...">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="#94A3B8" stroke-width="2.2" fill="none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);">
                    <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </div>
            
            <div style="display: flex; gap: 12px; align-items: center;">
                <select name="jabatan" class="filter-dropdown-select" onchange="this.form.submit()">
                    <option value="Semua" {{ ($jabatan === 'Semua' || empty($jabatan)) ? 'selected' : '' }}>Semua</option>
                    <option value="Anggota" {{ $jabatan === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                    <option value="Ketua" {{ $jabatan === 'Ketua' ? 'selected' : '' }}>Ketua</option>
                    <option value="Wakil Ketua" {{ $jabatan === 'Wakil Ketua' ? 'selected' : '' }}>Wakil Ketua</option>
                    <option value="Sekretaris" {{ $jabatan === 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                    <option value="Bendahara" {{ $jabatan === 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                    <option value="Kepala Unit" {{ $jabatan === 'Kepala Unit' ? 'selected' : '' }}>Kepala Unit</option>
                    <option value="Pengawas" {{ $jabatan === 'Pengawas' ? 'selected' : '' }}>Pengawas</option>
                </select>

                <select name="status" class="filter-dropdown-select" onchange="this.form.submit()">
                    <option value="Semua" {{ ($status === 'Semua' || empty($status)) ? 'selected' : '' }}>Semua</option>
                    <option value="aktif" {{ $status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Pending</option>
                </select>
                
                @if($search || ($status && $status !== 'Semua') || ($jabatan && $jabatan !== 'Semua'))
                    <a href="{{ route('admin.data-anggota') }}" style="color: #64748B; font-size: 0.85rem; font-weight: 600; text-decoration: none;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Container -->
    <div class="list-table-container">
        <table class="members-data-table">
            <thead>
                <tr>
                    <th style="width: 32%;">Nama</th>
                    <th style="width: 13%;">Jabatan</th>
                    <th style="width: 16%;">RT/Dusun</th>
                    <th style="width: 13%;">Sumber</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 15%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($anggotaList as $a)
                    @php
                        // Inisial nama untuk foto profil avatar
                        $initials = '';
                        $words = explode(' ', $a->nama_lengkap);
                        foreach ($words as $w) {
                            $initials .= strtoupper(substr($w, 0, 1));
                        }
                        $initials = substr($initials, 0, 2);

                        // Rotasi warna deterministik berdasarkan ID anggota
                        $avatarColors = ['#8B5CF6', '#12B76A', '#2E90FA', '#7A5AF8', '#F59E0B', '#EF4444'];
                        $bgColor = $avatarColors[$a->id % count($avatarColors)];

                        // Tentukan sumber pendaftaran
                        $sumber = $a->sumber ?? 'Pendaftaran';
                    @endphp
                    <tr>
                        <td>
                            <div style="overflow-wrap: break-word; word-wrap: break-word; word-break: normal; white-space: normal;">
                                <div style="font-weight: 700; color: #0F172A; font-size: 0.92rem; margin-bottom: 2px; line-height: 1.35; overflow-wrap: break-word; word-wrap: break-word; word-break: normal; white-space: normal;">{{ $a->nama_lengkap }}</div>
                                <div style="font-size: 0.78rem; color: #94A3B8; white-space: nowrap;">{{ $a->nik }}</div>
                            </div>
                        </td>
                        <td style="color: #475569; font-weight: 500;">
                            {{ $a->jabatan }}
                        </td>
                        <td style="color: #475569; font-weight: 500;">
                            {{ $a->rt }} / {{ str_replace(['Dusun I', 'Dusun II', 'Dusun III'], ['DI', 'DII', 'DIII'], $a->dusun) }}
                        </td>
                        <td style="color: #475569; font-weight: 500;">
                            {{ $sumber }}
                        </td>
                        <td>
                            @if ($a->status === 'diterima')
                                <span class="status-pill-active">Aktif</span>
                            @elseif ($a->status === 'menunggu')
                                <span class="status-pill-pending">Pending</span>
                            @else
                                <span class="status-pill-rejected">Ditolak</span>
                            @endif
                        </td>
                        <td style="text-align: center; white-space: nowrap; padding: 14px 4px;">
                            <div style="display: inline-flex; gap: 4px; align-items: center; justify-content: center;">
                                <!-- Detail button -->
                                <button onclick="openDetailModal({{ json_encode($a) }})" class="btn-icon-action" title="Detail">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </button>

                                <!-- Edit button -->
                                <button onclick="openEditModal({{ json_encode($a) }})" class="btn-icon-action btn-icon-edit" title="Edit">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                    </svg>
                                </button>

                                <!-- Approve button (if pending) -->
                                @if ($a->status === 'menunggu')
                                    <form method="POST" action="{{ route('admin.anggota.verifikasi') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" name="anggota_id" value="{{ $a->id }}">
                                        <input type="hidden" name="action" value="terima">
                                        <button type="submit" class="btn-icon-action" style="color: #16A34A;" title="Setujui">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </button>
                                    </form>
                                    <!-- Tolak button (if pending) -->
                                    <button onclick="openTolakModal({{ $a->id }}, '{{ addslashes($a->nama_lengkap) }}')" class="btn-icon-action" style="color: #EF4444;" title="Tolak">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                @endif

                                <!-- Delete button -->
                                <button onclick="openHapusModal({{ $a->id }}, '{{ addslashes($a->nama_lengkap) }}')" class="btn-icon-action btn-icon-delete" title="Hapus">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </button>
                                <!-- Hidden form for delete (submitted via modal) -->
                                <form id="deleteForm-{{ $a->id }}" method="POST" action="{{ route('admin.anggota.delete', $a->id) }}" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94A3B8; font-weight: 500;">
                            Tidak ada data anggota ditemukan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $anggotaList->appends(request()->query())->links() }}
    </div>

    <!-- MODAL 1: DETAIL ANGGOTA -->
    <div class="custom-overlay" id="detailModal">
        <div class="modal-body" style="max-width: 600px;">
            <!-- Header (fixed) -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px; flex-shrink: 0;">
                <h3 style="font-weight: 800; font-size: 1.25rem; color: #0F172A; margin: 0;">Detail Anggota</h3>
                <button onclick="closeDetailModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Scrollable content wrapper for detail modal -->
            <div style="flex: 1; overflow-y: auto; padding-right: 8px;">
                <!-- Profile summary header box -->
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px; flex-wrap: wrap;">
                        <span id="detailNamaLengkap" style="font-size: 1.4rem; font-weight: 800; color: #0F172A; word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;">-</span>
                        <span id="detailJabatanBadge" class="role-pill-badge" style="vertical-align: middle;">-</span>
                    </div>
                    <span id="detailNik" style="font-size: 0.88rem; color: #94A3B8; font-family: monospace;">-</span>
                </div>

                <!-- Two-column grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px;">
                    <div>
                        <div class="detail-label">Jenis Kelamin</div>
                        <div class="detail-val" id="detailJenisKelamin">-</div>

                        <div class="detail-label">RT</div>
                        <div class="detail-val" id="detailRt">-</div>

                        <div class="detail-label">No HP</div>
                        <div class="detail-val" id="detailNoHp">-</div>

                        <div class="detail-label">Pekerjaan</div>
                        <div class="detail-val" id="detailPekerjaan">-</div>
                    </div>

                    <div>
                        <div class="detail-label">Tanggal Lahir</div>
                        <div class="detail-val" id="detailTanggalLahir">-</div>

                        <div class="detail-label">Dusun</div>
                        <div class="detail-val" id="detailDusun">-</div>

                        <div class="detail-label">Email</div>
                        <div class="detail-val" id="detailEmail">-</div>

                        <div class="detail-label">Pendidikan</div>
                        <div class="detail-val" id="detailPendidikan">-</div>
                    </div>
                </div>

                <!-- Full width fields -->
                <div style="margin-top: 8px;">
                    <div class="detail-label">Alamat</div>
                    <div class="detail-val" id="detailAlamatLengkap" style="margin-bottom: 16px;">-</div>

                    <div class="detail-label">Motivasi Bergabung</div>
                    <div class="detail-val" id="detailMotivasi" style="margin-bottom: 0; white-space: normal; word-wrap: break-word; overflow-wrap: anywhere; word-break: break-word;">-</div>
                </div>
            </div>
            
            <!-- Footer (fixed) -->
            <div style="margin-top: 24px; border-top: 1px solid #F1F5F9; padding-top: 16px; text-align: right; flex-shrink: 0;">
                <button onclick="closeDetailModal()" class="btn-cancel-custom" style="padding: 10px 32px; width: auto; flex: none;">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL 2: TAMBAH ANGGOTA -->
    <div class="custom-overlay" id="tambahModal">
        <div class="modal-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-weight: 800; font-size: 1.2rem; color: #1E293B; margin: 0;">Tambah Anggota</h3>
                <button onclick="closeTambahModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.anggota.store') }}" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
                @csrf
                <!-- Scrollable form container inside modal -->
                <div style="flex: 1; overflow-y: auto; padding-right: 8px;">
                    <!-- Data Diri -->
                    <div class="modal-section-title" style="margin-top: 0;">Data Diri</div>
                    
                    <div class="form-row">
                        <label>Nama Lengkap <span style="color:#EF4444">*</span></label>
                        <input type="text" name="nama_lengkap" required maxlength="40" placeholder="Contoh: Budi Santoso" value="{{ old('nama_lengkap') }}">
                        @error('nama_lengkap')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>NIK <span style="color:#EF4444">*</span></label>
                            <input type="text" name="nik" id="tambahNik" required maxlength="16" placeholder="Contoh: 6401010101010001" value="{{ old('nik') }}">
                            <div id="tambahNikError" style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;display:none;"></div>
                            @error('nik')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <label>Jenis Kelamin <span style="color:#EF4444">*</span></label>
                            <select name="jenis_kelamin" required>
                                <option value="" disabled {{ old('jenis_kelamin') ? '' : 'selected' }}>Pilih</option>
                                <option value="Laki-Laki" {{ old('jenis_kelamin') === 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>Tempat Lahir <span style="color:#EF4444">*</span></label>
                            <input type="text" name="tempat_lahir" required maxlength="25" placeholder="Contoh: Paser" value="{{ old('tempat_lahir') }}">
                            @error('tempat_lahir')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <label>Tanggal Lahir <span style="color:#EF4444">*</span></label>
                            <input type="date" name="tanggal_lahir" required value="{{ old('tanggal_lahir') }}">
                            @error('tanggal_lahir')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="modal-section-title">Alamat</div>

                    <div class="form-row">
                        <label>Alamat <span style="color:#EF4444">*</span></label>
                        <textarea name="alamat_lengkap" rows="2" required maxlength="80" placeholder="Tulis alamat lengkap...">{{ old('alamat_lengkap') }}</textarea>
                        @error('alamat_lengkap')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>RT <span style="color:#EF4444">*</span></label>
                            <select name="rt" required>
                                <option value="" disabled {{ old('rt') ? '' : 'selected' }}>Pilih RT</option>
                                <option value="RT01" {{ old('rt') === 'RT01' ? 'selected' : '' }}>RT01</option>
                                <option value="RT02" {{ old('rt') === 'RT02' ? 'selected' : '' }}>RT02</option>
                                <option value="RT03" {{ old('rt') === 'RT03' ? 'selected' : '' }}>RT03</option>
                                <option value="RT04" {{ old('rt') === 'RT04' ? 'selected' : '' }}>RT04</option>
                                <option value="RT05" {{ old('rt') === 'RT05' ? 'selected' : '' }}>RT05</option>
                                <option value="RT06" {{ old('rt') === 'RT06' ? 'selected' : '' }}>RT06</option>
                                <option value="RT07" {{ old('rt') === 'RT07' ? 'selected' : '' }}>RT07</option>
                                <option value="RT08" {{ old('rt') === 'RT08' ? 'selected' : '' }}>RT08</option>
                            </select>
                            @error('rt')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <label>Dusun <span style="color:#EF4444">*</span></label>
                            <select name="dusun" required>
                                <option value="" disabled {{ old('dusun') ? '' : 'selected' }}>Pilih Dusun</option>
                                <option value="Dusun I" {{ old('dusun') === 'Dusun I' ? 'selected' : '' }}>Dusun I</option>
                                <option value="Dusun II" {{ old('dusun') === 'Dusun II' ? 'selected' : '' }}>Dusun II</option>
                            </select>
                            @error('dusun')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="modal-section-title">Kontak</div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>No HP <span style="color:#EF4444">*</span></label>
                            <input type="tel" name="no_hp" id="tambahNoHp" required maxlength="15" placeholder="Contoh: 08123456789" value="{{ old('no_hp') }}">
                            <div id="tambahNoHpError" style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;display:none;"></div>
                            @error('no_hp')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <label>Email <span style="color:#94A3B8;font-weight:400;">(Opsional)</span></label>
                            <input type="email" name="email" maxlength="60" placeholder="Contoh: budi@gmail.com" value="{{ old('email') }}">
                            @error('email')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Pekerjaan, Pendidikan & Jabatan -->
                    <div class="modal-section-title">Pekerjaan, Pendidikan & Jabatan</div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>Pekerjaan <span style="color:#EF4444">*</span></label>
                            <input type="text" name="pekerjaan" required maxlength="20" placeholder="Contoh: Petani" value="{{ old('pekerjaan') }}">
                            @error('pekerjaan')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <label>Pendidikan Terakhir <span style="color:#EF4444">*</span></label>
                            <select name="pendidikan" required>
                                <option value="" disabled {{ old('pendidikan') ? '' : 'selected' }}>— Pilih —</option>
                                <option value="SD" {{ old('pendidikan') === 'SD' ? 'selected' : '' }}>SD</option>
                                <option value="SMP" {{ old('pendidikan') === 'SMP' ? 'selected' : '' }}>SMP</option>
                                <option value="SMA/SMK" {{ old('pendidikan') === 'SMA/SMK' ? 'selected' : '' }}>SMA/SMK</option>
                                <option value="D3" {{ old('pendidikan') === 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="S1" {{ old('pendidikan') === 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="S2/S3" {{ old('pendidikan') === 'S2/S3' ? 'selected' : '' }}>S2/S3</option>
                            </select>
                            @error('pendidikan')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <label>Jabatan di Koperasi <span style="color:#EF4444">*</span></label>
                            <select name="jabatan" required>
                                <option value="Anggota" {{ old('jabatan') === 'Anggota' ? 'selected' : '' }}>Anggota</option>
                                <option value="Ketua" {{ old('jabatan') === 'Ketua' ? 'selected' : '' }}>Ketua</option>
                                <option value="Wakil Ketua" {{ old('jabatan') === 'Wakil Ketua' ? 'selected' : '' }}>Wakil Ketua</option>
                                <option value="Sekretaris" {{ old('jabatan') === 'Sekretaris' ? 'selected' : '' }}>Sekretaris</option>
                                <option value="Bendahara" {{ old('jabatan') === 'Bendahara' ? 'selected' : '' }}>Bendahara</option>
                                <option value="Kepala Unit" {{ old('jabatan') === 'Kepala Unit' ? 'selected' : '' }}>Kepala Unit</option>
                                <option value="Pengawas" {{ old('jabatan') === 'Pengawas' ? 'selected' : '' }}>Pengawas</option>
                            </select>
                            @error('jabatan')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Motivasi Bergabung -->
                    <div class="modal-section-title">Motivasi Bergabung</div>

                    <div class="form-row">
                        <label>Motivasi Bergabung <span style="color:#EF4444">*</span></label>
                        <textarea name="motivasi" rows="2" required placeholder="Tulis motivasi bergabung...">{{ old('motivasi') }}</textarea>
                        @error('motivasi')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                    </div>

                    <!-- Status Keanggotaan -->
                    <div class="modal-section-title">Status Keanggotaan</div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>Status <span style="color:#EF4444">*</span></label>
                            <select name="status" required>
                                <option value="diterima" {{ old('status') === 'diterima' ? 'selected' : '' }}>Aktif</option>
                                <option value="menunggu" {{ old('status') === 'menunggu' ? 'selected' : '' }}>Pending</option>
                                <option value="ditolak" {{ old('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                            @error('status')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-row">
                            <label>Sumber Data <span style="color:#EF4444">*</span></label>
                            <select name="sumber" required>
                                <option value="Admin" {{ old('sumber') === 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Pendaftaran" {{ old('sumber') === 'Pendaftaran' ? 'selected' : '' }}>Pendaftaran</option>
                            </select>
                            @error('sumber')<div style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel-custom" onclick="closeTambahModal()">Batal</button>
                    <button type="submit" class="btn-submit-custom">Tambah Anggota</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: EDIT ANGGOTA -->
    <div class="custom-overlay" id="editModal">
        <div class="modal-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-weight: 800; font-size: 1.2rem; color: #1E293B; margin: 0;">Edit Anggota</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form id="editForm" method="POST" action="" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
                @csrf
                @method('PUT')
                <!-- Scrollable form container inside modal -->
                <div style="flex: 1; overflow-y: auto; padding-right: 8px;">
                    <!-- Data Diri -->
                    <div class="modal-section-title" style="margin-top: 0;">Data Diri</div>
                    
                    <div class="form-row">
                        <label>Nama Lengkap <span style="color:#EF4444">*</span></label>
                        <input type="text" name="nama_lengkap" id="editNamaLengkap" required maxlength="40">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>NIK <span style="color:#EF4444">*</span></label>
                            <input type="text" name="nik" id="editNikVal" required maxlength="16">
                            <div id="editNikError" style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;display:none;"></div>
                        </div>
                        <div class="form-row">
                            <label>Jenis Kelamin <span style="color:#EF4444">*</span></label>
                            <select name="jenis_kelamin" id="editJenisKelamin" required>
                                <option value="Laki-Laki">Laki-Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>Tempat Lahir <span style="color:#EF4444">*</span></label>
                            <input type="text" name="tempat_lahir" id="editTempatLahir" required maxlength="25">
                        </div>
                        <div class="form-row">
                            <label>Tanggal Lahir <span style="color:#EF4444">*</span></label>
                            <input type="date" name="tanggal_lahir" id="editTanggalLahir" required>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="modal-section-title">Alamat</div>

                    <div class="form-row">
                        <label>Alamat <span style="color:#EF4444">*</span></label>
                        <textarea name="alamat_lengkap" id="editAlamatLengkap" rows="2" required maxlength="80"></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>RT <span style="color:#EF4444">*</span></label>
                            <select name="rt" id="editRtVal" required>
                                <option value="RT01">RT01</option>
                                <option value="RT02">RT02</option>
                                <option value="RT03">RT03</option>
                                <option value="RT04">RT04</option>
                                <option value="RT05">RT05</option>
                                <option value="RT06">RT06</option>
                                <option value="RT07">RT07</option>
                                <option value="RT08">RT08</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Dusun <span style="color:#EF4444">*</span></label>
                            <select name="dusun" id="editDusunVal" required>
                                <option value="Dusun I">Dusun I</option>
                                <option value="Dusun II">Dusun II</option>
                            </select>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="modal-section-title">Kontak</div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>No HP <span style="color:#EF4444">*</span></label>
                            <input type="tel" name="no_hp" id="editNoHp" required maxlength="15">
                            <div id="editNoHpError" style="color:#EF4444;font-size:0.78rem;margin-top:3px;font-weight:600;display:none;"></div>
                        </div>
                        <div class="form-row">
                            <label>Email <span style="color:#94A3B8;font-weight:400;">(Opsional)</span></label>
                            <input type="email" name="email" id="editEmailVal" maxlength="60">
                        </div>
                    </div>

                    <!-- Pekerjaan, Pendidikan & Jabatan -->
                    <div class="modal-section-title">Pekerjaan, Pendidikan & Jabatan</div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>Pekerjaan <span style="color:#EF4444">*</span></label>
                            <input type="text" name="pekerjaan" id="editPekerjaanVal" required maxlength="20">
                        </div>
                        <div class="form-row">
                            <label>Pendidikan Terakhir <span style="color:#EF4444">*</span></label>
                            <select name="pendidikan" id="editPendidikanVal" required>
                                <option value="" disabled>— Pilih —</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2/S3">S2/S3</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Jabatan di Koperasi <span style="color:#EF4444">*</span></label>
                            <select name="jabatan" id="editJabatan" required>
                                <option value="Anggota">Anggota</option>
                                <option value="Ketua">Ketua</option>
                                <option value="Wakil Ketua">Wakil Ketua</option>
                                <option value="Sekretaris">Sekretaris</option>
                                <option value="Bendahara">Bendahara</option>
                                <option value="Kepala Unit">Kepala Unit</option>
                                <option value="Pengawas">Pengawas</option>
                            </select>
                        </div>
                    </div>

                    <!-- Motivasi Bergabung -->
                    <div class="modal-section-title">Motivasi Bergabung</div>

                    <div class="form-row">
                        <label>Motivasi Bergabung <span style="color:#EF4444">*</span></label>
                        <textarea name="motivasi" id="editMotivasi" rows="2" required placeholder="Tulis motivasi bergabung..."></textarea>
                    </div>

                    <!-- Status Keanggotaan -->
                    <div class="modal-section-title">Status Keanggotaan</div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-row">
                            <label>Status <span style="color:#EF4444">*</span></label>
                            <select name="status" id="editStatus" required>
                                <option value="diterima">Aktif</option>
                                <option value="menunggu">Pending</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label>Sumber Data <span style="color:#EF4444">*</span></label>
                            <select name="sumber" id="editSumber" required>
                                <option value="Admin">Admin</option>
                                <option value="Pendaftaran">Pendaftaran</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel-custom" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-submit-custom">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: TOLAK PENDAFTARAN -->
    <div class="custom-overlay" id="tolakModal">
        <div class="modal-body" style="max-width: 420px; border-radius: 20px; padding: 28px 28px 24px;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <!-- Icon -->
                <div style="flex-shrink: 0; width: 48px; height: 48px; border-radius: 50%; background: #FEE2E2; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#EF4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="15" y1="9" x2="9" y2="15"></line>
                        <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                </div>
                <!-- Text -->
                <div style="flex: 1;">
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Tolak Pendaftaran?</h3>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.6;">
                        Anda akan menolak pendaftaran <strong id="tolakNama" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;"></strong>.<br>
                        Status akan berubah menjadi "Ditolak".
                    </p>
                </div>
            </div>
            <!-- Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button onclick="closeTolakModal()" class="btn-cancel-custom" style="flex: none; width: auto; padding: 9px 22px;">Batal</button>
                <form id="tolakForm" method="POST" action="" style="display: inline; margin: 0;">
                    @csrf
                    <input type="hidden" name="anggota_id" id="tolakAnggotaId">
                    <input type="hidden" name="action" value="tolak">
                    <button type="submit" class="btn-submit-custom" style="flex: none; width: auto; padding: 9px 22px;">Ya, Tolak</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: HAPUS ANGGOTA -->
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
                    <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Hapus Anggota?</h3>
                    <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.6;">
                        Anda akan menghapus data anggota <strong id="hapusNama" style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-word;"></strong>.<br>
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
@endsection

@section('scripts')
    <script>
        @if ($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                openTambahModal();
            });
        @endif

        // Fungsi-fungsi kontrol modal
        function openTambahModal() {
            document.getElementById('tambahModal').classList.add('active');
        }

        function closeTambahModal() {
            document.getElementById('tambahModal').classList.remove('active');
        }

        function openDetailModal(a) {
            document.getElementById('detailNamaLengkap').innerText = a.nama_lengkap;
            document.getElementById('detailJabatanBadge').innerText = a.jabatan || 'Anggota';
            document.getElementById('detailNik').innerText = a.nik;
            document.getElementById('detailJenisKelamin').innerText = a.jenis_kelamin;
            document.getElementById('detailRt').innerText = a.rt || '-';
            document.getElementById('detailNoHp').innerText = a.no_hp;
            document.getElementById('detailPekerjaan').innerText = a.pekerjaan || '-';
            document.getElementById('detailTanggalLahir').innerText = (a.tempat_lahir || '-') + ', ' + (a.tanggal_lahir || '-');
            document.getElementById('detailDusun').innerText = a.dusun || '-';
            document.getElementById('detailEmail').innerText = a.email || '-';
            document.getElementById('detailPendidikan').innerText = a.pendidikan || '-';
            document.getElementById('detailAlamatLengkap').innerText = a.alamat_lengkap || '-';
            document.getElementById('detailMotivasi').innerText = a.motivasi || '-';

            document.getElementById('detailModal').classList.add('active');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
        }

        function openEditModal(a) {
            document.getElementById('editForm').action = "{{ url('/admin/anggota') }}/" + a.id;
            
            document.getElementById('editNamaLengkap').value = a.nama_lengkap;
            document.getElementById('editNikVal').value = a.nik;
            document.getElementById('editJenisKelamin').value = a.jenis_kelamin;
            document.getElementById('editTempatLahir').value = a.tempat_lahir;
            document.getElementById('editTanggalLahir').value = a.tanggal_lahir;
            document.getElementById('editRtVal').value = a.rt ? String(a.rt).replace(/\s+/g, '') : '';
            document.getElementById('editDusunVal').value = a.dusun || '';
            document.getElementById('editNoHp').value = a.no_hp;
            document.getElementById('editEmailVal').value = a.email || '';
            document.getElementById('editPekerjaanVal').value = a.pekerjaan || '';
            document.getElementById('editPendidikanVal').value = a.pendidikan || '';
            document.getElementById('editAlamatLengkap').value = a.alamat_lengkap;
            document.getElementById('editMotivasi').value = a.motivasi || '';
            document.getElementById('editJabatan').value = a.jabatan || 'Anggota';
            document.getElementById('editSumber').value = a.sumber || 'Admin';
            document.getElementById('editStatus').value = a.status;

            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Pengendali modal konfirmasi tolak pendaftaran
        var _tolakFormAction = "{{ route('admin.anggota.verifikasi') }}";
        function openTolakModal(id, nama) {
            document.getElementById('tolakNama').innerText = nama;
            document.getElementById('tolakAnggotaId').value = id;
            document.getElementById('tolakForm').action = _tolakFormAction;
            document.getElementById('tolakModal').classList.add('active');
        }
        function closeTolakModal() {
            document.getElementById('tolakModal').classList.remove('active');
        }

        // Pengendali modal konfirmasi hapus anggota
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

        // Close templates when clicking overlay backdrop
        document.getElementById('detailModal').addEventListener('click', e => {
            if (e.target === document.getElementById('detailModal')) closeDetailModal();
        });
        document.getElementById('tambahModal').addEventListener('click', e => {
            if (e.target === document.getElementById('tambahModal')) closeTambahModal();
        });
        document.getElementById('editModal').addEventListener('click', e => {
            if (e.target === document.getElementById('editModal')) closeEditModal();
        });
        document.getElementById('tolakModal').addEventListener('click', e => {
            if (e.target === document.getElementById('tolakModal')) closeTolakModal();
        });
        document.getElementById('hapusModal').addEventListener('click', e => {
            if (e.target === document.getElementById('hapusModal')) closeHapusModal();
        });

        // Pembatasan input nomor telepon (hanya angka) dan validasi kustom awalan 08
        document.addEventListener('DOMContentLoaded', function() {
            const addNoHp = document.getElementById('tambahNoHp');
            const editNoHp = document.getElementById('editNoHp');
            const addNik = document.getElementById('tambahNik');
            const editNik = document.getElementById('editNikVal');

            function restrictNonDigits(inputEl) {
                if (!inputEl) return;
                inputEl.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            restrictNonDigits(addNik);
            restrictNonDigits(editNik);
            restrictNonDigits(addNoHp);
            restrictNonDigits(editNoHp);

            function showInlineError(errorEl, message) {
                if (!errorEl) return;
                errorEl.textContent = message;
                errorEl.style.display = 'block';
            }

            function clearInlineError(errorEl) {
                if (!errorEl) return;
                errorEl.textContent = '';
                errorEl.style.display = 'none';
            }

            function validatePhone(inputEl, errorEl) {
                if (!inputEl) return true;
                const val = inputEl.value;
                if (!val) {
                    showInlineError(errorEl, 'Nomor HP / WhatsApp wajib diisi.');
                    return false;
                }
                if (!/^08[0-9]{8,13}$/.test(val)) {
                    showInlineError(errorEl, 'Nomor HP / WhatsApp wajib diawali dengan 08 dan berisi 10-15 angka.');
                    return false;
                }
                clearInlineError(errorEl);
                return true;
            }

            function validateNik(inputEl, errorEl) {
                if (!inputEl) return true;
                const val = inputEl.value;
                if (!val) {
                    showInlineError(errorEl, 'NIK wajib diisi.');
                    return false;
                }
                if (val.length !== 16) {
                    showInlineError(errorEl, 'NIK wajib berupa 16 digit angka.');
                    return false;
                }
                clearInlineError(errorEl);
                return true;
            }

            const tambahNikError = document.getElementById('tambahNikError');
            const tambahNoHpError = document.getElementById('tambahNoHpError');
            const editNikError = document.getElementById('editNikError');
            const editNoHpError = document.getElementById('editNoHpError');

            if (addNoHp) {
                addNoHp.addEventListener('input', () => validatePhone(addNoHp, tambahNoHpError));
            }
            if (editNoHp) {
                editNoHp.addEventListener('input', () => validatePhone(editNoHp, editNoHpError));
            }
            if (addNik) {
                addNik.addEventListener('input', () => validateNik(addNik, tambahNikError));
            }
            if (editNik) {
                editNik.addEventListener('input', () => validateNik(editNik, editNikError));
            }

            const tambahForm = document.querySelector('#tambahModal form');
            if (tambahForm) {
                tambahForm.addEventListener('submit', function(e) {
                    let valid = true;
                    if (!validatePhone(addNoHp, tambahNoHpError)) {
                        valid = false;
                    }
                    if (!validateNik(addNik, tambahNikError)) {
                        valid = false;
                    }
                    if (!valid) {
                        e.preventDefault();
                    }
                });
            }

            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    let valid = true;
                    if (!validatePhone(editNoHp, editNoHpError)) {
                        valid = false;
                    }
                    if (!validateNik(editNik, editNikError)) {
                        valid = false;
                    }
                    if (!valid) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
@endsection
