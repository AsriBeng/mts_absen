<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function showProfile()
    {
        /** @var \App\Models\User $user */
        $user = User::with(['role', 'guru'])->find(Auth::id());

        return view('layouts.profile', compact('user'));
    }

    public function showSetting()
    {
        return view('layouts.setting');
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            'nama_lengkap'   => 'nullable|string|max:255',
            'gelar_depan'    => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'nik'            => 'nullable|string|max:16',
            'nip'            => 'nullable|string|max:30',
            'jenis_kelamin'  => 'nullable|in:L,P',
            'tempat_lahir'   => 'nullable|string|max:255',
            'tanggal_lahir'  => 'nullable|date',
            'agama'          => 'nullable|string|max:50',
            'no_hp'          => 'nullable|string|max:20',
            'alamat'         => 'nullable|string',
            'foto_profile'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Update Email User Utama
        $user->update([
            'email' => $request->email,
        ]);

        // Update Biodata Guru jika role user adalah guru
        if (strtolower($user->role->name ?? '') === 'guru') {
            $guru = Guru::firstOrCreate(['user_id' => $user->id]);

            $guruData = [
                'email'          => $request->email,
                'nama_lengkap'   => $request->nama_lengkap,
                'gelar_depan'    => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'nik'            => $request->nik,
                'nip'            => $request->nip,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'tempat_lahir'   => $request->tempat_lahir,
                'tanggal_lahir'  => $request->tanggal_lahir,
                'agama'          => $request->agama,
                'no_hp'          => $request->no_hp,
                'alamat'         => $request->alamat,
            ];

            // Upload Foto Profile jika ada
            if ($request->hasFile('foto_profile')) {
                if ($guru->foto_profile && Storage::disk('public')->exists($guru->foto_profile)) {
                    Storage::disk('public')->delete($guru->foto_profile);
                }
                $guruData['foto_profile'] = $request->file('foto_profile')->store('profile_photos', 'public');
            }

            $guru->update($guruData);
        }

        return redirect()->back()->with('success', 'Informasi profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }
}
