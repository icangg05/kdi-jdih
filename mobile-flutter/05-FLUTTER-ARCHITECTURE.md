# 05 — Arsitektur Flutter & Roadmap

Panduan teknis membangun app: struktur, paket, model, layer jaringan, state, routing, contoh kode, dan rencana bertahap untuk vibe coding.

- **Flutter:** 3.44.0 · **Dart:** 3.9+ · **Material 3**, null‑safe.
- **Pola:** feature‑first, dua layer per fitur — `data` (repository + model) dan `presentation` (screen + provider + widget).
- **State:** Riverpod. **Jaringan:** Dio. **Routing:** go_router.

---

## 1. Struktur folder

```
lib/
├── main.dart                     # runApp + ProviderScope + init prefs
├── app.dart                      # MaterialApp.router + theme + locale
│
├── core/
│   ├── config/
│   │   └── app_config.dart       # baseUrl, apiKey, konstanta
│   ├── theme/
│   │   ├── app_colors.dart       # (lihat 03-DESIGN-SYSTEM.md)
│   │   └── app_theme.dart
│   ├── network/
│   │   ├── api_client.dart       # Dio + interceptor
│   │   ├── api_result.dart       # sealed Result<T> (data/error)
│   │   └── api_exception.dart
│   ├── router/
│   │   └── app_router.dart       # go_router
│   ├── localization/
│   │   ├── locale_provider.dart  # locale aktif (persist)
│   │   └── app_localizations...  # .arb (opsional untuk label statis UI)
│   ├── settings/
│   │   └── theme_provider.dart   # themeMode (persist)
│   └── widgets/                  # widget bersama
│       ├── async_view.dart       # bungkus AsyncValue → loading/error/data
│       ├── error_view.dart
│       ├── empty_view.dart
│       ├── skeletons.dart        # shimmer
│       ├── app_network_image.dart
│       ├── html_body.dart        # render HTML
│       ├── status_badge.dart
│       ├── jenis_chip.dart
│       └── section_header.dart
│
├── shared/
│   └── models/
│       ├── paginated.dart        # Paginated<T> + pagination meta
│       └── document.dart         # DocumentListItem, DocumentDetail
│
└── features/
    ├── home/
    │   ├── data/{home_repository.dart, models/home_summary.dart}
    │   └── presentation/{home_screen.dart, home_providers.dart, widgets/…}
    ├── documents/
    │   ├── data/{documents_repository.dart}
    │   └── presentation/{documents_hub_screen.dart, document_list_screen.dart,
    │                     document_detail_screen.dart, documents_providers.dart, widgets/…}
    ├── search/
    │   ├── data/{search_repository.dart, models/ai_result.dart}
    │   └── presentation/{search_screen.dart, search_providers.dart, widgets/…}
    ├── news/ …           (berita)
    ├── announcements/ …  (pengumuman)
    ├── legal_info/ …     (informasi hukum)
    ├── videos/ …
    ├── profile/ …        (profil)
    ├── disability/ …     (layanan disabilitas)
    ├── puu/ …            (pembentukan puu)
    ├── survey/ …
    └── menu/ …           (menu + settings + about)
```

---

## 2. `pubspec.yaml` (paket inti)

Gunakan versi terbaru yang kompatibel dengan Flutter 3.44.
```yaml
name: jdih_kendari
description: Aplikasi mobile JDIH Kota Kendari (frontend publik).
publish_to: "none"
version: 1.0.0+1

environment:
  sdk: ">=3.9.0 <4.0.0"
  flutter: ">=3.44.0"

dependencies:
  flutter:
    sdk: flutter
  # State
  flutter_riverpod: ^2.6.1
  # Jaringan
  dio: ^5.7.0
  # Routing
  go_router: ^14.6.0
  # UI
  google_fonts: ^6.2.1
  cached_network_image: ^3.4.1
  shimmer: ^3.0.0
  flutter_widget_from_html_core: ^0.16.0   # render isi HTML (berita/pengumuman/profil)
  # Video
  youtube_player_iframe: ^5.2.0            # atau url_launcher untuk buka eksternal
  # Util
  url_launcher: ^6.3.1                      # buka PDF/YouTube/sosial
  shared_preferences: ^2.3.3                # simpan bahasa & tema
  intl: ^0.19.0                             # format tanggal per locale
  connectivity_plus: ^6.1.0                 # deteksi offline (opsional)

dev_dependencies:
  flutter_test:
    sdk: flutter
  flutter_lints: ^5.0.0
  build_runner: ^2.4.13         # bila pakai json_serializable/freezed (opsional)

flutter:
  uses-material-design: true
  assets:
    - assets/images/
  # fonts:  # (opsional, bila membundel Source Sans 3 alih-alih google_fonts)
  #   - family: SourceSans3
  #     fonts: [...]
```

