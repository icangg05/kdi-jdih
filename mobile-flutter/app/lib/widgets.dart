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

Future<void> openUrl(BuildContext context, String? url) async {
  if (url == null || url.isEmpty) return;
  final ok = await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
  if (!ok && context.mounted) {
    ScaffoldMessenger.of(context)
        .showSnackBar(const SnackBar(content: Text('Tidak dapat membuka tautan.')));
  }
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
      offset: _shown ? Offset.zero : const Offset(0, .05),
      duration: const Duration(milliseconds: 550),
      curve: Curves.easeOutCubic,
      child: AnimatedOpacity(
        opacity: _shown ? 1 : 0,
        duration: const Duration(milliseconds: 450),
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

/// Skeleton daftar: kotak-kotak pulse seukuran kartu (bukan spinner).
class SkeletonList extends StatelessWidget {
  const SkeletonList({super.key, this.count = 5, this.height = 104});
  final int count;
  final double height;

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    return ListView.separated(
      padding: const EdgeInsets.all(16),
      physics: const NeverScrollableScrollPhysics(),
      itemCount: count,
      separatorBuilder: (_, __) => const SizedBox(height: 12),
      itemBuilder: (_, __) => Pulse(
        child: Container(
          height: height,
          decoration: BoxDecoration(
            color: dark ? C.darkSubtle : C.lightSubtle,
            borderRadius: BorderRadius.circular(16),
          ),
        ),
      ),
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

/// AppBar dengan garis gradient oranye->biru di bawahnya (signature brand).
/// `tabs` opsional: TabBar dirender di atas garis gradient.
class BrandAppBar extends StatelessWidget implements PreferredSizeWidget {
  const BrandAppBar(this.title, {super.key, this.actions, this.tabs, this.showBack});
  final String title;
  final List<Widget>? actions;
  final TabBar? tabs;
  final bool? showBack;

  @override
  Size get preferredSize => Size.fromHeight(
      kToolbarHeight + 3 + (tabs?.preferredSize.height ?? 0));

  @override
  Widget build(BuildContext context) => AppBar(
        title: Text(title),
        actions: actions,
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
                  gradient: LinearGradient(colors: [C.primary, C.accent]),
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
    final ph = Container(
      width: width,
      height: height,
      color: Theme.of(context).brightness == Brightness.dark ? C.darkSubtle : C.lightSubtle,
      child: const Icon(Icons.image_outlined, color: Colors.grey),
    );
    final img = (url == null || url!.isEmpty)
        ? ph
        : CachedNetworkImage(
            imageUrl: url!,
            width: width,
            height: height,
            fit: BoxFit.cover,
            placeholder: (_, __) => Pulse(child: ph),
            errorWidget: (_, __, ___) => ph,
          );
    return radius > 0
        ? ClipRRect(borderRadius: BorderRadius.circular(radius), child: img)
        : img;
  }
}

class StatusBadge extends StatelessWidget {
  const StatusBadge(this.status, {super.key});
  final String? status;

  @override
  Widget build(BuildContext context) {
    if (status == null || status!.isEmpty) return const SizedBox.shrink();
    final s = status!.toLowerCase();
    Color fg = const Color(0xFF64748B), bg = const Color(0xFFF1F5F9);
    if (s.contains('tidak berlaku') || s.contains('dicabut')) {
      fg = const Color(0xFF991B1B);
      bg = const Color(0xFFFEE2E2);
    } else if (s.contains('diubah') || s.contains('mengubah') || s.contains('mencabut')) {
      fg = const Color(0xFF854D0E);
      bg = const Color(0xFFFEF9C3);
    } else if (s.contains('berlaku')) {
      fg = const Color(0xFF166534);
      bg = const Color(0xFFDCFCE7);
    }
    final dark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: dark ? fg.withValues(alpha: .18) : bg,
        borderRadius: BorderRadius.circular(8),
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
    if (label == null || label!.isEmpty) return const SizedBox.shrink();
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: C.accent.withValues(alpha: .1),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Text(label!,
          style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: Theme.of(context).brightness == Brightness.dark
                  ? const Color(0xFF7EB5E3)
                  : C.accent)),
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
                  icon: const Icon(Icons.arrow_forward, size: 15, color: C.accent),
                  label: const Text('Lihat semua',
                      style: TextStyle(color: C.accent, fontWeight: FontWeight.w600))),
          ],
        ),
      );
}

/// Ikon dalam squircle tinted — vocabulary tile hub.
class IconSquircle extends StatelessWidget {
  const IconSquircle(this.icon, {super.key, this.color = C.accent, this.size = 44});
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
  const ErrorRetry(this.message, {super.key, required this.onRetry});
  final String message;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) => Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const IconSquircle(Icons.cloud_off, color: C.primary, size: 56),
              const SizedBox(height: 14),
              Text(message, textAlign: TextAlign.center),
              const SizedBox(height: 14),
              FilledButton(onPressed: onRetry, child: const Text('Coba lagi')),
            ],
          ),
        ),
      );
}

/// FutureBuilder + retry + pull-to-refresh. `builder` harus mengembalikan scrollable.
class LoadView extends StatefulWidget {
  const LoadView({super.key, required this.load, required this.builder});
  final Future<Json> Function() load;
  final Widget Function(BuildContext, Json) builder;

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
            return const SkeletonList(count: 4, height: 120);
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
      this.padding = const EdgeInsets.all(16)});
  final Future<Paginated> Function(int page) fetch;
  final Widget Function(BuildContext, Json) itemBuilder;
  final String empty;
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
      final p = await widget.fetch(_page);
      if (!mounted) return;
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
      return Center(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const IconSquircle(Icons.inbox_outlined, size: 56),
            const SizedBox(height: 10),
            Text(widget.empty),
            TextButton(onPressed: _refresh, child: const Text('Muat ulang')),
          ],
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
          final item = widget.itemBuilder(context, _items[i]);
          return i < 8 ? Rise(delayMs: i * 50, child: item) : item;
        },
      ),
    );
  }
}
