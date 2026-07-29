import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html_core/flutter_widget_from_html_core.dart';
import 'package:intl/intl.dart';
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
String ucFirst(String s) =>
    s.isEmpty ? s : s[0].toUpperCase() + s.substring(1);

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
  final ok = await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
  if (!ok && context.mounted) {
    ScaffoldMessenger.of(context)
        .showSnackBar(const SnackBar(content: Text('Tidak dapat membuka tautan.')));
  }
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

/// Entrance "rise": fade + naik 12dp, easeOutCubic. Cermin animate-rise web.
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
      offset: _shown ? Offset.zero : const Offset(0, .035),
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

/// Press feedback fisik: scale 0.97 saat ditekan. Bungkus kartu/tile interaktif;
/// child tetap memegang InkWell/onTap-nya sendiri.
class Pressable extends StatefulWidget {
  const Pressable({super.key, required this.child});
  final Widget child;

  @override
  State<Pressable> createState() => _PressableState();
}

class _PressableState extends State<Pressable> {
  bool _down = false;

  @override
  Widget build(BuildContext context) {
    if (MediaQuery.of(context).disableAnimations) return widget.child;
    return Listener(
      onPointerDown: (_) => setState(() => _down = true),
      onPointerUp: (_) => setState(() => _down = false),
      onPointerCancel: (_) => setState(() => _down = false),
      child: AnimatedScale(
        scale: _down ? .97 : 1,
        duration: const Duration(milliseconds: 120),
        curve: Curves.easeOut,
        child: widget.child,
      ),
    );
  }
}

/// Pulse opacity untuk skeleton loading.
class Pulse extends StatefulWidget {
  const Pulse({super.key, required this.child});
  final Widget child;

  @override
  State<Pulse> createState() => _PulseState();
}

class _PulseState extends State<Pulse> with SingleTickerProviderStateMixin {
  late final AnimationController _c = AnimationController(
      vsync: this, duration: const Duration(milliseconds: 900))
    ..repeat(reverse: true);

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

/// Skeleton daftar: kartu pulse berbentuk seperti isi aslinya (thumbnail +
/// baris teks), bukan spinner dan bukan kotak polos — kotak polos berwarna
/// samar terbaca sebagai halaman kosong, bukan sebagai "sedang memuat".
class SkeletonList extends StatelessWidget {
  const SkeletonList(
      {super.key,
      this.count = 5,
      this.height = 104,
      this.padding = const EdgeInsets.all(16)});
  final int count;
  final double height;
  final EdgeInsets padding;

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final block = dark ? C.darkSubtle : C.lightLine;
    final line = dark ? C.darkLine : C.lightSubtle;

    Widget bar(double w, [double h = 12]) => Container(
          width: w,
          height: h,
          decoration: BoxDecoration(
            color: line,
            borderRadius: BorderRadius.circular(AppRadius.chip),
          ),
        );

    return ListView.separated(
      padding: padding,
      physics: const NeverScrollableScrollPhysics(),
      // sering dipakai sebagai anak ListView lain (mis. skeleton jawaban AI):
      // tanpa shrinkWrap tingginya tak terbatas dan layout meledak
      shrinkWrap: true,
      itemCount: count,
      separatorBuilder: (_, __) => const SizedBox(height: 12),
      itemBuilder: (_, __) => Pulse(
        child: Container(
          height: height,
          decoration: BoxDecoration(
            color: dark ? C.darkSurface : C.lightSurface,
            border: Border.all(color: dark ? C.darkLine : C.lightLine),
            borderRadius: BorderRadius.circular(AppRadius.card),
          ),
          child: Row(children: [
            Container(
              width: height * .9,
              decoration: BoxDecoration(color: block, borderRadius: BorderRadius.circular(AppRadius.card)),
            ),
            Expanded(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 12),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    bar(double.infinity, 14),
                    const SizedBox(height: 8),
                    bar(70, 10),
                    const SizedBox(height: 8),
                    bar(double.infinity, 10),
                  ],
                ),
              ),
            ),
          ]),
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
    final block = dark ? C.darkSubtle : C.lightLine;
    final line = dark ? C.darkLine : C.lightSubtle;
    final surface = dark ? C.darkSurface : C.lightSurface;

