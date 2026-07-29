import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
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
          // Tinggi dikunci, bukan mengikuti isi: satu kartu yang judulnya
          // pendek atau ringkasannya kosong tidak boleh jadi lebih ceking
          // daripada tetangganya di daftar yang sama.
          child: SizedBox(
            height: 116,
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                NetImage(item.sn('image_url'), width: 110),
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
                        Text(ucFirst(item.s('judul')),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style:
                                const TextStyle(fontWeight: FontWeight.w600)),
                        const SizedBox(height: 4),
                        Text(fmtDate(item.sn('tanggal')),
                            style: TextStyle(fontSize: 12, color: muted)),
                        const SizedBox(height: 4),
                        // Expanded, bukan tinggi baris tetap: sisa ruang yang
                        // ada dipakai apa adanya, jadi aman saat pengguna
                        // membesarkan ukuran teks sistem
                        Expanded(
                          child: Text(ucFirst(item.s('ringkasan')),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: TextStyle(fontSize: 13, color: muted)),
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

/// Daftar berpaginasi dengan kolom cari di atasnya. Hanya dipakai tab yang
/// isinya ratusan entri; menggulir 100+ kartu untuk mencari satu judul jauh
/// lebih mahal daripada satu baris kolom cari.
class _SearchableList extends StatefulWidget {
  const _SearchableList(
      {required this.hint,
      required this.fetch,
      required this.empty,
      required this.itemBuilder});
  final String hint;
  final Future<Paginated> Function(int page, String q) fetch;
  final String empty;

  /// `searching` true saat ada kata kunci: pemanggil memakainya untuk mematikan
  /// kartu sorotan — "Terbaru" tidak bermakna pada daftar hasil pencarian.
  final Widget Function(BuildContext, Json, int index, bool searching)
      itemBuilder;

  @override
  State<_SearchableList> createState() => _SearchableListState();
}

class _SearchableListState extends State<_SearchableList> {
  final _ctrl = TextEditingController();
  String _q = '';

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  void _apply(String v) => setState(() => _q = v.trim());

  @override
  Widget build(BuildContext context) => Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, AppSpacing.md, AppSpacing.lg, 0),
            child: TextField(
              controller: _ctrl,
              decoration: InputDecoration(
                hintText: widget.hint,
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _q.isEmpty
                    ? null
                    : IconButton(
                        icon: const Icon(Icons.close),
                        tooltip: 'Hapus pencarian',
                        onPressed: () {
                          _ctrl.clear();
                          _apply('');
                        },
                      ),
              ),
              textInputAction: TextInputAction.search,
              onSubmitted: _apply,
            ),
          ),
          Expanded(
            child: PagedListView(
              // ganti kata kunci = muat ulang dari halaman pertama
              key: ValueKey(_q),
              fetch: (page) => widget.fetch(page, _q),
              empty: widget.empty,
              emptyView: _q.isEmpty
                  ? null
                  : NoResults(
                      _q,
                      saran: 'Coba kata kunci yang lebih pendek, atau periksa '
                          'ejaannya.',
                      actions: [
                        FilledButton.icon(
                          onPressed: () {
                            _ctrl.clear();
                            _apply('');
                          },
                          icon: const Icon(Icons.arrow_back, size: 18),
                          label: const Text('Lihat semua'),
                        ),
                      ],
                    ),
              itemBuilder: (c, item, i) =>
                  widget.itemBuilder(c, item, i, _q.isNotEmpty),
            ),
          ),
        ],
      );
}

/// Item pertama tampil sebagai kartu sorotan bergambar, sisanya baris ringkas.
class _BeritaTab extends StatelessWidget {
  const _BeritaTab();

  @override
  Widget build(BuildContext context) => _SearchableList(
        hint: 'Cari judul berita...',
        fetch: (page, q) => api.news(page: page, q: q),
        empty: 'Belum ada berita yang dipublikasikan.',
        itemBuilder: (context, b, i, searching) {
          void open() => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => BeritaDetailScreen(id: b.i('id'))));
          return i == 0 && !searching
              ? NewsTile(b, featured: true, onTap: open)
              : MediaCard(b, onTap: open);
        },
      );
}

class _PengumumanTab extends StatelessWidget {
  const _PengumumanTab();

