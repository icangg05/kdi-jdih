<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">
	@php
		$selectJenisArtikel = DB::table('document')
		    ->where('tipe_dokumen', 3)
		    ->select('jenis_peraturan')
		    ->distinct()
		    ->pluck('jenis_peraturan');

		$hasFilter = request()->hasAny(['jenis_peraturan', 'judul', 'tahun_terbit', 'sumber']);
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .per-index / .mon-index) ── */
    .art-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .art-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .art-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    .art-index .art-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .art-index .art-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .art-index .art-meta .reset { color:var(--accent); font-size:12px; }

    .art-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .art-tablewrap table.table { min-width:760px; margin-bottom:0; }
    .art-index table.table { border:1px solid var(--line); }
    .art-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }
    .art-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .art-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .art-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .art-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .art-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .art-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .art-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .art-index .filters .btn-reset:hover { color:var(--ink); }
    .art-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    .art-index table.table > tbody > tr { transition:background .12s ease; }
    .art-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .art-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; font-size:13px; }
    .art-index .num { font-variant-numeric:tabular-nums; }
    .art-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .art-index .judul-link:hover { color:var(--accent); }
    .art-index .muted { color:var(--muted); font-size:12.5px; }
    .art-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; background:#eef1f4; color:#3a4149; }

    .art-index .act { display:flex; justify-content:center; gap:0; }
    .art-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .art-index .act .btn:hover { color:var(--ink); }
    .art-index .act .btn.danger:hover { color:var(--accent); }

    .art-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .art-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .art-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .art-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row art-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $title }}</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.artikel.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <form id="artFilter" method="GET" action="{{ route('backend.artikel.index') }}"></form>

                @if($data->total() > 0)
                <div class="art-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> artikel</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.artikel.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="art-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="4%" class="text-center">No</th>
                            <th width="18%">Jenis Artikel</th>
                            <th>Judul Artikel</th>
                            <th width="8%" class="text-center">Tahun</th>
                            <th width="18%">Sumber</th>
                            <th width="11%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <select form="artFilter" name="jenis_peraturan" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    @foreach($selectJenisArtikel as $j)
                                        <option value="{{ $j }}" {{ request('jenis_peraturan') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th><input form="artFilter" type="text" name="judul" class="form-control" placeholder="Cari judul…" value="{{ request('judul') }}"></th>
                            <th><input form="artFilter" type="text" name="tahun_terbit" class="form-control" placeholder="Tahun…" value="{{ request('tahun_terbit') }}"></th>
                            <th><input form="artFilter" type="text" name="sumber" class="form-control" placeholder="Sumber…" value="{{ request('sumber') }}"></th>
                            <th>
                                <div class="filter-act">
                                    <button form="artFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari"><i class="fa fa-search"></i></button>
                                    <a href="{{ route('backend.artikel.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset"><i class="fa fa-refresh"></i></a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center num">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td>@if($item->jenis_peraturan)<span class="tag">{{ $item->jenis_peraturan }}</span>@else<span class="muted">—</span>@endif</td>
                            <td><a href="{{ route('backend.artikel.show', $item->id) }}" class="judul-link">{{ \Illuminate\Support\Str::limit($item->judul, 90) }}</a></td>
                            <td class="text-center num">{{ $item->tahun_terbit ?: '—' }}</td>
                            <td class="muted">{{ \Illuminate\Support\Str::limit($item->sumber, 30) ?: '—' }}</td>
                            <td class="text-center act">
                                <a href="{{ route('backend.artikel.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail"><i class="fa fa-eye"></i></a>
                                <a href="{{ route('backend.artikel.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit"><i class="fa fa-pencil"></i></a>
                                <form action="{{ route('backend.artikel.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus" onclick="return confirm('Yakin akan menghapus data ini?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="art-empty">
                                    <i class="fa fa-newspaper-o"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada artikel yang cocok dengan filter.<br><a href="{{ route('backend.artikel.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada artikel</h4>
                                        <p>Mulai dengan menambahkan artikel pertama.</p>
                                        <a href="{{ route('backend.artikel.create') }}" class="btn btn-danger btn-sm"><i class="fa fa-plus"></i> Tambah data</a>
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