    Widget bar(double w, [double h = 12]) => Container(
          width: w,
          height: h,
          decoration: BoxDecoration(
            color: line,
            borderRadius: BorderRadius.circular(AppRadius.chip),
          ),
        );

    return Pulse(
      child: ListView(padding: EdgeInsets.zero, children: [
        Container(height: 320, color: block),
        Transform.translate(
          offset: const Offset(0, -24),
          child: Container(
            decoration: BoxDecoration(
              color: surface,
              borderRadius: const BorderRadius.vertical(
                  top: Radius.circular(AppRadius.sheet)),
            ),
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, 48, AppSpacing.lg, AppSpacing.xxl),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Row(children: [
                bar(64, 22),
                const SizedBox(width: AppSpacing.sm),
                bar(104, 22),
                const SizedBox(width: AppSpacing.sm),
                bar(88, 22),
              ]),
              const SizedBox(height: AppSpacing.md),
              bar(double.infinity, 26),
              const SizedBox(height: AppSpacing.sm),
              bar(200, 26),
              const SizedBox(height: AppSpacing.lg),
              bar(56, 3),
              const SizedBox(height: AppSpacing.lg),
              for (var i = 0; i < 6; i++) ...[
                bar(i == 5 ? 180 : double.infinity),
                const SizedBox(height: AppSpacing.md),
              ],
            ]),
          ),
        ),
      ]),
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
    final line = dark ? C.darkLine : C.lightSubtle;
    final surface = dark ? C.darkSurface : C.lightSurface;

    Widget bar(double w, [double h = 12]) => Container(
          width: w,
          height: h,
          decoration: BoxDecoration(
            color: line,
            borderRadius: BorderRadius.circular(AppRadius.chip),
          ),
        );

    return Pulse(
      child: Column(children: [
        Expanded(
          child: ListView(padding: EdgeInsets.zero, children: [
            const SizedBox(
              height: 200,
              child: DecoratedBox(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                      colors: [C.ink, C.inkSoft],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight),
                ),
              ),
            ),
            // lembar isi menimpa hero, sama seperti halaman aslinya
            Transform.translate(
              offset: const Offset(0, -24),
              child: Container(
                decoration: BoxDecoration(
                  color: surface,
                  borderRadius: const BorderRadius.vertical(
                      top: Radius.circular(AppRadius.sheet)),
                ),
                padding: const EdgeInsets.fromLTRB(
                    AppSpacing.lg, 40, AppSpacing.lg, AppSpacing.xl),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(children: [
                      bar(88, 22),
                      const SizedBox(width: AppSpacing.sm),
                      bar(72, 22),
                    ]),
                    const SizedBox(height: AppSpacing.md),
                    bar(double.infinity, 24),
                    const SizedBox(height: AppSpacing.sm),
                    bar(220, 24),
                    const SizedBox(height: AppSpacing.md),
                    Row(children: [
                      bar(96, 20),
                      const SizedBox(width: AppSpacing.sm),
                      bar(88, 20),
                    ]),
                    const SizedBox(height: AppSpacing.lg),
                    bar(56, 3),
                    const SizedBox(height: AppSpacing.lg),
                    // pil tab
                    Row(children: [
                      bar(84, 32),
                      const SizedBox(width: AppSpacing.sm),
                      bar(96, 32),
                      const SizedBox(width: AppSpacing.sm),
                      bar(80, 32),
                    ]),
                    const SizedBox(height: AppSpacing.lg),
                    // kartu spesifikasi 2 kolom
                    Row(children: [
                      Expanded(child: bar(double.infinity, 100)),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(child: bar(double.infinity, 100)),
                    ]),
                    const SizedBox(height: AppSpacing.sm),
                    Row(children: [
                      Expanded(child: bar(double.infinity, 100)),
                      const SizedBox(width: AppSpacing.sm),
                      Expanded(child: bar(double.infinity, 100)),
                    ]),
                    const SizedBox(height: AppSpacing.xl),
                    // tabel metadata: label pendek + nilai panjang
                    for (var i = 0; i < 5; i++) ...[
                      Row(children: [
                        bar(96),
                        const SizedBox(width: AppSpacing.md),
                        Expanded(child: bar(double.infinity)),
                      ]),
                      const SizedBox(height: AppSpacing.md),
                    ],
                  ],
                ),
              ),
            ),
          ]),
        ),
        // bilah aksi tetap di bawah, sama seperti halaman aslinya
        Container(
          color: surface,
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Row(children: [
            bar(72, 40),
            const SizedBox(width: AppSpacing.md),
            Expanded(child: bar(double.infinity, 40)),
          ]),
        ),
      ]),
    );
  }
}

