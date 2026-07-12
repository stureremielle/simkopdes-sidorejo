@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/daftar.css') }}">
@endsection

@section('content')
    <section class="daftar-hero-section">
        <div class="container hero-container">
            <h1 class="daftar-hero-title">Daftar Anggota</h1>
            <p class="daftar-hero-subtitle">Bergabunglah bersama kami dan wujudkan kesejahteraan bersama melalui Koperasi Desa Merah Putih Sidorejo.</p>
        </div>
    </section>

    <section class="daftar-form-section">
        <div class="container">
            <div class="daftar-form-card">

                @if (session('success'))
                <div class="daftar-success-card">
                    <div class="success-icon-wrapper">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>
                    <h2 class="success-title">Pendaftaran Terkirim!</h2>
                    <p class="success-description">
                        Terima kasih telah mendaftar. Pengurus koperasi akan memverifikasi data Anda dan menghubungi Anda dalam 3–5 hari kerja.
                    </p>
                    <a href="{{ route('daftar') }}" class="btn-daftar-lagi">Daftar Lagi</a>
                </div>
                @else



                <!-- Syarat Keanggotaan Section -->
                <div class="syarat-keanggotaan-section">
                    <h3 class="syarat-title">Syarat Keanggotaan</h3>
                    <ul class="syarat-list">
                        <li class="syarat-item">
                            <svg viewBox="0 0 24 24" class="check-icon" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span class="syarat-text-content">Warga Desa Sidorejo atau sekitarnya</span>
                        </li>
                        <li class="syarat-item">
                            <svg viewBox="0 0 24 24" class="check-icon" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span class="syarat-text-content">Berusia minimal 17 tahun atau sudah menikah</span>
                        </li>
                        <li class="syarat-item">
                            <svg viewBox="0 0 24 24" class="check-icon" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span class="syarat-text-content">Bersedia membayar simpanan pokok dan wajib</span>
                        </li>
                        <li class="syarat-item">
                            <svg viewBox="0 0 24 24" class="check-icon" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <span class="syarat-text-content">Menyetujui AD/ART Koperasi</span>
                        </li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('daftar.store') }}" class="custom-form grid-layout" id="daftarForm" novalidate>
                    @csrf

                    <!-- Data Diri -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <svg class="form-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <h3 class="form-section-title">Data Diri</h3>
                        </div>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="namaLengkap" class="form-label">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" id="namaLengkap" name="namaLengkap" class="form-input" placeholder="Sesuai KTP" required value="{{ old('namaLengkap') }}">
                                <span class="field-hint" id="hint-namaLengkap">Wajib diisi</span>
                            </div>
                        </div>
                        <div class="form-row split-row">
                            <div class="form-group">
                                <label for="nikKtp" class="form-label">NIK (KTP) <span class="required">*</span></label>
                                <input type="text" id="nikKtp" name="nikKtp" class="form-input" placeholder="16 digit" maxlength="16" required value="{{ old('nikKtp') }}">
                                <span class="field-hint" id="hint-nikKtp">Wajib diisi / harus 16 digit</span>
                            </div>
                            <div class="form-group">
                                <label for="jenisKelamin" class="form-label">Jenis Kelamin <span class="required">*</span></label>
                                <div class="select-input-wrapper">
                                    <select id="jenisKelamin" name="jenisKelamin" class="form-input form-select" required>
                                        <option value="" disabled {{ empty(old('jenisKelamin')) ? 'selected' : '' }}>— Pilih —</option>
                                        <option value="Laki-Laki" {{ old('jenisKelamin') === 'Laki-Laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenisKelamin') === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    <svg class="select-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <span class="field-hint" id="hint-jenisKelamin">Wajib dipilih</span>
                            </div>
                        </div>
                        <div class="form-row split-row">
                            <div class="form-group">
                                <label for="tempatLahir" class="form-label">Tempat Lahir <span class="required">*</span></label>
                                <input type="text" id="tempatLahir" name="tempatLahir" class="form-input" placeholder="Kota/Kabupaten" required value="{{ old('tempatLahir') }}">
                                <span class="field-hint" id="hint-tempatLahir">Wajib diisi</span>
                            </div>
                            <div class="form-group">
                                <label for="tanggalLahir" class="form-label">Tanggal Lahir <span class="required">*</span></label>
                                <div class="date-input-wrapper">
                                    <input type="date" id="tanggalLahir" name="tanggalLahir" class="form-input form-date" required value="{{ old('tanggalLahir') }}">
                                    <svg class="calendar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                </div>
                                <span class="field-hint" id="hint-tanggalLahir">Wajib diisi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <svg class="form-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <h3 class="form-section-title">Alamat</h3>
                        </div>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="alamatLengkap" class="form-label">Alamat Lengkap <span class="required">*</span></label>
                                <input type="text" id="alamatLengkap" name="alamatLengkap" class="form-input" placeholder="Nama jalan / blok / nomor rumah" required value="{{ old('alamatLengkap') }}">
                                <span class="field-hint" id="hint-alamatLengkap">Wajib diisi</span>
                            </div>
                        </div>
                        <div class="form-row split-row">
                            <div class="form-group">
                                <label for="rtSelect" class="form-label">RT <span class="required">*</span></label>
                                <div class="select-input-wrapper">
                                    <select id="rtSelect" name="rtSelect" class="form-input form-select" required>
                                        <option value="" disabled {{ empty(old('rtSelect')) ? 'selected' : '' }}>— Pilih RT —</option>
                                        @for($i=1; $i<=8; $i++)
                                            @php $rtName = "RT 0". $i; @endphp
                                            <option value="{{ $rtName }}" {{ old('rtSelect') === $rtName ? 'selected' : '' }}>{{ $rtName }}</option>
                                        @endfor
                                    </select>
                                    <svg class="select-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <span class="field-hint" id="hint-rtSelect">Wajib dipilih</span>
                            </div>
                            <div class="form-group">
                                <label for="dusunSelect" class="form-label">Dusun <span class="required">*</span></label>
                                <div class="select-input-wrapper">
                                    <select id="dusunSelect" name="dusunSelect" class="form-input form-select" required>
                                        <option value="" disabled {{ empty(old('dusunSelect')) ? 'selected' : '' }}>— Pilih Dusun —</option>
                                        <option value="Dusun I" {{ old('dusunSelect') === 'Dusun I' ? 'selected' : '' }}>Dusun I</option>
                                        <option value="Dusun II" {{ old('dusunSelect') === 'Dusun II' ? 'selected' : '' }}>Dusun II</option>
                                    </select>
                                    <svg class="select-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                                <span class="field-hint" id="hint-dusunSelect">Wajib dipilih</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <svg class="form-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <h3 class="form-section-title">Kontak</h3>
                        </div>
                        <div class="form-row split-row">
                            <div class="form-group">
                                <label for="noHp" class="form-label">No. HP / WhatsApp <span class="required">*</span></label>
                                <input type="tel" id="noHp" name="noHp" class="form-input" placeholder="08xxxxxxxxxx" required value="{{ old('noHp') }}">
                                <span class="field-hint" id="hint-noHp">Wajib diisi</span>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email (opsional)</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="contoh@email.com" value="{{ old('email') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Pekerjaan & Pendidikan -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <svg class="form-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            <h3 class="form-section-title">Pekerjaan &amp; Pendidikan</h3>
                        </div>
                        <div class="form-row split-row">
                            <div class="form-group">
                                <label for="pekerjaan" class="form-label">Pekerjaan</label>
                                <input type="text" id="pekerjaan" name="pekerjaan" class="form-input" placeholder="Petani, Pedagang, dll." value="{{ old('pekerjaan') }}">
                            </div>
                            <div class="form-group">
                                <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                                <div class="select-input-wrapper">
                                    <select id="pendidikan" name="pendidikan" class="form-input form-select">
                                        <option value="" disabled {{ empty(old('pendidikan')) ? 'selected' : '' }}>— Pilih —</option>
                                        @foreach (['SD','SMP','SMA/SMK','D3','S1','S2/S3'] as $p)
                                            <option value="{{ $p }}" {{ old('pendidikan') === $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                    <svg class="select-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Motivasi -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <svg class="form-section-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            <h3 class="form-section-title">Motivasi Bergabung</h3>
                        </div>
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="motivasi" class="form-label">Motivasi Bergabung</label>
                                <textarea id="motivasi" name="motivasi" class="form-input form-textarea" rows="4" placeholder="Ceritakan alasan Anda ingin bergabung dengan koperasi...">{{ old('motivasi') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-submit-container">
                        <button type="submit" class="btn-submit">
                            <span>Kirim Pendaftaran</span>
                            <svg class="submit-btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                        </button>
                    </div>
                </form>
                @endif

            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('daftarForm');
    if (!form) return;

    const requiredFields = [
        ['namaLengkap',  'hint-namaLengkap'],
        ['nikKtp',       'hint-nikKtp'],
        ['jenisKelamin', 'hint-jenisKelamin'],
        ['tempatLahir',  'hint-tempatLahir'],
        ['tanggalLahir', 'hint-tanggalLahir'],
        ['alamatLengkap','hint-alamatLengkap'],
        ['rtSelect',     'hint-rtSelect'],
        ['dusunSelect',  'hint-dusunSelect'],
        ['noHp',         'hint-noHp'],
    ];

    function validate(fieldId, hintId) {
        const el   = document.getElementById(fieldId);
        const hint = document.getElementById(hintId);
        if (!el || !hint) return true;
        let invalid = el.value.trim() === '';
        // NIK khusus: harus tepat 16 karakter
        if (fieldId === 'nikKtp' && !invalid && el.value.trim().length !== 16) {
            invalid = true;
        }
        hint.classList.toggle('field-hint-visible', invalid);
        el.classList.toggle('form-input-error', invalid);
        return !invalid;
    }

    requiredFields.forEach(([fId, hId]) => {
        const el = document.getElementById(fId);
        if (!el) return;
        el.addEventListener('input',  () => validate(fId, hId));
        el.addEventListener('change', () => validate(fId, hId));
    });

    form.addEventListener('submit', function (e) {
        let valid = true;
        requiredFields.forEach(([fId, hId]) => {
            if (!validate(fId, hId)) valid = false;
        });
        if (!valid) e.preventDefault();
    });
});
</script>
@endsection
