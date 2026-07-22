<x-layouts.backend :title="$title" :listNav="[['label' => 'Berita', 'route' => route('backend.berita.index')], ['label' => $title]]">

@php
    $image = checkFilePath(config('app.img_directory'), $data->image)
        ? 'storage/' . config('app.img_directory') . $data->image
        : config('app.default_img');
@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-show / .vid-show) ── */
    .ber-show { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .ber-show .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }

    /* Header */
    .ber-show .box-header.with-border { border-bottom:1px solid var(--line); padding:20px 22px; }
    .ber-show .eyebrow { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--soft); font-weight:600; margin-bottom:6px; }
    .ber-show .doc-title { font-size:21px; line-height:1.32; font-weight:600; letter-spacing:-.01em; color:var(--ink); margin:0; max-width:60ch; }
    .ber-show .doc-tags { margin-top:13px; display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
    .ber-show .box-header .actions { margin-top:2px; }
    .ber-show .box-header .btn { border-radius:5px; }

    /* Tag */
    .ber-show .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .ber-show .tag-published { background:#e5f3e9; color:#227a3b; }
    .ber-show .tag-draft { background:#f2efe9; color:#77706a; }
    .ber-show .meta-date { font-size:12.5px; color:var(--muted); }

    /* Section heading */
    .ber-show h4 { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:26px 0 11px; padding-bottom:8px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:9px; }
    .ber-show .pane > h4:first-child { margin-top:4px; }
    .ber-show h4::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }

    /* Isi berita (rich text) */
    .ber-show .isi-content { border:1px solid var(--line); border-radius:7px; padding:16px 18px; background:#fff; color:var(--ink); font-size:14px; line-height:1.72; }
    .ber-show .isi-content img { max-width:100%; height:auto; border-radius:4px; }
    .ber-show .isi-content p:last-child { margin-bottom:0; }
    .ber-show .isi-empty { color:var(--soft); font-size:13px; }

    /* Definition tables */
    .ber-show table.table { border:1px solid var(--line); border-radius:6px; overflow:hidden; margin-bottom:6px; }
    .ber-show table.table > tbody > tr > th { background:#faf9f7; color:var(--muted); font-weight:600; font-size:12px; width:34%; vertical-align:top; border-color:var(--line) !important; padding:10px 14px; letter-spacing:.01em; }
    .ber-show table.table > tbody > tr > td { color:var(--ink); font-size:13.5px; line-height:1.62; border-color:var(--line) !important; padding:10px 14px; vertical-align:top; }
    .ber-show table.table > tbody > tr:hover > td { background:#fcfbf9; }

    /* Media */
    .ber-show .media-frame { border:1px solid var(--line); border-radius:8px; overflow:hidden; background:#faf9f7; }
    .ber-show .media-frame img { display:block; width:100%; height:auto; }

    /* Metadata */
    .ber-show .meta-time { margin-top:4px; font-size:12.5px; color:var(--muted); line-height:2; }
    .ber-show .meta-time i { color:var(--soft); width:17px; text-align:center; }
    .ber-show .meta-time b { color:var(--ink); font-weight:600; }
</style>
@endpush

<div class="row ber-show">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-right actions">
                    <a href="{{ route('backend.berita.edit', $data->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                    <a href="{{ route('backend.berita.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <form style="display:inline" action="{{ route('backend.berita.destroy', $data->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin akan menghapus data ini?')">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
                <div class="eyebrow">Detail · Berita</div>
                <h3 class="doc-title">{{ $data->judul }}</h3>
                <div class="doc-tags">
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
                        <h4>Isi Berita</h4>
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
                        <h4>Gambar Sampul</h4>
                        <div class="media-frame">
                            <img src="{{ asset($image) }}" alt="Sampul berita">
                        </div>

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
