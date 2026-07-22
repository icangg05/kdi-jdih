# 03 — Design System (Flutter)

Menerjemahkan identitas visual website (formal‑premium, brand oranye‑biru, Source Sans 3, dark mode penuh) ke Material 3 di Flutter. Tujuan: aplikasi terasa **satu keluarga** dengan website.

---

## 1. Brand & prinsip visual

- **Nuansa:** formal, tepercaya, bersih. Instansi pemerintah → hindari norak; utamakan keterbacaan.
- **Aksen hemat:** oranye `primary` dipakai untuk aksi/penekanan; biru `accent` untuk tautan/informasi. Jangan mewarnai seluruh layar.
- **Rounding "rounded" saja:** sudut membulat **moderat** (8–12dp), bukan pill/penuh. Ikuti konvensi web (kelas `rounded`, bukan `rounded-2xl/full`).
- **Kartu ber‑ring tipis:** border/ring 1dp abu, shadow halus saat ditekan/hover (di web: `ring-1 ring-slate-200 hover:shadow-xl`).
- **Motion halus:** fade + naik sedikit (mirip util `animate-rise` web). Hormati "reduce motion".

---

## 2. Token warna

Nilai diambil dari `resources/css/app.css` (sumber kebenaran brand).

### Brand (light & dark sama — identitas dijaga)
| Token | Hex | Pakai |
|---|---|---|
| `primary` | `#FF891E` | tombol utama, aksen, highlight |
| `primaryHover`/pressed | `#EA8221` | state ditekan |
| `accent` | `#015BA5` | tautan, chip info, ikon sekunder |
| `accentHover` | `#014F8E` | state ditekan accent |

### Light theme (netral)
| Token | Hex |
|---|---|
| `bg` (scaffold) | `#F8FAFC` (slate‑50) |
| `surface` (kartu) | `#FFFFFF` |
| `surfaceSubtle` | `#F1F5F9` (slate‑100) |
| `line` (border/ring) | `#E2E8F0` (slate‑200) |
| `ink900` (teks utama) | `#0B1220` |
| `ink700` | `#334155` |
| `ink500` (sekunder) | `#64748B` |
| `ink400` (muted) | `#94A3B8` |

### Dark theme (dari palet `--dk-*` app.css)
| Token | Hex |
|---|---|
| `bg` | `#0A1020` |
| `surface` | `#111A2C` |
| `surfaceSubtle` | `#16223A` |
| `line` | `#223047` |
| `lineStrong` | `#2C3D59` |
| `ink900` | `#EEF2F8` |
| `ink800` | `#DEE5F0` |
| `ink700` | `#CBD5E6` |
| `ink600` | `#AAB8D0` |
| `ink500` | `#94A3BD` |
| `ink400` | `#7F8DA8` |

### Warna status dokumen (badge)
Petakan `status`/`status_terakhir` → warna semantik (teks + latar lembut):
| Status | Light bg / text | Makna |
|---|---|---|
| Berlaku | `#DCFCE7` / `#166534` (hijau) | aktif |
| Diubah | `#FEF9C3` / `#854D0E` (kuning) | direvisi sebagian |
| Dicabut / Tidak Berlaku | `#FEE2E2` / `#991B1B` (merah) | tidak berlaku |
| default/lainnya | `surfaceSubtle` / `ink500` | netral |

> Di dark mode, pakai versi teduh: latar `color.withOpacity(.15)`, teks versi terang.

---

## 3. Tipografi

**Font:** Source Sans 3 (sama dengan web). Tambahkan via `google_fonts` (`GoogleFonts.sourceSans3TextTheme`) **atau** bundel file font di `assets/fonts` (disarankan untuk konsistensi offline). Heading & body memakai family yang sama (web pun begitu).

Skala (Material 3 TextTheme → peran):
| Peran | Size / Weight | Pakai |
|---|---|---|
| `displaySmall` | 30 / w700 | judul hero beranda |
| `headlineMedium` | 24 / w700 | judul layar |
| `titleLarge` | 20 / w600 | judul section, judul kartu besar |
| `titleMedium` | 16 / w600 | judul item daftar |
| `bodyLarge` | 16 / w400 | isi baca (line‑height 1.6) |
| `bodyMedium` | 14 / w400 | teks umum |
| `labelLarge` | 14 / w600 | teks tombol |
| `bodySmall` | 12 / w400 | metadata, tanggal, caption |

**Aturan baca (HTML content):** body 16dp, line‑height 1.6–1.7, lebar nyaman (padding horizontal 16dp), heading di dalam artikel di‑bold.

---

## 4. Spacing, radius, elevasi

