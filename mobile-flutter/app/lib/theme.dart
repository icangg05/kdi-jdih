import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

/// Token brand — sumber: JDIH_FLUTTER_STYLE_IMPLEMENTATION.md.
class C {
  // Brand palette
  static const primary = Color(0xFFF97316);
  static const primarySoft = Color(0xFFFB923C);

  /// Oranye untuk TEKS dan ikon kecil di atas permukaan terang.
  /// `primary` hanya 2,9:1 di atas putih — gagal syarat 4,5:1; ini 5,1:1.
  /// `primary` tetap dipakai sebagai isian (tombol, indikator) dengan teks ink.
  static const primaryInk = Color(0xFFC2410C);
  static const gold = Color(0xFFFBBF24);
  static const goldSoft = Color(0xFFFDE68A);
  static const cream = Color(0xFFFEFBE8);
  static const ink = Color(0xFF111827);

  /// Ujung gelap gradient color-block (hero). Bukan token brand.
  static const inkSoft = Color(0xFF1F2937);

  // Status hukum — sengaja di luar brand agar maknanya tidak ambigu.
  static const statusActive = Color(0xFF166534);
  static const statusActiveBg = Color(0xFFDCFCE7);
  static const statusChanged = Color(0xFF854D0E);
  static const statusChangedBg = Color(0xFFFEF9C3);
  static const statusRevoked = Color(0xFF991B1B);
  static const statusRevokedBg = Color(0xFFFEE2E2);

  // Netral turunan — nilai hasil blend palette di atas base light/dark.
  static const lightBg = cream;
  static const lightSurface = Color(0xFFFFFFFF);
  static const lightSubtle = Color(0xFFFEF6D6); // goldSoft 35% di atas putih
  static const lightLine = Color(0xFFFEE6D5); // primary 18% di atas putih
  static const lightInk = ink;
  static const lightInkMuted = Color(0xFF5D626C); // ink 68%

  static const darkBg = ink;
  static const darkSurface = Color(0xFF1F2634); // putih 6% di atas ink
  static const darkSubtle = Color(0xFF292F3D); // putih 10% di atas ink
  static const darkLine = Color(0xFF2D3441); // putih 12% di atas ink
  static const darkInk = cream;
  static const darkInkMuted = Color(0xFFBCBBB2); // cream 72%
}

class AppSpacing {
  static const double xs = 4;
  static const double sm = 8;
  static const double md = 12;
  static const double lg = 16;
  static const double xl = 24;
  static const double xxl = 32;
}

class AppRadius {
  static const double chip = 8;
  static const double button = 10;
  static const double card = 12;
  static const double sheet = 20;
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

  final base = ThemeData(brightness: b).textTheme;
  final text = base
      .copyWith(
        displaySmall: base.displaySmall?.copyWith(
            fontSize: 30, fontWeight: FontWeight.w700, height: 1.15),
        headlineMedium: base.headlineMedium?.copyWith(
            fontSize: 24, fontWeight: FontWeight.w700, height: 1.25),
        titleLarge: base.titleLarge?.copyWith(
            fontSize: 20, fontWeight: FontWeight.w700, height: 1.30),
        titleMedium: base.titleMedium?.copyWith(
            fontSize: 16, fontWeight: FontWeight.w600, height: 1.40),
        bodyLarge: base.bodyLarge?.copyWith(
            fontSize: 16, fontWeight: FontWeight.w400, height: 1.65),
        bodyMedium: base.bodyMedium?.copyWith(
            fontSize: 14, fontWeight: FontWeight.w400, height: 1.55),
        bodySmall: base.bodySmall?.copyWith(
            fontSize: 12, fontWeight: FontWeight.w400, height: 1.45),
        labelLarge: base.labelLarge?.copyWith(
            fontSize: 14, fontWeight: FontWeight.w600),
      )
      .apply(fontFamily: 'Roboto', bodyColor: ink, displayColor: ink);

