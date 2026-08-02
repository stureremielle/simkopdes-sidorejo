<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\Layanan;
use App\Models\Galeri;
use App\Models\Berita;

class AdminController extends Controller
{
    /**
     * Menampilkan dasbor admin.
     */
    public function dashboard()
    {
        $totalAnggota = Anggota::where('status', 'diterima')->count();
        $layananAktif = Layanan::where('status', 'aktif')->count();
        $kategoriLayananCount = Layanan::where('status', 'aktif')
            ->whereNotNull('kategori_id')
            ->distinct()
            ->count('kategori_id');
        $fotoGaleri   = Galeri::where('status', 'aktif')->count();
        $kategoriGaleriCount = Galeri::where('status', 'aktif')
            ->whereNotNull('kategori_id')
            ->distinct()
            ->count('kategori_id');

        // Berita terbaru
        $beritaTerbaru = Berita::orderByRaw('COALESCE(tanggal_publikasi, DATE(created_at)) DESC')->orderBy('id', 'desc')->take(5)->get();

        // Daftar pendaftaran anggota yang menunggu verifikasi
        $pendaftaranList = Anggota::where('status', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalAnggota',
            'layananAktif',
            'kategoriLayananCount',
            'fotoGaleri',
            'kategoriGaleriCount',
            'beritaTerbaru',
            'pendaftaranList'
        ));
    }

    /**
     * Memproses verifikasi pendaftaran anggota (terima atau tolak).
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

        return redirect()->back();
    }

    /**
     * Menampilkan daftar data anggota pada panel admin.
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

        // Perhitungan statistik ringkasan anggota
        $statTotal = Anggota::count();
        $statAktif = Anggota::where('status', 'diterima')->count();
        $statMenunggu = Anggota::where('status', 'menunggu')->count();
        $statDitolak = Anggota::where('status', 'ditolak')->count();

        // Mengambil daftar nama dusun unik
        $dusunList = Anggota::select('dusun')->distinct()->whereNotNull('dusun')->pluck('dusun')->toArray();

        $anggotaList = $query->orderBy('status', 'asc')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.anggota', compact('anggotaList', 'status', 'search', 'jabatan', 'statTotal', 'statAktif', 'statMenunggu', 'statDitolak', 'dusunList'));
    }

    /**
     * Memproses dan menyimpan data anggota baru.
     */
    public function simpanAnggota(Request $request)
    {
        $messages = [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 40 karakter.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.regex' => 'NIK wajib berupa 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.max' => 'Tempat lahir maksimal 25 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
            'alamat_lengkap.max' => 'Alamat lengkap maksimal 80 karakter.',
            'rt.required' => 'RT wajib dipilih.',
            'dusun.required' => 'Dusun wajib dipilih.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'no_hp.regex' => 'Nomor HP / WhatsApp wajib diawali dengan 08 dan hanya boleh berisi angka.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 60 karakter.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'pekerjaan.max' => 'Pekerjaan maksimal 20 karakter.',
            'pendidikan.required' => 'Pendidikan terakhir wajib dipilih.',
            'motivasi.required' => 'Motivasi bergabung wajib diisi.',
            'jabatan.required' => 'Jabatan wajib dipilih.',
            'status.required' => 'Status keanggotaan wajib dipilih.',
        ];

        $request->validate([
            'nama_lengkap' => 'required|string|max:40',
            'nik' => ['required', 'string', 'regex:/^[0-9]{16}$/', 'unique:anggota,nik'],
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir' => 'required|string|max:25',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap' => 'required|string|max:80',
            'rt' => 'required|string|max:5',
            'dusun' => 'required|string|max:8',
            'no_hp' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'email' => 'nullable|email|max:60',
            'pekerjaan' => 'required|string|max:20',
            'pendidikan' => 'required|string|max:10',
            'motivasi' => 'required|string',
            'jabatan' => 'required|string|max:20',
            'sumber' => 'required|in:Admin,Pendaftaran',
            'status' => 'required|in:menunggu,diterima,ditolak',
        ], $messages);

        Anggota::create($request->all());

        return redirect()->route('admin.data-anggota');
    }

    /**
     * Memproses pembaruan data anggota.
     */
    public function updateAnggota(Request $request, $id)
    {
        $messages = [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max' => 'Nama lengkap maksimal 40 karakter.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.regex' => 'NIK wajib berupa 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.max' => 'Tempat lahir maksimal 25 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
            'alamat_lengkap.max' => 'Alamat lengkap maksimal 80 karakter.',
            'rt.required' => 'RT wajib dipilih.',
            'dusun.required' => 'Dusun wajib dipilih.',
            'no_hp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'no_hp.regex' => 'Nomor HP / WhatsApp wajib diawali dengan 08 dan hanya boleh berisi angka.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 60 karakter.',
            'pekerjaan.required' => 'Pekerjaan wajib diisi.',
            'pekerjaan.max' => 'Pekerjaan maksimal 20 karakter.',
            'pendidikan.required' => 'Pendidikan terakhir wajib dipilih.',
            'motivasi.required' => 'Motivasi bergabung wajib diisi.',
            'jabatan.required' => 'Jabatan wajib dipilih.',
            'status.required' => 'Status keanggotaan wajib dipilih.',
        ];

        $request->validate([
            'nama_lengkap' => 'required|string|max:40',
            'nik' => ['required', 'string', 'regex:/^[0-9]{16}$/', 'unique:anggota,nik,' . $id],
            'jenis_kelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempat_lahir' => 'required|string|max:25',
            'tanggal_lahir' => 'required|date',
            'alamat_lengkap' => 'required|string|max:80',
            'rt' => 'required|string|max:5',
            'dusun' => 'required|string|max:8',
            'no_hp' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'email' => 'nullable|email|max:60',
            'pekerjaan' => 'required|string|max:20',
            'pendidikan' => 'required|string|max:10',
            'motivasi' => 'required|string',
            'jabatan' => 'required|string|max:20',
            'sumber' => 'required|in:Admin,Pendaftaran',
            'status' => 'required|in:menunggu,diterima,ditolak',
        ], $messages);

        $anggota = Anggota::findOrFail($id);
        $anggota->update($request->all());

        return redirect()->route('admin.data-anggota');
    }

    /**
     * Menghapus data anggota.
     */
    public function hapusAnggota($id)
    {
        $anggota = Anggota::findOrFail($id);
        $anggota->delete();

        return redirect()->route('admin.data-anggota');
    }
}
