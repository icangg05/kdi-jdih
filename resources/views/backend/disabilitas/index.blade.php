@php
    $title = 'Data Disabilitas';
    $hasFilter = request()->hasAny(['search', 'status', 'hak_akses', 'tahun', 'jenis_dokumen']);
@endphp

<x-layouts.backend :title="$title" :listNav="[['label' => 'Disabilitas']]">

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (dis-index) ── */
    .dis-index { --ink: #1f2328; --muted: #6b7075; --accent: #c0392b; }
    .dis-index .box { border-top: 3px solid var(--accent); box-shadow: 0 1px 2px rgba(31,35,40,.06); }
    .dis-index .box-title { font-weight: 600; letter-spacing: -.01em; color: var(--ink); }

    /* Toolbar filter */
    .dis-toolbar { background: #faf9f7; border: 1px solid #eceae5; border-radius: 6px; padding: 16px; margin-bottom: 18px; }
    .dis-toolbar .form-control { box-shadow: none; border-color: #dfddd7; }
    .dis-toolbar .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(192,57,43,.08); }
    .dis-toolbar label.field { display:block; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; color: var(--muted); margin-bottom: 5px; font-weight: 600; }
    .dis-toolbar .row + .row { margin-top: 12px; }

    /* Chip filter aktif */
    .dis-chips { display:flex; flex-wrap:wrap; gap:6px; align-items:center; margin-top:14px; }
    .dis-chips .chip { display:inline-flex; align-items:center; gap:5px; background:#fff; border:1px solid #e3e0da; color:var(--ink); font-size:12px; padding:4px 10px; border-radius:999px; }
    .dis-chips .chip b { font-weight:600; }
    .dis-chips .chip .k { color: var(--muted); }

    /* Meta bar */
    .dis-meta { display:flex; justify-content:space-between; align-items:baseline; margin-bottom:10px; color:var(--muted); font-size:13px; }

    /* Tabel */
    .dis-tablewrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .dis-tablewrap table.table { min-width: 900px; margin-bottom: 0; }
    .dis-index table.table { border: 1px solid #eceae5; }
    .dis-index table.table > thead > tr > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle;
    }
    .dis-index table.table > tbody > tr { transition: background .12s ease; }
    .dis-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .dis-index table.table > tbody > tr > td { vertical-align: middle; border-color:#f0eee9; }
    .dis-index .num { font-variant-numeric: tabular-nums; }
    .dis-index .judul-link { color: var(--ink); font-weight:500; }
    .dis-index .judul-link:hover { color: var(--accent); }

    /* Badge halus */
    .dis-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .dis-index .tag-type { background:#eef1f4; color:#3a4149; }
    .dis-index .tag-published { background:#e5f3e9; color:#227a3b; }
    .dis-index .tag-draft { background:#fdf2e0; color:#9a6a13; }
    .dis-index .tag-archived { background:#eceff1; color:#5a636b; }
    .dis-index .tag-public { background:#e8eff7; color:#2f5c93; }
    .dis-index .tag-private { background:#f2efe9; color:#77706a; }

    /* Aksi */
    .dis-index .act { display:flex; justify-content:center; gap:0px; }
    .dis-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; }
    .dis-index .act .btn:hover { color:var(--ink); }
    .dis-index .act .btn.danger:hover { color:var(--accent); }

    /* Empty state */
    .dis-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .dis-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .dis-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }
</style>
@endpush

<div class="row dis-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $title }}</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.disabilitas.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                {{-- ── Toolbar pencarian & filter ── --}}
                <form method="GET" action="{{ route('backend.disabilitas.index') }}" class="dis-toolbar">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="field" for="search">Cari data</label>
                            <div class="input-group">
                                <input type="text" name="search" id="search" class="form-control"
                                       placeholder="Judul, nomor, atau lembaga penetap…"
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
                                @foreach($jenisDokumenList as $jenis)
                                    <option value="{{ $jenis }}" {{ request('jenis_dokumen') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="field" for="tahun">Tahun</label>
                            <input type="number" name="tahun" id="tahun" class="form-control"
                                   placeholder="cth. {{ date('Y') }}" value="{{ request('tahun') }}"
                                   min="1900" max="{{ date('Y') }}" onchange="this.form.submit()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="field" for="status">Status publikasi</label>
                            <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Arsip</option>
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
                    <div class="dis-chips">
                        @if(request('search'))
                            <span class="chip"><span class="k">Kata kunci</span> <b>{{ request('search') }}</b></span>
                        @endif
                        @if(request('jenis_dokumen'))
                            <span class="chip"><span class="k">Jenis</span> <b>{{ request('jenis_dokumen') }}</b></span>
                        @endif
                        @if(request('status'))
                            <span class="chip"><span class="k">Status</span> <b>{{ ucfirst(request('status')) }}</b></span>
                        @endif
                        @if(request('hak_akses'))
                            <span class="chip"><span class="k">Akses</span> <b>{{ ucfirst(request('hak_akses')) }}</b></span>
                        @endif
                        @if(request('tahun'))
                            <span class="chip"><span class="k">Tahun</span> <b>{{ request('tahun') }}</b></span>
                        @endif
                        <a href="{{ route('backend.disabilitas.index') }}" class="chip" style="color:var(--accent)">
                            <i class="fa fa-times"></i> Reset filter
                        </a>
                    </div>
                    @endif
                </form>

                {{-- ── Meta ── --}}
                @if($disabilitas->count() > 0)
                <div class="dis-meta">
                    <span>Menampilkan <span class="num">{{ $disabilitas->firstItem() }}</span>–<span class="num">{{ $disabilitas->lastItem() }}</span> dari <span class="num">{{ $disabilitas->total() }}</span> data</span>
                </div>
                @endif

                {{-- ── Tabel ── --}}
                <div class="dis-tablewrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="4%" class="text-center">No</th>
                            <th width="14%">Jenis dokumen</th>
                            <th width="32%">Judul</th>
                            <th width="11%">Nomor</th>
                            <th width="7%" class="text-center">Tahun</th>
                            <th width="14%">Lembaga penetap</th>
                            <th width="9%" class="text-center">Status</th>
                            <th width="7%" class="text-center">Akses</th>
                            <th width="10%" class="text-center">Diunggah</th>
                            <th width="9%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disabilitas as $index => $item)
                        <tr>
                            <td class="text-center num">{{ ($disabilitas->currentPage() - 1) * $disabilitas->perPage() + $index + 1 }}</td>
                            <td>
                                <span class="tag tag-type">{{ $item->jenis_dokumen }}</span>
                            </td>
                            <td>
                                <a href="{{ route('backend.disabilitas.show', $item->id) }}" class="judul-link">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 70) }}
                                </a>
                            </td>
                            <td class="num">{{ $item->nomor_dokumen ?? '—' }}</td>
                            <td class="text-center num">{{ $item->tahun }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($item->lembaga_penetap, 25) }}</td>
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
                                <a href="{{ route('backend.disabilitas.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('backend.disabilitas.edit', $item->id) }}" class="btn btn-sm" title="Edit" aria-label="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('backend.disabilitas.destroy', $item->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus"
                                            onclick="return confirm('Yakin ingin menghapus dokumen ini?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">
                                <div class="dis-empty">
                                    <i class="fa fa-inbox"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada data yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.disabilitas.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada data</h4>
                                        <p>Mulai dengan menambahkan data Disabilitas pertama.</p>
                                        <a href="{{ route('backend.disabilitas.create') }}" class="btn btn-danger btn-sm">
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

            @if($disabilitas->hasPages())
            <div class="box-footer">
                <div class="pull-right">
                    {{ $disabilitas->appends(request()->query())->links() }}
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.backend>
