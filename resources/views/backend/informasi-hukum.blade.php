<x-layouts.backend title="Informasi Hukum" :listNav="[['label' => 'Informasi Hukum']]">

	<x-backend.table-data title="Informasi Hukum" :data="$data" prefixRoute="informasi-hukum" :columns="[
	    ['title' => 'Tanggal', 'key' => 'tanggal', 'format' => 'date'],
	    ['title' => 'Judul', 'key' => 'judul', 'limit' => 100, 'format' => 'href'],
	    ['title' => 'Isi Informasi Hukum', 'key' => 'isi', 'limit' => 150, 'strip' => true],
	]" />

</x-layouts.backend>
