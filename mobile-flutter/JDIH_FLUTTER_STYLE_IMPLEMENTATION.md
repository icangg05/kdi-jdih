# Implementasi Branding Flutter — JDIH Kota Kendari

Dokumen ini merangkum konfigurasi package, font, dan color palette untuk aplikasi **JDIH Kota Kendari**.

## 1. Keputusan desain

### Font

- **Font utama Latin:** Plus Jakarta Sans
- **Fallback Mandarin:** Noto Sans SC
- **Fallback Korea:** Noto Sans KR
- **Fallback terakhir:** sans-serif bawaan sistem

Font disimpan secara lokal di dalam aplikasi agar:

- tetap konsisten saat offline;
- tidak bergantung pada Google Fonts saat runtime;
- tidak terjadi perubahan tampilan karena koneksi;
- lebih aman untuk aplikasi layanan publik.

### Color palette

| Token | Hex | Pemakaian utama |
|---|---|---|
| `primary` | `#F97316` | CTA, tombol utama, navigasi aktif |
| `primarySoft` | `#FB923C` | aksen sekunder dan hover/pressed ringan |
| `gold` | `#FBBF24` | penekanan, AI search, ikon atau highlight |
| `goldSoft` | `#FDE68A` | latar chip, badge, empty state |
| `cream` | `#FEFBE8` | background utama light mode |
| `ink` | `#111827` | teks utama dan background dark mode |

> Untuk aksesibilitas, tombol berwarna `#F97316` sebaiknya memakai teks `#111827`, bukan putih. Kombinasi oranye dan putih kurang kuat untuk teks berukuran normal.

---

## 2. Struktur file

```text
assets/
├── fonts/
│   ├── plus_jakarta_sans/
│   │   ├── PlusJakartaSans-Regular.ttf
│   │   ├── PlusJakartaSans-Medium.ttf
│   │   ├── PlusJakartaSans-SemiBold.ttf
│   │   └── PlusJakartaSans-Bold.ttf
│   ├── noto_sans_sc/
│   │   ├── NotoSansSC-Regular.ttf
│   │   └── NotoSansSC-Bold.ttf
│   └── noto_sans_kr/
│       ├── NotoSansKR-Regular.ttf
│       └── NotoSansKR-Bold.ttf
└── images/
    ├── logo.png
    └── placeholders/

lib/
├── app.dart
└── core/
    └── theme/
        ├── app_colors.dart
        └── app_theme.dart
```

Untuk menghemat ukuran aplikasi, font Mandarin dan Korea cukup memakai weight `400` dan `700` terlebih dahulu. Flutter dapat melakukan fallback ketika UI meminta weight lain.

---

## 3. `pubspec.yaml`

Versi package berikut mengikuti rancangan awal project. Sesuaikan dengan `pubspec.lock` dan kompatibilitas Flutter yang digunakan.

```yaml
name: jdih_kendari
description: Aplikasi mobile JDIH Kota Kendari.
publish_to: "none"
version: 1.0.0+1

environment:
  sdk: ">=3.9.0 <4.0.0"
  flutter: ">=3.44.0"

dependencies:
  flutter:
    sdk: flutter

  flutter_localizations:
    sdk: flutter

  # State management
  flutter_riverpod: ^2.6.1

  # REST API Laravel
  dio: ^5.7.0

  # Routing
  go_router: ^14.6.0

  # UI dan konten
  cached_network_image: ^3.4.1
  shimmer: ^3.0.0
  flutter_widget_from_html_core: ^0.16.0

  # URL, PDF eksternal, dan YouTube
  url_launcher: ^6.3.1

  # Menyimpan bahasa dan theme mode
  shared_preferences: ^2.3.3

  # Format tanggal dan angka
  intl: any

  # Opsional: banner ketika perangkat offline
  connectivity_plus: ^6.1.0

dev_dependencies:
  flutter_test:
    sdk: flutter

  flutter_lints: ^5.0.0

  # Opsional untuk ikon dan splash
  flutter_launcher_icons:
  flutter_native_splash:

flutter:
  uses-material-design: true
  generate: true

  assets:
    - assets/images/

  fonts:
    - family: PlusJakartaSans
      fonts:
        - asset: assets/fonts/plus_jakarta_sans/PlusJakartaSans-Regular.ttf
          weight: 400
        - asset: assets/fonts/plus_jakarta_sans/PlusJakartaSans-Medium.ttf
          weight: 500
        - asset: assets/fonts/plus_jakarta_sans/PlusJakartaSans-SemiBold.ttf
          weight: 600
        - asset: assets/fonts/plus_jakarta_sans/PlusJakartaSans-Bold.ttf
          weight: 700

    - family: NotoSansSC
      fonts:
        - asset: assets/fonts/noto_sans_sc/NotoSansSC-Regular.ttf
          weight: 400
        - asset: assets/fonts/noto_sans_sc/NotoSansSC-Bold.ttf
          weight: 700

    - family: NotoSansKR
      fonts:
        - asset: assets/fonts/noto_sans_kr/NotoSansKR-Regular.ttf
          weight: 400
        - asset: assets/fonts/noto_sans_kr/NotoSansKR-Bold.ttf
          weight: 700
```

