@php
    $title = 'Data Pembentukan PUU';

    $jenisLabels = [
        'naskah_akademik' => 'Naskah Akademik',
        'naskah_keterangan_penjelasan' => 'Naskah Keterangan dan/atau Penjelasan',
        'rancangan_puu' => 'Rancangan PUU',
        'penelitian_hukum' => 'Penelitian Hukum',
        'pengkajian_hukum' => 'Pengkajian Hukum',
        'pengkajian_konstitusi' => 'Pengkajian Konstitusi',
        'analisis_evaluasi' => 'Analisis Evaluasi',
    ];

    $hasFilter = request()->hasAny(['search', 'jenis_dokumen', 'status_publikasi', 'hak_akses', 'tahun']);
@endphp

<x-layouts.backend :title="$title" :listNav="[['label' => 'Pembentukan PUU']]">

@push('link')
<style>
    /* ── Scoped ke halaman ini saja ── */
    .puu-index { --ink: #1f2328; --muted: #6b7075; --accent: #c0392b; }
    .puu-index .box { border-top: 3px solid var(--accent); box-shadow: 0 1px 2px rgba(31,35,40,.06); }
    .puu-index .box-title { font-weight: 600; letter-spacing: -.01em; color: var(--ink); }

    /* Toolbar filter */
    .puu-toolbar { background: #faf9f7; border: 1px solid #eceae5; border-radius: 6px; padding: 16px; margin-bottom: 18px; }
    .puu-toolbar .form-control { box-shadow: none; border-color: #dfddd7; }
    .puu-toolbar .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(192,57,43,.08); }
    .puu-toolbar label.field { display:block; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: 5px; font-weight: 600; }
    .puu-toolbar .row + .row { margin-top: 12px; }

    /* Chip filter aktif */
    .puu-chips { display:flex; flex-wrap:wrap; gap:6px; align-items:center; margin-top:14px; }
    .puu-chips .chip { display:inline-flex; align-items:center; gap:5px; background:#fff; border:1px solid #e3e0da; color:var(--ink); font-size:12px; padding:4px 10px; border-radius:999px; }
    .puu-chips .chip b { font-weight:600; }
    .puu-chips .chip .k { color: var(--muted); }

    /* Meta bar */
    .puu-meta { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:10px; color:var(--muted); font-size:13px; }

    /* Tabel */
    .puu-tablewrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .puu-tablewrap table.table { min-width: 860px; margin-bottom: 0; }
    .puu-index table.table { border: 1px solid #eceae5; }
    .puu-index table.table > thead > tr > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle;
    }
    .puu-index table.table > tbody > tr { transition: background .12s ease; }
    .puu-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .puu-index table.table > tbody > tr > td { vertical-align: middle; border-color:#f0eee9; }
    .puu-index .num { font-variant-numeric: tabular-nums; }
    .puu-index .judul-link { color: var(--ink); font-weight:500; }
    .puu-index .judul-link:hover { color: var(--accent); }
    .puu-index .dl-link { color:#27893f; font-size:12px; }

    /* Badge halus */
    .puu-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .puu-index .tag-type { background:#eef1f4; color:#3a4149; }
    .puu-index .tag-published { background:#e5f3e9; color:#227a3b; }
    .puu-index .tag-draft { background:#fdf2e0; color:#9a6a13; }
    .puu-index .tag-archived { background:#eceff1; color:#5a636b; }
    .puu-index .tag-public { background:#e8eff7; color:#2f5c93; }
    .puu-index .tag-private { background:#f2efe9; color:#77706a; }

    /* Aksi */
    .puu-index .act { display:flex; justify-content:center; gap:0px; }
    .puu-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; }
    .puu-index .act .btn:hover { color:var(--ink); }
    .puu-index .act .btn.danger:hover { color:var(--accent); }

    /* Empty state */
    .puu-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .puu-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .puu-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }
</style>
@endpush

<div class="row puu-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $title }}</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.pembentukan-puu.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                {{-- ── Toolbar pencarian & filter ── --}}
                <form method="GET" action="{{ route('backend.pembentukan-puu.index') }}" class="puu-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="field" for="search">Cari data</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control"
                                       placeholder="Judul, nomor, atau lembaga pemrakarsa…"
                                       value="{{ request('search') }}">
                                <span class="input-group-btn">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="field" for="jenis_dokumen">Jenis dokumen</label>
                            <select name="jenis_dokumen" id="jenis_dokumen" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua jenis</option>
                                @foreach($jenisDokumenList as $key => $label)
                                    <option value="{{ $key }}" {{ request('jenis_dokumen') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="field" for="tahun">Tahun</label>
                            <input type="number" name="tahun" id="tahun" class="form-control no-spinner"
                                   placeholder="cth. {{ date('Y') }}" value="{{ request('tahun') }}"
                                   min="1900" max="{{ date('Y') }}" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="field" for="status_publikasi">Status publikasi</label>
                            <select name="status_publikasi" id="status_publikasi" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua status</option>
                                <option value="draft" {{ request('status_publikasi') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status_publikasi') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ request('status_publikasi') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="field" for="hak_akses">Hak akses</label>
                            <select name="hak_akses" id="hak_akses" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua akses</option>
                                <option value="public" {{ request('hak_akses') == 'public' ? 'selected' : '' }}>Public</option>
                                <option value="private" {{ request('hak_akses') == 'private' ? 'selected' : '' }}>Private</option>
                            </select>
                        </div>
                    </div>

                    @if($hasFilter)
                    <div class="puu-chips">
                        @if(request('search'))
                            <span class="chip"><span class="k">Kata kunci</span> <b>{{ request('search') }}</b></span>
                        @endif
                        @if(request('jenis_dokumen') && isset($jenisDokumenList[request('jenis_dokumen')]))
                            <span class="chip"><span class="k">Jenis</span> <b>{{ $jenisDokumenList[request('jenis_dokumen')] }}</b></span>
                        @endif
                        @if(request('status_publikasi'))
                            <span class="chip"><span class="k">Status</span> <b>{{ ucfirst(request('status_publikasi')) }}</b></span>
                        @endif
                        @if(request('hak_akses'))
                            <span class="chip"><span class="k">Akses</span> <b>{{ ucfirst(request('hak_akses')) }}</b></span>
                        @endif
                        @if(request('tahun'))
                            <span class="chip"><span class="k">Tahun</span> <b>{{ request('tahun') }}</b></span>
                        @endif
                        <a href="{{ route('backend.pembentukan-puu.index') }}" class="chip" style="color:var(--accent)">
                            <i class="fa fa-times"></i> Reset filter
                        </a>
                    </div>
                    @endif
                </form>

                {{-- ── Meta ── --}}
                @if($puu->count() > 0)
                <div class="puu-meta">
                    <span>Menampilkan <span class="num">{{ $puu->firstItem() }}</span>–<span class="num">{{ $puu->lastItem() }}</span> dari <span class="num">{{ $puu->total() }}</span> data</span>
                </div>
                @endif

                {{-- ── Tabel ── --}}
                <div class="puu-tablewrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            <th>Jenis dokumen</th>
                            <th width="32%">Judul</th>
                            <th width="10%">Nomor</th>
                            <th width="7%" class="text-center">Tahun</th>
                            <th width="15%">Lembaga pemrakarsa</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="7%" class="text-center">Akses</th>
                            <th width="10%" class="text-center">Diunggah</th>
                            <th width="9%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($puu as $index => $item)
                        <tr>
                            <td class="text-center num">{{ ($puu->currentPage() - 1) * $puu->perPage() + $index + 1 }}</td>
                            <td>
                                <span class="tag tag-type">{{ $jenisLabels[$item->jenis_dokumen] ?? $item->jenis_dokumen }}</span>
                            </td>
                            <td>
                                <a href="{{ route('backend.pembentukan-puu.show', $item->id) }}" class="judul-link">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 70) }}
                                </a>
                                @if($item->dokumen_utama)
                                <br>
                                <a href="{{ route('backend.pembentukan-puu.download', ['id' => $item->id, 'type' => 'dokumen']) }}"
                                   class="dl-link" title="Unduh dokumen">
                                    <i class="fa fa-download"></i> Unduh dokumen
                                </a>
                                @endif
                            </td>
                            <td class="num">{{ $item->nomor_dokumen ?? '—' }}</td>
                            <td class="text-center num">{{ $item->tahun }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($item->lembaga_pemrakarsa, 25) }}</td>
                            <td class="text-center">
                                @if($item->status_publikasi == 'published')
                                    <span class="tag tag-published">Published</span>
                                @elseif($item->status_publikasi == 'draft')
                                    <span class="tag tag-draft">Draft</span>
                                @elseif($item->status_publikasi == 'archived')
                                    <span class="tag tag-archived">Arsip</span>
                                @else
                                    <span class="tag tag-archived">{{ $item->status_publikasi }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->hak_akses == 'public')
                                    <span class="tag tag-public">Public</span>
                                @else
                                    <span class="tag tag-private">{{ $item->hak_akses == 'private' ? 'Private' : $item->hak_akses }}</span>
                                @endif
                            </td>
                            <td class="text-center num">
                                {{ $item->tanggal_unggah ? \Carbon\Carbon::parse($item->tanggal_unggah)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="text-center act">
                                <a href="{{ route('backend.pembentukan-puu.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.pembentukan-puu.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('backend.pembentukan-puu.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                <div class="puu-empty">
                                    <i class="fa fa-inbox"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada data yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.pembentukan-puu.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada data</h4>
                                        <p>Mulai dengan menambahkan data Pembentukan PUU pertama.</p>
                                        <a href="{{ route('backend.pembentukan-puu.create') }}" class="btn btn-danger btn-sm">
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

            @if($puu->hasPages())
            <div class="box-footer">
                <div class="pull-right">
                    {{ $puu->appends(request()->query())->links() }}
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.backend>
