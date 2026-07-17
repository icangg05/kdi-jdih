<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">
	@php
		$selectJenisMonografi = DB::table('document')
		    ->where('tipe_dokumen', 2)
		    ->select('jenis_peraturan')
		    ->distinct()
		    ->pluck('jenis_peraturan');
	@endphp

	<x-backend.table-data :title="$title" :data="$data" prefixRoute="monografi" :columns="[
	    [
	        'title' => 'Jenis Monografi',
	        'key' => 'jenis_peraturan',
	        'type_search' => 'select',
	        'data_type_search' => $selectJenisMonografi,
	    ],
	    ['title' => 'Judul Monografi', 'key' => 'judul', 'format' => 'href'],
	    ['title' => 'Tahun', 'key' => 'tahun_terbit'],
	    ['title' => 'Sumber Perolehan', 'key' => 'sumber_perolehan'],
	    ['title' => 'Subjek', 'key' => 'subyek'],
	    ['title' => 'Kode Eksemplar', 'key' => 'kode_eksemplar'],
	]" />

</x-layouts.backend>
