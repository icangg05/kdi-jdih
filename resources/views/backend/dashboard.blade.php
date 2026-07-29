<x-layouts.backend title="Dashboard" :listNav="[['label' => 'Dashboard']]">

@php
    // Helper skala bar (offline-safe, tanpa Chart.js)
    $statusMax = max(1, ($statusKeberlakuan ?? collect())->max('total') ?: 1);
    $jenisMax  = max(1, ($jenisPUU ?? collect())->max('total') ?: 1);
    $timelineMax = max(1, max(
        max($peraturanData ?: [0]), max($monografiData ?: [0]), max($putusanData ?: [0]),
        max($pembentukanData ?: [0]), max($disabilitasData ?: [0])
    ));
    $surveyBars = [
        ['Kemudahan Akses', $statistikSurvei['average_kemudahan'] ?? 0],
        ['Kelengkapan Informasi', $statistikSurvei['average_kelengkapan'] ?? 0],
        ['Kecepatan Loading', $statistikSurvei['average_kecepatan'] ?? 0],
        ['Tampilan Antarmuka', $statistikSurvei['average_tampilan'] ?? 0],
        ['Relevansi Pencarian', $statistikSurvei['average_relevansi'] ?? 0],
    ];
@endphp

@push('link')
<style>
    /* ── Scoped ke dashboard (selaras .peng-* / .usr-*) ── */
    .dash {
        --ink:#1c2024; --muted:#5c636b; --soft:#9aa0a6; --line:#e9e6e0;
        --accent:#c0392b; --accent-2:#e2705f;
        --blue:#2f5c93; --green:#248a43; --amber:#b8791b; --purple:#5b3f9a; --teal:#1f7a70;
        font-variant-numeric:tabular-nums;
    }

    /* ══ Hero header ══ */
    .dash .dash-hero {
        position:relative; overflow:hidden; border-radius:10px; padding:26px 30px; margin-bottom:24px; color:#fff;
        background:
            radial-gradient(115% 150% at 100% 0%, rgba(226,112,95,.55), transparent 58%),
            radial-gradient(90% 130% at 0% 105%, rgba(91,63,154,.38), transparent 55%),
            linear-gradient(135deg,#b53224 0%,#8f2318 100%);
        box-shadow:0 18px 40px -18px rgba(143,35,24,.6);
        display:flex; align-items:flex-end; justify-content:space-between; gap:22px; flex-wrap:wrap;
    }
    .dash .dash-hero::after {
        content:''; position:absolute; inset:0; pointer-events:none; opacity:.4; mix-blend-mode:overlay;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    }
    .dash .hero-main { position:relative; z-index:1; }
    .dash .hero-eyebrow { font-size:11.5px; letter-spacing:.14em; text-transform:uppercase; font-weight:600; opacity:.82; }
    .dash .hero-title { font-size:27px; font-weight:700; letter-spacing:-.025em; margin:9px 0 5px; line-height:1.1; text-wrap:balance; }
    .dash .hero-sub { font-size:13.5px; opacity:.9; margin:0; }
    .dash .hero-stat { position:relative; z-index:1; text-align:right; }
    .dash .hero-stat .hs-v { font-size:40px; font-weight:800; line-height:1; letter-spacing:-.03em; }
    .dash .hero-stat .hs-l { font-size:11.5px; opacity:.85; margin-top:5px; text-transform:uppercase; letter-spacing:.06em; }

    .dash .sec-title { font-size:12px; text-transform:uppercase; letter-spacing:.05em; font-weight:700; color:var(--muted); margin:4px 0 14px; display:flex; align-items:center; gap:9px; }
    .dash .sec-title::before { content:''; width:3px; height:13px; background:var(--accent); border-radius:2px; flex:none; }
    .dash .sec-title .sub { font-weight:500; text-transform:none; letter-spacing:0; color:var(--soft); font-size:12px; margin-left:auto; }

    /* KPI kategori — grid rapat (bukan gutter Bootstrap) */
    .dash .kpi-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; margin-bottom:6px; }
    @media (max-width:1200px){ .dash .kpi-grid { grid-template-columns:repeat(3,1fr); } }
    @media (max-width:600px){ .dash .kpi-grid { grid-template-columns:repeat(2,1fr); } }

    .dash .stat-card { position:relative; display:block; background:#fff; background:linear-gradient(180deg, color-mix(in srgb, var(--cc,var(--accent)) 6%, #fff), #fff); border:1px solid var(--line); border-radius:8px; padding:15px 15px 14px 18px; box-shadow:0 1px 2px rgba(31,35,40,.04); transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease; overflow:hidden; }
    .dash a.stat-card:hover { text-decoration:none; }
    .dash .stat-card::before { content:''; position:absolute; left:0; top:0; bottom:0; width:3px; background:var(--cc,var(--accent)); }
    .dash .stat-card::after { content:''; position:absolute; right:-28px; bottom:-28px; width:92px; height:92px; border-radius:50%; background:var(--cc,var(--accent)); opacity:.07; transition:transform .35s ease; }
    .dash .stat-card:hover { transform:translateY(-4px); box-shadow:0 16px 30px -14px color-mix(in srgb, var(--cc,var(--accent)) 45%, transparent); border-color:color-mix(in srgb, var(--cc,var(--accent)) 28%, var(--line)); }
    .dash .stat-card:hover::after { transform:scale(1.4); }
    .dash .stat-card:active { transform:translateY(-1px); }
    .dash .stat-card .top { display:flex; align-items:center; justify-content:space-between; margin-bottom:13px; }
    .dash .stat-card .ic { width:36px; height:36px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:15px; }
    .dash .stat-card .arw { color:#d3cfc8; font-size:13px; transition:transform .18s ease, color .18s ease; }
    .dash .stat-card:hover .arw { color:var(--cc,var(--accent)); transform:translateX(3px); }
    .dash .stat-card .v { font-size:28px; font-weight:800; letter-spacing:-.025em; color:var(--cc,var(--ink)); line-height:1; position:relative; }
    .dash .stat-card .l { font-size:12px; color:var(--muted); margin-top:6px; font-weight:500; position:relative; }
    .dash .c-blue{background:#e8eff7;color:#2f5c93;} .dash .c-green{background:#e5f3e9;color:#227a3b;}
    .dash .c-amber{background:#fdf2e0;color:#9a6a13;} .dash .c-red{background:#fdecea;color:#c0392b;}
    .dash .c-purple{background:#efeaf7;color:#5b3f9a;} .dash .c-teal{background:#e2f2f1;color:#1f7a70;}

    /* Tile ringkasan */
    .dash .tile { position:relative; overflow:hidden; background:#fff; background:linear-gradient(180deg, color-mix(in srgb, var(--tc,var(--accent)) 7%, #fff), #fff); border:1px solid var(--line); border-left:3px solid var(--tc,var(--accent)); border-radius:8px; padding:16px 18px; box-shadow:0 1px 2px rgba(31,35,40,.05); display:flex; align-items:center; gap:14px; height:100%; transition:transform .18s ease, box-shadow .18s ease; }
    .dash .tile:hover { transform:translateY(-3px); box-shadow:0 14px 26px -14px color-mix(in srgb, var(--tc,var(--accent)) 42%, transparent); }
    .dash .tile .tic { width:46px; height:46px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:19px; background:color-mix(in srgb, var(--tc,var(--accent)) 15%, #fff); color:var(--tc,var(--accent)); flex:none; }
    .dash .tile .v { font-size:23px; font-weight:800; letter-spacing:-.02em; color:var(--ink); line-height:1.1; }
    .dash .tile .l { font-size:12px; color:var(--muted); margin-top:2px; }
    .dash .col-kpi { margin-bottom:16px; }

    /* Card panel */
    .dash .panel-card { position:relative; background:#fff; border:1px solid var(--line); border-radius:8px; box-shadow:0 1px 3px rgba(31,35,40,.06); overflow:hidden; margin-bottom:22px; }
    .dash .panel-card::before { content:''; position:absolute; inset:0 0 auto 0; height:3px; background:linear-gradient(90deg, var(--pc,var(--accent)), color-mix(in srgb, var(--pc,var(--accent)) 45%, #fff)); }
    .dash .panel-card > .head { padding:16px 18px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:11px; }
    .dash .panel-card > .head .hic { width:30px; height:30px; border-radius:7px; flex:none; display:flex; align-items:center; justify-content:center; font-size:14px; background:color-mix(in srgb, var(--pc,var(--accent)) 13%, #fff); color:var(--pc,var(--accent)); }
    .dash .panel-card > .head b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .dash .panel-card > .head .cnt { margin-left:auto; font-size:12px; font-weight:600; color:var(--pc,var(--soft)); background:color-mix(in srgb, var(--pc,var(--accent)) 9%, #fff); padding:3px 10px; border-radius:999px; }
    .dash .panel-card > .body { padding:18px; }

    /* Mini stat dalam panel */
    .dash .mini-grid { display:grid; grid-template-columns:1fr 1fr; gap:1px; background:var(--line); border:1px solid var(--line); border-radius:7px; overflow:hidden; margin-bottom:16px; }
    .dash .mini { background:#fff; background:linear-gradient(180deg, color-mix(in srgb, var(--pc,var(--accent)) 5%, #fff), #fff); padding:14px 16px; }
    .dash .mini .v { font-size:21px; font-weight:800; color:var(--ink); letter-spacing:-.02em; }
    .dash .mini .l { font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:var(--soft); margin-top:3px; }

    /* Bar list (pengganti chart, offline-safe) */
    .dash .barlist { display:flex; flex-direction:column; gap:12px; }
    .dash .barrow .barhead { display:flex; justify-content:space-between; font-size:12.5px; margin-bottom:5px; }
    .dash .barrow .barhead .k { color:var(--ink); font-weight:600; letter-spacing:.01em; }
    .dash .barrow .barhead .n { color:var(--muted); font-weight:700; }
    .dash .bartrack { height:9px; background:#f1efeb; border-radius:999px; overflow:hidden; }
    .dash .barfill { height:100%; border-radius:999px; background:linear-gradient(90deg, var(--pc,var(--accent)), color-mix(in srgb, var(--pc,var(--accent)) 50%, #fff)); transition:width .6s cubic-bezier(.2,.8,.2,1); }
    .dash .barfill.b-blue{ background:linear-gradient(90deg,#2f5c93,#7ba0cc); }
    .dash .barlist-empty { color:var(--soft); font-size:13px; padding:8px 0; }

    /* Tabel dashboard */
    .dash table.dash-table { width:100%; border:1px solid var(--line); border-radius:7px; border-collapse:separate; border-spacing:0; overflow:hidden; margin-bottom:4px; }
    .dash table.dash-table th { background:#f7f5f2; color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:.03em; font-weight:600; padding:10px 12px; text-align:left; border-bottom:1px solid #e6e3dd; }
    .dash table.dash-table td { padding:10px 12px; font-size:13px; color:var(--ink); border-bottom:1px solid #f0eee9; vertical-align:middle; }
    .dash table.dash-table tr:last-child td { border-bottom:none; }
    .dash table.dash-table tbody tr { transition:background .12s ease; }
    .dash table.dash-table tbody tr:hover td { background:color-mix(in srgb, var(--pc,var(--accent)) 5%, #fff); }
    .dash .tbl-num { font-variant-numeric:tabular-nums; }
    .dash .pill { display:inline-block; min-width:26px; text-align:center; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px; background:color-mix(in srgb, var(--pc,var(--accent)) 12%, #eef1f4); color:color-mix(in srgb, var(--pc,#3a4149) 75%, #000); }
    .dash .tblwrap { overflow-x:auto; }

    /* Recent list */
    .dash .recent { list-style:none; margin:14px 0 0; padding:0; }
    .dash .recent li { display:flex; align-items:flex-start; gap:11px; padding:10px 0; border-bottom:1px solid var(--line); }
    .dash .recent li:last-child { border-bottom:none; }
    .dash .recent .dot { width:8px; height:8px; border-radius:50%; background:var(--pc,var(--accent)); margin-top:5px; flex:none; box-shadow:0 0 0 3px color-mix(in srgb, var(--pc,var(--accent)) 15%, transparent); }
    .dash .recent .txt { font-size:13px; color:var(--ink); line-height:1.45; flex:1; }
    .dash .recent .dt { font-size:11.5px; color:var(--soft); white-space:nowrap; }
    .dash .recent-title { font-size:11px; text-transform:uppercase; letter-spacing:.04em; color:var(--muted); font-weight:700; margin:18px 0 2px; display:flex; align-items:center; gap:6px; }
    .dash .recent-title i { color:var(--pc,var(--accent)); }

    /* Rating stars */
    .dash .stars i { color:#e0a800; font-size:12px; }
    .dash .stars .rv { color:var(--muted); font-size:12px; margin-left:5px; }

    /* Timeline table sparkbar */
    .dash .spark { display:inline-block; height:7px; border-radius:999px; background:linear-gradient(90deg, var(--pc,var(--accent)), color-mix(in srgb, var(--pc,var(--accent)) 50%, #fff)); vertical-align:middle; min-width:2px; }

    /* CTA button */
    .dash .btn-cta { display:inline-flex; align-items:center; gap:8px; background:linear-gradient(135deg, var(--accent), #a5271b); color:#fff; border:none; border-radius:6px; font-weight:600; padding:10px 18px; font-size:13px; box-shadow:0 8px 18px -8px rgba(192,57,43,.6); transition:transform .16s ease, box-shadow .16s ease; }
    .dash .btn-cta:hover { color:#fff; transform:translateY(-2px); box-shadow:0 12px 22px -8px rgba(192,57,43,.7); }
    .dash .btn-cta:active { transform:translateY(0); }
</style>
@endpush

<div class="dash">

    {{-- ══ Hero ══ --}}
    <div class="dash-hero">
        <div class="hero-main">
            <div class="hero-eyebrow">JDIH Kota Kendari · Panel Admin</div>
            <h1 class="hero-title">Halo, {{ ucfirst(auth()->user()->username ?? 'Admin') }}</h1>
            <p class="hero-sub">Ringkasan koleksi &amp; aktivitas per {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="hero-stat">
            <div class="hs-v">{{ number_format($totalDokumenHukum ?? 0) }}</div>
            <div class="hs-l">Total dokumen hukum</div>
        </div>
    </div>

    {{-- ══ Statistik Dokumen ══ --}}
    <div class="sec-title">Statistik Dokumen <span class="sub">Ringkasan koleksi</span></div>
    @php
        $kpi = [
            ['Peraturan', $countPeraturan, 'fa-gavel', 'c-blue', '#2f5c93', route('backend.peraturan.index')],
            ['Monografi', $countMonografi, 'fa-book', 'c-green', '#227a3b', route('backend.monografi.index')],
            ['Artikel', $countArtikel, 'fa-newspaper-o', 'c-amber', '#9a6a13', route('backend.artikel.index')],
            ['Putusan', $countPutusan, 'fa-balance-scale', 'c-red', '#c0392b', route('backend.putusan.index')],
            ['Pembentukan PUU', $countPembentukan, 'fa-pencil-square-o', 'c-purple', '#5b3f9a', Route::has('backend.pembentukan-puu.index') ? route('backend.pembentukan-puu.index') : null],
            ['Disabilitas', $countDisabilitas, 'fa-wheelchair', 'c-teal', '#1f7a70', Route::has('backend.disabilitas.index') ? route('backend.disabilitas.index') : null],
        ];
    @endphp
    <div class="kpi-grid">
        @foreach($kpi as [$label, $count, $icon, $tint, $color, $link])
        <{{ $link ? 'a' : 'div' }} class="stat-card" style="--cc:{{ $color }}" @if($link) href="{{ $link }}" @endif>
            <div class="top">
                <div class="ic {{ $tint }}"><i class="fa {{ $icon }}"></i></div>
                @if($link)<span class="arw"><i class="fa fa-angle-right"></i></span>@endif
            </div>
            <div class="v">{{ number_format($count) }}</div>
            <div class="l">{{ $label }}</div>
        </{{ $link ? 'a' : 'div' }}>
        @endforeach
    </div>

    {{-- ══ Ringkasan & Akses ══ --}}
    <div class="sec-title" style="margin-top:8px;">Ringkasan &amp; Interaksi</div>
    <div class="row">
        @php
            $tiles = [
                ['Total Dokumen Hukum', $totalDokumenHukum, 'fa-folder-open-o', 'var(--blue)'],
                ['Koleksi PUU', $totalKoleksiPUU, 'fa-archive', 'var(--green)'],
                ['Total Akses Dokumen', $totalAkses, 'fa-eye', 'var(--amber)'],
                ['Total Download', $totalDownload, 'fa-download', 'var(--teal)'],
            ];
        @endphp
        @foreach($tiles as [$label, $val, $icon, $tc])
        <div class="col-lg-3 col-sm-6 col-kpi">
            <div class="tile" style="--tc:{{ $tc }}">
                <div class="tic"><i class="fa {{ $icon }}"></i></div>
                <div>
                    <div class="v">{{ number_format($val) }}</div>
                    <div class="l">{{ $label }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══ Distribusi (server-side, pengganti chart) ══ --}}
    <div class="sec-title" style="margin-top:8px;">Distribusi Koleksi PUU</div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel-card" style="--pc:var(--accent)">
                <div class="head"><span class="hic"><i class="fa fa-check-circle-o"></i></span><b>Status Keberlakuan</b><span class="cnt">{{ ($statusKeberlakuan ?? collect())->sum('total') }} dokumen</span></div>
                <div class="body">
                    @forelse($statusKeberlakuan ?? [] as $row)
                    <div class="barlist"><div class="barrow">
                        <div class="barhead"><span class="k">{{ Str::upper($row->status ?: 'Tidak Terdefinisi') }}</span><span class="n">{{ number_format($row->total) }}</span></div>
                        <div class="bartrack"><div class="barfill" style="width:{{ round(($row->total / $statusMax) * 100) }}%"></div></div>
                    </div></div>
                    @empty
                    <div class="barlist-empty">Belum ada data status.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel-card" style="--pc:var(--blue)">
                <div class="head"><span class="hic"><i class="fa fa-sitemap"></i></span><b>Jenis PUU</b><span class="cnt">{{ ($jenisPUU ?? collect())->sum('total') }} dokumen</span></div>
                <div class="body">
                    @forelse($jenisPUU ?? [] as $row)
                    <div class="barlist"><div class="barrow">
                        <div class="barhead"><span class="k">{{ Str::upper($row->jenis_peraturan ?: 'Lainnya') }}</span><span class="n">{{ number_format($row->total) }}</span></div>
                        <div class="bartrack"><div class="barfill b-blue" style="width:{{ round(($row->total / $jenisMax) * 100) }}%"></div></div>
                    </div></div>
                    @empty
                    <div class="barlist-empty">Belum ada data jenis.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Tren 5 Tahun ══ --}}
    <div class="panel-card" style="--pc:var(--purple)">
        <div class="head"><span class="hic"><i class="fa fa-line-chart"></i></span><b>Tren Dokumen 5 Tahun Terakhir</b><span class="cnt">{{ ($years[0] ?? '') }}–{{ ($years[count($years)-1] ?? '') }}</span></div>
        <div class="body tblwrap">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        @foreach($years as $y)<th class="text-center">{{ $y }}</th>@endforeach
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $trends = [
                            ['Peraturan', $peraturanData], ['Monografi', $monografiData],
                            ['Putusan', $putusanData], ['Pembentukan PUU', $pembentukanData],
                            ['Disabilitas', $disabilitasData],
                        ];
                    @endphp
                    @foreach($trends as [$name, $series])
                    <tr>
                        <td>{{ $name }}</td>
                        @foreach($series as $val)
                        <td class="text-center tbl-num">
                            {{ $val }}
                            <div><span class="spark" style="width:{{ max(2, round(($val / $timelineMax) * 46)) }}px; opacity:{{ $val > 0 ? 1 : .15 }}"></span></div>
                        </td>
                        @endforeach
                        <td class="text-center"><span class="pill">{{ array_sum($series) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══ Detail Pembentukan PUU & Disabilitas ══ --}}
    <div class="row">
        <div class="col-md-6">
            <div class="panel-card" style="--pc:var(--green)">
                <div class="head"><span class="hic"><i class="fa fa-pencil-square-o"></i></span><b>Statistik Pembentukan PUU</b></div>
                <div class="body">
                    <div class="mini-grid">
                        <div class="mini"><div class="v">{{ number_format($statistikPembentukan['total'] ?? 0) }}</div><div class="l">Total Dokumen</div></div>
                        <div class="mini"><div class="v">{{ ($statistikPembentukan['by_tahapan'] ?? collect())->count() }}</div><div class="l">Tahapan</div></div>
                    </div>

                    <table class="dash-table">
                        <thead><tr><th>Tahapan Pembentukan</th><th class="text-right">Jumlah</th></tr></thead>
                        <tbody>
                            @forelse($statistikPembentukan['by_tahapan'] ?? [] as $tahapan)
                            <tr><td style="text-transform:uppercase">{{ $tahapan->tahapan ?: 'Belum Ditentukan' }}</td><td class="text-right"><span class="pill">{{ $tahapan->total ?? 0 }}</span></td></tr>
                            @empty
                            <tr><td colspan="2" style="color:var(--soft)">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(($statistikPembentukan['recent_uploads'] ?? collect())->count() > 0)
                    <div class="recent-title"><i class="fa fa-clock-o"></i> Upload Terbaru</div>
                    <ul class="recent">
                        @foreach($statistikPembentukan['recent_uploads'] as $recent)
                        <li>
                            <span class="dot"></span>
                            <span class="txt">{{ Str::limit($recent->judul ?? '', 55) }}</span>
                            <span class="dt">
                                @if(is_string($recent->created_at)){{ date('d/m/Y', strtotime($recent->created_at)) }}
                                @elseif($recent->created_at instanceof \Carbon\Carbon){{ $recent->created_at->format('d/m/Y') }}
                                @else{{ $recent->created_at ?? '' }}@endif
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel-card" style="--pc:var(--teal)">
                <div class="head"><span class="hic"><i class="fa fa-wheelchair"></i></span><b>Statistik Disabilitas</b></div>
                <div class="body">
                    <div class="mini-grid">
                        <div class="mini"><div class="v">{{ number_format($statistikDisabilitas['total'] ?? 0) }}</div><div class="l">Total Dokumen</div></div>
                        <div class="mini"><div class="v">{{ ($statistikDisabilitas['by_jenis_disabilitas'] ?? collect())->count() }}</div><div class="l">Jenis Disabilitas</div></div>
                    </div>

                    <table class="dash-table">
                        <thead><tr><th>Jenis Disabilitas</th><th class="text-right">Jumlah</th></tr></thead>
                        <tbody>
                            @forelse($statistikDisabilitas['by_jenis_disabilitas'] ?? [] as $jenis)
                            <tr><td>{{ $jenis->jenis ?: 'Umum' }}</td><td class="text-right"><span class="pill">{{ $jenis->total ?? 0 }}</span></td></tr>
                            @empty
                            <tr><td colspan="2" style="color:var(--soft)">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if(($statistikDisabilitas['recent_uploads'] ?? collect())->count() > 0)
                    <div class="recent-title"><i class="fa fa-clock-o"></i> Upload Terbaru</div>
                    <ul class="recent">
                        @foreach($statistikDisabilitas['recent_uploads'] as $recent)
                        <li>
                            <span class="dot"></span>
                            <span class="txt">{{ Str::limit($recent->judul ?? '', 55) }}</span>
                            <span class="dt">
                                @if(is_string($recent->created_at)){{ date('d/m/Y', strtotime($recent->created_at)) }}
                                @elseif($recent->created_at instanceof \Carbon\Carbon){{ $recent->created_at->format('d/m/Y') }}
                                @else{{ $recent->created_at ?? '' }}@endif
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Survei Kepuasan ══ --}}
    @isset($statistikSurvei)
    <div class="panel-card" style="--pc:var(--amber)">
        <div class="head"><span class="hic"><i class="fa fa-star"></i></span><b>Survei Kepuasan Pengguna</b><span class="cnt">{{ $statistikSurvei['total'] ?? 0 }} responden</span></div>
        <div class="body">
            <div class="row">
                <div class="col-md-3 col-sm-6 col-kpi"><div class="tile" style="--tc:var(--blue)"><div class="tic"><i class="fa fa-users"></i></div><div><div class="v">{{ $statistikSurvei['total'] ?? 0 }}</div><div class="l">Total Responden</div></div></div></div>
                <div class="col-md-3 col-sm-6 col-kpi"><div class="tile" style="--tc:var(--amber)"><div class="tic"><i class="fa fa-star"></i></div><div><div class="v">{{ $statistikSurvei['average_overall'] ?? 0 }}<span style="font-size:14px;color:var(--soft)">/5</span></div><div class="l">Rating Rata-rata</div></div></div></div>
                <div class="col-md-3 col-sm-6 col-kpi"><div class="tile" style="--tc:var(--teal)"><div class="tic"><i class="fa fa-search"></i></div><div><div class="v">{{ $statistikSurvei['average_kemudahan'] ?? 0 }}<span style="font-size:14px;color:var(--soft)">/5</span></div><div class="l">Kemudahan Akses</div></div></div></div>
                <div class="col-md-3 col-sm-6 col-kpi"><div class="tile" style="--tc:var(--green)"><div class="tic"><i class="fa fa-tachometer"></i></div><div><div class="v">{{ $statistikSurvei['average_kecepatan'] ?? 0 }}<span style="font-size:14px;color:var(--soft)">/5</span></div><div class="l">Kecepatan Loading</div></div></div></div>
            </div>

            <div class="row" style="margin-top:8px;">
                <div class="col-md-6">
                    <div class="recent-title" style="margin-top:6px;"><i class="fa fa-users"></i> Distribusi Jenis Pengguna</div>
                    <table class="dash-table" style="margin-top:8px;">
                        <thead><tr><th>Jenis Pengguna</th><th class="text-center">Jumlah</th><th style="width:35%">Persentase</th></tr></thead>
                        <tbody>
                            @forelse($statistikSurvei['jenis_pengguna'] ?? [] as $jenis)
                            @php $percentage = ($statistikSurvei['total'] ?? 0) > 0 ? round(($jenis->total / $statistikSurvei['total']) * 100, 1) : 0; @endphp
                            <tr>
                                <td>{{ $jenis->jenis_pengguna ?: '—' }}</td>
                                <td class="text-center tbl-num">{{ $jenis->total ?? 0 }}</td>
                                <td>
                                    <div class="bartrack" style="margin-bottom:3px;"><div class="barfill b-blue" style="width:{{ $percentage }}%"></div></div>
                                    <span style="font-size:11px;color:var(--muted)">{{ $percentage }}%</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" style="color:var(--soft)">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="col-md-6">
                    <div class="recent-title" style="margin-top:6px;"><i class="fa fa-bar-chart"></i> Rata-rata per Aspek</div>
                    <div class="barlist" style="margin-top:12px;">
                        @foreach($surveyBars as [$label, $avg])
                        <div class="barrow">
                            <div class="barhead"><span class="k">{{ $label }}</span><span class="n">{{ $avg }}/5</span></div>
                            <div class="bartrack"><div class="barfill" style="width:{{ round(($avg / 5) * 100) }}%"></div></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if(isset($statistikSurvei['recent_surveys']) && $statistikSurvei['recent_surveys']->count() > 0)
            <div class="recent-title"><i class="fa fa-history"></i> Survei Terbaru</div>
            <div class="tblwrap" style="margin-top:8px;">
                <table class="dash-table">
                    <thead><tr><th>Nama</th><th>Jenis Pengguna</th><th>Rating</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @foreach($statistikSurvei['recent_surveys'] as $survey)
                        @php
                            $vals = [
                                $survey->kemudahan_akses ?? 0, $survey->kelengkapan_informasi ?? 0,
                                $survey->kecepatan_loading ?? 0, $survey->tampilan_antarmuka ?? 0,
                                $survey->relevansi_pencarian ?? 0,
                            ];
                            $avg = array_sum($vals) / 5;
                            $createdAt = $survey->created_at ?? null;
                            if (is_string($createdAt)) { $formattedDate = date('d/m/Y H:i', strtotime($createdAt)); }
                            elseif ($createdAt instanceof \Carbon\Carbon) { $formattedDate = $createdAt->format('d/m/Y H:i'); }
                            else { $formattedDate = ''; }
                        @endphp
                        <tr>
                            <td>{{ $survey->nama ?? '—' }}</td>
                            <td><span class="pill">{{ $survey->jenis_pengguna ?? '—' }}</span></td>
                            <td class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa {{ $i <= round($avg) ? 'fa-star' : 'fa-star-o' }}"></i>
                                @endfor
                                <span class="rv">{{ round($avg, 1) }}/5</span>
                            </td>
                            <td class="tbl-num">{{ $formattedDate }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            @if(Route::has('backend.survei.index'))
            <div style="margin-top:18px;">
                <a href="{{ route('backend.survei.index') }}" class="btn-cta">
                    <i class="fa fa-list"></i> Lihat Semua Survei
                </a>
            </div>
            @endif
        </div>
    </div>
    @endisset

</div>

</x-layouts.backend>
