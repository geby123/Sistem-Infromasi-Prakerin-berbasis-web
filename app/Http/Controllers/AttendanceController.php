<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Memeriksa apakah peran pengguna adalah admin atau Guru
        if ($user->role === 'admin' || $user->role === 'Guru') {
            // Jika perannya admin atau Guru, tampilkan semua data absensi
            $attendances = Attendance::paginate(10);
        } else {
            // Jika bukan admin atau Guru, hanya tampilkan absensi milik mereka sendiri
            $attendances = $user->attendances()->paginate(10);
        }

        return view('dashboard.attendance', compact('attendances'));
    }




    public function store(Request $request)
    {
        $user = auth()->user();
        $currentDateTime = Carbon::now(); // Mengambil waktu saat ini
    
        // Cek apakah user sudah absen hari ini
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $currentDateTime->toDateString())
            ->first();
    
        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Anda sudah absen hari ini.');
        }
    
        // Aturan jam absensi
        $startTime = Carbon::createFromTime(6, 0, 0);  // 06:00 pagi
        $endTime = Carbon::createFromTime(17, 0, 0);   // 17:00 sore
        $eightAM = Carbon::createFromTime(8, 0, 0);    // 08:00 pagi
        $twelvePM = Carbon::createFromTime(12, 0, 0);  // 12:00 siang
    
        // Periksa apakah waktu absensi dalam rentang waktu yang diizinkan
        if ($currentDateTime->lt($startTime)) {
            return redirect()->back()->with('error', 'Absensi hanya bisa dilakukan mulai pukul 06:00.');
        } elseif ($currentDateTime->gt($endTime)) {
            return redirect()->back()->with('error', 'Absensi sudah ditutup. Silakan absen besok.');
        }
    
        // Tentukan status absensi berdasarkan waktu
        if ($currentDateTime->between($startTime, $eightAM)) {
            $status = 'present';  // Hadir sebelum pukul 08:00
        } elseif ($currentDateTime->between($eightAM, $twelvePM)) {
            $status = 'late';     // Terlambat setelah pukul 08:00 hingga 12:00
        } elseif ($currentDateTime->between($twelvePM, $endTime)) {
            $status = 'alpha'; // Tidak ada status setelah pukul 12:00 hingga 17:00
        }
    
        // Simpan absensi dengan status yang sudah ditentukan
        Attendance::create([
            'user_id' => $user->id,
            'date' => $currentDateTime, // Menyimpan waktu lengkap
            'status' => $status,
        ]);
    
        return redirect()->back()->with('success', 'Absensi berhasil disimpan dengan status: ' . ucfirst($status));
    }
    

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:present,late,alpha,hospitality',
        ]);

        $attendance->status = $request->input('status');
        $attendance->save();

        return redirect()->route('attendance.show')->with('success', 'Status absensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect()->route('attendance.show')->with('success', 'Absensi berhasil dihapus.');
    }

}
