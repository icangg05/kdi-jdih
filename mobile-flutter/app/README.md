# JDIH Kendari — App Flutter

Kode aplikasi (Track B). Rancangan lengkap: folder [`../`](../README.md).

## Menjalankan (`flutter run`)

```bash
flutter pub get
flutter devices                 # lihat ID emulator/HP yang terhubung

# Emulator Android + backend lokal di komputer ini (port 6992)
flutter run                     # BASE_URL default debug: http://10.0.2.2:6992

# HP fisik (USB/Wi-Fi debugging): pakai IP komputer di jaringan yang sama
flutter run -d <id-device> --dart-define=BASE_URL=http://192.168.x.x:6992

# Pakai data server produksi
flutter run --dart-define=BASE_URL=https://jdih.kendarikota.go.id

# Mode release di HP (performa asli, otomatis ke server produksi)
flutter run --release
```

Saat `flutter run` berjalan: `r` = hot reload, `R` = hot restart, `q` = keluar.

`BASE_URL` bawaan: build debug memakai `http://10.0.2.2:6992` (alamat komputer
dilihat dari emulator), build release memakai `https://jdih.kendarikota.go.id`.
HTTP biasa (tanpa HTTPS) hanya diizinkan di build debug.

## Build release

Penandatanganan memakai `android/key.properties` + `android/app/upload-keystore.jks`
(tidak ikut git, minta ke pengelola). Tanpa kedua file itu build tetap jadi tapi
ditandatangani kunci debug, sehingga tidak bisa dipasang sebagai update app di
Play Store. Naikkan `version` di `pubspec.yaml` (angka setelah `+`) sebelum rilis.

```bash
# Versi full: satu APK untuk semua jenis HP (armeabi-v7a, arm64-v8a, x86_64)
flutter build apk --release
# hasil: build/app/outputs/flutter-apk/app-release.apk

# Versi v8a: hanya HP 64-bit (arm64-v8a, hampir semua HP Android sekarang), ukuran jauh lebih kecil
flutter build apk --release --target-platform android-arm64
# hasil: build/app/outputs/flutter-apk/app-release.apk (nama sama, menimpa versi full;
#        ganti nama dulu bila keduanya perlu disimpan, mis. jdih-kendari-arm64-v8a.apk)

# Untuk Play Store: App Bundle (Play memecah per jenis HP secara otomatis)
flutter build appbundle --release
# hasil: build/app/outputs/bundle/release/app-release.aab
```

Untuk versi v8a sengaja dipakai `--target-platform`, bukan `--split-per-abi`:
`--split-per-abi` mengubah versionCode (mis. `5` jadi `2005`), sehingga APK full
dengan versionCode asli tidak bisa lagi dipasang di atasnya.

## Struktur

```
lib/
├── main.dart        # entry + RootShell (bottom nav 5 tab) + persist bahasa/tema
├── api.dart         # http client + endpoint /api/v1 + helper Json/Paginated
├── theme.dart       # token brand, font Source Sans 3 (assets/fonts), radius 4dp
├── widgets.dart     # LoadView, PagedListView, NetImage, StatusBadge, MetaTable, dll.
└── screens/
    ├── home.dart        # beranda (hero, statistik, terbaru, pengumuman, berita, video, pejabat)
    ├── documents.dart   # hub 4 kategori + list (filter) + detail + unduh
    ├── search.dart      # pencarian teks + Tanya AI (percakapan + lampiran dokumen)
    ├── kabar.dart       # berita, pengumuman, informasi hukum, video
    ├── disability.dart  # layanan disabilitas
    ├── puu.dart         # pembentukan PUU (7 kategori)
    ├── survey.dart      # form survei kepuasan
    └── lainnya.dart     # profil, pengaturan bahasa/tema, tentang
```
