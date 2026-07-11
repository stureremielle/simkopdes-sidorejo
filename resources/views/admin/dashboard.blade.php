@extends('layouts.admin')

@section('title', 'Dashboard - Panel Admin')
@section('breadcrumb', 'Dashboard')

@section('styles')
<style>
    /* === DASHBOARD-SPECIFIC STYLES === */

    /* Stats Cards Grid - 3 columns */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 28px;
    }

    .stats-card {
        background-color: #FEF2F2;
        border-radius: 14px;
        padding: 24px 28px;
        border: 1px solid rgba(220, 38, 38, 0.08);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        gap: 6px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(220, 38, 38, 0.07);
    }

    .stats-card-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: #DC2626;
        line-height: 1.1;
        letter-spacing: -1px;
    }

    .stats-card-label {
        font-size: 0.9rem;
        font-weight: 700;
        color: #DC2626;
        margin-bottom: 2px;
    }

    .stats-card-trend {
        font-size: 0.8rem;
        font-weight: 500;
        color: #B91C1C;
        opacity: 0.8;
    }

    /* Full-width widget cards */
    .widget-card {
        background-color: #FFFFFF;
        border-radius: 16px;
        padding: 28px 32px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 1px 4px rgba(0,0,0,0.03);
        margin-bottom: 24px;
    }

    .widget-card:last-child {
        margin-bottom: 0;
    }

    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .widget-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #1E293B;
        letter-spacing: -0.3px;
    }

    .widget-link {
        font-size: 0.85rem;
        font-weight: 700;
        color: #DC2626;
        text-decoration: none;
        transition: color 0.2s;
    }
    .widget-link:hover { color: #991B1B; }

    /* Article list */
    .article-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #F1F5F9;
    }
    .article-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .article-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .article-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1E293B;
        line-height: 1.4;
    }

    .article-date {
        font-size: 0.78rem;
        color: #94A3B8;
        font-weight: 500;
    }

    /* Badges */
    .badge {
        font-size: 0.73rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 9999px;
        white-space: nowrap;
    }

    .badge-tayang {
        background-color: #DC2626;
        color: #FFFFFF;
    }

    .badge-draft {
        background-color: #F1F5F9;
        color: #64748B;
        border: 1px solid #E2E8F0;
    }

    /* Verification Table */
    .verify-table {
        width: 100%;
        border-collapse: collapse;
    }

    .verify-table thead tr {
        border-bottom: 1px solid #F1F5F9;
    }

    .verify-table thead th {
        font-size: 0.75rem;
        font-weight: 700;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 0 0 12px 0;
        text-align: left;
    }

    .verify-table tbody tr {
        border-bottom: 1px solid #F8FAFC;
        transition: background-color 0.15s ease;
    }

    .verify-table tbody tr:last-child {
        border-bottom: none;
    }

    .verify-table tbody tr:hover {
        background-color: #FAFAFA;
    }

    .verify-table tbody td {
        font-size: 0.9rem;
        color: #1E293B;
        padding: 16px 0;
        vertical-align: middle;
    }

    .verify-table tbody td.td-name {
        font-weight: 700;
        color: #0F172A;
    }

    .verify-table tbody td.td-muted {
        color: #64748B;
        font-weight: 500;
    }

    @media (max-width: 1100px) {
        .stats-grid { grid-template-columns: repeat(2,1fr); }
    }
    @media (max-width: 640px) {
        .stats-grid  { grid-template-columns: 1fr; }
        .widget-card { padding: 20px; }
        .verify-table thead th:nth-child(n+3),
        .verify-table tbody td:nth-child(n+3) { display: none; }
    }
</style>
@endsection

@section('content')
    <h1 style="font-weight:800;font-size:1.85rem;color:#0F172A;margin:0 0 24px 0;letter-spacing:-0.5px;">Dashboard</h1>

    {{-- Stats Row --}}
    <div class="stats-grid">
        <div class="stats-card">
            <span class="stats-card-value">{{ $totalAnggota }}</span>
            <span class="stats-card-label">Total Anggota</span>
            <span class="stats-card-trend">+14 tahun ini</span>
        </div>
        <div class="stats-card">
            <span class="stats-card-value">{{ $produkAktif }}</span>
            <span class="stats-card-label">Produk Aktif</span>
            <span class="stats-card-trend">2 kategori</span>
        </div>
        <div class="stats-card">
            <span class="stats-card-value">{{ $fotoGaleri }}</span>
            <span class="stats-card-label">Foto Galeri</span>
            <span class="stats-card-trend">4 kategori</span>
        </div>
    </div>

    {{-- Artikel Terbaru - Full Width --}}
    <div class="widget-card">
        <div class="widget-header">
            <h3 class="widget-title">Artikel Terbaru</h3>
            <a href="{{ route('admin.berita') }}" class="widget-link">Lihat semua</a>
        </div>
        <div>
            @forelse($artikelList as $artikel)
            <div class="article-row">
                <div class="article-info">
                    <span class="article-title">{{ Str::limit($artikel->judul, 80) }}</span>
                    <span class="article-date">{{ $artikel->created_at ? \Carbon\Carbon::parse($artikel->created_at)->translatedFormat('d M Y') : '-' }}</span>
                </div>
                <span class="badge {{ $artikel->status === 'tayang' ? 'badge-tayang' : 'badge-draft' }}">
                    {{ $artikel->status === 'tayang' ? 'Tayang' : 'Draft' }}
                </span>
            </div>
            @empty
            <div style="text-align:center;color:#94A3B8;padding:24px 0;font-size:0.88rem;">Belum ada artikel.</div>
            @endforelse
        </div>
    </div>

    {{-- Pendaftaran Menunggu Verifikasi - Full Width Table --}}
    <div class="widget-card">
        <div class="widget-header">
            <h3 class="widget-title">Pendaftaran Menunggu Verifikasi</h3>
            <a href="{{ route('admin.anggota') }}" class="widget-link">Kelola</a>
        </div>
        <div>
            @if($pendaftaranList->isEmpty())
                <div style="text-align:center;color:#94A3B8;padding:24px 0;font-size:0.88rem;">Tidak ada pendaftaran menunggu.</div>
            @else
            <table class="verify-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>RT/Dusun</th>
                        <th>Pekerjaan Saat Ini</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendaftaranList as $anggota)
                    <tr>
                        <td class="td-name">{{ $anggota->nama_lengkap }}</td>
                        <td class="td-muted">{{ $anggota->rt ?? '-' }} / {{ $anggota->dusun ?? '-' }}</td>
                        <td class="td-muted">{{ $anggota->pekerjaan ?? '-' }}</td>
                        <td class="td-muted">{{ $anggota->jabatan ?? 'Anggota' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
@endsection