  @override
  Widget build(BuildContext context) => _SearchableList(
        hint: 'Cari judul pengumuman...',
        fetch: (page, q) => api.announcements(page: page, q: q),
        empty: 'Belum ada pengumuman.',
        itemBuilder: (context, p, i, searching) {
          void open() => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => PengumumanDetailScreen(id: p.i('id'))));
          return i == 0 && !searching
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
        // dari mana pun masuknya (beranda, pencarian, berita lain), tombol
        // kembali pulang ke tab Kabar di shell — bukan mem-push halaman indeks
        // baru, yang membuat navigasi bawah ikut hilang
        onBack: () => kembaliKeTab(context, kTabKabar),
        footer: (context, b) => [
          _ArtikelLainnya(
            judul: 'Berita Lainnya',
            fetch: api.news,
            kecuali: b.i('id'),
            open: (id) => BeritaDetailScreen(id: id),
          ),
        ],
      );
}

/// Tiga artikel lain (acak) di dasar halaman detail. Gagal atau kosong =
/// senyap — pelengkap, bukan isi utama, jadi tidak perlu error state.
class _ArtikelLainnya extends StatefulWidget {
  const _ArtikelLainnya(
      {required this.judul,
      required this.fetch,
      required this.kecuali,
      required this.open,
      this.badge = false});
  final String judul;
  final Future<Paginated> Function() fetch;
  final int kecuali;
  final Widget Function(int id) open;

  /// Tampilkan tag pada kartu (dipakai pengumuman).
  final bool badge;

  @override
  State<_ArtikelLainnya> createState() => _ArtikelLainnyaState();
}

class _ArtikelLainnyaState extends State<_ArtikelLainnya> {
  /// Diacak sekali di sini, bukan di build: rebuild (mis. pull-to-refresh
  /// induk) tidak boleh menukar-nukar kartu yang sedang dilihat.
  late final Future<List<Json>> _future = widget.fetch().then((p) =>
      (p.items.where((x) => x.i('id') != widget.kecuali).toList()..shuffle())
          .take(3)
          .toList());

  @override
  Widget build(BuildContext context) => FutureBuilder<List<Json>>(
        future: _future,
        builder: (context, snap) {
          if (snap.connectionState == ConnectionState.waiting) {
            return _blok(const SkeletonList(
                count: 3, height: 104, padding: EdgeInsets.zero));
          }
          final lain = snap.data ?? const <Json>[];
          if (lain.isEmpty) return const SizedBox.shrink();
          return _blok(Column(children: [
            for (final x in lain) ...[
              MediaCard(x,
                  badge: widget.badge ? x.sn('tag') : null,
                  // ganti halaman, bukan menumpuk: rantai detail->detail
                  // membuat tombol kembali harus ditekan berkali-kali
                  onTap: () => Navigator.pushReplacement(context,
                      MaterialPageRoute(builder: (_) => widget.open(x.i('id'))))),
              const SizedBox(height: AppSpacing.md),
            ],
          ]));
        },
      );

  Widget _blok(Widget isi) => Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: AppSpacing.xl),
          const Divider(),
          const SizedBox(height: AppSpacing.lg),
          SectionHeader(widget.judul),
          isi,
        ],
      );
}

/// Detail artikel: hero gambar (ketuk = layar penuh, bisa dizoom) yang ditimpa
/// lembar isi bersudut membulat — kesan majalah. Dipakai berita dan pengumuman.
class ArticleDetail extends StatelessWidget {
  const ArticleDetail(
      {super.key,
      required this.kicker,
      required this.load,
      this.footer,
      this.onBack,
      this.heroIcon});
  final String kicker;
  final Future<Json> Function() load;

  /// Ikon cap air pada hero saat artikel tidak bergambar.
  final IconData? heroIcon;

  /// Tambahan di bawah isi (mis. berkas lampiran pengumuman).
  final List<Widget> Function(BuildContext, Json)? footer;