> **Opsional (fase lanjut):** `syncfusion_flutter_pdfviewer` (baca PDF in‑app), `share_plus` (berbagi), `hive`/`isar` (bookmark & cache offline), `firebase_messaging` (push).

---

## 3. Konfigurasi & environment

`lib/core/config/app_config.dart`
```dart
class AppConfig {
  // Ganti sesuai lingkungan. Emulator Android → 10.0.2.2 ; device fisik → IP LAN.
  static const String baseUrl = String.fromEnvironment(
    'BASE_URL', defaultValue: 'http://10.0.2.2:6902',
  );
  static const String apiKey = String.fromEnvironment('MOBILE_API_KEY', defaultValue: '');
  static const String apiPrefix = '/api/v1';
  static const Duration timeout = Duration(seconds: 15);
  static const int defaultPerPage = 10;
}
```
Jalankan dengan override: `flutter run --dart-define=BASE_URL=https://jdih... --dart-define=MOBILE_API_KEY=xxx`.

---

## 4. Layer jaringan

`lib/core/network/api_client.dart`
```dart
import 'package:dio/dio.dart';
import '../config/app_config.dart';

class ApiClient {
  final Dio dio;
  ApiClient(String lang)
      : dio = Dio(BaseOptions(
          baseUrl: AppConfig.baseUrl + AppConfig.apiPrefix,
          connectTimeout: AppConfig.timeout,
          receiveTimeout: AppConfig.timeout,
          headers: {
            'Accept': 'application/json',
            if (AppConfig.apiKey.isNotEmpty) 'X-API-Key': AppConfig.apiKey,
          },
        )) {
    // Sisipkan ?lang= di setiap request
    dio.interceptors.add(InterceptorsWrapper(onRequest: (o, h) {
      o.queryParameters = {'lang': lang, ...o.queryParameters};
      h.next(o);
    }));
    // Logging (dev)
    dio.interceptors.add(LogInterceptor(responseBody: false));
  }
}
```

`lib/core/network/api_exception.dart`
```dart
class ApiException implements Exception {
  final String message;
  final int? statusCode;
  const ApiException(this.message, [this.statusCode]);

  factory ApiException.from(Object e) {
    // Petakan DioException → pesan ramah Bahasa Indonesia
    // timeout → "Koneksi lambat / timeout", 404 → dari body 'message', dst.
    return ApiException('Gagal memuat data. Coba lagi.');
  }
  @override
  String toString() => message;
}
```

Provider Dio yang reaktif terhadap bahasa:
```dart
final apiClientProvider = Provider<ApiClient>((ref) {
  final lang = ref.watch(localeProvider).languageCode; // 'id'/'en'/...
  return ApiClient(lang);
});
```

---

## 5. Model (contoh)

`lib/shared/models/paginated.dart`
```dart
class Paginated<T> {
  final List<T> data;
  final int currentPage, lastPage, total;
  final bool hasMore;
  Paginated({required this.data, required this.currentPage,
    required this.lastPage, required this.total, required this.hasMore});

  factory Paginated.fromJson(Map<String, dynamic> json, T Function(Map<String, dynamic>) item) {
    final p = json['pagination'] ?? {};
    return Paginated(
      data: (json['data'] as List).map((e) => item(e as Map<String, dynamic>)).toList(),
      currentPage: p['current_page'] ?? 1,
      lastPage: p['last_page'] ?? 1,
      total: p['total'] ?? 0,
      hasMore: p['has_more'] ?? false,
    );
  }
}
```

