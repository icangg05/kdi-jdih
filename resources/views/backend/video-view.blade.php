<x-layouts.backend :title="$title" :listNav="[['label' => 'Video', 'route' => route('backend.video.index')], ['label' => $title]]">

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-show / .puu-show) ── */
    .vid-show { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .vid-show .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }

    /* Header */
    .vid-show .box-header.with-border { border-bottom:1px solid var(--line); padding:20px 22px; }
    .vid-show .eyebrow { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--soft); font-weight:600; margin-bottom:6px; }
    .vid-show .doc-title { font-size:21px; line-height:1.32; font-weight:600; letter-spacing:-.01em; color:var(--ink); margin:0; max-width:60ch; }
    .vid-show .doc-tags { margin-top:13px; display:flex; flex-wrap:wrap; gap:6px; align-items:center; }
    .vid-show .box-header .actions { margin-top:2px; }
    .vid-show .box-header .btn { border-radius:5px; }
    .vid-show .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; background:#eef1f4; color:#3a4149; }
    .vid-show .meta-date { font-size:12.5px; color:var(--muted); }

    /* Section heading */
    .vid-show h4 { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:26px 0 11px; padding-bottom:8px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:9px; }
    .vid-show .pane > h4:first-child { margin-top:4px; }
    .vid-show h4::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }

    /* Player 16:9 responsif */
    .vid-show .player { position:relative; width:100%; padding-top:56.25%; border-radius:9px; overflow:hidden; background:#1f2328; border:1px solid var(--line); }
    .vid-show .player iframe { position:absolute; inset:0; width:100%; height:100%; border:0; }

    /* Definition tables */
    .vid-show table.table { border:1px solid var(--line); border-radius:6px; overflow:hidden; margin-bottom:6px; }
    .vid-show table.table > tbody > tr > th { background:#faf9f7; color:var(--muted); font-weight:600; font-size:12px; width:34%; vertical-align:top; border-color:var(--line) !important; padding:10px 14px; letter-spacing:.01em; }
    .vid-show table.table > tbody > tr > td { color:var(--ink); font-size:13.5px; line-height:1.62; border-color:var(--line) !important; padding:10px 14px; vertical-align:top; }
    .vid-show table.table > tbody > tr:hover > td { background:#fcfbf9; }
    .vid-show .code { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:12.5px; background:#f2efe9; color:#5a4b3f; padding:2px 7px; border-radius:4px; }

    /* Watch button */
    .vid-show .watch { display:inline-flex; align-items:center; gap:8px; margin-top:2px; padding:9px 15px; border-radius:7px; background:var(--accent); color:#fff; font-size:13px; font-weight:600; transition:background .14s ease; }
    .vid-show .watch:hover { background:#a5271b; color:#fff; text-decoration:none; }

    /* Metadata */
    .vid-show .meta-time { margin-top:4px; font-size:12.5px; color:var(--muted); line-height:2; }
    .vid-show .meta-time i { color:var(--soft); width:17px; text-align:center; }
    .vid-show .meta-time b { color:var(--ink); font-weight:600; }
</style>
@endpush

<div class="row vid-show">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-right actions">
                    <a href="{{ route('backend.video.edit', $data->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                    <a href="{{ route('backend.video.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <form style="display:inline" action="{{ route('backend.video.destroy', $data->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin akan menghapus data ini?')">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
                <div class="eyebrow">Detail · Video</div>
                <h3 class="doc-title">{{ $data->judul }}</h3>
                <div class="doc-tags">
                    <span class="tag"><i class="fa fa-youtube-play"></i> YouTube</span>
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
                        <h4>Pratinjau Video</h4>
                        <div class="player">
                            <iframe src="https://www.youtube.com/embed/{{ $data->link }}"
                                title="{{ $data->judul }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
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
                                <th>Kode / Link</th>
                                <td><span class="code">{{ $data->link }}</span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-4 pane">
                        <h4>Tautan</h4>
                        <a href="https://www.youtube.com/watch?v={{ $data->link }}" target="_blank" rel="noopener" class="watch">
                            <i class="fa fa-youtube-play"></i> Buka di YouTube
                        </a>

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
