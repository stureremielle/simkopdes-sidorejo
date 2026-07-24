<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PenyimpananFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PenyimpananController extends Controller
{
    /**
     * Display file storage.
     */
    public function index(Request $request)
    {
        if (PenyimpananFile::count() === 0) {
            $initialFiles = [
                [
                    'nama_file' => 'ad_art_koperasi.pdf',
                    'nama_asli' => 'AD_ART_Koperasi.pdf',
                    'kategori' => 'Legalitas',
                    'ukuran' => 1258291,
                    'tipe' => 'application/pdf',
                    'keterangan' => 'Anggaran Dasar & Anggaran Rumah Tangga Koperasi',
                    'uploaded_at' => '2024-01-15 08:30:00'
                ],
                [
                    'nama_file' => 'laporan_rat_2023.pdf',
                    'nama_asli' => 'Laporan_RAT_2023.pdf',
                    'kategori' => 'Laporan',
                    'ukuran' => 3565158,
                    'tipe' => 'application/pdf',
                    'keterangan' => 'Laporan Pertanggungjawaban RAT Buku 2023',
                    'uploaded_at' => '2023-12-20 14:15:00'
                ],
                [
                    'nama_file' => 'sk_pengurus_2024.pdf',
                    'nama_asli' => 'SK_Pengurus_2024.pdf',
                    'kategori' => 'Legalitas',
                    'ukuran' => 524288,
                    'tipe' => 'application/pdf',
                    'keterangan' => 'Surat Keputusan Pengurus 2024',
                    'uploaded_at' => '2024-01-18 10:00:00'
                ],
                [
                    'nama_file' => 'laporan_keuangan_2023.xlsx',
                    'nama_asli' => 'Laporan_Keuangan_2023.xlsx',
                    'kategori' => 'Keuangan',
                    'ukuran' => 2202009,
                    'tipe' => 'application/vnd.ms-excel',
                    'keterangan' => 'Laporan Keuangan Tahunan 2023',
                    'uploaded_at' => '2023-12-28 16:45:00'
                ],
                [
                    'nama_file' => 'daftar_anggota_2024.pdf',
                    'nama_asli' => 'Daftar_Anggota_2024.pdf',
                    'kategori' => 'Keanggotaan',
                    'ukuran' => 943718,
                    'tipe' => 'application/pdf',
                    'keterangan' => 'Daftar Anggota Aktif 2024',
                    'uploaded_at' => '2024-03-05 11:20:00'
                ],
                [
                    'nama_file' => 'shu_2023.pdf',
                    'nama_asli' => 'SHU_2023.pdf',
                    'kategori' => 'Keuangan',
                    'ukuran' => 1572864,
                    'tipe' => 'application/pdf',
                    'keterangan' => 'Rincian Pembagian SHU 2023',
                    'uploaded_at' => '2024-01-10 09:10:00'
                ]
            ];
            foreach ($initialFiles as $fileData) {
                PenyimpananFile::create($fileData);
            }
        }

        $filterKat = $request->query('kat');

        $query = PenyimpananFile::query();
        if ($filterKat) {
            $matchingCat = \App\Models\KategoriPenyimpanan::where('nama', $filterKat)->first();
            $query->where('kategori_id', $matchingCat ? $matchingCat->id : 0);
        }

        $fileList = $query->orderBy('uploaded_at', 'desc')->get();

        // Get total stats
        $totalFiles = PenyimpananFile::count();
        $totalSize = PenyimpananFile::sum('ukuran') ?: 0;

        // Group size and count stats per category_id
        $statsKatQuery = PenyimpananFile::selectRaw('kategori_id, COUNT(*) as c, SUM(ukuran) as total')
            ->groupBy('kategori_id')
            ->get();

        $allCategories = \App\Models\KategoriPenyimpanan::all()->keyBy('id');
        $statsKat = [];
        foreach ($statsKatQuery as $stat) {
            $catName = $allCategories->get($stat->kategori_id)?->nama ?? 'Lainnya';
            $statsKat[$catName] = [
                'c' => $stat->c,
                'total' => $stat->total
            ];
        }

        $kategoriList = \App\Models\KategoriPenyimpanan::pluck('nama')->toArray();

        return view('admin.penyimpanan', compact(
            'fileList',
            'kategoriList',
            'filterKat',
            'totalFiles',
            'totalSize',
            'statsKat'
        ));
    }

    /**
     * Upload a new file.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file_upload' => 'required|file|max:20480', // limit 20MB
            'nama_file' => 'nullable|string|max:255',
            'kategori' => 'required|string|max:30',
            'keterangan' => 'nullable|string',
        ]);

        $file = $request->file('file_upload');
        $originalName = $file->getClientOriginalName();

        // Gunakan nama file kustom jika diisi
        if ($request->filled('nama_file')) {
            $customName = $request->nama_file;
            $ext = $file->getClientOriginalExtension();
            if ($ext && !str_ends_with(strtolower($customName), '.' . strtolower($ext))) {
                $customName .= '.' . $ext;
            }
            $originalName = $customName;
        }

        // Truncate originalName to fit 255 character limit while preserving its extension
        $ext = $file->getClientOriginalExtension();
        if (strlen($originalName) > 255) {
            $limit = 255 - (strlen($ext) ? strlen($ext) + 1 : 0);
            $base = pathinfo($originalName, PATHINFO_FILENAME);
            $originalName = substr($base, 0, $limit) . (strlen($ext) ? '.' . $ext : '');
        }
        
        $size = $file->getSize();
        $mime = $file->getMimeType();

        // Konfigurasi NAS API dari config
        $nasUrl = config('services.nas.url');
        $nasKey = config('services.nas.key');

        try {
            // Kirim file ke REST API NAS (ZTE TV Box) via Multipart Form Data
            $response = Http::withHeaders([
                'X-API-KEY' => $nasKey
            ])->attach(
                'file_upload',
                file_get_contents($file->getRealPath()),
                $originalName
            )->post($nasUrl . '/upload', [
                'kategori' => $request->kategori,
                'uploader' => auth()->user()->username ?? 'Admin'
            ]);

            if ($response->failed()) {
                $errMsg = $response->json('message') ?? 'Server NAS mengembalikan status error ' . $response->status();
                return redirect()->back()->withInput()->with('error', "Gagal mengunggah file ke NAS: " . $errMsg);
            }

            // Dapatkan nama file yang di-sanitize dari respon Node.js NAS
            $safeName = $response->json('filename');

            // Simpan record metadata di database lokal Alwaysdata
            PenyimpananFile::create([
                'nama_file' => $safeName,
                'nama_asli' => $originalName,
                'kategori' => $request->kategori,
                'ukuran' => $size,
                'tipe' => substr($mime, 0, 50),
                'keterangan' => $request->keterangan ?? '',
            ]);

            return redirect()->route('admin.penyimpanan')->with('success', "File \"{$originalName}\" berhasil diunggah ke NAS.");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', "Gagal terhubung ke NAS Server: " . $e->getMessage());
        }
    }

    /**
     * Edit file description and category.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_asli' => 'required|string|max:255',
            'kategori' => 'required|string|max:30',
            'keterangan' => 'nullable|string',
        ]);

        $item = PenyimpananFile::findOrFail($id);
        $item->update([
            'nama_asli' => $request->nama_asli,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('admin.penyimpanan')->with('success', 'File berhasil diperbarui.');
    }

    /**
     * Delete file from storage and database.
     */
    public function destroy($id)
    {
        $item = PenyimpananFile::findOrFail($id);
        
        $nasUrl = config('services.nas.url');
        $nasKey = config('services.nas.key');

        try {
            // Hapus file secara fisik dari server NAS
            $response = Http::withHeaders([
                'X-API-KEY' => $nasKey
            ])->delete($nasUrl . '/' . $item->nama_file);

            if ($response->failed()) {
                logger()->warning("Gagal menghapus file fisik {$item->nama_file} di NAS: " . $response->status());
            }
        } catch (\Exception $e) {
            logger()->error("Koneksi gagal saat menghapus file {$item->nama_file} di NAS: " . $e->getMessage());
        }

        // Hapus record dari database lokal
        $item->delete();

        return redirect()->route('admin.penyimpanan')->with('success', 'File berhasil dihapus.');
    }

    /**
     * Download file via NAS API streaming (Chunked streaming to save RAM).
     */
    public function download($id)
    {
        $item = PenyimpananFile::findOrFail($id);

        $nasUrl = config('services.nas.url');
        $nasKey = config('services.nas.key');

        try {
            // Gunakan Guzzle client langsung untuk streaming respon
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $nasUrl . '/download/' . $item->nama_file, [
                'headers' => [
                    'X-API-KEY' => $nasKey
                ],
                'stream' => true, // Mengaktifkan opsi stream Guzzle
            ]);

            $body = $response->getBody();

            return response()->stream(function () use ($body) {
                while (!$body->eof()) {
                    echo $body->read(1024 * 8); // Mengalirkan data per 8KB
                    flush();
                }
            }, 200, [
                'Content-Type' => $response->getHeaderLine('Content-Type') ?: ($item->tipe ?: 'application/octet-stream'),
                'Content-Length' => $response->getHeaderLine('Content-Length') ?: $item->ukuran,
                'Content-Disposition' => 'attachment; filename="' . rawurlencode($item->nama_asli) . '"',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke NAS untuk unduh berkas: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:50',
        ]);

        $newCat = trim($request->kategori);
        
        $exists = \App\Models\KategoriPenyimpanan::whereRaw('LOWER(nama) = ?', [strtolower($newCat)])->exists();

        if (!$exists) {
            \App\Models\KategoriPenyimpanan::create(['nama' => ucwords($newCat)]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kategori tersebut sudah terdaftar.'], 400);
    }

    /**
     * Remove the specified category.
     */
    public function destroyCategory($kategori)
    {
        $catToDelete = $kategori;

        // Check if there are any files using this category
        $cat = \App\Models\KategoriPenyimpanan::where('nama', $catToDelete)->first();
        if ($cat) {
            $hasFiles = PenyimpananFile::where('kategori_id', $cat->id)->count();
            if ($hasFiles > 0) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus karena kategori ini masih digunakan pada berkas.'], 400);
            }
            $cat->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 400);
    }
}
