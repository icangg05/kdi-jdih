import 'package:flutter/cupertino.dart' show CupertinoPageTransitionsBuilder;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

/// Token warna — sama dengan web (DESIGN.md, resources/css/app.css) dan
/// mobile-flutter/03-DESIGN-SYSTEM.md: Kendari Amber + Civic Blue di atas
/// netral slate.
///
/// Peran, bukan sekadar swatch:
/// - amber  = aksi utama & posisi saat ini (tombol utama, tab/nav aktif,
///            pilihan, Tanya AI). Langka supaya tetap berarti "di sini".
/// - biru   = tautan, informasi (chip jenis, ikon kategori), fokus & kontrol
///            formulir, data grafik, dan kepala dokumen resmi.
/// - slate  = kanvas, permukaan, garis, teks.
class C {
  /// Kendari Amber, ISIAN saja (tombol, indikator, pilihan) dengan teks
  /// [ink] 7,5:1. Teks putih di atasnya hanya 2,4:1 — dilarang.
  static const primary = Color(0xFFFF891E);

  /// Amber untuk TEKS & ikon kecil di permukaan terang: hue yang sama,
  /// 5,4:1 di putih, 4,7:1 di atas tint indikator aktif.
  static const primaryInk = Color(0xFFA65307);

  /// Civic Blue: aman sebagai teks (6,9:1) maupun isian dengan teks putih.
  static const accent = Color(0xFF015BA5);
  static const accentDeep = Color(0xFF014F8E);

  /// Ujung gelap gradien kepala halaman ([PolaBiru]).
  static const accentNight = Color(0xFF02315C);

  /// Teks/ikon di atas permukaan gelap (slate-900, biru).
  static const onDark = Color(0xFFF8FAFC);

  /// Slate-900: teks utama, blok gelap (ajakan AI, snackbar).
  static const ink = Color(0xFF0F172A);

  // Status hukum — sengaja di luar brand agar maknanya tidak ambigu.
  static const statusActive = Color(0xFF166534);
  static const statusActiveBg = Color(0xFFDCFCE7);
  static const statusChanged = Color(0xFF854D0E);
  static const statusChangedBg = Color(0xFFFEF9C3);
  static const statusRevoked = Color(0xFF991B1B);
  static const statusRevokedBg = Color(0xFFFEE2E2);

  // Netral slate (web: bg-slate-50, ring-slate-200, text-slate-600).
  static const lightBg = Color(0xFFF8FAFC); // slate-50: kanvas
  static const lightSurface = Color(0xFFFFFFFF); // kartu, panel
  static const lightSubtle = Color(0xFFF1F5F9); // slate-100: lapis kedua
  static const lightLine = Color(0xFFE2E8F0); // slate-200: tepi kartu, pemisah

  /// Tepi kontrol kecil (kotak centang, radio): 3,5:1 di putih, syarat
  /// WCAG 1.4.11.
  static const lightLineStrong = Color(0xFF7C8AA0);

  /// Tepi kolom isian & tombol bergaris (slate-300, sama dengan formulir web).
  /// Lebih terang dari [lightLineStrong] atas permintaan pemilik: kolom tetap
  /// dikenali lewat ikon/placeholder/label, dan fokus = biru 2dp.
  static const lightLineField = Color(0xFFCBD5E1);
  static const lightInk = ink;
  static const lightInkMuted = Color(0xFF475569); // slate-600, 7,6:1

  // Tema gelap (03-DESIGN-SYSTEM.md). App masih dikunci terang.
  static const darkBg = Color(0xFF0A1020);
  static const darkSurface = Color(0xFF111A2C);
  static const darkSubtle = Color(0xFF16223A);
  static const darkLine = Color(0xFF223047);
  static const darkInk = Color(0xFFEEF2F8);
  static const darkInkMuted = Color(0xFFAAB8D0);

  /// Versi terang amber & biru untuk teks di tema gelap.
  static const primaryOnDark = Color(0xFFFFA552);
  static const accentOnDark = Color(0xFF7FB6E8);
}

/// Satu keluarga untuk seluruh app, termasuk teks bacaan: Roboto bawaan
/// sistem Android (tidak dibundel). Di iOS jatuh ke font sistem.
const kFont = 'Roboto';