  /// Perilaku tombol kembali; default pop biasa.
  final VoidCallback? onBack;

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final surface = dark ? C.darkSurface : C.lightSurface;
    return Scaffold(
      body: LoadView(
        load: load,
        skeleton: const ArticleSkeleton(),
        builder: (context, a) {
          final img = a.sn('image_url');
          final tag = 'cover-${a.i('id')}';
          return CustomScrollView(slivers: [
            SliverAppBar(
              expandedHeight: img != null ? 320 : 200,
              pinned: true,
              stretch: true,
              backgroundColor: surface,
              surfaceTintColor: Colors.transparent,
              leadingWidth: 64,
              leading: Padding(
                padding: const EdgeInsets.only(left: AppSpacing.lg),
                child: RoundIconButton(Icons.arrow_back,
                    tooltip: 'Kembali',
                    onPressed: onBack ?? () => Navigator.pop(context)),
              ),
              flexibleSpace: FlexibleSpaceBar(
                stretchModes: const [StretchMode.zoomBackground],
                background: img != null
                    ? _Cover(img, tag: tag)
                    : Stack(fit: StackFit.expand, children: [
                        const DecoratedBox(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                                colors: [C.ink, C.inkSoft],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight),
                          ),
                        ),
                        // ornamen brand: hero tanpa gambar tidak jadi kotak polos
                        const GeoPattern(color: C.primary, opacity: .18),
                        if (heroIcon != null)
                          Positioned(
                            right: -16,
                            bottom: 4,
                            child: Icon(heroIcon,
                                size: 150,
                                color: C.primary.withValues(alpha: .22)),
                          ),
                        Positioned(
                          left: AppSpacing.lg,
                          bottom: 44,
                          child: Text(kicker.toUpperCase(),
                              style: const TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                  letterSpacing: 2,
                                  color: C.goldSoft)),
                        ),
                      ]),
              ),
            ),
            SliverToBoxAdapter(
              // lembar isi naik menimpa gambar 24dp
              child: Transform.translate(
                offset: const Offset(0, -24),
                child: Container(
                  decoration: BoxDecoration(
                    color: surface,
                    borderRadius: const BorderRadius.vertical(
                        top: Radius.circular(AppRadius.sheet)),
                  ),
                  // 48 = 24 tertelan tumpangan sheet + 24 jarak nyata ke gambar
                  padding: const EdgeInsets.fromLTRB(
                      AppSpacing.lg, 48, AppSpacing.lg, AppSpacing.xxl),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Wrap(
                        spacing: AppSpacing.sm,
                        runSpacing: AppSpacing.sm,
                        crossAxisAlignment: WrapCrossAlignment.center,
                        children: [
                          JenisChip(a.sn('tag') ?? kicker),
                          MetaPill(
                              Icons.event_outlined, fmtDate(a.sn('tanggal'))),
                          // info hukum kerap hanya berkas tanpa isi: pil waktu
                          // baca di halaman kosong hanya menipu
                          if (a.s('isi').trim().isNotEmpty)
                            MetaPill(Icons.schedule_outlined,
                                readTime(a.s('isi'))),
                        ],
                      ),
                      const SizedBox(height: AppSpacing.md),
                      Text(ucFirst(a.s('judul')),
                          style: const TextStyle(
                              fontSize: 26,
                              height: 1.25,
                              letterSpacing: -.3,
                              fontWeight: FontWeight.w700)),
                      const SizedBox(height: AppSpacing.lg),
                      // pemisah gradient pendek, cermin kicker bar SectionHeader
                      Container(
                        height: 3,
                        width: 56,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(2),
                          gradient: const LinearGradient(
                              colors: [C.primary, C.gold]),
                        ),
                      ),
                      const SizedBox(height: AppSpacing.lg),
                      HtmlBody(a.s('isi')),
                      ...?footer?.call(context, a),
                    ],
                  ),
                ),
              ),
            ),
          ]);
        },
      ),
    );
  }
}

/// Gambar sampul: ketuk untuk membuka layar penuh yang bisa dizoom.
class _Cover extends StatelessWidget {
  const _Cover(this.url, {required this.tag});
  final String url;
  final String tag;

