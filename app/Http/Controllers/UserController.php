<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function update(Request $request, User $user)
    {
        // Validasi input form
        $request->validate([
            'name' => 'nullable|string|max:255',
            'role' => 'nullable|string',
            'nis' => 'nullable|numeric',
            'nik' => 'nullable|numeric',
            'phone' => 'nullable|string|max:15',
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string',
        ]);

        try {
            // Coba untuk mengupdate data user
            $user->update([
                'name' => $request->input('name'),
                'role' => $request->input('role'),
                'nis' => $request->input('nis'),
                'nik' => $request->input('nik'),
                'phone' => $request->input('phone'),
                'kelas' => $request->input('kelas'),
                'jurusan' => $request->input('jurusan'),
            ]);

            // Jika berhasil, redirect dengan pesan sukses
            return redirect()->route('dashboard.manage')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            // Jika gagal, redirect kembali dengan pesan error
            return redirect()->route('dashboard.manage')->with('error', 'Failed to update user. Please try again.');
        }
    }                                                                   



    public function destroy(User $user)
{
    // Menghapus user dari database
    $user->delete();

    // Redirect kembali ke halaman manage users dengan pesan sukses
    return redirect()->route('dashboard.manage')->with('success', 'User deleted successfully.');
}

}
