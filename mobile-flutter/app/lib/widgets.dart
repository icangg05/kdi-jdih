import 'dart:math' as math;

import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/gestures.dart' show kTouchSlop;
import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html_core/flutter_widget_from_html_core.dart';
import 'package:intl/intl.dart';
import 'package:share_plus/share_plus.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:url_launcher/url_launcher.dart';

import 'api.dart';
import 'theme.dart';

/// "2023-05-01" -> "1 Mei 2023". ponytail: selalu locale id, cukup untuk MVP.
String fmtDate(String? s) {
  final d = s == null ? null : DateTime.tryParse(s);
  return d == null ? (s ?? '') : DateFormat('d MMMM y', 'id').format(d);
}

/// Versi ringkas untuk kartu sempit: "12 Okt 2023".
String fmtDateShort(String? s) {
  final d = s == null ? null : DateTime.tryParse(s);
  return d == null ? (s ?? '') : DateFormat('d MMM y', 'id').format(d);
}

/// "pemberitahuan pelaksanaan" -> "Pemberitahuan pelaksanaan".
/// Judul & isi di basis data banyak yang diketik huruf kecil semua.
String ucFirst(String s) => s.isEmpty ? s : s[0].toUpperCase() + s.substring(1);

/// Estimasi waktu baca isi HTML, ~200 kata/menit. Minimal 1 menit.
String readTime(String html) {
  final kata = html
      .replaceAll(RegExp(r'<[^>]*>'), ' ')
      .split(RegExp(r'\s+'))
      .where((w) => w.isNotEmpty)
      .length;
  return '${(kata / 200).ceil().clamp(1, 99)} menit baca';
}

/// "PERATURAN WALI KOTA" -> "Peraturan Wali Kota".
String titleCase(String s) => s
    .toLowerCase()
    .split(RegExp(r'\s+'))
    .map((w) => w.isEmpty ? w : w[0].toUpperCase() + w.substring(1))
    .join(' ');

Future<void> openUrl(BuildContext context, String? url) async {
  if (url == null || url.isEmpty) return;
  final ok = await launchUrl(
    Uri.parse(url),
    mode: LaunchMode.externalApplication,
  );
  if (!ok && context.mounted) {
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Tidak dapat membuka tautan.')),
    );
  }
}

/// ID video YouTube (11 karakter) dari isian admin yang bisa berupa ID polos
/// atau tautan (youtu.be/…, watch?v=…, embed/…, shorts/…). null = bukan
/// YouTube, mis. ID video Facebook.
String? youtubeId(String raw) =>
    RegExp(r'(?:^|v=|youtu\.be/|embed/|shorts/)([A-Za-z0-9_-]{11})(?=$|[?&#/])')
        .firstMatch(raw.trim())
        ?.group(1);

/// Faktor pembesaran teks sistem untuk body (~14sp). Wadah bertinggi tetap
/// (kartu, karusel) mengalikan bagian teksnya dengan ini supaya isinya tidak
/// terpotong saat pengguna memperbesar huruf. Diukur pada 14sp, bukan
/// dikalikan langsung: skala Android 14 tidak linear dan angka besar seperti
/// tinggi kartu akan diperbesar terlalu sedikit.
double skalaTeks(BuildContext context) =>
    MediaQuery.textScalerOf(context).scale(14) / 14;

// ============================================================
//  ADAPTASI LEBAR — kelas lebar jendela Material 3
// ============================================================
// Struktur ditentukan lebar JENDELA, bukan model perangkat: tablet, foldable
// terbuka, HP lanskap, dan split-screen tertangani dengan aturan yang sama.

/// Ringkas < 600 <= sedang < 840 <= lebar.
const kLebarSedang = 600.0;
const kLebarLebar = 840.0;

/// Kolom teks bacaan & formulir: ±70 karakter serif 16,5.
const kKolomBaca = 720.0;

/// Grid kartu tidak melebar melewati ini; di layar sangat lebar isinya
/// berada di tengah.
const kKolomGrid = 1200.0;

/// Jumlah kolom kartu untuk lebar [w]: kartu dokumen/berita butuh ±340dp.
int kolomUntuk(double w) => w >= 1200 ? 3 : (w >= 720 ? 2 : 1);

/// Padding yang menaruh kolom isi selebar [maks] di tengah TANPA
/// mempersempit area gulir: daftar tetap bisa digulir dari tepi layar.
EdgeInsets padTengah(
  BuildContext context, {
  double maks = kKolomBaca,
  double atas = AppSpacing.lg,
  double bawah = AppSpacing.lg,
}) {
  final h = math.max(
    AppSpacing.lg,
    (MediaQuery.sizeOf(context).width - maks) / 2,
  );
  return EdgeInsets.fromLTRB(h, atas, h, bawah);
}

/// Isi di luar daftar (lembar detail, bilah aksi, komposer) dibatasi
/// selebar [maks] dan diletakkan di tengah.
class Kolom extends StatelessWidget {
  const Kolom({super.key, this.maks = kKolomBaca, required this.child});
  final double maks;
  final Widget child;

  @override
  Widget build(BuildContext context) => Align(
    alignment: Alignment.topCenter,
    child: ConstrainedBox(
      constraints: BoxConstraints(maxWidth: maks),
      child: child,
    ),
  );
}

/// Bagian layar (panel kanan rail, panel daftar/detail) yang melaporkan
/// lebarnya sendiri lewat MediaQuery, supaya [padTengah] dan [kolomUntuk]
/// di dalamnya menghitung dari lebar panel, bukan lebar jendela.
class Panel extends StatelessWidget {
  const Panel({super.key, required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) => LayoutBuilder(
    builder: (context, box) => MediaQuery(
      data: MediaQuery.of(context)
          .copyWith(size: Size(box.maxWidth, box.maxHeight)),
      child: child,
    ),
  );
}

/// Kartu berderet [kolom] per baris, setinggi kartu tertinggi di barisnya.
class BarisKartu extends StatelessWidget {
  const BarisKartu({super.key, required this.kolom, required this.children});
  final int kolom;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) => IntrinsicHeight(
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        for (var i = 0; i < kolom; i++) ...[
          if (i > 0) const SizedBox(width: AppSpacing.md),
          Expanded(child: i < children.length ? children[i] : const SizedBox()),
        ],
      ],
    ),
  );
}

/// Tab shell utama yang sedang aktif (0 Beranda, 1 Dokumen, 2 Cari, 3 Kabar,
/// 4 Menu). Halaman detail memakai ini untuk pulang ke tab tertentu.
final rootTab = ValueNotifier<int>(0);

