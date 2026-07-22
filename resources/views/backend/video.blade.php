<x-layouts.backend title="Video" :listNav="[['label' => 'Video']]">

@php
    $hasFilter = request()->hasAny(['judul', 'tanggal', 'link']);
@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-index / .puu-index) ── */
    .vid-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .vid-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .vid-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    /* Meta bar */
    .vid-index .vid-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .vid-index .vid-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .vid-index .vid-meta .reset { color:var(--accent); font-size:12px; }

    /* Tabel */
    .vid-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .vid-tablewrap table.table { min-width:720px; margin-bottom:0; }
    .vid-index table.table { border:1px solid var(--line); }
    .vid-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }

    /* Baris filter per-kolom */
    .vid-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .vid-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .vid-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .vid-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .vid-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .vid-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .vid-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .vid-index .filters .btn-reset:hover { color:var(--ink); }
    .vid-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    /* Body rows */
    .vid-index table.table > tbody > tr { transition:background .12s ease; }
    .vid-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .vid-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; }
    .vid-index .num { font-variant-numeric:tabular-nums; }
    .vid-index .cell-date { color:var(--muted); font-size:12.5px; white-space:nowrap; }

    /* Judul + thumb play */
    .vid-index .title-cell { display:flex; align-items:center; gap:11px; }
    .vid-index .thumb { width:52px; height:34px; border-radius:5px; background:#1f2328; color:#fff; display:flex; align-items:center; justify-content:center; flex:none; font-size:13px; box-shadow:inset 0 0 0 1px rgba(255,255,255,.08); }
    .vid-index .thumb i { opacity:.92; }
    .vid-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .vid-index .judul-link:hover { color:var(--accent); }

    /* Link kode */
    .vid-index .code-link { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; background:#f2efe9; color:#5a4b3f; padding:3px 8px; border-radius:4px; }
    .vid-index .code-link:hover { color:var(--accent); }

    /* Aksi */
    .vid-index .act { display:flex; justify-content:center; gap:0; }
    .vid-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .vid-index .act .btn:hover { color:var(--ink); }
    .vid-index .act .btn.danger:hover { color:var(--accent); }

    /* Empty state */
    .vid-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .vid-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .vid-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .vid-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row vid-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Video</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.video.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                {{-- Form filter (di-referensikan input per-kolom lewat atribut form) --}}
                <form id="vidFilter" method="GET" action="{{ route('backend.video.index') }}"></form>

                @if($data->total() > 0)
                <div class="vid-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> video</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.video.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="vid-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="4%" class="text-center">No</th>
                            <th width="14%">Tanggal</th>
                            <th>Judul</th>
                            <th width="22%">Link</th>
                            <th width="11%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <input form="vidFilter" type="text" name="tanggal" class="form-control"
                                       placeholder="cth. Juli 2026" value="{{ request('tanggal') }}">
                            </th>
                            <th>
                                <input form="vidFilter" type="text" name="judul" class="form-control"
                                       placeholder="Cari judul…" value="{{ request('judul') }}">
                            </th>
                            <th>
                                <input form="vidFilter" type="text" name="link" class="form-control"
                                       placeholder="Cari kode link…" value="{{ request('link') }}">
                            </th>
                            <th>
                                <div class="filter-act">
                                    <button form="vidFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ route('backend.video.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center num">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td class="cell-date">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="title-cell">
                                    <span class="thumb"><i class="fa fa-play"></i></span>
                                    <a href="{{ route('backend.video.show', $item->id) }}" class="judul-link">
                                        {{ \Illuminate\Support\Str::limit($item->judul, 80) }}
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="https://www.youtube.com/watch?v={{ $item->link }}" target="_blank" rel="noopener" class="code-link" title="Buka di YouTube">
                                    <i class="fa fa-youtube-play"></i> {{ \Illuminate\Support\Str::limit($item->link, 18) }}
                                </a>
                            </td>
                            <td class="text-center act">
                                <a href="{{ route('backend.video.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.video.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('backend.video.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus"
                                            onclick="return confirm('Yakin akan menghapus data ini?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="vid-empty">
                                    <i class="fa fa-film"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada video yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.video.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada video</h4>
                                        <p>Mulai dengan menambahkan video pertama.</p>
                                        <a href="{{ route('backend.video.create') }}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-plus"></i> Tambah data
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>
            </div>

            @if($data->hasPages())
            <div class="box-footer">
                <div class="pull-right">
                    {{ $data->appends(request()->query())->links() }}
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.backend>
