<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\EditorImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Upload gambar dari editor Quill (semua form). Siklus hidup file: lihat EditorImages.
class EditorImageController extends Controller
{
	public function __invoke(Request $request)
	{
		$max = EditorImages::MAX_KB / 1024 . ' MB';

		$request->validate([
			'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:' . EditorImages::MAX_KB],
		], [
			'image.required' => 'Pilih gambar yang akan diupload.',
			// File melebihi upload_max_filesize ditolak PHP sebelum sampai sini → aturan "uploaded"
			'image.uploaded' => "Gambar gagal diupload. Pastikan ukurannya tidak lebih dari {$max}.",
			'image.image'    => 'File harus berupa gambar (JPG, PNG, WEBP, atau GIF).',
			'image.mimes'    => 'File harus berupa gambar (JPG, PNG, WEBP, atau GIF).',
			'image.max'      => "Ukuran gambar maksimal {$max}.",
		]);

		$filename = uploadFile(EditorImages::DIRECTORY, $request->file('image'));
		EditorImages::prune();

		return response()->json(['url' => Storage::url(EditorImages::DIRECTORY . "/{$filename}")]);
	}
}
