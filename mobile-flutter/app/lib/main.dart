import 'dart:math' as math;

import 'package:flutter/foundation.dart';
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
import 'screens/search.dart';
import 'theme.dart';
import 'widgets.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id');
  // tema dikunci terang -> ikon status bar selalu gelap
  SystemChrome.setSystemUIOverlayStyle(
    SystemUiOverlayStyle.dark.copyWith(statusBarColor: Colors.transparent),
  );

  // lisensi OFL font yang dibundel ikut tampil di halaman lisensi
  LicenseRegistry.addLicense(() async* {
    yield LicenseEntryWithLineBreaks([
      'Source Sans 3',
    ], await rootBundle.loadString('assets/fonts/OFL.txt'));
    yield LicenseEntryWithLineBreaks([
      'Source Serif 4',
    ], await rootBundle.loadString('assets/fonts/OFL-SourceSerif4.txt'));
  });

  final prefs = await SharedPreferences.getInstance();
  langNotifier.value = prefs.getString('lang') ?? 'id';
  langNotifier.addListener(() => prefs.setString('lang', langNotifier.value));

  runApp(const JdihApp());
}

class JdihApp extends StatelessWidget {
  const JdihApp({super.key});

  @override
  Widget build(BuildContext context) => ListenableBuilder(
    listenable: langNotifier,
    builder: (context, _) => MaterialApp(
      title: 'JDIH Kota Kendari',
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
      // ganti bahasa -> rebuild seluruh shell agar semua layar refetch
      home: RootShell(key: ValueKey(langNotifier.value)),
    ),
  );
}

/// Transisi antar-tab: fade saja, dijalankan ulang tiap kali index
/// berubah. Dipasang di atas IndexedStack — bukan menggantinya — supaya isi
/// tiap tab (hasil pencarian, posisi gulir) tidak ikut dibuang saat berpindah.
class _TabTransition extends StatefulWidget {
  const _TabTransition({super.key, required this.index, required this.child});
  final int index;
  final Widget child;

  @override
  State<_TabTransition> createState() => _TabTransitionState();
}

class _TabTransitionState extends State<_TabTransition>
    with SingleTickerProviderStateMixin {
  late final _c = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 200),
    value: 1,
  );

  @override
  void didUpdateWidget(_TabTransition old) {
    super.didUpdateWidget(old);
    if (old.index != widget.index) _c.forward(from: 0);
  }

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (MediaQuery.of(context).disableAnimations) return widget.child;
    // fade-through: berpindah tab bukan perjalanan, jadi tanpa gerak naik
    return FadeTransition(
      opacity: CurvedAnimation(parent: _c, curve: Curves.easeOutCubic),
      child: widget.child,
    );
  }
}

class RootShell extends StatefulWidget {
  const RootShell({super.key});

  @override
  State<RootShell> createState() => _RootShellState();
}

class _RootShellState extends State<RootShell> {
  /// Tab pencarian; navigasi bawah disembunyikan di sini agar kolom cari,
  /// papan ketik, dan jawaban AI dapat tinggi layar penuh.
  static const _cari = 2;

  /// Tab asal sebelum masuk pencarian: tujuan tombol kembali di sana, karena
  /// tanpa nav bar tidak ada cara lain untuk keluar.
  int _sebelumCari = 0;

  /// Tumpukan tab berpindah induk saat jendela melewati 600dp (HP diputar,
  /// foldable dibuka). Kunci global membawa state-nya ikut pindah: percakapan
  /// AI, hasil cari, dan posisi gulir tidak hilang karena rotasi.
  final _tumpukan = GlobalKey();

  /// Tab dibangun saat pertama dibuka, lalu dipertahankan. IndexedStack
  /// membangun semua anaknya sekaligus: saat cold start tab tersembunyi ikut
  /// memanggil API dan mengunduh foto berita (PNG 0,2-1,3 MB per foto).
  final _dibuka = <int>{};

  void _pindah(int i) {
    if (i == _cari) _sebelumCari = rootTab.value;
    rootTab.value = i;
  }

