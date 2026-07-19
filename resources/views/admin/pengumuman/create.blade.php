@extends('layouts.admin', ['activeSidebar' => 'pengumuman'])

@section('title', 'Tambah Pengumuman')
@section('breadcrumb', 'Pengumuman')

@section('content')
<style>
    .page-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0F172A;
    }

    .form-container {
        background-color: #FFFFFF;
        border-radius: 16px;
        border: 1.5px solid #F1F5F9;
        padding: 28px;
        max-width: 800px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
    }

    .form-group input[type="text"],
    .form-group input[type="date"],
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #E2E8F0;
        border-radius: 8px;
        font-size: 0.88rem;
        color: #0F172A;
        font-family: inherit;
        outline: none;
        background-color: #FAFAFA;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #DC2626;
        background-color: #FFFFFF;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    /* Radio button / custom style for status */
    .status-group {
        display: flex;
        gap: 16px;
        align-items: center;
        margin-top: 4px;
    }

    .status-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 0.88rem;
        font-weight: 500;
        color: #475569;
    }

    .status-option input[type="radio"] {
        accent-color: #DC2626;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        justify-content: flex-start;
        gap: 12px;
        margin-top: 32px;
        border-top: 1px solid #F1F5F9;
        padding-top: 20px;
    }

    .btn-submit {
        background-color: #DC2626;
        color: #FFFFFF;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.9rem;
        font-family: inherit;
        transition: background-color 0.2s;
    }

    .btn-submit:hover {
        background-color: #B91C1C;
    }

    .btn-cancel {
        background-color: #FFFFFF;
        color: #1E293B;
        border: 1.5px solid #CBD5E1;
        padding: 10px 24px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.9rem;
        font-family: inherit;
        transition: background-color 0.2s;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .btn-cancel:hover {
        background-color: #F8FAFC;
    }

    .error-text {
        color: #EF4444;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 4px;
        display: block;
    }
</style>

<!-- Header Section -->
<div style="margin-bottom: 24px;">
    <h1 class="page-title" style="margin: 0 0 4px 0;">Tambah Pengumuman</h1>
    <p style="margin: 0; color: #64748B; font-size: 0.9rem; font-weight: 500;">Kelola informasi atau pengumuman yang akan ditampilkan pada halaman Beranda website.</p>
</div>

<!-- Form Card -->
<div class="form-container">
    <form method="POST" action="{{ route('admin.pengumuman.store') }}">
        @csrf

        {{-- Judul Pengumuman --}}
        <div class="form-group">
            <label for="judul">Judul Pengumuman <span style="color: #EF4444;">*</span></label>
            <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required placeholder="Contoh: Koperasi libur operasional...">
            @error('judul')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        {{-- Tanggal Mulai dan Selesai --}}
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai <span style="color: #EF4444;">*</span></label>
                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                @error('tanggal_mulai')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai (Opsional)</label>
                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
                @error('tanggal_selesai')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Isi Pengumuman --}}
        <div class="form-group">
            <label for="isi">Isi Pengumuman <span style="color: #EF4444;">*</span></label>
            <textarea name="isi" id="isi" rows="6" required placeholder="Tulis isi pengumuman secara detail... (Bisa menggunakan tag HTML sederhana seperti <p>, <strong>, etc.)">{{ old('isi') }}</textarea>
            @error('isi')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        {{-- Status --}}
        <div class="form-group">
            <label>Status <span style="color: #EF4444;">*</span></label>
            <div class="status-group">
                <label class="status-option">
                    <input type="radio" name="status" value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'checked' : '' }} required>
                    Aktif
                </label>
                <label class="status-option">
                    <input type="radio" name="status" value="Tidak Aktif" {{ old('status') === 'Tidak Aktif' ? 'checked' : '' }}>
                    Tidak Aktif
                </label>
            </div>
            @error('status')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        {{-- Actions --}}
        <div class="form-actions">
            <button type="submit" class="btn-submit">Simpan</button>
            <a href="{{ route('admin.pengumuman.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
</div>
@endsection
