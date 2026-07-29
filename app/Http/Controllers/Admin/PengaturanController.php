<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengaturan;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class PengaturanController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $settingsQuery = Pengaturan::all();
        $settings = [];
        foreach ($settingsQuery as $row) {
            $settings[$row->key_name] = $row->value;
        }

        return view('admin.pengaturan', compact('settings'));
    }

    /**
     * Save settings.
     */
    public function save(Request $request)
    {
        $fields = [
            'nama_koperasi', 'alamat', 'telepon', 'email', 'visi', 'misi'
        ];

        foreach ($fields as $key) {
            if ($request->has($key)) {
                $val = $request->input($key, '');
                Pengaturan::updateOrCreate(
                    ['key_name' => $key],
                    ['value' => $val]
                );
            }
        }

        // Handle administrator password change if provided in the unified settings form
        if ($request->filled('password_lama') || $request->filled('password_baru') || $request->filled('password_konfirmasi')) {
            $request->validate([
                'password_lama' => 'required',
                'password_baru' => 'required|min:6',
                'password_konfirmasi' => 'required|same:password_baru',
            ], [
                'password_lama.required'        => 'Password lama wajib diisi.',
                'password_baru.required'        => 'Password baru wajib diisi.',
                'password_baru.min'             => 'Password baru minimal 6 karakter.',
                'password_konfirmasi.required'  => 'Konfirmasi password wajib diisi.',
                'password_konfirmasi.same'      => 'Konfirmasi password tidak cocok.',
            ]);

            $admin = Auth::guard('admin')->user();
            
            // Check old password (using MD5)
            if (md5($request->password_lama) !== $admin->password) {
                return redirect()->route('admin.pengaturan')->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
            }

            $admin->password = md5($request->password_baru);
            $admin->save();
        }



        return redirect()->route('admin.pengaturan')->with('success', 'Pengaturan berhasil disimpan!');
    }

    /**
     * Save hero background image.
     */
    public function saveHeroBg(Request $request)
    {
        if ($request->input('action') === 'reset') {
            $oldValue = Pengaturan::getValue('hero_background', '');
            if ($oldValue && !str_starts_with($oldValue, 'http')) {
                $oldPath = public_path('uploads/' . $oldValue);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            Pengaturan::where('key_name', 'hero_background')->delete();
            return redirect()->route('admin.pengaturan')->with('success', 'Background Beranda diset kembali ke default.');
        }

        if ($request->hasFile('hero_upload')) {
            $request->validate([
                'hero_upload' => 'required|image|mimes:jpeg,png,webp,jpg|max:5120',
            ], [
                'hero_upload.max' => 'Ukuran file maksimal 5 MB.',
                'hero_upload.image' => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
                'hero_upload.mimes' => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            ]);

            $file = $request->file('hero_upload');
            $uploadPath = public_path('uploads');
            if (!\Illuminate\Support\Facades\File::isDirectory($uploadPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadPath, 0755, true, true);
            }

            $safeName = 'hero_' . time() . '.' . $file->getClientOriginalExtension();

            // Delete old file
            $oldValue = Pengaturan::getValue('hero_background', '');
            if ($oldValue && !str_starts_with($oldValue, 'http') && $oldValue !== $safeName) {
                $oldPath = public_path('uploads/' . $oldValue);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file->move($uploadPath, $safeName);

            Pengaturan::updateOrCreate(
                ['key_name' => 'hero_background'],
                ['value' => $safeName]
            );

            return redirect()->route('admin.pengaturan')->with('success', 'Background Beranda berhasil diperbarui.');
        }

        if ($request->filled('hero_url')) {
            $request->validate([
                'hero_url' => 'required|url',
            ], [
                'hero_url.url' => 'Format URL tidak valid.',
            ]);

            // Delete old file
            $oldValue = Pengaturan::getValue('hero_background', '');
            if ($oldValue && !str_starts_with($oldValue, 'http')) {
                $oldPath = public_path('uploads/' . $oldValue);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            Pengaturan::updateOrCreate(
                ['key_name' => 'hero_background'],
                ['value' => $request->hero_url]
            );

            return redirect()->route('admin.pengaturan')->with('success', 'Background Beranda berhasil diperbarui.');
        }

        return redirect()->route('admin.pengaturan')->withErrors(['hero_upload' => 'Harap unggah file atau masukkan URL gambar.']);
    }

    /**
     * Save organizational structure chart image.
     */
    public function saveOrgChart(Request $request)
    {
        if ($request->input('action') === 'reset') {
            $oldValue = Pengaturan::getValue('org_chart', '');
            if ($oldValue && !str_starts_with($oldValue, 'http')) {
                $oldPath = public_path('uploads/' . $oldValue);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            Pengaturan::where('key_name', 'org_chart')->delete();
            return redirect()->route('admin.pengaturan')->with('success', 'Struktur Organisasi diset kembali ke default.');
        }

        if ($request->hasFile('org_upload')) {
            $request->validate([
                'org_upload' => 'required|image|mimes:jpeg,png,webp,jpg|max:5120',
            ], [
                'org_upload.max' => 'Ukuran file maksimal 5 MB.',
                'org_upload.image' => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
                'org_upload.mimes' => 'Foto harus berupa file dengan format JPG, JPEG, PNG, GIF, SVG, atau WEBP.',
            ]);

            $file = $request->file('org_upload');
            $uploadPath = public_path('uploads');
            if (!\Illuminate\Support\Facades\File::isDirectory($uploadPath)) {
                \Illuminate\Support\Facades\File::makeDirectory($uploadPath, 0755, true, true);
            }

            $safeName = 'org_' . time() . '.' . $file->getClientOriginalExtension();

            // Delete old file
            $oldValue = Pengaturan::getValue('org_chart', '');
            if ($oldValue && !str_starts_with($oldValue, 'http') && $oldValue !== $safeName) {
                $oldPath = public_path('uploads/' . $oldValue);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $file->move($uploadPath, $safeName);

            Pengaturan::updateOrCreate(
                ['key_name' => 'org_chart'],
                ['value' => $safeName]
            );

            return redirect()->route('admin.pengaturan')->with('success', 'Struktur Organisasi berhasil diperbarui.');
        }

        if ($request->filled('org_url')) {
            $request->validate([
                'org_url' => 'required|url',
            ], [
                'org_url.url' => 'Format URL tidak valid.',
            ]);

            // Delete old file
            $oldValue = Pengaturan::getValue('org_chart', '');
            if ($oldValue && !str_starts_with($oldValue, 'http')) {
                $oldPath = public_path('uploads/' . $oldValue);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            Pengaturan::updateOrCreate(
                ['key_name' => 'org_chart'],
                ['value' => $request->org_url]
            );

            return redirect()->route('admin.pengaturan')->with('success', 'Struktur Organisasi berhasil diperbarui.');
        }

        return redirect()->route('admin.pengaturan')->withErrors(['org_upload' => 'Harap unggah file atau masukkan URL gambar.']);
    }

    /**
     * Change admin password.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        ], [
            'old_password.required'     => 'Password lama wajib diisi.',
            'new_password.required'     => 'Password baru wajib diisi.',
            'new_password.min'          => 'Password baru minimal 6 karakter.',
            'confirm_password.required' => 'Konfirmasi password wajib diisi.',
            'confirm_password.same'     => 'Konfirmasi password tidak cocok.',
        ]);

        $admin = Auth::guard('admin')->user();

        // Check old password (using MD5)
        if (md5($request->old_password) !== $admin->password) {
            return redirect()->route('admin.pengaturan')->withErrors(['old_password' => 'Password lama tidak sesuai.']);
        }

        // Update password (using MD5)
        $admin->password = md5($request->new_password);
        $admin->save();

        return redirect()->route('admin.pengaturan')->with('success_password', 'Password berhasil diubah!');
    }
}