  @override
  Widget build(BuildContext context) => ValueListenableBuilder<int>(
    // tab disimpan di notifier, bukan state lokal: halaman detail yang
    // di-push perlu memindahkan tab saat pulang (lihat kembaliKeTab)
    valueListenable: rootTab,
    builder: (context, index, _) => PopScope(
      // Back sistem hanya menutup app dari Beranda. Dari tab lain ia pulang
      // dulu: dari Tanya AI ke tab asal (sama dengan panah di layarnya),
      // dari tab lain ke Beranda — pola tujuan awal Material
      canPop: index == 0,
      onPopInvokedWithResult: (didPop, _) {
        if (!didPop) _pindah(index == _cari ? _sebelumCari : 0);
      },
      child: Builder(
        builder: (context) {
          _dibuka.add(index);
          final halaman = [
            const HomeScreen(),
            const DocumentsHubScreen(),
            // tab berlabel Tanya AI: langsung ke percakapan
            SearchScreen(initialAi: true, onExit: () => _pindah(_sebelumCari)),
            const KabarHubScreen(),
            const LainnyaScreen(),
          ];
          final tab = _TabTransition(
            key: _tumpukan,
            index: index,
            child: IndexedStack(
              index: index,
              children: [
                for (final (i, h) in halaman.indexed)
                  // tab tersembunyi tidak dilukis, tetapi animasinya (kilau
                  // kerangka muat, denyut) tetap meminta frame tanpa henti
                  TickerMode(
                    enabled: i == index,
                    child: _dibuka.contains(i) ? h : const SizedBox.shrink(),
                  ),
              ],
            ),
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
            bottomNavigationBar: index == _cari
                ? null
                : NavBawah(index: index, onTap: _pindah),
          );
        },
      ),
    ),
  );
}

const _tujuan = [
  (icon: Icons.home_outlined, aktif: Icons.home, label: 'Beranda'),
  (
    icon: Icons.description_outlined,
    aktif: Icons.description,
    label: 'Dokumen',
  ),
  (icon: Icons.auto_awesome, aktif: Icons.auto_awesome, label: 'Tanya AI'),
  (icon: Icons.newspaper_outlined, aktif: Icons.newspaper, label: 'Kabar'),
  (icon: Icons.menu, aktif: Icons.menu, label: 'Menu'),
];

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
            child: IntrinsicHeight(child: _rail()),
          ),
        ),
      ),
    ),
  );

  Widget _rail() => NavigationRail(
    selectedIndex: _tab.contains(index) ? _tab.indexOf(index) : null,
    onDestinationSelected: (i) => onTap(_tab[i]),
    labelType: NavigationRailLabelType.all,
    leading: Padding(
      padding: const EdgeInsets.only(top: AppSpacing.sm, bottom: AppSpacing.lg),
      child: _TombolAiRail(aktif: index == 2, onTap: () => onTap(2)),
    ),
    destinations: [
      for (final t in _tujuan)
        if (t.label != 'Tanya AI')
          NavigationRailDestination(
            icon: Icon(t.icon),
            selectedIcon: Icon(t.aktif),
            label: Text(t.label),
          ),
    ],
  );
}