- **Grid spacing (dp):** 4, 8, 12, 16, 20, 24, 32. Padding layar default **16**. Jarak antar section **24**.
- **Radius:** kartu/`Card` & input **12**, chip/badge **8**, tombol **10**, bottom sheet **20** (atas saja). Konstanta `kRadius = 12`.
- **Elevasi:** default **0–1** (andalkan `line` border, bukan shadow tebal). Shadow lembut hanya pada elemen terangkat (FAB, bottom sheet, dialog). Kartu daftar: `elevation 0` + `border: line 1dp`.
- **Touch target:** minimal **48×48dp**.

---

## 5. Komponen inti (spesifikasi)

### 5.1 AppBar
- `surface` bg, teks `ink900`, `elevation 0`, garis bawah tipis `line`. Judul `titleLarge`. Aksi: tombol cari + bahasa.

### 5.2 Bottom Navigation (Material 3 `NavigationBar`)
- 5 destinasi (Beranda, Dokumen, Cari, Kabar, Menu). Indicator `primary.withOpacity(.14)`, ikon terpilih `primary`. Label selalu tampil.

### 5.3 Kartu dokumen (DocumentCard)
```
┌─────────────────────────────────────────┐
│ [PERDA] ·  2023            [● Berlaku]   │  ← chip jenis + badge status
│ Judul dokumen dua baris maksimum di sini │  titleMedium, maxLines 2
│ No. 5 · Bidang: Retribusi Daerah         │  bodySmall ink500
│ 👁 120   ⤓ 34                             │  metadata
└─────────────────────────────────────────┘
```
Card `surface`, radius 12, border `line`, padding 16, tap → detail (ripple).

### 5.4 Kartu berita/pengumuman (MediaCard)
Gambar 16:9 (atau kiri 96dp thumb pada layar lebar) + judul 2 baris + tanggal + tag/badge. Placeholder shimmer saat load; fallback ikon bila gambar null.

### 5.5 Chip filter & badge
- Chip filter (jenis/tahun/status): `FilterChip`, terpilih → latar `primary.withOpacity(.12)`, teks `primary`.
- Badge status: pill kecil radius 8, warna dari §2.
- Chip jenis (PERDA/PERWAL): outline `accent`, teks `accent`.

### 5.6 Search bar
Field radius 12, prefix ikon cari, `surfaceSubtle` bg, hint "Cari peraturan, berita, ...". Di layar Cari ada 2 tab: **Dokumen** (hasil daftar) & **Tanya AI** (jawaban + hasil).

### 5.7 Kartu hasil AI (AiResultCard)
Panel penjelasan AI di atas (ikon ✦/sparkle, latar `accent.withOpacity(.06)`, border `accent.withOpacity(.2)`), lalu daftar dokumen dengan **meter akurasi** (bar 0–100, warna primary) di tiap item.

### 5.8 State kosong / error / loading
- **Loading:** skeleton shimmer (bukan spinner penuh) untuk daftar & kartu.
- **Empty:** ilustrasi ikon + judul + subjudul + (opsional) tombol reset filter.
- **Error:** ikon awan‑putus + pesan ramah + tombol "Coba lagi".

### 5.9 Tombol
- Primary: `FilledButton`, bg `primary`, teks putih, radius 10.
- Secondary: `OutlinedButton`, border `line`, teks `ink900`.
- Text/tautan: teks `accent`.
- Unduh: `FilledButton.icon` ikon ⤓.

### 5.10 Video card
Thumbnail YouTube (16:9) + overlay tombol play bulat `primary` + judul + tanggal. Tap → buka embed/YouTube.

---

## 6. Ikonografi
- Set: **Material Symbols/Icons** (bawaan) untuk konsistensi. Opsional `phosphor_flutter` untuk gaya lebih halus.
- Peta menu → ikon: Beranda `home`, Dokumen `gavel`/`account_balance`, Cari `search`, Kabar `newspaper`, Menu `menu`, Pengumuman `campaign`, Video `play_circle`, Informasi Hukum `description`, Disabilitas `accessible`, PUU `balance`, Profil `info`, Survei `star`, Unduh `download`, Bahasa `translate`, Tema `dark_mode`.

## 7. Motion
- Transisi halaman: `fade + slide up 8dp`, 250–300ms, kurva `easeOutCubic` (setara `animate-rise` web).
- Stagger daftar: delay bertahap 40–60ms per item saat pertama muncul (opsional, matikan bila `MediaQuery.disableAnimations`).
- Ripple standar Material pada semua tap.

## 8. Aksesibilitas
- Kontras minimal WCAG AA (teks `ink700`+ di atas `surface`).
- Dukung text scaling sistem (jangan kunci `textScaleFactor`); uji sampai 1.3×.
- `Semantics`/`tooltip` untuk ikon‑only button.
- Dark mode penuh + hormati `prefers-reduced-motion` (via `MediaQuery.disableAnimations`).
- Target sentuh ≥ 48dp; jarak antar aksi cukup.

---

## 9. Implementasi tema (Dart)

