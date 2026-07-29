<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Galeri;
use Illuminate\Support\Facades\File;

class GaleriController extends Controller
{
    /**
     * Display a listing of activities.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $kategoriFilter = $request->query('kategori');

        $query = Galeri::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%");
            });
        }

        if ($kategoriFilter) {
            $matchingCat = \App\Models\KategoriGaleri::where('nama', $kategoriFilter)->first();
            $query->where('kategori_id', $matchingCat ? $matchingCat->id : 0);
        }

        $galeriList = $query->orderBy('id', 'desc')->get();

        // Get unique categories for filter row
        $kategoriList = \App\Models\KategoriGaleri::pluck('nama')->toArray();

        // Stats
        $statTotal = Galeri::count();
        $statHasFile = Galeri::whereNotNull('materi')->where('materi', '<>', '')->count();

        return view('admin.galeri', compact(
            'galeriList',
            'kategoriList',
            'search',
            'kategoriFilter',
            'statTotal',
            'statHasFile'
        ));
    }

    /**
     * Store a newly created category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:20',
        ]);

        $newCat = trim($request->kategori);
        
        $exists = \App\Models\KategoriGaleri::whereRaw('LOWER(nama) = ?', [strtolower($newCat)])->exists();

        if (!$exists) {
            \App\Models\KategoriGaleri::create(['nama' => ucwords($newCat)]);
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

        // Check if there are any activities using this category
        $cat = \App\Models\KategoriGaleri::where('nama', $catToDelete)->first();
        if ($cat) {
            $hasActivities = Galeri::where('kategori_id', $cat->id)->count();
            if ($hasActivities > 0) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus karena kategori ini masih digunakan pada kegiatan.'], 400);
            }
            $cat->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Kategori tidak ditemukan.'], 400);
    }

    /**
     * Store a newly created activity.
     */
    public function store(Request $request)
    {
        $hasMateri = $request->input('has_materi') == '1' || $request->hasFile('materi_file') || $request->filled('keterangan');

        $rules = [
            'judul'       => 'required|string|max:50',
            'kategori'    => 'required|string|max:20',
            'periode'     => 'required|string',
            'gambar_file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,heic,heif|max:5120',
            'status'      => 'required|in:aktif,nonaktif',
        ];

        if ($hasMateri) {
            $rules['materi_file'] = 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,png,jpg,gif,svg,webp|max:10240';
            $rules['keterangan']  = 'required|string';
        } else {
            $rules['materi_file'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,png,jpg,gif,svg,webp|max:10240';
            $rules['keterangan']  = 'nullable|string';
        }

        $messages = [
            'judul.required'       => 'Judul kegiatan wajib diisi.',
            'judul.max'            => 'Judul kegiatan maksimal 50 karakter.',
            'kategori.required'    => 'Kategori wajib dipilih.',
            'periode.required'     => 'Tanggal kegiatan wajib diisi.',
            'gambar_file.required' => 'Foto kegiatan wajib diisi.',
            'gambar_file.image'    => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.mimes'    => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.max'      => 'Ukuran foto maksimal 5 MB.',
            'materi_file.required' => 'File materi wajib diunggah jika File Materi diaktifkan.',
            'materi_file.file'     => 'File materi harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'materi_file.mimes'    => 'File materi harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'materi_file.max'      => 'Ukuran file materi maksimal 10 MB.',
            'keterangan.required'  => 'Deskripsi file materi wajib diisi jika File Materi diaktifkan.',
            'status.required'      => 'Status wajib dipilih.',
        ];

        $request->validate($rules, $messages);

        $gambar = '';
        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/galeri');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambar = 'uploads/galeri/' . $safeName;
        }

        $materi = null;
        if ($request->hasFile('materi_file')) {
            $file = $request->file('materi_file');
            $safeName = time() . '_doc_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/galeri/materi');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $materi = $safeName;
        }

        $createdAt = now();
        $periode = $request->input('periode');
        if ($periode) {
            $monthsMap = [
                'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'jun' => 6,
                'jul' => 7, 'agu' => 8, 'agt' => 8, 'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12,
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 'mei' => 5, 'juni' => 6,
                'juli' => 7, 'agustus' => 8, 'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
            ];
            $parts = explode(' ', strtolower(trim($periode)));
            if (count($parts) === 2) {
                $monthStr = $parts[0];
                $year = intval($parts[1]);
                $monthNum = $monthsMap[$monthStr] ?? 1;
                if ($year > 1900 && $year < 2100) {
                    $createdAt = sprintf('%04d-%02d-01 00:00:00', $year, $monthNum);
                }
            }
        }

        Galeri::create([
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar' => $gambar,
            'materi' => $materi,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'created_at' => $createdAt,
        ]);

