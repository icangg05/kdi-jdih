<x-layouts.backend title="Video" :listNav="[['label' => 'Video']]">
	<x-backend.table-data title="Video" :data="$data" prefixRoute="video" :columns="[
	    ['title' => 'Tanggal', 'key' => 'tanggal', 'format' => 'date'],
	    ['title' => 'Judul', 'key' => 'judul', 'limit' => 100, 'format' => 'href'],
	    ['title' => 'Link', 'key' => 'link'],
	]" />
</x-layouts.backend>
