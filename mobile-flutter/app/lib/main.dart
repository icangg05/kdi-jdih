import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:skeletonizer/skeletonizer.dart';

import 'api.dart';
import 'screens/documents.dart';
import 'screens/home.dart';
import 'screens/kabar.dart';
import 'screens/lainnya.dart';
import 'screens/pembuka.dart';
import 'screens/search.dart';
import 'theme.dart';
import 'widgets.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting();
  // tema dikunci terang -> ikon status bar selalu gelap
  SystemChrome.setSystemUIOverlayStyle(
    SystemUiOverlayStyle.dark.copyWith(statusBarColor: Colors.transparent),
  );

  final prefs = await SharedPreferences.getInstance();
  langNotifier.value = prefs.getString('lang') ?? 'id';
  langNotifier.addListener(() => prefs.setString('lang', langNotifier.value));
  percakapanAi.muat(prefs);

  runApp(const JdihApp());
}

class JdihApp extends StatelessWidget {
  const JdihApp({super.key});

  @override
  Widget build(BuildContext context) => ListenableBuilder(
    listenable: langNotifier,
    builder: (context, _) => MaterialApp(
      onGenerateTitle: (c) => c.l10n.appName,
      // bahasa UI = bahasa konten (langNotifier); teks Material bawaan
      // (tooltip, menu salin-tempel) ikut lewat GlobalMaterialLocalizations
      locale: Locale(langNotifier.value),
      supportedLocales: AppLocalizations.supportedLocales,
      localizationsDelegates: AppLocalizations.localizationsDelegates,
      debugShowCheckedModeBanner: false,
      // tema dikunci terang; dark theme tetap ada kalau nanti dibuka lagi
      theme: appTheme(Brightness.light),
      darkTheme: appTheme(Brightness.dark),
      themeMode: ThemeMode.light,
      // satu efek kerangka untuk seluruh app, berwarna brand; shimmer
      // diganti warna diam bila pengguna meminta animasi dikurangi
      builder: (context, child) => SkeletonizerConfig(
        data: SkeletonizerConfigData(
          brightness: Brightness.light,
          effectResolver: (_) => MediaQuery.of(context).disableAnimations
              ? const SolidColorEffect(color: C.lightLine)
              : const ShimmerEffect(
                  baseColor: C.lightLine,
                  highlightColor: C.lightSurface,
                ),
        ),
        child: child!,
      ),
      home: const _Awal(),
    ),
  );
}

/// Beranda dibangun (dan mulai memuat) di bawah layar pembuka: saat pembuka
/// memudar, isinya sudah datang atau tinggal sebentar lagi.
class _Awal extends StatefulWidget {
  const _Awal();

  @override
  State<_Awal> createState() => _AwalState();
}

class _AwalState extends State<_Awal> {
  var _pembuka = true;

  @override
  Widget build(BuildContext context) => Stack(
    fit: StackFit.expand,
    children: [
      ExcludeSemantics(
        excluding: _pembuka,
        // ganti bahasa -> rebuild seluruh shell agar semua layar refetch.
        // Didengar di sini: _Awal const tidak ikut dibangun ulang MaterialApp
        child: ListenableBuilder(
          listenable: langNotifier,
          builder: (_, _) => RootShell(key: ValueKey(langNotifier.value)),
        ),
      ),
      if (_pembuka) Pembuka(onSelesai: () => setState(() => _pembuka = false)),
    ],
  );
}

class RootShell extends StatefulWidget {
  const RootShell({super.key});

  @override
  State<RootShell> createState() => _RootShellState();
}

class _RootShellState extends State<RootShell> {
  /// Slot tombol Tanya AI di navigasi bawah dan rail. Bukan tab: ia membuka
  /// rute yang sama dengan tombol Tanya AI di beranda (lihat bukaTanyaAi),
  /// jadi animasi buka-tutup dan percakapannya pun sama.
  static const _ai = 2;

  /// Tumpukan tab berpindah induk saat jendela melewati 600dp (HP diputar,
  /// foldable dibuka). Kunci global membawa state-nya ikut pindah: hasil
  /// cari dan posisi gulir tidak hilang karena rotasi.
  final _tumpukan = GlobalKey();

