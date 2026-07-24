@extends('layouts.admin', ['activeSidebar' => 'pengumuman'])

@section('title', 'Pengumuman')
@section('breadcrumb', 'Pengumuman')

@section('content')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/pengumuman.css') }}?v={{ filemtime(public_path('assets/css/admin/pengumuman.css')) }}">
@endsection

<!-- Header Section -->
<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h1 class="page-title" style="margin: 0 0 4px 0;">Pengumuman</h1>
        <p style="margin: 0; color: #64748B; font-size: 0.9rem; font-weight: 500;">Kelola informasi atau pengumuman yang akan ditampilkan pada halaman Beranda website.</p>
    </div>
    <button type="button" onclick="openTambahModal()" class="btn-green-action">
        + Tambah Pengumuman
    </button>
</div>


<!-- Data Table Container -->
<div class="list-table-container">
    <table class="members-data-table">
        <thead>
            <tr>
                <th>Judul Pengumuman</th>
                <th style="width: 250px;">Periode</th>
                <th style="width: 120px;">Status</th>
                <th style="width: 100px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengumumanList as $index => $p)
                <tr>
                    <td>
                        <div>
                            <span style="font-weight: 600; color: #0F172A; font-size: 0.95rem;">{{ $p->judul }}</span>
                            <div style="font-size: 0.76rem; color: #94A3B8; margin-top: 4px; font-weight: 400; max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ Str::limit(strip_tags($p->isi), 50) }}
                            </div>
                        </div>
                    </td>
                    <td>{{ $p->tanggal }}</td>
                    <td>
                        @if ($p->status === 'Aktif')
                            <span class="status-pill-active">Aktif</span>
                        @else
                            <span class="status-pill-inactive">Tidak Aktif</span>
                        @endif
                    </td>
                    <td style="text-align: center; white-space: nowrap;">
                        <div style="display: inline-flex; gap: 8px; align-items: center;">
                            <!-- Edit Button -->
                            <button type="button" onclick="openEditModal({{ json_encode($p) }})" class="btn-icon-action btn-icon-edit" title="Edit">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </button>

                            <!-- Delete Button -->
                            <button type="button" onclick="openConfirmDelete({{ $p->id }}, '{{ addslashes($p->judul) }}')" class="btn-icon-action btn-icon-delete" title="Hapus">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px; color: #94A3B8; font-weight: 500;">
                        Tidak ada data pengumuman ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top: 20px;">
    {{ $pengumumanList->links() }}
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="custom-overlay" id="confirmDeleteModal">
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
            <div style="flex: 1; text-align: left;">
                <h3 style="font-size: 1.05rem; font-weight: 800; color: #0F172A; margin: 0 0 8px;">Hapus Pengumuman?</h3>
                <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.6;">
                    Anda akan menghapus pengumuman <strong id="deletePromoTitle"></strong>.<br>
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
        </div>
        <!-- Buttons -->
        <form id="deleteForm" method="POST" action="" style="margin: 0;">
            @csrf
            @method('DELETE')
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" onclick="closeConfirmDelete()" class="btn-cancel-custom" style="flex: none; width: auto; padding: 9px 22px;">Batal</button>
                <button type="submit" class="btn-submit-custom" style="flex: none; width: auto; padding: 9px 22px;">Ya, Hapus</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Pengumuman -->
<div class="custom-overlay" id="tambahModal">
    <div class="modal-body">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <h3 style="font-weight: 800; font-size: 1.2rem; color: #1E293B; margin: 0;">Tambah Pengumuman</h3>
            <button onclick="closeTambahModal()" style="background: none; border: none; color: #94A3B8; cursor: pointer; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.pengumuman.store') }}" style="display: flex; flex-direction: column; flex: 1; overflow: hidden; margin: 0;">
            @csrf

            <!-- Scrollable form container inside modal -->
            <div style="flex: 1; overflow-y: auto; padding-right: 8px;">
                {{-- Judul Pengumuman --}}
                <div class="form-row">
                    <label for="judul">Judul Pengumuman</label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required placeholder="Contoh: Koperasi libur operasional...">
                    @error('judul')
                        <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tanggal Mulai dan Selesai --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-row">
                        <label for="tanggal_mulai">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                            <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-row">
                        <label for="tanggal_selesai">Tanggal Selesai (Opsional)</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ old('tanggal_selesai') }}">
                        @error('tanggal_selesai')
                            <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Isi Pengumuman --}}
                <div class="form-row">
                    <label for="isi">Isi Pengumuman</label>
                    <textarea name="isi" id="isi" rows="5" required placeholder="Tulis isi pengumuman secara detail...">{{ old('isi') }}</textarea>
                    @error('isi')
                        <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-row">
                    <label for="status">Status</label>
                    <select name="status" id="status" required>
                        <option value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ old('status') === 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status')
                        <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="modal-buttons">
                <button type="button" class="btn-cancel-custom" onclick="closeTambahModal()">Batal</button>
                <button type="submit" class="btn-submit-custom">Simpan Pengumuman</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengumuman -->