// ============================================================
//  ORNAMEN GEOMETRIS & SIGNATURE BRAND
// ============================================================

/// Ornamen geometris: lingkaran outline, kuarter lingkaran, dot-grid.
/// Pakai sebagai lapisan di dalam Stack (Positioned.fill).
class GeoPattern extends StatelessWidget {
  const GeoPattern({super.key, this.color = Colors.white, this.opacity = .1});
  final Color color;
  final double opacity;

  @override
  Widget build(BuildContext context) => IgnorePointer(
        child: CustomPaint(painter: _GeoPainter(color.withValues(alpha: opacity))),
      );
}

class _GeoPainter extends CustomPainter {
  const _GeoPainter(this.color);
  final Color color;

  @override
  void paint(Canvas canvas, Size size) {
    final stroke = Paint()
      ..color = color
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.5;
    final fill = Paint()..color = color;

    // lingkaran outline besar, keluar dari kanan-atas
    canvas.drawCircle(Offset(size.width * .95, size.height * .05), size.height * .55, stroke);
    canvas.drawCircle(Offset(size.width * .95, size.height * .05), size.height * .32, stroke);
    // kuarter lingkaran solid di kiri-bawah
    canvas.drawCircle(Offset(0, size.height), size.height * .28, fill);
    // dot-grid kecil di kanan-bawah
    for (var i = 0; i < 4; i++) {
      for (var j = 0; j < 3; j++) {
        canvas.drawCircle(
            Offset(size.width - 64 + i * 14.0, size.height - 40 + j * 14.0), 1.6, fill);
      }
    }
  }

  @override
  bool shouldRepaint(_GeoPainter old) => old.color != color;
}

/// AppBar dengan garis gradient oranye->gold di bawahnya (signature brand).
/// `tabs` opsional: TabBar dirender di atas garis gradient.
class BrandAppBar extends StatelessWidget implements PreferredSizeWidget {
  const BrandAppBar(this.title,
      {super.key, this.actions, this.tabs, this.showBack, this.leading});
  final String title;
  final List<Widget>? actions;
  final TabBar? tabs;
  final bool? showBack;
  final Widget? leading;

  @override
  Size get preferredSize => Size.fromHeight(
      kToolbarHeight + 3 + (tabs?.preferredSize.height ?? 0));

  @override
  Widget build(BuildContext context) => AppBar(
        title: Text(title),
        actions: actions,
        leading: leading,
        automaticallyImplyLeading: showBack ?? true,
        bottom: PreferredSize(
          preferredSize:
              Size.fromHeight(3 + (tabs?.preferredSize.height ?? 0)),
          child: Column(mainAxisSize: MainAxisSize.min, children: [
            if (tabs != null) tabs!,
            const SizedBox(
              height: 3,
              width: double.infinity,
              child: DecoratedBox(
                decoration: BoxDecoration(
                  gradient: LinearGradient(colors: [C.primary, C.gold]),
                ),
              ),
            ),
          ]),
        ),
      );
}

// ============================================================
//  ELEMEN UI
// ============================================================

class NetImage extends StatelessWidget {
  const NetImage(this.url, {super.key, this.width, this.height, this.radius = 0});
  final String? url;
  final double? width, height;
  final double radius;

