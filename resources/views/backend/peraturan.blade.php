<x-layouts.backend title="Peraturan" :listNav="[['label' => 'Peraturan']]">
	@php
		$selectBentukPeraturan = DB::table('document')
		    ->where('tipe_dokumen', 1)
		    ->select('bentuk_peraturan')
		    ->distinct()
		    ->pluck('bentuk_peraturan');

		$selectKeteranganStatus = DB::table('status')->whereIn('id', [2, 4, 6, 7])->pluck('status');

		$hasFilter = request()->hasAny(['bentuk_peraturan', 'nomor_peraturan', 'tahun_terbit', 'judul', 'status', 'status_peraturan']);
	@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-index / .ih-index) ── */
    .per-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .per-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .per-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    .per-index .per-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .per-index .per-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .per-index .per-meta .reset { color:var(--accent); font-size:12px; }

    .per-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .per-tablewrap table.table { min-width:1020px; margin-bottom:0; }
    .per-index table.table { border:1px solid var(--line); }
    .per-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }

    .per-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .per-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .per-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .per-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .per-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .per-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .per-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .per-index .filters .btn-reset:hover { color:var(--ink); }
    .per-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    .per-index table.table > tbody > tr { transition:background .12s ease; }
    .per-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .per-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; }
    .per-index .num { font-variant-numeric:tabular-nums; }
    .per-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .per-index .judul-link:hover { color:var(--accent); }
    .per-index .cell-nomor { font-variant-numeric:tabular-nums; color:var(--muted); font-size:12.5px; white-space:nowrap; }

    .per-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .per-index .tag-bentuk { background:#eef1f4; color:#3a4149; }
    .per-index .tag-on { background:#e5f3e9; color:#227a3b; }
    .per-index .tag-off { background:#fdecea; color:#c0392b; }
    .per-index .tag-ket { background:#fdf2e0; color:#9a6a13; }
    .per-index .tag-neutral { background:#f2efe9; color:#77706a; }

    .per-index .act { display:flex; justify-content:center; gap:0; }
    .per-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .per-index .act .btn:hover { color:var(--ink); }
    .per-index .act .btn.danger:hover { color:var(--accent); }

    .per-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .per-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .per-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .per-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row per-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Peraturan</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.peraturan.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <form id="perFilter" method="GET" action="{{ route('backend.peraturan.index') }}"></form>

                @if($data->total() > 0)
                <div class="per-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> peraturan</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.peraturan.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="per-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="3%" class="text-center">No</th>
                            <th width="15%">Jenis Peraturan</th>
                            <th width="10%">Nomor</th>
                            <th width="6%" class="text-center">Tahun</th>
                            <th>Judul Peraturan</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="12%" class="text-center">Keterangan</th>
                            <th width="9%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <select form="perFilter" name="bentuk_peraturan" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    @foreach($selectBentukPeraturan as $b)
                                        <option value="{{ $b }}" {{ request('bentuk_peraturan') == $b ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th>
                                <input form="perFilter" type="text" name="nomor_peraturan" class="form-control"
                                       placeholder="Nomor…" value="{{ request('nomor_peraturan') }}">
                            </th>
                            <th>
                                <input form="perFilter" type="text" name="tahun_terbit" class="form-control"
                                       placeholder="Tahun…" value="{{ request('tahun_terbit') }}">
                            </th>
                            <th>
                                <input form="perFilter" type="text" name="judul" class="form-control"
                                       placeholder="Cari judul…" value="{{ request('judul') }}">
                            </th>
                            <th>
                                <select form="perFilter" name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    <option value="Berlaku" {{ request('status') == 'Berlaku' ? 'selected' : '' }}>Berlaku</option>
                                    <option value="Tidak Berlaku" {{ request('status') == 'Tidak Berlaku' ? 'selected' : '' }}>Tidak Berlaku</option>
                                </select>
                            </th>
                            <th>
                                <select form="perFilter" name="status_peraturan" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua</option>
                                    @foreach($selectKeteranganStatus as $s)
                                        <option value="{{ $s }}" {{ request('status_peraturan') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th>
                                <div class="filter-act">
                                    <button form="perFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ route('backend.peraturan.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset">
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
                            <td><span class="tag tag-bentuk">{{ $item->bentuk_peraturan ?: '—' }}</span></td>
                            <td class="cell-nomor">{{ $item->nomor_peraturan ?: '—' }}</td>
                            <td class="text-center num">{{ $item->tahun_terbit ?: '—' }}</td>
                            <td>
                                <a href="{{ route('backend.peraturan.show', $item->id) }}" class="judul-link">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 90) }}
                                </a>
                            </td>
                            <td class="text-center">
                                @if($item->status === 'Berlaku')
                                    <span class="tag tag-on">Berlaku</span>
                                @elseif($item->status === 'Tidak Berlaku')
                                    <span class="tag tag-off">Tidak Berlaku</span>
                                @else
                                    <span class="tag tag-neutral">{{ $item->status ?: '—' }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status_peraturan)
                                    <span class="tag tag-ket">{{ ucfirst($item->status_peraturan) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center act">
                                <a href="{{ route('backend.peraturan.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.peraturan.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('backend.peraturan.destroy', $item->id) }}" method="POST" style="display:inline;">
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
                            <td colspan="8">
                                <div class="per-empty">
                                    <i class="fa fa-gavel"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada peraturan yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.peraturan.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada peraturan</h4>
                                        <p>Mulai dengan menambahkan peraturan pertama.</p>
                                        <a href="{{ route('backend.peraturan.create') }}" class="btn btn-danger btn-sm">
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
