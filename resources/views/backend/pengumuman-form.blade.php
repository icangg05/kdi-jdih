<x-layouts.backend :title="$title" :listNav="[['label' => 'Pengumuman', 'route' => route('backend.pengumuman.index')], ['label' => $title]]">
  @php
    $isCreate = request()->routeIs('backend.pengumuman.create') ? true : false;
  @endphp

  <div class="box-body no-padding">
    <form class="form-horizontal"
      action="{{ $isCreate ? route('backend.pengumuman.store') : route('backend.pengumuman.update', $pengumuman->id) }}"
      method="POST" enctype="multipart/form-data">
      @csrf
      @if (!$isCreate)
        @method('PATCH')
      @endif

      <div class="box box-primary box-solid">
        <div class="box-header with-border">
          <b>Form {{ $title }}</b>
        </div>
        <div class="box-body">
          <x-backend.input.date :value="$pengumuman->tanggal ?? ''" label="Tanggal" key="tanggal" :required="true"
            placeholder="Tulis tanggal pengumuman" />

          <x-backend.input.text :value="$pengumuman->judul ?? ''" label="Judul" key="judul" :required="true" />
          <x-backend.input.text :value="$pengumuman->tag ?? ''" label="Tag" key="tag" :required="true" :length="100" />

          <x-backend.input.editor :value="$pengumuman->isi ?? ''" label="Isi" key="isi" :required="true"
            placeholder="Tulis isi pengumuman..." />

          <x-backend.input.file label="Gambar" key="image" :mimes="['jpg', 'jpeg', 'png']" />
          <x-backend.input.file label="Dokumen" key="dokumen" :mimes="['pdf']" />

          <x-backend.input.select :value="$pengumuman->status ?? ''" label="Status" key="status" placeholder="Pilih Status..."
            :required="true" :data="[['label' => 'Publish', 'value' => 1], ['label' => 'Tidak Publish', 'value' => 0]]" />

        </div>
        <div class="box-footer">
          <button type="submit" class="btn btn-success btn-flat">
            <i class="fa fa-save"></i> Simpan</button>
          <a class="btn btn-danger btn-flat" href="{{ route('backend.pengumuman.index') }}">
            <i class="fa fa-remove"></i> Batal</a>
        </div>
      </div>
    </form>
  </div>
</x-layouts.backend>