  @override
  Widget build(BuildContext context) {
    // Kotak abu untuk denyut saat memuat — alignment (bukan width/height)
    // supaya kotak isi mengikuti kotak pembungkus.
    final ph = Container(
      alignment: Alignment.center,
      color: Theme.of(context).brightness == Brightness.dark ? C.darkSubtle : C.lightSubtle,
      child: const Icon(Icons.image_outlined, color: Colors.grey),
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
    final img = (url == null || url!.isEmpty)
        ? fallback
        : CachedNetworkImage(
            imageUrl: url!,
            fit: BoxFit.cover,
            placeholder: (_, __) => Pulse(child: ph),
            errorWidget: (_, __, ___) => fallback,
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
      pageBuilder: (_, __, ___) => _ImageViewer(url, tag: tag),
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
        body: Stack(children: [
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
                    placeholder: (_, __) => const Center(
                        child: CircularProgressIndicator(color: C.primary)),
                    errorWidget: (_, __, ___) => const Icon(
                        Icons.broken_image_outlined,
                        color: Colors.white54,
                        size: 48),
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
        ]),
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
  if (s.contains('diubah') || s.contains('mengubah') || s.contains('mencabut')) {
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
      child: Text(status!,
          style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: dark ? bg : fg)),
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
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: C.primary.withValues(alpha: .1),
        borderRadius: BorderRadius.circular(AppRadius.chip),
        border: Border.all(color: C.primary.withValues(alpha: .35)),
      ),
      child: Text(text,
          // tag panjang ("Bagian Hukum Sekretariat Daerah Kota Kendari")
          // tidak boleh melipat jadi dua baris dan mendominasi kartu
          maxLines: 1,
          overflow: TextOverflow.ellipsis,
          style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w700,
              color: Theme.of(context).brightness == Brightness.dark
                  ? C.primarySoft
                  : C.primaryInk)),
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
        child: Row(
          children: [
            Container(
              width: 4,
              height: 20,
              decoration: BoxDecoration(
                color: C.primary,
                borderRadius: BorderRadius.circular(2),
              ),
            ),
            const SizedBox(width: 10),
            Expanded(
                child: Text(title,
                    style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.w800, letterSpacing: -.3))),
            if (onSeeAll != null)
              TextButton.icon(
                  onPressed: onSeeAll,
                  iconAlignment: IconAlignment.end,
                  icon: const Icon(Icons.arrow_forward, size: 15, color: C.primaryInk),
                  label: const Text('Lihat semua',
                      style: TextStyle(color: C.primaryInk, fontWeight: FontWeight.w600))),
          ],
        ),
      );
}

/// Ikon dalam squircle tinted — vocabulary tile hub.
class IconSquircle extends StatelessWidget {
  const IconSquircle(this.icon, {super.key, this.color = C.primaryInk, this.size = 44});
  final IconData icon;
  final Color color;
  final double size;

  @override
  Widget build(BuildContext context) => Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          color: color.withValues(alpha: .12),
          borderRadius: BorderRadius.circular(size * .32),
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

