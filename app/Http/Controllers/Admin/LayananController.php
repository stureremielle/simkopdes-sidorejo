<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use Illuminate\Support\Facades\File;

class LayananController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index()
    {
        $layananList = Layanan::orderBy('kategori_id')->orderBy('nama')->get();
        
        // 1. Fetch dynamic categories array from KategoriLayanan table
        $categoriesArray = \App\Models\KategoriLayanan::pluck('nama')->toArray();
        
        // 2. Fetch product count grouped by category_id
        $counts = Layanan::selectRaw('kategori_id, count(*) as total, sum(case when status = "aktif" then 1 else 0 end) as active')
            ->groupBy('kategori_id')
            ->get()
            ->keyBy('kategori_id');

        $allCategories = \App\Models\KategoriLayanan::all()->keyBy('id');

        // 3. Build category stats for Tab 2
        $categoriesWithStats = [];
        foreach ($categoriesArray as $catName) {
            $cat = $allCategories->first(fn($c) => strtolower($c->nama) === strtolower($catName));
            $catId = $cat ? $cat->id : null;
            $c = $catId ? $counts->get($catId) : null;
            
            $categoriesWithStats[] = [
                'nama' => $catName,
                'total' => $c ? $c->total : 0,
                'active' => $c ? $c->active : 0
            ];
        }

        $settingsQuery = \App\Models\Pengaturan::all();
        $settings = [];
        foreach ($settingsQuery as $row) {
            $settings[$row->key_name] = $row->value;
        }

        return view('admin.layanan', [
            'layananList' => $layananList,
            'settings' => $settings,
            'categories' => $categoriesArray,
            'categoriesWithStats' => $categoriesWithStats
        ]);
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'kategori' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'harga' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:20',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $gambarUrl = '';
        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/layanan');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambarUrl = 'uploads/layanan/' . $safeName;
        }

        Layanan::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi ?? '',
            'harga' => $request->harga ?? 0,
            'satuan' => $request->satuan ?? 'unit',
            'gambar_url' => $gambarUrl,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.layanan')->with('success', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Update the specified service.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:50',
            'kategori' => 'required|string|max:50',
            'deskripsi' => 'nullable|string',
            'harga' => 'nullable|numeric|min:0',
            'satuan' => 'nullable|string|max:20',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $layanan = Layanan::findOrFail($id);
        $gambarUrl = $layanan->gambar_url;

        if ($request->hasFile('gambar_file')) {
            if ($layanan->gambar_url) {
                $oldPath = public_path($layanan->gambar_url);
                if (File::isFile($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/layanan');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambarUrl = 'uploads/layanan/' . $safeName;
        }

        $layanan->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi ?? '',
            'harga' => $request->harga ?? 0,
            'satuan' => $request->satuan ?? 'unit',
            'gambar_url' => $gambarUrl,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.layanan')->with('success', 'Layanan berhasil diperbarui.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->status = $layanan->status === 'aktif' ? 'nonaktif' : 'aktif';
        $layanan->save();

        return redirect()->route('admin.layanan')->with('success', 'Status layanan berhasil diubah.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);
        if ($layanan->gambar_url) {
            $oldPath = public_path($layanan->gambar_url);
            if (File::isFile($oldPath)) {
                File::delete($oldPath);
            }
        }
        $layanan->delete();

        return redirect()->route('admin.layanan')->with('success', 'Layanan berhasil dihapus.');
    }

    /**
     * Save featured products for homepage.
     */
    public function saveFeatured(Request $request)
    {
        $ids = $request->input('featured_ids', []);
        $ids = array_map('intval', (array) $ids);
        $ids = array_slice($ids, 0, 3); // max 3

        // Reset all
        Layanan::query()->update(['is_featured' => false]);

        // Mark selected
        if (!empty($ids)) {
            Layanan::whereIn('id', $ids)->update(['is_featured' => true]);
        }

        return response()->json(['ok' => true]);
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
        
        $exists = \App\Models\KategoriLayanan::whereRaw('LOWER(nama) = ?', [strtolower($newCat)])->exists();

        if (!$exists) {
            \App\Models\KategoriLayanan::create(['nama' => ucwords($newCat)]);
            return redirect()->route('admin.layanan')->with('success', 'Kategori baru berhasil ditambahkan.');
        }

        return redirect()->route('admin.layanan')->with('error', 'Kategori tersebut sudah terdaftar.');
    }

    /**
     * Remove the specified category.
     */
    public function destroyCategory($kategori)
    {
        $catToDelete = $kategori;

        // Check if there are any products using this category
        $cat = \App\Models\KategoriLayanan::where('nama', $catToDelete)->first();
        if ($cat) {
            $hasProducts = Layanan::where('kategori_id', $cat->id)->count();
            if ($hasProducts > 0) {
                return redirect()->route('admin.layanan')->with('error', 'Tidak dapat menghapus karena kategori ini masih memiliki produk.');
            }
            $cat->delete();
            return redirect()->route('admin.layanan')->with('success', 'Kategori berhasil dihapus.');
        }

        return redirect()->route('admin.layanan')->with('error', 'Kategori tidak ditemukan.');
    }
}