<div class="custom-overlay" id="editModal">
    <div class="modal-body">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #F1F5F9; padding-bottom: 12px;">
            <h3 style="font-weight: 800; font-size: 1.2rem; color: #1E293B; margin: 0;">Edit Pengumuman</h3>
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
                {{-- Judul Pengumuman --}}
                <div class="form-row">
                    <label for="editJudul">Judul Pengumuman</label>
                    <input type="text" name="judul" id="editJudul" required placeholder="Contoh: Koperasi libur operasional...">
                    @error('judul')
                        <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Tanggal Mulai dan Selesai --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-row">
                        <label for="editTanggalMulai">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="editTanggalMulai" required>
                        @error('tanggal_mulai')
                            <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-row">
                        <label for="editTanggalSelesai">Tanggal Selesai (Opsional)</label>
                        <input type="date" name="tanggal_selesai" id="editTanggalSelesai">
                        @error('tanggal_selesai')
                            <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- Isi Pengumuman --}}
                <div class="form-row">
                    <label for="editIsi">Isi Pengumuman</label>
                    <textarea name="isi" id="editIsi" rows="5" required placeholder="Tulis isi pengumuman secara detail..."></textarea>
                    @error('isi')
                        <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="form-row">
                    <label for="editStatus">Status</label>
                    <select name="status" id="editStatus" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                    @error('status')
                        <span style="color: #EF4444; font-size: 0.8rem; font-weight: 500; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="modal-buttons">
                <button type="button" class="btn-cancel-custom" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit-custom">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTambahModal() {
        const modal = document.getElementById('tambahModal');
        modal.classList.add('active');
    }

    function closeTambahModal() {
        const modal = document.getElementById('tambahModal');
        modal.classList.remove('active');
    }

    function openEditModal(p) {
        const form = document.getElementById('editForm');
        form.action = `/admin/pengumuman/${p.id}`;
        
        document.getElementById('editJudul').value = p.judul;
        document.getElementById('editTanggalMulai').value = p.tanggal_mulai;
        document.getElementById('editTanggalSelesai').value = p.tanggal_selesai || '';
        document.getElementById('editIsi').value = p.isi;
        document.getElementById('editStatus').value = p.status;
        
        document.getElementById('editModal').classList.add('active');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.remove('active');
    }

    function openConfirmDelete(id, title) {
        const modal = document.getElementById('confirmDeleteModal');
        const form = document.getElementById('deleteForm');
        const titleEl = document.getElementById('deletePromoTitle');
        
        form.action = `/admin/pengumuman/${id}`;
        titleEl.textContent = `"${title}"`;
        modal.classList.add('active');
    }

    function closeConfirmDelete() {
        const modal = document.getElementById('confirmDeleteModal');
        modal.classList.remove('active');
    }


    // Auto open Tambah Modal if there are validation errors on load
    @if ($errors->any())
        openTambahModal();
    @endif

    // Close modal when clicking overlay backdrop
    document.getElementById('tambahModal').addEventListener('click', e => {
        if (e.target === document.getElementById('tambahModal')) closeTambahModal();
    });
    document.getElementById('editModal').addEventListener('click', e => {
        if (e.target === document.getElementById('editModal')) closeEditModal();
    });
    document.getElementById('confirmDeleteModal').addEventListener('click', e => {
        if (e.target === document.getElementById('confirmDeleteModal')) closeConfirmDelete();
    });
</script>
@endsection
