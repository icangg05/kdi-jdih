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
    final status = d.sn('status') ?? d.sn('status_terakhir');
    // rail kiri mewarisi warna status: satu lirikan sudah tahu masih berlaku
    // atau tidak, tanpa harus membaca badge di ujung kanan
    final (aksen, _) = statusColors(status);
    return Pressable(
      child: Card(
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => DocumentDetailScreen(id: d.i('id')))),
          child: IntrinsicHeight(
            child: Row(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
              Container(width: 4, color: aksen),
              // blok nomor: penanda khas dokumen hukum, sekaligus jangkar visual
              if (nomor != null)
                Container(
                  width: 56,
                  color: aksen.withValues(alpha: .08),
                  padding: const EdgeInsets.symmetric(vertical: AppSpacing.md),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text('No.',
                          style: TextStyle(fontSize: 10, color: muted)),
                      Text(nomor,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.w800,
                              height: 1.1,
                              fontFeatures: [FontFeature.tabularFigures()])),
                      if (tahun != null)
                        Text(tahun,
                            style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: muted)),
                    ],
                  ),
                ),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.all(AppSpacing.md),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(children: [
                        Flexible(
                            child: JenisChip(d.sn('singkatan_jenis') ??
                                d.sn('jenis_peraturan'))),
                        const Spacer(),
                        StatusBadge(status),
                      ]),
                      const SizedBox(height: AppSpacing.sm),
                      // titleCase: judul di basis data KAPITAL SEMUA dan
                      // terbaca seperti diteriakkan
                      Text(titleCase(d.s('judul')),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                              fontSize: 15,
                              height: 1.35,
                              fontWeight: FontWeight.w700)),
                      if (d.sn('bidang_hukum') != null) ...[
                        const SizedBox(height: AppSpacing.sm),
                        MetaPill(Icons.folder_outlined, d.s('bidang_hukum')),
                      ],
                      const SizedBox(height: AppSpacing.sm),
                      if (accuracy != null)
                        Row(children: [
                          Expanded(
                              child: ClipRRect(
                                  borderRadius: BorderRadius.circular(4),
                                  child: LinearProgressIndicator(
                                      value: accuracy! / 100,
                                      minHeight: 6,
                                      backgroundColor:
                                          C.primary.withValues(alpha: .12)))),
                          const SizedBox(width: 8),
                          Text('$accuracy%',
                              style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  color: C.primaryInk)),
                        ])
                      else
                        Row(children: [
                          Icon(Icons.visibility_outlined, size: 14, color: muted),
                          const SizedBox(width: 4),
                          Text('${d.i('hit_see')}',
                              style: TextStyle(fontSize: 12, color: muted)),
                          const SizedBox(width: 12),
                          Icon(Icons.download_outlined, size: 14, color: muted),
                          const SizedBox(width: 4),
                          Text('${d.i('hit_download')}',
                              style: TextStyle(fontSize: 12, color: muted)),
                          const Spacer(),
                          const Text('Detail',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  color: C.primaryInk)),
                          const Icon(Icons.chevron_right,
                              size: 16, color: C.primaryInk),
                        ]),
                    ],
                  ),
                ),
              ),
            ]),
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
            // kotak polos terbaca sebagai halaman kosong; pakai kerangka
            // berbentuk kartu yang sama dengan daftar lain di aplikasi
            return const SliverToBoxAdapter(
              child: SkeletonList(count: 3, height: 132, padding: EdgeInsets.zero),
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

  /// Jumlah hasil dari halaman pertama; null selagi dimuat.
  int? _total;

  @override
  void initState() {
    super.initState();
    api.documentFilters(widget.category).then((f) {
      if (mounted) setState(() => _filters = f);
    }).catchError((_) {}); // filter gagal -> daftar tetap jalan
  }

  /// Pil filter: dropdown tanpa garis bawah di dalam kapsul. Menyala oranye
  /// saat aktif supaya terlihat filter mana yang sedang membatasi daftar.
  Widget _dropdown(String hint, String value, String key, void Function(String) set) {
    final opts = _filters.ls(key);
    if (opts.isEmpty) return const SizedBox.shrink();
    final dark = Theme.of(context).brightness == Brightness.dark;
    final on = value.isNotEmpty;
    return Padding(
      padding: const EdgeInsets.only(right: AppSpacing.sm),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
        decoration: BoxDecoration(
          color: on
              ? C.primary.withValues(alpha: .14)
              : (dark ? C.darkSurface : C.lightSurface),
          borderRadius: BorderRadius.circular(999),
          border: Border.all(
              color: on ? C.primary : (dark ? C.darkLine : C.lightLine)),
        ),
        child: DropdownButtonHideUnderline(
          child: DropdownButton<String>(
            hint: Text(hint,
                style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: dark ? C.darkInkMuted : C.lightInkMuted)),
            value: on ? value : null,
            isDense: true,
            borderRadius: BorderRadius.circular(AppRadius.card),
            icon: Icon(Icons.expand_more,
                size: 18, color: on ? C.primaryInk : C.lightInkMuted),
            style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: dark ? C.darkInk : C.lightInk),
            items: [
              // satu filter bisa dilepas tanpa mereset filter yang lain
              DropdownMenuItem(value: '', child: Text('Semua $hint')),
              // nilai tetap apa adanya untuk API; yang di-titleCase hanya
              // tampilannya — "TIDAK BERLAKU" terbaca seperti diteriakkan
              for (final o in opts)
                DropdownMenuItem(
                    value: o,
                    child:
                        Text(titleCase(o), overflow: TextOverflow.ellipsis)),
            ],
            onChanged: (v) => setState(() => set(v ?? '')),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final muted = dark ? C.darkInkMuted : C.lightInkMuted;
    final hasFilter =
        _q.isNotEmpty || _jenis.isNotEmpty || _tahun.isNotEmpty || _status.isNotEmpty;
    return Scaffold(
      appBar: BrandAppBar(docCategoryLabel(widget.category)),
      body: Column(
        children: [
          // bilah alat berlatar surface: pencarian + filter jadi satu blok yang
          // jelas terpisah dari daftar di bawahnya
          Container(
            color: dark ? C.darkSurface : C.lightSurface,
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, AppSpacing.md, AppSpacing.lg, AppSpacing.sm),
            child: Column(children: [
              TextField(
                decoration: const InputDecoration(
                    hintText: 'Cari judul...', prefixIcon: Icon(Icons.search)),
                textInputAction: TextInputAction.search,
                onSubmitted: (v) => setState(() => _q = v),
              ),
              const SizedBox(height: AppSpacing.sm),
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                child: Row(children: [
                  _dropdown('Jenis', _jenis, 'jenis', (v) => _jenis = v),
                  _dropdown('Tahun', _tahun, 'tahun', (v) => _tahun = v),
                  _dropdown('Status', _status, 'status', (v) => _status = v),
                  if (hasFilter)
                    TextButton.icon(
                        onPressed: () => setState(() {
                              _q = '';
                              _jenis = '';
                              _tahun = '';
                              _status = '';
                            }),
                        icon: const Icon(Icons.close, size: 16),
                        label: const Text('Reset')),
                ]),
              ),
              if (_total != null)
                Padding(
                  padding: const EdgeInsets.only(top: AppSpacing.sm),
                  child: Align(
                    alignment: Alignment.centerLeft,
                    child: Text(
                        '$_total dokumen${hasFilter ? ' sesuai filter' : ''}',
                        style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: muted)),
                  ),
                ),
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
              onTotal: (t) => setState(() => _total = t),
              empty: 'Belum ada dokumen pada kategori ini.',
              // kosong karena filter != kosong karena belum ada isinya:
              // "muat ulang" tidak akan mengubah apa pun
              emptyView: !hasFilter
                  ? null
                  : NoResults(
                      // semua filter aktif ditulis apa adanya, jadi jelas
                      // kombinasi mana yang tidak menghasilkan apa-apa
                      [_q, _jenis, _tahun, _status]
                          .where((f) => f.isNotEmpty)
                          .join(' · '),
                      saran: 'Longgarkan filternya atau periksa ejaan kata '
                          'kuncinya.',
                      actions: [
                        FilledButton.icon(
                          onPressed: () => setState(() {
                            _q = '';
                            _jenis = '';
                            _tahun = '';
                            _status = '';
                          }),
                          icon: const Icon(Icons.close, size: 18),
                          label: const Text('Reset filter'),
                        ),
                      ],
                    ),
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
          skeleton: const DocDetailSkeleton(),
          builder: (context, d) {
            final lampiran = d.l('lampiran');
            final utama = lampiran.firstOrNull;
            final dark = Theme.of(context).brightness == Brightness.dark;
            return Column(children: [
              Expanded(
                child: CustomScrollView(slivers: [
                  _heroBar(context, d),
                  SliverToBoxAdapter(
                    // lembar isi menimpa hero 24dp, sama seperti detail berita
                    child: Transform.translate(
                      offset: const Offset(0, -24),
                      child: Container(
                        decoration: BoxDecoration(
                          color: dark ? C.darkSurface : C.lightSurface,
                          borderRadius: const BorderRadius.vertical(
                              top: Radius.circular(AppRadius.sheet)),
                        ),
                        padding: const EdgeInsets.fromLTRB(AppSpacing.lg, 40,
                            AppSpacing.lg, AppSpacing.xl),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: _body(context, d),
                        ),
                      ),
                    ),
                  ),
                ]),
              ),
              _actionBar(context, d, utama),
            ]);
          },
        ),
      );

  Widget _heroBar(BuildContext context, Json d) => SliverAppBar(
        expandedHeight: 200,
        pinned: true,
        stretch: true,
        backgroundColor:
            Theme.of(context).brightness == Brightness.dark
                ? C.darkSurface
                : C.lightSurface,
        surfaceTintColor: Colors.transparent,
        leadingWidth: 64,
        leading: Padding(
          padding: const EdgeInsets.only(left: AppSpacing.lg),
          child: RoundIconButton(Icons.arrow_back,
              tooltip: 'Kembali', onPressed: () => Navigator.pop(context)),
        ),
        flexibleSpace: FlexibleSpaceBar(
          stretchModes: const [StretchMode.zoomBackground],
          background: Stack(fit: StackFit.expand, children: [
            const DecoratedBox(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                    colors: [C.ink, C.inkSoft],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight),
              ),
            ),
            const GeoPattern(color: C.primary, opacity: .18),
            // cap air: lambang + tahun terbit, penanda dokumen resmi
            Positioned(
              right: -12,
              bottom: 4,
              child: Icon(Icons.account_balance,
                  size: 150, color: C.primary.withValues(alpha: .22)),
            ),
            Positioned(
              left: AppSpacing.lg,
              bottom: 44,
              right: AppSpacing.lg,
              child: Row(children: [
                Text(
                    titleCase(d.sn('jenis_peraturan') ??
                            d.sn('singkatan_jenis') ??
                            'Dokumen Hukum')
                        .toUpperCase(),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 2,
                        color: C.goldSoft)),
                const Spacer(),
                Text(d.sn('tahun_terbit') ?? '',
                    style: TextStyle(
                        fontSize: 34,
                        fontWeight: FontWeight.w800,
                        color: C.cream.withValues(alpha: .32))),
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
      Wrap(
        spacing: AppSpacing.sm,
        runSpacing: AppSpacing.sm,
        crossAxisAlignment: WrapCrossAlignment.center,
        children: [
          JenisChip(d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan')),
          StatusBadge(d.sn('status') ?? d.sn('status_terakhir')),
        ],
      ),
      const SizedBox(height: AppSpacing.md),
      // titleCase: judul peraturan di basis data KAPITAL SEMUA
      Text(titleCase(d.s('judul')),
          style: const TextStyle(
              fontSize: 22,
              height: 1.3,
              letterSpacing: -.3,
              fontWeight: FontWeight.w700)),
      const SizedBox(height: AppSpacing.md),
      Wrap(spacing: AppSpacing.sm, runSpacing: 6, children: [
        if (nomor != null) MetaPill(Icons.tag, 'Nomor $nomor'),
        if (tahun != null) MetaPill(Icons.event_outlined, 'Tahun $tahun'),
        if (d.sn('bidang_hukum') != null)
          MetaPill(Icons.gavel, titleCase(d.s('bidang_hukum'))),
      ]),
      const SizedBox(height: AppSpacing.lg),
      // pemisah gradient, signature yang sama dengan detail berita
      Container(
        height: 3,
        width: 56,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(2),
          gradient: const LinearGradient(colors: [C.primary, C.gold]),
        ),
      ),
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
      (
        Icons.category_outlined,
        titleCase(d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan') ?? '-'),
        'Jenis'
      ),
      (Icons.event_outlined, d.sn('tahun_terbit') ?? '-', 'Tahun'),
      (
        Icons.verified_outlined,
        titleCase(d.sn('status') ?? d.sn('status_terakhir') ?? '-'),
        'Status'
      ),
      (Icons.translate, titleCase(d.sn('bahasa') ?? '-'), 'Bahasa'),
    ];
    return [
      const Text('Tentang Dokumen',
          style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
      const SizedBox(height: AppSpacing.md),
      // dua kolom, bukan deret mendatar: empat kartu terbaca sekaligus dan
      // nilai panjang punya ruang dua baris alih-alih dipotong
      LayoutBuilder(
        builder: (context, c) {
          final w = (c.maxWidth - AppSpacing.sm) / 2;
          return Wrap(
            spacing: AppSpacing.sm,
            runSpacing: AppSpacing.sm,
            children: [
              for (final s in specs)
                SizedBox(
                    width: w, height: 100, child: SpecCard(s.$1, s.$2, s.$3)),
            ],
          );
        },
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
      color: Theme.of(context).brightness == Brightness.dark
          ? C.darkSurface
          : C.lightSurface,
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
                          downloadDoc(context, url,
                              title: utama!.sn('judul'));
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

