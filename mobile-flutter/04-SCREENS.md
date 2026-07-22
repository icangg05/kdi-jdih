# 04 — Spesifikasi Layar (Screen Specs)

Setiap layar: tujuan, sumber data (endpoint dari `02`), layout (wireframe ASCII), state (loading/empty/error), dan navigasi. Semua layar wajib punya 3 state jaringan + pull‑to‑refresh (untuk daftar & detail).

---

## Peta navigasi

```
BottomNav
├── 0 Beranda
│    ├─ → Detail Dokumen
│    ├─ → Detail Pengumuman / Detail Berita
│    ├─ → List (Dokumen/Berita/Pengumuman/Video) via "Lihat semua"
│    └─ → Cari (dari search bar hero)
├── 1 Dokumen (hub 4 kategori)
│    └─ List Dokumen[kategori] → Detail Dokumen → (Unduh / Peraturan terkait → Detail)
├── 2 Cari
│    ├─ Tab Dokumen  → Detail Dokumen
│    └─ Tab Tanya AI → Detail Dokumen
├── 3 Kabar (hub)
│    ├─ Berita        → Detail Berita
│    ├─ Pengumuman    → Detail Pengumuman
│    ├─ Informasi Hukum (pilih jenis) → List → Detail
│    └─ Video          → buka YouTube
└── 4 Menu
     ├─ Profil (5 kategori) → Detail Profil
     ├─ Layanan Disabilitas → Detail
     ├─ Pembentukan PUU → Detail
     ├─ Survei Kepuasan (form) → Terima kasih
     ├─ Bahasa (id/en/zh/ko)
     ├─ Tema (System/Light/Dark)
     └─ Tentang Aplikasi
```

Routing (go_router path): `/` `/documents/:category` `/document/:id` `/search` `/news` `/news/:id` `/announcements` `/announcements/:id` `/legal-info` `/legal-info/:type` `/legal-info/detail/:id` `/videos` `/profile/:kategori` `/disability` `/disability/:id` `/puu` `/puu/:id` `/survey` `/menu`.

---

## S1 — Beranda `/`  · endpoint `GET /home`
Etalase seluruh konten (agregat 1 request).

```
┌──────────────────────────────────────────┐
│  JDIH Kota Kendari            🔍  🌐       │  AppBar (cari, bahasa)
├──────────────────────────────────────────┤
│  ╔══════════════════════════════════════╗ │
│  ║ Selamat datang di JDIH Kota Kendari  ║ │  HERO (narasi.text)
│  ║ [ Cari peraturan, berita... 🔍 ]     ║ │  → tap = ke tab Cari
│  ║ [ Cari Dokumen ]   [ ✦ Tanya AI ]    ║ │  2 tombol
│  ╚══════════════════════════════════════╝ │
│  ┌───────┬───────┬───────┬───────┐        │  STATISTIK (4 counter)
│  │ 812   │ 90    │ 61    │ 45    │        │  Peraturan/Monografi/
│  │Perat. │Monog. │Artikel│Putus. │        │  Artikel/Putusan → tap ke list
│  └───────┴───────┴───────┴───────┘        │
│  Peraturan Terbaru           Lihat semua →│  section header
│  [DocumentCard] [DocumentCard] ...        │  horizontal / vertical x4
│  Monografi Pilihan                        │  1 highlight card
│  Pengumuman                  Lihat semua →│  x3 MediaCard
│  Berita                      Lihat semua →│  x3 MediaCard
│  Video                       Lihat semua →│  x3 VideoCard (horizontal)
│  Pimpinan Daerah                          │  3 kartu pejabat
├──────────────────────────────────────────┤
│  🏠   ⚖   🔍   📰   ☰                      │  BottomNav
└──────────────────────────────────────────┘
```
- **Loading:** skeleton per section. **Error:** full‑screen error + retry. **Refresh:** pull‑to‑refresh re‑fetch `/home`.
- Tap counter → `/documents/:category`. "Lihat semua" → list terkait.

## S2 — Hub Dokumen (tab 1)  · tanpa endpoint (menu)
Grid/list 4 kategori dengan ikon + jumlah (opsional dari `/home` cache).
```
┌──────────────────────────────────────────┐
│  Dokumen Hukum                            │
│  ┌────────────────┐ ┌────────────────┐    │
│  │ ⚖ Peraturan &  │ │ 📘 Monografi   │    │
│  │   Keputusan    │ │    Hukum       │    │
│  └────────────────┘ └────────────────┘    │
│  ┌────────────────┐ ┌────────────────┐    │
│  │ 📰 Artikel /   │ │ 🔨 Putusan     │    │
│  │   Majalah Hukum│ │                │    │
│  └────────────────┘ └────────────────┘    │
└──────────────────────────────────────────┘
```
Tap kartu → `/documents/:category`.

