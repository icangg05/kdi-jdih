@php
    $title = 'Detail Data Pembentukan PUU';

    $jenisLabels = [
        'naskah_akademik' => 'Naskah Akademik',
        'naskah_keterangan_penjelasan' => 'Naskah Keterangan dan/atau Penjelasan',
        'rancangan_puu' => 'Rancangan PUU',
        'penelitian_hukum' => 'Penelitian Hukum',
        'pengkajian_hukum' => 'Pengkajian Hukum',
        'pengkajian_konstitusi' => 'Pengkajian Konstitusi',
        'analisis_evaluasi' => 'Analisis Evaluasi',
    ];
@endphp

<x-layouts.backend :title="$title" :listNav="[['label' => 'Pembentukan PUU', 'route' => route('backend.pembentukan-puu.index')], ['label' => $title]]">

@push('link')
<style>
    /* ── Scoped ke halaman ini saja ── */
    .puu-show { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#eceae5; }
    .puu-show .box { border-top:3px solid var(--accent); box-shadow:0 1px 2px rgba(31,35,40,.06); }

    /* Header */
    .puu-show .box-header.with-border { border-bottom:1px solid var(--line); padding:20px 22px; }
    .puu-show .eyebrow { font-size:11px; text-transform:uppercase; letter-spacing:.08em; color:var(--soft); font-weight:600; margin-bottom:6px; }
    .puu-show .doc-title { font-size:21px; line-height:1.32; font-weight:600; letter-spacing:-.01em; color:var(--ink); margin:0; max-width:54ch; }
    .puu-show .doc-tags { margin-top:13px; display:flex; flex-wrap:wrap; gap:6px; }
    .puu-show .box-header .actions { margin-top:2px; }
    .puu-show .box-header .btn { border-radius:5px; }

    /* Tag vocabulary (selaras dengan index) */
    .puu-show .tag { display:inline-block; font-size:11px; font-weight:600; padding:3px 9px; border-radius:4px; line-height:1.4; }
    .puu-show .tag-type{background:#eef1f4;color:#3a4149;}
    .puu-show .tag-published{background:#e5f3e9;color:#227a3b;}
    .puu-show .tag-draft{background:#fdf2e0;color:#9a6a13;}
    .puu-show .tag-archived{background:#eceff1;color:#5a636b;}
    .puu-show .tag-public{background:#e8eff7;color:#2f5c93;}
    .puu-show .tag-private{background:#f2efe9;color:#77706a;}

    /* Section heading */
    .puu-show h4 { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:26px 0 11px; padding-bottom:8px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:9px; }
    .puu-show .pane > h4:first-child { margin-top:4px; }
    .puu-show h4::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }

    /* Definition tables (termasuk partial field-spesifik) */
    .puu-show table.table { border:1px solid var(--line); border-radius:6px; overflow:hidden; margin-bottom:6px; }
    .puu-show table.table > tbody > tr > th { background:#faf9f7; color:var(--muted); font-weight:600; font-size:12px; width:32%; vertical-align:top; border-color:var(--line) !important; padding:10px 14px; letter-spacing:.01em; }
    .puu-show table.table > tbody > tr > td { color:var(--ink); font-size:13.5px; line-height:1.62; border-color:var(--line) !important; padding:10px 14px; vertical-align:top; }
    .puu-show table.table > tbody > tr:hover > td { background:#fcfbf9; }

    /* File list */
    .puu-show .file-list { display:flex; flex-direction:column; gap:8px; margin-bottom:6px; }
    .puu-show .file-item { display:flex; align-items:center; gap:11px; padding:11px 13px; border:1px solid var(--line); border-radius:7px; background:#fff; color:var(--ink); font-size:13px; font-weight:500; transition:border-color .14s ease, transform .14s ease, box-shadow .14s ease; }
    .puu-show .file-item:hover { border-color:#d8b3ad; box-shadow:0 2px 9px rgba(192,57,43,.08); transform:translateY(-1px); text-decoration:none; color:var(--ink); }
    .puu-show .file-item .ic { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; background:#f3f0ec; color:var(--muted); flex:none; }
    .puu-show .file-item.primary .ic { background:#fdecea; color:var(--accent); }
    .puu-show .file-item .arw { margin-left:auto; color:var(--soft); font-size:12px; }
    .puu-show .file-empty { font-size:13px; color:var(--soft); padding:2px 0 6px; }

    /* Statistik */
    .puu-show .stat-grid { display:grid; grid-template-columns:1fr 1fr; gap:1px; background:var(--line); border:1px solid var(--line); border-radius:7px; overflow:hidden; }
    .puu-show .stat { background:#fff; padding:14px 15px; }
    .puu-show .stat .v { font-size:22px; font-weight:600; color:var(--ink); font-variant-numeric:tabular-nums; letter-spacing:-.02em; line-height:1.1; }
    .puu-show .stat .l { font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:var(--soft); margin-top:3px; }
    .puu-show .stat-time { margin-top:11px; font-size:12.5px; color:var(--muted); line-height:2; }
    .puu-show .stat-time i { color:var(--soft); width:17px; text-align:center; }
</style>
@endpush

<div class="row puu-show">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="pull-right actions">
                    <a href="{{ route('backend.pembentukan-puu.edit', $puu->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                    <a href="{{ route('backend.pembentukan-puu.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="eyebrow">Detail · Pembentukan PUU</div>
                <h3 class="doc-title">{{ $puu->judul }}</h3>
                <div class="doc-tags">
                    <span class="tag tag-type">{{ $jenisLabels[$puu->jenis_dokumen] ?? $puu->jenis_dokumen }}</span>
                    @if($puu->status_publikasi == 'published')
                        <span class="tag tag-published">Published</span>
                    @elseif($puu->status_publikasi == 'draft')
                        <span class="tag tag-draft">Draft</span>
                    @elseif($puu->status_publikasi == 'archived')
                        <span class="tag tag-archived">Arsip</span>
                    @endif
                    @if($puu->hak_akses == 'public')
                        <span class="tag tag-public">Public</span>
                    @else
                        <span class="tag tag-private">Private</span>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-8 pane">
                        <h4>Informasi Dasar</h4>
                        <table class="table">
                            <tr>
                                <th width="30%">Jenis Dokumen</th>
                                <td><span class="tag tag-type">{{ $jenisLabels[$puu->jenis_dokumen] ?? $puu->jenis_dokumen }}</span></td>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>{{ $puu->judul }}</td>
                            </tr>
                            <tr>
                                <th>Nomor Dokumen</th>
                                <td>{{ $puu->nomor_dokumen ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tahun</th>
                                <td>{{ $puu->tahun }}</td>
                            </tr>
                            <tr>
                                <th>Lembaga Pemrakarsa</th>
                                <td>{{ $puu->lembaga_pemrakarsa }}</td>
                            </tr>
                            <tr>
                                <th>Status Dokumen</th>
                                <td>{{ $puu->status_dokumen }}</td>
                            </tr>
                            <tr>
                                <th>Tahapan Pembentukan</th>
                                <td>{{ $puu->tahapan_pembentukan ?? '-' }}</td>
                            </tr>
                        </table>

                        <h4>Informasi Umum</h4>
                        <table class="table">
                            <tr>
                                <th width="30%">Abstrak/Ringkasan</th>
                                <td>{!! nl2br(e($puu->abstrak)) ?? '-' !!}</td>
                            </tr>
                            <tr>
                                <th>Kata Kunci</th>
                                <td>{{ $puu->kata_kunci ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Penulis/Penyusun</th>
                                <td>{{ $puu->penulis ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Editor/Reviewer</th>
                                <td>{{ $puu->editor ?? '-' }}</td>
                            </tr>
                        </table>

                        {{-- Tampilkan field spesifik berdasarkan jenis --}}
                        @include('backend.pembentukan-puu.partials.field-spesifik')

                    </div>

                    <div class="col-md-4 pane">
                        <h4>Pengelolaan Dokumen</h4>
                        <table class="table">
                            <tr>
                                <th width="40%">Status Publikasi</th>
                                <td>
                                    @if($puu->status_publikasi == 'published')
                                        <span class="tag tag-published">Published</span>
                                    @elseif($puu->status_publikasi == 'draft')
                                        <span class="tag tag-draft">Draft</span>
                                    @elseif($puu->status_publikasi == 'archived')
                                        <span class="tag tag-archived">Arsip</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Hak Akses</th>
                                <td>
                                    @if($puu->hak_akses == 'public')
                                        <span class="tag tag-public">Public</span>
                                    @else
                                        <span class="tag tag-private">Private</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>{{ $puu->kategori ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Pengunggah</th>
                                <td>{{ $puu->pengunggah }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Unggah</th>
                                <td>{{ \Carbon\Carbon::parse($puu->tanggal_unggah)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $puu->keterangan ?? '-' }}</td>
                            </tr>
                        </table>

                        <h4>File Dokumen</h4>
                        <div class="file-list">
                            @if($puu->dokumen_utama)
                            <a href="{{ route('backend.pembentukan-puu.download', ['id' => $puu->id, 'type' => 'dokumen']) }}" class="file-item primary">
                                <span class="ic"><i class="fa fa-file-pdf-o"></i></span>
                                Dokumen Utama (PDF)
                                <span class="arw"><i class="fa fa-download"></i></span>
                            </a>
                            @endif

                            @if($puu->cover)
                            <a href="{{ route('backend.pembentukan-puu.download', ['id' => $puu->id, 'type' => 'cover']) }}" class="file-item">
                                <span class="ic"><i class="fa fa-image"></i></span>
                                Cover/Gambar
                                <span class="arw"><i class="fa fa-download"></i></span>
                            </a>
                            @endif

                            @if($puu->lampiran)
                            <a href="{{ route('backend.pembentukan-puu.download', ['id' => $puu->id, 'type' => 'lampiran']) }}" class="file-item">
                                <span class="ic"><i class="fa fa-paperclip"></i></span>
                                Lampiran
                                <span class="arw"><i class="fa fa-download"></i></span>
                            </a>
                            @endif

                            @if(!$puu->dokumen_utama && !$puu->cover && !$puu->lampiran)
                            <div class="file-empty">Belum ada file terlampir.</div>
                            @endif
                        </div>

                        <h4>Statistik</h4>
                        <div class="stat-grid">
                            <div class="stat">
                                <div class="v">{{ number_format($puu->jumlah_download) }}</div>
                                <div class="l"><i class="fa fa-download"></i> Unduhan</div>
                            </div>
                            <div class="stat">
                                <div class="v">{{ number_format($puu->views) }}</div>
                                <div class="l"><i class="fa fa-eye"></i> Dilihat</div>
                            </div>
                        </div>
                        <div class="stat-time">
                            <div><i class="fa fa-clock-o"></i> Dibuat: {{ $puu->created_at->format('d/m/Y H:i') }}</div>
                            <div><i class="fa fa-refresh"></i> Diupdate: {{ $puu->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.backend>
