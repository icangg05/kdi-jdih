<x-layouts.backend title="Berita" :listNav="[['label' => 'Berita']]">
  <x-backend.table-data title="Berita" :data="$data" prefixRoute="berita" :columns="[
      ['title' => 'Tanggal', 'key' => 'tanggal', 'format' => 'date'],
      ['title' => 'Judul', 'key' => 'judul', 'limit' => 100, 'format' => 'href'],
      ['title' => 'Isi Berita', 'key' => 'isi', 'limit' => 150, 'strip' => true],
  ]" />
</x-layouts.backend>