        return redirect()->route('admin.galeri')->with('success', 'Kegiatan baru berhasil ditambahkan.');
    }

    /**
     * Update the specified activity.
     */
    public function update(Request $request, $id)
    {
        $item = Galeri::findOrFail($id);

        $hasMateri = $request->input('has_materi') == '1' || $request->hasFile('materi_file') || $request->filled('keterangan');
        $removeMateri = $request->input('remove_materi') == '1';

        $hasPhoto = $request->hasFile('gambar_file') || ($item->gambar && $request->input('remove_gambar') != '1');

        $rules = [
            'judul'    => 'required|string|max:50',
            'kategori' => 'required|string|max:20',
            'periode'  => 'required|string',
            'status'   => 'required|in:aktif,nonaktif',
        ];

        if (!$hasPhoto) {
            $rules['gambar_file'] = 'required|image|mimes:jpeg,png,jpg,gif,svg,webp,heic,heif|max:5120';
        } else {
            $rules['gambar_file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,heic,heif|max:5120';
        }

        if ($hasMateri && !$removeMateri) {
            if (!$item->materi || $request->hasFile('materi_file')) {
                $rules['materi_file'] = ($item->materi && !$request->hasFile('materi_file')) ? 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,png,jpg,gif,svg,webp|max:10240' : 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,png,jpg,gif,svg,webp|max:10240';
            } else {
                $rules['materi_file'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,png,jpg,gif,svg,webp|max:10240';
            }
            $rules['keterangan'] = 'required|string';
        } else {
            $rules['materi_file'] = 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpeg,png,jpg,gif,svg,webp|max:10240';
            $rules['keterangan']  = 'nullable|string';
        }

        $messages = [
            'judul.required'       => 'Judul kegiatan wajib diisi.',
            'judul.max'            => 'Judul kegiatan maksimal 50 karakter.',
            'kategori.required'    => 'Kategori wajib dipilih.',
            'periode.required'     => 'Tanggal kegiatan wajib diisi.',
            'gambar_file.required' => 'Foto kegiatan wajib diisi.',
            'gambar_file.image'    => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.mimes'    => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'gambar_file.max'      => 'Ukuran foto maksimal 5 MB.',
            'materi_file.required' => 'File materi wajib diunggah jika File Materi diaktifkan.',
            'materi_file.file'     => 'File materi harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'materi_file.mimes'    => 'File materi harus berformat PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            'materi_file.max'      => 'Ukuran file materi maksimal 10 MB.',
            'keterangan.required'  => 'Deskripsi file materi wajib diisi jika File Materi diaktifkan.',
            'status.required'      => 'Status wajib dipilih.',
        ];

        $request->validate($rules, $messages);

        $gambar = $item->gambar;
        if ($request->input('remove_gambar') == '1' && !$request->hasFile('gambar_file')) {
            $oldPath = public_path($item->gambar);
            if (File::isFile($oldPath)) {
                File::delete($oldPath);
            }
            $gambar = '';
        } elseif ($request->hasFile('gambar_file')) {
            $oldPath = public_path($item->gambar);
            if (File::isFile($oldPath)) {
                File::delete($oldPath);
            }

            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/galeri');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambar = 'uploads/galeri/' . $safeName;
        }

        $materi = $item->materi;
        $keterangan = $request->keterangan;
        if ($request->input('remove_materi') == '1' && !$request->hasFile('materi_file')) {
            if ($item->materi) {
                $oldDocPath = public_path('uploads/galeri/materi/' . $item->materi);
                if (File::isFile($oldDocPath)) {
                    File::delete($oldDocPath);
                }
            }
            $materi = null;
            $keterangan = null;
        } elseif ($request->hasFile('materi_file')) {
            if ($item->materi) {
                $oldDocPath = public_path('uploads/galeri/materi/' . $item->materi);
                if (File::isFile($oldDocPath)) {
                    File::delete($oldDocPath);
                }
            }

            $file = $request->file('materi_file');
            $safeName = time() . '_doc_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/galeri/materi');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $materi = $safeName;
        }

        $updateData = [
            'judul'      => $request->judul,
            'kategori'   => $request->kategori,
            'gambar'     => $gambar,
            'materi'     => $materi,
            'keterangan' => $keterangan,
            'status'     => $request->status,
        ];

        $periode = $request->input('periode');
        if ($periode) {
            $monthsMap = [
                'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'mei' => 5, 'jun' => 6,
                'jul' => 7, 'agu' => 8, 'agt' => 8, 'sep' => 9, 'okt' => 10, 'nov' => 11, 'des' => 12,
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 'mei' => 5, 'juni' => 6,
                'juli' => 7, 'agustus' => 8, 'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
            ];
            $parts = explode(' ', strtolower(trim($periode)));
            if (count($parts) === 2) {
                $monthStr = $parts[0];
                $year = intval($parts[1]);
                $monthNum = $monthsMap[$monthStr] ?? 1;
                if ($year > 1900 && $year < 2100) {
                    $updateData['created_at'] = sprintf('%04d-%02d-01 00:00:00', $year, $monthNum);
                }
            }
        }

        $item->update($updateData);

        return redirect()->route('admin.galeri')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Remove the specified activity.
     */
    public function destroy($id)
    {
        $item = Galeri::findOrFail($id);

        // Delete physical files
        $oldPath = public_path($item->gambar);
        if (File::isFile($oldPath)) {
            File::delete($oldPath);
        }

        if ($item->materi) {
            $oldDocPath = public_path('uploads/galeri/materi/' . $item->materi);
            if (File::isFile($oldDocPath)) {
                File::delete($oldDocPath);
            }
        }

        $item->delete();

        return redirect()->route('admin.galeri')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
