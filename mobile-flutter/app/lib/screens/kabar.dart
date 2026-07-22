import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';

/// Kartu berita/pengumuman: gambar + judul + tanggal + ringkasan.
class MediaCard extends StatelessWidget {
  const MediaCard(this.item, {super.key, this.badge, required this.onTap});
  final Json item;
  final String? badge;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final muted = Theme.of(context).brightness == Brightness.dark
        ? C.darkInkMuted
        : C.lightInkMuted;
    return Pressable(
      child: Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            NetImage(item.sn('image_url'), width: 110, height: 110),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    if (badge != null && badge!.isNotEmpty) ...[
                      JenisChip(badge),
                      const SizedBox(height: 4),
                    ],
                    Text(item.s('judul'),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontWeight: FontWeight.w600)),
                    const SizedBox(height: 4),
                    Text(fmtDate(item.sn('tanggal')),
                        style: TextStyle(fontSize: 12, color: muted)),
                    const SizedBox(height: 4),
                    Text(item.s('ringkasan'),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(fontSize: 13, color: muted)),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    ),
    );
  }
}

/// Tab "Kabar": hub Berita / Pengumuman / Informasi Hukum / Video.
class KabarHubScreen extends StatelessWidget {
  const KabarHubScreen({super.key});

  void _push(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) {
    final items = [
      (Icons.campaign, 'Pengumuman', 'Pemberitahuan resmi', const PengumumanListScreen()),
      (Icons.description, 'Informasi Hukum', 'Propemperda, Ranperda, dll.', const InfoHukumScreen()),
      (Icons.play_circle, 'Video', 'Kanal YouTube JDIH', const VideoListScreen()),
    ];
    return Scaffold(
      appBar: const BrandAppBar('Kabar'),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // tile utama: Berita sebagai color-block oranye
          Rise(
            child: Pressable(
              child: ClipRRect(
                borderRadius: BorderRadius.circular(20),
                child: Stack(children: [
                  Positioned.fill(
                    child: DecoratedBox(
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(
                            colors: [C.primary, C.primaryDeep],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight),
                      ),
                    ),
                  ),
                  const Positioned.fill(child: GeoPattern(opacity: .14)),
                  Material(
                    color: Colors.transparent,
                    child: InkWell(
                      onTap: () => _push(context, const BeritaListScreen()),
                      // teks gelap di atas oranye: kontras WCAG >= 4.5:1
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Row(children: [
                          Container(
                            width: 56,
                            height: 56,
                            decoration: BoxDecoration(
                              color: C.lightInk.withValues(alpha: .1),
                              borderRadius: BorderRadius.circular(18),
                            ),
                            child: const Icon(Icons.newspaper,
                                color: C.lightInk, size: 30),
                          ),
                          const SizedBox(width: 16),
                          const Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Berita',
                                    style: TextStyle(
                                        color: C.lightInk,
                                        fontSize: 18,
                                        fontWeight: FontWeight.w800)),
                                SizedBox(height: 4),
                                Text('Kabar terbaru JDIH Kota Kendari',
                                    style: TextStyle(
                                        color: C.lightInk, fontSize: 13)),
                              ],
                            ),
                          ),
                          const Icon(Icons.arrow_forward, color: C.lightInk),
                        ]),
                      ),
                    ),
                  ),
                ]),
              ),
            ),
          ),
          const SizedBox(height: 12),
          for (final (i, item) in items.indexed) ...[
            Rise(
              delayMs: 80 + i * 60,
              child: Pressable(
                child: Card(
                  child: ListTile(
                    leading: IconSquircle(item.$1),
                    title: Text(item.$2,
                        style: const TextStyle(fontWeight: FontWeight.w700)),
                    subtitle: Text(item.$3),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => _push(context, item.$4),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 12),
          ],
        ],
      ),
    );
  }
}

// ---------------- BERITA ----------------

class BeritaListScreen extends StatelessWidget {
  const BeritaListScreen({super.key});

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Berita'),
        body: PagedListView(
          fetch: (page) => api.news(page: page),
          itemBuilder: (context, b) => MediaCard(b,
              onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (_) => BeritaDetailScreen(id: b.i('id'))))),
        ),
      );
}

class BeritaDetailScreen extends StatelessWidget {
  const BeritaDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Berita'),
        body: LoadView(
          load: () => api.newsDetail(id),
          builder: (context, b) => ListView(
            padding: const EdgeInsets.all(16),
            children: [
              if (b.sn('image_url') != null)
                AspectRatio(
                    aspectRatio: 16 / 9,
                    child: NetImage(b.sn('image_url'), radius: 12)),
              const SizedBox(height: 16),
              Text(b.s('judul'),
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
              const SizedBox(height: 4),
              Text(fmtDate(b.sn('tanggal')),
                  style: const TextStyle(fontSize: 12, color: Colors.grey)),
              const SizedBox(height: 16),
              HtmlBody(b.s('isi')),
              const SizedBox(height: 24),
            ],
          ),
        ),
      );
}

// ---------------- PENGUMUMAN ----------------

class PengumumanListScreen extends StatelessWidget {
  const PengumumanListScreen({super.key});

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Pengumuman'),
        body: PagedListView(
          fetch: (page) => api.announcements(page: page),
          itemBuilder: (context, p) => MediaCard(p,
              badge: p.sn('tag'),
              onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                      builder: (_) => PengumumanDetailScreen(id: p.i('id'))))),
        ),
      );
}

