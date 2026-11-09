<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	public function index(Request $request)
	{
		$data = DB::table('user');

		if ($request->filled('username'))
			$data->where('username', 'like', '%' . $request->username . '%');

		if ($request->filled('email')) {
			$data->where('email', 'like', '%' . $request->email . '%');
		}

		$data = $data->paginate(15)->withQueryString();


		return view('backend.user', compact(
			'data'
		));
	}


	public function create()
	{
		$title = 'Tambah Data User';
		return view('backend.user-form', compact(
			'title',
		));
	}


	public function store(Request $request)
	{
		$validated = $request->validate([
			'username' => 'required|string|max:255|unique:user,username',
			'email' => 'required|string|email|max:255|unique:user,email',
			'password' => 'required|string|min:8|confirmed',
		]);

		// Simpan user baru
		User::create([
			'username'      => $validated['username'],
			'email'         => $validated['email'],
			'password_hash' => Hash::make($validated['password']),
			'auth_key'      => str()->random(32),
			'status'        => 10,
			'created_at'    => now(),
			'updated_at'    => now(),
		]);

		return redirect()->route('backend.user.index')->with('success', 'User baru berhasil ditambahkan');
	}


	public function edit($id)
	{
		$title = 'Edit Data User';
		$data  = User::findOrFail($id);

		return view('backend.user-form', compact(
			'title',
			'data',
		));
	}


	public function update(Request $request, $id)
	{
		$user = User::findOrFail($id);

		$validated = $request->validate([
			'username' => ['required', 'string', 'max:255', Rule::unique('user', 'username')->ignore($user->id)],
			'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('user', 'email')->ignore($user->id)],
			'password' => ['nullable', 'string', 'min:8', 'confirmed'],
		]);

		$data = [
			'username' => $validated['username'],
			'email'    => $validated['email'],
		];

		// Password hanya diganti kalau diisi
		if (filled($validated['password'] ?? null))
			$data['password_hash'] = Hash::make($validated['password']);

		$user->update($data);

		return redirect()->route('backend.user.index')->with('success', 'Data user berhasil diupdate');
	}


	public function show($id)
	{
		$data = User::findOrFail($id);
		$logUser = DB::table('log_pustakawan')
			->orderBy('created_at', 'desc')
			->paginate(10);
		$logCount = DB::table('log_pustakawan')->count();

		return view('backend.profil', compact(
			'data',
			'logUser',
			'logCount',
		));
	}


	public function destroy($id)
	{
		User::findOrFail($id)->delete();

		return redirect()->back()->with('info', 'Data user berhasil dihapus');
	}


	public function changeActiveUser($idUser, $status)
	{
		$user = User::findOrFail($idUser);

		if ($status == 10)
			$user->update(['status' => 0]);
		else
			$user->update(['status' => 10]);


		return redirect()->back()->with('success', 'Status aktif user berhasil diupdate');
	}
}
