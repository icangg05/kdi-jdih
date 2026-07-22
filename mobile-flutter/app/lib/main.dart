import 'package:flutter/material.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'api.dart';
import 'screens/documents.dart';
import 'screens/home.dart';
import 'screens/kabar.dart';
import 'screens/lainnya.dart';
import 'screens/search.dart';
import 'theme.dart';

Future<void> main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id');

  final prefs = await SharedPreferences.getInstance();
  langNotifier.value = prefs.getString('lang') ?? 'id';
  themeNotifier.value = ThemeMode.values[prefs.getInt('theme') ?? 0];
  langNotifier.addListener(() => prefs.setString('lang', langNotifier.value));
  themeNotifier.addListener(() => prefs.setInt('theme', themeNotifier.value.index));

  runApp(const JdihApp());
}

class JdihApp extends StatelessWidget {
  const JdihApp({super.key});

  @override
  Widget build(BuildContext context) => ListenableBuilder(
        listenable: Listenable.merge([themeNotifier, langNotifier]),
        builder: (context, _) => MaterialApp(
          title: 'JDIH Kota Kendari',
          debugShowCheckedModeBanner: false,
          theme: appTheme(Brightness.light),
          darkTheme: appTheme(Brightness.dark),
          themeMode: themeNotifier.value,
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
        bottomNavigationBar: NavigationBar(
          selectedIndex: _index,
          onDestinationSelected: (i) => setState(() => _index = i),
          destinations: const [
            NavigationDestination(icon: Icon(Icons.home_outlined), selectedIcon: Icon(Icons.home), label: 'Beranda'),
            NavigationDestination(icon: Icon(Icons.account_balance_outlined), selectedIcon: Icon(Icons.account_balance), label: 'Dokumen'),
            NavigationDestination(icon: Icon(Icons.search), label: 'Cari'),
            NavigationDestination(icon: Icon(Icons.newspaper_outlined), selectedIcon: Icon(Icons.newspaper), label: 'Kabar'),
            NavigationDestination(icon: Icon(Icons.menu), label: 'Lainnya'),
          ],
        ),
      );
}
