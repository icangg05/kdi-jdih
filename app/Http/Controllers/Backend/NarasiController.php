<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Narasi;
use Illuminate\Http\Request;

class NarasiController extends Controller
{
	public function edit($id = 1)
	{
		$title = 'Edit Data Narasi';
		$data = Narasi::findOrFail($id);
		

		return view('backend.narasi-form', compact(
			'title',
			'data',
		));
	}


	public function update(Request $request, $id)
	{
		$data = Narasi::findOrFail($id);
		
		$data->update($request->all());

		return redirect()->route('backend.narasi.edit')->with('success', 'Data narasi berhasil diupdate');
	}
}