  @override
  Widget build(BuildContext context) => GestureDetector(
        onTap: () => openImage(context, url, tag: tag),
        child: Stack(fit: StackFit.expand, children: [
          Hero(tag: tag, child: NetImage(url)),
          // gradasi atas: tombol bulat tetap terbaca. Bawah: menyatu ke lembar
          DecoratedBox(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
                colors: [
                  C.ink.withValues(alpha: .35),
                  C.ink.withValues(alpha: 0),
                  C.ink.withValues(alpha: .30),
                ],
                stops: const [0, .45, 1],
              ),
            ),
          ),
          Positioned(
            right: AppSpacing.lg,
            bottom: 40, // di atas lengkungan lembar isi
            child: Semantics(
              button: true,
              label: 'Perbesar gambar sampul',
              child: Container(
                padding: const EdgeInsets.symmetric(
                    horizontal: AppSpacing.md, vertical: 6),
                decoration: BoxDecoration(
                  color: C.ink.withValues(alpha: .55),
                  borderRadius: BorderRadius.circular(AppRadius.chip),
                ),
                child: const Row(mainAxisSize: MainAxisSize.min, children: [
                  Icon(Icons.zoom_out_map, size: 14, color: Colors.white),
                  SizedBox(width: 6),
                  Text('Ketuk untuk perbesar',
                      style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Colors.white)),
                ]),
              ),
            ),
          ),
        ]),
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
        heroIcon: Icons.campaign_outlined,
        onBack: () => kembaliKeTab(context, kTabKabar),
        footer: (context, p) => [
          if (p.sn('dokumen_url') != null) ...[
            const SizedBox(height: AppSpacing.xl),
            _LampiranBox(p.s('dokumen_url')),
          ],
          _ArtikelLainnya(
            judul: 'Pengumuman Lainnya',
            fetch: api.announcements,
            kecuali: p.i('id'),
            badge: true,
            open: (id) => PengumumanDetailScreen(id: id),
          ),
        ],
      );
}

/// Berkas terlampir: kotak bertanda oranye supaya tidak tenggelam di ujung
/// teks — lampiran sering jadi alasan orang membuka halamannya.
class _LampiranBox extends StatelessWidget {
  const _LampiranBox(this.url,
      {this.label = 'LAMPIRAN', this.title = 'Lampiran Pengumuman'});
  final String url;
  final String label, title;

  @override
  Widget build(BuildContext context) => Container(
        padding: const EdgeInsets.all(AppSpacing.md),
        decoration: BoxDecoration(
          color: C.primary.withValues(alpha: .06),
          border: Border.all(color: C.primary.withValues(alpha: .30)),
          borderRadius: BorderRadius.circular(AppRadius.card),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(children: [
              Icon(Icons.attach_file,
                  size: 16,
                  color: Theme.of(context).brightness == Brightness.dark
                      ? C.primarySoft
                      : C.primaryInk),
              const SizedBox(width: 6),
              Text(label,
                  style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 1.2,
                      color: Theme.of(context).brightness == Brightness.dark
                          ? C.primarySoft
                          : C.primaryInk)),
            ]),
            const SizedBox(height: AppSpacing.sm),
            DocFileTile(url, title: title),
          ],
        ),
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

  /// Nama panjang jenis yang sedang dipilih; kosong bila filternya "Semua".
  String get _namaJenis {
    final i = _types.indexWhere((t) => t.s('id') == _type);
    return i < 0 ? '' : _types[i].s('nama');
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
                onSelected: (i) =>
                    setState(() => _type = i == 0 ? '' : _types[i - 1].s('id')),
              ),
            ),
          Expanded(
            child: PagedListView(
              key: ValueKey(_type),
              empty: 'Naskah akademik, rancangan peraturan, dan kajian hukum '
                  'akan tampil di sini begitu dipublikasikan.',
              // kosong karena filter ≠ kosong karena belum ada isinya:
              // "Muat ulang" tidak akan mengubah apa pun, yang dibutuhkan
              // pengguna adalah jalan keluar dari filternya
              emptyView: _type.isEmpty
                  ? null
                  : EmptyState(
                      icon: Icons.filter_alt_outlined,
                      title: 'Kategori ini masih kosong',
                      message: _namaJenis.isEmpty
                          ? 'Belum ada dokumen pada kategori yang dipilih.'
                          : 'Belum ada dokumen berjenis $_namaJenis. '
                              'Kategori lain mungkin sudah terisi.',
                      action: FilledButton.icon(
                        onPressed: () => setState(() => _type = ''),
                        icon: const Icon(Icons.apps, size: 18),
                        label: const Text('Lihat semua kategori'),
                      ),
                    ),
              fetch: (page) => api.legalInfo(type: _type, page: page),
              itemBuilder: (context, x, __) => InfoHukumCard(x),
            ),
          ),
        ],
      );
}