Setelah menambahkan font:

```bash
flutter clean
flutter pub get
```

Pastikan nama file pada `pubspec.yaml` sama persis dengan nama file di folder `assets/fonts`.

---

## 4. `lib/core/theme/app_colors.dart`

```dart
import 'package:flutter/material.dart';

abstract final class AppColors {
  // Brand palette
  static const Color primary = Color(0xFFF97316);
  static const Color primarySoft = Color(0xFFFB923C);
  static const Color gold = Color(0xFFFBBF24);
  static const Color goldSoft = Color(0xFFFDE68A);
  static const Color cream = Color(0xFFFEFBE8);
  static const Color ink = Color(0xFF111827);

  // Neutral yang dibutuhkan komponen
  static const Color white = Color(0xFFFFFFFF);
  static const Color transparent = Colors.transparent;

  // Semantic status dokumen.
  // Warna status tetap dibedakan agar maknanya tidak bergantung pada brand color.
  static const Color statusActive = Color(0xFF166534);
  static const Color statusActiveBg = Color(0xFFDCFCE7);

  static const Color statusChanged = Color(0xFF854D0E);
  static const Color statusChangedBg = Color(0xFFFEF9C3);

  static const Color statusRevoked = Color(0xFF991B1B);
  static const Color statusRevokedBg = Color(0xFFFEE2E2);
}

abstract final class AppSpacing {
  static const double xs = 4;
  static const double sm = 8;
  static const double md = 12;
  static const double lg = 16;
  static const double xl = 24;
  static const double xxl = 32;
}

abstract final class AppRadius {
  static const double chip = 8;
  static const double button = 10;
  static const double card = 12;
  static const double sheet = 20;
}
```

---

## 5. `lib/core/theme/app_theme.dart`

