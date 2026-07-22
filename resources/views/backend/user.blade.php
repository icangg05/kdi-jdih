<x-layouts.backend title="User" :listNav="[['label' => 'User']]">

@php
    $hasFilter = request()->hasAny(['username', 'email']);
    $authId = auth()->id();
@endphp

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (selaras .usr-show / .peng-index) ── */
    .usr-index { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .usr-index .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .usr-index .box-title { font-weight:600; letter-spacing:-.01em; color:var(--ink); }

    /* Meta bar */
    .usr-index .usr-meta { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; color:var(--muted); font-size:13px; gap:12px; flex-wrap:wrap; }
    .usr-index .usr-meta .num { font-variant-numeric:tabular-nums; color:var(--ink); font-weight:600; }
    .usr-index .usr-meta .reset { color:var(--accent); font-size:12px; }

    /* Tabel */
    .usr-tablewrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .usr-tablewrap table.table { min-width:680px; margin-bottom:0; }
    .usr-index table.table { border:1px solid var(--line); }
    .usr-index table.table > thead > tr.head > th {
        background:#f7f5f2; border-bottom:1px solid #e6e3dd !important; color:var(--muted);
        font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; vertical-align:middle; padding:11px 12px;
    }

    /* Baris filter per-kolom */
    .usr-index table.table > thead > tr.filters > th { background:#faf9f7; border-bottom:1px solid #e6e3dd !important; padding:8px 10px; vertical-align:middle; }
    .usr-index .filters .form-control { height:32px; font-size:12.5px; box-shadow:none; border-color:#dcd9d3; border-radius:5px; padding:4px 9px; }
    .usr-index .filters .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.08); }
    .usr-index .filters .btn { border-radius:5px; padding:5px 9px; }
    .usr-index .filters .btn-search { background:var(--accent); border-color:var(--accent); color:#fff; }
    .usr-index .filters .btn-search:hover { background:#a5271b; border-color:#a5271b; }
    .usr-index .filters .btn-reset { background:#fff; border:1px solid #dcd9d3; color:var(--muted); }
    .usr-index .filters .btn-reset:hover { color:var(--ink); }
    .usr-index .filters .filter-act { display:flex; gap:5px; justify-content:center; }

    /* Body rows */
    .usr-index table.table > tbody > tr { transition:background .12s ease; }
    .usr-index table.table > tbody > tr:hover { background:#faf9f7 !important; }
    .usr-index table.table > tbody > tr > td { vertical-align:middle; border-color:#f0eee9; padding:10px 12px; }
    .usr-index .num { font-variant-numeric:tabular-nums; }

    /* User cell */
    .usr-index .user-cell { display:flex; align-items:center; gap:11px; }
    .usr-index .avatar { width:36px; height:36px; border-radius:50%; object-fit:cover; aspect-ratio:1/1; flex:none; border:1px solid var(--line); background:#f2efe9; }
    .usr-index .uname { color:var(--ink); font-weight:600; line-height:1.3; }
    .usr-index .uname:hover { color:var(--accent); }
    .usr-index .uself { font-size:10px; font-weight:600; color:var(--soft); text-transform:uppercase; letter-spacing:.04em; }
    .usr-index .email-cell { color:var(--muted); font-size:13px; }

    /* Badge status */
    .usr-index .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:999px; line-height:1.4; }
    .usr-index .tag-on { background:#e5f3e9; color:#227a3b; }
    .usr-index .tag-off { background:#fdecea; color:#c0392b; }

    /* Aksi */
    .usr-index .act { display:flex; justify-content:center; gap:0; align-items:center; }
    .usr-index .act .btn { border:none; background:transparent; color:var(--muted); padding:5px 7px; box-shadow:none; }
    .usr-index .act .btn:hover { color:var(--ink); }
    .usr-index .act .btn.on:hover { color:#227a3b; }
    .usr-index .act .btn.off:hover { color:#c77d0a; }
    .usr-index .act .btn.danger:hover { color:var(--accent); }

    /* Empty state */
    .usr-empty { text-align:center; padding:56px 20px; color:var(--muted); }
    .usr-empty i { font-size:38px; color:#cfcbc3; display:block; margin-bottom:12px; }
    .usr-empty h4 { color:var(--ink); font-weight:600; margin:0 0 4px; }

    .usr-index .box-footer .pagination { margin:0; }
</style>
@endpush

<div class="row usr-index">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">User</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.user.create') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Tambah data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <form id="usrFilter" method="GET" action="{{ route('backend.user.index') }}"></form>

                @if($data->total() > 0)
                <div class="usr-meta">
                    <span>Menampilkan <span class="num">{{ $data->firstItem() }}</span>–<span class="num">{{ $data->lastItem() }}</span> dari <span class="num">{{ $data->total() }}</span> user</span>
                    @if($hasFilter)
                        <a href="{{ route('backend.user.index') }}" class="reset"><i class="fa fa-times"></i> Reset filter</a>
                    @endif
                </div>
                @endif

                <div class="usr-tablewrap">
                <table class="table">
                    <thead>
                        <tr class="head">
                            <th width="4%" class="text-center">No</th>
                            <th width="30%">Username</th>
                            <th>Email</th>
                            <th width="12%" class="text-center">Status</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                        <tr class="filters">
                            <th></th>
                            <th>
                                <input form="usrFilter" type="text" name="username" class="form-control"
                                       placeholder="Cari username…" value="{{ request('username') }}">
                            </th>
                            <th>
                                <input form="usrFilter" type="text" name="email" class="form-control"
                                       placeholder="Cari email…" value="{{ request('email') }}">
                            </th>
                            <th></th>
                            <th>
                                <div class="filter-act">
                                    <button form="usrFilter" type="submit" class="btn btn-sm btn-search" title="Cari" aria-label="Cari">
                                        <i class="fa fa-search"></i>
                                    </button>
                                    <a href="{{ route('backend.user.index') }}" class="btn btn-sm btn-reset" title="Reset" aria-label="Reset">
                                        <i class="fa fa-refresh"></i>
                                    </a>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        @php
                            $avatar = checkFilePath(config('app.img_directory'), $item->picture)
                                ? asset('storage/' . config('app.img_directory') . $item->picture)
                                : asset('assets/img/default-user.jpg');
                            $isSelf = $authId == $item->id;
                        @endphp
                        <tr>
                            <td class="text-center num">{{ ($data->currentPage() - 1) * $data->perPage() + $index + 1 }}</td>
                            <td>
                                <div class="user-cell">
                                    <img class="avatar" src="{{ $avatar }}" alt="{{ $item->username }}">
                                    <div>
                                        <a href="{{ route('backend.user.show', $item->id) }}" class="uname">{{ $item->username }}</a>
                                        @if($isSelf)<div class="uself">Anda</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="email-cell">{{ $item->email }}</td>
                            <td class="text-center">
                                @if($item->status == 10)
                                    <span class="tag tag-on">Aktif</span>
                                @else
                                    <span class="tag tag-off">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center act">
                                <a href="{{ route('backend.user.show', $item->id) }}" class="btn btn-sm" title="Lihat detail" aria-label="Lihat detail">
                                    <i class="fa fa-eye"></i>
                                </a>

                                @unless($isSelf)
                                    @if($item->status == 10)
                                        <a href="{{ route('backend.change-active-user', [$item->id, $item->status]) }}"
                                           class="btn btn-sm off" title="Nonaktifkan" aria-label="Nonaktifkan"
                                           onclick="return confirm('Nonaktifkan user ini?')">
                                            <i class="fa fa-ban"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('backend.change-active-user', [$item->id, $item->status]) }}"
                                           class="btn btn-sm on" title="Aktifkan" aria-label="Aktifkan"
                                           onclick="return confirm('Aktifkan user ini?')">
                                            <i class="fa fa-check-circle"></i>
                                        </a>
                                    @endif

                                    <form action="{{ route('backend.user.destroy', $item->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm danger" title="Hapus" aria-label="Hapus"
                                                onclick="return confirm('Yakin akan menghapus user ini?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="usr-empty">
                                    <i class="fa fa-users"></i>
                                    @if($hasFilter)
                                        <h4>Tidak ada hasil</h4>
                                        <p>Tidak ada user yang cocok dengan filter yang dipilih.<br>
                                           <a href="{{ route('backend.user.index') }}">Reset filter</a> untuk melihat semua data.</p>
                                    @else
                                        <h4>Belum ada user</h4>
                                        <p>Mulai dengan menambahkan user pertama.</p>
                                        <a href="{{ route('backend.user.create') }}" class="btn btn-danger btn-sm">
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
