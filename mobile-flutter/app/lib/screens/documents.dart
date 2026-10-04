import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'doc_view.dart';

const docCategories = [
  (
    slug: 'peraturan',
    label: 'Peraturan & Keputusan',
    short: 'Peraturan',
    icon: Icons.account_balance,
  ),
  (
    slug: 'monografi',
    label: 'Monografi Hukum',
    short: 'Monografi',
    icon: Icons.menu_book,
  ),
  (
    slug: 'artikel',
    label: 'Artikel / Majalah Hukum',
    short: 'Artikel',
    icon: Icons.article,
  ),
  (slug: 'putusan', label: 'Putusan', short: 'Putusan', icon: Icons.gavel),
];

final _angka = NumberFormat.decimalPattern('id');

/// Abstrak dokumen sebagai teks ATAU berkas PDF. Server yang belum memuat
/// field `abstrak_url` mengirim NAMA berkas di kolom `abstrak`
/// ("fEroj….pdf") dan nama itu tampil mentah sebagai teks; di sini ditiru
/// isPdfName + docUrl server (berkas di storage/dokumen/).
({String? teks, String? url}) abstrakDokumen(Json d) {
  final mentah = d.sn('abstrak')?.trim();
  final berkas =
      mentah != null &&
      RegExp(r'^\S+\.pdf$', caseSensitive: false).hasMatch(mentah);
  return (
    teks: berkas ? null : mentah,
    url:
        d.sn('abstrak_url') ??
        (berkas ? '$kBaseUrl/storage/dokumen/$mentah' : null),
  );
}

String docCategoryLabel(String slug) =>
    docCategories.where((c) => c.slug == slug).firstOrNull?.label ?? slug;

class DocumentCard extends StatelessWidget {
  const DocumentCard(this.d, {super.key});
  final Json d;

