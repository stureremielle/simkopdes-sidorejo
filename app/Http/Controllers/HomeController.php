<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Berita;
use App\Models\Layanan;
use App\Models\Galeri;
use App\Models\Pengaturan;
use App\Models\Anggota;
use App\Models\Pengumuman;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama (beranda).
     */
    public function index()
    {
        // Layanan unggulan yang dipilih admin, opsi cadangan ke 3 layanan aktif terbaru
        $layananList = Layanan::where('is_featured', true)->where('status', 'aktif')->take(3)->get();
        if ($layananList->isEmpty()) {
            $layananList = Layanan::where('status', 'aktif')->orderBy('id', 'desc')->take(3)->get();
        }

        // Pengumuman aktif yang diurutkan berdasarkan tanggal terbaru
        $pengumumanList = Pengumuman::where('status', 'Aktif')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.index', compact('layananList', 'pengumumanList'));
    }

    /**
     * Halaman detail pengumuman.
     */
    public function detailPengumuman($id)
    {
        $pengumuman = Pengumuman::where('status', 'Aktif')->findOrFail($id);

        return view('pages.detail-pengumuman', compact('pengumuman'));
    }

    /**
     * Halaman daftar layanan dan produk.
     */
    public function layanan(Request $request)
    {
        $kategori = $request->query('category');
        $query = Layanan::where('status', 'aktif');

        if ($kategori) {
            $matchingCat = \App\Models\KategoriLayanan::where('nama', $kategori)->first();
            $query->where('kategori_id', $matchingCat ? $matchingCat->id : 0);
        }

        $layananList = $query->orderBy('id', 'desc')->get();
        
        $activeKategoriIds = Layanan::where('status', 'aktif')
            ->distinct()
            ->pluck('kategori_id')
            ->filter();

        $kategoriList = \App\Models\KategoriLayanan::whereIn('id', $activeKategoriIds)
            ->orderBy('nama')
            ->pluck('nama');

        return view('pages.layanan', compact('layananList', 'kategoriList', 'kategori'));
    }

    /**
     * Halaman daftar berita & informasi.
     */
    public function berita(Request $request)
    {
        $query = Berita::where('status', 'tayang');

        $totalCount = (clone $query)->count();

        // Jika permintaan AJAX, muat berita tambahan berdasarkan offset
        if ($request->ajax()) {
            $offset = (int) $request->input('offset', 6);
            $limit = 6;
            
            $additionalArticles = (clone $query)->orderByRaw('COALESCE(tanggal_publikasi, DATE(created_at)) DESC')
                ->orderBy('id', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get();
                
            $totalRemaining = $totalCount - ($offset + $additionalArticles->count());
            
            $html = '';
            foreach ($additionalArticles as $a) {
                // URL gambar kartu berita
                $cardGambar = $a->gambar;
                $cardGambarSrc = '';
                if ($cardGambar) {
                    $cardGambarSrc = \Str::startsWith($cardGambar, 'http') ? $cardGambar : (\Str::startsWith($cardGambar, 'uploads/') || \Str::startsWith($cardGambar, 'storage/') || \Str::startsWith($cardGambar, '/') ? asset(ltrim($cardGambar, '/')) : asset('assets/images/' . $cardGambar));
                }
                
                $formattedDate = \App\Helpers\Helper::formatTanggal($a->tanggal_publikasi ?? ($a->created_at ? $a->created_at->toDateString() : date('Y-m-d')));
                $detailUrl = route('berita.detail', $a->id);
                
                $html .= '
                <article class="berita-card">
                     <div class="card-img-wrapper">
                          <img src="' . $cardGambarSrc . '" alt="' . e($a->judul) . '">
                          <span class="card-badge">' . e($a->kategori) . '</span>
                     </div>
                    <div class="card-body">
                        <div class="card-meta">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                            <span>' . $formattedDate . '</span>
                        </div>
                        <h3 class="card-title">' . e($a->judul) . '</h3>
                        <p class="card-desc">' . e(\Str::limit(strip_tags($a->isi), 130)) . '</p>
                        <a href="' . $detailUrl . '" class="card-footer-link">Baca selengkapnya &rarr;</a>
                    </div>
                </article>';
            }
            
            return response()->json([
                'html' => $html,
                'has_more' => $totalRemaining > 0
            ]);
        }

        $semuaBerita  = $query->orderByRaw('COALESCE(tanggal_publikasi, DATE(created_at)) DESC')->orderBy('id', 'desc')->take(6)->get();
        $beritaUtama  = $semuaBerita->first();
        $daftarBerita = $semuaBerita->slice(1);

        return view('pages.berita', compact('beritaUtama', 'daftarBerita'));
    }

    /**
     * Halaman galeri kegiatan.
     */
    public function galeri()
    {
        $galeriList = Galeri::where('status', 'aktif')
            ->orderBy('id', 'desc')
            ->get();

        $activeCategoryIds = Galeri::where('status', 'aktif')
            ->whereNotNull('kategori_id')
            ->distinct()
            ->pluck('kategori_id')
            ->toArray();

        $categories = \App\Models\KategoriGaleri::whereIn('id', $activeCategoryIds)
            ->pluck('nama')
            ->toArray();
        $kategoriList = collect($categories);

        return view('pages.galeri', compact('galeriList', 'kategoriList'));
    }

    /**
     * Halaman tentang kami (profil koperasi).
     */
    public function tentang()
    {
        $settingsQuery = Pengaturan::all();
        $settings = [];
        foreach ($settingsQuery as $row) {
            $settings[$row->key_name] = $row->value;
        }

        // Mengambil metrik dinamis untuk statistik persebaran anggota
        $activeMembers = Anggota::where('status', 'diterima')->get();
        $totalAnggota = $activeMembers->count();

        // 1. Menghitung jumlah RT aktif
        // Normalisasi nama RT untuk menemukan RT unik
        $normalizeRt = function($rt) {
            $clean = preg_replace('/[^A-Za-z0-9]/', '', $rt);
            $clean = strtoupper($clean);
            if (preg_match('/^RT(\d+)$/', $clean, $matches)) {
                return 'RT ' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            }
            return $rt;
        };

        $uniqueRts = [];
        foreach ($activeMembers as $m) {
            if ($m->rt) {
                $uniqueRts[$normalizeRt($m->rt)] = true;
            }
        }
        $jumlahRt = max(8, count($uniqueRts)); // Opsi cadangan minimal 8 RT sesuai daftar atau hitung jumlah aktual

        // 2. Menghitung jumlah anggota baru pada tahun berjalan
        $currentYear = date('Y');
        $anggotaBaru = Anggota::where('status', 'diterima')
            ->whereYear('created_at', $currentYear)
            ->count();

        // 3. Data rincian per RT
        // Menyiapkan data awal RT 01 hingga RT 08 dengan nilai bawaan
        $rtData = [];
        for ($i = 1; $i <= 8; $i++) {
            $rtName = 'RT ' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $rtDusun = ($i <= 3) ? 'Dusun I' : 'Dusun II';
            $rtData[$rtName] = [
                'name' => $rtName,
                'dusun' => $rtDusun,
                'count' => 0,
                'pct' => 0,
            ];
        }

        // Fungsi pembantu untuk normalisasi nama dusun
        $normalizeDusun = function($dusun) {
            $dusun = trim($dusun);
            if (strcasecmp($dusun, 'DI') === 0 || strcasecmp($dusun, 'Dusun I') === 0) {
                return 'Dusun I';
            }
            if (strcasecmp($dusun, 'DII') === 0 || strcasecmp($dusun, 'Dusun II') === 0) {
                return 'Dusun II';
            }
            if (strcasecmp($dusun, 'DIII') === 0 || strcasecmp($dusun, 'Dusun III') === 0) {
                return 'Dusun III';
            }
            return $dusun;
        };

        // Mengisi data berdasarkan jumlah dari database
        foreach ($activeMembers as $m) {
            if ($m->rt) {
                $normRt = $normalizeRt($m->rt);
                $normDusun = $m->dusun ? $normalizeDusun($m->dusun) : (($normRt <= 'RT 03') ? 'Dusun I' : 'Dusun II');
                
                if (!isset($rtData[$normRt])) {
                    $rtData[$normRt] = [
                        'name' => $normRt,
                        'dusun' => $normDusun,
                        'count' => 0,
                        'pct' => 0,
                    ];
                }
                $rtData[$normRt]['count']++;
                if ($m->dusun) {
                    $rtData[$normRt]['dusun'] = $normDusun;
                }
            }
        }

        // Menghitung persentase
        foreach ($rtData as $rtName => &$data) {
            if ($totalAnggota > 0) {
                $data['pct'] = round(($data['count'] / $totalAnggota) * 100);
            } else {
                $data['pct'] = 0;
            }
        }
        unset($data);

        // Mengurutkan berdasarkan nama RT agar RT 01 berada di urutan pertama
        ksort($rtData);

        return view('pages.tentang', compact('settings', 'totalAnggota', 'jumlahRt', 'anggotaBaru', 'currentYear', 'rtData'));
    }

    /**
     * Halaman kontak.
     */
    public function kontak()
    {
        return view('pages.kontak');
    }

    /**
     * Halaman formulir pendaftaran anggota.
     */
    public function daftar()
    {
        return view('pages.daftar');
    }

    /**
     * Memproses dan menyimpan data pendaftaran anggota.
     */
    public function prosesDaftar(Request $request)
    {
        $request->validate([
            'namaLengkap' => 'required|string|max:40',
            'nikKtp' => ['required', 'string', 'regex:/^[0-9]{16}$/', 'unique:anggota,nik'],
            'jenisKelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempatLahir' => 'required|string|max:25',
            'tanggalLahir' => 'required|date',
            'alamatLengkap' => 'required|string|max:80',
            'rtSelect' => 'required|string|max:5',
            'dusunSelect' => 'required|string|max:8',
            'noHp' => ['required', 'string', 'starts_with:08', 'regex:/^[0-9]+$/', 'min:10', 'max:15'],
            'email' => 'nullable|email|max:60',
            'pekerjaan' => 'required|string|max:20',
            'pendidikan' => 'required|string|max:10',
            'motivasi' => 'required|string',
        ], [
            'nikKtp.required' => 'NIK wajib diisi.',
            'nikKtp.regex' => 'NIK wajib berupa 16 digit angka.',
            'nikKtp.unique' => 'NIK sudah terdaftar.',
            'noHp.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'noHp.starts_with' => 'Nomor HP / WhatsApp harus diawali dengan angka 08.',
            'noHp.regex' => 'Nomor HP / WhatsApp hanya boleh berisi angka.',
            'noHp.min' => 'Nomor HP / WhatsApp harus terdiri dari 10–15 digit.',
            'noHp.max' => 'Nomor HP / WhatsApp maksimal 15 digit.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 60 karakter.',
        ]);


        Anggota::create([
            'nama_lengkap' => $request->namaLengkap,
            'nik' => $request->nikKtp,
            'jenis_kelamin' => $request->jenisKelamin,
            'tempat_lahir' => $request->tempatLahir,
            'tanggal_lahir' => $request->tanggalLahir,
            'alamat_lengkap' => $request->alamatLengkap,
            'rt' => $request->rtSelect,
            'dusun' => $request->dusunSelect,
            'no_hp' => $request->noHp,
            'email' => $request->email,
            'pekerjaan' => $request->pekerjaan,
            'pendidikan' => $request->pendidikan,
            'motivasi' => $request->motivasi,
            'status' => 'menunggu',
        ]);

        return redirect()->route('daftar')->with('success', true);
    }

    /**
     * Halaman detail berita.
     */
    public function detailBerita($id)
    {
        $berita = Berita::where('status', 'tayang')->findOrFail($id);

        $query = Berita::where('status', 'tayang');
        $beritaTerkait = $query->orderByRaw('COALESCE(tanggal_publikasi, DATE(created_at)) DESC')->orderBy('id', 'desc')->take(6)->get();

        return view('pages.detail-berita', compact('berita', 'beritaTerkait'));
    }
}
