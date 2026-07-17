<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">
	@php
		$selectJenisPutusan = DB::table('document')
		    ->where('tipe_dokumen', 4)
		    ->select('jenis_peraturan')
		    ->distinct()
		    ->pluck('jenis_peraturan');
	@endphp

	<x-backend.table-data :title="$title" :data="$data" prefixRoute="putusan" :columns="[
	    [
	        'title' => 'Jenis Putusan',
	        'key' => 'jenis_peraturan',
	        'type_search' => 'select',
	        'data_type_search' => $selectJenisPutusan,
	    ],
	    ['title' => 'Nomor', 'key' => 'nomor_peraturan'],
	    ['title' => 'Tahun', 'key' => 'tahun_terbit'],
	    ['title' => 'Judul Putusan', 'key' => 'judul', 'format' => 'href'],
	    ['title' => 'Amar Putusan', 'key' => 'amar_status'],
	]" />

</x-layouts.backend>
