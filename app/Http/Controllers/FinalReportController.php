<?php

namespace App\Http\Controllers;

use App\Models\FinalReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinalReportController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $finalReport = $user->finalReport; // Mengambil laporan akhir milik pengguna yang sedang login
        $allReports = [];

        // Periksa jika user adalah admin atau guru untuk mendapatkan semua laporan
        if ($user->role === 'admin' || $user->role === 'guru') {
            $allReports = FinalReport::all();
        } elseif ($user->role === 'student') {
            // Untuk student, hanya ambil laporan miliknya sendiri
            $allReports = FinalReport::where('user_id', $user->id)->get();
        }

        return view('dashboard.finalReport', compact('finalReport', 'allReports', 'user'));
    }


    public function show()
    {
        // Mengambil semua data dari FinalReport
        $finalReports = FinalReport::all();

        // Mengirimkan data ke view
        return view('dashboard.finalReportShow', compact('finalReports'));
    }


    // Menyimpan laporan yang diunggah user
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:2048',
        ]);

        $filePath = $request->file('file')->store('final_reports', 'public');

        FinalReport::create([
            'user_id' => Auth::id(),
            'file_path' => $filePath,
            'status' => 'menunggu verifikasi',
        ]);

        return redirect()->route('final_report.create')->with('success', 'Laporan berhasil diupload, menunggu verifikasi.');
    }

    public function verify($id)
    {
        // Cari data FinalReport berdasarkan ID
        $finalReport = FinalReport::findOrFail($id);
        $finalReport->status = 'terverifikasi';
        $finalReport->save();

        return redirect()->route('final_report.show')->with('success', 'Laporan berhasil diverifikasi.');
    }

    public function reject($id)
    {
        // Cari data FinalReport berdasarkan ID
        $finalReport = FinalReport::findOrFail($id);
        $finalReport->status = 'rejected';
        $finalReport->save();

        return redirect()->route('final_report.show')->with('success', 'Laporan berhasil ditolak.');
    }

    public function destroy($id)
    {
        // Cari data FinalReport berdasarkan ID dan hapus
        $finalReport = FinalReport::findOrFail($id);
        $finalReport->delete();

        return redirect()->route('final_report.show')->with('success', 'Laporan berhasil dihapus.');
    }


    
}
