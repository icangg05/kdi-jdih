<x-layouts.backend title="Informasi Hukum" :listNav="[['label' => 'Informasi Hukum']]">

@php
    $hasFilter = request()->hasAny(['judul', 'tanggal', 'isi']);
    $jenisMap = DB::table('jenis_informasi_hukum')->pluck('singkatan', 'id');
@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .peng-index / .ber-index) ── */
    .ih-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .ih-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .ih-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    .ih-index .ih-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .ih-index .ih-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .ih-index .ih-meta .reset { color:var(--accent); font-size:12px; }

    .ih-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .ih-tablewrap table.table { min-width:840px; margin-bottom:0; }
    .ih-index table.table { border:1px solid var(--line); }
    .ih-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }

    .ih-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .ih-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .ih-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .ih-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .ih-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .ih-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .ih-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .ih-index .filters .btn-reset:hover { color:var(--ink); }
    .ih-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    .ih-index table.table > tbody > tr { transition:background .12s ease; }
    .ih-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .ih-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; }
    .ih-index .num { font-variant-numeric:tabular-nums; }
    .ih-index .cell-date { color:var(--muted); font-size:12.5px; white-space:nowrap; }
    .ih-index .judul-link { color:var(--ink); font-weight:500; line-height:1.4; }
    .ih-index .judul-link:hover { color:var(--accent); }
    .ih-index .excerpt { color:var(--soft); font-size:12.5px; line-height:1.55; }

    .ih-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .ih-index .tag-jenis { background:#eef1f4; color:#3a4149; }
    .ih-index .tag-published { background:#e5f3e9; color:#227a3b; }
    .ih-index .tag-draft { background:#f2efe9; color:#77706a; }

    .ih-index .act { display:flex; justify-content:center; gap:0; }
    .ih-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .ih-index .act .btn:hover { color:var(--ink); }
    .ih-index .act .btn.danger:hover { color:var(--accent); }

    .ih-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .ih-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .ih-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .ih-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row ih-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Informasi Hukum</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.informasi-hukum.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <form id="ihFilter" method="GET" action="{{ route('backend.informasi-hukum.index') }}"></form>

                @if($data->total() > 0)
                <div class="ih-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> data</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.informasi-hukum.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="ih-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="4%" class="text-center">No</th>
                            <th width="13%">Tanggal</th>
                            <th width="34%">Judul</th>
                            <th>Isi</th>
                            <th width="9%" class="text-center">Status</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <input form="ihFilter" type="text" name="tanggal" class="form-control"
                                       placeholder="cth. Juli 2026" value="{{ request('tanggal') }}">
                            </th>
                            <th>
                                <input form="ihFilter" type="text" name="judul" class="form-control"
                                       placeholder="Cari judul…" value="{{ request('judul') }}">
                            </th>
                            <th>
                                <input form="ihFilter" type="text" name="isi" class="form-control"
                                       placeholder="Cari isi…" value="{{ request('isi') }}">
                            </th>
                            <th></th>
                            <th>
                                <div class="filter-act">
                                    <button form="ihFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ route('backend.informasi-hukum.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset">
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
                                <a href="{{ route('backend.informasi-hukum.show', $item->id) }}" class="judul-link">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 70) }}
                                </a>
                                @if(!empty($jenisMap[$item->jenis]))
                                    <div style="margin-top:4px;"><span class="tag tag-jenis">{{ $jenisMap[$item->jenis] }}</span></div>
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
                                <a href="{{ route('backend.informasi-hukum.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.informasi-hukum.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('backend.informasi-hukum.destroy', $item->id) }}" method="POST" style="display:inline;">
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
                                <div class="ih-empty">
                                    <i class="fa fa-gavel"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada data yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.informasi-hukum.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada data</h4>
                                        <p>Mulai dengan menambahkan informasi hukum pertama.</p>
                                        <a href="{{ route('backend.informasi-hukum.create') }}" class="btn btn-danger btn-sm">
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
