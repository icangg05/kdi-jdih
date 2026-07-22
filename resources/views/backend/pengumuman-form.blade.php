<x-layouts.backend :title="$title" :listNav="[['label' => 'Pengumuman', 'route' => route('backend.pengumuman.index')], ['label' => $title]]">
  @php
    $isCreate = request()->routeIs('backend.pengumuman.create') ? true : false;
  @endphp

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras .puu-form) ── */
    .peng-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:960px; }

    .peng-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .peng-form .box.box-solid > .box-header { background:#fff; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .peng-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .peng-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .peng-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    /* Aksen per peran box */
    .peng-form .box.box-info    { border-top-color:#2f6fb0; }
    .peng-form .box.box-info    > .box-header::before { background:#2f6fb0; }
    .peng-form .box.box-success { border-top-color:#2b9348; }
    .peng-form .box.box-success > .box-header::before { background:#2b9348; }

    /* Label & input */
    .peng-form .control-label { color:var(--muted); font-weight:600; font-size:13px; }
    .peng-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; transition:border-color .14s ease, box-shadow .14s ease; }
    .peng-form input.form-control:not(.file-loading) { height:38px; }
    .peng-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .peng-form .form-group { margin-bottom:16px; }
    .peng-form .help-block { font-size:12px; color:var(--soft); }
    .peng-form .input-group-addon { background:#faf9f7; border-color:#dcd9d3; color:var(--muted); }

    /* Select2 selaras */
    .peng-form .select2-container--default .select2-selection--single { border-color:#dcd9d3 !important; border-radius:6px; }
    .peng-form .select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }

    /* Summernote editor selaras */

    /* Checkbox auto-translate */
    .peng-form input[type="checkbox"] { margin-right:5px; }

    /* Baris aksi */
    .peng-form .box-footer { background:transparent; border-top:none; padding:4px 0 0; }
    .peng-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .peng-form .btn-success.btn-flat { background:var(--accent); border-color:var(--accent); }
    .peng-form .btn-success.btn-flat:hover, .peng-form .btn-success.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
    .peng-form .btn-danger.btn-flat { background:#fff; color:var(--muted); border:1px solid #dcd9d3; }
    .peng-form .btn-danger.btn-flat:hover { background:#f6f4f1; color:var(--ink); }
</style>
@endpush

  <div class="box-body no-padding">
    <div class="section peng-form">
      <form class="form-horizontal"
        action="{{ $isCreate ? route('backend.pengumuman.store') : route('backend.pengumuman.update', $pengumuman->id) }}"
        method="POST" enctype="multipart/form-data">
        @csrf
        @if (!$isCreate)
          @method('PATCH')
        @endif

        {{-- INFORMASI PENGUMUMAN --}}
        <div class="box box-primary box-solid">
          <div class="box-header with-border">
            <b>Informasi Pengumuman</b>
          </div>
          <div class="box-body">
            <x-backend.input.date :value="$pengumuman->tanggal ?? ''" label="Tanggal" key="tanggal" :required="true"
              placeholder="Tulis tanggal pengumuman" />

            <x-backend.input.text :value="$pengumuman->judul ?? ''" label="Judul" key="judul" :required="true" />
            <x-backend.input.text :value="$pengumuman->tag ?? ''" label="Tag" key="tag" :required="true" :length="100" />

            <x-backend.input.editor-quill :value="$pengumuman->isi ?? ''" label="Isi" key="isi" :required="true"
              placeholder="Tulis isi pengumuman..." />
          </div>
        </div>

        {{-- MEDIA & DOKUMEN --}}
        <div class="box box-info box-solid">
          <div class="box-header with-border">
            <b>Media &amp; Dokumen</b>
          </div>
          <div class="box-body">
            <x-backend.input.file label="Gambar" key="image" :mimes="['jpg', 'jpeg', 'png']" />
            <x-backend.input.file label="Dokumen" key="dokumen" :mimes="['pdf']" />
          </div>
        </div>

        {{-- PUBLIKASI --}}
        <div class="box box-success box-solid">
          <div class="box-header with-border">
            <b>Publikasi</b>
          </div>
          <div class="box-body">
            <x-backend.input.select :value="$pengumuman->status ?? ''" label="Status" key="status" placeholder="Pilih Status..."
              :required="true" :data="[['label' => 'Publish', 'value' => 1], ['label' => 'Tidak Publish', 'value' => 0]]" />

            @include('backend.partials.auto-translate-checkbox')
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
  </div>
</x-layouts.backend>
