<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\File;

class BeritaController extends Controller
{
    /**
     * Display a listing of articles.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategoriFilter = $request->query('kategori');
        $statusFilter = $request->query('status'); // 'semua', 'tayang', 'draf'

        $query = Berita::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        if ($kategoriFilter) {
            $matchingCat = \App\Models\KategoriBerita::where('nama', $kategoriFilter)->first();
            $query->where('kategori_id', $matchingCat ? $matchingCat->id : 0);
        }

        if ($statusFilter && $statusFilter !== 'semua') {
            $query->where('status', $statusFilter === 'draf' ? 'draft' : $statusFilter);
        }

        $beritaList = $query->orderBy('created_at', 'desc')->get();

        // Get unique categories for filter dropdown
        $kategoriList = \App\Models\KategoriBerita::pluck('nama')->toArray();

        return view('admin.berita', compact('beritaList', 'kategoriList', 'search', 'kategoriFilter', 'statusFilter'));
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
        
        $exists = \App\Models\KategoriBerita::whereRaw('LOWER(nama) = ?', [strtolower($newCat)])->exists();

        if (!$exists) {
            \App\Models\KategoriBerita::create(['nama' => ucwords($newCat)]);
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

        // Check if there are any articles using this category
        $cat = \App\Models\KategoriBerita::where('nama', $catToDelete)->first();
        if ($cat) {
            $hasArticles = Berita::where('kategori_id', $cat->id)->count();
            if ($hasArticles > 0) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus karena kategori ini masih digunakan pada artikel.'], 400);
            }
            $cat->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 400);
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:80',
            'kategori' => 'required|string|max:50',
            'isi' => 'required|string',
            'penulis' => 'required|string|max:100',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,heic,heif|max:5120',
            'status' => 'required|in:tayang,draft',
            'is_featured' => 'nullable|boolean',
            'tanggal_publikasi' => 'required|date',
        ]);

        $featured = $request->has('is_featured') ? 1 : 0;

        // If this article is featured, reset all other articles' is_featured flag
        if ($featured === 1) {
            Berita::query()->update(['is_featured' => 0]);
        }

        $gambarUrl = '';
        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/berita');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambarUrl = 'uploads/berita/' . $safeName;
        }

        Berita::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'isi' => $request->isi,
            'penulis' => $request->penulis,
            'gambar_url' => $gambarUrl,
            'status' => $request->status,
            'is_featured' => $featured,
            'tanggal_publikasi' => $request->tanggal_publikasi,
        ]);

        return redirect()->route('admin.berita')->with('success', 'Artikel berhasil ditambahkan.');
    }

    /**
     * Update the specified article.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:80',
            'kategori' => 'required|string|max:50',
            'isi' => 'required|string',
            'penulis' => 'required|string|max:100',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,heic,heif|max:5120',
            'status' => 'required|in:tayang,draft',
            'is_featured' => 'nullable|boolean',
            'tanggal_publikasi' => 'required|date',
        ]);

        $berita = Berita::findOrFail($id);
        $featured = $request->has('is_featured') ? 1 : 0;

        // If this article is featured, reset all other articles' is_featured flag
        if ($featured === 1) {
            Berita::query()->update(['is_featured' => 0]);
        }

        $gambarUrl = $berita->gambar_url;
        if ($request->hasFile('gambar_file')) {
            if ($berita->gambar_url) {
                $oldPath = public_path($berita->gambar_url);
                if (File::isFile($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/berita');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambarUrl = 'uploads/berita/' . $safeName;
        }

        $berita->update([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'isi' => $request->isi,
            'penulis' => $request->penulis,
            'gambar_url' => $gambarUrl,
            'status' => $request->status,
            'is_featured' => $featured,
            'tanggal_publikasi' => $request->tanggal_publikasi,
        ]);

        return redirect()->route('admin.berita')->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Remove the specified article.
     */
    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        if ($berita->gambar_url) {
            $oldPath = public_path($berita->gambar_url);
            if (File::isFile($oldPath)) {
                File::delete($oldPath);
            }
        }
        $berita->delete();

        return redirect()->route('admin.berita')->with('success', 'Artikel berhasil dihapus.');
    }
}