## S3 — List Dokumen `/documents/:category`  · `GET /documents` + `GET /document-filters`
```
┌──────────────────────────────────────────┐
│  ← Peraturan dan Keputusan            🔍  │  AppBar + toggle search
│  [ Cari judul...                       ]  │  search field (live/submit)
│  [Jenis ▾][Tahun ▾][Status ▾]  Reset      │  filter chips/dropdown (dari /document-filters)
│  1.008 hasil                              │  count
│  ┌──────────────────────────────────────┐ │
│  │ [PERDA]·2023           [● Berlaku]   │ │  DocumentCard
│  │ Judul dokumen ...                    │ │
│  │ No.5 · Retribusi   👁120 ⤓34         │ │
│  └──────────────────────────────────────┘ │
│  ...(infinite scroll, per_page 10)...     │
└──────────────────────────────────────────┘
```
- **Filter:** ubah filter → reset ke page 1, re‑fetch. **Infinite scroll:** muat halaman berikut saat mendekati bawah (`has_more`).
- **Empty:** "Tidak ada dokumen sesuai filter" + tombol Reset. **Loading awal:** skeleton list; **loading page berikut:** spinner kecil di footer.

## S4 — Detail Dokumen `/document/:id`  · `GET /documents/{id}`
```
┌──────────────────────────────────────────┐
│  ← Detail Dokumen              ⤴ share    │
│  [PERDA]  [● Berlaku]                      │  chip jenis + badge status
│  Judul lengkap dokumen (headline)         │
│  No. 5 Tahun 2023 · Kendari               │
│  ┌── Metadata ─────────────────────────┐  │  tabel key–value
│  │ Bentuk        : Peraturan Daerah     │  │
│  │ T. Penetapan  : 01 Mei 2023          │  │
│  │ T. Pengundangan: 10 Mei 2023         │  │
│  │ Bidang Hukum  : Retribusi Daerah     │  │
│  │ Penandatangan : Wali Kota Kendari    │  │
│  │ Sumber/Bahasa : ... / Indonesia      │  │
│  └──────────────────────────────────────┘ │
│  Abstrak                                   │  HTML/teks
│  Subjek: [Retribusi] [Persampahan]        │  chips (jika ada)
│  Pengarang: Bagian Hukum                  │  (monografi/artikel)
│  Lampiran                                  │
│  [ ⤓ Dokumen Utama.pdf ]                   │  tombol unduh per lampiran
│  Peraturan Terkait                        │
│  • Perda No.2/2019 ... →                  │  tap → /document/:id
│  👁 121 dilihat · ⤓ 34 diunduh            │  statistik
└──────────────────────────────────────────┘
```
- Field kosong disembunyikan. Untuk **putusan** tampilkan blok: Lembaga Peradilan, Pemohon, Termohon, Jenis Perkara, Amar/Status. Untuk **monografi** tampilkan ISBN, Deskripsi Fisik, Pengarang, Subjek.
- **Unduh:** panggil `GET /documents/{id}/download` → buka `url` (OS/browser, atau in‑app PDF di fase lanjut).
- **Share (P2):** bagikan judul + deep link.

## S5 — Cari `/search`  · tab Dokumen `GET /documents?q=` · tab AI `POST /ai-search`
```
┌──────────────────────────────────────────┐
│  [ Cari peraturan, berita...          🔍] │
│  ┌ Dokumen ┐ ┌ Tanya AI ┐                 │  TabBar
│  ══════════                                │
│  (Tab Dokumen) daftar DocumentCard hasil  │
│  ...                                       │
│  ──────────────────────────────────────── │
│  (Tab Tanya AI)                            │
│  ╔ ✦ Jawaban AI ═══════════════════════╗  │  panel penjelasan
│  ║ Ditemukan 6 dokumen terkait ...      ║  │
│  ╚══════════════════════════════════════╝  │
│  Hasil (6)                                 │
│  ┌──────────────────────────────────────┐ │
│  │ Judul dokumen              [92%]      │ │  DocumentCard + meter akurasi
│  │ PERDA · 2023 · Berlaku  ▓▓▓▓▓▓▓▓▓░    │ │
│  └──────────────────────────────────────┘ │
└──────────────────────────────────────────┘
```
- Kueri min 3 karakter (khusus AI). Debounce 400ms untuk tab Dokumen (live), tombol/enter untuk AI.
- **Empty AI:** tampilkan `explanation` fallback dari server + saran kata kunci. **Loading AI:** shimmer panel + "Menganalisis...".

## S6 — Hub Kabar (tab 3)
List menu: Berita, Pengumuman, Informasi Hukum, Video (kartu dengan ikon). Opsional tampilkan cuplikan terbaru.

