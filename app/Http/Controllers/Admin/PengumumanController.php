<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengumuman;

class PengumumanController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index()
    {
        $pengumumanList = Pengumuman::orderBy('id', 'desc')->paginate(10);
        return view('admin.pengumuman', compact('pengumumanList'));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return redirect()->route('admin.pengumuman.index');
    }

    /**
     * Store a newly created announcement in database.
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

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit($id)
    {
        return redirect()->route('admin.pengumuman.index');
    }

    /**
     * Update the specified announcement in database.
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

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified announcement from database.
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