class _TombolAiRail extends StatelessWidget {
  const _TombolAiRail({required this.aktif, required this.onTap});
  final bool aktif;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Semantics(
    button: true,
    selected: aktif,
    label: 'Tanya AI dan pencarian',
    onTap: onTap,
    excludeSemantics: true,
    child: GestureDetector(
      behavior: HitTestBehavior.opaque,
      onTap: onTap,
      child: Column(
        children: [
          Material(
            color: C.primary,
            elevation: aktif ? 0 : 2,
            shadowColor: C.ink.withValues(alpha: .3),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(AppRadius.button),
              // terbuka = bertepi gelap tipis, padanan indikator aktif rail
              side: aktif
                  ? const BorderSide(color: C.primaryInk, width: 2)
                  : BorderSide.none,
            ),
            child: InkWell(
              onTap: onTap,
              borderRadius: BorderRadius.circular(AppRadius.button),
              child: const SizedBox(
                width: 56,
                height: 56,
                child: Icon(Icons.auto_awesome, color: C.ink, size: 24),
              ),
            ),
          ),
          const SizedBox(height: AppSpacing.xs),
          Text(
            'Tanya AI',
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

/// Navigasi bawah melayang: empat tujuan + tombol Tanya AI yang menonjol di
/// tengah — pintu masuk pencarian dan AI, aksi utama aplikasi ini. Semua
/// tujuan tetap berlabel; ikon saja ambigu bagi pengguna awam.
class NavBawah extends StatelessWidget {
  const NavBawah({super.key, required this.index, required this.onTap});
  final int index;
  final ValueChanged<int> onTap;

  /// Tombol tengah menyembul sejauh ini di atas bar.
  static const _sembul = 16.0;

  @override
  Widget build(BuildContext context) {
    // label ikut membesar dengan ukuran teks sistem, dibatasi 150% supaya
    // "Beranda"/"Dokumen" tetap utuh di slot selebar seperlima layar; tinggi
    // bar menyesuaikan, jadi tidak ada yang terpotong
    final teks = MediaQuery.textScalerOf(context).clamp(maxScaleFactor: 1.5);
    final label = teks.scale(_gayaLabel.fontSize!) * _gayaLabel.height!;
    return MediaQuery(
      data: MediaQuery.of(context).copyWith(textScaler: teks),
      child: SafeArea(
        top: false,
        minimum: const EdgeInsets.only(bottom: AppSpacing.sm),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
          child: SizedBox(
            // tombol tengah (56) + label + jarak dasar menentukan tinggi minimum
            height: math.max(64 + _sembul, 56 + label + _dasarLabel),
            child: Stack(
              children: [
                Positioned.fill(
                  top: _sembul,
                  child: DecoratedBox(
                    decoration: BoxDecoration(
                      color: C.lightSurface,
                      borderRadius: BorderRadius.circular(AppRadius.card),
                      border: Border.all(color: C.lightLine),
                      // bar benar-benar melayang di atas isi: satu bayangan lembut
                      boxShadow: [
                        BoxShadow(
                          color: C.ink.withValues(alpha: .08),
                          blurRadius: 16,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                  ),
                ),
                Row(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    for (final (i, t) in _tujuan.indexed)
                      Expanded(
                        child: i == 2
                            ? _TombolTengah(
                                label: t.label,
                                onTap: () => onTap(i),
                              )
                            : _Item(
                                icon: index == i ? t.aktif : t.icon,
                                label: t.label,
                                aktif: index == i,
                                onTap: () => onTap(i),
                              ),
                      ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

/// Label tujuan; tinggi baris tetap supaya tinggi bar bisa dihitung.
const _gayaLabel = T.label;

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
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(top: NavBawah._sembul),
    child: MergeSemantics(
      child: Semantics(
        selected: aktif,
        button: true,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(AppRadius.card),
          // dasar label rata dengan label tombol tengah
          child: Column(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              // penanda aktif: kotak bertinta oranye di belakang ikon
              AnimatedContainer(
                duration: MediaQuery.of(context).disableAnimations
                    ? Duration.zero
                    : const Duration(milliseconds: 200),
                curve: Curves.easeOutCubic,
                width: 52,
                height: 30,
                decoration: BoxDecoration(
                  color: aktif
                      ? C.primary.withValues(alpha: .16)
                      : Colors.transparent,
                  borderRadius: BorderRadius.circular(AppRadius.chip),
                  border: Border.all(
                    color: aktif
                        ? C.primary.withValues(alpha: .55)
                        : Colors.transparent,
                  ),
                ),
                child: Icon(
                  icon,
                  size: 22,
                  color: aktif ? C.primaryInk : C.lightInkMuted,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                label,
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: _gayaLabel.copyWith(
                  fontWeight: aktif ? FontWeight.w700 : FontWeight.w500,
                  color: aktif ? C.primaryInk : C.lightInkMuted,
                ),
              ),
              const SizedBox(height: _dasarLabel),
            ],
          ),
        ),
      ),
    ),
  );
}

/// Jarak label ke dasar bar, sama untuk semua slot.
const _dasarLabel = 8.0;

/// Kotak oranye bersudut 4dp yang menyembul di atas bar, labelnya sebaris
/// dengan label tujuan lain. Cincin berwarna latar halaman memotong tepi bar
/// sehingga tombol tampak duduk di lekukan.
class _TombolTengah extends StatelessWidget {
  const _TombolTengah({required this.label, required this.onTap});
  final String label;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => Semantics(
    button: true,
    label: '$label dan pencarian',
    onTap: onTap,
    excludeSemantics: true,
    child: GestureDetector(
      behavior: HitTestBehavior.opaque,
      onTap: onTap,
      child: Column(
        children: [
          Pressable(
            child: Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: C.lightBg,
                borderRadius: BorderRadius.circular(AppRadius.button + 4),
              ),
              child: Material(
                color: C.primary,
                elevation: 2,
                shadowColor: C.ink.withValues(alpha: .3),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(AppRadius.button),
                ),
                child: InkWell(
                  onTap: onTap,
                  borderRadius: BorderRadius.circular(AppRadius.button),
                  child: const SizedBox(
                    width: 48,
                    height: 48,
                    // teks/ikon gelap di atas oranye: kontras >= 4,5:1
                    child: Icon(Icons.auto_awesome, color: C.ink, size: 24),
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
              fontWeight: FontWeight.w700,
              color: C.lightInk,
            ),
          ),
          const SizedBox(height: _dasarLabel),
        ],
      ),
    ),
  );
}
