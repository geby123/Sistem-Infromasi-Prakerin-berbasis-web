<?php

namespace App\Http\Controllers;

use App\Models\Dudi;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DuDiController extends Controller
{
    // Menampilkan semua Du/Di
    public function index()
    {
        $dudis = Dudi::all();
        return view('dashboard.dudi', compact('dudis'));
    }

    public function show()
    {
        // Mengambil semua data aplikasi dengan relasi User dan Dudi
        $applications = Application::with(['user', 'dudi'])->paginate(10);

        return view('dashboard.apply', compact('applications'));
    }

    

    
    // Method untuk menambahkan perusahaan
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'company' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'job_type' => 'required|array|min:1', // Memastikan job_type berisi minimal 1 pilihan
            'job_type.*' => 'in:IT Support Internship,Networking Internship,Software Development Internship,Web Development Internship,Data Analyst Internship,UI/UX Design Internship,Digital Marketing Internship,Content Writing Internship,Business Development Internship,Graphic Design Internship', // Memvalidasi setiap job type yang dipilih
            'status' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'closing_date' => 'nullable|date',
          
            'max_total' => 'numeric|max:15',
        ]);

        // Membuat instance Dudi baru dan menyimpan data ke database
        Dudi::create([
            'company' => $request->company,
            'city' => $request->city,
            'job_type' => implode(', ', $request->job_type), // Menyimpan job_type sebagai string yang dipisahkan koma
            'status' => $request->status,
            'qualifications' => $request->qualifications,
            'closing_date' => $request->closing_date,
           
            'max_total' => $request->max_total,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('dudis.index')->with('success', 'Perusahaan berhasil ditambahkan!');
    }



    public function apply(Request $request, $id)
    {
        // Validasi file CV yang di-upload
        $request->validate([
            'cv' => 'required|mimes:pdf,doc,docx|max:2048',
        ]);
    
        // Ambil data dudi berdasarkan ID
        $dudi = Dudi::findOrFail($id);
    
        // Cek apakah user sudah pernah apply ke dudi ini dengan status "Accept" atau "Pending" (status null)
        $existingApplication = Application::where('user_id', auth()->user()->id)
                                          ->where('dudi_id', $dudi->id)
                                          ->first();
    
        // Jika sudah ada aplikasi dengan status "Accept" atau "Pending" (status null)
        if ($existingApplication && ($existingApplication->status === 'Accept' || is_null($existingApplication->status))) {
            return redirect()->route('dudis.index')->with('error', 'Anda sudah apply ke perusahaan ini dan aplikasi Anda sedang diproses atau telah diterima.');
        }
    
        // Jika sudah ada aplikasi dengan status "Reject", lanjutkan proses apply
        if ($existingApplication && $existingApplication->status === 'Reject') {
            // Simpan file CV
            if ($request->hasFile('cv')) {
                $fileName = time() . '_' . $request->cv->getClientOriginalName();
                $filePath = $request->file('cv')->storeAs('uploads/cv', $fileName, 'public');
            }
    
            // Update aplikasi yang ada dengan data baru
            $existingApplication->update([
                'cv' => $filePath,  // Simpan path file CV ke database
            ]);
    
            return redirect()->route('dudis.index')->with('success', 'Pengajuan PKL berhasil dikirim kembali!');
        }
    
        // Jika tidak ada aplikasi sebelumnya, lanjutkan proses apply baru
        // Simpan file CV
        if ($request->hasFile('cv')) {
            $fileName = time() . '_' . $request->cv->getClientOriginalName();
            $filePath = $request->file('cv')->storeAs('uploads/cv', $fileName, 'public');
        }
    
        // Simpan data ke model Application
        Application::create([
            'user_id' => auth()->user()->id,  // Mengambil ID user yang sedang login
            'dudi_id' => $dudi->id,
            'cv' => $filePath,  // Simpan path file CV ke database
        ]);
    
        // Redirect dengan pesan sukses
        return redirect()->route('dudis.index')->with('success', 'Pengajuan PKL berhasil dikirim!');
    }
    
    



    // Memproses update data Dudi
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'company' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'job_type' => 'required|array|min:1', // Memastikan job_type berisi minimal 1 pilihan
            'job_type.*' => 'in:IT Support Internship,Networking Internship,Software Development Internship,Web Development Internship,Data Analyst Internship,UI/UX Design Internship,Digital Marketing Internship,Content Writing Internship,Business Development Internship,Graphic Design Internship', // Memvalidasi setiap job type yang dipilih
            'status' => 'required|string|max:255',
            'qualifications' => 'nullable|string',
            'closing_date' => 'nullable|date',
            'job_description' => 'nullable|string',
            'max_total' => 'numeric|max:15',
        ]);

        // Temukan data Dudi berdasarkan ID
        $dudi = Dudi::findOrFail($id);

        // Update data dengan input yang diberikan
        $dudi->update([
            'company' => $request->company,
            'city' => $request->city,
            'job_type' => implode(', ', $request->job_type), // Menyimpan job_type sebagai string yang dipisahkan koma
            'status' => $request->status,
            'qualifications' => $request->qualifications,
            'closing_date' => $request->closing_date,
            'job_description' => $request->job_description,
            'max_total' => $request->max_total,
        ]);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('dudis.index')->with('success', 'Dudi berhasil diupdate!');



    }


    // Menghapus data Dudi
    public function destroy($id)
    {
        $dudi = Dudi::findOrFail($id);
        $dudi->delete();

        return redirect()->route('dudis.index')->with('success', 'Dudi berhasil dihapus!');
    }

    public function updateStatus(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        $application->status = $request->status; // Set status sesuai tombol yang ditekan (Accept/Reject)
        $application->save();

        return redirect()->back()->with('success', 'Status berhasil diperbarui menjadi ' . $request->status);
    }

    public function destroyApply($id)
    {
        $application = Application::findOrFail($id);
        $application->delete();

        return redirect()->back()->with('success', 'Aplikasi berhasil dihapus');
    }
    
    
}