const kTabKabar = 3;

/// Pulang ke shell utama pada [tab]. Semua route yang ditumpuk ditutup, jadi
/// navigasi bawah tetap ada — mem-push halaman indeks sebagai Scaffold baru
/// justru menyembunyikannya karena nav bar hanya milik shell.
void kembaliKeTab(BuildContext context, int tab) {
  rootTab.value = tab;
  Navigator.popUntil(context, (r) => r.isFirst);
}

// ============================================================
//  MOTION — hormati MediaQuery.disableAnimations
// ============================================================

/// Entrance "rise": fade + naik tipis, easeOutCubic. Cermin animate-rise web.
/// Dipakai sekali per isi yang baru tiba, bukan berurutan per kartu.
class Rise extends StatefulWidget {
  const Rise({super.key, required this.child, this.delayMs = 0});
  final Widget child;
  final int delayMs;

  @override
  State<Rise> createState() => _RiseState();
}

class _RiseState extends State<Rise> {
  bool _shown = false;

  @override
  void initState() {
    super.initState();
    if (widget.delayMs == 0) {
      WidgetsBinding.instance.addPostFrameCallback((_) {
        if (mounted) setState(() => _shown = true);
      });
    } else {
      Future.delayed(Duration(milliseconds: widget.delayMs), () {
        if (mounted) setState(() => _shown = true);
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (MediaQuery.of(context).disableAnimations) return widget.child;
    return AnimatedSlide(
      offset: _shown ? Offset.zero : const Offset(0, .015),
      duration: const Duration(milliseconds: 240),
      curve: Curves.easeOutCubic,
      child: AnimatedOpacity(
        opacity: _shown ? 1 : 0,
        duration: const Duration(milliseconds: 200),
        curve: Curves.easeOut,
        child: widget.child,
      ),
    );
  }
}

/// Press feedback fisik: susut tipis (1,5%) saat ditekan, pelengkap riak
/// InkWell. Bungkus kartu/tile interaktif; child tetap memegang onTap-nya.
class Pressable extends StatefulWidget {
  const Pressable({super.key, required this.child});
  final Widget child;

  @override
  State<Pressable> createState() => _PressableState();
}

class _PressableState extends State<Pressable> {
  bool _down = false;
  Offset _awal = Offset.zero;

  void _lepas() {
    if (_down) setState(() => _down = false);
  }

  @override
  Widget build(BuildContext context) {
    if (MediaQuery.of(context).disableAnimations) return widget.child;
    return Listener(
      onPointerDown: (e) => setState(() {
        _down = true;
        _awal = e.position;
      }),
      // jari yang mulai menggulir daftar bukan lagi menekan kartu: tanpa
      // ini kartu tetap mengecil sepanjang guliran
      onPointerMove: (e) {
        if ((e.position - _awal).distance > kTouchSlop) _lepas();
      },
      onPointerUp: (_) => _lepas(),
      onPointerCancel: (_) => _lepas(),
      child: AnimatedScale(
        scale: _down ? .985 : 1,
        duration: const Duration(milliseconds: 120),
        curve: Curves.easeOut,
        child: widget.child,
      ),
    );
  }
}

/// Denyut opacity untuk isi yang sedang diproses (gambar dimuat, AI menelusuri).
/// Kerangka muat halaman/daftar memakai Skeletonizer, bukan ini.
class Pulse extends StatefulWidget {
  const Pulse({super.key, required this.child});
  final Widget child;

  @override
  State<Pulse> createState() => _PulseState();
}

class _PulseState extends State<Pulse> with SingleTickerProviderStateMixin {
  late final AnimationController _c = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 900),
  )..repeat(reverse: true);

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (MediaQuery.of(context).disableAnimations) return widget.child;
    return FadeTransition(
      opacity: Tween(begin: .4, end: .9).animate(_c),
      child: widget.child,
    );
  }
}

/// Baris kerangka: tulang Skeletonizer bersudut chip, diwarnai shimmer.
Widget _bar(double w, [double h = 12]) =>
    Bone(width: w, height: h, uniRadius: AppRadius.chip);

