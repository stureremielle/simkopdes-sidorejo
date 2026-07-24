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
     * Display a listing of the resource.
     */
    public function index()
    {
        // Featured news
        $featured = Berita::where('is_featured', 1)
            ->where('status', 'tayang')
            ->first();

        // Featured products selected by admin, fallback to 3 latest active
        $products = Layanan::where('is_featured', true)->where('status', 'aktif')->get();
        if ($products->isEmpty()) {
            $products = Layanan::where('status', 'aktif')->orderBy('id', 'desc')->take(3)->get();
        }

        // Active announcements sorted by latest date (all active data)
        $announcements = Pengumuman::where('status', 'Aktif')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('pages.index', compact('featured', 'products', 'announcements'));
    }

    /**
     * Announcement detail page.
     */
    public function detailPengumuman($id)
    {
        $pengumuman = Pengumuman::where('status', 'Aktif')->findOrFail($id);

        return view('pages.detail-pengumuman', compact('pengumuman'));
    }

    /**
     * Services page.
     */
    public function layanan(Request $request)
    {
        $kategori = $request->query('category');
        $query = Layanan::where('status', 'aktif');

        if ($kategori) {
            $matchingCat = \App\Models\KategoriLayanan::where('nama', $kategori)->first();
            $query->where('kategori_id', $matchingCat ? $matchingCat->id : 0);
        }

        $layananList = $query->orderBy('kategori_id')->orderBy('nama')->get();
        
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
     * News page.
     */
    public function berita(Request $request)
    {
        $featured = Berita::where('status', 'tayang')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $query = Berita::where('status', 'tayang');
        if ($featured) {
            $query->where('id', '!=', $featured->id);
        }

        $totalCount = (clone $query)->count();

        // If AJAX request, load more articles based on offset
        if ($request->ajax()) {
            $offset = (int) $request->input('offset', 6);
            $limit = 6;
            
            $additionalArticles = (clone $query)->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->skip($offset)
                ->take($limit)
                ->get();
                
            $totalRemaining = $totalCount - ($offset + $additionalArticles->count());
            
            $html = '';
            foreach ($additionalArticles as $a) {
                // Card image url
                $cardImg = $a->gambar_url;
                $cardImgUrl = \Str::startsWith($cardImg, 'http') ? $cardImg : (\Str::startsWith($cardImg, 'uploads/') || \Str::startsWith($cardImg, 'storage/') || \Str::startsWith($cardImg, '/') ? asset(ltrim($cardImg, '/')) : asset('assets/images/' . $cardImg));
                if (!$cardImg) {
                    $cardImgUrl = 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400&fit=crop&q=80';
                }
                
                $formattedDate = \App\Helpers\Helper::formatTanggal($a->tanggal_publikasi ?? ($a->created_at ? $a->created_at->toDateString() : date('Y-m-d')));
                $detailUrl = route('berita.detail', $a->id);
                
                $html .= '
                <article class="berita-card">
                     <div class="card-img-wrapper">
                         <img src="' . $cardImgUrl . '" alt="' . e($a->judul) . '">
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

        $artikel = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->take(6)->get();

        $badgeColors = [
            'Pertanian' => 'badge-pertanian',
            'Kerajinan' => 'badge-kerajinan',
            'Keuangan' => 'badge-keuangan',
            'Umum' => 'badge-umum'
        ];

        return view('pages.berita', compact('featured', 'artikel', 'badgeColors'));
    }

    /**
     * Gallery page.
     */
    public function galeri()
    {
        $galeriList = Galeri::where('status', 'aktif')
            ->orderBy('created_at', 'desc')
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
     * About page.
     */
    public function tentang()
    {
        $settingsQuery = Pengaturan::all();
        $settings = [];
        foreach ($settingsQuery as $row) {
            $settings[$row->key_name] = $row->value;
        }

        // Fetch dynamic metrics for "Persebaran Anggota"
        $activeMembers = Anggota::where('status', 'diterima')->get();
        $totalAnggota = $activeMembers->count();

        // 1. Calculate active RT count (Jumlah RT)
        // Normalize RT names to find the unique ones
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
        $jumlahRt = max(8, count($uniqueRts)); // Fallback to min 8 RTs as in list or count actual ones

        // 2. Count "Anggota Baru" (in current year, e.g. 2026 or whatever current year is)
        $currentYear = date('Y');
        $anggotaBaru = Anggota::where('status', 'diterima')
            ->whereYear('created_at', $currentYear)
            ->count();

        // 3. RT breakdown data
        // Pre-populate RT 01 to RT 08 with default values
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

        // Helper to normalize dusun
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

        // Populate with database counts
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

        // Calculate percentages
        foreach ($rtData as $rtName => &$data) {
            if ($totalAnggota > 0) {
                $data['pct'] = round(($data['count'] / $totalAnggota) * 100);
            } else {
                $data['pct'] = 0;
            }
        }
        unset($data);

        // Sort by RT name so RT 01 comes first, then RT 02, etc.
        ksort($rtData);

        return view('pages.tentang', compact('settings', 'totalAnggota', 'jumlahRt', 'anggotaBaru', 'currentYear', 'rtData'));
    }

    /**
     * Contact page.
     */
    public function kontak()
    {
        return view('pages.kontak');
    }

    /**
     * Registration form.
     */
    public function daftar()
    {
        return view('pages.daftar');
    }

    /**
     * Store registration.
     */
    public function prosesDaftar(Request $request)
    {
        $request->validate([
            'namaLengkap' => 'required|string|max:150',
            'nikKtp' => ['required', 'string', 'regex:/^[0-9]{16}$/'],
            'jenisKelamin' => 'required|in:Laki-Laki,Perempuan',
            'tempatLahir' => 'required|string|max:100',
            'tanggalLahir' => 'required|date',
            'alamatLengkap' => 'required|string|max:255',
            'rtSelect' => 'required|string|max:10',
            'dusunSelect' => 'required|string|max:50',
            'noHp' => ['required', 'string', 'regex:/^08[0-9]{8,18}$/'],
            'email' => 'nullable|email|max:100',
            'pekerjaan' => 'required|string|max:30',
            'pendidikan' => 'required|string|max:20',
            'motivasi' => 'required|string',
        ], [
            'nikKtp.regex' => 'NIK wajib berupa 16 digit angka.',
            'noHp.regex' => 'Nomor HP / WhatsApp wajib diawali dengan 08 dan hanya boleh berisi angka.',
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

        $successMessage = "Pendaftaran atas nama <strong>" . htmlspecialchars($request->namaLengkap) . "</strong> berhasil dikirim! Kami akan memverifikasi dan menghubungi Anda segera.";

        return redirect()->route('daftar')->with('success', $successMessage);
    }

    /**
     * News detail page.
     */
    public function detailBerita($id)
    {
        $berita = Berita::where('status', 'tayang')->findOrFail($id);
        $related = Berita::where('status', 'tayang')
            ->where('id', '!=', $id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Fetch news background elements
        $featured = Berita::where('status', 'tayang')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        $query = Berita::where('status', 'tayang');
        if ($featured) {
            $query->where('id', '!=', $featured->id);
        }
        $artikel = $query->orderBy('created_at', 'desc')->take(6)->get();

        $badgeColors = [
            'Pertanian' => 'badge-pertanian',
            'Kerajinan' => 'badge-kerajinan',
            'Keuangan' => 'badge-keuangan',
            'Umum' => 'badge-umum'
        ];

        return view('pages.detail-berita', compact('berita', 'related', 'featured', 'artikel', 'badgeColors'));
    }
}
