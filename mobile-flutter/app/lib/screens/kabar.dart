import 'package:flutter/material.dart';
import 'package:youtube_player_iframe/youtube_player_iframe.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'doc_view.dart';

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

/// Tab "Kabar": indeks bertab — Berita, Pengumuman, Video, Informasi Hukum.
/// Konten langsung terlihat, bukan hub yang menambah satu ketukan.
class KabarHubScreen extends StatelessWidget {
  const KabarHubScreen({super.key, this.showBack = false});
  final bool showBack;

  @override
  Widget build(BuildContext context) => DefaultTabController(
        length: 4,
        child: Scaffold(
          appBar: BrandAppBar(
            'Kabar & Informasi',
            showBack: showBack,
            tabs: const TabBar(
              isScrollable: true,
              tabAlignment: TabAlignment.start,
              tabs: [
                Tab(text: 'Berita'),
                Tab(text: 'Pengumuman'),
                Tab(text: 'Video'),
                Tab(text: 'Info Hukum'),
              ],
            ),
          ),
          body: const TabBarView(children: [
            _BeritaTab(),
            _PengumumanTab(),
            _VideoTab(),
            InfoHukumTab(),
          ]),
        ),
      );
}

/// Item pertama tampil sebagai kartu sorotan bergambar, sisanya baris ringkas.
class _BeritaTab extends StatelessWidget {
  const _BeritaTab();

  @override
  Widget build(BuildContext context) => PagedListView(
        fetch: (page) => api.news(page: page),
        empty: 'Belum ada berita yang dipublikasikan.',
        itemBuilder: (context, b, i) {
          void open() => Navigator.push(context,
              MaterialPageRoute(builder: (_) => BeritaDetailScreen(id: b.i('id'))));
          return i == 0
              ? NewsTile(b, featured: true, onTap: open)
              : MediaCard(b, onTap: open);
        },
      );
}

class _PengumumanTab extends StatelessWidget {
  const _PengumumanTab();

  @override
  Widget build(BuildContext context) => PagedListView(
        fetch: (page) => api.announcements(page: page),
        empty: 'Belum ada pengumuman.',
        itemBuilder: (context, p, i) {
          void open() => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => PengumumanDetailScreen(id: p.i('id'))));
          return i == 0
              ? NewsTile(p, featured: true, onTap: open)
              : MediaCard(p, badge: p.sn('tag'), onTap: open);
        },
      );
}

class _VideoTab extends StatelessWidget {
  const _VideoTab();

  @override
  Widget build(BuildContext context) => PagedListView(
        fetch: (page) => api.videos(page: page),
        empty: 'Belum ada video di kanal JDIH.',
        itemBuilder: (context, v, __) => VideoCard(v),
      );
}

// ---------------- BERITA ----------------

class BeritaDetailScreen extends StatelessWidget {
  const BeritaDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => ArticleDetail(
        kicker: 'Berita',
        load: () => api.newsDetail(id),
      );
}

/// Detail artikel: hero gambar penuh + tombol bulat melayang, lalu isi.
/// Dipakai berita dan pengumuman.
class ArticleDetail extends StatelessWidget {
  const ArticleDetail(
      {super.key, required this.kicker, required this.load, this.footer});
  final String kicker;
  final Future<Json> Function() load;

  /// Tambahan di bawah isi (mis. berkas lampiran pengumuman).
  final List<Widget> Function(BuildContext, Json)? footer;

  @override
  Widget build(BuildContext context) => Scaffold(
        body: LoadView(
          load: load,
          builder: (context, a) => CustomScrollView(slivers: [
            SliverAppBar(
              expandedHeight: a.sn('image_url') != null ? 240 : 96,
              pinned: true,
              backgroundColor: C.lightSurface,
              surfaceTintColor: Colors.transparent,
              leadingWidth: 64,
              leading: Padding(
                padding: const EdgeInsets.only(left: AppSpacing.lg),
                child: RoundIconButton(Icons.arrow_back,
                    tooltip: 'Kembali',
                    onPressed: () => Navigator.pop(context)),
              ),
              flexibleSpace: FlexibleSpaceBar(
                background: a.sn('image_url') != null
                    ? Stack(fit: StackFit.expand, children: [
                        NetImage(a.sn('image_url')),
                        // gradasi tipis supaya tombol bulat tetap terbaca
                        DecoratedBox(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              begin: Alignment.topCenter,
                              end: Alignment.center,
                              colors: [
                                C.ink.withValues(alpha: .35),
                                C.ink.withValues(alpha: 0),
                              ],
                            ),
                          ),
                        ),
                      ])
                    : const DecoratedBox(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                              colors: [C.ink, C.inkSoft],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight),
                        ),
                      ),
              ),
            ),
            SliverPadding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              sliver: SliverList.list(children: [
                Row(children: [
                  JenisChip(a.sn('tag') ?? kicker),
                  const SizedBox(width: AppSpacing.sm),
                  MetaPill(Icons.event_outlined, fmtDate(a.sn('tanggal'))),
                ]),
                const SizedBox(height: AppSpacing.md),
                Text(a.s('judul'),
                    style: const TextStyle(
                        fontSize: 22, height: 1.3, fontWeight: FontWeight.w700)),
                const SizedBox(height: AppSpacing.lg),
                HtmlBody(a.s('isi')),
                ...?footer?.call(context, a),
                const SizedBox(height: AppSpacing.xl),
              ]),
            ),
          ]),
        ),
      );
}