/// Skeleton daftar: kartu pulse berbentuk seperti isi aslinya (thumbnail +
/// baris teks), bukan spinner dan bukan kotak polos — kotak polos berwarna
/// samar terbaca sebagai halaman kosong, bukan sebagai "sedang memuat".
class SkeletonList extends StatelessWidget {
  const SkeletonList({
    super.key,
    this.count = 5,
    this.height = 104,
    this.padding = const EdgeInsets.all(16),
  });
  final int count;
  final double height;
  final EdgeInsets padding;

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    return Skeletonizer.zone(
      child: ListView.separated(
        padding: padding,
        physics: const NeverScrollableScrollPhysics(),
        // sering dipakai sebagai anak ListView lain (mis. skeleton jawaban AI):
        // tanpa shrinkWrap tingginya tak terbatas dan layout meledak
        shrinkWrap: true,
        itemCount: count,
        separatorBuilder: (_, _) => const SizedBox(height: 12),
        itemBuilder: (_, _) => Container(
          height: height,
          decoration: BoxDecoration(
            color: dark ? C.darkSurface : C.lightSurface,
            border: Border.all(color: dark ? C.darkLine : C.lightLine),
            borderRadius: BorderRadius.circular(AppRadius.card),
          ),
          child: Row(
            children: [
              Bone(
                width: height * .9,
                height: height,
                uniRadius: AppRadius.card,
              ),
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 12),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _bar(double.infinity, 14),
                      const SizedBox(height: 8),
                      _bar(70, 10),
                      const SizedBox(height: 8),
                      _bar(double.infinity, 10),
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

/// Kerangka muat halaman detail artikel: blok hero, pil meta, judul, lalu
/// baris paragraf — mengikuti tata letak ArticleDetail, bukan daftar kartu.
class ArticleSkeleton extends StatelessWidget {
  const ArticleSkeleton({super.key});

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final surface = dark ? C.darkSurface : C.lightSurface;

    return Skeletonizer.zone(
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          const Bone(height: 320, width: double.infinity),
          Transform.translate(
            offset: const Offset(0, -24),
            child: Container(
              decoration: BoxDecoration(
                color: surface,
                borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(AppRadius.sheet),
                ),
              ),
              padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg,
                48,
                AppSpacing.lg,
                AppSpacing.xxl,
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      _bar(64, 22),
                      const SizedBox(width: AppSpacing.sm),
                      _bar(104, 22),
                      const SizedBox(width: AppSpacing.sm),
                      _bar(88, 22),
                    ],
                  ),
                  const SizedBox(height: AppSpacing.md),
                  _bar(double.infinity, 26),
                  const SizedBox(height: AppSpacing.sm),
                  _bar(200, 26),
                  const SizedBox(height: AppSpacing.lg),
                  for (var i = 0; i < 6; i++) ...[
                    _bar(i == 5 ? 180 : double.infinity),
                    const SizedBox(height: AppSpacing.md),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// Kerangka muat halaman detail dokumen: hero gelap, chip status, judul,
/// baris metadata, lalu bilah aksi — mengikuti tata letak detail peraturan.
class DocDetailSkeleton extends StatelessWidget {
  const DocDetailSkeleton({super.key});

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final surface = dark ? C.darkSurface : C.lightSurface;

    return Skeletonizer.zone(
      child: Column(
        children: [
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                const SizedBox(
                  height: 136,
                  child: ColoredBox(color: C.accentDeep),
                ),
                // lembar isi menimpa hero, sama seperti halaman aslinya
                Transform.translate(
                  offset: const Offset(0, -24),
                  child: Container(
                    decoration: BoxDecoration(
                      color: surface,
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
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            _bar(88, 22),
                            const SizedBox(width: AppSpacing.sm),
                            _bar(72, 22),
                          ],
                        ),
                        const SizedBox(height: AppSpacing.md),
                        _bar(double.infinity, 24),
                        const SizedBox(height: AppSpacing.sm),
                        _bar(220, 24),
                        const SizedBox(height: AppSpacing.md),
                        Row(
                          children: [
                            _bar(96, 20),
                            const SizedBox(width: AppSpacing.sm),
                            _bar(88, 20),
                          ],
                        ),
                        const SizedBox(height: AppSpacing.lg),
                        // pil tab
                        Row(
                          children: [
                            _bar(84, 32),
                            const SizedBox(width: AppSpacing.sm),
                            _bar(96, 32),
                            const SizedBox(width: AppSpacing.sm),
                            _bar(80, 32),
                          ],
                        ),
                        const SizedBox(height: AppSpacing.lg),
                        // kartu spesifikasi 2 kolom
                        Row(
                          children: [
                            Expanded(child: _bar(double.infinity, 100)),
                            const SizedBox(width: AppSpacing.sm),
                            Expanded(child: _bar(double.infinity, 100)),
                          ],
                        ),
                        const SizedBox(height: AppSpacing.sm),
                        Row(
                          children: [
                            Expanded(child: _bar(double.infinity, 100)),
                            const SizedBox(width: AppSpacing.sm),
                            Expanded(child: _bar(double.infinity, 100)),
                          ],
                        ),
                        const SizedBox(height: AppSpacing.xl),
                        // tabel metadata: label pendek + nilai panjang
                        for (var i = 0; i < 5; i++) ...[
                          Row(
                            children: [
                              _bar(96),
                              const SizedBox(width: AppSpacing.md),
                              Expanded(child: _bar(double.infinity)),
                            ],
                          ),
                          const SizedBox(height: AppSpacing.md),
                        ],
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
          // bilah aksi tetap di bawah, sama seperti halaman aslinya
          Container(
            color: surface,
            padding: const EdgeInsets.all(AppSpacing.lg),
            child: Row(
              children: [
                _bar(72, 40),
                const SizedBox(width: AppSpacing.md),
                Expanded(child: _bar(double.infinity, 40)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

// ============================================================
//  SIGNATURE BRAND
// ============================================================

/// AppBar dengan garis gradien amber->biru di bawahnya (signature brand,
/// sama dengan gradien from-primary to-accent di web).
/// `tabs` opsional: TabBar dirender di atas garis gradient.
class BrandAppBar extends StatelessWidget implements PreferredSizeWidget {
  const BrandAppBar(
    this.title, {
    super.key,
    this.actions,
    this.tabs,
    this.showBack,
    this.leading,
  });
  final String title;
  final List<Widget>? actions;
  final TabBar? tabs;
  final bool? showBack;
  final Widget? leading;

  @override
  Size get preferredSize =>
      Size.fromHeight(kToolbarHeight + 3 + (tabs?.preferredSize.height ?? 0));

  @override
  Widget build(BuildContext context) => AppBar(
    // satu baris: judul panjang atau huruf besar tidak boleh terpotong
    // setengah oleh tinggi toolbar
    title: Text(title, maxLines: 1, overflow: TextOverflow.ellipsis),
    actions: actions,
    leading: leading,
    automaticallyImplyLeading: showBack ?? true,
    bottom: PreferredSize(
      preferredSize: Size.fromHeight(3 + (tabs?.preferredSize.height ?? 0)),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          ?tabs,
          const SizedBox(
            height: 3,
            width: double.infinity,
            child: DecoratedBox(
              decoration: BoxDecoration(
                gradient: LinearGradient(colors: [C.primary, C.accent]),
              ),
            ),
          ),
        ],
      ),
    ),
  );
}

// ============================================================
//  ELEMEN UI
// ============================================================

class NetImage extends StatelessWidget {
  const NetImage(
    this.url, {
    super.key,
    this.width,
    this.height,
    this.radius = 0,
  });
  final String? url;
  final double? width, height;
  final double radius;

  @override
  Widget build(BuildContext context) {
    // Kotak abu untuk denyut saat memuat — alignment (bukan width/height)
    // supaya kotak isi mengikuti kotak pembungkus.
    final ph = Container(
      alignment: Alignment.center,
      color: Theme.of(context).brightness == Brightness.dark
          ? C.darkSubtle
          : C.lightSubtle,
      child: const Icon(Icons.image_outlined, color: C.lightLineStrong),
    );
    // Sampul cadangan saat berita/pengumuman tidak punya gambar atau gagal
    // dimuat. Aset lokal, jadi tetap tampil tanpa jaringan.
    const fallback = Image(
      image: AssetImage('assets/img/default-cover.webp'),
      fit: BoxFit.cover,
    );
    // Ukuran dipasang di SizedBox, bukan di gambarnya. Bila width diteruskan ke
    // CachedNetworkImage, intrinsic height-nya jadi width/rasio gambar — dan
    // IntrinsicHeight pada kartu daftar ikut memakainya, sehingga kartu
    // meregang setinggi foto potret alih-alih setinggi kolom teks.
    // Decode seukuran tampilan, bukan ukuran berkas: foto berita di server
    // 470-1400px, sedangkan thumbnail daftar hanya 110dp. Lebar 2x sisi kotak
    // cukup untuk BoxFit.cover pada foto hingga rasio 2:1; tanpa ukuran,
    // paling lebar selebar layar. Rasio tetap (hanya lebar yang dibatasi).
    final sisi = math.max(width ?? 0, height ?? 0);
    final lebarDecode =
        ((sisi > 0 ? sisi * 2 : MediaQuery.sizeOf(context).width) *
                MediaQuery.devicePixelRatioOf(context))
            .round();
    final img = (url == null || url!.isEmpty)
        ? fallback
        : CachedNetworkImage(
            imageUrl: url!,
            memCacheWidth: lebarDecode,
            fit: BoxFit.cover,
            placeholder: (_, _) => Pulse(child: ph),
            errorWidget: (_, _, _) => fallback,
          );
    return SizedBox(
      width: width,
      height: height,
      child: radius > 0
          ? ClipRRect(borderRadius: BorderRadius.circular(radius), child: img)
          : img,
    );
  }
}

/// Buka gambar layar penuh: cubit untuk memperbesar, ketuk untuk menutup.
/// [tag] harus sama dengan Hero pada gambar asal agar transisinya menyatu.
void openImage(BuildContext context, String? url, {required String tag}) {
  if (url == null || url.isEmpty) return;
  Navigator.push(
    context,
    PageRouteBuilder(
      opaque: false,
      barrierColor: C.ink.withValues(alpha: .94),
      pageBuilder: (_, _, _) => _ImageViewer(url, tag: tag),
    ),
  );
}

class _ImageViewer extends StatelessWidget {
  const _ImageViewer(this.url, {required this.tag});
  final String url;
  final String tag;

  @override
  Widget build(BuildContext context) => Scaffold(
    backgroundColor: Colors.transparent,
    body: Stack(
      children: [
        GestureDetector(
          onTap: () => Navigator.pop(context),
          child: InteractiveViewer(
            minScale: 1,
            maxScale: 5,
            child: Center(
              child: Hero(
                tag: tag,
                child: CachedNetworkImage(
                  imageUrl: url,
                  fit: BoxFit.contain,
                  // versi selebar layar dari NetImage halaman asal sudah ada
                  // di memori: tampil seketika selama Hero terbang, lalu
                  // diganti resolusi penuh untuk diperbesar
                  placeholder: (context, _) => CachedNetworkImage(
                    imageUrl: url,
                    fit: BoxFit.contain,
                    memCacheWidth:
                        (MediaQuery.sizeOf(context).width *
                                MediaQuery.devicePixelRatioOf(context))
                            .round(),
                    placeholder: (_, _) => const Center(
                      child: CircularProgressIndicator(color: C.primary),
                    ),
                  ),
                  errorWidget: (_, _, _) => const Icon(
                    Icons.broken_image_outlined,
                    color: Colors.white54,
                    size: 48,
                  ),
                ),
              ),
            ),
          ),
        ),
        SafeArea(
          child: Align(
            alignment: Alignment.topRight,
            child: Padding(
              padding: const EdgeInsets.all(AppSpacing.sm),
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white),
                tooltip: 'Tutup gambar',
                onPressed: () => Navigator.pop(context),
              ),
            ),
          ),
        ),
      ],
    ),
  );
}

/// Tiga makna status hukum yang dipakai seragam oleh badge maupun grafik.
enum StatusKind { berlaku, diubah, dicabut, lain }

/// Satu-satunya tempat teks status dari basis data dipetakan ke maknanya.
/// Urutan pengecekan penting: "tidak berlaku" harus menang atas "berlaku".
StatusKind statusKindOf(String? status) {
  final s = (status ?? '').toLowerCase();
  if (s.isEmpty) return StatusKind.lain;
  if (s.contains('tidak berlaku') || s.contains('dicabut')) {
    return StatusKind.dicabut;
  }
  if (s.contains('diubah') ||
      s.contains('mengubah') ||
      s.contains('mencabut')) {
    return StatusKind.diubah;
  }
  if (s.contains('berlaku')) return StatusKind.berlaku;
  return StatusKind.lain;
}

/// Pasangan warna (teks, latar) status hukum. Palette semantik sendiri, bukan
/// warna brand; satu pemetaan dipakai badge maupun rail kartu dokumen.
(Color, Color) statusColors(String? status) => switch (statusKindOf(status)) {
  StatusKind.dicabut => (C.statusRevoked, C.statusRevokedBg),
  StatusKind.diubah => (C.statusChanged, C.statusChangedBg),
  StatusKind.berlaku => (C.statusActive, C.statusActiveBg),
  StatusKind.lain => (C.lightInkMuted, C.lightSubtle),
};

class StatusBadge extends StatelessWidget {
  const StatusBadge(this.status, {super.key});
  final String? status;

  @override
  Widget build(BuildContext context) {
    if (status == null || status!.isEmpty) return const SizedBox.shrink();
    final (fg, bg) = statusColors(status);
    final dark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: dark ? fg.withValues(alpha: .18) : bg,
        borderRadius: BorderRadius.circular(AppRadius.chip),
      ),
      child: Text(
        status!,
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        style: T.labelKecil.copyWith(color: dark ? bg : fg),
      ),
    );
  }
}

class JenisChip extends StatelessWidget {
  const JenisChip(this.label, {super.key});
  final String? label;

  @override
  Widget build(BuildContext context) {
    final l = label?.trim() ?? '';
    final text = (l.isEmpty || l == '-') ? 'Dokumen Peraturan' : titleCase(l);
    // informasi, bukan aksi: biru. Amber disisakan untuk yang bisa ditekan
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: C.accent.withValues(alpha: .08),
        borderRadius: BorderRadius.circular(AppRadius.chip),
      ),
      child: Text(
        text,
        // tag panjang ("Bagian Hukum Sekretariat Daerah Kota Kendari")
        // tidak boleh melipat jadi dua baris dan mendominasi kartu
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        style: T.labelKecil.copyWith(
          color: Theme.of(context).brightness == Brightness.dark
              ? C.accentOnDark
              : C.accent,
        ),
      ),
    );
  }
}

/// Judul section dengan kicker bar oranye (signature brand).
class SectionHeader extends StatelessWidget {
  const SectionHeader(this.title, {super.key, this.onSeeAll});
  final String title;
  final VoidCallback? onSeeAll;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(top: 28, bottom: 14),
    child: LayoutBuilder(
      builder: (context, box) => Row(
        children: [
          const SizedBox(
            width: 4,
            height: 20,
            child: ColoredBox(color: C.primary),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              title,
              style: Theme.of(context).textTheme.titleLarge
                  ?.copyWith(fontWeight: FontWeight.w700, letterSpacing: -.3),
            ),
          ),
          // tautan selebar isinya, paling banyak 45% baris: dengan huruf
          // besar ia menyusut (elipsis) tanpa mencuri lebar judul. Flexible
          // membagi ruang 50:50 dan menaruh tautan di tengah baris.
          if (onSeeAll != null)
            ConstrainedBox(
              constraints: BoxConstraints(maxWidth: box.maxWidth * .45),
              child: TextButton.icon(
                onPressed: onSeeAll,
                iconAlignment: IconAlignment.end,
                icon: const Icon(
                  Icons.arrow_forward,
                  size: 15,
                  color: C.accent,
                ),
                label: const Text(
                  'Lihat semua',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: C.accent,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),
        ],
      ),
    ),
  );
}

/// Ikon dalam kotak tinted bersudut 4dp, vocabulary tile hub. Bawaannya
/// biru: ikon informasi/sekunder, bukan ajakan bertindak.
class IconTile extends StatelessWidget {
  const IconTile(this.icon, {super.key, this.color = C.accent, this.size = 44});
  final IconData icon;
  final Color color;
  final double size;

  @override
  Widget build(BuildContext context) => Container(
    width: size,
    height: size,
    decoration: BoxDecoration(
      color: color.withValues(alpha: .12),
      borderRadius: BorderRadius.circular(AppRadius.chip),
    ),
    child: Icon(icon, color: color, size: size * .52),
  );
}

const kLangs = {'id': 'Indonesia', 'en': 'English', 'zh': '中文', 'ko': '한국어'};

/// Dialog bahasa konten; dipakai dari beranda maupun tab Lainnya.
/// Pil bahasa di header: bendera huruf + kode aktif + caret pemicu dialog.
class LangPill extends StatelessWidget {
  const LangPill({super.key, required this.onTap});
  final VoidCallback onTap;

  // area sentuh 48dp mengelilingi pil 36dp: ketukan yang meleset sedikit
  // dari pil tetap membuka pemilih bahasa
  @override
  Widget build(BuildContext context) => GestureDetector(
    behavior: HitTestBehavior.opaque,
    onTap: onTap,
    child: Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: _pil(),
    ),
  );

  // kontrol sekunder: biru, supaya amber di header tidak bersaing dengan
  // aksi utama halaman
  Widget _pil() => Pressable(
    child: Material(
      color: C.accent.withValues(alpha: .08),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.chip),
        side: BorderSide(color: C.accent.withValues(alpha: .35)),
      ),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: SizedBox(
          height: 36,
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                // kode bahasa saja ("ID") tidak bermakna bagi pembaca layar
                const Icon(
                  Icons.language,
                  size: 16,
                  color: C.accent,
                  semanticLabel: 'Bahasa konten',
                ),
                const SizedBox(width: 6),
                Text(
                  langNotifier.value.toUpperCase(),
                  style: T.label.copyWith(
                    fontWeight: FontWeight.w700,
                    color: C.accent,
                  ),
                ),
                const Icon(Icons.expand_more, size: 16, color: C.accent),
              ],
            ),
          ),
        ),
      ),
    ),
  );
}

Future<void> pickLang(BuildContext context) => showDialog(
  context: context,
  builder: (c) => SimpleDialog(
    title: const Text('Bahasa Konten'),
    children: [
      RadioGroup<String>(
        groupValue: langNotifier.value,
        onChanged: (v) {
          langNotifier.value = v!;
          Navigator.pop(c);
        },
        child: Column(
          children: [
            for (final e in kLangs.entries)
              RadioListTile<String>(title: Text(e.value), value: e.key),
          ],
        ),
      ),
    ],
  ),
);

/// Pil metadata kecil: ikon + label (jumlah dilihat/diunduh, dll).
class MetaPill extends StatelessWidget {
  const MetaPill(this.icon, this.label, {super.key});
  final IconData icon;
  final String label;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
    decoration: BoxDecoration(
      color: C.lightSubtle,
      borderRadius: BorderRadius.circular(AppRadius.chip),
    ),
    child: Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, size: 12, color: C.lightInkMuted),
        const SizedBox(width: 4),
        // Flexible: tanpa ini Row memberi teks lebar tak terbatas, elipsis
        // tidak pernah terpicu, dan bidang hukum yang panjang meluap
        Flexible(
          child: Text(
            label,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: T.isiKecil.copyWith(color: C.lightInkMuted),
          ),
        ),
      ],
    ),
  );
}

