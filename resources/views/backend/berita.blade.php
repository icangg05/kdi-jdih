<x-layouts.backend title="Berita" :listNav="[['label' => 'Berita']]">

@php
    $hasFilter = request()->hasAny(['judul', 'tanggal', 'isi']);
@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-index / .vid-index) ── */
    .ber-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .ber-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .ber-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    /* Meta bar */
    .ber-index .ber-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .ber-index .ber-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .ber-index .ber-meta .reset { color:var(--accent); font-size:12px; }

    /* Tabel */
    .ber-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .ber-tablewrap table.table { min-width:840px; margin-bottom:0; }
    .ber-index table.table { border:1px solid var(--line); }
    .ber-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }

    /* Baris filter per-kolom */
    .ber-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .ber-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .ber-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .ber-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .ber-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .ber-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .ber-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .ber-index .filters .btn-reset:hover { color:var(--ink); }
    .ber-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    /* Body rows */
    .ber-index table.table > tbody > tr { transition:background .12s ease; }
    .ber-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .ber-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; }
    .ber-index .num { font-variant-numeric:tabular-nums; }
    .ber-index .cell-date { color:var(--muted); font-size:12.5px; white-space:nowrap; }

    /* Judul + thumbnail sampul */
    .ber-index .title-cell { display:flex; align-items:center; gap:12px; }
    .ber-index .thumb { width:56px; height:40px; border-radius:6px; object-fit:cover; flex:none; border:1px solid var(--line); background:#f2efe9; }
    .ber-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .ber-index .judul-link:hover { color:var(--accent); }

    /* Excerpt */
    .ber-index .excerpt { color:var(--soft); font-size:12.5px; line-height:1.55; }

    /* Badge */
    .ber-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .ber-index .tag-published { background:#e5f3e9; color:#227a3b; }
    .ber-index .tag-draft { background:#f2efe9; color:#77706a; }

    /* Aksi */
    .ber-index .act { display:flex; justify-content:center; gap:0; }
    .ber-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .ber-index .act .btn:hover { color:var(--ink); }
    .ber-index .act .btn.danger:hover { color:var(--accent); }

    /* Empty state */
    .ber-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .ber-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .ber-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .ber-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row ber-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Berita</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.berita.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <form id="berFilter" method="GET" action="{{ route('backend.berita.index') }}"></form>

                @if($data->total() > 0)
                <div class="ber-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> berita</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.berita.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="ber-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="4%" class="text-center">No</th>
                            <th width="13%">Tanggal</th>
                            <th width="34%">Judul</th>
                            <th>Isi Berita</th>
                            <th width="9%" class="text-center">Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <input form="berFilter" type="text" name="tanggal" class="form-control"
                                       placeholder="cth. Juli 2026" value="{{ request('tanggal') }}">
                            </th>
                            <th>
                                <input form="berFilter" type="text" name="judul" class="form-control"
                                       placeholder="Cari judul…" value="{{ request('judul') }}">
                            </th>
                            <th>
                                <input form="berFilter" type="text" name="isi" class="form-control"
                                       placeholder="Cari isi…" value="{{ request('isi') }}">
                            </th>
                            <th></th>
                            <th>
                                <div class="filter-act">
                                    <button form="berFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ route('backend.berita.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        @php
                            $thumb = checkFilePath(config('app.img_directory'), $item->image)
                                ? asset('storage/' . config('app.img_directory') . $item->image)
                                : asset(config('app.default_img'));
                        @endphp
                        <tr>
                            <td class="text-center num">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td class="cell-date">{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="title-cell">
                                    <img class="thumb" src="{{ $thumb }}" alt="Sampul">
                                    <a href="{{ route('backend.berita.show', $item->id) }}" class="judul-link">
                                        {{ \Illuminate\Support\Str::limit($item->judul, 70) }}
                                    </a>
                                </div>
                            </td>
                            <td>
                                <span class="excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($item->isi), 90) ?: '—' }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->status == 1)
                                    <span class="tag tag-published">Publish</span>
                                @else
                                    <span class="tag tag-draft">Draft</span>
                                @endif
                            </td>
                            <td class="text-center act">
                                <a href="{{ route('backend.berita.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.berita.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('backend.berita.destroy', $item->id) }}" method="POST" style="display:inline;">
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
                            <td colspan="6">
                                <div class="ber-empty">
                                    <i class="fa fa-newspaper-o"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada berita yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.berita.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada berita</h4>
                                        <p>Mulai dengan menambahkan berita pertama.</p>
                                        <a href="{{ route('backend.berita.create') }}" class="btn btn-danger btn-sm">
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
