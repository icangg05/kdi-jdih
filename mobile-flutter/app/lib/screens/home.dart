import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';
import 'kabar.dart';
import 'search.dart';
import 'statistik.dart';

void _push(BuildContext context, Widget page) =>
    Navigator.push(context, MaterialPageRoute(builder: (_) => page));

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) => AnnotatedRegion<SystemUiOverlayStyle>(
    // ikon bilah status putih di atas biru
    value: SystemUiOverlayStyle.light.copyWith(
      statusBarColor: Colors.transparent,
    ),
    child: Scaffold(
      body: Column(
        children: [
          // bilah status selalu biru: kepala beranda ikut tergulir, dan ikon
          // status putih tidak boleh jatuh di atas kanvas terang
          ColoredBox(
            color: C.accent,
            child: SizedBox(
              height: MediaQuery.paddingOf(context).top,
              width: double.infinity,
            ),
          ),
          Expanded(
            child: MediaQuery.removePadding(
              context: context,
              removeTop: true,
              // kepala di luar LoadView: tetap tampil selagi isi dimuat/gagal
              child: NestedScrollView(
                headerSliverBuilder: (_, _) => const [
                  SliverToBoxAdapter(child: _KepalaBeranda()),
                ],
                body: _isi(),
              ),
            ),
          ),
        ],
      ),
    ),
  );

  Widget _isi() => LoadView(
    load: api.home,
    contoh: const {
      'statistik': {
        'peraturan': 1234,
        'monografi': 56,
        'artikel': 78,
        'putusan': 9,
      },
      'berita': [kSkeletonItem, kSkeletonItem, kSkeletonItem],
      'peraturan_terbaru': [kSkeletonItem, kSkeletonItem, kSkeletonItem],
    },
    builder: (context, d) {
      final stats = d.m('statistik');
      // dipasangkan eksplisit dengan jenisnya, bukan menebak dari
      // ada-tidaknya field 'tag'
      final kabar = [
        for (final b in d.l('berita')) (item: b, pengumuman: false),
        for (final p in d.l('pengumuman')) (item: p, pengumuman: true),
      ];
      final pintu = <Widget>[
        const SizedBox(height: AppSpacing.md),
        _CategoryTiles(stats),
        const SizedBox(height: AppSpacing.lg),
        const _AdatBanner(),
        const SizedBox(height: AppSpacing.xl),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
          child: AiPromoCard(
            onTap: () => Navigator.push(
              context,
              ruteNaik(const SearchScreen(showBack: true, initialAi: true)),
            ),
          ),
        ),
      ];
      final dokumen = <Widget>[
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
          child: SectionHeader(
            context.l10n.latestDocuments,
            onSeeAll: () =>
                _push(context, const DocumentListScreen(category: 'peraturan')),
          ),
        ),
        for (final p in d.l('peraturan_terbaru'))
          Padding(
            padding: const EdgeInsets.fromLTRB(
              AppSpacing.lg,
              0,
              AppSpacing.lg,
              AppSpacing.md,
            ),
            child: DocumentCard(p),
          ),
        if (d['monografi_highlight'] != null)
          Padding(
            padding: const EdgeInsets.fromLTRB(
              AppSpacing.lg,
              0,
              AppSpacing.lg,
              AppSpacing.md,
            ),
            child: DocumentCard(d.m('monografi_highlight')),
          ),
      ];
      final berita = <Widget>[
        if (kabar.isNotEmpty) ...[
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
            child: SectionHeader(
              context.l10n.jdihNews,
              onSeeAll: () =>
                  _push(context, const KabarHubScreen(showBack: true)),
            ),
          ),
          SizedBox(
            // ~52dp dari 244 adalah teks: bagian itu ikut skala huruf
            height: 244 + 52 * (skalaTeks(context) - 1),
            child: ListView.separated(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
              itemCount: kabar.length,
              separatorBuilder: (_, _) => const SizedBox(width: AppSpacing.md),
              itemBuilder: (_, i) {
                final (:item, :pengumuman) = kabar[i];
                return NewsTile(
                  item,
                  width: 224,
                  onTap: () => _push(
                    context,
                    pengumuman
                        ? PengumumanDetailScreen(id: item.i('id'))
                        : BeritaDetailScreen(id: item.i('id')),
                  ),
                );
              },
            ),
          ),
        ],
      ];
      final statistik = <Widget>[
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
          child: SectionHeader(
            context.l10n.collectionStats,
            onSeeAll: () => _push(context, const StatistikScreen()),
          ),
        ),
        const _HomeStats(),
      ];
      final lebar = MediaQuery.sizeOf(context).width;
      // Jendela >= 840dp: dua kolom — pintu masuk & statistik di kiri,
      // yang baru (dokumen, kabar) di kanan — bukan satu kolom HP melar.
      // Lebih sempit: satu kolom, paling lebar kKolomBaca di tengah.
      if (lebar >= kLebarLebar) {
        return ListView(
          padding: EdgeInsets.symmetric(
            horizontal: math.max(0, (lebar - kKolomGrid) / 2),
          ).copyWith(bottom: AppSpacing.xxl),
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(child: Column(children: [...pintu, ...statistik])),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [...dokumen, ...berita],
                  ),
                ),
              ],
            ),
          ],
        );
      }
      return ListView(
        padding: EdgeInsets.symmetric(
          horizontal: math.max(0, (lebar - kKolomBaca) / 2),
        ).copyWith(bottom: AppSpacing.xxl),
        children: [...pintu, ...dokumen, ...berita, ...statistik],
      );
    },
  );
}

