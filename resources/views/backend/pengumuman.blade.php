<x-layouts.backend title="Pengumuman" :listNav="[['label' => 'Pengumuman']]">

@php
    $hasFilter = request()->hasAny(['judul', 'tag', 'tanggal', 'isi']);
@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .puu-index / .dis-index) ── */
    .peng-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .peng-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .peng-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    /* Meta bar */
    .peng-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .peng-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .peng-meta .reset { color:var(--accent); font-size:12px; }

    /* Tabel */
    .peng-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .peng-tablewrap table.table { min-width:820px; margin-bottom:0; }
    .peng-index table.table { border:1px solid var(--line); }
    .peng-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }

    /* Baris filter per-kolom (di bawah header) */
    .peng-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .peng-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .peng-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .peng-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .peng-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .peng-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .peng-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .peng-index .filters .btn-reset:hover { color:var(--ink); }
    .peng-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    /* Body rows */
    .peng-index table.table > tbody > tr { transition:background .12s ease; }
    .peng-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .peng-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; }
    .peng-index .num { font-variant-numeric:tabular-nums; }
    .peng-index .cell-date { color:var(--muted); font-size:12.5px; white-space:nowrap; }
    .peng-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .peng-index .judul-link:hover { color:var(--accent); }
    .peng-index .excerpt { color:var(--soft); font-size:12.5px; line-height:1.55; }

    /* Badge halus */
    .peng-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .peng-index .tag-cat { background:#eef1f4; color:#3a4149; }
    .peng-index .tag-published { background:#e5f3e9; color:#227a3b; }
    .peng-index .tag-draft { background:#f2efe9; color:#77706a; }

    /* Aksi */
    .peng-index .act { display:flex; justify-content:center; gap:0; }
    .peng-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .peng-index .act .btn:hover { color:var(--ink); }
    .peng-index .act .btn.danger:hover { color:var(--accent); }

    /* Empty state */
    .peng-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .peng-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .peng-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    /* Pagination (bootstrap) — rata kanan, tunggal */
    .peng-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row peng-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Pengumuman</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.pengumuman.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                {{-- Form filter (di-referensikan input per-kolom lewat atribut form) --}}
                <form id="pengFilter" method="GET" action="{{ route('backend.pengumuman.index') }}"></form>

                {{-- ── Meta ── --}}
                @if($data->total() > 0)
                <div class="peng-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> pengumuman</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.pengumuman.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                {{-- ── Tabel ── --}}
                <div class="peng-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="4%" class="text-center">No</th>
                            <th width="13%">Tanggal</th>
                            <th width="30%">Judul</th>
                            <th width="14%">Tag</th>
                            <th>Isi Pengumuman</th>
                            <th width="9%" class="text-center">Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <input form="pengFilter" type="text" name="tanggal" class="form-control"
                                       placeholder="cth. Juli 2026" value="{{ request('tanggal') }}">
                            </th>
                            <th>
                                <input form="pengFilter" type="text" name="judul" class="form-control"
                                       placeholder="Cari judul…" value="{{ request('judul') }}">
                            </th>
                            <th>
                                <input form="pengFilter" type="text" name="tag" class="form-control"
                                       placeholder="Cari tag…" value="{{ request('tag') }}">
                            </th>
                            <th>
                                <input form="pengFilter" type="text" name="isi" class="form-control"
                                       placeholder="Cari isi…" value="{{ request('isi') }}">
                            </th>
                            <th></th>
                            <th>
                                <div class="filter-act">
                                    <button form="pengFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ route('backend.pengumuman.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset">
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
                                <a href="{{ route('backend.pengumuman.show', $item->id) }}" class="judul-link">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 70) }}
                                </a>
                            </td>
                            <td>
                                @if($item->tag)
                                    <span class="tag tag-cat">{{ $item->tag }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
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
                                <a href="{{ route('backend.pengumuman.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.pengumuman.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('backend.pengumuman.destroy', $item->id) }}" method="POST" style="display:inline;">
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
                            <td colspan="7">
                                <div class="peng-empty">
                                    <i class="fa fa-bullhorn"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada pengumuman yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.pengumuman.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada pengumuman</h4>
                                        <p>Mulai dengan menambahkan pengumuman pertama.</p>
                                        <a href="{{ route('backend.pengumuman.create') }}" class="btn btn-danger btn-sm">
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