```dart
import 'package:flutter/material.dart';

import 'app_colors.dart';

abstract final class AppTheme {
  static ThemeData light() => _build(Brightness.light);

  static ThemeData dark() => _build(Brightness.dark);

  static ThemeData _build(Brightness brightness) {
    final bool isDark = brightness == Brightness.dark;

    final Color background =
        isDark ? AppColors.ink : AppColors.cream;

    final Color surface = isDark
        ? Color.alphaBlend(
            Colors.white.withValues(alpha: 0.06),
            AppColors.ink,
          )
        : AppColors.white;

    final Color surfaceMuted = isDark
        ? Color.alphaBlend(
            Colors.white.withValues(alpha: 0.10),
            AppColors.ink,
          )
        : AppColors.goldSoft.withValues(alpha: 0.35);

    final Color line = isDark
        ? Colors.white.withValues(alpha: 0.12)
        : AppColors.primary.withValues(alpha: 0.18);

    final Color textPrimary =
        isDark ? AppColors.cream : AppColors.ink;

    final Color textSecondary = isDark
        ? AppColors.cream.withValues(alpha: 0.72)
        : AppColors.ink.withValues(alpha: 0.68);

    final ColorScheme colorScheme = ColorScheme.fromSeed(
      seedColor: AppColors.primary,
      brightness: brightness,
    ).copyWith(
      primary: AppColors.primary,
      onPrimary: AppColors.ink,
      secondary: AppColors.gold,
      onSecondary: AppColors.ink,
      surface: surface,
      onSurface: textPrimary,
    );

    final TextTheme baseTextTheme = ThemeData(
      useMaterial3: true,
      brightness: brightness,
    ).textTheme;

    final TextTheme textTheme = baseTextTheme
        .copyWith(
          displaySmall: baseTextTheme.displaySmall?.copyWith(
            fontSize: 30,
            fontWeight: FontWeight.w700,
            height: 1.15,
          ),
          headlineMedium: baseTextTheme.headlineMedium?.copyWith(
            fontSize: 24,
            fontWeight: FontWeight.w700,
            height: 1.25,
          ),
          titleLarge: baseTextTheme.titleLarge?.copyWith(
            fontSize: 20,
            fontWeight: FontWeight.w700,
            height: 1.30,
          ),
          titleMedium: baseTextTheme.titleMedium?.copyWith(
            fontSize: 16,
            fontWeight: FontWeight.w600,
            height: 1.40,
          ),
          bodyLarge: baseTextTheme.bodyLarge?.copyWith(
            fontSize: 16,
            fontWeight: FontWeight.w400,
            height: 1.65,
          ),
          bodyMedium: baseTextTheme.bodyMedium?.copyWith(
            fontSize: 14,
            fontWeight: FontWeight.w400,
            height: 1.55,
          ),
          bodySmall: baseTextTheme.bodySmall?.copyWith(
            fontSize: 12,
            fontWeight: FontWeight.w400,
            height: 1.45,
          ),
          labelLarge: baseTextTheme.labelLarge?.copyWith(
            fontSize: 14,
            fontWeight: FontWeight.w600,
          ),
        )
        .apply(
          fontFamily: 'PlusJakartaSans',
          bodyColor: textPrimary,
          displayColor: textPrimary,
        );

    return ThemeData(
      useMaterial3: true,
      brightness: brightness,
      colorScheme: colorScheme,
      scaffoldBackgroundColor: background,

      // Font utama dan urutan fallback.
      fontFamily: 'PlusJakartaSans',
      fontFamilyFallback: const <String>[
        'NotoSansSC',
        'NotoSansKR',
        'sans-serif',
      ],
      textTheme: textTheme,

      appBarTheme: AppBarTheme(
        backgroundColor: surface,
        foregroundColor: textPrimary,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        scrolledUnderElevation: 0,
        centerTitle: false,
        titleTextStyle: textTheme.titleLarge,
        iconTheme: IconThemeData(color: textPrimary),
        shape: Border(
          bottom: BorderSide(color: line),
        ),
      ),

      cardTheme: CardThemeData(
        color: surface,
        surfaceTintColor: Colors.transparent,
        elevation: 0,
        margin: EdgeInsets.zero,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.card),
          side: BorderSide(color: line),
        ),
      ),

      dividerTheme: DividerThemeData(
        color: line,
        thickness: 1,
        space: 1,
      ),

      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: surfaceMuted,
        hintStyle: textTheme.bodyMedium?.copyWith(
          color: textSecondary,
        ),
        contentPadding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.lg,
          vertical: 14,
        ),
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
          borderSide: const BorderSide(
            color: AppColors.primary,
            width: 1.5,
          ),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.card),
          borderSide: const BorderSide(
            color: AppColors.statusRevoked,
          ),
        ),
      ),

      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: AppColors.primary,
          foregroundColor: AppColors.ink,
          disabledBackgroundColor:
              AppColors.primary.withValues(alpha: 0.35),
          disabledForegroundColor:
              AppColors.ink.withValues(alpha: 0.55),
          padding: const EdgeInsets.symmetric(
            horizontal: 20,
            vertical: 14,
          ),
          minimumSize: const Size(48, 48),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.button),
          ),
          textStyle: textTheme.labelLarge,
        ),
      ),

      outlinedButtonTheme: OutlinedButtonThemeData(
        style: OutlinedButton.styleFrom(
          foregroundColor: textPrimary,
          side: BorderSide(color: line),
          padding: const EdgeInsets.symmetric(
            horizontal: 20,
            vertical: 14,
          ),
          minimumSize: const Size(48, 48),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(AppRadius.button),
          ),
          textStyle: textTheme.labelLarge,
        ),
      ),

      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: AppColors.primary,
          minimumSize: const Size(48, 48),
          textStyle: textTheme.labelLarge,
        ),
      ),

      chipTheme: ChipThemeData(
        backgroundColor: surfaceMuted,
        selectedColor:
            AppColors.primary.withValues(alpha: 0.16),
        disabledColor: surfaceMuted.withValues(alpha: 0.45),
        labelStyle: textTheme.bodySmall?.copyWith(
          color: textPrimary,
          fontWeight: FontWeight.w600,
        ),
        secondaryLabelStyle: textTheme.bodySmall?.copyWith(
          color: AppColors.ink,
          fontWeight: FontWeight.w600,
        ),
        side: BorderSide(color: line),
        padding: const EdgeInsets.symmetric(
          horizontal: AppSpacing.sm,
          vertical: AppSpacing.xs,
        ),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.chip),
        ),
      ),

      navigationBarTheme: NavigationBarThemeData(
        height: 72,
        backgroundColor: surface,
        surfaceTintColor: Colors.transparent,
        indicatorColor:
            AppColors.primary.withValues(alpha: 0.16),
        iconTheme: WidgetStateProperty.resolveWith<IconThemeData>(
          (Set<WidgetState> states) {
            final bool selected =
                states.contains(WidgetState.selected);

            return IconThemeData(
              color: selected
                  ? AppColors.primary
                  : textSecondary,
            );
          },
        ),
        labelTextStyle:
            WidgetStateProperty.resolveWith<TextStyle?>(
          (Set<WidgetState> states) {
            final bool selected =
                states.contains(WidgetState.selected);

            return textTheme.bodySmall?.copyWith(
              color: selected
                  ? AppColors.primary
                  : textSecondary,
              fontWeight: selected
                  ? FontWeight.w700
                  : FontWeight.w500,
            );
          },
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

      snackBarTheme: SnackBarThemeData(
        backgroundColor: AppColors.ink,
        contentTextStyle: textTheme.bodyMedium?.copyWith(
          color: AppColors.cream,
        ),
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(AppRadius.card),
        ),
      ),

      progressIndicatorTheme: const ProgressIndicatorThemeData(
        color: AppColors.primary,
      ),

      iconTheme: IconThemeData(
        color: textPrimary,
        size: 24,
      ),

      extensions: <ThemeExtension<dynamic>>[
        JdihThemeColors(
          background: background,
          surface: surface,
          surfaceMuted: surfaceMuted,
          line: line,
          textPrimary: textPrimary,
          textSecondary: textSecondary,
        ),
      ],
    );
  }
}

/// Warna tambahan yang tidak tersedia langsung pada ColorScheme.
///
/// Pemakaian:
/// final colors = Theme.of(context).extension<JdihThemeColors>()!;
class JdihThemeColors
    extends ThemeExtension<JdihThemeColors> {
  const JdihThemeColors({
    required this.background,
    required this.surface,
    required this.surfaceMuted,
    required this.line,
    required this.textPrimary,
    required this.textSecondary,
  });

  final Color background;
  final Color surface;
  final Color surfaceMuted;
  final Color line;
  final Color textPrimary;
  final Color textSecondary;

  @override
  JdihThemeColors copyWith({
    Color? background,
    Color? surface,
    Color? surfaceMuted,
    Color? line,
    Color? textPrimary,
    Color? textSecondary,
  }) {
    return JdihThemeColors(
      background: background ?? this.background,
      surface: surface ?? this.surface,
      surfaceMuted: surfaceMuted ?? this.surfaceMuted,
      line: line ?? this.line,
      textPrimary: textPrimary ?? this.textPrimary,
      textSecondary: textSecondary ?? this.textSecondary,
    );
  }

  @override
  JdihThemeColors lerp(
    covariant JdihThemeColors? other,
    double t,
  ) {
    if (other == null) return this;

    return JdihThemeColors(
      background:
          Color.lerp(background, other.background, t)!,
      surface: Color.lerp(surface, other.surface, t)!,
      surfaceMuted:
          Color.lerp(surfaceMuted, other.surfaceMuted, t)!,
      line: Color.lerp(line, other.line, t)!,
      textPrimary:
          Color.lerp(textPrimary, other.textPrimary, t)!,
      textSecondary:
          Color.lerp(textSecondary, other.textSecondary, t)!,
    );
  }
}
```