/// Keadaan kosong yang mengajarkan langkah berikutnya, bukan sekadar
/// "tidak ada data".
class EmptyState extends StatelessWidget {
  const EmptyState({
    super.key,
    required this.icon,
    required this.title,
    required this.message,
    this.action,
  });
  final IconData icon;
  final String title, message;
  final Widget? action;

  @override
  Widget build(BuildContext context) => Center(
    // Rise: keadaan kosong sering muncul setelah jeda muat, jadi kalau
    // ditampilkan mendadak terbaca seperti error yang menyalak
    child: Rise(
      child: SingleChildScrollView(
        padding: const EdgeInsets.all(AppSpacing.xl),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            IconTile(icon, size: 64),
            const SizedBox(height: AppSpacing.lg),
            Text(title, textAlign: TextAlign.center, style: T.judul),
            const SizedBox(height: AppSpacing.sm),
            ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 320),
              child: Text(
                message,
                textAlign: TextAlign.center,
                style: T.isiKecil.copyWith(color: C.lightInkMuted),
              ),
            ),
            if (action != null) ...[
              const SizedBox(height: AppSpacing.lg),
              action!,
            ],
          ],
        ),
      ),
    ),
  );
}

/// Hasil pencarian/filter yang kosong. Beda dari "belum ada isinya": yang
/// dibutuhkan pengguna adalah jalan keluar dari kata kuncinya, bukan tombol
/// muat ulang yang tidak akan mengubah apa pun.
class NoResults extends StatelessWidget {
  const NoResults(this.query, {super.key, this.saran, this.actions = const []});
  final String query;

