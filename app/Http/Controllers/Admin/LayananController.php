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
        $layananList = Layanan::orderBy('kategori')->orderBy('nama')->get();
        return view('admin.layanan', compact('layananList'));
    }

    /**
     * Store a newly created service.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
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
            'nama' => 'required|string|max:150',
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
}