  @override
  Widget build(BuildContext context) {
    final muted = Theme.of(context).brightness == Brightness.dark
        ? C.darkInkMuted
        : C.lightInkMuted;
    final nomor = d.sn('nomor_peraturan');
    final tahun = d.sn('tahun_terbit');
    final status = d.sn('status') ?? d.sn('status_terakhir');
    // Status cukup dibawa badge berteks. Rail & blok nomor berwarna status
    // dulu mengulang makna yang sama tiga kali dan membuat daftar riuh.
    final pilih = PilihDokumen.maybeOf(context);
    final terpilih = pilih != null && pilih.terpilih == d.i('id');
    return Pressable(
      child: Card(
        clipBehavior: Clip.antiAlias,
        // dokumen yang sedang dibuka di panel kanan: tepi biru di keempat sisi
        shape: terpilih
            ? RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(AppRadius.card),
                side: const BorderSide(color: C.accent, width: 2),
              )
            : null,
        child: InkWell(
          onTap: pilih != null
              ? () => pilih.onPilih(d.i('id'))
              : () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => DocumentDetailScreen(id: d.i('id')),
                  ),
                ),
          child: IntrinsicHeight(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // blok nomor: penanda khas dokumen hukum, sekaligus jangkar
                // visual — netral, satu langkah dari permukaan kartu
                if (nomor != null)
                  Container(
                    width: 56,
                    color: C.lightBg,
                    padding: const EdgeInsets.symmetric(
                      vertical: AppSpacing.md,
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text('No.', style: T.labelKecil.copyWith(color: muted)),
                        Text(
                          nomor,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: T.angka,
                        ),
                        if (tahun != null)
                          Text(
                            tahun,
                            style: T.labelKecil.copyWith(color: muted),
                          ),
                      ],
                    ),
                  ),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.all(AppSpacing.md),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Wrap, bukan Row: status panjang ("Berlaku, diubah
                        // sebagian ...") turun ke baris kedua alih-alih
                        // meluap dari kartu
                        // lebar penuh: Wrap menyusut selebar isinya, dan
                        // tanpa ini spaceBetween tidak berefek
                        SizedBox(
                          width: double.infinity,
                          child: Wrap(
                            alignment: WrapAlignment.spaceBetween,
                            spacing: AppSpacing.sm,
                            runSpacing: 6,
                            children: [
                              JenisChip(
                                d.sn('singkatan_jenis') ??
                                    d.sn('jenis_peraturan'),
                              ),
                              StatusBadge(status),
                            ],
                          ),
                        ),
                        const SizedBox(height: AppSpacing.sm),
                        // titleCase: judul di basis data KAPITAL SEMUA dan
                        // terbaca seperti diteriakkan
                        Text(
                          titleCase(d.s('judul')),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: T.judulItem,
                        ),
                        if (d.sn('bidang_hukum') != null) ...[
                          const SizedBox(height: AppSpacing.sm),
                          MetaPill(Icons.folder_outlined, d.s('bidang_hukum')),
                        ],
                        const SizedBox(height: AppSpacing.sm),
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.end,
                          children: [
                            // Wrap: dengan huruf besar atau angka jutaan,
                            // jumlah unduhan turun ke baris kedua alih-alih
                            // meluap; "Detail" tetap di kanan
                            Expanded(
                              child: Wrap(
                                spacing: 12,
                                runSpacing: 2,
                                children: [
                                  _Hitungan(
                                    Icons.visibility_outlined,
                                    d.i('hit_see'),
                                    'dilihat',
                                    muted,
                                  ),
                                  _Hitungan(
                                    Icons.download_outlined,
                                    d.i('hit_download'),
                                    'diunduh',
                                    muted,
                                  ),
                                ],
                              ),
                            ),
                            // panah saja: seluruh kartu sudah bisa diketuk;
                            // label "Detail" berwarna di tiap kartu hanya
                            // menambah riuh daftar
                            const Icon(
                              Icons.chevron_right,
                              size: 16,
                              color: C.accent,
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

/// Pemilihan dokumen di tata letak daftar+detail. Bila ada di atas sebuah
/// [DocumentCard], ketukan memilih dokumen untuk panel kanan alih-alih
/// membuka halaman baru.
class PilihDokumen extends InheritedWidget {
  const PilihDokumen({
    super.key,
    required this.terpilih,
    required this.onPilih,
    required super.child,
  });
  final int? terpilih;
  final ValueChanged<int> onPilih;

  static PilihDokumen? maybeOf(BuildContext context) =>
      context.dependOnInheritedWidgetOfExactType<PilihDokumen>();

  @override
  bool updateShouldNotify(PilihDokumen old) => old.terpilih != terpilih;
}

/// Jendela >= 840dp: [daftar] di kiri, detail dokumen terpilih di kanan —
/// membandingkan dan menelusuri hasil tanpa bolak-balik halaman. Lebih
/// sempit: [daftar] saja, ketukan membuka halaman detail seperti biasa.
class DaftarDetail extends StatefulWidget {
  const DaftarDetail({super.key, required this.daftar});
  final Widget daftar;

  @override
  State<DaftarDetail> createState() => _DaftarDetailState();
}

class _DaftarDetailState extends State<DaftarDetail> {
  int? _id;

  /// Daftar berpindah induk saat lebar melewati 840dp; kunci global membawa
  /// isian cari, filter, dan posisi gulirnya ikut pindah.
  final _daftar = GlobalKey();

  @override
  Widget build(BuildContext context) => LayoutBuilder(
    builder: (context, box) {
      final daftar = KeyedSubtree(key: _daftar, child: widget.daftar);
      if (box.maxWidth < kLebarLebar) return daftar;
      return Row(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          SizedBox(
            width: 400,
            child: Panel(
              child: PilihDokumen(
                terpilih: _id,
                onPilih: (id) => setState(() => _id = id),
                child: daftar,
              ),
            ),
          ),
          const VerticalDivider(width: 1),
          Expanded(
            child: Panel(
              child: _id == null
                  ? const EmptyState(
                      icon: Icons.description_outlined,
                      title: 'Pilih dokumen',
                      message:
                          'Ketuk dokumen di daftar untuk membaca rinciannya '
                          'di sini, berdampingan dengan hasil lainnya.',
                    )
                  : DocumentDetailScreen(
                      key: ValueKey(_id),
                      id: _id!,
                      panel: true,
                    ),
            ),
          ),
        ],
      );
    },
  );
}

/// Ikon + angka (dilihat/diunduh) pada kartu dokumen. Pembaca layar
/// mendengar "1.234 dilihat", bukan angka tanpa konteks.
class _Hitungan extends StatelessWidget {
  const _Hitungan(this.icon, this.nilai, this.arti, this.warna);
  final IconData icon;
  final int nilai;
  final String arti;
  final Color warna;

  @override
  Widget build(BuildContext context) => Semantics(
    label: '${_angka.format(nilai)} $arti',
    excludeSemantics: true,
    child: Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 14, color: warna),
        const SizedBox(width: 4),
        Flexible(
          child: Text(
            _angka.format(nilai),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: T.isiKecil.copyWith(color: warna),
          ),
        ),
      ],
    ),
  );
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
    body: CustomScrollView(
      slivers: [
        SliverPadding(
          // grid paling lebar kKolomGrid di tengah
          padding: padTengah(context, maks: kKolomGrid, bawah: 0),
          sliver: SliverList.list(
            children: [
              FutureBuilder<Json>(
                future: _stats,
                builder: (context, snap) {
                  final stats =
                      snap.data?.m('statistik') ?? const <String, dynamic>{};
                  // pintu koleksi sudah ada sejak awal; tanpa kemunculan
                  // berurutan, hanya angkanya yang menyusul. Layar lebar:
                  // dua kartu sebaris, bukan empat kartu selebar tablet.
                  final kartu = [
                    for (final c in docCategories)
                      _CategoryCard(
                        category: c,
                        subtitle: _subtitle[c.slug] ?? '',
                        count: snap.hasData ? stats.i(c.slug) : null,
                        memuat: snap.connectionState != ConnectionState.done,
                      ),
                  ];
                  final kolom = math.min(
                    2,
                    kolomUntuk(MediaQuery.sizeOf(context).width),
                  );
                  return Column(
                    children: [
                      for (var i = 0; i < kartu.length; i += kolom)
                        Padding(
                          padding: const EdgeInsets.only(bottom: AppSpacing.md),
                          child: kolom == 1
                              ? kartu[i]
                              : BarisKartu(
                                  kolom: kolom,
                                  children: kartu.sublist(
                                    i,
                                    math.min(i + kolom, kartu.length),
                                  ),
                                ),
                        ),
                    ],
                  );
                },
              ),
              SectionHeader(
                'Produk Hukum Terbaru',
                onSeeAll: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) =>
                        DocumentListScreen(category: docCategories[_tab].slug),
                  ),
                ),
              ),
              FilterPills(
                labels: [for (final c in docCategories) c.short],
                selected: _tab,
                onSelected: (i) => setState(() => _tab = i),
              ),
              const SizedBox(height: AppSpacing.md),
            ],
          ),
        ),
        SliverPadding(
          padding: padTengah(
            context,
            maks: kKolomGrid,
            atas: 0,
            bawah: AppSpacing.xxl,
          ),
          sliver: _LatestDocuments(
            key: ValueKey(_tab),
            category: docCategories[_tab].slug,
          ),
        ),
      ],
    ),
  );
}

