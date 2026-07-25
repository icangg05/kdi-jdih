import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'doc_view.dart';

const docCategories = [
  (slug: 'peraturan', label: 'Peraturan & Keputusan', short: 'Peraturan', icon: Icons.account_balance),
  (slug: 'monografi', label: 'Monografi Hukum', short: 'Monografi', icon: Icons.menu_book),
  (slug: 'artikel', label: 'Artikel / Majalah Hukum', short: 'Artikel', icon: Icons.article),
  (slug: 'putusan', label: 'Putusan', short: 'Putusan', icon: Icons.gavel),
];

String docCategoryLabel(String slug) =>
    docCategories.where((c) => c.slug == slug).firstOrNull?.label ?? slug;

class DocumentCard extends StatelessWidget {
  const DocumentCard(this.d, {super.key, this.accuracy});
  final Json d;

  /// Skor akurasi 0-100 (hasil AI); null = sembunyikan.
  final int? accuracy;

  @override
  Widget build(BuildContext context) {
    final muted = Theme.of(context).brightness == Brightness.dark
        ? C.darkInkMuted
        : C.lightInkMuted;
    final nomor = d.sn('nomor_peraturan');
    final tahun = d.sn('tahun_terbit');
    return Pressable(
      child: Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: () => Navigator.push(
            context,
            MaterialPageRoute(
                builder: (_) => DocumentDetailScreen(id: d.i('id')))),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(children: [
                JenisChip(d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan')),
                if (tahun != null) ...[
                  const SizedBox(width: 8),
                  Text(tahun, style: TextStyle(fontSize: 12, color: muted)),
                ],
                const Spacer(),
                StatusBadge(d.sn('status') ?? d.sn('status_terakhir')),
              ]),
              const SizedBox(height: 8),
              Text(d.s('judul'),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600)),
              const SizedBox(height: 6),
              Text(
                [
                  if (nomor != null) 'No. $nomor',
                  if (d.sn('bidang_hukum') != null) d.s('bidang_hukum'),
                ].join(' · '),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(fontSize: 12, color: muted),
              ),
              const SizedBox(height: 6),
              if (accuracy != null)
                Row(children: [
                  Expanded(
                      child: ClipRRect(
                          borderRadius: BorderRadius.circular(4),
                          child: LinearProgressIndicator(
                              value: accuracy! / 100,
                              minHeight: 6,
                              backgroundColor: C.primary.withValues(alpha: .12)))),
                  const SizedBox(width: 8),
                  Text('$accuracy%',
                      style: const TextStyle(
                          fontSize: 12, fontWeight: FontWeight.w700, color: C.primaryInk)),
                ])
              else
                Row(children: [
                  Icon(Icons.visibility_outlined, size: 14, color: muted),
                  const SizedBox(width: 4),
                  Text('${d.i('hit_see')}', style: TextStyle(fontSize: 12, color: muted)),
                  const SizedBox(width: 12),
                  Icon(Icons.download_outlined, size: 14, color: muted),
                  const SizedBox(width: 4),
                  Text('${d.i('hit_download')}',
                      style: TextStyle(fontSize: 12, color: muted)),
                ]),
            ],
          ),
        ),
      ),
    ),
    );
  }
}

/// Tab "Dokumen": empat pintu koleksi + daftar produk hukum terbaru
/// yang bisa disaring per kategori.
class DocumentsHubScreen extends StatefulWidget {
  const DocumentsHubScreen({super.key});

  @override
  State<DocumentsHubScreen> createState() => _DocumentsHubScreenState();
}

class _DocumentsHubScreenState extends State<DocumentsHubScreen> {
  /// Jumlah per kategori diambil sekali dari /home (statistik), bukan
  /// empat panggilan terpisah.
  late final Future<Json> _stats = api.home();
  int _tab = 0;

