<x-layouts.backend title="Survei Kepuasan" :listNav="[['label' => 'Survei Kepuasan']]">

@php
    $jenisOptions = ['Mahasiswa', 'Akademisi', 'Praktisi Hukum', 'Masyarakat Umum', 'Lainnya'];
@endphp

@push('link')
<style>
    .srv-index { --ink:#1f2328; --muted:#6b7075; --accent:#c0392b; --line:#eceae5; }
    .srv-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .srv-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    /* Kartu statistik */
    .srv-stats { display:flex; flex-wrap:wrap; gap:12px; margin-bottom:16px; }
    .srv-stat { flex:1 1 150px; border:1px solid var(--line); border-radius:8px; padding:12px 14px; background:#faf9f7; }
    .srv-stat .lbl { font-size:11px; text-transform:uppercase; letter-spacing:.03em; color:var(--muted); font-weight:600; }
    .srv-stat .val { font-size:22px; font-weight:700; color:var(--ink); font-variant-numeric:tabular-nums; margin-top:2px; }
    .srv-stat .val small { font-size:12px; color:var(--muted); font-weight:500; }
    .srv-stat.hl { background:#fdf3f1; border-color:#f3d6d1; }

    /* Meta bar */
    .srv-index .srv-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .srv-index .srv-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .srv-index .srv-meta .reset { color:var(--accent); font-size:12px; }

    /* Tabel */
    .srv-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .srv-tablewrap table.table { min-width:760px; margin-bottom:0; }
    .srv-index table.table { border:1px solid var(--line); }
    .srv-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }
    .srv-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .srv-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .srv-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .srv-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .srv-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .srv-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .srv-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .srv-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    .srv-index table.table > tbody > tr { transition:background .12s ease; }
    .srv-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .srv-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:11px 12px; }
    .srv-index .num { font-variant-numeric:tabular-nums; }
    .srv-index .cell-date { color:var(--muted); font-size:12.5px; white-space:nowrap; }
    .srv-index .nama-link { color:var(--ink); font-weight:500; }
    .srv-index .nama-link:hover { color:var(--accent); }
    .srv-index .tag { display:inline-block; font-size:11.5px; padding:2px 9px; border-radius:20px; background:#f2efe9; color:#5a4b3f; }
    .srv-index .rating { font-weight:600; color:var(--ink); }
    .srv-index .rating i { color:#e0a800; }

    .srv-index .act { display:flex; justify-content:center; gap:0; }
    .srv-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .srv-index .act .btn:hover { color:var(--ink); }
    .srv-index .act .btn.danger:hover { color:var(--accent); }

    .srv-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .srv-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .srv-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .srv-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row srv-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Survei Kepuasan</h3>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                {{-- Kartu statistik --}}
                <div class="srv-stats">
                    <div class="srv-stat hl">
                        <div class="lbl">Total Responden</div>
                        <div class="val">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="srv-stat hl">
                        <div class="lbl">Rata-rata Keseluruhan</div>
                        <div class="val">{{ $stats['rata_total'] }} <small>/ 5</small></div>
                    </div>
                    @foreach($stats['rata_aspek'] as $label => $nilai)
                    <div class="srv-stat">
                        <div class="lbl">{{ $label }}</div>
                        <div class="val">{{ $nilai }} <small>/ 5</small></div>
                    </div>
                    @endforeach
                </div>

                <form id="srvFilter" method="GET" action="{{ route('backend.survei.index') }}"></form>

                @if($data->total() > 0)
                <div class="srv-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> jawaban</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.survei.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="srv-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="4%" class="text-center">No</th>
                            <th width="15%">Tanggal</th>
                            <th>Nama</th>
                            <th width="18%">Jenis Pengguna</th>
                            <th width="12%" class="text-center">Rata Nilai</th>
                            <th width="11%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th></th>
                            <th>
                                <input form="srvFilter" type="text" name="nama" class="form-control"
                                       placeholder="Cari nama…" value="{{ request('nama') }}">
                            </th>
                            <th>
                                <select form="srvFilter" name="jenis_pengguna" class="form-control">
                                    <option value="">Semua</option>
                                    @foreach($jenisOptions as $opt)
                                        <option value="{{ $opt }}" @selected(request('jenis_pengguna') === $opt)>{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th></th>
                            <th>
                                <div class="filter-act">
                                    <button form="srvFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ route('backend.survei.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        @php
                            $rata = round(($item->kemudahan_akses + $item->kelengkapan_informasi + $item->kecepatan_loading
                                + $item->tampilan_antarmuka + $item->relevansi_pencarian) / 5, 1);
                        @endphp
                        <tr>
                            <td class="text-center num">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td class="cell-date">{{ $item->created_at?->translatedFormat('d M Y, H:i') }}</td>
                            <td>
                                <a href="{{ route('backend.survei.show', $item->id) }}" class="nama-link">
                                    {{ \Illuminate\Support\Str::limit($item->nama, 40) }}
                                </a>
                            </td>
                            <td><span class="tag">{{ $item->jenis_pengguna }}</span></td>
                            <td class="text-center rating"><i class="fa fa-star"></i> {{ $rata }}</td>
                            <td class="text-center act">
                                <a href="{{ route('backend.survei.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <form action="{{ route('backend.survei.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus"
                                            onclick="return confirm('Yakin akan menghapus jawaban ini?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="srv-empty">
                                    <i class="fa fa-star-o"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada jawaban yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.survei.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada jawaban survei</h4>
                                        <p>Jawaban akan muncul di sini saat pengunjung mengisi survei kepuasan.</p>
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
