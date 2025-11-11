<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login() {
        return view('auth.login');
    }

    public function register() {
        return view('auth.register');
    }

   // Register Function
    public function register_action(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nis' => 'nullable|string',
            'nik' => 'nullable|string',
            'phone' => 'nullable|string',
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string',
        ], [
            'name.required' => 'Nama lengkap harus diisi.',
            'username.required' => 'Username harus diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password harus diisi.',
            'password.min' => 'Password harus memiliki minimal 8 karakter.',
            'password.confirmed' => 'Password dan konfirmasi password tidak cocok.',
        ]);

        // Cek validasi
        if ($validator->fails()) {
            // Mengirimkan kembali dengan pesan error dan input yang telah dimasukkan
            return back()->withErrors($validator)->withInput()->with('error', 'Periksa kembali data yang Anda masukkan.');
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'student', // Default role
            'nis' => $request->nis,
            'nik' => $request->nik,
            'phone' => $request->phone,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('login')->with('success', 'Berhasil Register!');
    }   


   // Login Function
   public function login_action(Request $request)
   {
       // Validasi input
       $validator = Validator::make($request->all(), [
           'username' => 'required|string',
           'password' => 'required|string',
       ]);

       // Cek validasi
       if ($validator->fails()) {
           return back()->withErrors($validator)->withInput();
       }

       // Cek credentials (username dan password)
       if (auth()->attempt(['username' => $request->username, 'password' => $request->password])) {
           // Jika berhasil, redirect ke dashboard
           // Redirect dengan pesan sukses
            return redirect()->route('dashboard.index')->with('success', 'Login Berhasil!');
       } else {
           // Jika gagal, kembali dengan pesan error
           return back()->withErrors(['username' => 'Invalid credentials'])->withInput();
       }
   }

   // Logout Function
   public function logout()
   {
       // Logout user
       auth()->logout();

       // Redirect to login page
       return redirect()->route('login')->with('success', 'Logged out successfully!');
   }

}
