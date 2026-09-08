<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSettingController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name', 'asc')->get();
        $roles = Role::all();

        return view('app.admin.userssetting', compact('users', 'roles'));
    }

    // Simpan User Baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255', // Input Username
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        // 1. Buat User Baru
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
        ]);

        // 2. Cek Role User
        $role = Role::find($request->role_id);
        if ($role && strtolower($role->name) === 'guru') {
            Guru::create([
                'user_id'      => $user->id,
                'nama_lengkap' => $user->name,
                'email'        => $user->email,
            ]);
        }

        return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    // Update Data User
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email|max:255|unique:users,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Sinkronisasi data di tabel Guru jika rolenya Guru
        $role = Role::find($request->role_id);
        if ($role && strtolower($role->name) === 'guru') {
            Guru::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap' => $user->name,
                    'email'        => $user->email,
                ]
            );
        } else {
            // Jika role diubah dari Guru ke Admin, hapus data guru-nya jika ada
            Guru::where('user_id', $user->id)->delete();
        }

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    // Hapus User
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // Hapus data guru terlebih dahulu jika ada (atau otomatis via onDelete cascade di DB)
        Guru::where('user_id', $user->id)->delete();
        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus!');
    }
}
