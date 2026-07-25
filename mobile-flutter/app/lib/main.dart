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
  SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle.dark
      .copyWith(statusBarColor: Colors.transparent));

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

class RootShell extends StatefulWidget {
  const RootShell({super.key});

  @override
  State<RootShell> createState() => _RootShellState();
}

class _RootShellState extends State<RootShell> {
  int _index = 0;

  static const _tabs = [
    HomeScreen(),
    DocumentsHubScreen(),
    SearchScreen(),
    KabarHubScreen(),
    LainnyaScreen(),
  ];

  @override
  Widget build(BuildContext context) => Scaffold(
        body: IndexedStack(index: _index, children: _tabs),
        bottomNavigationBar: FloatingNavBar(
          index: _index,
          onChanged: (i) => setState(() => _index = i),
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
      );
}
