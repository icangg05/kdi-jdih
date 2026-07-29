import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:shared_preferences/shared_preferences.dart';

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
      SystemUiOverlayStyle.dark.copyWith(statusBarColor: Colors.transparent));

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
          // ganti bahasa -> rebuild seluruh shell agar semua layar refetch
          home: RootShell(key: ValueKey(langNotifier.value)),
        ),
      );
}

/// Transisi antar-tab: fade + naik tipis, dijalankan ulang tiap kali index
/// berubah. Dipasang di atas IndexedStack — bukan menggantinya — supaya isi
/// tiap tab (hasil pencarian, posisi gulir) tidak ikut dibuang saat berpindah.
class _TabTransition extends StatefulWidget {
  const _TabTransition({required this.index, required this.child});
  final int index;
  final Widget child;

  @override
  State<_TabTransition> createState() => _TabTransitionState();
}

class _TabTransitionState extends State<_TabTransition>
    with SingleTickerProviderStateMixin {
  late final _c = AnimationController(
      vsync: this, duration: const Duration(milliseconds: 260), value: 1);

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
    final curved = CurvedAnimation(parent: _c, curve: Curves.easeOutCubic);
    return FadeTransition(
      opacity: curved,
      child: SlideTransition(
        position: Tween(begin: const Offset(0, .02), end: Offset.zero)
            .animate(curved),
        child: widget.child,
      ),
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

  void _pindah(int i) {
    if (i == _cari) _sebelumCari = rootTab.value;
    rootTab.value = i;
  }

  @override
  Widget build(BuildContext context) => ValueListenableBuilder<int>(
        // tab disimpan di notifier, bukan state lokal: halaman detail yang
        // di-push perlu memindahkan tab saat pulang (lihat kembaliKeTab)
        valueListenable: rootTab,
        builder: (context, index, _) => Scaffold(
          body: _TabTransition(
            index: index,
            child: IndexedStack(index: index, children: [
              const HomeScreen(),
              const DocumentsHubScreen(),
              SearchScreen(onExit: () => _pindah(_sebelumCari)),
              const KabarHubScreen(),
              const LainnyaScreen(),
            ]),
          ),
          bottomNavigationBar: index == _cari
              ? null
              : FloatingNavBar(
                  index: index,
                  onChanged: _pindah,
                items: const [
                  (
                    icon: Icons.home_outlined,
                    activeIcon: Icons.home,
                    label: 'Beranda'
                  ),
                  (
                    icon: Icons.description_outlined,
                    activeIcon: Icons.description,
                    label: 'Dokumen'
                  ),
                  (
                    icon: Icons.auto_awesome,
                    activeIcon: Icons.auto_awesome,
                    label: 'Cari AI'
                  ),
                  (
                    icon: Icons.newspaper_outlined,
                    activeIcon: Icons.newspaper,
                    label: 'Kabar'
                  ),
                  (icon: Icons.menu, activeIcon: Icons.menu, label: 'Menu'),
                ],
              ),
        ),
      );
}