  /// Kalimat saran pengganti; default menyarankan kata kunci lebih pendek.
  final String? saran;
  final List<Widget> actions;

  @override
  Widget build(BuildContext context) => EmptyState(
    icon: Icons.search_off,
    title: 'Tidak ditemukan',
    message:
        'Tidak ada yang cocok dengan "$query". '
        '${saran ?? 'Coba kata kunci yang lebih pendek, periksa ejaannya, '
                'atau ganti kategori.'}',
    action: actions.isEmpty
        ? null
        : Wrap(
            spacing: AppSpacing.sm,
            runSpacing: AppSpacing.sm,
            alignment: WrapAlignment.center,
            children: actions,
          ),
  );
}

/// Ajakan ke Asisten AI: satu-satunya blok gelap di beranda selain semboyan
/// adat, dipakai sekali untuk menandai aksi paling menonjol.
class AiPromoCard extends StatelessWidget {
  const AiPromoCard({super.key, required this.onTap});
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Pressable(
    child: Material(
      color: C.ink,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
      ),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        // blok gelap sudah cukup menonjol; tanpa garis oranye di tepi kiri
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(Icons.auto_awesome, size: 18, color: C.primary),
                  SizedBox(width: AppSpacing.sm),
                  Flexible(
                    child: Text(
                      'Tanya AI',
                      style: T.judul.copyWith(color: C.onDark),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: AppSpacing.xs),
              Text(
                'Tanyakan dengan bahasa sehari-hari. Jawabannya berupa '
                'percakapan, lengkap dengan dokumen hukum yang relevan.',
                style: T.isi.copyWith(color: C.onDark.withValues(alpha: .82)),
              ),
              const SizedBox(height: AppSpacing.md),
              FilledButton.icon(
                onPressed: onTap,
                icon: const Icon(Icons.arrow_forward, size: 18),
                iconAlignment: IconAlignment.end,
                label: const Text('Mulai bertanya'),
              ),
            ],
          ),
        ),
      ),
    ),
  );
}

