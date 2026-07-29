import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';
import 'kabar.dart';
import 'search.dart';

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
                child: IconSquircle(Icons.account_balance, size: 36)),
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
            return ListView(
              padding: const EdgeInsets.only(bottom: AppSpacing.xxl),
              children: [
                const SizedBox(height: AppSpacing.md),
                const _HomeSearchBar(),
                const SizedBox(height: AppSpacing.lg),
                _CategoryTiles(stats),
                const SizedBox(height: AppSpacing.xl),
                Padding(
                  padding:
                      const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
                  child: AiPromoCard(
                    onTap: () => _push(context,
                        const SearchScreen(showBack: true, initialAi: true)),
                  ),
                ),
                Padding(
                  padding:
                      const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
                  child: SectionHeader('Dokumen Terbaru',
                      onSeeAll: () => _push(context,
                          const DocumentListScreen(category: 'peraturan'))),
                ),
                for (final (i, p) in d.l('peraturan_terbaru').indexed)
                  Padding(
                    padding: const EdgeInsets.fromLTRB(
                        AppSpacing.lg, 0, AppSpacing.lg, AppSpacing.md),
                    child: Rise(delayMs: i * 45, child: DocumentCard(p)),
                  ),
                if (d['monografi_highlight'] != null)
                  Padding(
                    padding: const EdgeInsets.fromLTRB(
                        AppSpacing.lg, 0, AppSpacing.lg, AppSpacing.md),
                    child: DocumentCard(d.m('monografi_highlight')),
                  ),
                if (kabar.isNotEmpty) ...[
                  Padding(
                    padding:
                        const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
                    child: SectionHeader('Kabar JDIH',
                        onSeeAll: () =>
                            _push(context, const KabarHubScreen(showBack: true))),
                  ),
                  SizedBox(
                    height: 244,
                    child: ListView.separated(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(
                          horizontal: AppSpacing.lg),
                      itemCount: kabar.length,
                      separatorBuilder: (_, __) =>
                          const SizedBox(width: AppSpacing.md),
                      itemBuilder: (_, i) {
                        final (:item, :pengumuman) = kabar[i];
                        return NewsTile(item,
                            width: 224,
                            onTap: () => _push(
                                context,
                                pengumuman
                                    ? PengumumanDetailScreen(id: item.i('id'))
                                    : BeritaDetailScreen(id: item.i('id'))));
                      },
                    ),
                  ),
                ],
              ],
            );
          },
        ),
      );
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
            showBack: true, initialAi: ai, initialQuery: _ctrl.text.trim()));
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
            border: const OutlineInputBorder(
              borderRadius: BorderRadius.all(Radius.circular(28)),
              borderSide: BorderSide(color: C.lightLine),
            ),
            enabledBorder: const OutlineInputBorder(
              borderRadius: BorderRadius.all(Radius.circular(28)),
              borderSide: BorderSide(color: C.lightLine),
            ),
            focusedBorder: const OutlineInputBorder(
              borderRadius: BorderRadius.all(Radius.circular(28)),
              borderSide: BorderSide(color: C.primary, width: 1.5),
            ),
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
        child: Row(children: [
          for (final (i, c) in docCategories.indexed) ...[
            Expanded(
              child: Pressable(
                child: Material(
                  color: C.lightSurface,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(AppRadius.card),
                    side: const BorderSide(color: C.lightLine),
                  ),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(AppRadius.card),
                    onTap: () =>
                        _push(context, DocumentListScreen(category: c.slug)),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                          vertical: AppSpacing.md, horizontal: 4),
                      child: Column(children: [
                        Icon(c.icon, size: 22, color: C.primaryInk),
                        const SizedBox(height: 6),
                        Text(c.short,
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                                fontSize: 11.5, fontWeight: FontWeight.w700)),
                        Text('${stats.i(c.slug)}',
                            style: const TextStyle(
                                fontSize: 11,
                                color: C.lightInkMuted,
                                fontFeatures: [FontFeature.tabularFigures()])),
                      ]),
                    ),
                  ),
                ),
              ),
            ),
            if (i < docCategories.length - 1)
              const SizedBox(width: AppSpacing.sm),
          ],
        ]),
      );
}