  static const _subtitle = {
    'peraturan': 'Perda, Perwali, dan Keputusan Wali Kota',
    'monografi': 'Buku dan kajian hukum daerah',
    'artikel': 'Artikel dan majalah hukum',
    'putusan': 'Putusan pengadilan terkait daerah',
  };

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Dokumen Hukum', showBack: false),
        body: CustomScrollView(slivers: [
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, AppSpacing.lg, AppSpacing.lg, 0),
            sliver: SliverList.list(children: [
              FutureBuilder<Json>(
                future: _stats,
                builder: (context, snap) {
                  final stats = snap.data?.m('statistik') ?? const <String, dynamic>{};
                  return Column(children: [
                    for (final (i, c) in docCategories.indexed)
                      Padding(
                        padding: const EdgeInsets.only(bottom: AppSpacing.md),
                        child: Rise(
                          delayMs: i * 45,
                          child: _CategoryCard(
                            category: c,
                            subtitle: _subtitle[c.slug] ?? '',
                            count: snap.hasData ? stats.i(c.slug) : null,
                            accent: _accents[i],
                          ),
                        ),
                      ),
                  ]);
                },
              ),
              SectionHeader('Produk Hukum Terbaru',
                  onSeeAll: () => Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => DocumentListScreen(
                              category: docCategories[_tab].slug)))),
              FilterPills(
                labels: [for (final c in docCategories) c.short],
                selected: _tab,
                onSelected: (i) => setState(() => _tab = i),
              ),
              const SizedBox(height: AppSpacing.md),
            ]),
          ),
          SliverPadding(
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, 0, AppSpacing.lg, AppSpacing.xxl),
            sliver: _LatestDocuments(
                key: ValueKey(_tab), category: docCategories[_tab].slug),
          ),
        ]),
      );

  /// Empat tint hangat berbeda supaya keempat kartu tidak terbaca seragam.
  static const _accents = [C.primary, C.gold, C.primarySoft, C.statusChanged];
}

class _CategoryCard extends StatelessWidget {
  const _CategoryCard(
      {required this.category,
      required this.subtitle,
      required this.count,
      required this.accent});
  final ({String slug, String label, String short, IconData icon}) category;
  final String subtitle;

  /// null selagi jumlah masih dimuat.
  final int? count;
  final Color accent;

  @override
  Widget build(BuildContext context) => Pressable(
        child: Card(
          child: InkWell(
            borderRadius: BorderRadius.circular(AppRadius.card),
            onTap: () => Navigator.push(
                context,
                MaterialPageRoute(
                    builder: (_) => DocumentListScreen(category: category.slug))),
            child: Padding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Row(children: [
                Container(
                  width: 52,
                  height: 52,
                  decoration: BoxDecoration(
                    color: accent.withValues(alpha: .14),
                    borderRadius: BorderRadius.circular(16),
                  ),
                  child: Icon(category.icon, color: C.ink, size: 26),
                ),
                const SizedBox(width: AppSpacing.lg),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(category.label,
                          style: const TextStyle(
                              fontSize: 16, fontWeight: FontWeight.w700)),
                      const SizedBox(height: 2),
                      Text(subtitle,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                              fontSize: 12, color: C.lightInkMuted)),
                      const SizedBox(height: AppSpacing.sm),
                      count == null
                          ? Pulse(
                              child: Container(
                                width: 120,
                                height: 12,
                                decoration: BoxDecoration(
                                  color: C.lightSubtle,
                                  borderRadius: BorderRadius.circular(6),
                                ),
                              ),
                            )
                          : Text('$count dokumen tersedia',
                              style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  color: C.primaryInk,
                                  fontFeatures: [
                                    FontFeature.tabularFigures()
                                  ])),
                    ],
                  ),
                ),
                const Icon(Icons.chevron_right, color: C.lightInkMuted),
              ]),
            ),
          ),
        ),
      );
}

/// Lima dokumen terbaru pada kategori terpilih.
class _LatestDocuments extends StatefulWidget {
  const _LatestDocuments({super.key, required this.category});
  final String category;

  @override
  State<_LatestDocuments> createState() => _LatestDocumentsState();
}

class _LatestDocumentsState extends State<_LatestDocuments> {
  late final Future<Paginated> _future =
      api.documents(category: widget.category);

  @override
  Widget build(BuildContext context) => FutureBuilder<Paginated>(
        future: _future,
        builder: (context, snap) {
          if (snap.hasError) {
            return SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.symmetric(vertical: AppSpacing.xl),
                child: Text('${snap.error}',
                    textAlign: TextAlign.center,
                    style: const TextStyle(color: C.lightInkMuted)),
              ),
            );
          }
          if (!snap.hasData) {
            return SliverList.separated(
              itemCount: 3,
              separatorBuilder: (_, __) => const SizedBox(height: AppSpacing.md),
              itemBuilder: (_, __) => Pulse(
                child: Container(
                  height: 132,
                  decoration: BoxDecoration(
                    color: C.lightSubtle,
                    borderRadius: BorderRadius.circular(AppRadius.card),
                  ),
                ),
              ),
            );
          }
          final items = snap.data!.items.take(5).toList();
          return SliverList.separated(
            itemCount: items.length,
            separatorBuilder: (_, __) => const SizedBox(height: AppSpacing.md),
            itemBuilder: (_, i) =>
                Rise(delayMs: i * 45, child: DocumentCard(items[i])),
          );
        },
      );
}