## S7 — List Berita `/news`  · `GET /news`
Grid/list MediaCard (gambar 16:9, judul 2 baris, tanggal). Search opsional (`?q=`). Infinite scroll.

## S8 — Detail Berita `/news/:id`  · `GET /news/{id}`
Header gambar (16:9) → judul → tanggal → isi (HTML). Pull‑to‑refresh. Share (P2).

## S9 — List Pengumuman `/announcements`  · `GET /announcements`
MediaCard + badge `tag`. Infinite scroll.

## S10 — Detail Pengumuman `/announcements/:id`  · `GET /announcements/{id}`
Sama seperti berita + blok **Lampiran**: bila `dokumen_url` ada → tombol `⤓ Unduh Lampiran`.

## S11 — Informasi Hukum `/legal-info`  · `GET /legal-info/types` lalu `GET /legal-info?type=`
```
┌──────────────────────────────────────────┐
│  Informasi Hukum                          │
│  [Propemperda][Propemperkada][Ranperda]…  │  chip pilih jenis (types)
│  ── daftar item jenis terpilih ──         │  judul + tanggal + (⤓ dokumen)
└──────────────────────────────────────────┘
```
Detail `/legal-info/detail/:id` → isi (HTML) + unduh dokumen.

## S12 — Video `/videos`  · `GET /videos`
Grid VideoCard (thumbnail YouTube + play). Tap → buka `embed_url` (in‑app WebView/`youtube_player_iframe`) atau `watch_url` (eksternal). Infinite scroll.

## S13 — Menu (tab 4)
Daftar tautan: Profil ▸, Layanan Disabilitas ▸, Pembentukan PUU ▸, Survei Kepuasan ▸, Bahasa, Tema, Tentang. Header kecil logo + versi app.

## S14 — Profil `/profile/:kategori`  · `GET /profile/{kategori}`
Sub‑menu 5: Sekilas Sejarah, Dasar Hukum, Visi, Misi, Struktur Organisasi. Konten `body` (HTML). STO: bila `body` null tampilkan gambar bagan (image_url) + caption.

## S15 — Layanan Disabilitas `/disability` + `/disability/:id`  · `GET /disability`
List kartu (judul, jenis_dokumen, tahun, status) → detail metadata inklusif + unduh (dokumen_utama/lampiran) + cover. Angkat sebagai fitur aksesibilitas unggulan (ikon `accessible`).

## S16 — Pembentukan PUU `/puu` + `/puu/:id`  · `GET /puu`
Filter kategori (Naskah Akademik, Rancangan, Penelitian, Pengkajian, Pengkajian Konstitusi, Analisis). List → detail (field sesuai kategori) + unduh.

## S17 — Survei Kepuasan `/survey`  · `POST /survey`
```
┌──────────────────────────────────────────┐
│  Survei Kepuasan                          │
│  Nama*            [___________]           │
│  Email            [___________]           │
│  Instansi         [___________]           │
│  Jenis Pengguna*  ( ) Mahasiswa (•) ...   │  radio 5 opsi
│  Kemudahan Akses*        ★★★★★             │  rating 1–5 (5 aspek)
│  Kelengkapan Informasi*  ★★★★☆             │
│  Kecepatan Loading*      ★★★★☆             │
│  Tampilan Antarmuka*     ★★★★★             │
│  Relevansi Pencarian*    ★★★★☆             │
│  Saran Perbaikan  [__________________]    │  textarea
│  Fitur Harapan    [__________________]    │
│  [x] Bersedia dihubungi → Kontak [_____]  │
│  [ Kirim Survei ]                         │
└──────────────────────────────────────────┘
```
Validasi klien mirror server (nama & jenis_pengguna & 5 rating wajib). Sukses → layar/toast "Terima kasih atas partisipasi Anda." Error 422 → tampilkan pesan per field.

## S18 — Pengaturan (di dalam Menu)
- **Bahasa:** id/en/zh/ko → simpan (shared_preferences) → semua request kirim `?lang=` + set locale app.
- **Tema:** System/Light/Dark → simpan.
- **Tentang:** versi app, tautan sosial (`/meta`), kebijakan singkat.

---

## Aturan state global (berlaku semua layar)
| State | Tampilan |
|---|---|
| Loading (awal) | Skeleton shimmer sesuai bentuk konten |
| Loading (page berikut) | Spinner kecil di footer list |
| Empty | Ikon + judul + subjudul (+ reset filter bila relevan) |
| Error jaringan | Ikon + "Gagal memuat. Periksa koneksi." + "Coba lagi" |
| Offline | Banner atas "Tidak ada koneksi" + pakai cache bila ada |
| Sukses aksi | SnackBar singkat |

Semua daftar: **pull‑to‑refresh** + **infinite scroll**. Semua gambar: **cached** + placeholder + fallback.
