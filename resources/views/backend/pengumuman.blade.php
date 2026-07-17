<x-layouts.backend title="Pengumuman" :listNav="[['label' => 'Pengumuman']]">

  <x-backend.table-data title="Pengumuman" :data="$data" prefixRoute="pengumuman" :columns="[
      ['title' => 'Tanggal', 'key' => 'tanggal', 'format' => 'date'],
      ['title' => 'Judul', 'key' => 'judul', 'limit' => 70, 'format' => 'href'],
      ['title' => 'Tag', 'key' => 'tag'],
      ['title' => 'Isi Pengumuman', 'key' => 'isi', 'limit' => 100, 'strip' => true],
  ]" />

</x-layouts.backend>
