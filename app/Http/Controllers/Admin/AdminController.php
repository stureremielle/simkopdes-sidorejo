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
use App\Models\KategoriLayanan;

class AdminController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function dashboard()
    {
        $totalAnggota = Anggota::where('status', 'diterima')->count();
        $produkAktif  = Layanan::where('status', 'aktif')->count();
        $kategoriProdukCount = Layanan::where('status', 'aktif')
            ->whereNotNull('kategori_id')
            ->distinct()
            ->count('kategori_id');
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
            'kategoriProdukCount',
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
            'pekerjaan' => 'nullable|string|max:30',
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
            'pekerjaan' => 'nullable|string|max:30',
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