/// Peran tipografi: setiap teks memakai salah satu peran ini, bukan ukuran
/// pilihan per layar. Skala ±1,15 (12 · 13 · 15 · 17 · 20 · 26 · 36); peran
/// yang ukurannya sama dibedakan ketebalan. Batas bawah 12sp, isi 15sp.
/// Warna tidak
/// ditetapkan di sini: teks mewarisi tinta tema, varian redup/tautan memakai
/// copyWith(color: ...). letterSpacing 0 di tiap peran menimpa tracking
/// bawaan M3 (0,25-0,5 px) lewat merge textTheme: web memakai
/// tracking normal.
class T {
  /// Satu angka utama di layar (total koleksi).
  static const display = TextStyle(
    letterSpacing: 0,
    fontSize: 36,
    height: 1.1,
    fontWeight: FontWeight.w700,
    fontFeatures: [FontFeature.tabularFigures()],
  );

  /// Judul besar di kepala biru beranda.
  static const hero = TextStyle(
    letterSpacing: 0,
    fontSize: 26,
    height: 1.2,
    fontWeight: FontWeight.w700,
  );

  /// Judul halaman detail, pasangan [bacaan].
  static const judulDetail = TextStyle(
    letterSpacing: 0,
    fontSize: 22,
    height: 1.3,
    fontWeight: FontWeight.w700,
  );

  /// Judul layar dan seksi.
  static const judul = TextStyle(
    letterSpacing: 0,
    fontSize: 20,
    height: 1.3,
    fontWeight: FontWeight.w700,
  );

  /// Angka data yang menonjol: nomor dokumen, nilai KPI.
  static const angka = TextStyle(
    letterSpacing: 0,
    fontSize: 20,
    height: 1.15,
    fontWeight: FontWeight.w700,
    fontFeatures: [FontFeature.tabularFigures()],
  );

  /// Judul sub-bagian dan kartu besar.
  static const subjudul = TextStyle(
    letterSpacing: 0,
    fontSize: 17,
    height: 1.35,
    fontWeight: FontWeight.w700,
  );

  /// Judul kartu dalam daftar.
  static const judulItem = TextStyle(
    letterSpacing: 0,
    fontSize: 15,
    height: 1.35,
    fontWeight: FontWeight.w700,
  );

  /// Teks yang DIBACA panjang: isi artikel, abstrak, uraian, jawaban AI.
  static const bacaan = TextStyle(
    letterSpacing: 0,
    fontSize: 16.5,
    height: 1.65,
  );

  /// Teks UI biasa.
  static const isi = TextStyle(letterSpacing: 0, fontSize: 15, height: 1.5);

  /// Teks sekunder & metadata: subjudul kartu, ringkasan, catatan.
  static const isiKecil = TextStyle(
    letterSpacing: 0,
    fontSize: 13,
    height: 1.45,
  );

  /// Label tombol & kontrol.
  static const labelBesar = TextStyle(
    letterSpacing: 0,
    fontSize: 15,
    height: 1.25,
    fontWeight: FontWeight.w600,
  );

  /// Label chip, pil, nilai kecil.
  static const label = TextStyle(
    letterSpacing: 0,
    fontSize: 13,
    height: 1.25,
    fontWeight: FontWeight.w600,
  );

  /// Badge, tag, sumbu grafik. Batas bawah ukuran di app ini.
  static const labelKecil = TextStyle(
    letterSpacing: 0,
    fontSize: 12,
    height: 1.25,
    fontWeight: FontWeight.w600,
  );
}

class AppSpacing {
  static const double xs = 4;
  static const double sm = 8;
  static const double md = 12;
  static const double lg = 16;
  static const double xl = 24;
  static const double xxl = 32;
}

/// Satu skala sudut untuk seluruh app: 4dp, cermin kelas `rounded` di web.
/// Nama dipertahankan per peran supaya pemakainya tetap terbaca.
class AppRadius {
  static const double chip = 4;
  static const double button = 4;
  static const double card = 4;
  static const double sheet = 4;
}

/// Transisi halaman ala WhatsApp: halaman baru meluncur dari kanan, halaman
/// asal bergeser sedikit ke kiri, dan halaman bisa ditutup dengan mengusap
/// dari tepi kiri. Gerak dikurangi: langsung tampil.
class _GeserKanan extends CupertinoPageTransitionsBuilder {
  const _GeserKanan();

  @override
  Widget buildTransitions<R>(
    PageRoute<R> route,
    BuildContext context,
    Animation<double> animation,
    Animation<double> secondaryAnimation,
    Widget child,
  ) => MediaQuery.of(context).disableAnimations
      ? child
      : super.buildTransitions(
          route,
          context,
          animation,
          secondaryAnimation,
          child,
        );
}