/// Kepala beranda: pola biru, sapaan, judul besar, lalu kartu cari putih
/// yang menimpa tepi bawah biru — komposisi dari desain acuan.
class _KepalaBeranda extends StatelessWidget {
  const _KepalaBeranda();

  /// Bagian bawah kartu cari yang duduk di kanvas terang.
  static const _timpa = 44.0;

  static String _sapaan(AppLocalizations l) {
    final jam = DateTime.now().hour;
    if (jam < 11) return l.greetMorning;
    if (jam < 15) return l.greetMidday;
    if (jam < 18) return l.greetAfternoon;
    return l.greetEvening;
  }

  @override
  Widget build(BuildContext context) => Stack(
    children: [
      const Positioned.fill(bottom: _timpa, child: PolaBiru()),
      Kolom(
        child: Padding(
          padding: const EdgeInsets.fromLTRB(
            AppSpacing.lg,
            AppSpacing.sm,
            AppSpacing.lg,
            0,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  // logo berwarna butuh alas putih di atas biru
                  Container(
                    padding: const EdgeInsets.all(4),
                    decoration: BoxDecoration(
                      color: C.lightSurface,
                      borderRadius: BorderRadius.circular(AppRadius.chip),
                    ),
                    child: Image(
                      image: AssetImage('assets/img/logo-jdihn.png'),
                      width: 30,
                      semanticLabel: context.l10n.logoJdihn,
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Expanded(
                    child: Text(
                      context.l10n.appName,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: T.labelBesar.copyWith(color: C.onDark),
                    ),
                  ),
                  ListenableBuilder(
                    listenable: langNotifier,
                    builder: (context, _) =>
                        LangPill(gelap: true, onTap: () => pickLang(context)),
                  ),
                ],
              ),
              const SizedBox(height: AppSpacing.xl),
              Text(
                _sapaan(context.l10n),
                style: T.isi.copyWith(color: C.onDark.withValues(alpha: .8)),
              ),
              const SizedBox(height: 2),
              Text(
                context.l10n.homeHeadline,
                style: T.hero.copyWith(color: C.onDark),
              ),
              const SizedBox(height: AppSpacing.xl),
              const _HomeSearchBar(),
            ],
          ),
        ),
      ),
    ],
  );
}

/// Lambang Kota Kendari + semboyan adat Tolaki: identitas daerah yang
/// dibawa dari aplikasi versi pertama.
class _AdatBanner extends StatelessWidget {
  const _AdatBanner();

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
    // permukaan terang: blok gelap di beranda disisakan untuk ajakan AI
    child: Container(
      decoration: BoxDecoration(
        color: C.lightSurface,
        borderRadius: BorderRadius.circular(AppRadius.card),
        border: Border.all(color: C.lightLine),
      ),
      padding: const EdgeInsets.all(AppSpacing.md),
      child: Row(
        children: [
          Image(
            image: AssetImage('assets/img/logo-kendari.png'),
            width: 44,
            semanticLabel: context.l10n.kendariEmblem,
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  "Inae konasara ie'e pinesara inae lia",
                  style: T.subjudul.copyWith(
                    color: C.accent,
                    fontStyle: FontStyle.italic,
                  ),
                ),
                SizedBox(height: 2),
                Text(
                  context.l10n.adatMeaning,
                  style: T.isiKecil.copyWith(color: C.lightInkMuted),
                ),
              ],
            ),
          ),
        ],
      ),
    ),
  );
}

/// Ringkasan statistik di beranda: angka total + status keberlakuan.
/// Pelengkap, jadi gagal = senyap; grafik lengkap ada di StatistikScreen.
class _HomeStats extends StatefulWidget {
  const _HomeStats();

  @override
  State<_HomeStats> createState() => _HomeStatsState();
}

