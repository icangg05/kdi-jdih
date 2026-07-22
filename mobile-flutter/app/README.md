# JDIH Kendari — App Flutter

Kode aplikasi (Track B). Rancangan lengkap: folder [`../`](../README.md).

## Menjalankan

```bash
flutter pub get

# Emulator Android (backend lokal di host, port 6992):
flutter run                                      # BASE_URL default http://10.0.2.2:6992

# Device fisik / Chrome — arahkan ke IP server:
flutter run --dart-define=BASE_URL=http://192.168.x.x:6992
flutter run -d chrome --dart-define=BASE_URL=http://localhost:6992

# Produksi:
flutter build apk --release --dart-define=BASE_URL=https://domain-jdih --dart-define=MOBILE_API_KEY=xxx
```

## Struktur

```
lib/
├── main.dart        # entry + RootShell (bottom nav 5 tab) + persist bahasa/tema
├── api.dart         # http client + endpoint /api/v1 + helper Json/Paginated
├── theme.dart       # token brand (oranye #FF891E / biru #015BA5) light+dark
├── widgets.dart     # LoadView, PagedListView, NetImage, StatusBadge, MetaTable, dll.
└── screens/
    ├── home.dart        # beranda (hero, statistik, terbaru, pengumuman, berita, video, pejabat)
    ├── documents.dart   # hub 4 kategori + list (filter) + detail + unduh
    ├── search.dart      # tab pencarian dokumen + Tanya AI
    ├── kabar.dart       # berita, pengumuman, informasi hukum, video
    ├── disability.dart  # layanan disabilitas
    ├── puu.dart         # pembentukan PUU (7 kategori)
    ├── survey.dart      # form survei kepuasan
    └── lainnya.dart     # profil, pengaturan bahasa/tema, tentang
```

Catatan: `android:usesCleartextTraffic="true"` aktif untuk dev HTTP — hapus dari
`android/app/src/main/AndroidManifest.xml` saat rilis produksi (pakai HTTPS).