class DocumentListScreen extends StatefulWidget {
  const DocumentListScreen({super.key, required this.category});
  final String category;

  @override
  State<DocumentListScreen> createState() => _DocumentListScreenState();
}

class _DocumentListScreenState extends State<DocumentListScreen> {
  String _q = '', _jenis = '', _tahun = '', _status = '';
  Json _filters = {};

  @override
  void initState() {
    super.initState();
    api.documentFilters(widget.category).then((f) {
      if (mounted) setState(() => _filters = f);
    }).catchError((_) {}); // filter gagal -> daftar tetap jalan
  }

  Widget _dropdown(String hint, String value, String key, void Function(String) set) {
    final opts = _filters.ls(key);
    if (opts.isEmpty) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: DropdownButton<String>(
        hint: Text(hint),
        value: value.isEmpty ? null : value,
        underline: const SizedBox.shrink(),
        borderRadius: BorderRadius.circular(12),
        items: opts
            .map((o) => DropdownMenuItem(
                value: o, child: Text(o, overflow: TextOverflow.ellipsis)))
            .toList(),
        onChanged: (v) => setState(() => set(v ?? '')),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final hasFilter =
        _q.isNotEmpty || _jenis.isNotEmpty || _tahun.isNotEmpty || _status.isNotEmpty;
    return Scaffold(
      appBar: BrandAppBar(docCategoryLabel(widget.category)),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: TextField(
              decoration: const InputDecoration(
                  hintText: 'Cari judul...', prefixIcon: Icon(Icons.search)),
              textInputAction: TextInputAction.search,
              onSubmitted: (v) => setState(() => _q = v),
            ),
          ),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: Row(children: [
              _dropdown('Jenis', _jenis, 'jenis', (v) => _jenis = v),
              _dropdown('Tahun', _tahun, 'tahun', (v) => _tahun = v),
              _dropdown('Status', _status, 'status', (v) => _status = v),
              if (hasFilter)
                TextButton(
                    onPressed: () => setState(() {
                          _q = '';
                          _jenis = '';
                          _tahun = '';
                          _status = '';
                        }),
                    child: const Text('Reset')),
            ]),
          ),
          Expanded(
            child: PagedListView(
              key: ValueKey('${widget.category}|$_q|$_jenis|$_tahun|$_status'),
              fetch: (page) => api.documents(
                  category: widget.category,
                  q: _q,
                  jenis: _jenis,
                  tahun: _tahun,
                  status: _status,
                  page: page),
              empty: 'Tidak ada dokumen sesuai filter.',
              itemBuilder: (_, d, __) => DocumentCard(d),
            ),
          ),
        ],
      ),
    );
  }
}

/// Detail dokumen: hero color-block, tab Tentang/Berkas/Terkait,
/// dan bilah aksi tetap di bawah (cermin layout referensi).
class DocumentDetailScreen extends StatefulWidget {
  const DocumentDetailScreen({super.key, required this.id});
  final int id;

  @override
  State<DocumentDetailScreen> createState() => _DocumentDetailScreenState();
}

class _DocumentDetailScreenState extends State<DocumentDetailScreen> {
  int _tab = 0;