/// Kartu kabar bergambar. Dipakai berderet mendatar di beranda dan
/// sebagai kartu sorotan di indeks Kabar.
class NewsTile extends StatelessWidget {
  const NewsTile(
    this.item, {
    super.key,
    required this.onTap,
    this.width,
    this.featured = false,
  });
  final Json item;
  final VoidCallback onTap;
  final double? width;

  /// Varian sorotan: gambar lebih tinggi, ada ringkasan dan penanda "Terbaru".
  final bool featured;

  @override
  Widget build(BuildContext context) => SizedBox(
    width: width,
    child: Pressable(
      child: Card(
        clipBehavior: Clip.antiAlias,
        child: InkWell(
          onTap: onTap,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Stack(
                children: [
                  AspectRatio(
                    aspectRatio: featured ? 16 / 9 : 3 / 2,
                    child: NetImage(item.sn('image_url')),
                  ),
                  if (featured)
                    Positioned(
                      left: AppSpacing.md,
                      top: AppSpacing.md,
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: AppSpacing.sm,
                          vertical: 4,
                        ),
                        decoration: BoxDecoration(
                          color: C.primary,
                          borderRadius: BorderRadius.circular(AppRadius.chip),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.star, size: 12, color: C.ink),
                            SizedBox(width: 4),
                            Text(
                              'Terbaru',
                              style: T.labelKecil.copyWith(color: C.ink),
                            ),
                          ],
                        ),
                      ),
                    ),
                ],
              ),
              Padding(
                padding: const EdgeInsets.all(AppSpacing.md),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // satu Text, bukan Row: tanggal + tag panjang tidak
                    // bisa meluber pada kartu sempit
                    Text.rich(
                      TextSpan(
                        children: [
                          TextSpan(
                            text: fmtDateShort(item.sn('tanggal'))
                                .toUpperCase(),
                          ),
                          if (item.sn('tag') != null)
                            TextSpan(
                              text: ' · ${titleCase(item.s('tag'))}',
                              style: const TextStyle(
                                fontWeight: FontWeight.w700,
                                color: C.primaryInk,
                              ),
                            ),
                        ],
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: T.labelKecil.copyWith(color: C.lightInkMuted),
                    ),
                    const SizedBox(height: AppSpacing.xs),
                    Text(
                      ucFirst(item.s('judul')),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: featured ? T.subjudul : T.judulItem,
                    ),
                    if (featured && item.sn('ringkasan') != null) ...[
                      const SizedBox(height: 6),
                      Text(
                        ucFirst(item.s('ringkasan')),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: T.isiKecil.copyWith(color: C.lightInkMuted),
                      ),
                    ],
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

/// Kartu spesifikasi: ikon + nilai + label. Dipakai berjajar pada detail.
class SpecCard extends StatelessWidget {
  const SpecCard(this.icon, this.value, this.label, {super.key, this.onTap});
  final IconData icon;
  final String value, label;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) => Card(
    child: InkWell(
      borderRadius: BorderRadius.circular(AppRadius.card),
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.md,
          vertical: AppSpacing.sm,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 20, color: C.accent),
            const SizedBox(height: AppSpacing.sm),
            // dua baris, bukan satu: "Peraturan Walikota" dan "Tidak
            // Berlaku" tidak muat di satu baris kartu selebar ini
            Text(
              value,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
              style: T.label.copyWith(fontWeight: FontWeight.w700),
            ),
            Text(
              label,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: T.isiKecil.copyWith(color: C.lightInkMuted),
            ),
          ],
        ),
      ),
    ),
  );
}

/// Tombol ikon berlatar putih di atas gambar hero (back / bagikan).
class RoundIconButton extends StatelessWidget {
  const RoundIconButton(
    this.icon, {
    super.key,
    required this.onPressed,
    this.tooltip,
  });
  final IconData icon;
  final VoidCallback onPressed;
  final String? tooltip;

  @override
  Widget build(BuildContext context) => Material(
    color: C.lightSurface,
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(AppRadius.button),
    ),
    elevation: 2,
    shadowColor: C.ink.withValues(alpha: .25),
    child: IconButton(
      tooltip: tooltip,
      icon: Icon(icon, size: 20, color: C.ink),
      onPressed: onPressed,
    ),
  );
}

/// Tombol bagikan di hero detail: tautan halaman web publik (`share_url` dari
/// API). Tidak tampil bila server belum mengirimkannya.
class ShareAction extends StatelessWidget {
  const ShareAction(this.d, {super.key});
  final Json d;