ThemeData appTheme(Brightness b) {
  final dark = b == Brightness.dark;
  final ink = dark ? C.darkInk : C.lightInk;
  final muted = dark ? C.darkInkMuted : C.lightInkMuted;
  final line = dark ? C.darkLine : C.lightLine;
  final surface = dark ? C.darkSurface : C.lightSurface;
  final subtle = dark ? C.darkSubtle : C.lightSubtle;
  final lineStrong = dark ? C.darkInkMuted : C.lightLineStrong;
  final lineField = dark ? C.darkLine : C.lightLineField;
  final accent = dark ? C.accentOnDark : C.accent;

  // peran Material dipetakan ke peran T: komponen bawaan (ListTile, kolom
  // isian, tab, dialog) berbicara dengan skala yang sama dengan layar
  final base = ThemeData(brightness: b).textTheme;
  final text = base
      .copyWith(
        displaySmall: base.displaySmall?.merge(T.display),
        titleLarge: base.titleLarge?.merge(T.judul),
        titleMedium: base.titleMedium?.merge(T.subjudul),
        titleSmall: base.titleSmall?.merge(T.judulItem),
        // bodyLarge = teks isian & judul ListTile: satu langkah di atas isi
        bodyLarge: base.bodyLarge?.merge(T.isi.copyWith(fontSize: 16)),
        bodyMedium: base.bodyMedium?.merge(T.isi),
        bodySmall: base.bodySmall?.merge(T.isiKecil),
        labelLarge: base.labelLarge?.merge(T.labelBesar),
        labelMedium: base.labelMedium?.merge(T.label),
        labelSmall: base.labelSmall?.merge(T.labelKecil),
      )
      .apply(fontFamily: kFont, bodyColor: ink, displayColor: ink);

  return ThemeData(
    useMaterial3: true,
    // peran Material diisi eksplisit: turunan otomatis seed amber membuat
    // kontrol, wadah tonal, dan garis berwarna kecokelatan di luar brand
    colorScheme: ColorScheme.fromSeed(
      seedColor: C.primary,
      brightness: b,
      primary: C.primary,
      onPrimary: C.ink,
      primaryContainer: C.primary.withValues(alpha: .16),
      onPrimaryContainer: dark ? C.primaryOnDark : C.primaryInk,
      secondary: accent,
      onSecondary: dark ? C.ink : Colors.white,
      secondaryContainer: accent.withValues(alpha: .12),
      onSecondaryContainer: accent,
      tertiary: accent,
      error: C.statusRevoked,
      surface: surface,
      onSurface: ink,
      onSurfaceVariant: muted,
      surfaceContainerHighest: subtle,
      outline: lineStrong,
      outlineVariant: line,
    ),
    scaffoldBackgroundColor: dark ? C.darkBg : C.lightBg,
    // dibundel (pubspec); aksara Han/Hangul jatuh ke font sistem
    fontFamily: kFont,
    fontFamilyFallback: const ['Noto Sans SC', 'Noto Sans KR', 'sans-serif'],
    textTheme: text,
    appBarTheme: AppBarTheme(
      backgroundColor: surface,
      foregroundColor: ink,
      elevation: 0,
      scrolledUnderElevation: 0,
      surfaceTintColor: Colors.transparent,
      centerTitle: false,
      titleTextStyle: text.titleLarge,
      systemOverlayStyle: dark
          ? SystemUiOverlayStyle.light
          : SystemUiOverlayStyle.dark,
    ),
    pageTransitionsTheme: const PageTransitionsTheme(
      builders: {
        TargetPlatform.android: _GeserKanan(),
        TargetPlatform.iOS: _GeserKanan(),
        TargetPlatform.linux: _GeserKanan(),
      },
    ),
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
        borderRadius: BorderRadius.circular(AppRadius.chip),
      ),
      side: BorderSide(color: line),
      labelStyle: text.labelMedium?.copyWith(color: ink),
      secondaryLabelStyle: text.labelMedium?.copyWith(color: C.ink),
    ),
    // kolom isian seperti formulir web: putih, tepi slate-300, fokus biru
    // 2dp. Isian tint di atas putih dulu nyaris tak terlihat (1,08:1)
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: surface,
      hintStyle: text.bodyMedium?.copyWith(color: muted),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: BorderSide(color: lineField),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: BorderSide(color: lineField),
      ),
      disabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: BorderSide(color: line),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        borderSide: BorderSide(color: accent, width: 2),
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
          borderRadius: BorderRadius.circular(AppRadius.button),
        ),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        textStyle: text.labelLarge,
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        foregroundColor: ink,
        side: BorderSide(color: lineField),
        minimumSize: const Size(48, 48),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.button),
        ),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        textStyle: text.labelLarge,
      ),
    ),
    // tombol teks = tautan & aksi sekunder: biru, sama dengan tautan web.
    // Amber disisakan untuk aksi utama dan posisi saat ini
    textButtonTheme: TextButtonThemeData(
      style: TextButton.styleFrom(
        foregroundColor: accent,
        minimumSize: const Size(48, 48),
        textStyle: text.labelLarge,
      ),
    ),
    bottomSheetTheme: BottomSheetThemeData(
      backgroundColor: surface,
      surfaceTintColor: Colors.transparent,
      showDragHandle: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(
          top: Radius.circular(AppRadius.sheet),
        ),
      ),
    ),
    // rail samping (jendela >= 600dp): penanda aktif sama dengan NavBawah
    navigationRailTheme: NavigationRailThemeData(
      backgroundColor: surface,
      minWidth: 88,
      indicatorColor: C.primary.withValues(alpha: .16),
      indicatorShape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.chip),
        side: BorderSide(color: C.primary.withValues(alpha: .55)),
      ),
      selectedIconTheme: const IconThemeData(color: C.primaryInk, size: 22),
      unselectedIconTheme: IconThemeData(color: muted, size: 22),
      selectedLabelTextStyle: T.label.copyWith(
        color: C.primaryInk,
        fontWeight: FontWeight.w700,
      ),
      unselectedLabelTextStyle: T.label.copyWith(
        color: muted,
        fontWeight: FontWeight.w500,
      ),
    ),
    // navigasi bawah datar menempel di tepi: garis atas tipis, label selalu
    // tampil (ikon saja ambigu bagi pengguna awam), penanda aktif bersudut 4dp
    navigationBarTheme: NavigationBarThemeData(
      height: 64,
      backgroundColor: surface,
      surfaceTintColor: Colors.transparent,
      elevation: 0,
      indicatorColor: C.primary.withValues(alpha: .16),
      indicatorShape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.chip),
      ),
      labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
      iconTheme: WidgetStateProperty.resolveWith(
        (s) => IconThemeData(
          size: 24,
          color: s.contains(WidgetState.selected) ? C.primaryInk : muted,
        ),
      ),
      labelTextStyle: WidgetStateProperty.resolveWith(
        (s) => text.labelSmall?.copyWith(
          fontSize: 12.5,
          fontWeight: s.contains(WidgetState.selected)
              ? FontWeight.w700
              : FontWeight.w500,
          color: s.contains(WidgetState.selected) ? ink : muted,
        ),
      ),
    ),
    dialogTheme: DialogThemeData(
      backgroundColor: surface,
      surfaceTintColor: Colors.transparent,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
      ),
    ),
    popupMenuTheme: PopupMenuThemeData(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
      ),
    ),
    iconButtonTheme: IconButtonThemeData(
      style: IconButton.styleFrom(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.button),
        ),
      ),
    ),
    // kontrol formulir terpilih: biru (amber di putih hanya 2,4:1, di bawah
    // syarat 3:1 untuk komponen)
    radioTheme: RadioThemeData(
      fillColor: WidgetStateProperty.resolveWith(
        (s) => s.contains(WidgetState.selected) ? accent : lineStrong,
      ),
    ),
    checkboxTheme: CheckboxThemeData(
      fillColor: WidgetStateProperty.resolveWith(
        (s) => s.contains(WidgetState.selected) ? accent : null,
      ),
      checkColor: const WidgetStatePropertyAll(Colors.white),
      side: BorderSide(color: lineStrong, width: 2),
    ),
    textSelectionTheme: TextSelectionThemeData(
      cursorColor: accent,
      selectionColor: accent.withValues(alpha: .25),
      selectionHandleColor: accent,
    ),
    snackBarTheme: SnackBarThemeData(
      backgroundColor: C.ink,
      contentTextStyle: text.bodyMedium?.copyWith(color: C.onDark),
      // amber di atas slate-900: 7,5:1
      actionTextColor: C.primary,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
      ),
    ),
    progressIndicatorTheme: const ProgressIndicatorThemeData(color: C.primary),
    tabBarTheme: TabBarThemeData(
      labelColor: C.primaryInk,
      unselectedLabelColor: muted,
      indicatorColor: C.primary,
      indicatorSize: TabBarIndicatorSize.tab,
      dividerColor: line,
      labelStyle: text.labelLarge,
      unselectedLabelStyle: text.labelLarge?.copyWith(
        fontWeight: FontWeight.w500,
      ),
    ),
    dividerTheme: DividerThemeData(color: line, thickness: 1, space: 1),
    dividerColor: line,
  );
}
