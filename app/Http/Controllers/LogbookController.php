<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Logbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{


    public function create()
    {
        // Mendapatkan user yang sedang login
        $user = auth()->user();

        // Cek role user
        if ($user->role === 'student') {
            // Jika role "student", hanya mengambil data logbook miliknya
            $logbooks = Logbook::with('user')->where('user_id', $user->id)->paginate(5);
        } else {
            // Jika role "admin" atau "guru", mengambil semua data logbook
            $logbooks = Logbook::with('user')->paginate(5);
        }

        // Mengirimkan data logbook ke view
        return view('dashboard.logbook', compact('logbooks'));
    }


    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'upload_date' => 'required|date',
            'week' => 'required|integer',
            'file' => 'required|mimes:pdf,doc,docx|max:2048', // Validasi file
        ]);

        // Upload file ke storage
        $filePath = $request->file('file')->store('logbooks', 'public');

        // Simpan data logbook
        Logbook::create([
            'upload_date' => $request->upload_date,
            'week' => $request->week,
            'file_path' => $filePath,
            'user_id' => Auth::id(), // Relasi ke user yang sedang login
        ]);

        return redirect()->route('logbook.create')->with('success', 'Logbook uploaded successfully!');
    }

    // Fungsi untuk menampilkan form edit dengan data logbook
    public function edit($id)
    {
        $logbook = Logbook::findOrFail($id);
        return view('logbook.edit', compact('logbook'));
    }

    // Fungsi untuk update logbook
    public function update(Request $request, $id)
    {
        $logbook = Logbook::findOrFail($id);

        $request->validate([
            'upload_date' => 'required|date',
            'week' => 'required|integer',
            'file' => 'nullable|mimes:pdf,doc,docx|max:2048',
        ]);

        // Jika ada file baru yang di-upload, hapus file lama dan simpan file baru
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('logbooks', 'public');
            $logbook->file_path = $filePath;
        }

        $logbook->upload_date = $request->upload_date;
        $logbook->week = $request->week;
        $logbook->save();

        return redirect()->route('logbook.create')->with('success', 'Logbook berhasil diperbarui!');
    }

    // Fungsi untuk menghapus logbook
    public function destroy($id)
    {
        $logbook = Logbook::findOrFail($id);

        // Cek apakah user yang menghapus adalah pemilik logbook atau admin
        if (Auth::id() === $logbook->user_id || Auth::user()->role === 'admin') {
            $logbook->delete();
            return redirect()->route('logbook.create')->with('success', 'Logbook berhasil dihapus!');
        }

        return redirect()->route('logbook.create')->with('error', 'Anda tidak memiliki izin untuk menghapus logbook ini.');
    }
}
