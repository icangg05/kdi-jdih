# JDIH Kota Kendari

Portal **Jaringan Dokumentasi dan Informasi Hukum (JDIH)** Pemerintah Kota Kendari — akses peraturan daerah, putusan, monografi, dan dokumen hukum secara lengkap, akurat, dan cepat. Dilengkapi REST API untuk integrasi dengan **JDIHN Nasional**.

- **Produksi:** https://jdih.kendarikota.go.id
- **Dokumentasi API:** `/api-docs`

---

## Fitur

**Situs publik**
- Beranda dengan statistik dokumen, berita, pengumuman, monografi, dan video
- Katalog dokumen: **Peraturan & Keputusan**, **Putusan**, **Monografi**, **Artikel/Majalah Hukum**, **Informasi Hukum**, **Pembentukan PUU**
- **Layanan Disabilitas** — pencarian dokumen dengan mode suara (text‑to‑speech) & voice search
- Berita, video, pengumuman, dan profil (sejarah, dasar hukum, visi/misi, struktur organisasi)
- **Pencarian AI** atas dokumen hukum (Google Cloud Language + fulltext MySQL)
- **Survei Kepuasan** pengguna
- **Multi-bahasa**: Indonesia, Inggris, Mandarin, Korea (`id` / `en` / `zh` / `ko`)

**Backend (dashboard admin)**
- Manajemen seluruh jenis dokumen (CRUD), pengumuman, berita, narasi, dan layanan disabilitas
- Statistik & aktivitas terbaru
- Terjemahan otomatis konten dokumen (opsi "Terjemahkan otomatis")

**API (JDIHN)**
- Endpoint publik ber-API Key untuk sinkronisasi ke JDIHN Nasional (`/api/jdih/*`)

---

## Tech Stack

| Komponen        | Teknologi                          |
|-----------------|------------------------------------|
| Framework       | Laravel 12 (PHP ^8.2)              |
| Frontend dinamis| Livewire 3                        |
| Styling         | Tailwind CSS v4 + plugin Typography |
| Build tool      | Vite 6                            |
| Database        | MySQL 8 (utf8mb4)                 |
| Lainnya         | google/cloud-language (AI search), stichoza/google-translate-php (auto-translate), simple-qrcode, vinkla/hashids, yoeunes/toastr |
| Container       | Docker Compose (app, MySQL, Node/Vite, phpMyAdmin) |

---

## Kebutuhan

- PHP ^8.2, Composer
- Node.js 20+ & npm
- MySQL 8
- (Opsional) Docker & Docker Compose

---

## Instalasi

### A. Dengan Docker (disarankan)

```bash
git clone <repo-url> jdihkendarikota
cd jdihkendarikota
cp .env.example .env

# Jalankan seluruh service (app, mysql, vite, phpmyadmin)
docker compose up -d

# Setup aplikasi di dalam container app
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Vite dev server berjalan di port `5173`, phpMyAdmin di `8091`.

### B. Tanpa Docker (lokal)

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# sesuaikan kredensial DB di .env, lalu:
php artisan migrate --seed

npm run dev      # atau: npm run build (produksi)
php artisan serve
```

---

## Konfigurasi `.env`

```env
APP_NAME="JDIH Kota Kendari"
APP_URL=http://localhost:6902

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jdihkendarikota
DB_USERNAME=jdihkendari
DB_PASSWORD=

# Pencarian AI (opsional) — kredensial Google Cloud Language
GOOGLE_APPLICATION_CREDENTIALS=
```

---

## Multi-bahasa

Situs publik mendukung `id`, `en`, `zh`, `ko`. Dua sistem terjemahan:

1. **Label UI statis** — `lang/{id,en,zh,ko}.json` di-key oleh string sumber Bahasa Indonesia, dipakai via helper `__()` di blade. Fallback otomatis ke Bahasa Indonesia bila key belum ada.
2. **Konten dinamis (DB)** — kolom sidecar `translations` (JSON) pada tabel dokumen/berita/pengumuman, dirender via helper global `tt($row, 'field')`. Diisi lewat checkbox "Terjemahkan otomatis" di admin (`stichoza/google-translate-php`, tanpa API key).

Locale ditentukan lewat prefix rute `/{locale}` + middleware `SetLocale` (fallback ke session).

---

## Rute Penting

| Rute                          | Keterangan                        |
|-------------------------------|-----------------------------------|
| `/{locale}`                   | Beranda (id/en/zh/ko)            |
| `/{locale}/pembentukan-puu`   | Dokumen Pembentukan PUU          |
| `/{locale}/layanan-disabilitas` | Layanan disabilitas            |
| `/survei-kepuasan`            | Form survei kepuasan             |
| `/api-docs`                   | Dokumentasi REST API             |
| `/dashboard`                  | Backend admin (auth)             |
| `/api/jdih/*`                 | REST API JDIHN (API Key)         |

---

## REST API (JDIHN)

Base URL: `https://jdih.kendarikota.go.id/api/jdih/` · Format: JSON · Auth: header `X-API-Key`.

Endpoint: `health`, `search`, `documents/{id}`, `documents/{id}/download`, `statistics`, `document-types`, plus format khusus `jdihn-format/*` untuk sinkronisasi. Detail lengkap parameter, contoh request/response, dan error codes ada di halaman **`/api-docs`**.

---

## Perintah Berguna

```bash
php artisan migrate:fresh --seed   # reset & isi ulang database
php artisan view:clear             # bersihkan cache blade
npm run build                      # build aset produksi
./vendor/bin/pint                  # format kode (Laravel Pint)
```

---

© 2026 Pemerintah Kota Kendari — Bagian Hukum Setda Kota Kendari.
