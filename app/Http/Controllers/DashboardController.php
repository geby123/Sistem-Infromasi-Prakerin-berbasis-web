<?php

namespace App\Http\Controllers;

use App\Models\Dudi;
use App\Models\User;
use App\Models\Application;
use App\Models\FinalReport;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index()
    {
        // Menghitung jumlah data untuk setiap model
        $applicationCount = Application::count();
        $dudiCount = Dudi::count();
        $finalReportCount = FinalReport::count();

        // Menghitung jumlah data Application yang memiliki status 'Accept'
        $acceptedApplicationCount = Application::where('status', 'Accept')->count();

        // Menjumlahkan total
        $totalCount = $applicationCount + $dudiCount + $finalReportCount;

        // Mengirimkan data ke view
        return view('dashboard.index', [
            'applicationCount' => $applicationCount,
            'dudiCount' => $dudiCount,
            'finalReportCount' => $finalReportCount,
            'totalCount' => $totalCount,
            'acceptedApplicationCount' => $acceptedApplicationCount // Mengirimkan jumlah yang memiliki status 'Accept'
        ]);
    }


    public function dudi() {
        return view('dashboard.dudi');
    }

    public function manage() {
        // Mengecualikan user yang sedang login dari hasil query
        $users = User::where('id', '!=', auth()->id())->paginate(10);
    
        // Mengirim data ke view
        return view('dashboard.manage', compact('users'));
    }
    
}
