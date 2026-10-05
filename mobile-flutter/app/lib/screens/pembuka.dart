import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';

/// Layar pembuka di atas beranda yang sedang dimuat. Dimulai persis seperti
/// splash sistem (logo di kanvas slate-50) supaya peralihannya tak terlihat,
/// lalu: riak cincin dari logo, tirai biru berpola yang membuka melingkar,
/// lencana putih di belakang logo, nama aplikasi kata demi kata, partikel
/// yang naik, dan orbit amber selagi beranda dimuat. Ketuk untuk melewati.
class Pembuka extends StatefulWidget {
  const Pembuka({super.key, required this.onSelesai});
  final VoidCallback onSelesai;

  @override
  State<Pembuka> createState() => _PembukaState();
}

class _PembukaState extends State<Pembuka> with TickerProviderStateMixin {
  static const _logo = AssetImage('assets/img/logo-jdih.png');

  // preserve: durasi tidak dipangkas sistem; mode gerak-dikurangi diatur
  // sendiri (tampilan akhir diam sebentar, lalu memudar)
  late final _intro = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 2300),
    animationBehavior: AnimationBehavior.preserve,
  );
  late final _putar = AnimationController(
    vsync: this,
    duration: const Duration(seconds: 1),
  );
  late final _keluar = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 450),
    animationBehavior: AnimationBehavior.preserve,
  );
  var _diam = false;
  var _siap = false;

  @override
  void initState() {
    super.initState();
    // splash sistem bertahan sampai logo selesai didekode: tanpa ini satu-dua
    // frame pertama kosong dan logo tampak berkedip
    WidgetsBinding.instance.deferFirstFrame();
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_siap) return;
    _siap = true;
    _diam = MediaQuery.of(context).disableAnimations;
    precacheImage(_logo, context).whenComplete(() {
      WidgetsBinding.instance.allowFirstFrame();
      if (!mounted) return;
      if (_diam) {
        _intro.duration = const Duration(milliseconds: 800);
      } else {
        _putar.repeat();
      }
      _intro.forward().whenComplete(_tutup);
    });
  }

  void _tutup() {
    if (!mounted || _keluar.status != AnimationStatus.dismissed) return;
    _keluar.forward().whenComplete(() {
      if (mounted) widget.onSelesai();
    });
  }

  @override
  void dispose() {
    _intro.dispose();
    _putar.dispose();
    _keluar.dispose();
    super.dispose();
  }

  // Material transparan: pembuka duduk di luar Scaffold, dan tanpa ini Text
  // memakai gaya darurat Flutter (merah, bergaris bawah kuning)
  @override
  Widget build(BuildContext context) => Material(
    type: MaterialType.transparency,
    child: Semantics(
      label: context.l10n.loadingApp(context.l10n.appName),
      excludeSemantics: true,
      child: GestureDetector(
        behavior: HitTestBehavior.opaque,
        onTap: _tutup,
        child: AnimatedBuilder(
          animation: Listenable.merge([_intro, _putar, _keluar]),
          builder: (context, _) => _bingkai(context),
        ),
      ),
    ),
  );

  Widget _bingkai(BuildContext context) {
    final layar = MediaQuery.sizeOf(context);
    final t = _diam ? 1.0 : _intro.value;
    double fase(double a, double b, [Curve c = Curves.easeOutCubic]) =>
        c.transform(((t - a) / (b - a)).clamp(0.0, 1.0));

    final tirai = fase(.16, .52, Curves.easeInOutCubic);
    final lencana = fase(.28, .58, Curves.easeOutBack);
    final angkat = fase(.42, .72, Curves.easeInOutCubic);
    final judul = fase(.50, .82);
    final sub = fase(.62, .92);
    final orbit = fase(.60, .85);
    final napas = 1 + .06 * math.sin(math.pi * fase(0, .22, Curves.linear));
    final keluar = Curves.easeInCubic.transform(_keluar.value);
    // waktu berjalan terus untuk partikel & orbit (lastElapsed tidak kembali
    // ke nol tiap putaran repeat)
    final detik = (_putar.lastElapsedDuration?.inMicroseconds ?? 0) / 1e6;

    // lencana menyesuaikan layar pendek (HP lanskap); logo selebar 1,5 r =
    // ±150dp di ponsel, sama dengan logo splash sistem
    final r = math.min(100.0, layar.shortestSide * .27);
    final pusat = Offset(layar.width / 2, layar.height / 2 - r * .56 * angkat);
    final jauh = [
      Offset.zero,
      Offset(layar.width, 0),
      Offset(0, layar.height),
      Offset(layar.width, layar.height),
    ].map((p) => (p - pusat).distance).reduce(math.max);

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value:
          (tirai > .3 ? SystemUiOverlayStyle.light : SystemUiOverlayStyle.dark)
              .copyWith(statusBarColor: Colors.transparent),
      child: Opacity(
        opacity: 1 - keluar,
        child: Transform.scale(
          scale: 1 + .08 * keluar,
          child: Stack(
            fit: StackFit.expand,
            children: [
              const ColoredBox(color: C.lightBg),
              ClipPath(
                clipper: _Lingkar(pusat, jauh * tirai),
                child: const PolaBiru(),
              ),
              CustomPaint(
                painter: _Hiasan(
                  pusat: pusat,
                  r: r,
                  t: t,
                  tirai: tirai,
                  orbit: orbit,
                  detik: detik,
                ),
              ),
              Positioned(
                left: pusat.dx - r,
                top: pusat.dy - r,
                width: r * 2,
                height: r * 2,
                child: Transform.scale(
                  scale: lencana,
                  child: DecoratedBox(
                    decoration: BoxDecoration(
                      color: C.lightSurface,
                      shape: BoxShape.circle,
                      boxShadow: [
                        BoxShadow(
                          color: C.accentNight.withValues(alpha: .35),
                          blurRadius: 32,
                          offset: const Offset(0, 12),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
              Positioned(
                left: pusat.dx - r * .75,
                top: pusat.dy - r * .58,
                width: r * 1.5,
                height: r * 1.16,
                child: Transform.scale(
                  scale: napas,
                  child: const Image(image: _logo, fit: BoxFit.contain),
                ),
              ),
              Positioned(
                left: AppSpacing.xl,
                right: AppSpacing.xl,
                top: pusat.dy + r + AppSpacing.xl,
                child: Column(
                  children: [
                    _kataDemiKata(context.l10n.appName, judul),
                    const SizedBox(height: AppSpacing.sm),
                    Opacity(
                      opacity: sub,
                      child: Text(
                        context.l10n.jdihFull,
                        textAlign: TextAlign.center,
                        style: T.isi.copyWith(
                          color: C.onDark.withValues(alpha: .85),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              Positioned(
                left: 0,
                right: 0,
                bottom: MediaQuery.paddingOf(context).bottom + AppSpacing.xl,
                child: Opacity(
                  opacity: sub,
                  child: Text(
                    context.l10n.kendariGov,
                    textAlign: TextAlign.center,
                    style: T.labelKecil.copyWith(
                      color: C.onDark.withValues(alpha: .7),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// Tiap kata naik dan muncul bergiliran.
  Widget _kataDemiKata(String teks, double p) {
    final kata = teks.split(' ');
    return Wrap(
      alignment: WrapAlignment.center,
      spacing: 8,
      children: [
        for (final (i, k) in kata.indexed)
          Builder(
            builder: (_) {
              final q = (p * (1 + .35 * (kata.length - 1)) - i * .35).clamp(
                0.0,
                1.0,
              );
              return Opacity(
                opacity: q,
                child: Transform.translate(
                  offset: Offset(0, 14 * (1 - q)),
                  child: Text(k, style: T.hero.copyWith(color: C.onDark)),
                ),
              );
            },
          ),
      ],
    );
  }
}

/// Tirai melingkar dari pusat logo.
class _Lingkar extends CustomClipper<Path> {
  _Lingkar(this.pusat, this.jari);
  final Offset pusat;
  final double jari;

  @override
  Path getClip(Size size) =>
      Path()..addOval(Rect.fromCircle(center: pusat, radius: jari));

  @override
  bool shouldReclip(_Lingkar old) => old.pusat != pusat || old.jari != jari;
}

/// Riak cincin, partikel naik, dan orbit amber di sekeliling lencana.
class _Hiasan extends CustomPainter {
  _Hiasan({
    required this.pusat,
    required this.r,
    required this.t,
    required this.tirai,
    required this.orbit,
    required this.detik,
  });
  final Offset pusat;
  final double r, t, tirai, orbit, detik;

  /// Partikel tetap (benih sama) supaya tidak melompat antar-frame.
  static final _partikel = () {
    final acak = math.Random(7);
    return [
      for (var i = 0; i < 30; i++)
        (
          x: acak.nextDouble(),
          fase: acak.nextDouble(),
          laju: .04 + acak.nextDouble() * .08,
          besar: 1 + acak.nextDouble() * 2,
          amber: i % 6 == 0,
        ),
    ];
  }();

  @override
  void paint(Canvas canvas, Size size) {
    // riak: tiga cincin bergantian amber/biru, mengembang dari logo
    for (var i = 0; i < 3; i++) {
      final p = ((t - i * .07) / .32).clamp(0.0, 1.0);
      if (p <= 0 || p >= 1) continue;
      final e = Curves.easeOutCubic.transform(p);
      canvas.drawCircle(
        pusat,
        r * .8 + e * size.longestSide * .55,
        Paint()
          ..style = PaintingStyle.stroke
          ..strokeWidth = 2.5 * (1 - e) + .5
          ..color = (i.isEven ? C.primary : C.accent).withValues(
            alpha: .55 * (1 - e),
          ),
      );
    }

    // partikel naik di atas biru, ikut muncul bersama tirai
    if (tirai > 0) {
      final titik = Paint();
      for (final p in _partikel) {
        final y =
            size.height * (1 - ((p.fase + detik * p.laju) % 1.0)) * 1.05 - 10;
        final kedip = .5 + .5 * math.sin(detik * 2 + p.fase * 6);
        titik.color = (p.amber ? C.primary : Colors.white).withValues(
          alpha: tirai * (.18 + .32 * kedip),
        );
        canvas.drawCircle(Offset(p.x * size.width, y), p.besar, titik);
      }
    }

    // orbit: lintasan tipis + busur amber yang berputar, satu titik putih
    // berputar berlawanan sedikit lebih jauh
    if (orbit > 0) {
      final jalur = r + 12;
      canvas.drawCircle(
        pusat,
        jalur,
        Paint()
          ..style = PaintingStyle.stroke
          ..strokeWidth = 1
          ..color = Colors.white.withValues(alpha: .22 * orbit),
      );
      final sudut = detik * math.pi * 1.4;
      canvas.drawArc(
        Rect.fromCircle(center: pusat, radius: jalur),
        sudut,
        math.pi * .55,
        false,
        Paint()
          ..style = PaintingStyle.stroke
          ..strokeWidth = 3.5
          ..strokeCap = StrokeCap.round
          ..color = C.primary.withValues(alpha: orbit),
      );
      final balik = -detik * math.pi * .9;
      canvas.drawCircle(
        pusat + Offset(math.cos(balik), math.sin(balik)) * (jalur + 10),
        3,
        Paint()..color = Colors.white.withValues(alpha: .9 * orbit),
      );
    }
  }

  @override
  bool shouldRepaint(_Hiasan old) => true;
}
