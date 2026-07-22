<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">
	@php
		$selectJenisMonografi = DB::table('document')
		    ->where('tipe_dokumen', 2)
		    ->select('jenis_peraturan')
		    ->distinct()
		    ->pluck('jenis_peraturan');

		$hasFilter = request()->hasAny(['jenis_peraturan', 'judul', 'tahun_terbit', 'sumber_perolehan', 'subyek', 'kode_eksemplar']);
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .per-index / .ih-index) ── */
    .mon-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .mon-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .mon-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    .mon-index .mon-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .mon-index .mon-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .mon-index .mon-meta .reset { color:var(--accent); font-size:12px; }

    .mon-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .mon-tablewrap table.table { min-width:1080px; margin-bottom:0; }
    .mon-index table.table { border:1px solid var(--line); }
    .mon-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }
    .mon-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .mon-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .mon-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .mon-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .mon-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .mon-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .mon-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .mon-index .filters .btn-reset:hover { color:var(--ink); }
    .mon-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    .mon-index table.table > tbody > tr { transition:background .12s ease; }
    .mon-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .mon-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; font-size:13px; }
    .mon-index .num { font-variant-numeric:tabular-nums; }
    .mon-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .mon-index .judul-link:hover { color:var(--accent); }
    .mon-index .muted { color:var(--muted); font-size:12.5px; }
    .mon-index .code { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:12px; background:#f2efe9; color:#5a4b3f; padding:2px 7px; border-radius:4px; }
    .mon-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; background:#eef1f4; color:#3a4149; }

    .mon-index .act { display:flex; justify-content:center; gap:0; }
    .mon-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .mon-index .act .btn:hover { color:var(--ink); }
    .mon-index .act .btn.danger:hover { color:var(--accent); }

    .mon-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .mon-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .mon-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .mon-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row mon-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $title }}</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.monografi.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <form id="monFilter" method="GET" action="{{ route('backend.monografi.index') }}"></form>

                @if($data->total() > 0)
                <div class="mon-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> monografi</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.monografi.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="mon-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="3%" class="text-center">No</th>
                            <th width="13%">Jenis Monografi</th>
                            <th>Judul Monografi</th>
                            <th width="6%" class="text-center">Tahun</th>
                            <th width="13%">Sumber Perolehan</th>
                            <th width="13%">Subjek</th>
                            <th width="11%">Kode Eksemplar</th>
                            <th width="9%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <select form="monFilter" name="jenis_peraturan" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    @foreach($selectJenisMonografi as $j)
                                        <option value="{{ $j }}" {{ request('jenis_peraturan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th><input form="monFilter" type="text" name="judul" class="form-control" placeholder="Cari judul…" value="{{ request('judul') }}"></th>
                            <th><input form="monFilter" type="text" name="tahun_terbit" class="form-control" placeholder="Tahun…" value="{{ request('tahun_terbit') }}"></th>
                            <th><input form="monFilter" type="text" name="sumber_perolehan" class="form-control" placeholder="Sumber…" value="{{ request('sumber_perolehan') }}"></th>
                            <th><input form="monFilter" type="text" name="subyek" class="form-control" placeholder="Subjek…" value="{{ request('subyek') }}"></th>
                            <th><input form="monFilter" type="text" name="kode_eksemplar" class="form-control" placeholder="Kode…" value="{{ request('kode_eksemplar') }}"></th>
                            <th>
                                <div class="filter-act">
                                    <button form="monFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari"><i class="fa fa-search"></i></button>
                                    <a href="{{ route('backend.monografi.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset"><i class="fa fa-refresh"></i></a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center num">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td>@if($item->jenis_peraturan)<span class="tag">{{ $item->jenis_peraturan }}</span>@else<span class="muted">—</span>@endif</td>
                            <td><a href="{{ route('backend.monografi.show', $item->id) }}" class="judul-link">{{ \Illuminate\Support\Str::limit($item->judul, 80) }}</a></td>
                            <td class="text-center num">{{ $item->tahun_terbit ?: '—' }}</td>
                            <td class="muted">{{ \Illuminate\Support\Str::limit($item->sumber_perolehan, 24) ?: '—' }}</td>
                            <td class="muted">{{ \Illuminate\Support\Str::limit($item->subyek, 24) ?: '—' }}</td>
                            <td>@if($item->kode_eksemplar)<span class="code">{{ $item->kode_eksemplar }}</span>@else<span class="muted">—</span>@endif</td>
                            <td class="text-center act">
                                <a href="{{ route('backend.monografi.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('backend.monografi.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit"><i class="fa fa-pencil"></i></a>
                                <form action="{{ route('backend.monografi.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus" onclick="return confirm('Yakin akan menghapus data ini?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8">
                                <div class="mon-empty">
                                    <i class="fa fa-book"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada monografi yang cocok dengan filter.<br><a href="{{ route('backend.monografi.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada monografi</h4>
                                        <p>Mulai dengan menambahkan monografi pertama.</p>
                                        <a href="{{ route('backend.monografi.create') }}" class="btn btn-danger btn-sm"><i class="fa fa-plus"></i> Tambah data</a>
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
                <div class="pull-right">{{ $data->appends(request()->query())->links() }}</div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.backend>