class PengumumanDetailScreen extends StatelessWidget {
  const PengumumanDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Pengumuman'),
        body: LoadView(
          load: () => api.announcementDetail(id),
          builder: (context, p) => ListView(
            padding: const EdgeInsets.all(16),
            children: [
              if (p.sn('image_url') != null)
                AspectRatio(
                    aspectRatio: 16 / 9,
                    child: NetImage(p.sn('image_url'), radius: 12)),
              const SizedBox(height: 16),
              JenisChip(p.sn('tag')),
              const SizedBox(height: 8),
              Text(p.s('judul'),
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
              const SizedBox(height: 4),
              Text(fmtDate(p.sn('tanggal')),
                  style: const TextStyle(fontSize: 12, color: Colors.grey)),
              const SizedBox(height: 16),
              HtmlBody(p.s('isi')),
              if (p.sn('dokumen_url') != null) ...[
                const SizedBox(height: 16),
                FilledButton.icon(
                  icon: const Icon(Icons.download),
                  label: const Text('Unduh Lampiran'),
                  onPressed: () => openUrl(context, p.sn('dokumen_url')),
                ),
              ],
              const SizedBox(height: 24),
            ],
          ),
        ),
      );
}

// ---------------- INFORMASI HUKUM ----------------

class InfoHukumScreen extends StatefulWidget {
  const InfoHukumScreen({super.key});

  @override
  State<InfoHukumScreen> createState() => _InfoHukumScreenState();
}

class _InfoHukumScreenState extends State<InfoHukumScreen> {
  List<Json> _types = [];
  String _type = '';

  @override
  void initState() {
    super.initState();
    api.legalInfoTypes().then((t) {
      if (mounted) setState(() => _types = t);
    }).catchError((_) {});
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Informasi Hukum'),
        body: Column(
          children: [
            if (_types.isNotEmpty)
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                child: Row(children: [
                  for (final t in _types)
                    Padding(
                      padding: const EdgeInsets.only(right: 8),
                      child: ChoiceChip(
                        label: Text(t.s('singkatan')),
                        selected: _type == t.s('id'),
                        onSelected: (sel) =>
                            setState(() => _type = sel ? t.s('id') : ''),
                      ),
                    ),
                ]),
              ),
            Expanded(
              child: PagedListView(
                key: ValueKey(_type),
                fetch: (page) => api.legalInfo(type: _type, page: page),
                itemBuilder: (context, x) => Card(
                  child: ListTile(
                    title: Text(x.s('judul'),
                        maxLines: 2, overflow: TextOverflow.ellipsis),
                    subtitle: Text(
                        '${x.s('jenis_singkatan')} · ${fmtDate(x.sn('tanggal'))}'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => InfoHukumDetailScreen(id: x.i('id')))),
                  ),
                ),
              ),
            ),
          ],
        ),
      );
}

class InfoHukumDetailScreen extends StatelessWidget {
  const InfoHukumDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Informasi Hukum'),
        body: LoadView(
          load: () => api.legalInfoDetail(id),
          builder: (context, x) => ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Text(x.s('judul'),
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
              const SizedBox(height: 4),
              Text(fmtDate(x.sn('tanggal')),
                  style: const TextStyle(fontSize: 12, color: Colors.grey)),
              const SizedBox(height: 16),
              if (x.sn('isi') != null) HtmlBody(x.s('isi')),
              if (x.sn('dokumen_url') != null) ...[
                const SizedBox(height: 16),
                FilledButton.icon(
                  icon: const Icon(Icons.download),
                  label: const Text('Unduh Dokumen'),
                  onPressed: () => openUrl(context, x.sn('dokumen_url')),
                ),
              ],
              const SizedBox(height: 24),
            ],
          ),
        ),
      );
}

// ---------------- VIDEO ----------------

class VideoCard extends StatelessWidget {
  const VideoCard(this.v, {super.key, this.width});
  final Json v;
  final double? width;

  @override
  Widget build(BuildContext context) {
    final muted = Theme.of(context).brightness == Brightness.dark
        ? C.darkInkMuted
        : C.lightInkMuted;
    return SizedBox(
      width: width,
      child: Pressable(
          child: Card(
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: () => openUrl(context, v.sn('watch_url')),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Stack(alignment: Alignment.center, children: [
                AspectRatio(
                    aspectRatio: 16 / 9, child: NetImage(v.sn('thumbnail_url'))),
                Container(
                  decoration: const BoxDecoration(
                      color: C.primary, shape: BoxShape.circle),
                  padding: const EdgeInsets.all(10),
                  child: const Icon(Icons.play_arrow, color: Colors.white),
                ),
              ]),
              Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(v.s('judul'),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontWeight: FontWeight.w600)),
                    const SizedBox(height: 4),
                    Text(fmtDate(v.sn('tanggal')),
                        style: TextStyle(fontSize: 12, color: muted)),
                  ],
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

class VideoListScreen extends StatelessWidget {
  const VideoListScreen({super.key});

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Video'),
        body: PagedListView(
          fetch: (page) => api.videos(page: page),
          itemBuilder: (context, v) => VideoCard(v),
        ),
      );
}
