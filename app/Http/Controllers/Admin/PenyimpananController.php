<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriPenyimpanan;
use App\Models\PenyimpananFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PenyimpananController extends Controller
{
    /**
     * Menampilkan daftar penyimpanan berkas beserta statistik.
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
     * Memproses dan mengunggah berkas baru.
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
        $safeName     = $this->buildSafeName($originalName, $ext);

        $uploadPath = public_path('uploads');
        if (!File::isDirectory($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true, true);
        }

        $size = $file->getSize();
        $mime = $file->getMimeType();
        $file->move($uploadPath, $safeName);

        PenyimpananFile::create([
            'nama_file'  => $safeName,
            'nama_asli'  => $originalName,
            'kategori'   => $request->kategori,
            'ukuran'     => $size,
            'tipe'       => substr($mime, 0, 80),
            'keterangan' => $request->keterangan ?? '',
        ]);

        return redirect()->route('admin.penyimpanan');
    }

    /**
     * Memproses dan memperbarui nama, kategori, serta keterangan berkas.
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

        return redirect()->route('admin.penyimpanan');
    }

    /**
     * Menghapus file dari penyimpanan fisik dan basis data.
     */
    public function destroy($id)
    {
        $item     = PenyimpananFile::findOrFail($id);
        $filePath = public_path('uploads/' . $item->nama_file);

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $item->delete();

        return redirect()->route('admin.penyimpanan');
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
     * Menghapus kategori penyimpanan berkas yang dipilih.
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
    // Fungsi pembantu privat
    // -------------------------------------------------------------------------

    /**
     * Menyusun array statistik per kategori untuk tampilan view.
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
     * Menentukan nama tampilan untuk berkas yang diunggah.
     * Menerapkan nama kustom (jika diisi) dan membatasi maksimal 150 karakter.
     */
    private function resolveOriginalName($file, ?string $customName, string $ext): string
    {
        $name = $file->getClientOriginalName();

        if ($customName) {
            // Menambahkan ekstensi jika nama kustom belum memilikinya
            if ($ext && !str_ends_with(strtolower($customName), '.' . strtolower($ext))) {
                $customName .= '.' . $ext;
            }
            $name = $customName;
        }

        return $this->truncateFilename($name, $ext, 150);
    }

    /**
     * Menghasilkan nama berkas aman yang disimpan pada direktori fisik.
     * Membatasi panjang maksimal 255 karakter pada direktori.
     */
    private function buildSafeName(string $originalName, string $ext): string
    {
        $safe = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

        return $this->truncateFilename($safe, $ext, 255);
    }

    /**
     * Memotong nama berkas sesuai batas $maxLen karakter dengan tetap mempertahankan ekstensinya.
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
