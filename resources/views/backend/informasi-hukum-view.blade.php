<x-layouts.backend :title="$title" :listNav="[['label' => 'Informasi Hukum', 'route' => route('backend.informasi-hukum.index')], ['label' => $title]]">

@php
    $image = checkFilePath(config('app.img_directory'), $data->image)
        ? 'storage/' . config('app.img_directory') . $data->image
        : config('app.default_img');
    $hasDoc = checkFilePath(config('app.doc_directory'), $data->dokumen);
    $jenis = DB::table('jenis_informasi_hukum')->where('id', $data->jenis)->first();
    $jenisLabel = $jenis ? trim($jenis->name . ' — ' . $jenis->singkatan, ' —') : ($data->jenis ?: '-');
@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-show / .ber-show) ── */
    .ih-show { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .ih-show .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }

    .ih-show .box-header.with-border { border-bottom:1px solid var(--line); padding:20px 22px; }
    .ih-show .eyebrow { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--soft); font-weight:600; margin-bottom:6px; }
    .ih-show .doc-title { font-size:21px; line-height:1.32; font-weight:600; letter-spacing:-.01em; color:var(--ink); margin:0; max-width:60ch; }
    .ih-show .doc-tags { margin-top:13px; display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
    .ih-show .box-header .actions { margin-top:2px; }
    .ih-show .box-header .btn { border-radius:5px; }

    .ih-show .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .ih-show .tag-jenis { background:#eef1f4; color:#3a4149; }
    .ih-show .tag-published { background:#e5f3e9; color:#227a3b; }
    .ih-show .tag-draft { background:#f2efe9; color:#77706a; }
    .ih-show .meta-date { font-size:12.5px; color:var(--muted); }

    .ih-show h4 { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:26px 0 11px; padding-bottom:8px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:9px; }
    .ih-show .pane > h4:first-child { margin-top:4px; }
    .ih-show h4::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }

    .ih-show .isi-content { border:1px solid var(--line); border-radius:7px; padding:16px 18px; background:#fff; color:var(--ink); font-size:14px; line-height:1.72; }
    .ih-show .isi-content img { max-width:100%; height:auto; border-radius:4px; }
    .ih-show .isi-content p:last-child { margin-bottom:0; }
    .ih-show .isi-empty { color:var(--soft); font-size:13px; }

    .ih-show table.table { border:1px solid var(--line); border-radius:6px; overflow:hidden; margin-bottom:6px; }
    .ih-show table.table > tbody > tr > th { background:#faf9f7; color:var(--muted); font-weight:600; font-size:12px; width:34%; vertical-align:top; border-color:var(--line) !important; padding:10px 14px; letter-spacing:.01em; }
    .ih-show table.table > tbody > tr > td { color:var(--ink); font-size:13.5px; line-height:1.62; border-color:var(--line) !important; padding:10px 14px; vertical-align:top; }
    .ih-show table.table > tbody > tr:hover > td { background:#fcfbf9; }

    .ih-show .media-frame { border:1px solid var(--line); border-radius:8px; overflow:hidden; background:#faf9f7; }
    .ih-show .media-frame img { display:block; width:100%; height:auto; }

    .ih-show .file-item { display:flex; align-items:center; gap:11px; padding:11px 13px; border:1px solid var(--line); border-radius:7px; background:#fff; color:var(--ink); font-size:13px; font-weight:500; transition:border-color .14s ease, transform .14s ease, box-shadow .14s ease; width:100%; text-align:left; margin:0; }
    .ih-show button.file-item { cursor:pointer; }
    .ih-show .file-item:hover { border-color:#d8b3ad; box-shadow:0 2px 9px rgba(192,57,43,.08); transform:translateY(-1px); }
    .ih-show .file-item .ic { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; background:#fdecea; color:var(--accent); flex:none; }
    .ih-show .file-item .arw { margin-left:auto; color:var(--soft); font-size:12px; }
    .ih-show .file-item[disabled] { opacity:.55; cursor:not-allowed; }
    .ih-show .file-item[disabled] .ic { background:#f3f0ec; color:var(--soft); }

    .ih-show .meta-time { margin-top:4px; font-size:12.5px; color:var(--muted); line-height:2; }
    .ih-show .meta-time i { color:var(--soft); width:17px; text-align:center; }
    .ih-show .meta-time b { color:var(--ink); font-weight:600; }
</style>
@endpush

<div class="row ih-show">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-right actions">
                    <a href="{{ route('backend.informasi-hukum.edit', $data->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                    <a href="{{ route('backend.informasi-hukum.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <form style="display:inline" action="{{ route('backend.informasi-hukum.destroy', $data->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin akan menghapus data ini?')">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
                <div class="eyebrow">Detail · Informasi Hukum</div>
                <h3 class="doc-title">{{ $data->judul }}</h3>
                <div class="doc-tags">
                    @if($jenis)
                        <span class="tag tag-jenis">{{ $jenis->singkatan }}</span>
                    @endif
                    @if($data->status == 1)
                        <span class="tag tag-published">Publish</span>
                    @else
                        <span class="tag tag-draft">Tidak Publish</span>
                    @endif
                    <span class="meta-date">
                        <i class="fa fa-calendar-o"></i>
                        {{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}
                    </span>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 pane">
                        <h4>Isi</h4>
                        <div class="isi-content">
                            @if(trim(strip_tags($data->isi)) !== '')
                                {!! $data->isi !!}
                            @else
                                <span class="isi-empty">Tidak ada isi.</span>
                            @endif
                        </div>

                        <h4>Informasi</h4>
                        <table class="table">
                            <tr>
                                <th>Jenis Informasi Hukum</th>
                                <td>{{ $jenisLabel }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td>{{ \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>{{ $data->judul }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($data->status == 1)
                                        <span class="tag tag-published">Publish</span>
                                    @else
                                        <span class="tag tag-draft">Tidak Publish</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-4 pane">
                        <h4>Media & Dokumen</h4>
                        <div class="media-frame" style="margin-bottom:10px;">
                            <img src="{{ asset($image) }}" alt="Gambar informasi hukum">
                        </div>

                        @if($hasDoc)
                            <form action="{{ route('download_file') }}" method="POST">
                                @csrf
                                <input type="hidden" name="filePath" value="{{ config('app.doc_directory') . $data->dokumen }}">
                                <button type="submit" class="file-item">
                                    <span class="ic"><i class="fa fa-file-pdf-o"></i></span>
                                    Dokumen (PDF)
                                    <span class="arw"><i class="fa fa-download"></i></span>
                                </button>
                            </form>
                        @else
                            <div class="file-item" disabled>
                                <span class="ic"><i class="fa fa-file-o"></i></span>
                                Belum ada dokumen
                            </div>
                        @endif

                        <h4>Riwayat</h4>
                        <div class="meta-time">
                            <div><i class="fa fa-user-o"></i> Dibuat oleh <b>{{ ucfirst($data->created_by) }}</b></div>
                            <div><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($data->created_at)->translatedFormat('d F Y, H:i') }}</div>
                            <div style="margin-top:6px;"><i class="fa fa-user-o"></i> Diubah oleh <b>{{ ucfirst($data->updated_by) }}</b></div>
                            <div><i class="fa fa-refresh"></i> {{ \Carbon\Carbon::parse($data->updated_at)->translatedFormat('d F Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.backend>
