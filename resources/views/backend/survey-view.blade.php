<x-layouts.backend title="Detail Survei" :listNav="[['label' => 'Survei Kepuasan', 'route' => route('backend.survei.index')], ['label' => 'Detail']]">

@php
    $rata = round(($item->kemudahan_akses + $item->kelengkapan_informasi + $item->kecepatan_loading
        + $item->tampilan_antarmuka + $item->relevansi_pencarian) / 5, 1);
@endphp

@push('link')
<style>
    .srv-show { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .srv-show .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }
    .srv-show .box-header.with-border { border-bottom:1px solid var(--line); padding:20px 22px; }
    .srv-show .eyebrow { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--soft); font-weight:600; margin-bottom:6px; }
    .srv-show .doc-title { font-size:21px; line-height:1.32; font-weight:600; letter-spacing:-.01em; color:var(--ink); margin:0; }
    .srv-show .doc-tags { margin-top:13px; display:flex; flex-wrap:wrap; gap:8px; align-items:center; }
    .srv-show .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; background:#eef1f4; color:#3a4149; }
    .srv-show .meta-date { font-size:12.5px; color:var(--muted); }

    .srv-show h4 { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:26px 0 11px; padding-bottom:8px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:9px; }
    .srv-show .pane > h4:first-child { margin-top:4px; }
    .srv-show h4::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }

    .srv-show table.table { border:1px solid var(--line); border-radius:6px; overflow:hidden; margin-bottom:6px; }
    .srv-show table.table > tbody > tr > th { background:#faf9f7; color:var(--muted); font-weight:600; font-size:12px; width:38%; vertical-align:top; border-color:var(--line) !important; padding:10px 14px; }
    .srv-show table.table > tbody > tr > td { color:var(--ink); font-size:13.5px; line-height:1.62; border-color:var(--line) !important; padding:10px 14px; vertical-align:top; }
    .srv-show .stars { color:#e0a800; letter-spacing:1px; }
    .srv-show .stars .off { color:#dcd9d3; }
    .srv-show .stars b { color:var(--ink); font-size:12.5px; margin-left:6px; }
    .srv-show .prose { font-size:13.5px; line-height:1.7; color:var(--ink); background:#faf9f7; border:1px solid var(--line); border-radius:6px; padding:12px 14px; white-space:pre-wrap; }
    .srv-show .prose.empty { color:var(--soft); font-style:italic; }
    .srv-show .overall { display:inline-flex; align-items:baseline; gap:6px; font-size:26px; font-weight:700; color:var(--ink); }
    .srv-show .overall small { font-size:13px; color:var(--muted); font-weight:500; }
    .srv-show .meta-time { margin-top:4px; font-size:12.5px; color:var(--muted); line-height:2; word-break:break-word; }
    .srv-show .meta-time i { color:var(--soft); width:17px; text-align:center; }
    .srv-show .meta-time b { color:var(--ink); font-weight:600; }
</style>
@endpush

<div class="row srv-show">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-right actions">
                    <a href="{{ route('backend.survei.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <form style="display:inline" action="{{ route('backend.survei.destroy', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin akan menghapus jawaban ini?')">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
                <div class="eyebrow">Detail · Survei Kepuasan</div>
                <h3 class="doc-title">{{ $item->nama }}</h3>
                <div class="doc-tags">
                    <span class="tag">{{ $item->jenis_pengguna }}</span>
                    <span class="meta-date">
                        <i class="fa fa-calendar-o"></i>
                        {{ $item->created_at?->translatedFormat('d F Y, H:i') }}
                    </span>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-7 pane">
                        <h4>Penilaian</h4>
                        <table class="table">
                            @foreach($aspek as $kolom => $label)
                            @php $nilai = (int) $item->$kolom; @endphp
                            <tr>
                                <th>{{ $label }}</th>
                                <td>
                                    <span class="stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star {{ $i <= $nilai ? '' : 'off' }}"></i>
                                        @endfor
                                        <b>{{ $nilai }}/5</b>
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>Rata-rata</th>
                                <td><span class="overall">{{ $rata }} <small>/ 5</small></span></td>
                            </tr>
                        </table>

                        <h4>Saran Perbaikan</h4>
                        <div class="prose {{ $item->saran_perbaikan ? '' : 'empty' }}">{{ $item->saran_perbaikan ?: 'Tidak ada saran.' }}</div>

                        <h4>Fitur yang Diharapkan</h4>
                        <div class="prose {{ $item->fitur_harapan ? '' : 'empty' }}">{{ $item->fitur_harapan ?: 'Tidak ada masukan.' }}</div>
                    </div>

                    <div class="col-md-5 pane">
                        <h4>Data Responden</h4>
                        <table class="table">
                            <tr><th>Nama</th><td>{{ $item->nama }}</td></tr>
                            <tr><th>Email</th><td>{{ $item->email ?: '—' }}</td></tr>
                            <tr><th>Instansi</th><td>{{ $item->instansi ?: '—' }}</td></tr>
                            <tr><th>Jenis Pengguna</th><td>{{ $item->jenis_pengguna }}</td></tr>
                            <tr>
                                <th>Bersedia Dihubungi</th>
                                <td>{{ $item->bersedia_dihubungi ? 'Ya' : 'Tidak' }}</td>
                            </tr>
                            @if($item->bersedia_dihubungi)
                            <tr><th>Kontak</th><td>{{ $item->kontak ?: '—' }}</td></tr>
                            @endif
                        </table>

                        <h4>Metadata</h4>
                        <div class="meta-time">
                            <div><i class="fa fa-clock-o"></i> {{ $item->created_at?->translatedFormat('d F Y, H:i') }}</div>
                            <div><i class="fa fa-globe"></i> IP: <b>{{ $item->ip_address ?: '—' }}</b></div>
                            <div style="margin-top:6px;"><i class="fa fa-desktop"></i> {{ $item->user_agent ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.backend>
