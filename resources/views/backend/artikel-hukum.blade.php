<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">
	@php
		$selectJenisArtikel = DB::table('document')
		    ->where('tipe_dokumen', 3)
		    ->select('jenis_peraturan')
		    ->distinct()
		    ->pluck('jenis_peraturan');
	@endphp

	<x-backend.table-data :title="$title" :data="$data" prefixRoute="artikel" :columns="[
	    [
	        'title' => 'Jenis Artikel',
	        'key' => 'jenis_peraturan',
	        'type_search' => 'select',
	        'data_type_search' => $selectJenisArtikel,
	    ],
	    ['title' => 'Judul Artikel', 'key' => 'judul', 'format' => 'href'],
	    ['title' => 'Tahun', 'key' => 'tahun_terbit'],
	    ['title' => 'Sumber', 'key' => 'sumber'],
	]" />

</x-layouts.backend>