  @override
  Widget build(BuildContext context) => Pressable(
        child: Material(
          color: C.primary.withValues(alpha: .12),
          shape: StadiumBorder(
              side: BorderSide(color: C.primary.withValues(alpha: .4))),
          clipBehavior: Clip.antiAlias,
          child: InkWell(
            onTap: onTap,
            child: SizedBox(
              height: 36,
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
                child: Row(mainAxisSize: MainAxisSize.min, children: [
                  const Icon(Icons.language, size: 16, color: C.primaryInk),
                  const SizedBox(width: 6),
                  Text(langNotifier.value.toUpperCase(),
                      style: const TextStyle(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w800,
                          color: C.primaryInk)),
                  const Icon(Icons.expand_more, size: 16, color: C.primaryInk),
                ]),
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
            child: Column(children: [
              for (final e in kLangs.entries)
                RadioListTile<String>(title: Text(e.value), value: e.key),
            ]),
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
        child: Row(mainAxisSize: MainAxisSize.min, children: [
          Icon(icon, size: 12, color: C.lightInkMuted),
          const SizedBox(width: 4),
          Text(label,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 11, color: C.lightInkMuted)),
        ]),
      );
}

/// Satu tujuan pada navigasi bawah.
typedef NavItem = ({IconData icon, IconData activeIcon, String label});

/// Navigasi bawah mengambang: pil putih, satu tombol tengah terangkat untuk
/// aksi utama, dan titik kecil menandai tujuan yang aktif.
///
/// Tanpa label teks (mengikuti rancangan), jadi setiap tujuan dibungkus
/// [Semantics] dan tooltip supaya pembaca layar tetap menyebut namanya.
class FloatingNavBar extends StatelessWidget {
  const FloatingNavBar({
    super.key,
    required this.index,
    required this.onChanged,
    required this.items,
    this.raisedIndex = 2,
  });

  final int index;
  final ValueChanged<int> onChanged;
  final List<NavItem> items;

  /// Tujuan yang dinaikkan sebagai lingkaran menonjol.
  final int raisedIndex;

  static const double _bar = 62;
  static const double _circle = 54;

  /// Bagian lingkaran yang menonjol di atas pil.
  static const double _raise = 24;

  @override
  Widget build(BuildContext context) {
    final reduce = MediaQuery.of(context).disableAnimations;
    return SafeArea(
      top: false,
      child: Padding(
        padding: const EdgeInsets.fromLTRB(
            AppSpacing.lg, 0, AppSpacing.lg, AppSpacing.md),
        child: SizedBox(
          height: _bar + _raise,
          child: Stack(
            clipBehavior: Clip.none,
            children: [
              Positioned(
                left: 0,
                right: 0,
                bottom: 0,
                height: _bar,
                child: Material(
                  color: C.lightSurface,
                  elevation: 3,
                  shadowColor: C.ink.withValues(alpha: .22),
                  shape: const StadiumBorder(
                      side: BorderSide(color: C.lightLine)),
                  child: Row(children: [
                    for (final (i, item) in items.indexed)
                      Expanded(
                        child: i == raisedIndex
                            // ruang untuk tombol terangkat yang digambar
                            // di lapisan atas Stack
                            ? const SizedBox.shrink()
                            : _Destination(
                                item: item,
                                active: i == index,
                                reduce: reduce,
                                onTap: () => onChanged(i),
                              ),
                      ),
                  ]),
                ),
              ),
              // di atas pil agar lingkaran tidak terpotong, dan tetap di dalam
              // batas SizedBox supaya seluruh areanya bisa disentuh
              Positioned.fill(
                child: Align(
                  alignment: Alignment.topCenter,
                  child: _RaisedDestination(
                    item: items[raisedIndex],
                    active: index == raisedIndex,
                    reduce: reduce,
                    onTap: () => onChanged(raisedIndex),
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

/// Titik penanda tujuan aktif.
class _ActiveDot extends StatelessWidget {
  const _ActiveDot({required this.active, required this.reduce});
  final bool active;
  final bool reduce;

  @override
  Widget build(BuildContext context) => AnimatedScale(
        scale: active ? 1 : 0,
        duration: Duration(milliseconds: reduce ? 0 : 180),
        curve: Curves.easeOutBack,
        child: Container(
          width: 5,
          height: 5,
          decoration: const BoxDecoration(
              color: C.primary, shape: BoxShape.circle),
        ),
      );
}

class _Destination extends StatelessWidget {
  const _Destination(
      {required this.item,
      required this.active,
      required this.reduce,
      required this.onTap});
  final NavItem item;
  final bool active;
  final bool reduce;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Semantics(
        button: true,
        selected: active,
        label: item.label,
        child: Tooltip(
          message: item.label,
          child: InkResponse(
            onTap: onTap,
            radius: 34,
            child: SizedBox(
              height: FloatingNavBar._bar,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  AnimatedSwitcher(
                    duration: Duration(milliseconds: reduce ? 0 : 180),
                    child: Icon(
                      active ? item.activeIcon : item.icon,
                      key: ValueKey(active),
                      size: 24,
                      color: active ? C.primaryInk : C.lightInkMuted,
                    ),
                  ),
                  const SizedBox(height: 6),
                  _ActiveDot(active: active, reduce: reduce),
                ],
              ),
            ),
          ),
        ),
      );
}

class _RaisedDestination extends StatelessWidget {
  const _RaisedDestination(
      {required this.item,
      required this.active,
      required this.reduce,
      required this.onTap});
  final NavItem item;
  final bool active;
  final bool reduce;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Semantics(
        button: true,
        selected: active,
        label: item.label,
        child: Tooltip(
          message: item.label,
          child: InkResponse(
            onTap: onTap,
            radius: 36,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: FloatingNavBar._circle + 12,
                  height: FloatingNavBar._circle + 12,
                  decoration: BoxDecoration(
                    color: C.primary.withValues(alpha: .14),
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: Container(
                      width: FloatingNavBar._circle,
                      height: FloatingNavBar._circle,
                      decoration: BoxDecoration(
                        color: C.primary,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: C.primary.withValues(alpha: .38),
                            blurRadius: 12,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      // ikon ink di atas oranye: 6,2:1
                      child: Icon(active ? item.activeIcon : item.icon,
                          size: 26, color: C.ink),
                    ),
                  ),
                ),
                const SizedBox(height: 2),
                _ActiveDot(active: active, reduce: reduce),
              ],
            ),
          ),
        ),
      );
}

/// Keadaan kosong yang mengajarkan langkah berikutnya, bukan sekadar
/// "tidak ada data".
class EmptyState extends StatelessWidget {
  const EmptyState(
      {super.key,
      required this.icon,
      required this.title,
      required this.message,
      this.action});
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
                // dua lingkaran konsentris di belakang ikon: memberi kedalaman
                // tanpa aset gambar, memakai warna merek yang sudah ada
                Stack(alignment: Alignment.center, children: [
                  Container(
                    width: 132,
                    height: 132,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: C.primary.withValues(alpha: .06),
                    ),
                  ),
                  Container(
                    width: 96,
                    height: 96,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      border:
                          Border.all(color: C.primary.withValues(alpha: .18)),
                    ),
                  ),
                  IconSquircle(icon, size: 64),
                ]),
                const SizedBox(height: AppSpacing.lg),
                Text(title,
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                        fontSize: 19, fontWeight: FontWeight.w700)),
                const SizedBox(height: AppSpacing.sm),
                ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 320),
                  child: Text(message,
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                          fontSize: 13, height: 1.6, color: C.lightInkMuted)),
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

/// Ajakan ke Asisten AI. Satu-satunya permukaan di app yang "drenched" —
/// warna penuh dipakai sekali untuk menandai aksi paling menonjol.
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
        message: 'Tidak ada yang cocok dengan "$query". '
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

class AiPromoCard extends StatelessWidget {
  const AiPromoCard({super.key, required this.onTap});
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Pressable(
        child: ClipRRect(
          borderRadius: BorderRadius.circular(AppRadius.sheet),
          child: Stack(children: [
            const Positioned.fill(
              child: DecoratedBox(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                      colors: [C.ink, C.inkSoft],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight),
                ),
              ),
            ),
            const Positioned.fill(child: GeoPattern(color: C.gold, opacity: .2)),
            Positioned(
              right: -6,
              top: -10,
              child: Icon(Icons.auto_awesome,
                  size: 108, color: C.gold.withValues(alpha: .14)),
            ),
            Padding(
              padding: const EdgeInsets.all(AppSpacing.xl),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(children: [
                    const Icon(Icons.auto_awesome, size: 18, color: C.gold),
                    const SizedBox(width: 6),
                    Text('Pencarian AI',
                        style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            color: C.cream)),
                  ]),
                  const SizedBox(height: AppSpacing.sm),
                  Text(
                      'Tulis pertanyaan dengan bahasa sehari-hari, AI akan '
                      'mencarikan dokumen hukum Kota Kendari yang paling cocok.',
                      style: TextStyle(
                          fontSize: 13,
                          height: 1.55,
                          color: C.cream.withValues(alpha: .82))),
                  const SizedBox(height: AppSpacing.lg),
                  FilledButton.icon(
                    onPressed: onTap,
                    icon: const Icon(Icons.arrow_forward, size: 18),
                    iconAlignment: IconAlignment.end,
                    label: const Text('Cari dengan AI'),
                    style: FilledButton.styleFrom(
                      backgroundColor: C.gold,
                      foregroundColor: C.ink,
                      shape: const StadiumBorder(),
                    ),
                  ),
                ],
              ),
            ),
          ]),
        ),
      );
}

