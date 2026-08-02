@extends('layouts.admin')

@section('title', 'Dashboard - Panel Admin')
@section('breadcrumb', 'Dashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/admin/dashboard.css') }}?v={{ filemtime(public_path('assets/css/admin/dashboard.css')) }}">
@endsection

@section('content')
    <h1 style="font-weight:800;font-size:1.85rem;color:#0F172A;margin:0 0 24px 0;letter-spacing:-0.5px;">Dashboard</h1>

    {{-- Stats Row --}}
    <div class="stats-grid">
        <div class="stats-card">
            <span class="stats-card-value">{{ $totalAnggota }}</span>
            <span class="stats-card-label">Total Anggota</span>
            <span class="stats-card-trend">+2 tahun ini</span>
        </div>
        <div class="stats-card">
            <span class="stats-card-value">{{ $layananAktif }}</span>
            <span class="stats-card-label">Layanan Aktif</span>
            <span class="stats-card-trend">{{ $kategoriLayananCount }} kategori</span>
        </div>
        <div class="stats-card">
            <span class="stats-card-value">{{ $fotoGaleri }}</span>
            <span class="stats-card-label">Foto Galeri</span>
            <span class="stats-card-trend">{{ $kategoriGaleriCount }} kategori</span>
        </div>
    </div>

    {{-- Berita Terbaru - Full Width --}}
    <div class="widget-card">
        <div class="widget-header">
            <h3 class="widget-title">Berita Terbaru</h3>
            <a href="{{ route('admin.berita') }}" class="widget-link">Lihat semua</a>
        </div>
        <div>
            @forelse($beritaTerbaru as $berita)
            <div class="article-row">
                <div class="article-info">
                    <span class="article-title">{{ Str::limit($berita->judul, 80) }}</span>
                    <span class="article-date">{{ \App\Helpers\Helper::formatTanggal($berita->tanggal_publikasi ?? ($berita->created_at ? $berita->created_at->toDateString() : date('Y-m-d'))) }}</span>
                </div>
                <span class="badge {{ $berita->status === 'tayang' ? 'badge-tayang' : 'badge-draft' }}">
                    {{ $berita->status === 'tayang' ? 'Tayang' : 'Draft' }}
                </span>
            </div>
            @empty
            <div style="text-align:center;color:#94A3B8;padding:24px 0;font-size:0.88rem;">Belum ada berita.</div>
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