`lib/core/theme/app_colors.dart`
```dart
import 'package:flutter/material.dart';

class AppColors {
  // Brand
  static const primary = Color(0xFFFF891E);
  static const primaryPressed = Color(0xFFEA8221);
  static const accent = Color(0xFF015BA5);
  static const accentPressed = Color(0xFF014F8E);

  // Light
  static const lightBg = Color(0xFFF8FAFC);
  static const lightSurface = Color(0xFFFFFFFF);
  static const lightSubtle = Color(0xFFF1F5F9);
  static const lightLine = Color(0xFFE2E8F0);
  static const lightInk900 = Color(0xFF0B1220);
  static const lightInk700 = Color(0xFF334155);
  static const lightInk500 = Color(0xFF64748B);

  // Dark (dari --dk-* app.css)
  static const darkBg = Color(0xFF0A1020);
  static const darkSurface = Color(0xFF111A2C);
  static const darkSubtle = Color(0xFF16223A);
  static const darkLine = Color(0xFF223047);
  static const darkInk900 = Color(0xFFEEF2F8);
  static const darkInk700 = Color(0xFFCBD5E6);
  static const darkInk500 = Color(0xFF94A3BD);

  // Status
  static const statusOk = Color(0xFF166534);
  static const statusOkBg = Color(0xFFDCFCE7);
  static const statusWarn = Color(0xFF854D0E);
  static const statusWarnBg = Color(0xFFFEF9C3);
  static const statusDanger = Color(0xFF991B1B);
  static const statusDangerBg = Color(0xFFFEE2E2);
}

const double kRadius = 12;
const double kPad = 16;
```

`lib/core/theme/app_theme.dart`
```dart
import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'app_colors.dart';

class AppTheme {
  static ThemeData light() => _base(Brightness.light);
  static ThemeData dark() => _base(Brightness.dark);

  static ThemeData _base(Brightness b) {
    final dark = b == Brightness.dark;
    final scheme = ColorScheme.fromSeed(
      seedColor: AppColors.primary,
      brightness: b,
      primary: AppColors.primary,
      secondary: AppColors.accent,
      surface: dark ? AppColors.darkSurface : AppColors.lightSurface,
    );
    final ink900 = dark ? AppColors.darkInk900 : AppColors.lightInk900;
    final ink500 = dark ? AppColors.darkInk500 : AppColors.lightInk500;
    final line = dark ? AppColors.darkLine : AppColors.lightLine;
    final bg = dark ? AppColors.darkBg : AppColors.lightBg;
    final surface = dark ? AppColors.darkSurface : AppColors.lightSurface;

    final text = GoogleFonts.sourceSans3TextTheme(
      ThemeData(brightness: b).textTheme,
    ).apply(bodyColor: ink900, displayColor: ink900);

    return ThemeData(
      useMaterial3: true,
      colorScheme: scheme,
      scaffoldBackgroundColor: bg,
      textTheme: text,
      appBarTheme: AppBarTheme(
        backgroundColor: surface, foregroundColor: ink900, elevation: 0,
        surfaceTintColor: Colors.transparent, centerTitle: false,
        titleTextStyle: text.titleLarge?.copyWith(fontWeight: FontWeight.w700),
      ),
      cardTheme: CardTheme(
        color: surface, elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(kRadius),
          side: BorderSide(color: line),
        ),
        margin: EdgeInsets.zero,
      ),
      chipTheme: ChipThemeData(
        backgroundColor: dark ? AppColors.darkSubtle : AppColors.lightSubtle,
        selectedColor: AppColors.primary.withOpacity(.14),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
        side: BorderSide(color: line),
      ),
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: dark ? AppColors.darkSubtle : AppColors.lightSubtle,
        hintStyle: TextStyle(color: ink500),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(kRadius),
          borderSide: BorderSide(color: line),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(kRadius),
          borderSide: BorderSide(color: line),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(kRadius),
          borderSide: const BorderSide(color: AppColors.primary, width: 1.5),
        ),
      ),
      filledButtonTheme: FilledButtonThemeData(
        style: FilledButton.styleFrom(
          backgroundColor: AppColors.primary, foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 14),
          textStyle: text.labelLarge,
        ),
      ),
      navigationBarTheme: NavigationBarThemeData(
        backgroundColor: surface,
        indicatorColor: AppColors.primary.withOpacity(.14),
        labelTextStyle: WidgetStatePropertyAll(text.labelMedium),
        surfaceTintColor: Colors.transparent,
      ),
      dividerColor: line,
    );
  }
}
```

`lib/app.dart` (pemakaian + dark mode ikut sistem/override)
```dart
MaterialApp.router(
  title: 'JDIH Kota Kendari',
  theme: AppTheme.light(),
  darkTheme: AppTheme.dark(),
  themeMode: themeMode, // dari provider (system/light/dark)
  locale: locale,       // dari locale provider
  routerConfig: appRouter,
);
```