class _HomeStatsState extends State<_HomeStats>
    with AutomaticKeepAliveClientMixin {
  // keep-alive: anak ListView dibuang saat tergulir keluar layar, dan tanpa
  // ini statistik diambil ulang setiap kali kembali terlihat
  late final Future<Json> _future = api.statistics();

  @override
  bool get wantKeepAlive => true;

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
      child: FutureBuilder<Json>(
        // di dalam kerangka muat beranda: ikut jadi kerangka tanpa memanggil
        // API, sebab beranda aslinya akan membangun _HomeStats baru
        future: Skeletonizer.maybeOf(context)?.enabled == true ? null : _future,
        builder: (context, snap) {
          if (snap.hasError) return const SizedBox.shrink();
          final d = snap.data;
          return Skeletonizer(
            enabled: d == null,
            child: Column(
              children: [
                RingkasanCard(
                  total: d?.i('total_dokumen') ?? 1234,
                  dilihat: d?.i('total_views') ?? 12345,
                  diunduh: d?.i('total_downloads') ?? 1234,
                ),
                const SizedBox(height: AppSpacing.md),
                StatusChart(
                  d == null
                      ? const {StatusKind.berlaku: 3, StatusKind.dicabut: 1}
                      : statusBuckets(d.l('dokumen_per_status')),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}

/// Satu kolom cari yang mengantar ke dua mode: teks dan Tanya AI.
class _HomeSearchBar extends StatefulWidget {
  const _HomeSearchBar();

  @override
  State<_HomeSearchBar> createState() => _HomeSearchBarState();
}

class _HomeSearchBarState extends State<_HomeSearchBar> {
  final _ctrl = TextEditingController();

  void _go({bool ai = false}) {
    FocusScope.of(context).unfocus();
    final cari = SearchScreen(
      showBack: true,
      initialAi: ai,
      initialQuery: _ctrl.text.trim(),
    );
    Navigator.push(
      context,
      ai ? ruteNaik(cari) : MaterialPageRoute(builder: (_) => cari),
    );
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(AppSpacing.md),
    decoration: BoxDecoration(
      color: C.lightSurface,
      borderRadius: BorderRadius.circular(AppRadius.card),
      boxShadow: [
        BoxShadow(
          color: C.accentNight.withValues(alpha: .18),
          blurRadius: 24,
          offset: const Offset(0, 8),
        ),
      ],
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        TextField(
          controller: _ctrl,
          textInputAction: TextInputAction.search,
          onSubmitted: (_) => _go(),
          decoration: InputDecoration(
            hintText: context.l10n.homeSearchHint,
            prefixIcon: Icon(Icons.search),
          ),
        ),
        const SizedBox(height: AppSpacing.md),
        // dua pintu dari satu kolom: cari teks atau tanya AI
        Row(
          children: [
            Expanded(
              child: OutlinedButton.icon(
                onPressed: _go,
                icon: const Icon(Icons.search, size: 18),
                label: Text(context.l10n.search),
              ),
            ),
            const SizedBox(width: AppSpacing.sm),
            Expanded(
              child: FilledButton.icon(
                onPressed: () => _go(ai: true),
                icon: const Icon(Icons.auto_awesome, size: 18),
                label: Text(context.l10n.navAskAi),
              ),
            ),
          ],
        ),
      ],
    ),
  );
}

/// Empat pintu masuk kategori dokumen + jumlah koleksinya.
class _CategoryTiles extends StatelessWidget {
  const _CategoryTiles(this.stats);
  final Json stats;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
    // empat sebaris; huruf besar -> dua kolom, supaya nama kategori tetap
    // terbaca utuh alih-alih "Perat…"
    child: LayoutBuilder(
      builder: (context, box) {
        final kolom = skalaTeks(context) > 1.3 ? 2 : 4;
        final lebar = (box.maxWidth - AppSpacing.sm * (kolom - 1)) / kolom;
        return Wrap(
          spacing: AppSpacing.sm,
          runSpacing: AppSpacing.sm,
          children: [
            for (final c in docCategories)
              SizedBox(width: lebar, child: _tile(context, c)),
          ],
        );
      },
    ),
  );

  Widget _tile(BuildContext context, ({String slug, IconData icon}) c) =>
      Pressable(
        child: Material(
          color: C.lightSurface,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.card),
            side: const BorderSide(color: C.lightLine),
          ),
          child: InkWell(
            borderRadius: BorderRadius.circular(AppRadius.card),
            onTap: () => _push(context, DocumentListScreen(category: c.slug)),
            child: Padding(
              padding: const EdgeInsets.symmetric(
                vertical: AppSpacing.md,
                horizontal: 4,
              ),
              child: Column(
                children: [
                  Icon(c.icon, size: 22, color: C.accent),
                  const SizedBox(height: 6),
                  Text(
                    docCategoryText(context, c.slug).short,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: T.label.copyWith(fontWeight: FontWeight.w700),
                  ),
                  Text(
                    '${stats.i(c.slug)}',
                    style: T.isiKecil.copyWith(
                      color: C.lightInkMuted,
                      fontFeatures: [FontFeature.tabularFigures()],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      );
}
