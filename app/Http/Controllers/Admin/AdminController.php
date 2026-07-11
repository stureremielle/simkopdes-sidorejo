<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Layanan;
use App\Models\Galeri;
use App\Models\Berita;
use App\Models\PenyimpananFile;
use App\Models\Pengaturan;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $totalAnggota = Anggota::where('status', 'diterima')->count();
        $produkAktif  = Layanan::where('status', 'aktif')->count();
        $fotoGaleri   = Galeri::where('status', 'aktif')->count();

        // Latest news
        $artikelList = Berita::orderBy('created_at', 'desc')->take(5)->get();

        // Pending verifications
        $pendaftaranList = Anggota::where('status', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAnggota',
            'produkAktif',
            'fotoGaleri',
            'artikelList',
            'pendaftaranList'
        ));
    }

    /**
     * Handle member verification accept/reject.
     */
    public function verifyAnggota(Request $request)
    {
        $request->validate([
            'anggota_id' => 'required|exists:anggota,id',
            'action' => 'required|in:terima,tolak',
        ]);

        $anggota = Anggota::findOrFail($request->anggota_id);
        $anggota->status = $request->action === 'terima' ? 'diterima' : 'ditolak';
        $anggota->save();

        $actionText = $request->action === 'terima' ? 'diterima' : 'ditolak';
        return redirect()->back()->with('success', "Pendaftaran anggota '{$anggota->nama_lengkap}' berhasil {$actionText}.");
    }

    /**
     * Display membership list in admin panel.
     */
    public function anggota(Request $request)
    {
        // Auto-seed mockup data if Budi Santoso doesn't exist
        if (!Anggota::where('nama_lengkap', 'Budi Santoso')->exists()) {
            // Avoid foreign key check issues if any, delete all
            Anggota::query()->delete();

            // Seed Budi Santoso
            Anggota::create([
                'nama_lengkap' => 'Budi Santoso',
                'nik' => '6401010101010001',
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_lahir' => 'Paser',
                'tanggal_lahir' => '1990-01-12',
                'alamat_lengkap' => 'Jl. Melati No. 12',
                'rt' => 'RT01',
                'dusun' => 'Dusun I',
                'no_hp' => '08123456789',
                'email' => 'budi@gmail.com',
                'pekerjaan' => 'Petani',
                'pendidikan' => 'SMA',
                'motivasi' => 'Ingin mengembangkan usaha pertanian',
                'jabatan' => 'Anggota',
                'sumber' => 'Admin',
                'status' => 'diterima',
            ]);

            // Seed Siti Rahma
            Anggota::create([
                'nama_lengkap' => 'Siti Rahma',
                'nik' => '6401010101010002',
                'jenis_kelamin' => 'Perempuan',
                'tempat_lahir' => 'Penajam',
                'tanggal_lahir' => '1995-10-15',
                'alamat_lengkap' => 'RT03 Dusun I',
                'rt' => 'RT03',
                'dusun' => 'Dusun I',
                'no_hp' => '08123456780',
                'email' => 'rahma@mail.com',
                'pekerjaan' => 'Pedagang',
                'pendidikan' => 'SMK',
                'motivasi' => 'Ingin meminjam modal untuk toko kelontong',
                'jabatan' => 'Anggota',
                'sumber' => 'Pendaftaran',
                'status' => 'diterima',
            ]);

            // Seed Ahmad Fauzi
            Anggota::create([
                'nama_lengkap' => 'Ahmad Fauzi',
                'nik' => '6401010101010003',
                'jenis_kelamin' => 'Laki-Laki',
                'tempat_lahir' => 'Balikpapan',
                'tanggal_lahir' => '1988-12-05',
                'alamat_lengkap' => 'RT06 Dusun II',
                'rt' => 'RT06',
                'dusun' => 'Dusun II',
                'no_hp' => '08987654321',
                'email' => 'fauzi@mail.com',
                'pekerjaan' => 'Pekebun',
                'pendidikan' => 'SMP',
                'motivasi' => 'Meningkatkan hasil panen kelapa sawit',
                'jabatan' => 'Anggota',
                'sumber' => 'Pendaftaran',
                'status' => 'menunggu',
            ]);
        }

        $status = $request->query('status');
        $search = $request->query('search');
        $jabatan = $request->query('jabatan');

        $query = Anggota::query();

        if ($status && $status !== 'Semua') {
            if ($status === 'aktif') {
                $query->where('status', 'diterima');
            } elseif ($status === 'menunggu') {
                $query->where('status', 'menunggu');
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        if ($jabatan && $jabatan !== 'Semua') {
            $query->where('jabatan', $jabatan);
        }

        // Stats
        $statTotal = Anggota::count();
        $statAktif = Anggota::where('status', 'diterima')->count();
        $statMenunggu = Anggota::where('status', 'menunggu')->count();
        $statDitolak = Anggota::where('status', 'ditolak')->count();

        // Get unique dusun list (fallback if needed elsewhere)
        $dusunList = Anggota::select('dusun')->distinct()->whereNotNull('dusun')->pluck('dusun')->toArray();

        $anggotaList = $query->orderBy('status', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.anggota', compact('anggotaList', 'status', 'search', 'jabatan', 'statTotal', 'statAktif', 'statMenunggu', 'statDitolak', 'dusunList'));
    }

    /**
     * Store new member.
     */
    public function simpanAnggota(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nik' => 'required|string|size:16|unique:anggota,nik',
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap' => 'required|string|max:255',
            'rt' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:50',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'pendidikan' => 'nullable|string|max:20',
            'motivasi' => 'nullable|string',
            'jabatan' => 'required|string|max:50',
            'sumber' => 'required|in:Admin,Pendaftaran',
            'status' => 'required|in:menunggu,diterima,ditolak',
        ]);

        Anggota::create($request->all());

        return redirect()->route('admin.data-anggota')->with('success', 'Data anggota berhasil ditambahkan.');
    }

    /**
     * Update member.
     */
    public function updateAnggota(Request $request, $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'nik' => 'required|string|size:16|unique:anggota,nik,' . $id,
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap' => 'required|string|max:255',
            'rt' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:50',
            'no_hp' => 'required|string|max:20',
            'email' => 'nullable|email|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'pendidikan' => 'nullable|string|max:20',
            'motivasi' => 'nullable|string',
            'jabatan' => 'required|string|max:50',
            'sumber' => 'required|in:Admin,Pendaftaran',
            'status' => 'required|in:menunggu,diterima,ditolak',
        ]);

        $anggota = Anggota::findOrFail($id);
        $anggota->update($request->all());

        return redirect()->route('admin.data-anggota')->with('success', 'Data anggota berhasil diperbarui.');
    }

    /**
     * Delete a member.
     */
    public function hapusAnggota($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete();

        return redirect()->route('admin.data-anggota')->with('success', 'Data anggota berhasil dihapus.');
    }
}