/// Kartu kabar bergambar. Dipakai berderet mendatar di beranda dan
/// sebagai kartu sorotan di indeks Kabar.
class NewsTile extends StatelessWidget {
  const NewsTile(this.item,
      {super.key, required this.onTap, this.width, this.featured = false});
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
                  Stack(children: [
                    AspectRatio(
                        aspectRatio: featured ? 16 / 9 : 3 / 2,
                        child: NetImage(item.sn('image_url'))),
                    if (featured)
                      Positioned(
                        left: AppSpacing.md,
                        top: AppSpacing.md,
                        child: Container(
                          padding: const EdgeInsets.symmetric(
                              horizontal: AppSpacing.sm, vertical: 4),
                          decoration: BoxDecoration(
                            color: C.gold,
                            borderRadius:
                                BorderRadius.circular(AppRadius.chip),
                          ),
                          child: const Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Icon(Icons.star, size: 12, color: C.ink),
                                SizedBox(width: 4),
                                Text('Terbaru',
                                    style: TextStyle(
                                        fontSize: 11,
                                        fontWeight: FontWeight.w700,
                                        color: C.ink)),
                              ]),
                        ),
                      ),
                  ]),
                  Padding(
                    padding: const EdgeInsets.all(AppSpacing.md),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // satu Text, bukan Row: tanggal + tag panjang tidak
                        // bisa meluber pada kartu sempit
                        Text.rich(
                          TextSpan(children: [
                            TextSpan(
                                text: fmtDateShort(item.sn('tanggal'))
                                    .toUpperCase()),
                            if (item.sn('tag') != null)
                              TextSpan(
                                  text: ' · ${titleCase(item.s('tag'))}',
                                  style: const TextStyle(
                                      fontWeight: FontWeight.w700,
                                      color: C.primaryInk)),
                          ]),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: C.lightInkMuted),
                        ),
                        const SizedBox(height: AppSpacing.xs),
                        Text(ucFirst(item.s('judul')),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                            style: TextStyle(
                                fontSize: featured ? 16 : 14,
                                height: 1.35,
                                fontWeight: FontWeight.w700)),
                        if (featured && item.sn('ringkasan') != null) ...[
                          const SizedBox(height: 6),
                          Text(ucFirst(item.s('ringkasan')),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                  fontSize: 13,
                                  height: 1.5,
                                  color: C.lightInkMuted)),
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
                horizontal: AppSpacing.md, vertical: AppSpacing.sm),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Icon(icon, size: 20, color: C.primaryInk),
                const SizedBox(height: AppSpacing.sm),
                // dua baris, bukan satu: "Peraturan Walikota" dan "Tidak
                // Berlaku" tidak muat di satu baris kartu selebar ini
                Text(value,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 13, height: 1.25, fontWeight: FontWeight.w700)),
                Text(label,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 11, color: C.lightInkMuted)),
              ],
            ),
          ),
        ),
      );
}

