import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';
import 'kabar.dart';
import 'search.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  void _push(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: BrandAppBar('JDIH Kota Kendari', actions: [
          IconButton(
              icon: const Icon(Icons.search),
              onPressed: () => _push(context, const SearchScreen(showBack: true))),
        ]),
        body: LoadView(
          load: api.home,
          builder: (context, d) {
            final stats = d.m('statistik');
            return ListView(
              padding: const EdgeInsets.all(16),
              children: [
                // ---- HERO: color-block biru + ornamen geometris ----
                ClipRRect(
                  borderRadius: BorderRadius.circular(20),
                  child: Stack(children: [
                    Positioned.fill(
                      child: DecoratedBox(
                        decoration: const BoxDecoration(
                          gradient: LinearGradient(
                              colors: [C.accent, C.accentDeep],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight),
                        ),
                      ),
                    ),
                    const Positioned.fill(child: GeoPattern(opacity: .12)),
                    Padding(
                      padding: const EdgeInsets.all(22),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text('Selamat Datang di\nJDIH Kota Kendari',
                              style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 26,
                                  height: 1.15,
                                  letterSpacing: -.5,
                                  fontWeight: FontWeight.w800)),
                          if (d.m('narasi').sn('text') != null) ...[
                            const SizedBox(height: 10),
                            Text(
                                // narasi dari DB berupa HTML -> tampilkan teks polosnya
                                d
                                    .m('narasi')
                                    .s('text')
                                    .replaceAll(RegExp(r'<[^>]*>'), ' ')
                                    .replaceAll(RegExp(r'\s+'), ' ')
                                    .trim(),
                                maxLines: 3,
                                overflow: TextOverflow.ellipsis,
                                style: const TextStyle(
                                    color: Colors.white70, height: 1.5)),
                          ],
                          const SizedBox(height: 18),
                          Pressable(
                            child: InkWell(
                              onTap: () => _push(
                                  context, const SearchScreen(showBack: true)),
                              borderRadius: BorderRadius.circular(12),
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 14, vertical: 13),
                                decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(12)),
                                child: const Row(children: [
                                  Icon(Icons.search, color: C.accent),
                                  SizedBox(width: 8),
                                  Text('Cari peraturan, dokumen hukum...',
                                      style: TextStyle(color: Colors.grey)),
                                ]),
                              ),
                            ),
                          ),
                          const SizedBox(height: 12),
                          Row(children: [
                            Expanded(
                              child: FilledButton(
                                  onPressed: () => _push(context,
                                      const SearchScreen(showBack: true)),
                                  child: const Text('Cari Dokumen',
                                      style:
                                          TextStyle(fontWeight: FontWeight.w700))),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: FilledButton.icon(
                                  style: FilledButton.styleFrom(
                                      backgroundColor:
                                          Colors.white.withValues(alpha: .16),
                                      foregroundColor: Colors.white,
                                      side: BorderSide(
                                          color:
                                              Colors.white.withValues(alpha: .4))),
                                  onPressed: () => _push(
                                      context,
                                      const SearchScreen(
                                          showBack: true, initialAi: true)),
                                  icon: const Icon(Icons.auto_awesome, size: 18),
                                  label: const Text('Tanya AI',
                                      style:
                                          TextStyle(fontWeight: FontWeight.w700))),
                            ),
                          ]),
                        ],
                      ),
                    ),
                  ]),
                ),

                // ---- STATISTIK: satu strip color-block, 4 kolom angka ----
                const SizedBox(height: 12),
                Container(
                  padding: const EdgeInsets.symmetric(vertical: 16),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16),
                    gradient: const LinearGradient(
                        colors: [C.primary, C.primaryDeep],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight),
                  ),
                  child: IntrinsicHeight(
                    child: Row(children: [
                      for (final c in docCategories) ...[
                        Expanded(
                          child: InkWell(
                            onTap: () => _push(
                                context, DocumentListScreen(category: c.slug)),
                            // teks gelap di atas oranye: kontras WCAG >= 4.5:1
                            child: Column(children: [
                              Text('${stats.i(c.slug)}',
                                  style: const TextStyle(
                                      fontSize: 22,
                                      height: 1.1,
                                      fontWeight: FontWeight.w800,
                                      color: C.lightInk,
                                      fontFeatures: [FontFeature.tabularFigures()])),
                              const SizedBox(height: 2),
                              Text(c.short,
                                  style: const TextStyle(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w700,
                                      color: C.lightInk)),
                            ]),
                          ),
                        ),
                        if (c != docCategories.last)
                          VerticalDivider(
                              width: 1,
                              thickness: 1,
                              indent: 6,
                              endIndent: 6,
                              color: C.lightInk.withValues(alpha: .2)),
                      ],
                    ]),
                  ),
                ),

                // ---- PERATURAN TERBARU ----
                SectionHeader('Peraturan Terbaru',
                    onSeeAll: () => _push(
                        context, const DocumentListScreen(category: 'peraturan'))),
                for (final (i, p) in d.l('peraturan_terbaru').indexed) ...[
                  Rise(delayMs: i * 60, child: DocumentCard(p)),
                  const SizedBox(height: 12),
                ],

                // ---- MONOGRAFI ----
                if (d['monografi_highlight'] != null) ...[
                  SectionHeader('Monografi Pilihan',
                      onSeeAll: () => _push(context,
                          const DocumentListScreen(category: 'monografi'))),
                  DocumentCard(d.m('monografi_highlight')),
                ],

                // ---- PENGUMUMAN ----
                SectionHeader('Pengumuman',
                    onSeeAll: () => _push(context, const PengumumanListScreen())),
                for (final p in d.l('pengumuman')) ...[
                  MediaCard(p,
                      badge: p.sn('tag'),
                      onTap: () => _push(
                          context, PengumumanDetailScreen(id: p.i('id')))),
                  const SizedBox(height: 12),
                ],

                // ---- BERITA ----
                SectionHeader('Berita',
                    onSeeAll: () => _push(context, const BeritaListScreen())),
                for (final b in d.l('berita')) ...[
                  MediaCard(b,
                      onTap: () =>
                          _push(context, BeritaDetailScreen(id: b.i('id')))),
                  const SizedBox(height: 12),
                ],

                // ---- VIDEO ----
                SectionHeader('Video',
                    onSeeAll: () => _push(context, const VideoListScreen())),
                SizedBox(
                  height: 220,
                  child: ListView(
                    scrollDirection: Axis.horizontal,
                    children: [
                      for (final v in d.l('video')) ...[
                        VideoCard(v, width: 260),
                        const SizedBox(width: 12),
                      ],
                    ],
                  ),
                ),

                // ---- PEJABAT ----
                const SectionHeader('Pimpinan Daerah'),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    for (final p in d.l('pejabat'))
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: 4),
                          child: Column(children: [
                            Container(
                              padding: const EdgeInsets.all(3),
                              decoration: const BoxDecoration(
                                shape: BoxShape.circle,
                                gradient: LinearGradient(
                                    colors: [C.primary, C.accent]),
                              ),
                              child: ClipOval(
                                  child: NetImage(p.sn('gambar_url'),
                                      width: 80, height: 80)),
                            ),
                            const SizedBox(height: 8),
                            Text(p.s('nama'),
                                textAlign: TextAlign.center,
                                maxLines: 3,
                                style: const TextStyle(
                                    fontSize: 12, fontWeight: FontWeight.w700)),
                            Text(p.s('jabatan'),
                                textAlign: TextAlign.center,
                                style: const TextStyle(
                                    fontSize: 11, color: Colors.grey)),
                          ]),
                        ),
                      ),
                  ],
                ),
                const SizedBox(height: 24),
              ],
            );
          },
        ),
      );
}
