<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
  // public function index()
  // {
  //   $logUser = DB::table('log_pustakawan')
  //     ->orderBy('created_at', 'desc')
  //     ->paginate(10);
  //   $logCount = DB::table('log_pustakawan')->count();

  //   return view('backend.profil', compact(
  //     'logUser',
  //     'logCount',
  //   ));
  // }



  public function changePassword(Request $request, $idUser)
  {
    $request->validate([
      'old_password'          => ['required'],
      'new_password'          => ['required', 'min:8'],
      'password_confirmation' => ['required', 'same:new_password'],
    ], [
      'old_password.required'          => 'Password lama wajib diisi.',
      'new_password.required'          => 'Password baru wajib diisi.',
      'new_password.min'               => 'Password baru minimal 8 karakter.',
      'password_confirmation.required' => 'Ulangi password wajib diisi.',
      'password_confirmation.same'     => 'Konfirmasi password tidak cocok.',
    ]);

    $user = User::findOrFail($idUser);

    // Cek apakah password lama cocok
    if (!Hash::check($request->old_password, $user->password_hash)) {
      return back()
        ->withErrors(['old_password' => 'Password lama salah.'])
        ->withInput()
        ->with('tabActive', 'tabUpdatePassword');
    }

    // Simpan password baru
    $user->update([
      'password_hash' => Hash::make($request->new_password),
      'updated_at'    => now(),
    ]);

    return back()->with('success', 'Password berhasil diperbarui.');
  }


  public function changeImageProfil(Request $request, $idUser)
  {
    try {
      $request->validate([
        'imgProfil' => ['required', 'image', 'max:2048'],
      ], [
        'imgProfil.required' => 'File gambar wajib diunggah.',
        'imgProfil.image'    => 'File bukan format image.',
        'imgProfil.max'      => 'Maksimal ukuran gambar 2MB.',
      ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return back()
        ->withErrors($e->validator)
        ->withInput()
        ->with('tabActive', 'tabUpdateImage');
    }

    $user = User::findOrFail($idUser);

    $imageUpdate = uploadFile(config('app.img_directory'), $request->file('imgProfil'));
    Storage::delete(config('app.img_directory') . $user->picture);

    // Simpan password baru
    $user->update([
      'picture'    => $imageUpdate,
      'updated_at' => now(),
    ]);

    return back()->with('success', 'Foto profil berhasil diperbarui.');
  }
}