  /// Tab dibangun saat pertama dibuka, lalu dipertahankan. IndexedStack
  /// membangun semua anaknya sekaligus: saat cold start tab tersembunyi ikut
  /// memanggil API dan mengunduh foto berita (PNG 0,2-1,3 MB per foto).
  final _dibuka = <int>{};

  void _pindah(int i) {
    if (i == _ai) return bukaTanyaAi(context);
    rootTab.value = i;
  }

  @override
  Widget build(BuildContext context) => ValueListenableBuilder<int>(
    // tab disimpan di notifier, bukan state lokal: halaman detail yang
    // di-push perlu memindahkan tab saat pulang (lihat kembaliKeTab)
    valueListenable: rootTab,
    builder: (context, index, _) => PopScope(
      // Back sistem hanya menutup app dari Beranda. Dari tab lain ia pulang
      // dulu ke Beranda — pola tujuan awal Material
      canPop: index == 0,
      onPopInvokedWithResult: (didPop, _) {
        if (!didPop) rootTab.value = 0;
      },
      child: Builder(
        builder: (context) {
          _dibuka.add(index);
          final halaman = [
            const HomeScreen(),
            const DocumentsHubScreen(),
            const SizedBox.shrink(), // slot tombol Tanya AI
            const KabarHubScreen(),
            const LainnyaScreen(),
          ];
          // tab berganti seketika: berpindah tab bukan perjalanan
          final tab = IndexedStack(
            key: _tumpukan,
            index: index,
            sizing: StackFit.expand,
            children: [
              for (final (i, h) in halaman.indexed)
                // tab tersembunyi tidak dilukis, tetapi animasinya (kilau
                // kerangka muat, denyut) tetap meminta frame tanpa henti
                TickerMode(
                  enabled: i == index,
                  child: _dibuka.contains(i) ? h : const SizedBox.shrink(),
                ),
            ],
          );
          // jendela >= 600dp: rail di samping, bukan bar bawah yang melar
          if (MediaQuery.sizeOf(context).width >= kLebarSedang) {
            return Scaffold(
              body: Row(
                children: [
                  RailSamping(index: index, onTap: _pindah),
                  const VerticalDivider(width: 1),
                  Expanded(child: Panel(child: tab)),
                ],
              ),
            );
          }
          return Scaffold(
            body: tab,
            bottomNavigationBar: NavBawah(index: index, onTap: _pindah),
          );
        },
      ),
    ),
  );
}

const _tujuan = [
  (icon: Icons.home_outlined, aktif: Icons.home),
  (icon: Icons.description_outlined, aktif: Icons.description),
  (icon: Icons.auto_awesome, aktif: Icons.auto_awesome),
  (icon: Icons.newspaper_outlined, aktif: Icons.newspaper),
  (icon: Icons.menu, aktif: Icons.menu),
];

/// Label [_tujuan], urutan sama.
List<String> _labelTujuan(BuildContext context) {
  final l = context.l10n;
  return [l.navHome, l.navDocuments, l.navAskAi, l.navNews, l.navMenu];
}

/// Navigasi samping untuk jendela >= 600dp (tablet, foldable terbuka, HP
/// lanskap): NavigationRail Material, dengan Tanya AI sebagai aksi utama di
/// puncaknya — padanan tombol tengah navigasi bawah.
class RailSamping extends StatelessWidget {
  const RailSamping({super.key, required this.index, required this.onTap});
  final int index;
  final ValueChanged<int> onTap;

  /// Indeks tab untuk tiap tujuan rail (Tanya AI ada di puncak, bukan tujuan).
  static const _tab = [0, 1, 3, 4];

  // Label dijepit 150% (sama dengan NavBawah) dan rail bisa digulir: HP
  // lanskap hanya setinggi ±390dp, dan huruf besar tak boleh memotong menu.
  @override
  Widget build(BuildContext context) => SafeArea(
    right: false,
    child: MediaQuery(
      data: MediaQuery.of(context).copyWith(
        textScaler: MediaQuery.textScalerOf(context).clamp(maxScaleFactor: 1.5),
      ),
      child: LayoutBuilder(
        builder: (context, box) => SingleChildScrollView(
          child: ConstrainedBox(
            constraints: BoxConstraints(minHeight: box.maxHeight),
            child: IntrinsicHeight(child: _rail(_labelTujuan(context))),
          ),
        ),
      ),
    ),
  );

