import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

/// Mode tema (system/light/dark); diubah dari tab Lainnya.
final themeNotifier = ValueNotifier<ThemeMode>(ThemeMode.system);

/// Token brand — sumber: resources/css/app.css website.
class C {
  static const primary = Color(0xFFFF891E);
  static const primaryDeep = Color(0xFFEA8221);
  static const accent = Color(0xFF015BA5);
  static const accentDeep = Color(0xFF013E70);

  static const lightBg = Color(0xFFF8FAFC);
  static const lightSurface = Colors.white;
  static const lightSubtle = Color(0xFFF1F5F9);
  static const lightLine = Color(0xFFE2E8F0);
  static const lightInk = Color(0xFF0B1220);
  static const lightInkMuted = Color(0xFF64748B);

  static const darkBg = Color(0xFF0A1020);
  static const darkSurface = Color(0xFF111A2C);
  static const darkSubtle = Color(0xFF16223A);
  static const darkLine = Color(0xFF223047);
  static const darkInk = Color(0xFFEEF2F8);
  static const darkInkMuted = Color(0xFF94A3BD);
}

/// Transisi halaman "rise": fade + naik sedikit, cermin animate-rise website.
class _RisePageTransitions extends PageTransitionsBuilder {
  const _RisePageTransitions();

  @override
  Widget buildTransitions<T>(
      PageRoute<T> route,
      BuildContext context,
      Animation<double> animation,
      Animation<double> secondaryAnimation,
      Widget child) {
    final curved = CurvedAnimation(parent: animation, curve: Curves.easeOutCubic);
    return FadeTransition(
      opacity: curved,
      child: SlideTransition(
        position: Tween(begin: const Offset(0, .03), end: Offset.zero).animate(curved),
        child: child,
      ),
    );
  }
}

ThemeData appTheme(Brightness b) {
  final dark = b == Brightness.dark;
  final ink = dark ? C.darkInk : C.lightInk;
  final muted = dark ? C.darkInkMuted : C.lightInkMuted;
  final line = dark ? C.darkLine : C.lightLine;
  final surface = dark ? C.darkSurface : C.lightSurface;
  final subtle = dark ? C.darkSubtle : C.lightSubtle;

  final text = GoogleFonts.sourceSans3TextTheme(ThemeData(brightness: b).textTheme)
      .apply(bodyColor: ink, displayColor: ink);

  return ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(
      seedColor: C.primary,
      brightness: b,
      primary: C.primary,
      secondary: C.accent,
      surface: surface,
    ),
    scaffoldBackgroundColor: dark ? C.darkBg : C.lightBg,
    textTheme: text,
    appBarTheme: AppBarTheme(
      backgroundColor: surface,
      foregroundColor: ink,
      elevation: 0,
      surfaceTintColor: Colors.transparent,
      centerTitle: false,
      titleTextStyle: text.titleLarge?.copyWith(fontWeight: FontWeight.w700),
    ),
    // iOS tidak di-set -> tetap transisi Cupertino bawaan.
    pageTransitionsTheme: const PageTransitionsTheme(builders: {
      TargetPlatform.android: _RisePageTransitions(),
      TargetPlatform.linux: _RisePageTransitions(),
    }),
    cardTheme: CardThemeData(
      color: surface,
      elevation: 0,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: BorderSide(color: line),
      ),
      margin: EdgeInsets.zero,
    ),
    chipTheme: ChipThemeData(
      backgroundColor: subtle,
      selectedColor: C.primary.withValues(alpha: .14),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
      side: BorderSide(color: line),
      labelStyle: TextStyle(color: ink),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: subtle,
      hintStyle: TextStyle(color: muted),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: line),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: BorderSide(color: line),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: C.primary, width: 1.5),
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        backgroundColor: C.primary,
        foregroundColor: Colors.white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
      ),
    ),
    navigationBarTheme: NavigationBarThemeData(
      backgroundColor: surface,
      indicatorColor: C.primary.withValues(alpha: .14),
      surfaceTintColor: Colors.transparent,
    ),
    dividerColor: line,
  );
}