---

## 6. Integrasi pada `lib/app.dart`

```dart
import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';

import 'core/theme/app_theme.dart';

class JdihApp extends StatelessWidget {
  const JdihApp({
    required this.routerConfig,
    required this.themeMode,
    required this.locale,
    super.key,
  });

  final RouterConfig<Object> routerConfig;
  final ThemeMode themeMode;
  final Locale locale;

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'JDIH Kota Kendari',
      debugShowCheckedModeBanner: false,

      theme: AppTheme.light(),
      darkTheme: AppTheme.dark(),
      themeMode: themeMode,

      locale: locale,
      supportedLocales: const <Locale>[
        Locale('id'),
        Locale('en'),
        Locale('zh', 'CN'),
        Locale('ko', 'KR'),
      ],
      localizationsDelegates: const <LocalizationsDelegate<dynamic>>[
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],

      routerConfig: routerConfig,
    );
  }
}
```

Untuk query API, gunakan `locale.languageCode`:

```dart
final String lang = locale.languageCode;

// Hasil:
// id-ID   -> id
// en-US   -> en
// zh-CN   -> zh
// ko-KR   -> ko
```

---

## 7. Contoh pemakaian warna pada widget

### Kartu hasil pencarian AI

```dart
class AiSummaryCard extends StatelessWidget {
  const AiSummaryCard({
    required this.text,
    super.key,
  });

  final String text;

  @override
  Widget build(BuildContext context) {
    final JdihThemeColors colors =
        Theme.of(context).extension<JdihThemeColors>()!;

    return Container(
      padding: const EdgeInsets.all(AppSpacing.lg),
      decoration: BoxDecoration(
        color: AppColors.goldSoft.withValues(alpha: 0.42),
        borderRadius: BorderRadius.circular(AppRadius.card),
        border: Border.all(
          color: AppColors.gold.withValues(alpha: 0.55),
        ),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: <Widget>[
          const Icon(
            Icons.auto_awesome,
            color: AppColors.primary,
          ),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Text(
              text,
              style: Theme.of(context)
                  .textTheme
                  .bodyLarge
                  ?.copyWith(color: colors.textPrimary),
            ),
          ),
        ],
      ),
    );
  }
}
```