  @override
  Widget build(BuildContext context) {
    final url = d.sn('share_url');
    if (url == null) return const SizedBox.shrink();
    final j = d.s('judul');
    // judul dokumen tersimpan KAPITAL SEMUA; judul berita dibiarkan apa adanya
    final judul = j == j.toUpperCase() ? titleCase(j) : j;
    return Padding(
      padding: const EdgeInsets.only(right: AppSpacing.lg),
      child: RoundIconButton(
        Icons.share_outlined,
        tooltip: 'Bagikan',
        onPressed: () {
          final box = context.findRenderObject() as RenderBox?;
          SharePlus.instance.share(
            ShareParams(
              text: '$judul\n$url',
              subject: judul,
              // iPad wajib punya titik asal popover lembar berbagi
              sharePositionOrigin: box == null
                  ? null
                  : box.localToGlobal(Offset.zero) & box.size,
            ),
          );
        },
      ),
    );
  }
}

/// Teks panjang yang dipotong sampai [max] baris, dengan tombol Selengkapnya.
class ReadMore extends StatefulWidget {
  const ReadMore(this.text, {super.key, this.max = 4, this.html = false});
  final String text;
  final int max;
  final bool html;

  @override
  State<ReadMore> createState() => _ReadMoreState();
}

class _ReadMoreState extends State<ReadMore> {
  bool _open = false;

  @override
  Widget build(BuildContext context) {
    final body = widget.html
        ? HtmlBody(widget.text)
        : Text(widget.text, style: T.bacaan);
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // dipangkas dengan tinggi maksimum: aman untuk HTML maupun teks polos
        _open
            ? body
            : ConstrainedBox(
                constraints: BoxConstraints(
                  maxHeight:
                      widget.max *
                      T.bacaan.fontSize! *
                      T.bacaan.height! *
                      MediaQuery.textScalerOf(context).scale(1),
                ),
                child: ClipRect(
                  child: Align(
                    alignment: Alignment.topLeft,
                    heightFactor: 1,
                    child: body,
                  ),
                ),
              ),
        Align(
          alignment: Alignment.centerLeft,
          child: TextButton(
            onPressed: () => setState(() => _open = !_open),
            child: Text(_open ? 'Tutup' : 'Selengkapnya'),
          ),
        ),
      ],
    );
  }
}

/// Deretan pil filter horizontal. Pil terpilih memakai penanda "posisi saat
/// ini" yang sama dengan nav, rail, dan tab: tint amber 16% + tepi amber +
/// teks Amber Ink — bukan isian amber penuh, yang setara tombol aksi utama.
class FilterPills extends StatelessWidget {
  const FilterPills({
    super.key,
    required this.labels,
    required this.selected,
    required this.onSelected,
  });
  final List<String> labels;
  final int selected;
  final ValueChanged<int> onSelected;

  @override
  Widget build(BuildContext context) => SizedBox(
    // 48 = target sentuh minimum; bertambah bila huruf diperbesar
    height: 48 + 18 * (skalaTeks(context) - 1),
    child: ListView.separated(
      scrollDirection: Axis.horizontal,
      itemCount: labels.length,
      separatorBuilder: (_, _) => const SizedBox(width: AppSpacing.sm),
      itemBuilder: (_, i) {
        final on = i == selected;
        return ChoiceChip(
          label: Text(labels[i]),
          selected: on,
          showCheckmark: false,
          onSelected: (_) => onSelected(i),
          backgroundColor: C.lightSurface,
          selectedColor: C.primary.withValues(alpha: .16),
          labelStyle: T.label.copyWith(
            color: on ? C.primaryInk : C.lightInkMuted,
            fontWeight: on ? FontWeight.w700 : FontWeight.w600,
          ),
          side: BorderSide(
            color: on ? C.primary.withValues(alpha: .55) : C.lightLine,
          ),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.chip),
          ),
        );
      },
    ),
  );
}

class HtmlBody extends StatelessWidget {
  const HtmlBody(this.html, {super.key});
  final String html;

  @override
  Widget build(BuildContext context) => HtmlWidget(
    html,
    textStyle: T.bacaan,
    onTapUrl: (url) {
      openUrl(context, url);
      return true;
    },
  );
}

/// Tabel metadata key-value; baris dengan nilai kosong otomatis disembunyikan.
class MetaTable extends StatelessWidget {
  const MetaTable(this.rows, {super.key});
  final Map<String, String?> rows;

  @override
  Widget build(BuildContext context) {
    final entries = rows.entries
        .where(
          (e) =>
              e.value != null && e.value!.trim().isNotEmpty && e.value != '-',
        )
        .toList();
    if (entries.isEmpty) return const SizedBox.shrink();
    final muted = Theme.of(context).brightness == Brightness.dark
        ? C.darkInkMuted
        : C.lightInkMuted;
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          children: [
            for (final e in entries)
              Padding(
                padding: const EdgeInsets.symmetric(vertical: 5),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    SizedBox(
                      width: 130,
                      child: Text(
                        e.key,
                        style: T.isiKecil.copyWith(color: muted),
                      ),
                    ),
                    Expanded(
                      child: Text(
                        e.value!,
                        style: T.isi.copyWith(fontWeight: FontWeight.w500),
                      ),
                    ),
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class ErrorRetry extends StatelessWidget {
  const ErrorRetry(
    this.message, {
    super.key,
    required this.onRetry,
    this.retryLabel = 'Coba lagi',
  });
  final String message;
  final VoidCallback onRetry;
  final String retryLabel;

  @override
  Widget build(BuildContext context) => Center(
    child: SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          const IconTile(Icons.cloud_off, size: 56),
          const SizedBox(height: 14),
          Text(message, textAlign: TextAlign.center),
          const SizedBox(height: 14),
          FilledButton(onPressed: onRetry, child: Text(retryLabel)),
        ],
      ),
    ),
  );
}

/// FutureBuilder + retry + pull-to-refresh. `builder` harus mengembalikan scrollable.
class LoadView extends StatefulWidget {
  const LoadView({
    super.key,
    required this.load,
    required this.builder,
    this.skeleton,
  });
  final Future<Json> Function() load;
  final Widget Function(BuildContext, Json) builder;

  /// Kerangka muat khusus halaman ini; default daftar kartu.
  final Widget? skeleton;

  @override
  State<LoadView> createState() => _LoadViewState();
}

class _LoadViewState extends State<LoadView> {
  late Future<Json> _future = widget.load();

  // pakai blok, bukan arrow: callback setState tidak boleh mengembalikan Future
  void _reload() {
    setState(() {
      _future = widget.load();
    });
  }

