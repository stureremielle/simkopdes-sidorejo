<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;

class PengumumanController extends Controller
{
    /**
     * Menampilkan daftar data pengumuman.
     */
    public function index()
    {
        $pengumumanList = Pengumuman::orderBy('id', 'desc')->paginate(10);
        return view('admin.pengumuman', compact('pengumumanList'));
    }

    /**
     * Menampilkan formulir penambahan pengumuman baru.
     */
    public function create()
    {
        return redirect()->route('admin.pengumuman.index');
    }

    /**
     * Memproses dan menyimpan data pengumuman baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:80',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'isi' => 'required|string',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        Pengumuman::create($request->all());

        return redirect()->route('admin.pengumuman.index');
    }

    /**
     * Menampilkan formulir pembaruan pengumuman.
     */
    public function edit($id)
    {
        return redirect()->route('admin.pengumuman.index');
    }

    /**
     * Memproses dan memperbarui data pengumuman di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:80',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'isi' => 'required|string',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update($request->all());

        return redirect()->route('admin.pengumuman.index');
    }

    /**
     * Menghapus data pengumuman dari database.
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index');
    }
}
