<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">
	@php
		$selectJenisPutusan = DB::table('document')
		    ->where('tipe_dokumen', 4)
		    ->select('jenis_peraturan')
		    ->distinct()
		    ->pluck('jenis_peraturan');

		$hasFilter = request()->hasAny(['jenis_peraturan', 'nomor_peraturan', 'tahun_terbit', 'judul', 'amar_status']);
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .per-index / .mon-index) ── */
    .put-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .put-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .put-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    .put-index .put-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .put-index .put-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .put-index .put-meta .reset { color:var(--accent); font-size:12px; }

    .put-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .put-tablewrap table.table { min-width:980px; margin-bottom:0; }
    .put-index table.table { border:1px solid var(--line); }
    .put-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }
    .put-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .put-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .put-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .put-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .put-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .put-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .put-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .put-index .filters .btn-reset:hover { color:var(--ink); }
    .put-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    .put-index table.table > tbody > tr { transition:background .12s ease; }
    .put-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .put-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; font-size:13px; }
    .put-index .num { font-variant-numeric:tabular-nums; }
    .put-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .put-index .judul-link:hover { color:var(--accent); }
    .put-index .cell-nomor { font-variant-numeric:tabular-nums; color:var(--muted); font-size:12.5px; white-space:nowrap; }
    .put-index .muted { color:var(--muted); font-size:12.5px; }
    .put-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; background:#eef1f4; color:#3a4149; }
    .put-index .tag-amar { background:#fdf2e0; color:#9a6a13; }

    .put-index .act { display:flex; justify-content:center; gap:0; }
    .put-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .put-index .act .btn:hover { color:var(--ink); }
    .put-index .act .btn.danger:hover { color:var(--accent); }

    .put-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .put-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .put-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .put-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row put-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $title }}</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.putusan.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <form id="putFilter" method="GET" action="{{ route('backend.putusan.index') }}"></form>

                @if($data->total() > 0)
                <div class="put-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> putusan</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.putusan.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="put-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="3%" class="text-center">No</th>
                            <th width="15%">Jenis Putusan</th>
                            <th width="11%">Nomor</th>
                            <th width="6%" class="text-center">Tahun</th>
                            <th>Judul Putusan</th>
                            <th width="14%">Amar Putusan</th>
                            <th width="9%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <select form="putFilter" name="jenis_peraturan" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    @foreach($selectJenisPutusan as $j)
                                        <option value="{{ $j }}" {{ request('jenis_peraturan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th><input form="putFilter" type="text" name="nomor_peraturan" class="form-control" placeholder="Nomor…" value="{{ request('nomor_peraturan') }}"></th>
                            <th><input form="putFilter" type="text" name="tahun_terbit" class="form-control" placeholder="Tahun…" value="{{ request('tahun_terbit') }}"></th>
                            <th><input form="putFilter" type="text" name="judul" class="form-control" placeholder="Cari judul…" value="{{ request('judul') }}"></th>
                            <th><input form="putFilter" type="text" name="amar_status" class="form-control" placeholder="Amar…" value="{{ request('amar_status') }}"></th>
                            <th>
                                <div class="filter-act">
                                    <button form="putFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari"><i class="fa fa-search"></i></button>
                                    <a href="{{ route('backend.putusan.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset"><i class="fa fa-refresh"></i></a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center num">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td>@if($item->jenis_peraturan)<span class="tag">{{ $item->jenis_peraturan }}</span>@else<span class="muted">—</span>@endif</td>
                            <td class="cell-nomor">{{ $item->nomor_peraturan ?: '—' }}</td>
                            <td class="text-center num">{{ $item->tahun_terbit ?: '—' }}</td>
                            <td><a href="{{ route('backend.putusan.show', $item->id) }}" class="judul-link">{{ \Illuminate\Support\Str::limit($item->judul, 85) }}</a></td>
                            <td>@if($item->amar_status)<span class="tag tag-amar">{{ \Illuminate\Support\Str::limit(strip_tags($item->amar_status), 22) }}</span>@else<span class="muted">—</span>@endif</td>
                            <td class="text-center act">
                                <a href="{{ route('backend.putusan.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('backend.putusan.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit"><i class="fa fa-pencil"></i></a>
                                <form action="{{ route('backend.putusan.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus" onclick="return confirm('Yakin akan menghapus data ini?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="put-empty">
                                    <i class="fa fa-balance-scale"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada putusan yang cocok dengan filter.<br><a href="{{ route('backend.putusan.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada putusan</h4>
                                        <p>Mulai dengan menambahkan putusan pertama.</p>
                                        <a href="{{ route('backend.putusan.create') }}" class="btn btn-danger btn-sm"><i class="fa fa-plus"></i> Tambah data</a>
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