  Widget _rail(List<String> label) => NavigationRail(
    selectedIndex: _tab.indexOf(index),
    onDestinationSelected: (i) => onTap(_tab[i]),
    labelType: NavigationRailLabelType.all,
    leading: Padding(
      padding: const EdgeInsets.only(top: AppSpacing.sm, bottom: AppSpacing.lg),
      child: _TombolAiRail(onTap: () => onTap(2)),
    ),
    destinations: [
      for (final i in _tab)
        NavigationRailDestination(
          icon: Icon(_tujuan[i].icon),
          selectedIcon: Icon(_tujuan[i].aktif),
          label: Text(label[i]),
        ),
    ],
  );
}

class _TombolAiRail extends StatelessWidget {
  const _TombolAiRail({required this.onTap});
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Semantics(
    button: true,
    label: context.l10n.aiAndSearch(context.l10n.navAskAi),
    onTap: onTap,
    excludeSemantics: true,
    child: GestureDetector(
      behavior: HitTestBehavior.opaque,
      onTap: onTap,
      child: Column(
        children: [
          Material(
            color: C.primary,
            elevation: 2,
            shadowColor: C.ink.withValues(alpha: .3),
            // lingkaran, sama dengan tombol tengah NavBawah
            shape: const CircleBorder(),
            clipBehavior: Clip.antiAlias,
            child: InkWell(
              onTap: onTap,
              child: const SizedBox(
                width: 56,
                height: 56,
                child: Icon(Icons.auto_awesome, color: C.ink, size: 24),
              ),
            ),
          ),
          const SizedBox(height: AppSpacing.xs),
          Text(
            context.l10n.navAskAi,
            style: T.label.copyWith(
              color: C.lightInk,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    ),
  );
}

/// Navigasi bawah: bar putih selebar layar, empat tujuan + tombol Tanya AI
/// berbentuk lingkaran yang menyembul di tengah — pintu masuk pencarian dan
/// AI, aksi utama aplikasi ini. Semua tujuan tetap berlabel; ikon saja
/// ambigu bagi pengguna awam.
class NavBawah extends StatelessWidget {
  const NavBawah({super.key, required this.index, required this.onTap});
  final int index;
  final ValueChanged<int> onTap;

  /// Lingkaran tengah menyembul sejauh ini di atas bar.
  static const _sembul = 24.0;

  @override
  Widget build(BuildContext context) {
    // label ikut membesar dengan ukuran teks sistem, dibatasi 150% supaya
    // "Beranda"/"Dokumen" tetap utuh di slot selebar seperlima layar; tinggi
    // bar menyesuaikan, jadi tidak ada yang terpotong
    final teks = MediaQuery.textScalerOf(context).clamp(maxScaleFactor: 1.5);
    final label = teks.scale(_gayaLabel.fontSize!) * _gayaLabel.height!;
    final labelTujuan = _labelTujuan(context);
    final bawah = MediaQuery.paddingOf(context).bottom;
    return MediaQuery(
      data: MediaQuery.of(context).copyWith(textScaler: teks),
      child: SizedBox(
        // lingkaran bercincin + label + jarak dasar menentukan tinggi minimum
        height:
            math.max(64 + _sembul, _TombolTengah.garis + 2 + label + _dasar) +
            bawah,
        child: Stack(
          children: [
            Positioned.fill(
              top: _sembul,
              child: DecoratedBox(
                decoration: BoxDecoration(
                  color: C.lightSurface,
                  border: const Border(top: BorderSide(color: C.lightLine)),
                  boxShadow: [
                    BoxShadow(
                      color: C.ink.withValues(alpha: .06),
                      blurRadius: 16,
                      offset: const Offset(0, -4),
                    ),
                  ],
                ),
              ),
            ),
            Padding(
              // bar ikut mewarnai area gestur sistem, isinya di atasnya
              padding: EdgeInsets.only(bottom: bawah),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  for (final (i, t) in _tujuan.indexed)
                    Expanded(
                      child: i == 2
                          ? _TombolTengah(
                              label: labelTujuan[i],
                              onTap: () => onTap(i),
                            )
                          : _Item(
                              icon: index == i ? t.aktif : t.icon,
                              label: labelTujuan[i],
                              aktif: index == i,
                              onTap: () => onTap(i),
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

/// Label tujuan; tinggi baris tetap supaya tinggi bar bisa dihitung.
const _gayaLabel = T.label;

/// Jarak label ke dasar bar, sama untuk semua slot.
const _dasar = 8.0;

class _Item extends StatelessWidget {
  const _Item({
    required this.icon,
    required this.label,
    required this.aktif,
    required this.onTap,
  });
  final IconData icon;
  final String label;
  final bool aktif;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final durasi = MediaQuery.of(context).disableAnimations
        ? Duration.zero
        : const Duration(milliseconds: 200);
    final warna = aktif ? C.primaryInk : C.lightInkMuted;
    return Padding(
      padding: const EdgeInsets.only(top: NavBawah._sembul),
      child: MergeSemantics(
        child: Semantics(
          selected: aktif,
          button: true,
          child: InkWell(
            onTap: onTap,
            // dasar label rata dengan label tombol tengah
            child: Column(
              children: [
                // penanda aktif: garis amber pendek di tepi atas bar
                AnimatedContainer(
                  duration: durasi,
                  curve: Curves.easeOutCubic,
                  width: aktif ? 24 : 0,
                  height: 3,
                  decoration: BoxDecoration(
                    color: C.primary,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
                const Spacer(),
                AnimatedScale(
                  duration: durasi,
                  curve: Curves.easeOutBack,
                  scale: aktif ? 1.12 : 1,
                  child: Icon(icon, size: 24, color: warna),
                ),
                const SizedBox(height: 4),
                Text(
                  label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: _gayaLabel.copyWith(
                    fontWeight: aktif ? FontWeight.w600 : FontWeight.w400,
                    color: warna,
                  ),
                ),
                const SizedBox(height: _dasar),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

/// Lingkaran amber yang menyembul di atas bar, labelnya sebaris dengan label
/// tujuan lain. Cincin putih selebar 5dp menyatukannya dengan bar sehingga
/// tombol tampak duduk di lekukan.
class _TombolTengah extends StatelessWidget {
  const _TombolTengah({required this.label, required this.onTap});
  final String label;
  final VoidCallback onTap;

  static const _isi = 56.0;
  static const _cincin = 5.0;

  /// Diameter lingkaran beserta cincinnya.
  static const garis = _isi + _cincin * 2;

  @override
  Widget build(BuildContext context) => Semantics(
    button: true,
    label: context.l10n.aiAndSearch(label),
    onTap: onTap,
    excludeSemantics: true,
    child: GestureDetector(
      behavior: HitTestBehavior.opaque,
      onTap: onTap,
      child: Column(
        children: [
          Pressable(
            child: Container(
              width: garis,
              height: garis,
              padding: const EdgeInsets.all(_cincin),
              decoration: const BoxDecoration(
                color: C.lightSurface,
                shape: BoxShape.circle,
              ),
              child: DecoratedBox(
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: const LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Color(0xFFFFA24D), C.primary],
                  ),
                  // cahaya amber di bawah lingkaran, bukan bayangan abu
                  boxShadow: [
                    BoxShadow(
                      color: C.primary.withValues(alpha: .45),
                      blurRadius: 14,
                      offset: const Offset(0, 5),
                    ),
                  ],
                ),
                child: Material(
                  type: MaterialType.transparency,
                  shape: const CircleBorder(),
                  clipBehavior: Clip.antiAlias,
                  child: InkWell(
                    onTap: onTap,
                    // ikon gelap di atas amber: kontras >= 4,5:1
                    child: const Icon(
                      Icons.auto_awesome,
                      color: C.ink,
                      size: 26,
                    ),
                  ),
                ),
              ),
            ),
          ),
          const Spacer(),
          Text(
            label,
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: _gayaLabel.copyWith(
              fontWeight: FontWeight.w600,
              color: C.lightInk,
            ),
          ),
          const SizedBox(height: _dasar),
        ],
      ),
    ),
  );
}