`lib/shared/models/document.dart`
```dart
class DocumentListItem {
  final int id;
  final String category, judul;
  final String? jenisPeraturan, singkatanJenis, nomor, tahun, status, bidangHukum, abstrakSingkat, gambarSampulUrl;
  final int hitSee, hitDownload;
  final bool hasFile;
  DocumentListItem({required this.id, required this.category, required this.judul,
    this.jenisPeraturan, this.singkatanJenis, this.nomor, this.tahun, this.status,
    this.bidangHukum, this.abstrakSingkat, this.gambarSampulUrl,
    this.hitSee = 0, this.hitDownload = 0, this.hasFile = false});

  factory DocumentListItem.fromJson(Map<String, dynamic> j) => DocumentListItem(
    id: j['id'], category: j['category'] ?? 'peraturan', judul: j['judul'] ?? '-',
    jenisPeraturan: j['jenis_peraturan'], singkatanJenis: j['singkatan_jenis'],
    nomor: j['nomor_peraturan'], tahun: j['tahun_terbit']?.toString(), status: j['status'],
    bidangHukum: j['bidang_hukum'], abstrakSingkat: j['abstrak_singkat'],
    gambarSampulUrl: j['gambar_sampul_url'],
    hitSee: j['hit_see'] ?? 0, hitDownload: j['hit_download'] ?? 0, hasFile: j['has_file'] ?? false,
  );
}
```
Detail (`DocumentDetail`), `NewsItem`, `AnnouncementItem`, `VideoItem`, `AiResult` mengikuti bentuk JSON di `02-API-CONTRACT.md`. Manual `fromJson` cukup; `json_serializable`/`freezed` opsional bila ingin lebih ringkas.

---

## 6. Repository (contoh)

`lib/features/documents/data/documents_repository.dart`
```dart
class DocumentsRepository {
  final Dio _dio;
  DocumentsRepository(this._dio);

  Future<Paginated<DocumentListItem>> list({
    required String category, String? q, String? jenis, String? tahun,
    String? status, int page = 1, int perPage = AppConfig.defaultPerPage,
  }) async {
    final res = await _dio.get('/documents', queryParameters: {
      'category': category, if (q != null && q.isNotEmpty) 'q': q,
      if (jenis != null) 'jenis': jenis, if (tahun != null) 'tahun': tahun,
      if (status != null) 'status': status, 'page': page, 'per_page': perPage,
    });
    return Paginated.fromJson(res.data, DocumentListItem.fromJson);
  }

  Future<DocumentDetail> detail(int id) async {
    final res = await _dio.get('/documents/$id');
    return DocumentDetail.fromJson(res.data['data']);
  }

  Future<Map<String, List<String>>> filters(String category) async {
    final res = await _dio.get('/document-filters', queryParameters: {'category': category});
    final d = res.data['data'];
    return {
      'jenis': List<String>.from(d['jenis'] ?? []),
      'tahun': List<String>.from((d['tahun'] ?? []).map((e) => e.toString())),
      'status': List<String>.from(d['status'] ?? []),
    };
  }
}

final documentsRepoProvider = Provider(
  (ref) => DocumentsRepository(ref.watch(apiClientProvider).dio));
```

---

## 7. State management (Riverpod)

**Detail (FutureProvider.family):**
```dart
final documentDetailProvider = FutureProvider.family<DocumentDetail, int>((ref, id) {
  return ref.watch(documentsRepoProvider).detail(id);
});
```

**List dengan filter + infinite scroll (Notifier):**
```dart
class DocListState {
  final List<DocumentListItem> items;
  final int page, total;
  final bool hasMore, loading, loadingMore;
  final String category, q, jenis, tahun, status;
  final Object? error;
  // ...copyWith
}

class DocListNotifier extends AutoDisposeFamilyNotifier<DocListState, String> {
  @override
  DocListState build(String category) {
    _load(reset: true);
    return DocListState.initial(category);
  }

  Future<void> _load({bool reset = false}) async { /* fetch page, append/replace */ }
  void setFilter({String? q, String? jenis, String? tahun, String? status}) { /* reset page → _load */ }
  Future<void> loadMore() async { if (state.hasMore && !state.loadingMore) _load(); }
  Future<void> refresh() => _load(reset: true);
}

final docListProvider = NotifierProvider.autoDispose
  .family<DocListNotifier, DocListState, String>(DocListNotifier.new);
```