// ---------------- PENGUMUMAN ----------------

class PengumumanDetailScreen extends StatelessWidget {
  const PengumumanDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => ArticleDetail(
        kicker: 'Pengumuman',
        load: () => api.announcementDetail(id),
        footer: (context, p) => [
          if (p.sn('dokumen_url') != null) ...[
            const SectionHeader('Lampiran'),
            DocFileTile(p.s('dokumen_url'), title: 'Lampiran Pengumuman'),
          ],
        ],
      );
}

// ---------------- INFORMASI HUKUM ----------------

class InfoHukumTab extends StatefulWidget {
  const InfoHukumTab({super.key});

  @override
  State<InfoHukumTab> createState() => _InfoHukumTabState();
}

class _InfoHukumTabState extends State<InfoHukumTab> {
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
  Widget build(BuildContext context) => Column(
        children: [
          if (_types.isNotEmpty)
            Padding(
              padding: const EdgeInsets.fromLTRB(
                  AppSpacing.lg, AppSpacing.md, AppSpacing.lg, 0),
              child: FilterPills(
                labels: ['Semua', for (final t in _types) t.s('singkatan')],
                selected: _type.isEmpty
                    ? 0
                    : _types.indexWhere((t) => t.s('id') == _type) + 1,
                onSelected: (i) => setState(
                    () => _type = i == 0 ? '' : _types[i - 1].s('id')),
              ),
            ),
          Expanded(
            child: PagedListView(
              key: ValueKey(_type),
              empty: 'Belum ada informasi hukum pada kategori ini.',
              fetch: (page) => api.legalInfo(type: _type, page: page),
              itemBuilder: (context, x, __) => Card(
                child: ListTile(
                  leading: const IconSquircle(Icons.description_outlined,
                      size: 40),
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
                const SectionHeader('Dokumen'),
                DocFileTile(x.s('dokumen_url'), title: x.s('judul')),
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
          onTap: () => Navigator.push(context,
              MaterialPageRoute(builder: (_) => VideoPlayerScreen(v))),
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
                  child: const Icon(Icons.play_arrow, color: C.ink),
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

/// Pemutar YouTube di dalam aplikasi (iframe player, tanpa keluar app).
class VideoPlayerScreen extends StatefulWidget {
  const VideoPlayerScreen(this.v, {super.key});
  final Json v;

  @override
  State<VideoPlayerScreen> createState() => _VideoPlayerScreenState();
}

class _VideoPlayerScreenState extends State<VideoPlayerScreen> {
  late final YoutubePlayerController _c = YoutubePlayerController.fromVideoId(
    videoId: widget.v.s('youtube_id'),
    autoPlay: true,
    params: const YoutubePlayerParams(
      showFullscreenButton: true,
      strictRelatedVideos: true,
    ),
  );

  @override
  void dispose() {
    _c.close();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Video'),
        body: ListView(
          children: [
            ColoredBox(
              color: C.ink,
              child: YoutubePlayer(controller: _c, aspectRatio: 16 / 9),
            ),
            Padding(
              padding: const EdgeInsets.all(AppSpacing.lg),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(widget.v.s('judul'),
                      style: const TextStyle(
                          fontSize: 18,
                          height: 1.3,
                          fontWeight: FontWeight.w700)),
                  const SizedBox(height: AppSpacing.sm),
                  MetaPill(
                      Icons.event_outlined, fmtDate(widget.v.sn('tanggal'))),
                  const SizedBox(height: AppSpacing.lg),
                  OutlinedButton.icon(
                    icon: const Icon(Icons.open_in_new, size: 18),
                    label: const Text('Buka di YouTube'),
                    onPressed: () =>
                        openUrl(context, widget.v.sn('watch_url')),
                  ),
                ],
              ),
            ),
          ],
        ),
      );
}