  return ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(
      seedColor: C.primary,
      brightness: b,
      primary: C.primary,
      onPrimary: C.ink,
      secondary: C.gold,
      onSecondary: C.ink,
      surface: surface,
      onSurface: ink,
    ),
    scaffoldBackgroundColor: dark ? C.darkBg : C.lightBg,
    // Roboto = font sistem Android, jadi tidak perlu dibundel dan tetap
    // tersedia offline. Platform lain jatuh ke font sistemnya masing-masing.
    fontFamily: 'Roboto',
    fontFamilyFallback: const ['Noto Sans SC', 'Noto Sans KR', 'sans-serif'],
    textTheme: text,
    appBarTheme: AppBarTheme(
      backgroundColor: surface,
      foregroundColor: ink,
      elevation: 0,
      scrolledUnderElevation: 0,
      surfaceTintColor: Colors.transparent,
      centerTitle: false,
      titleTextStyle: text.titleLarge?.copyWith(fontWeight: FontWeight.w700),
      systemOverlayStyle: dark
          ? SystemUiOverlayStyle.light
          : SystemUiOverlayStyle.dark,
    ),
    // iOS tidak di-set -> tetap transisi Cupertino bawaan.
    pageTransitionsTheme: const PageTransitionsTheme(builders: {
      TargetPlatform.android: _RisePageTransitions(),
      TargetPlatform.linux: _RisePageTransitions(),
    }),
    cardTheme: CardThemeData(
      color: surface,
      elevation: 0,
      surfaceTintColor: Colors.transparent,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        side: BorderSide(color: line),
      ),
      margin: EdgeInsets.zero,
    ),
    chipTheme: ChipThemeData(
      backgroundColor: subtle,
      selectedColor: C.primary.withValues(alpha: .16),
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.chip)),
      side: BorderSide(color: line),
      labelStyle: text.bodySmall?.copyWith(color: ink, fontWeight: FontWeight.w600),
      secondaryLabelStyle:
          text.bodySmall?.copyWith(color: C.ink, fontWeight: FontWeight.w600),
    ),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: subtle,
      hintStyle: text.bodyMedium?.copyWith(color: muted),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: BorderSide(color: line),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: BorderSide(color: line),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: const BorderSide(color: C.primary, width: 1.5),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: const BorderSide(color: C.statusRevoked),
      ),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        backgroundColor: C.primary,
        // teks gelap di atas oranye: kontras WCAG >= 4.5:1
        foregroundColor: C.ink,
        disabledBackgroundColor: C.primary.withValues(alpha: .35),
        disabledForegroundColor: C.ink.withValues(alpha: .55),
        minimumSize: const Size(48, 48),
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.button)),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        textStyle: text.labelLarge,
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        foregroundColor: ink,
        side: BorderSide(color: line),
        minimumSize: const Size(48, 48),
        shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.button)),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        textStyle: text.labelLarge,
      ),
    ),
    textButtonTheme: TextButtonThemeData(
      style: TextButton.styleFrom(
        // teks: pakai oranye gelap agar lolos 4,5:1
        foregroundColor: C.primaryInk,
        minimumSize: const Size(48, 48),
        textStyle: text.labelLarge,
      ),
    ),
    bottomSheetTheme: BottomSheetThemeData(
      backgroundColor: surface,
      surfaceTintColor: Colors.transparent,
      showDragHandle: true,
      shape: const RoundedRectangleBorder(
        borderRadius:
            BorderRadius.vertical(top: Radius.circular(AppRadius.sheet)),
      ),
    ),
    snackBarTheme: SnackBarThemeData(
      backgroundColor: C.ink,
      contentTextStyle: text.bodyMedium?.copyWith(color: C.cream),
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.card)),
    ),
    progressIndicatorTheme: const ProgressIndicatorThemeData(color: C.primary),
    tabBarTheme: TabBarThemeData(
      labelColor: C.primaryInk,
      unselectedLabelColor: muted,
      indicatorColor: C.primary,
      indicatorSize: TabBarIndicatorSize.tab,
      dividerColor: line,
      labelStyle: text.labelLarge,
      unselectedLabelStyle:
          text.labelLarge?.copyWith(fontWeight: FontWeight.w500),
    ),
    dividerTheme: DividerThemeData(color: line, thickness: 1, space: 1),
    dividerColor: line,
  );
}
