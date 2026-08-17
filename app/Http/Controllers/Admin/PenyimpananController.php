<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPenyimpanan;
use App\Models\PenyimpananFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class PenyimpananController extends Controller
{
    /**
     * Display file storage listing with stats.
     */
    public function index(Request $request)
    {
        $filterKat = $request->query('kat');

        $query = PenyimpananFile::query();
        if ($filterKat) {
            $matchingCat = KategoriPenyimpanan::where('nama', $filterKat)->first();
            $query->where('kategori_id', $matchingCat ? $matchingCat->id : 0);
        }

        $fileList   = $query->orderBy('uploaded_at', 'desc')->get();
        $totalFiles = PenyimpananFile::count();
        $totalSize  = PenyimpananFile::sum('ukuran') ?: 0;
        $statsKat   = $this->buildCategoryStats();
        $kategoriList = KategoriPenyimpanan::pluck('nama')->toArray();

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
     * Upload a new file to ZTE NAS.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file_upload' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    if (!$value || !$value->isValid()) {
                        $fail('Unggahan harus berupa file yang valid.');
                        return;
                    }
                    $allowedExts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp'];
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, $allowedExts)) {
                        $fail('Format file tidak didukung. Hanya file PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, dan WEBP yang diizinkan.');
                    }
                }
            ],
            'nama_file'   => 'nullable|string|max:150',
            'kategori'    => 'required|string|max:20',
            'keterangan'  => 'required|string',
        ], [
            'file_upload.required' => 'File wajib diunggah.',
            'file_upload.file'     => 'Unggahan harus berupa file yang valid.',
            'kategori.required'    => 'Kategori wajib dipilih.',
            'keterangan.required'  => 'Keterangan wajib diisi.',
        ]);

        $file         = $request->file('file_upload');
        $ext          = $file->getClientOriginalExtension();
        $originalName = $this->resolveOriginalName($file, $request->input('nama_file'), $ext);
        
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
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => "Gagal mengunggah file ke NAS: " . $errMsg], 400);
                }
                return redirect()->back()->withInput()->with('error', "Gagal mengunggah file ke NAS: " . $errMsg);
            }

            // Dapatkan nama file yang di-sanitize dari respon Node.js NAS
            $safeName = $response->json('filename');

            // Simpan record metadata di database lokal Alwaysdata
            PenyimpananFile::create([
                'nama_file'  => $safeName,
                'nama_asli'  => $originalName,
                'kategori'   => $request->kategori,
                'ukuran'     => $size,
                'tipe'       => substr($mime, 0, 80),
                'keterangan' => $request->keterangan ?? '',
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "File \"{$originalName}\" berhasil diunggah ke NAS."
                ]);
            }

            return redirect()->route('admin.penyimpanan')
                ->with('success', "File \"{$originalName}\" berhasil diunggah ke NAS.");

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Gagal terhubung ke NAS Server: " . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', "Gagal terhubung ke NAS Server: " . $e->getMessage());
        }
    }

    /**
     * Edit file description and category.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_asli'  => 'required|string|max:150',
            'kategori'   => 'required|string|max:20',
            'keterangan' => 'nullable|string',
        ]);

        $item = PenyimpananFile::findOrFail($id);
        $item->update([
            'nama_asli'  => $request->nama_asli,
            'kategori'   => $request->kategori,
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('admin.penyimpanan')
            ->with('success', 'File berhasil diperbarui.');
    }

    /**
     * Delete file from storage and database.
     */
    public function destroy($id)
    {
        $item     = PenyimpananFile::findOrFail($id);
        
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

        $item->delete();

        return redirect()->route('admin.penyimpanan')
            ->with('success', 'File berhasil dihapus.');
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
     * Preview file via NAS API streaming (Inline display for PDFs and images).
     */
    public function preview($id)
    {
        $item = PenyimpananFile::findOrFail($id);

        $nasUrl = config('services.nas.url');
        $nasKey = config('services.nas.key');

        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $nasUrl . '/download/' . $item->nama_file, [
                'headers' => [
                    'X-API-KEY' => $nasKey
                ],
                'stream' => true,
            ]);

            $body = $response->getBody();

            return response()->stream(function () use ($body) {
                while (!$body->eof()) {
                    echo $body->read(1024 * 8);
                    flush();
                }
            }, 200, [
                'Content-Type' => $response->getHeaderLine('Content-Type') ?: ($item->tipe ?: 'application/octet-stream'),
                'Content-Length' => $response->getHeaderLine('Content-Length') ?: $item->ukuran,
                'Content-Disposition' => 'inline; filename="' . rawurlencode($item->nama_asli) . '"',
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal terhubung ke NAS untuk pratinjau berkas: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate(['kategori' => 'required|string|max:20']);

        $newCat = trim($request->kategori);
        $exists = KategoriPenyimpanan::whereRaw('LOWER(nama) = ?', [strtolower($newCat)])->exists();

        if (!$exists) {
            KategoriPenyimpanan::create(['nama' => ucwords($newCat)]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kategori tersebut sudah terdaftar.'], 400);
    }

    /**
     * Remove the specified category.
     */
    public function destroyCategory($kategori)
    {
        $cat = KategoriPenyimpanan::where('nama', $kategori)->first();

        if (!$cat) {
            return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 400);
        }

        if (PenyimpananFile::where('kategori_id', $cat->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus karena kategori ini masih digunakan pada berkas.'], 400);
        }

        $cat->delete();

        return response()->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Build the per-category stats array used by the view.
     *
     * @return array<string, array{c: int, total: int}>
     */
    private function buildCategoryStats(): array
    {
        $rawStats      = PenyimpananFile::selectRaw('kategori_id, COUNT(*) as c, SUM(ukuran) as total')
            ->groupBy('kategori_id')
            ->get();
        $allCategories = KategoriPenyimpanan::all()->keyBy('id');

        $stats = [];
        foreach ($rawStats as $row) {
            $catName        = $allCategories->get($row->kategori_id)?->nama ?? 'Lainnya';
            $stats[$catName] = ['c' => $row->c, 'total' => $row->total];
        }

        return $stats;
    }

    /**
     * Resolve the display name for the uploaded file.
     * Applies the custom name (if given) and enforces the 150-char DB limit.
     */
    private function resolveOriginalName($file, ?string $customName, string $ext): string
    {
        $name = $file->getClientOriginalName();

        if ($customName) {
            // Append extension if the custom name is missing it
            if ($ext && !str_ends_with(strtolower($customName), '.' . strtolower($ext))) {
                $customName .= '.' . $ext;
            }
            $name = $customName;
        }

        return $this->truncateFilename($name, $ext, 150);
    }

    /**
     * Generate the safe (sanitised) filename stored on disk.
     * Enforces the 255-char on-disk limit.
     */
    private function buildSafeName(string $originalName, string $ext): string
    {
        $safe = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

        return $this->truncateFilename($safe, $ext, 255);
    }

    /**
     * Truncate a filename to $maxLen characters while preserving its extension.
     */
    private function truncateFilename(string $filename, string $ext, int $maxLen): string
    {
        if (strlen($filename) <= $maxLen) {
            return $filename;
        }

        $dotExt = $ext ? '.' . $ext : '';
        $limit  = $maxLen - strlen($dotExt);
        $base   = pathinfo($filename, PATHINFO_FILENAME);

        return substr($base, 0, $limit) . $dotExt;
    }
}