class _CategoryCard extends StatelessWidget {
  const _CategoryCard({
    required this.category,
    required this.subtitle,
    required this.count,
    required this.memuat,
  });
  final ({String slug, String label, String short, IconData icon}) category;
  final String subtitle;

  /// null selagi dimuat, atau bila gagal dimuat.
  final int? count;

  /// Gagal memuat jumlah = barisnya disembunyikan, bukan kerangka abadi.
  final bool memuat;

  @override
  Widget build(BuildContext context) => Pressable(
    child: Card(
      child: InkWell(
        borderRadius: BorderRadius.circular(AppRadius.card),
        onTap: () => Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => DocumentListScreen(category: category.slug),
          ),
        ),
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Row(
            children: [
              // satu tint biru untuk keempat koleksi: warna berbeda per kartu
              // dulu hanya hiasan, tidak membawa makna
              IconTile(category.icon, size: 52),
              const SizedBox(width: AppSpacing.lg),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(category.label, style: T.subjudul),
                    const SizedBox(height: 2),
                    Text(
                      subtitle,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: T.isiKecil.copyWith(color: C.lightInkMuted),
                    ),
                    if (count != null || memuat)
                      const SizedBox(height: AppSpacing.sm),
                    if (count == null && memuat)
                      const Skeletonizer.zone(
                        child: Bone(width: 120, height: 12, uniRadius: 6),
                      )
                    else if (count != null)
                      Text(
                        '$count dokumen tersedia',
                        style: T.labelKecil.copyWith(
                          color: C.accent,
                          fontFeatures: [FontFeature.tabularFigures()],
                        ),
                      ),
                  ],
                ),
              ),
              const Icon(Icons.chevron_right, color: C.lightInkMuted),
            ],
          ),
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
  late Future<Paginated> _future = api.documents(category: widget.category);

  @override
  Widget build(BuildContext context) => FutureBuilder<Paginated>(
    future: _future,
    builder: (context, snap) {
      if (snap.hasError) {
        return SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: AppSpacing.xl),
            child: Column(
              children: [
                Text(
                  '${snap.error}',
                  textAlign: TextAlign.center,
                  style: const TextStyle(color: C.lightInkMuted),
                ),
                TextButton.icon(
                  // blok, bukan arrow: callback setState tak boleh
                  // mengembalikan Future
                  onPressed: () => setState(() {
                    _future = api.documents(category: widget.category);
                  }),
                  icon: const Icon(Icons.refresh, size: 18),
                  label: const Text('Coba lagi'),
                ),
              ],
            ),
          ),
        );
      }
      if (!snap.hasData) {
        // kartu asli berisi data contoh: kerangka = bentuk isinya
        return SliverToBoxAdapter(
          child: Skeletonizer(
            child: Column(
              children: [
                for (var i = 0; i < 3; i++)
                  const Padding(
                    padding: EdgeInsets.only(bottom: AppSpacing.md),
                    child: DocumentCard(kSkeletonItem),
                  ),
              ],
            ),
          ),
        );
      }
      final items = snap.data!.items.take(5).toList();
      final kolom = kolomUntuk(MediaQuery.sizeOf(context).width);
      final baris = (items.length / kolom).ceil();
      return SliverList.separated(
        itemCount: baris,
        separatorBuilder: (_, _) => const SizedBox(height: AppSpacing.md),
        itemBuilder: (_, i) => kolom == 1
            ? DocumentCard(items[i])
            : BarisKartu(
                kolom: kolom,
                children: [
                  for (
                    var j = i * kolom;
                    j < math.min((i + 1) * kolom, items.length);
                    j++
                  )
                    DocumentCard(items[j]),
                ],
              ),
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
  String _q = '', _jenis = '', _tahun = '', _status = '', _nomor = '';
  Json _filters = {};
  final _qCtrl = TextEditingController(), _nomorCtrl = TextEditingController();

  /// Jumlah hasil dari halaman pertama; null selagi dimuat.
  int? _total;

  @override
  void initState() {
    super.initState();
    api
        .documentFilters(widget.category)
        .then((f) {
          if (mounted) setState(() => _filters = f);
        })
        .catchError((_) {}); // filter gagal -> daftar tetap jalan
  }

  @override
  void dispose() {
    _qCtrl.dispose();
    _nomorCtrl.dispose();
    super.dispose();
  }

  // controller ikut dikosongkan: tanpa itu teks lama tetap tampil di kolom
  // padahal filternya sudah dilepas
  void _reset() => setState(() {
    _q = _jenis = _tahun = _status = _nomor = '';
    _qCtrl.clear();
    _nomorCtrl.clear();
  });

  /// Filter: dropdown tanpa garis bawah di dalam kotak bersudut 4dp. Menyala oranye
  /// saat aktif supaya terlihat filter mana yang sedang membatasi daftar.
  Widget _dropdown(
    String hint,
    String value,
    String key,
    void Function(String) set,
  ) {
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
              ? C.primary.withValues(alpha: .16)
              : (dark ? C.darkSurface : C.lightSurface),
          borderRadius: BorderRadius.circular(AppRadius.chip),
          border: Border.all(
            color: on
                ? C.primary.withValues(alpha: .55)
                : (dark ? C.darkLine : C.lightLine),
          ),
        ),
        child: DropdownButtonHideUnderline(
          child: DropdownButton<String>(
            hint: Text(
              hint,
              style: T.label.copyWith(
                color: dark ? C.darkInkMuted : C.lightInkMuted,
              ),
            ),
            value: on ? value : null,
            // tidak isDense: tinggi tombol 48dp = target sentuh minimum
            // (dulu ±26dp), sejajar kolom cari di atasnya
            borderRadius: BorderRadius.circular(AppRadius.card),
            icon: Icon(
              Icons.expand_more,
              size: 18,
              color: on ? C.primaryInk : C.lightInkMuted,
            ),
            style: T.label.copyWith(color: dark ? C.darkInk : C.lightInk),
            items: [
              // satu filter bisa dilepas tanpa mereset filter yang lain
              DropdownMenuItem(value: '', child: Text('Semua $hint')),
              // nilai tetap apa adanya untuk API; yang di-titleCase hanya
              // tampilannya — "TIDAK BERLAKU" terbaca seperti diteriakkan
              for (final o in opts)
                DropdownMenuItem(
                  value: o,
                  child: Text(titleCase(o), overflow: TextOverflow.ellipsis),
                ),
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
    final hasFilter = [
      _q,
      _jenis,
      _tahun,
      _status,
      _nomor,
    ].any((f) => f.isNotEmpty);
    return Scaffold(
      appBar: BrandAppBar(docCategoryLabel(widget.category)),
      // jendela lebar: daftar + detail berdampingan
      body: DaftarDetail(
        daftar: Column(
          children: [
            // bilah alat berlatar surface: pencarian + filter jadi satu blok yang
            // jelas terpisah dari daftar di bawahnya
            Container(
              color: dark ? C.darkSurface : C.lightSurface,
              padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg,
                AppSpacing.md,
                AppSpacing.lg,
                AppSpacing.sm,
              ),
              child: Column(
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: TextField(
                          controller: _qCtrl,
                          decoration: const InputDecoration(
                            hintText: 'Cari judul...',
                            prefixIcon: Icon(Icons.search),
                          ),
                          textInputAction: TextInputAction.search,
                          onSubmitted: (v) => setState(() => _q = v.trim()),
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      // nomor diketik, bukan dipilih: ratusan nomor tak muat di
                      // dropdown. Keyboard teks karena nomor keputusan memuat "/" & "."
                      SizedBox(
                        width: 112,
                        child: TextField(
                          controller: _nomorCtrl,
                          decoration: const InputDecoration(
                            hintText: 'Nomor',
                            prefixIcon: Icon(Icons.tag),
                          ),
                          textInputAction: TextInputAction.search,
                          onSubmitted: (v) => setState(() => _nomor = v.trim()),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.sm),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: Row(
                      children: [
                        _dropdown('Jenis', _jenis, 'jenis', (v) => _jenis = v),
                        _dropdown('Tahun', _tahun, 'tahun', (v) => _tahun = v),
                        _dropdown(
                          'Status',
                          _status,
                          'status',
                          (v) => _status = v,
                        ),
                        if (hasFilter)
                          TextButton.icon(
                            onPressed: _reset,
                            icon: const Icon(Icons.close, size: 16),
                            label: const Text('Reset'),
                          ),
                      ],
                    ),
                  ),
                  if (_total != null)
                    Padding(
                      padding: const EdgeInsets.only(top: AppSpacing.sm),
                      child: Align(
                        alignment: Alignment.centerLeft,
                        child: Text(
                          '$_total dokumen${hasFilter ? ' sesuai filter' : ''}',
                          style: T.labelKecil.copyWith(color: muted),
                        ),
                      ),
                    ),
                ],
              ),
            ),
            Expanded(
              child: PagedListView(
                key: ValueKey(
                  '${widget.category}|$_q|$_jenis|$_tahun|$_status|$_nomor',
                ),
                fetch: (page) => api.documents(
                  category: widget.category,
                  q: _q,
                  jenis: _jenis,
                  tahun: _tahun,
                  status: _status,
                  nomor: _nomor,
                  page: page,
                ),
                onTotal: (t) => setState(() => _total = t),
                empty: 'Belum ada dokumen pada kategori ini.',
                // kosong karena filter != kosong karena belum ada isinya:
                // "muat ulang" tidak akan mengubah apa pun
                emptyView: !hasFilter
                    ? null
                    : NoResults(
                        // semua filter aktif ditulis apa adanya, jadi jelas
                        // kombinasi mana yang tidak menghasilkan apa-apa
                        [
                          _q,
                          if (_nomor.isNotEmpty) 'No. $_nomor',
                          _jenis,
                          _tahun,
                          _status,
                        ].where((f) => f.isNotEmpty).join(' · '),
                        saran:
                            'Longgarkan filternya atau periksa ejaan kata '
                            'kuncinya.',
                        actions: [
                          FilledButton.icon(
                            onPressed: _reset,
                            icon: const Icon(Icons.close, size: 18),
                            label: const Text('Reset filter'),
                          ),
                        ],
                      ),
                itemBuilder: (_, d, _) => DocumentCard(d),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// Detail dokumen: hero color-block, tab Tentang/Berkas/Terkait,
/// dan bilah aksi tetap di bawah (cermin layout referensi).
class DocumentDetailScreen extends StatefulWidget {
  const DocumentDetailScreen({super.key, required this.id, this.panel = false});
  final int id;

  /// Tampil sebagai panel kanan [DaftarDetail]: tanpa tombol kembali, karena
  /// halaman ini bukan rute tersendiri.
  final bool panel;

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
        return Column(
          children: [
            Expanded(
              child: CustomScrollView(
                slivers: [
                  _heroBar(context, d),
                  SliverToBoxAdapter(
                    // lembar isi menimpa hero 24dp, sama seperti detail berita
                    child: Transform.translate(
                      offset: const Offset(0, -24),
                      child: Container(
                        decoration: BoxDecoration(
                          color: dark ? C.darkSurface : C.lightSurface,
                          borderRadius: const BorderRadius.vertical(
                            top: Radius.circular(AppRadius.sheet),
                          ),
                        ),
                        padding: const EdgeInsets.fromLTRB(
                          AppSpacing.lg,
                          40,
                          AppSpacing.lg,
                          AppSpacing.xl,
                        ),
                        // tablet: kolom baca di tengah, bukan baris selebar layar
                        child: Kolom(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: _body(context, d),
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            _actionBar(context, d, utama),
          ],
        );
      },
    ),
  );

  Widget _heroBar(BuildContext context, Json d) => SliverAppBar(
    expandedHeight: 136,
    pinned: true,
    stretch: true,
    backgroundColor: Theme.of(context).brightness == Brightness.dark
        ? C.darkSurface
        : C.lightSurface,
    surfaceTintColor: Colors.transparent,
    // tersemat di atas isi yang digulir: tanpa garis, judul tampak terpotong
    // oleh bar putih yang menyatu dengan lembar putih di bawahnya
    shape: Border(bottom: BorderSide(color: Theme.of(context).dividerColor)),
    leadingWidth: 64,
    automaticallyImplyLeading: false,
    leading: widget.panel
        ? null
        : Padding(
            padding: const EdgeInsets.only(left: AppSpacing.lg),
            child: RoundIconButton(
              Icons.arrow_back,
              tooltip: 'Kembali',
              onPressed: () => Navigator.pop(context),
            ),
          ),
    actions: [ShareAction(d)],
    flexibleSpace: FlexibleSpaceBar(
      stretchModes: const [StretchMode.zoomBackground],
      // Pita biru polos: wilayah identitas "dokumen resmi", gema globe biru
      // pada logo. Tanpa cap air, tahun bayangan, atau label kapital — jenis,
      // tahun, dan status sudah dibawa chip & pil tepat di bawahnya.
      background: const ColoredBox(color: C.accentDeep),
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
      Text(titleCase(d.s('judul')), style: T.judulDetail),
      const SizedBox(height: AppSpacing.md),
      Wrap(
        spacing: AppSpacing.sm,
        runSpacing: 6,
        children: [
          if (nomor != null) MetaPill(Icons.tag, 'Nomor $nomor'),
          if (tahun != null) MetaPill(Icons.event_outlined, 'Tahun $tahun'),
          if (d.sn('bidang_hukum') != null)
            MetaPill(Icons.gavel, titleCase(d.s('bidang_hukum'))),
        ],
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
    final (teks: abstrak, url: abstrakUrl) = abstrakDokumen(d);
    final subjek = d.ls('subjek');
    final pengarang = d.l('pengarang');
    final specs = <(IconData, String, String)>[
      (
        Icons.category_outlined,
        titleCase(d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan') ?? '-'),
        'Jenis',
      ),
      (Icons.event_outlined, d.sn('tahun_terbit') ?? '-', 'Tahun'),
      (
        Icons.verified_outlined,
        titleCase(d.sn('status') ?? d.sn('status_terakhir') ?? '-'),
        'Status',
      ),
      (Icons.translate, titleCase(d.sn('bahasa') ?? '-'), 'Bahasa'),
    ];
    return [
      const Text('Tentang Dokumen', style: T.subjudul),
      const SizedBox(height: AppSpacing.md),
      // dua kolom, bukan deret mendatar: empat kartu terbaca sekaligus dan
      // nilai panjang punya ruang dua baris alih-alih dipotong. Tinggi per
      // baris mengikuti isi tertinggi (dulu tinggi tetap: ruang kosong di
      // bawah nilai satu baris)
      for (var i = 0; i < specs.length; i += 2) ...[
        if (i > 0) const SizedBox(height: AppSpacing.md),
        BarisKartu(
          kolom: 2,
          children: [
            for (final s in specs.skip(i).take(2)) SpecCard(s.$1, s.$2, s.$3),
          ],
        ),
      ],
      if (abstrak != null || abstrakUrl != null) ...[
        const SizedBox(height: AppSpacing.lg),
        const Text('Abstrak', style: T.subjudul),
        if (abstrak != null) ReadMore(abstrak, html: true),
        // dokumen lama menyimpan abstraknya sebagai berkas PDF, bukan teks
        if (abstrakUrl != null) ...[
          const SizedBox(height: AppSpacing.sm),
          DocFileTile(
            abstrakUrl,
            title: 'Abstrak ${titleCase(d.s('judul'))}',
            onOpen: () => api.documentDownload(widget.id).ignore(),
          ),
        ],
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
          children: [for (final s in subjek) JenisChip(s)],
        ),
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
          message:
              'Dokumen ini belum memiliki file yang bisa dilihat '
              'atau diunduh. Coba periksa peraturan terkait.',
        ),
      ];
    }
    return [
      for (final l in lampiran)
        if (l.sn('url') != null)
          Padding(
            padding: const EdgeInsets.only(bottom: AppSpacing.md),
            child: DocFileTile(
              l.s('url'),
              title: l.sn('judul') ?? 'Dokumen',
              onOpen: () => api.documentDownload(widget.id).ignore(),
            ),
          ),
    ];
  }

  List<Widget> _terkaitTab(BuildContext context, List<Json> terkait) {
    if (terkait.isEmpty) {
      return const [
        EmptyState(
          icon: Icons.link_off,
          title: 'Tidak ada peraturan terkait',
          message:
              'Dokumen ini berdiri sendiri: tidak mengubah, '
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
              leading: const IconTile(Icons.account_balance, size: 40),
              title: Text(
                t.s('judul'),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
              trailing: const Icon(Icons.chevron_right),
              onTap: () => Navigator.push(
                context,
                MaterialPageRoute(
                  builder: (_) => DocumentDetailScreen(id: t.i('id')),
                ),
              ),
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
        // tombol utama tidak melar selebar tablet
        child: Kolom(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(
              AppSpacing.lg,
              AppSpacing.md,
              AppSpacing.lg,
              AppSpacing.md,
            ),
            child: Row(
              children: [
                // Flexible: angka jutaan atau huruf besar menyusut/elipsis,
                // tombol utama tetap mendapat dua pertiga lebar
                Flexible(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        'Statistik',
                        maxLines: 1,
                        style: T.isiKecil.copyWith(color: C.lightInkMuted),
                      ),
                      Text(
                        '${_angka.format(st.i('dilihat'))} dilihat · '
                        '${_angka.format(st.i('diunduh'))} unduh',
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: T.label.copyWith(fontWeight: FontWeight.w700),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  flex: 2,
                  child: FilledButton.icon(
                    icon: Icon(
                      url == null
                          ? Icons.block
                          : (isPdfUrl(url)
                                ? Icons.menu_book_outlined
                                : Icons.download_outlined),
                    ),
                    label: Text(
                      url == null
                          ? 'Berkas tidak tersedia'
                          : (isPdfUrl(url) ? 'Lihat Dokumen' : 'Unduh Dokumen'),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
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
                                    title: utama!.sn('judul') ?? 'Dokumen',
                                  ),
                                ),
                              );
                            } else {
                              downloadDoc(
                                context,
                                url,
                                title: utama!.sn('judul'),
                              );
                            }
                          },
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