**Widget pembungkus async** (`core/widgets/async_view.dart`): terima `AsyncValue`, tampilkan skeleton saat loading, `ErrorView` + retry saat error, builder saat data. Dipakai di semua layar detail/agregat agar konsisten.

---

## 8. Routing (go_router)

`lib/core/router/app_router.dart`
```dart
final appRouter = GoRouter(
  initialLocation: '/',
  routes: [
    ShellRoute(
      builder: (c, s, child) => AppShell(child: child), // Scaffold + NavigationBar
      routes: [
        GoRoute(path: '/', builder: (c, s) => const HomeScreen()),
        GoRoute(path: '/documents', builder: (c, s) => const DocumentsHubScreen()),
        GoRoute(path: '/search', builder: (c, s) => const SearchScreen()),
        GoRoute(path: '/kabar', builder: (c, s) => const KabarHubScreen()),
        GoRoute(path: '/menu', builder: (c, s) => const MenuScreen()),
      ],
    ),
    // Full-screen (tanpa bottom nav):
    GoRoute(path: '/documents/:category',
      builder: (c, s) => DocumentListScreen(category: s.pathParameters['category']!)),
    GoRoute(path: '/document/:id',
      builder: (c, s) => DocumentDetailScreen(id: int.parse(s.pathParameters['id']!))),
    GoRoute(path: '/news', builder: (c, s) => const NewsListScreen()),
    GoRoute(path: '/news/:id', builder: (c, s) => NewsDetailScreen(id: int.parse(s.pathParameters['id']!))),
    GoRoute(path: '/announcements', builder: (c, s) => const AnnouncementListScreen()),
    GoRoute(path: '/announcements/:id', builder: (c, s) => AnnouncementDetailScreen(id: int.parse(s.pathParameters['id']!))),
    GoRoute(path: '/legal-info', builder: (c, s) => const LegalInfoScreen()),
    GoRoute(path: '/legal-info/detail/:id', builder: (c, s) => LegalInfoDetailScreen(id: int.parse(s.pathParameters['id']!))),
    GoRoute(path: '/videos', builder: (c, s) => const VideoListScreen()),
    GoRoute(path: '/profile/:kategori', builder: (c, s) => ProfileScreen(kategori: s.pathParameters['kategori']!)),
    GoRoute(path: '/disability', builder: (c, s) => const DisabilityListScreen()),
    GoRoute(path: '/disability/:id', builder: (c, s) => DisabilityDetailScreen(id: int.parse(s.pathParameters['id']!))),
    GoRoute(path: '/puu', builder: (c, s) => const PuuListScreen()),
    GoRoute(path: '/puu/:id', builder: (c, s) => PuuDetailScreen(id: int.parse(s.pathParameters['id']!))),
    GoRoute(path: '/survey', builder: (c, s) => const SurveyScreen()),
  ],
);
```

---

## 9. Bahasa & tema (persist)

- **Locale:** `localeProvider` (StateNotifier<Locale>) baca/tulis `shared_preferences` key `locale`. Saat berubah → `apiClientProvider` rebuild (kirim `?lang=` baru) → invalidasi provider data agar refetch. Set `MaterialApp.locale`.
- **Tema:** `themeProvider` (StateNotifier<ThemeMode>) persist key `theme` (system/light/dark). Set `MaterialApp.themeMode`.
- **Label UI statis** (mis. "Cari", "Lihat semua"): pakai `.arb`/`flutter gen-l10n` **atau** map sederhana per locale. Konten dinamis (dokumen/berita) sudah diterjemahkan server via `?lang=`.

---

## 10. Util penting

- **Format tanggal:** `intl` — `DateFormat('d MMMM yyyy', localeTag).format(DateTime.parse(tanggal))` → "21 Juli 2026". Inisialisasi `initializeDateFormatting`.
- **Buka PDF/URL:** `url_launcher` `launchUrl(uri, mode: LaunchMode.externalApplication)`. Fase lanjut: unduh dulu lalu buka `syncfusion_flutter_pdfviewer`.
- **HTML:** `HtmlWidget(item.isi)` dari `flutter_widget_from_html_core`; beri `textStyle` body 16/1.6, warna tautan `accent`.
- **Gambar:** `CachedNetworkImage` dengan `placeholder` shimmer + `errorWidget` ikon; selalu tangani `null` url → placeholder.
- **YouTube:** `youtube_player_iframe` (in‑app) memakai `youtube_id`, atau `launchUrl(watch_url)` (eksternal).

