<x-layouts.backend title="Peraturan" :listNav="[['label' => 'Peraturan']]">
	@php
		$selectBentukPeraturan = DB::table('document')
		    ->where('tipe_dokumen', 1)
		    ->select('bentuk_peraturan')
		    ->distinct()
		    ->pluck('bentuk_peraturan');

		$selectKeteranganStatus = DB::table('status')->whereIn('id', [2, 4, 6, 7])->pluck('status');
	@endphp

	<x-backend.table-data title="Peraturan" :data="$data" prefixRoute="peraturan" :columns="[
	    [
	        'title' => 'Jenis Peraturann',
	        'key' => 'bentuk_peraturan',
	        'type_search' => 'select',
	        'data_type_search' => $selectBentukPeraturan,
	    ],
	    ['title' => 'Nomor', 'key' => 'nomor_peraturan'],
	    ['title' => 'Tahun', 'key' => 'tahun_terbit'],
	    ['title' => 'Judul Peraturan', 'key' => 'judul', 'format' => 'href'],
	    [
	        'title' => 'Status Peraturan',
	        'key' => 'status',
	        'type_search' => 'select',
	        'data_type_search' => ['Berlaku', 'Tidak Berlaku'],
	    ],
	    [
	        'title' => 'Keterangan Status',
	        'key' => 'status_peraturan',
	        'format' => 'ucfirst',
	        'type_search' => 'select',
	        'data_type_search' => $selectKeteranganStatus,
	    ],
	]" />

</x-layouts.backend>
