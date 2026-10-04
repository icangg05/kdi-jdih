import 'dart:math' as math;

import 'package:flutter/material.dart';
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
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(
      'JDIH Kota Kendari',
      showBack: false,
      leading: const Padding(
        padding: EdgeInsets.only(left: AppSpacing.md),
        child: Center(
          child: Image(
            image: AssetImage('assets/img/logo-jdihn.png'),
            width: 36,
            semanticLabel: 'Logo JDIHN',
          ),
        ),
      ),
      actions: [
        ListenableBuilder(
          listenable: langNotifier,
          builder: (context, _) => Padding(
            padding: const EdgeInsets.only(right: AppSpacing.lg),
            child: Center(child: LangPill(onTap: () => pickLang(context))),
          ),
        ),
      ],
    ),
    body: LoadView(
      load: api.home,
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
          const _AdatBanner(),
          const SizedBox(height: AppSpacing.md),
          const _HomeSearchBar(),
          const SizedBox(height: AppSpacing.lg),
          _CategoryTiles(stats),
          const SizedBox(height: AppSpacing.xl),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
            child: AiPromoCard(
              onTap: () => _push(
                context,
                const SearchScreen(showBack: true, initialAi: true),
              ),
            ),
          ),
        ];
        final dokumen = <Widget>[
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
            child: SectionHeader(
              'Dokumen Terbaru',
              onSeeAll: () => _push(
                context,
                const DocumentListScreen(category: 'peraturan'),
              ),
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
                'Kabar JDIH',
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
                separatorBuilder: (_, _) =>
                    const SizedBox(width: AppSpacing.md),
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
              'Statistik Koleksi',
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
    ),
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
          const Image(
            image: AssetImage('assets/img/logo-kendari.png'),
            width: 44,
            semanticLabel: 'Lambang Kota Kendari',
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
                  'Siapa yang menghargai adat, ia akan dihormati',
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
        future: _future,
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
    _push(
      context,
      SearchScreen(
        showBack: true,
        initialAi: ai,
        initialQuery: _ctrl.text.trim(),
      ),
    );
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
    child: TextField(
      controller: _ctrl,
      textInputAction: TextInputAction.search,
      onSubmitted: (_) => _go(),
      decoration: InputDecoration(
        hintText: 'Cari produk hukum atau tanya AI...',
        prefixIcon: const Icon(Icons.search),
        suffixIcon: IconButton(
          tooltip: 'Tanya AI',
          icon: const Icon(Icons.auto_awesome, color: C.primaryInk),
          onPressed: () => _go(ai: true),
        ),
        fillColor: C.lightSurface,
      ),
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

  Widget _tile(
    BuildContext context,
    ({String slug, String label, String short, IconData icon}) c,
  ) => Pressable(
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
                c.short,
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