/// Tombol bulat melayang di atas gambar hero (back / bagikan).
class RoundIconButton extends StatelessWidget {
  const RoundIconButton(this.icon,
      {super.key, required this.onPressed, this.tooltip});
  final IconData icon;
  final VoidCallback onPressed;
  final String? tooltip;

  @override
  Widget build(BuildContext context) => Material(
        color: C.lightSurface,
        shape: const CircleBorder(),
        elevation: 2,
        shadowColor: C.ink.withValues(alpha: .25),
        child: IconButton(
          tooltip: tooltip,
          icon: Icon(icon, size: 20, color: C.ink),
          onPressed: onPressed,
        ),
      );
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
        : Text(widget.text, style: const TextStyle(height: 1.6));
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // dipangkas dengan tinggi maksimum: aman untuk HTML maupun teks polos
        _open
            ? body
            : ConstrainedBox(
                constraints: BoxConstraints(maxHeight: widget.max * 26),
                child: ClipRect(
                    child: Align(
                        alignment: Alignment.topLeft,
                        heightFactor: 1,
                        child: body)),
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

/// Deretan pil filter horizontal; pil terpilih berwarna primary.
class FilterPills extends StatelessWidget {
  const FilterPills(
      {super.key,
      required this.labels,
      required this.selected,
      required this.onSelected});
  final List<String> labels;
  final int selected;
  final ValueChanged<int> onSelected;

  @override
  Widget build(BuildContext context) => SizedBox(
        height: 40,
        child: ListView.separated(
          scrollDirection: Axis.horizontal,
          itemCount: labels.length,
          separatorBuilder: (_, __) => const SizedBox(width: AppSpacing.sm),
          itemBuilder: (_, i) {
            final on = i == selected;
            return ChoiceChip(
              label: Text(labels[i]),
              selected: on,
              showCheckmark: false,
              onSelected: (_) => onSelected(i),
              backgroundColor: C.lightSurface,
              selectedColor: C.primary,
              labelStyle: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: on ? C.ink : C.lightInkMuted),
              side: BorderSide(color: on ? C.primary : C.lightLine),
              shape: const StadiumBorder(),
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
        textStyle: const TextStyle(fontSize: 16, height: 1.6),
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
    final entries =
        rows.entries.where((e) => e.value != null && e.value!.trim().isNotEmpty && e.value != '-').toList();
    if (entries.isEmpty) return const SizedBox.shrink();
    final muted = Theme.of(context).brightness == Brightness.dark ? C.darkInkMuted : C.lightInkMuted;
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
                        child: Text(e.key, style: TextStyle(fontSize: 13, color: muted))),
                    Expanded(
                        child: Text(e.value!,
                            style: const TextStyle(
                                fontSize: 14, fontWeight: FontWeight.w500))),
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
  const ErrorRetry(this.message,
      {super.key, required this.onRetry, this.retryLabel = 'Coba lagi'});
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
              const IconSquircle(Icons.cloud_off, color: C.primaryInk, size: 56),
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
  const LoadView(
      {super.key, required this.load, required this.builder, this.skeleton});
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

/// Daftar berpaginasi: infinite scroll + pull-to-refresh + empty/error state.
/// Ganti `key` (ValueKey filter) untuk memuat ulang dengan filter baru.
class PagedListView extends StatefulWidget {
  const PagedListView(
      {super.key,
      required this.fetch,
      required this.itemBuilder,
      this.empty = 'Tidak ada data.',
      this.emptyView,
      this.onTotal,
      this.padding = const EdgeInsets.all(16)});
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
      // jeda minimum: di jaringan cepat indikator "memuat" berkedip sekejap
      // dan terbaca sebagai glitch, bukan sebagai proses
      final r = await Future.wait(
          [widget.fetch(_page), Future.delayed(const Duration(milliseconds: 800))]);
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
      if (_loading) return const SkeletonList();
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
    return RefreshIndicator(
      onRefresh: _refresh,
      child: ListView.separated(
        padding: widget.padding,
        itemCount: _items.length + ((_hasMore || _error != null) ? 1 : 0),
        separatorBuilder: (_, __) => const SizedBox(height: 12),
        itemBuilder: (context, i) {
          if (i >= _items.length) {
            if (_error != null) {
              return Center(
                  child: TextButton(onPressed: _load, child: const Text('Coba lagi')));
            }
            if (!_loading) {
              WidgetsBinding.instance.addPostFrameCallback((_) => _load());
            }
            return const Padding(
              padding: EdgeInsets.all(16),
              child: Center(child: CircularProgressIndicator()),
            );
          }
          // stagger entrance hanya untuk halaman pertama
          final item = widget.itemBuilder(context, _items[i], i);
          return i < 8 ? Rise(delayMs: i * 50, child: item) : item;
        },
      ),
    );
  }
}