/// Kartu informasi hukum: rail warna + sampul/ikon, judul, lalu meta.
/// Judul di basis data ditulis KAPITAL SEMUA sehingga perlu titleCase, dan
/// kartu lama membuang `image_url`/`dokumen_url` yang sebenarnya dikirim API.
class InfoHukumCard extends StatelessWidget {
  const InfoHukumCard(this.x, {super.key});
  final Json x;

  @override
  Widget build(BuildContext context) {
    final img = x.sn('image_url');
    return Pressable(
      child: Card(
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => InfoHukumDetailScreen(id: x.i('id')))),
          child: IntrinsicHeight(
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Container(width: 4, color: C.primary),
                Padding(
                  padding: const EdgeInsets.only(
                      left: AppSpacing.md,
                      top: AppSpacing.md,
                      bottom: AppSpacing.md),
                  // sudut tipis: preview dokumen, bukan avatar
                  child: img != null
                      ? NetImage(img, width: 52, height: 68, radius: 4)
                      : Container(
                          width: 52,
                          height: 68,
                          decoration: BoxDecoration(
                            color: C.primary.withValues(alpha: .12),
                            borderRadius: BorderRadius.circular(4),
                          ),
                          child: const Icon(Icons.gavel_outlined,
                              color: C.primaryInk, size: 26),
                        ),
                ),
                Expanded(
                  child: Padding(
                    padding: const EdgeInsets.all(AppSpacing.md),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(titleCase(x.s('judul')),
                            maxLines: 3,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(
                                fontSize: 14,
                                height: 1.35,
                                fontWeight: FontWeight.w700)),
                        const SizedBox(height: AppSpacing.sm),
                        // Wrap, bukan Row: tiga pil pada layar sempit melipat
                        // ke baris berikutnya alih-alih meluber
                        Wrap(spacing: AppSpacing.sm, runSpacing: 6, children: [
                          JenisChip(x.sn('jenis_singkatan')),
                          MetaPill(
                              Icons.event_outlined, fmtDate(x.sn('tanggal'))),
                          if (x.sn('dokumen_url') != null)
                            const MetaPill(
                                Icons.picture_as_pdf_outlined, 'PDF'),
                        ]),
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

class InfoHukumDetailScreen extends StatelessWidget {
  const InfoHukumDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => ArticleDetail(
        kicker: 'Informasi Hukum',
        heroIcon: Icons.gavel_outlined,
        // judul di basis data KAPITAL SEMUA dan jenisnya bernama lain; dirapikan
        // di sini supaya tata letak artikel dipakai apa adanya
        load: () => api.legalInfoDetail(id).then((x) => {
              ...x,
              'judul': titleCase(x.s('judul')),
              'tag': x.sn('jenis_singkatan'),
            }),
        onBack: () => kembaliKeTab(context, kTabKabar),
        footer: (context, x) => [
          if (x.sn('dokumen_url') != null) ...[
            const SizedBox(height: AppSpacing.xl),
            _LampiranBox(x.s('dokumen_url'),
                label: 'DOKUMEN', title: x.s('judul')),
          ],
        ],
      );
}

// ---------------- VIDEO ----------------

class VideoCard extends StatelessWidget {
  const VideoCard(this.v, {super.key, this.width, this.replace = false});
  final Json v;
  final double? width;

  /// Dipakai baris "Video Lainnya": ganti halaman alih-alih menumpuknya, agar
  /// tombol kembali selalu pulang ke daftar video, bukan ke video sebelumnya.
  final bool replace;

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
            onTap: () {
              final route =
                  MaterialPageRoute(builder: (_) => VideoPlayerScreen(v));
              if (replace) {
                Navigator.pushReplacement(context, route);
              } else {
                Navigator.push(context, route);
              }
            },
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Stack(alignment: Alignment.center, children: [
                  AspectRatio(
                      aspectRatio: 16 / 9,
                      child: NetImage(v.sn('thumbnail_url'))),
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

  /// Sekali muat, disimpan supaya rebuild (mis. SnackBar) tidak memanggil ulang.
  late final Future<Paginated> _lainnya = api.videos();

  void _salinTautan() {
    final url = widget.v.sn('watch_url');
    if (url == null) return;
    Clipboard.setData(ClipboardData(text: url));
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Tautan video disalin.')),
    );
  }

  @override
  Widget build(BuildContext context) =>
      // pita pemutar gelap sampai ke belakang status bar, jadi jam & ikon
      // sinyal harus putih. AnnotatedRegion memulihkan gaya semula sendiri
      // begitu halaman ditutup.
      AnnotatedRegion<SystemUiOverlayStyle>(
        value: const SystemUiOverlayStyle(
          statusBarColor: Colors.transparent,
          statusBarIconBrightness: Brightness.light, // Android
          statusBarBrightness: Brightness.dark, // iOS
        ),
        child: Scaffold(
          body: ListView(
            padding: EdgeInsets.zero,
            children: [
              // pemutar berlatar gelap tanpa app bar: kesan "teater". Tombol
              // kembali duduk di pita hitam di atas pemutar, bukan melayang di
              // atasnya — supaya tidak menutupi gambar maupun kontrol YouTube
              ColoredBox(
                color: C.ink,
                child: SafeArea(
                  bottom: false,
                  child: Column(children: [
                    Align(
                      alignment: Alignment.centerLeft,
                      child: IconButton(
                        icon: const Icon(Icons.arrow_back, color: Colors.white),
                        tooltip: 'Kembali',
                        onPressed: () => Navigator.pop(context),
                      ),
                    ),
                    YoutubePlayer(controller: _c, aspectRatio: 16 / 9),
                  ]),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(AppSpacing.lg),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(children: [
                      const JenisChip('Video'),
                      const SizedBox(width: AppSpacing.sm),
                      Flexible(
                        child: MetaPill(Icons.event_outlined,
                            fmtDate(widget.v.sn('tanggal'))),
                      ),
                    ]),
                    const SizedBox(height: AppSpacing.md),
                    Text(widget.v.s('judul'),
                        style: const TextStyle(
                            fontSize: 20,
                            height: 1.3,
                            fontWeight: FontWeight.w700)),
                    const SizedBox(height: AppSpacing.lg),
                    Row(children: [
                      Expanded(
                        child: FilledButton.icon(
                          icon: const Icon(Icons.open_in_new, size: 18),
                          label: const Text('Buka di YouTube'),
                          onPressed: () =>
                              openUrl(context, widget.v.sn('watch_url')),
                        ),
                      ),
                      const SizedBox(width: AppSpacing.sm),
                      OutlinedButton(
                        onPressed: _salinTautan,
                        child: const Icon(Icons.link, size: 20),
                      ),
                    ]),
                  ],
                ),
              ),
              _VideoLainnya(future: _lainnya, kecuali: widget.v.i('id')),
              const SizedBox(height: AppSpacing.xl),
            ],
          ),
        ),
      );
}

/// Deretan video lain di bawah pemutar. Diam saja bila gagal atau kosong —
/// ini pelengkap, bukan isi utama halaman, jadi tidak perlu error state.
class _VideoLainnya extends StatelessWidget {
  const _VideoLainnya({required this.future, required this.kecuali});
  final Future<Paginated> future;
  final int kecuali;

  @override
  Widget build(BuildContext context) => FutureBuilder<Paginated>(
        future: future,
        builder: (context, snap) {
          final lain = (snap.data?.items ?? [])
              .where((v) => v.i('id') != kecuali)
              .toList();
          if (lain.isEmpty) return const SizedBox.shrink();
          return Rise(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Padding(
                  padding: EdgeInsets.symmetric(horizontal: AppSpacing.lg),
                  child: SectionHeader('Video Lainnya'),
                ),
                SizedBox(
                  height: 240,
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    padding:
                        const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
                    itemCount: lain.length,
                    separatorBuilder: (_, __) =>
                        const SizedBox(width: AppSpacing.md),
                    itemBuilder: (_, i) =>
                        VideoCard(lain[i], width: 244, replace: true),
                  ),
                ),
              ],
            ),
          );
        },
      );
}