  @override
  Widget build(BuildContext context) => FutureBuilder<Json>(
    future: _future,
    builder: (context, snap) {
      if (snap.hasError) {
        return ErrorRetry('${snap.error}', onRetry: _reload);
      }
      if (!snap.hasData) {
        return widget.skeleton ?? const SkeletonList(count: 4, height: 120);
      }
      return RefreshIndicator(
        onRefresh: () async => _reload(),
        child: Rise(child: widget.builder(context, snap.data!)),
      );
    },
  );
}

/// Isi contoh untuk kerangka muat. [PagedListView] merender kartu aslinya
/// dengan data ini di dalam Skeletonizer, jadi bentuk kerangka selalu sama
/// dengan isi yang sedang dimuat — tanpa skeleton tiruan per jenis kartu.
const Json kSkeletonItem = {
  'id': 0,
  'judul': 'Peraturan Daerah tentang Rencana Tata Ruang Wilayah Kota',
  'nomor_peraturan': '12',
  'tahun_terbit': '2024',
  'tahun': '2024',
  'singkatan_jenis': 'PERDA',
  'jenis_peraturan': 'Peraturan Daerah',
  'jenis_dokumen': 'Naskah Akademik',
  'status': 'Berlaku',
  'bidang_hukum': 'Hukum Tata Negara',
  'tanggal': '2024-01-01',
  'ringkasan': 'Ringkasan singkat isi untuk kerangka muat daftar ini.',
  'tag': 'Info',
};

/// Daftar berpaginasi: infinite scroll + pull-to-refresh + empty/error state.
/// Ganti `key` (ValueKey filter) untuk memuat ulang dengan filter baru.
class PagedListView extends StatefulWidget {
  const PagedListView({
    super.key,
    required this.fetch,
    required this.itemBuilder,
    this.empty = 'Tidak ada data.',
    this.emptyView,
    this.onTotal,
    this.padding = const EdgeInsets.all(16),
  });
  final Future<Paginated> Function(int page) fetch;
  final Widget Function(BuildContext, Json, int index) itemBuilder;
  final String empty;

  /// Jumlah total hasil, dilaporkan sekali setelah halaman pertama termuat.
  final ValueChanged<int>? onTotal;

  /// Ganti seluruh keadaan kosong; "belum ada isinya + muat ulang" salah
  /// pesan untuk hasil pencarian yang kosong.
  final Widget? emptyView;
  final EdgeInsets padding;

  @override
  State<PagedListView> createState() => _PagedListViewState();
}

class _PagedListViewState extends State<PagedListView> {
  final _items = <Json>[];
  int _page = 1;
  bool _hasMore = true, _loading = false;
  String? _error;

  Future<void> _load() async {
    if (_loading || !_hasMore) return;
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      // Jeda minimum hanya untuk halaman pertama: kerangka muat yang hilang
      // sekejap terbaca sebagai glitch. Halaman lanjutan tidak ditahan —
      // server menjawab 110-200 ms, jeda lama 800 ms membuat gulir tak
      // berujung tersendat di setiap halaman.
      final r = await Future.wait([
        widget.fetch(_page),
        if (_page == 1) Future.delayed(const Duration(milliseconds: 400)),
      ]);
      final p = r.first as Paginated;
      if (!mounted) return;
      if (_page == 1) widget.onTotal?.call(p.total);
      setState(() {
        _items.addAll(p.items);
        _hasMore = p.hasMore;
        _page++;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = '$e';
        _loading = false;
      });
    }
  }

  Future<void> _refresh() async {
    _items.clear();
    _page = 1;
    _hasMore = true;
    await _load();
  }

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  Widget build(BuildContext context) {
    if (_items.isEmpty) {
      if (_loading) {
        return Skeletonizer(
          child: ListView.separated(
            padding: widget.padding,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: 6,
            separatorBuilder: (_, _) => const SizedBox(height: 12),
            itemBuilder: (c, i) => widget.itemBuilder(c, kSkeletonItem, i),
          ),
        );
      }
      if (_error != null) return ErrorRetry(_error!, onRetry: _load);
      return widget.emptyView ??
          EmptyState(
            icon: Icons.inbox_outlined,
            title: 'Belum ada isinya',
            message: widget.empty,
            action: OutlinedButton.icon(
              onPressed: _refresh,
              icon: const Icon(Icons.refresh, size: 18),
              label: const Text('Muat ulang'),
            ),
          );
    }
    // Layar lebar: kartu berderet 2-3 kolom (bukan kartu HP yang melar),
    // grid paling lebar kKolomGrid di tengah.
    final pad = widget.padding;
    final tepi = math.max(
      pad.left,
      (MediaQuery.sizeOf(context).width - kKolomGrid) / 2,
    );
    final kolom = kolomUntuk(MediaQuery.sizeOf(context).width - tepi * 2);
    final baris = (_items.length / kolom).ceil();
    // satu fade untuk seluruh daftar saat isi pertama tiba; kartu yang muncul
    // satu per satu hanya membuat pengguna menonton daftar dimuat
    return RefreshIndicator(
      onRefresh: _refresh,
      child: Rise(
        child: ListView.separated(
          padding: pad.copyWith(left: tepi, right: tepi),
          itemCount: baris + ((_hasMore || _error != null) ? 1 : 0),
          separatorBuilder: (_, _) => const SizedBox(height: 12),
          itemBuilder: (context, i) {
            if (i >= baris) {
              if (_error != null) {
                // isi yang sudah tampil dipertahankan; hanya lanjutannya gagal
                return Padding(
                  padding: const EdgeInsets.symmetric(vertical: AppSpacing.sm),
                  child: Column(
                    children: [
                      Text(
                        _error!,
                        textAlign: TextAlign.center,
                        style: T.isiKecil.copyWith(color: C.lightInkMuted),
                      ),
                      TextButton.icon(
                        onPressed: _load,
                        icon: const Icon(Icons.refresh, size: 18),
                        label: const Text('Muat lanjutan'),
                      ),
                    ],
                  ),
                );
              }
              if (!_loading) {
                WidgetsBinding.instance.addPostFrameCallback((_) => _load());
              }
              return const Padding(
                padding: EdgeInsets.all(16),
                child: Center(child: CircularProgressIndicator()),
              );
            }
            if (kolom == 1) return widget.itemBuilder(context, _items[i], i);
            return BarisKartu(
              kolom: kolom,
              children: [
                for (
                  var j = i * kolom;
                  j < math.min((i + 1) * kolom, _items.length);
                  j++
                )
                  widget.itemBuilder(context, _items[j], j),
              ],
            );
          },
        ),
      ),
    );
  }
}
