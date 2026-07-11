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
            $query->where('kategori', $kategoriFilter);
        }

        $galeriList = $query->orderBy('created_at', 'desc')->get();

        // Get unique categories for filter row
        $kategoriList = ['Rapat & Musyawarah', 'Panen & Pertanian', 'Pelatihan', 'Kegiatan Sosial', 'Kegiatan'];
        $dbKategori = Galeri::distinct()->whereNotNull('kategori')->pluck('kategori')->toArray();
        foreach ($dbKategori as $kat) {
            if (!in_array($kat, $kategoriList) && !empty($kat)) {
                $kategoriList[] = $kat;
            }
        }

        // Stats
        $statTotal = Galeri::count();
        $statHasFile = Galeri::whereNotNull('materi_url')->where('materi_url', '<>', '')->count();

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
     * Store a newly created activity.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:200',
            'kategori' => 'required|string|max:50',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // limit 5MB
            'materi_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240', // limit 10MB
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $gambarUrl = '';
        if ($request->hasFile('gambar_file')) {
            $file = $request->file('gambar_file');
            $safeName = time() . '_img_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/galeri');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $gambarUrl = 'uploads/galeri/' . $safeName;
        } else {
            // Fallback default image or empty
            $gambarUrl = 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&fit=crop&q=80';
        }

        $materiUrl = null;
        if ($request->hasFile('materi_file')) {
            $file = $request->file('materi_file');
            $safeName = time() . '_doc_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
            
            $uploadPath = public_path('uploads/galeri/materi');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }
            $file->move($uploadPath, $safeName);
            $materiUrl = $safeName; // Just save filename to match 'Notulen_RAT_2024.pdf' style in mockup
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
            'gambar_url' => $gambarUrl,
            'materi_url' => $materiUrl,
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
        $request->validate([
            'judul' => 'required|string|max:200',
            'kategori' => 'required|string|max:50',
            'gambar_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'materi_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $item = Galeri::findOrFail($id);
        
        $gambarUrl = $item->gambar_url;
        if ($request->input('remove_gambar') == '1' && !$request->hasFile('gambar_file')) {
            $oldPath = public_path($item->gambar_url);
            if (File::isFile($oldPath)) {
                File::delete($oldPath);
            }
            $gambarUrl = 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=600&fit=crop&q=80';
        } elseif ($request->hasFile('gambar_file')) {
            // Delete old file if exists in uploads
            $oldPath = public_path($item->gambar_url);
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
            $gambarUrl = 'uploads/galeri/' . $safeName;
        }

        $materiUrl = $item->materi_url;
        $keterangan = $request->keterangan;
        if ($request->input('remove_materi') == '1' && !$request->hasFile('materi_file')) {
            if ($item->materi_url) {
                $oldDocPath = public_path('uploads/galeri/materi/' . $item->materi_url);
                if (File::isFile($oldDocPath)) {
                    File::delete($oldDocPath);
                }
            }
            $materiUrl = null;
            $keterangan = null;
        } elseif ($request->hasFile('materi_file')) {
            if ($item->materi_url) {
                $oldDocPath = public_path('uploads/galeri/materi/' . $item->materi_url);
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
            $materiUrl = $safeName;
        }

        $updateData = [
            'judul' => $request->judul,
            'kategori' => $request->kategori,
            'gambar_url' => $gambarUrl,
            'materi_url' => $materiUrl,
            'keterangan' => $keterangan,
            'status' => $request->status,
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
        $oldPath = public_path($item->gambar_url);
        if (File::isFile($oldPath)) {
            File::delete($oldPath);
        }

        if ($item->materi_url) {
            $oldDocPath = public_path('uploads/galeri/materi/' . $item->materi_url);
            if (File::isFile($oldDocPath)) {
                File::delete($oldDocPath);
            }
        }

        $item->delete();

        return redirect()->route('admin.galeri')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
