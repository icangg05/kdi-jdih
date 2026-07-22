# JDIH Kota Kendari — Aplikasi Mobile (Flutter)

Rancangan lengkap (PRD + Design + Arsitektur) untuk membangun **aplikasi mobile Android/iOS** yang mereplikasi **halaman depan (frontend publik)** website JDIH Kota Kendari.

> **Ruang lingkup:** Hanya sisi publik/pembaca. Semua manajemen konten (admin/backend/dashboard) **tetap di website**. Aplikasi mobile bersifat **read‑only** (kecuali form Survei Kepuasan), mengonsumsi data lewat REST API JSON dari Laravel yang sudah ada.

---

## Cara memakai dokumen ini (untuk vibe coding)

Baca berurutan. Tiap file berdiri sendiri dan bisa langsung "disuapkan" ke AI coding assistant sebagai konteks.

| File | Isi | Untuk siapa |
|------|-----|-------------|
| [`01-PRD.md`](01-PRD.md) | Visi, tujuan, ruang lingkup, persona, daftar fitur + prioritas, user flow, requirement non‑fungsional | Product / arah keputusan |
| [`02-API-CONTRACT.md`](02-API-CONTRACT.md) | Kontrak REST API mobile (endpoint + JSON) **dan kode Laravel** yang harus ditambahkan di backend | Backend (Laravel) + Flutter |
| [`03-DESIGN-SYSTEM.md`](03-DESIGN-SYSTEM.md) | Token warna, tipografi, spacing, radius, komponen, dark mode, ikon, motion, aksesibilitas | UI/UX + Flutter |
| [`04-SCREENS.md`](04-SCREENS.md) | Spesifikasi per‑layar: wireframe ASCII, state (loading/empty/error), navigasi | Flutter |
| [`05-FLUTTER-ARCHITECTURE.md`](05-FLUTTER-ARCHITECTURE.md) | Struktur folder, paket (pubspec), model, layer network, state management, contoh kode, roadmap bertahap | Flutter |

**Alur kerja disarankan:** implementasikan **Track A (API Laravel)** dari `02-API-CONTRACT.md` lebih dulu → verifikasi endpoint dengan `curl`/Postman → baru bangun **Track B (Flutter)** mengikuti `05` + `04` + `03`.

---

## Ringkasan teknis (at a glance)

- **Flutter:** 3.44.0 (Dart 3.9+), Material 3, null‑safe.
- **Target:** Android (min SDK 24 / Android 7) + iOS 13+.
- **Arsitektur:** feature‑first, layer `data` (repository + model) ↔ `presentation` (screen + provider + widget).
- **State management:** Riverpod (`flutter_riverpod` / `hooks_riverpod`).
- **Networking:** Dio + interceptor (base URL, API key, logging, retry).
- **Routing:** go_router (deep link ready).
- **Sumber data:** REST JSON `GET {BASE_URL}/api/v1/...` (lihat `02-API-CONTRACT.md`).
- **Bahasa:** id (default), en, zh, ko — mengikuti fitur multi‑bahasa website (parameter `?lang=`).
- **Brand:** primary `#ff891e` (oranye), accent `#015BA5` (biru), font **Source Sans 3**, dark mode penuh.

## Konten yang di‑porting (dari website)

Beranda • Pencarian + **Pencarian AI** • Dokumen Hukum 4 kategori (Peraturan, Monografi, Artikel, Putusan) + detail • Berita • Pengumuman • Informasi Hukum • Video • Profil • Layanan Disabilitas • Pembentukan PUU • Survei Kepuasan.

Skala data saat ini (referensi paginasi/performa): ±1.008 dokumen, 117 berita, 122 pengumuman, 28 video.

## Yang TIDAK termasuk

Login/registrasi user, dashboard admin, CRUD konten, upload dokumen, manajemen master data, integrasi JDIHN — semuanya **tetap di website**.

---

## Prasyarat backend

Aplikasi butuh **satu penambahan** di Laravel: layer **API mobile read‑only** (`routes/api.php` + satu controller). Tidak ada perubahan pada admin/backend. Detail + kode siap tempel ada di [`02-API-CONTRACT.md`](02-API-CONTRACT.md).

Pastikan `php artisan storage:link` sudah dijalankan agar media (`/storage/gambar/...`, `/storage/dokumen/...`) dapat diakses publik.
