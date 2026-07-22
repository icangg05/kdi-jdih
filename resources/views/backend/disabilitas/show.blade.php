@php
    $title = 'Detail Data Disabilitas';

    // Vokabulari tag status publikasi → kelas (selaras index/show lain)
    $pubTag = [
        'published' => 'tag-published',
        'reviewed'  => 'tag-published',
        'uploaded'  => 'tag-public',
        'pending'   => 'tag-draft',
        'draft'     => 'tag-draft',
        'deleted'   => 'tag-archived',
    ];
    $pubClass = $pubTag[$disabilitas->status_publikasi] ?? 'tag-archived';
    $aksesPublic = $disabilitas->hak_akses === 'public';
@endphp

<x-layouts.backend :title="$title" :listNav="[['label' => 'Disabilitas', 'route' => route('backend.disabilitas.index')], ['label' => $title]]">

@push('link')
<style>
    /* ── Scoped ke halaman ini saja (dis-show) ── */
    .dis-show { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .dis-show .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }

    /* Header */
    .dis-show .box-header.with-border { border-bottom:1px solid var(--line); padding:20px 22px; }
    .dis-show .eyebrow { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--soft); font-weight:600; margin-bottom:6px; }
    .dis-show .doc-title { font-size:21px; line-height:1.32; font-weight:600; letter-spacing:-.01em; color:var(--ink); margin:0; max-width:54ch; }
    .dis-show .doc-tags { margin-top:13px; display:flex; flex-wrap:wrap; gap:6px; }
    .dis-show .box-header .actions { margin-top:2px; }
    .dis-show .box-header .btn { border-radius:5px; }

    /* Tag vocabulary (selaras dengan index) */
    .dis-show .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .dis-show .tag-type{background:#eef1f4;color:#3a4149;}
    .dis-show .tag-published{background:#e5f3e9;color:#227a3b;}
    .dis-show .tag-draft{background:#fdf2e0;color:#9a6a13;}
    .dis-show .tag-archived{background:#eceff1;color:#5a636b;}
    .dis-show .tag-public{background:#e8eff7;color:#2f5c93;}
    .dis-show .tag-private{background:#f2efe9;color:#77706a;}
    .dis-show .tag-soft{background:#f3f0ec;color:#6b6357;}

    /* Section heading */
    .dis-show h4 { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:26px 0 11px; padding-bottom:8px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:9px; }
    .dis-show .pane > h4:first-child { margin-top:4px; }
    .dis-show h4::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }

    /* Definition tables */
    .dis-show table.table { border:1px solid var(--line); border-radius:6px; overflow:hidden; margin-bottom:6px; }
    .dis-show table.table > tbody > tr > th { background:#faf9f7; color:var(--muted); font-weight:600; font-size:12px; width:32%; vertical-align:top; border-color:var(--line) !important; padding:10px 14px; letter-spacing:.01em; }
    .dis-show table.table > tbody > tr > td { color:var(--ink); font-size:13.5px; line-height:1.62; border-color:var(--line) !important; padding:10px 14px; vertical-align:top; }
    .dis-show table.table > tbody > tr:hover > td { background:#fcfbf9; }
    .dis-show .prose { white-space:pre-line; }

    /* Chip badges dalam sel */
    .dis-show .chipset { display:flex; flex-wrap:wrap; gap:6px; }

    /* File list */
    .dis-show .file-list { display:flex; flex-direction:column; gap:8px; margin-bottom:6px; }
    .dis-show .file-item { display:flex; align-items:center; gap:11px; padding:11px 13px; border:1px solid var(--line); border-radius:7px; background:#fff; color:var(--ink); font-size:13px; font-weight:500; transition:border-color .14s ease, transform .14s ease, box-shadow .14s ease; }
    .dis-show .file-item:hover { border-color:#d8b3ad; box-shadow:0 2px 9px rgba(192,57,43,.08); transform:translateY(-1px); text-decoration:none; color:var(--ink); }
    .dis-show .file-item .ic { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; background:#f3f0ec; color:var(--muted); flex:none; }
    .dis-show .file-item.primary .ic { background:#fdecea; color:var(--accent); }
    .dis-show .file-item .arw { margin-left:auto; color:var(--soft); font-size:12px; }
    .dis-show .file-empty { font-size:13px; color:var(--soft); padding:2px 0 6px; }

    /* Cover */
    .dis-show .cover-thumb { border:1px solid var(--line); border-radius:8px; padding:5px; background:#fff; max-width:170px; margin-bottom:8px; }

    /* Waktu sistem */
    .dis-show .stat-time { margin-top:11px; font-size:12.5px; color:var(--muted); line-height:2; }
    .dis-show .stat-time i { color:var(--soft); width:17px; text-align:center; }
</style>
@endpush

<div class="row dis-show">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-right actions">
                    <a href="{{ route('backend.disabilitas.edit', $disabilitas->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                    <a href="{{ route('backend.disabilitas.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="eyebrow">Detail · Disabilitas</div>
                <h3 class="doc-title">{{ $disabilitas->judul }}</h3>
                <div class="doc-tags">
                    <span class="tag tag-type">{{ $disabilitas->jenis_dokumen_formatted }}</span>
                    <span class="tag {{ $pubClass }}">{{ $disabilitas->status_publikasi_formatted }}</span>
                    <span class="tag {{ $aksesPublic ? 'tag-public' : 'tag-private' }}">{{ $disabilitas->hak_akses_formatted }}</span>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 pane">
                        <h4>Informasi Dasar</h4>
                        <table class="table">
                            <tr><th width="32%">Jenis Dokumen</th><td><span class="tag tag-type">{{ $disabilitas->jenis_dokumen_formatted }}</span></td></tr>
                            <tr><th>Judul</th><td>{{ $disabilitas->judul }}</td></tr>
                            <tr><th>Nomor Dokumen</th><td>{{ $disabilitas->nomor_dokumen ?? '—' }}</td></tr>
                            <tr><th>Tahun</th><td>{{ $disabilitas->tahun ?? '—' }}</td></tr>
                            <tr><th>Tempat Penetapan</th><td>{{ $disabilitas->tempat_penetapan ?? '—' }}</td></tr>
                            <tr><th>Tanggal Penetapan</th><td>{{ $disabilitas->tanggal_penetapan_formatted }}</td></tr>
                            <tr><th>Lembaga Penetap</th><td>{{ $disabilitas->lembaga_penetap ?? '—' }}</td></tr>
                            <tr><th>Status Dokumen</th><td>{{ $disabilitas->status_dokumen_formatted }}</td></tr>
                            <tr><th>Dokumen Terkait</th><td>{{ $disabilitas->dokumen_terkait ?? '—' }}</td></tr>
                        </table>

                        <h4>Klasifikasi Disabilitas</h4>
                        <table class="table">
                            <tr>
                                <th width="32%">Jenis Disabilitas</th>
                                <td>
                                    @php
                                        $jd = $disabilitas->jenis_disabilitas;
                                        if (is_string($jd)) $jd = json_decode($jd, true);
                                        $jd = is_array($jd) ? $jd : [];
                                    @endphp
                                    @if(!empty($jd))
                                        @php
                                            $jdLabels = ['fisik'=>'Fisik','intelektual'=>'Intelektual','mental'=>'Mental','sensorik'=>'Sensorik','ganda'=>'Ganda/Majemuk','lainnya'=>'Lainnya'];
                                        @endphp
                                        <div class="chipset">
                                            @foreach($jd as $j)
                                                <span class="tag tag-soft">{{ $jdLabels[$j] ?? ucfirst($j) }}</span>
                                            @endforeach
                                        </div>
                                    @else — @endif
                                </td>
                            </tr>
                            <tr><th>Ruang Lingkup</th><td>{{ $disabilitas->ruang_lingkup_formatted }}</td></tr>
                            <tr>
                                <th>Sektor Kebijakan</th>
                                <td>
                                    @php
                                        $sk = $disabilitas->sektor_kebijakan;
                                        if (is_string($sk)) $sk = json_decode($sk, true);
                                        $sk = is_array($sk) ? $sk : [];
                                    @endphp
                                    @if(!empty($sk))
                                        @php
                                            $skLabels = ['pendidikan'=>'Pendidikan','kesehatan'=>'Kesehatan','ketenagakerjaan'=>'Ketenagakerjaan','sosial'=>'Sosial','aksesibilitas'=>'Aksesibilitas','hukum'=>'Hukum & HAM','politik'=>'Politik','lainnya'=>'Lainnya'];
                                        @endphp
                                        <div class="chipset">
                                            @foreach($sk as $s)
                                                <span class="tag tag-public">{{ $skLabels[$s] ?? ucfirst($s) }}</span>
                                            @endforeach
                                        </div>
                                    @else — @endif
                                </td>
                            </tr>
                        </table>

                        <h4>Informasi Konten</h4>
                        <table class="table">
                            <tr>
                                <th width="32%">Abstrak / Sinopsis</th>
                                <td>@if($disabilitas->abstrak)<div class="prose">{{ $disabilitas->abstrak }}</div>@else — @endif</td>
                            </tr>
                            <tr>
                                <th>Kata Kunci</th>
                                <td>
                                    @if(!empty($disabilitas->kata_kunci_array))
                                        <div class="chipset">
                                            @foreach($disabilitas->kata_kunci_array as $kw)
                                                <span class="tag tag-soft">{{ $kw }}</span>
                                            @endforeach
                                        </div>
                                    @else — @endif
                                </td>
                            </tr>
                            <tr><th>Jumlah Halaman</th><td>{{ $disabilitas->jumlah_halaman ? $disabilitas->jumlah_halaman.' halaman' : '—' }}</td></tr>
                            <tr><th>Bahasa</th><td>{{ $disabilitas->bahasa_formatted }}</td></tr>
                        </table>

                        @if($disabilitas->penulis || $disabilitas->penerbit || $disabilitas->isbn_issn || $disabilitas->doi || $disabilitas->sumber || $disabilitas->url_referensi)
                        <h4>Referensi & Sumber</h4>
                        <table class="table">
                            @if($disabilitas->penulis)<tr><th width="32%">Penulis</th><td>{{ $disabilitas->penulis }}</td></tr>@endif
                            @if($disabilitas->penerbit)<tr><th>Penerbit</th><td>{{ $disabilitas->penerbit }}</td></tr>@endif
                            @if($disabilitas->isbn_issn)<tr><th>ISBN / ISSN</th><td>{{ $disabilitas->isbn_issn }}</td></tr>@endif
                            @if($disabilitas->doi)<tr><th>DOI</th><td>{{ $disabilitas->doi }}</td></tr>@endif
                            @if($disabilitas->sumber)<tr><th>Sumber</th><td>{{ $disabilitas->sumber }}</td></tr>@endif
                            @if($disabilitas->url_referensi)
                            <tr><th>URL Referensi</th><td><a href="{{ $disabilitas->url_referensi }}" target="_blank" rel="noopener">{{ $disabilitas->url_referensi }} <i class="fa fa-external-link"></i></a></td></tr>
                            @endif
                        </table>
                        @endif
                    </div>

                    <div class="col-md-4 pane">
                        <h4>Pengelolaan Dokumen</h4>
                        <table class="table">
                            <tr>
                                <th width="40%">Status Publikasi</th>
                                <td><span class="tag {{ $pubClass }}">{{ $disabilitas->status_publikasi_formatted }}</span></td>
                            </tr>
                            <tr>
                                <th>Hak Akses</th>
                                <td><span class="tag {{ $aksesPublic ? 'tag-public' : 'tag-private' }}">{{ $disabilitas->hak_akses_formatted }}</span></td>
                            </tr>
                            <tr><th>Pengunggah</th><td>{{ $disabilitas->pengunggah ?? '—' }}</td></tr>
                            <tr><th>Tanggal Unggah</th><td>{{ $disabilitas->tanggal_unggah_formatted }}</td></tr>
                            <tr><th>Keterangan</th><td>@if($disabilitas->keterangan)<div class="prose">{{ $disabilitas->keterangan }}</div>@else — @endif</td></tr>
                        </table>

                        <h4>File Dokumen</h4>
                        <div class="file-list">
                            @if($disabilitas->dokumen_utama)
                            <a href="{{ route('backend.disabilitas.download', ['id' => $disabilitas->id, 'type' => 'dokumen']) }}" class="file-item primary">
                                <span class="ic"><i class="fa fa-file-pdf-o"></i></span>
                                Dokumen Utama (PDF)
                                <span class="arw"><i class="fa fa-download"></i></span>
                            </a>
                            @endif

                            @if($disabilitas->cover)
                            <img src="{{ Storage::url($disabilitas->cover) }}" alt="Cover" class="cover-thumb">
                            <a href="{{ route('backend.disabilitas.download', ['id' => $disabilitas->id, 'type' => 'cover']) }}" class="file-item">
                                <span class="ic"><i class="fa fa-image"></i></span>
                                Cover / Gambar
                                <span class="arw"><i class="fa fa-download"></i></span>
                            </a>
                            @endif

                            @if($disabilitas->lampiran)
                            <a href="{{ route('backend.disabilitas.download', ['id' => $disabilitas->id, 'type' => 'lampiran']) }}" class="file-item">
                                <span class="ic"><i class="fa fa-paperclip"></i></span>
                                Lampiran
                                <span class="arw"><i class="fa fa-download"></i></span>
                            </a>
                            @endif

                            @if(!$disabilitas->dokumen_utama && !$disabilitas->cover && !$disabilitas->lampiran)
                            <div class="file-empty">Belum ada file terlampir.</div>
                            @endif
                        </div>

                        <h4>Data Sistem</h4>
                        <div class="stat-time">
                            <div><i class="fa fa-clock-o"></i> Dibuat: {{ \Carbon\Carbon::parse($disabilitas->created_at)->translatedFormat('d M Y, H:i') }}</div>
                            <div><i class="fa fa-refresh"></i> Diperbarui: {{ \Carbon\Carbon::parse($disabilitas->updated_at)->translatedFormat('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.backend>
