<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Layanan;
use Illuminate\Support\Facades\File;

class LayananController extends Controller
{
    /**
     * Menampilkan daftar layanan dan produk.
     */
    public function index()
    {
        $layananList = Layanan::orderBy('kategori_id')->orderBy('nama')->get();
        
        // 1. Mengambil array kategori dinamis dari tabel KategoriLayanan
        $categoriesArray = \App\Models\KategoriLayanan::pluck('nama')->toArray();
        
        // 2. Mengambil jumlah produk berdasarkan kategori_id
        $counts = Layanan::selectRaw('kategori_id, count(*) as total, sum(case when status = "aktif" then 1 else 0 end) as active')
            ->groupBy('kategori_id')
            ->get()
            ->keyBy('kategori_id');

        $allCategories = \App\Models\KategoriLayanan::all()->keyBy('id');

        // 3. Menyusun statistik kategori untuk Tab 2
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
     * Memproses dan menyimpan data produk layanan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:30',
            'kategori' => 'required|string|max:20',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:10',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama.required'      => 'Nama produk wajib diisi.',
            'nama.max'           => 'Nama produk maksimal 30 karakter.',
            'kategori.required'  => 'Kategori wajib dipilih.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'harga.required'     => 'Harga wajib diisi.',
            'harga.numeric'      => 'Harga harus berupa angka.',
            'harga.min'          => 'Harga tidak boleh negatif.',
            'satuan.required'    => 'Satuan wajib diisi.',
            'satuan.max'         => 'Satuan maksimal 10 karakter.',
            'status.required'    => 'Status wajib dipilih.',
            'gambar_file.image'  => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.mimes'  => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.max'    => 'Ukuran foto maksimal 3 MB.',
        ]);

        $gambar = '';
        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/layanan');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambar = 'uploads/layanan/' . $safeName;
        }

        Layanan::create([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi ?? '',
            'harga' => $request->harga ?? 0,
            'satuan' => $request->satuan ?? 'unit',
            'gambar' => $gambar,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.layanan');
    }

    /**
     * Memproses dan memperbarui data produk layanan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:30',
            'kategori' => 'required|string|max:20',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:10',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:3072',
            'status' => 'required|in:aktif,nonaktif',
        ], [
            'nama.required'      => 'Nama produk wajib diisi.',
            'nama.max'           => 'Nama produk maksimal 30 karakter.',
            'kategori.required'  => 'Kategori wajib dipilih.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'harga.required'     => 'Harga wajib diisi.',
            'harga.numeric'      => 'Harga harus berupa angka.',
            'harga.min'          => 'Harga tidak boleh negatif.',
            'satuan.required'    => 'Satuan wajib diisi.',
            'satuan.max'         => 'Satuan maksimal 10 karakter.',
            'status.required'    => 'Status wajib dipilih.',
            'gambar_file.image'  => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.mimes'  => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.max'    => 'Ukuran foto maksimal 3 MB.',
        ]);

        $layanan = Layanan::findOrFail($id);
        $gambar = $layanan->gambar;

        if ($request->hasFile('gambar_file')) {
            if ($layanan->gambar) {
                $oldPath = public_path($layanan->gambar);
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
            $gambar = 'uploads/layanan/' . $safeName;
        }

        $layanan->update([
            'nama' => $request->nama,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi ?? '',
            'harga' => $request->harga ?? 0,
            'satuan' => $request->satuan ?? 'unit',
            'gambar' => $gambar,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.layanan');
    }

    /**
     * Mengubah status aktif/nonaktif produk layanan.
     */
    public function toggleStatus($id)
    {
        $layanan = Layanan::findOrFail($id);
        $layanan->status = $layanan->status === 'aktif' ? 'nonaktif' : 'aktif';
        $layanan->save();

        return redirect()->route('admin.layanan');
    }

    /**
     * Menghapus data produk layanan.
     */
    public function destroy($id)
    {
        $layanan = Layanan::findOrFail($id);
        if ($layanan->gambar) {
            $oldPath = public_path($layanan->gambar);
            if (File::isFile($oldPath)) {
                File::delete($oldPath);
            }
        }
        $layanan->delete();

        return redirect()->route('admin.layanan');
    }

    /**
     * Menyimpan pilihan produk unggulan untuk ditampilkan di halaman beranda.
     */
    public function saveFeatured(Request $request)
    {
        $ids = $request->input('featured_ids', []);
        $ids = array_map('intval', (array) $ids);
        $ids = array_slice($ids, 0, 3); // maks 3

        // Menghapus status unggulan dari seluruh produk
        Layanan::query()->update(['is_featured' => false]);

        // Menandai produk terpilih sebagai unggulan
        if (!empty($ids)) {
            Layanan::whereIn('id', $ids)->update(['is_featured' => true]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Memproses dan menyimpan kategori layanan baru.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:20',
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
     * Menghapus kategori layanan yang dipilih.
     */
    public function destroyCategory($kategori)
    {
        $catToDelete = $kategori;

        // Memeriksa apakah terdapat produk yang menggunakan kategori ini
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