---

## 11. Penanganan error & offline
- Semua repository lempar `ApiException` (pesan Indonesia). Provider mengekspos `AsyncValue.error`.
- `async_view.dart` menampilkan `ErrorView(onRetry: () => ref.invalidate(provider))`.
- `connectivity_plus` → banner "Tidak ada koneksi" saat offline; daftar tetap tampilkan cache terakhir bila ada.

---

## 12. Testing (ringkas)
- **Unit:** parsing `fromJson` tiap model (pakai contoh JSON dari `02`).
- **Repository:** mock Dio (`DioAdapter`/`http_mock_adapter`) → verifikasi query params & mapping.
- **Widget:** `DocumentCard`, `StatusBadge`, `AsyncView` (loading/error/data), form Survei (validasi).
- **Golden (opsional):** kartu dokumen di light & dark.

---

## 13. Build & rilis
- **Android:** `flutter build apk --release` / `appbundle`. `minSdkVersion 24`. `INTERNET` permission (default). Untuk dev HTTP (bukan HTTPS) tambahkan `android:usesCleartextTraffic="true"` **hanya untuk build dev**; produksi wajib HTTPS.
- **iOS:** `flutter build ipa`. iOS 13+. ATS: produksi HTTPS; dev boleh exception domain lokal.
- **Ikon & splash:** `flutter_launcher_icons` + `flutter_native_splash` (logo JDIH, latar brand).
- **Env:** rilis pakai `--dart-define BASE_URL=<https prod>` (+ `MOBILE_API_KEY` bila diaktifkan).

---

## 14. Roadmap bertahap (untuk vibe coding)

**Fase 0 — Fondasi (½–1 hari)**
- [ ] `flutter create`, set pubspec, folder `core/`.
- [ ] Theme (03), `AppConfig`, `ApiClient`, `async_view`, `error_view`, `skeletons`, `app_network_image`, `html_body`, `status_badge`.
- [ ] `main.dart` + `app.dart` + `app_router` + `AppShell` (bottom nav 5 tab kosong).
- [ ] Provider locale & tema (persist).

**Fase 1 — Beranda + Dokumen (inti, P0)**
- [ ] Backend Track A siap & teruji (`02` §4.4).
- [ ] `home` repo/provider/screen + semua kartu.
- [ ] Dokumen hub → list (filter + infinite scroll) → detail + unduh.
- [ ] Cari: tab Dokumen + tab **Tanya AI** (meter akurasi).

**Fase 2 — Kabar (P0/P1)**
- [ ] Berita list+detail (HTML). Pengumuman list+detail+lampiran.
- [ ] Informasi Hukum (types → list → detail). Video (thumbnail → buka).

**Fase 3 — Menu & lainnya (P1)**
- [ ] Profil (5). Disabilitas list+detail. PUU list+detail. Survei (form + kirim).
- [ ] Pengaturan bahasa & tema, halaman Tentang (dari `/meta`).

**Fase 4 — Pemolesan**
- [ ] Empty/error state semua layar, animasi `animate-rise`, aksesibilitas & text scaling.
- [ ] Ikon & splash, uji Android/iOS, build rilis.

**Fase 5 — Nice‑to‑have (opsional)**
- [ ] Bookmark lokal, share deep link, PDF in‑app, push notification.

---

## 15. Definition of Done (MVP)
- Semua fitur P0 jalan end‑to‑end dengan data nyata dari `/api/v1`.
- Setiap layar punya state loading/empty/error + retry + pull‑to‑refresh.
- Dark mode & multi‑bahasa berfungsi; tanggal terformat per locale.
- Tidak ada URL storage yang di‑hard‑code di app (semua media dari URL absolut API).
- Crash‑free di alur utama; lulus uji di Android 7+ dan iOS 13+.
