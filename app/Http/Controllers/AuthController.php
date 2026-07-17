<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  public function login()
  {
    return view('auth.login');
  }

  public function authenticate(Request $request)
  {
    $credentials = $request->validate([
      'username' => ['required'],
      'password' => ['required'],
    ]);

    // Cari user berdasarkan username
    $user = User::where('username', $credentials['username'])->first();

    if (!$user) {
      return back()->withErrors([
        'username' => 'Username atau password salah.',
      ])->onlyInput('username');
    }

    // Cek apakah statusnya 10
    if ($user->status != 10) {
      return back()->withErrors([
        'username' => 'Username atau password salah.',
      ])->onlyInput('username');
    }

    // Lanjutkan autentikasi
    if (Auth::attempt($credentials)) {
      $user->update(['auth_key' => str()->random(32)]);

      $request->session()->regenerate();

      return redirect()->intended('dashboard')->with('success', 'Login berhasil.');
    }

    return back()->withErrors([
      'username' => 'Username atau password salah.',
    ])->onlyInput('username');
  }

  public function logout(Request $request)
  {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
  }
}