  @override
  Widget build(BuildContext context) => Scaffold(
        body: LoadView(
          load: () => api.documentDetail(widget.id),
          builder: (context, d) {
            final lampiran = d.l('lampiran');
            final utama = lampiran.firstOrNull;
            return Column(children: [
              Expanded(
                child: CustomScrollView(slivers: [
                  _heroBar(context, d),
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(AppSpacing.lg,
                        AppSpacing.lg, AppSpacing.lg, AppSpacing.lg),
                    sliver: SliverList.list(children: _body(context, d)),
                  ),
                ]),
              ),
              _actionBar(context, d, utama),
            ]);
          },
        ),
      );

  Widget _heroBar(BuildContext context, Json d) => SliverAppBar(
        expandedHeight: 176,
        pinned: true,
        backgroundColor: C.lightSurface,
        surfaceTintColor: Colors.transparent,
        leadingWidth: 64,
        leading: Padding(
          padding: const EdgeInsets.only(left: AppSpacing.lg),
          child: RoundIconButton(Icons.arrow_back,
              tooltip: 'Kembali', onPressed: () => Navigator.pop(context)),
        ),
        flexibleSpace: FlexibleSpaceBar(
          background: Stack(fit: StackFit.expand, children: [
            const DecoratedBox(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                    colors: [C.ink, C.inkSoft],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight),
              ),
            ),
            const GeoPattern(opacity: .14),
            Positioned(
              left: AppSpacing.lg,
              bottom: AppSpacing.lg,
              right: AppSpacing.lg,
              child: Row(children: [
                Icon(Icons.account_balance,
                    size: 42, color: C.cream.withValues(alpha: .5)),
                const Spacer(),
                Text(d.sn('tahun_terbit') ?? '',
                    style: TextStyle(
                        fontSize: 32,
                        fontWeight: FontWeight.w800,
                        color: C.cream.withValues(alpha: .28))),
              ]),
            ),
          ]),
        ),
      );

  List<Widget> _body(BuildContext context, Json d) {
    final nomor = d.sn('nomor_peraturan');
    final tahun = d.sn('tahun_terbit');
    final lampiran = d.l('lampiran');
    final terkait = d.l('peraturan_terkait');
    return [
      Row(children: [
        JenisChip(d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan')),
        const SizedBox(width: AppSpacing.sm),
        StatusBadge(d.sn('status') ?? d.sn('status_terakhir')),
      ]),
      const SizedBox(height: AppSpacing.md),
      Text(d.s('judul'),
          style: const TextStyle(
              fontSize: 20, height: 1.3, fontWeight: FontWeight.w700)),
      if (nomor != null || tahun != null) ...[
        const SizedBox(height: AppSpacing.sm),
        Row(children: [
          const Icon(Icons.tag, size: 15, color: C.lightInkMuted),
          const SizedBox(width: 4),
          Expanded(
            child: Text(
                [
                  if (nomor != null) 'Nomor $nomor',
                  if (tahun != null) 'Tahun $tahun',
                ].join(' · '),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontSize: 13, color: C.lightInkMuted)),
          ),
        ]),
      ],
      if (d.sn('bidang_hukum') != null) ...[
        const SizedBox(height: AppSpacing.xs),
        Row(children: [
          const Icon(Icons.gavel, size: 15, color: C.lightInkMuted),
          const SizedBox(width: 4),
          Expanded(
            child: Text(d.s('bidang_hukum'),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontSize: 13, color: C.lightInkMuted)),
          ),
        ]),
      ],
      const SizedBox(height: AppSpacing.lg),
      FilterPills(
        labels: ['Tentang', 'Berkas (${lampiran.length})', 'Terkait'],
        selected: _tab,
        onSelected: (i) => setState(() => _tab = i),
      ),
      const SizedBox(height: AppSpacing.lg),
      ...switch (_tab) {
        1 => _berkasTab(context, lampiran),
        2 => _terkaitTab(context, terkait),
        _ => _tentangTab(d),
      },
      const SizedBox(height: AppSpacing.lg),
    ];
  }

  List<Widget> _tentangTab(Json d) {
    final abstrak = d.sn('abstrak');
    final subjek = d.ls('subjek');
    final pengarang = d.l('pengarang');
    final specs = <(IconData, String, String)>[
      (Icons.category_outlined,
          d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan') ?? '-', 'Jenis'),
      (Icons.event_outlined, d.sn('tahun_terbit') ?? '-', 'Tahun'),
      (
        Icons.verified_outlined,
        d.sn('status') ?? d.sn('status_terakhir') ?? '-',
        'Status'
      ),
      (Icons.translate, d.sn('bahasa') ?? '-', 'Bahasa'),
    ];
    return [
      const Text('Tentang Dokumen',
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
      const SizedBox(height: AppSpacing.md),
      SizedBox(
        height: 88,
        child: ListView.separated(
          scrollDirection: Axis.horizontal,
          itemCount: specs.length,
          separatorBuilder: (_, __) => const SizedBox(width: AppSpacing.sm),
          itemBuilder: (_, i) => SizedBox(
              width: 124,
              child: SpecCard(specs[i].$1, specs[i].$2, specs[i].$3)),
        ),
      ),
      if (abstrak != null) ...[
        const SizedBox(height: AppSpacing.lg),
        const Text('Abstrak',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
        ReadMore(abstrak, html: true),
      ],
      const SizedBox(height: AppSpacing.sm),
      MetaTable({
        'T.E.U.': d.sn('teu'),
        'Bentuk': d.sn('bentuk_peraturan'),
        'Jenis': d.sn('jenis_peraturan'),
        'Tempat Terbit': d.sn('tempat_terbit'),
        'Penerbit': d.sn('penerbit'),
        'Tgl. Penetapan': fmtDate(d.sn('tanggal_penetapan')),
        'Tgl. Pengundangan': fmtDate(d.sn('tanggal_pengundangan')),
        'Sumber': d.sn('sumber'),
        'Bahasa': d.sn('bahasa'),
        'Bidang Hukum': d.sn('bidang_hukum'),
        'Penandatangan': d.sn('penandatanganan'),
        'ISBN': d.sn('isbn'),
        'Deskripsi Fisik': d.sn('deskripsi_fisik'),
        'Lembaga Peradilan': d.sn('lembaga_peradilan'),
        'Pemohon': d.sn('pemohon'),
        'Termohon': d.sn('termohon'),
        'Jenis Perkara': d.sn('jenis_perkara'),
      }),
      if (subjek.isNotEmpty) ...[
        const SectionHeader('Subjek'),
        Wrap(
            spacing: AppSpacing.sm,
            runSpacing: AppSpacing.sm,
            children: [for (final s in subjek) JenisChip(s)]),
      ],
      if (pengarang.isNotEmpty) ...[
        const SectionHeader('Pengarang'),
        Text(pengarang.map((p) => p.s('nama')).join(', ')),
      ],
    ];
  }

  List<Widget> _berkasTab(BuildContext context, List<Json> lampiran) {
    if (lampiran.isEmpty) {
      return const [
        EmptyState(
          icon: Icons.folder_off_outlined,
          title: 'Belum ada berkas',
          message: 'Dokumen ini belum memiliki file yang bisa dilihat '
              'atau diunduh. Coba periksa peraturan terkait.',
        ),
      ];
    }
    return [
      for (final l in lampiran)
        if (l.sn('url') != null)
          Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.md),
            child: DocFileTile(l.s('url'),
                title: l.sn('judul') ?? 'Dokumen',
                onOpen: () => api.documentDownload(widget.id).ignore()),
          ),
    ];
  }

  List<Widget> _terkaitTab(BuildContext context, List<Json> terkait) {
    if (terkait.isEmpty) {
      return const [
        EmptyState(
          icon: Icons.link_off,
          title: 'Tidak ada peraturan terkait',
          message: 'Dokumen ini berdiri sendiri — tidak mengubah, '
              'mencabut, atau diubah oleh peraturan lain.',
        ),
      ];
    }
    return [
      for (final t in terkait)
        Padding(
          padding: const EdgeInsets.only(bottom: AppSpacing.md),
          child: Card(
            child: ListTile(
              leading: const IconSquircle(Icons.account_balance, size: 40),
              title: Text(t.s('judul'),
                  maxLines: 2, overflow: TextOverflow.ellipsis),
              trailing: const Icon(Icons.chevron_right),
              onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (_) => DocumentDetailScreen(id: t.i('id')))),
            ),
          ),
        ),
    ];
  }

  /// Bilah aksi tetap: statistik + tombol utama membuka pratinjau berkas.
  Widget _actionBar(BuildContext context, Json d, Json? utama) {
    final url = utama?.sn('url');
    final st = d.m('statistik');
    return Material(
      color: C.lightSurface,
      elevation: 8,
      shadowColor: C.ink.withValues(alpha: .12),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.fromLTRB(
              AppSpacing.lg, AppSpacing.md, AppSpacing.lg, AppSpacing.md),
          child: Row(children: [
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                const Text('Statistik',
                    style: TextStyle(fontSize: 12, color: C.lightInkMuted)),
                Text('${st.i('dilihat')} dilihat · ${st.i('diunduh')} unduh',
                    style: const TextStyle(
                        fontSize: 13, fontWeight: FontWeight.w700)),
              ],
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: FilledButton.icon(
                icon: Icon(url == null
                    ? Icons.block
                    : (isPdfUrl(url)
                        ? Icons.menu_book_outlined
                        : Icons.download_outlined)),
                label: Text(url == null
                    ? 'Berkas tidak tersedia'
                    : (isPdfUrl(url) ? 'Lihat Dokumen' : 'Unduh Dokumen'),
                    maxLines: 1, overflow: TextOverflow.ellipsis),
                onPressed: url == null
                    ? null
                    : () {
                        api.documentDownload(widget.id).ignore();
                        if (isPdfUrl(url)) {
                          Navigator.push(
                              context,
                              MaterialPageRoute(
                                  builder: (_) => DocPreviewScreen(
                                      url: url,
                                      title: utama!.sn('judul') ?? 'Dokumen')));
                        } else {
                          downloadDoc(context, url);
                        }
                      },
              ),
            ),
          ]),
        ),
      ),
    );
  }
}