### Badge jenis dokumen

```dart
Container(
  padding: const EdgeInsets.symmetric(
    horizontal: AppSpacing.sm,
    vertical: AppSpacing.xs,
  ),
  decoration: BoxDecoration(
    color: AppColors.primary.withValues(alpha: 0.10),
    borderRadius: BorderRadius.circular(AppRadius.chip),
    border: Border.all(
      color: AppColors.primary.withValues(alpha: 0.35),
    ),
  ),
  child: Text(
    'PERWAL',
    style: Theme.of(context).textTheme.bodySmall?.copyWith(
      color: AppColors.primary,
      fontWeight: FontWeight.w700,
    ),
  ),
)
```

---

## 8. Aturan pemakaian palette

### Gunakan `primary`

- tombol unduh dokumen;
- tab atau bottom navigation aktif;
- ikon utama;
- indikator progres;
- link penting;
- filter aktif.

### Gunakan `gold`

- penanda pencarian AI;
- angka statistik penting;
- highlight informasi;
- dekorasi yang perlu menarik perhatian.

### Gunakan `goldSoft`

- background chip;
- panel informasi ringan;
- empty state;
- skeleton atau placeholder lembut.

### Gunakan `cream`

- background light mode;
- section dengan nuansa hangat;
- background splash screen.

### Gunakan `ink`

- seluruh teks utama;
- ikon utama;
- background dark mode;
- teks tombol di atas warna oranye atau kuning.

### Hindari

- memakai oranye pada seluruh permukaan layar;
- memakai putih sebagai teks normal di atas `#F97316`;
- memakai `#FDE68A` sebagai warna teks;
- menggunakan warna status hukum hanya berdasarkan warna brand;
- membuat seluruh kartu berwarna oranye atau kuning.

Brand harus terasa energik melalui aksen, bukan melalui penggunaan warna secara berlebihan.

---

## 9. Splash screen opsional

Tambahkan pada bagian paling bawah `pubspec.yaml`:

```yaml
flutter_native_splash:
  color: "#FEFBE8"
  color_dark: "#111827"
  image: assets/images/logo.png
  android_12:
    color: "#FEFBE8"
    color_dark: "#111827"
    image: assets/images/logo.png
```

Generate splash:

```bash
dart run flutter_native_splash:create
```

---

## 10. Checklist implementasi

- [ ] Salin seluruh file font ke `assets/fonts`.
- [ ] Pastikan nama file sama dengan konfigurasi `pubspec.yaml`.
- [ ] Jalankan `flutter clean` dan `flutter pub get`.
- [ ] Buat `app_colors.dart`.
- [ ] Buat `app_theme.dart`.
- [ ] Terapkan light dan dark theme pada `MaterialApp.router`.
- [ ] Simpan pilihan theme mode dengan Riverpod dan `shared_preferences`.
- [ ] Uji font Indonesia, Inggris, Mandarin, dan Korea.
- [ ] Uji text scaling minimal sampai 1.3×.
- [ ] Uji kontras tombol, chip, status, dan link.
- [ ] Gunakan semantic status terpisah untuk Berlaku, Diubah, dan Dicabut.
