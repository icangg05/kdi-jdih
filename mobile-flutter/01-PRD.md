# 01 — PRD (Product Requirements Document)

**Produk:** Aplikasi Mobile JDIH Kota Kendari
**Platform:** Android & iOS (Flutter 3.44.0)
**Versi dokumen:** 1.0 · **Tipe rilis awal:** MVP publik (read‑only)

---

## 1. Latar belakang & visi

JDIH (Jaringan Dokumentasi dan Informasi Hukum) Kota Kendari sudah punya website publik + backend admin. Website frontend menyajikan dokumen hukum, berita, pengumuman, informasi hukum, video, profil, layanan disabilitas, pembentukan PUU, plus fitur pencarian AI.

**Visi aplikasi mobile:** memberi masyarakat, akademisi, dan praktisi hukum akses cepat & nyaman ke seluruh produk hukum Kota Kendari langsung dari genggaman — dengan pencarian pintar, pengalaman baca yang enak di layar kecil, dan kemampuan mengunduh/berbagi dokumen.

**Prinsip produk:**
1. **Baca dulu (read‑first).** Aplikasi ini etalase publik, bukan alat kerja admin.
2. **Cepat & ringan.** Daftar ter‑paginasi, gambar ter‑cache, hemat kuota.
3. **Setia pada brand.** Warna, tipografi, dan nuansa formal‑premium website dipertahankan.
4. **Aksesibel.** Mendukung dark mode, teks skalabel, dan mengangkat "Layanan Disabilitas" sebagai kelas utama.

---

## 2. Tujuan & metrik sukses

| Tujuan | Metrik |
|--------|--------|
| Akses dokumen lebih mudah | ≥ 60% sesi membuka minimal 1 detail dokumen |
| Pencarian efektif | ≥ 40% sesi memakai pencarian; rata‑rata < 3 kueri sampai buka dokumen |
| Retensi | D7 retention ≥ 20% |
| Performa | Cold start < 3 dtk; layar daftar tampil < 1,5 dtk di 4G |
| Stabilitas | Crash‑free sessions ≥ 99,5% |

---

## 3. Persona pengguna

- **Masyarakat umum** — mencari peraturan yang berlaku (mis. retribusi, izin), ingin bahasa sederhana. Fitur andalan: Pencarian AI, Beranda, Pengumuman.
- **Mahasiswa/Akademisi** — butuh dokumen lengkap + metadata + unduh PDF untuk referensi. Fitur andalan: Dokumen (filter tahun/jenis), detail metadata, unduh.
- **Praktisi hukum (advokat/ASN)** — cek status peraturan (berlaku/dicabut), peraturan terkait, putusan. Fitur andalan: detail dokumen, status, peraturan terkait.
- **Penyandang disabilitas** — butuh konten hukum yang inklusif. Fitur andalan: Layanan Disabilitas, dark mode, teks skalabel.

---

## 4. Ruang lingkup

### 4.1 In‑scope (MVP)
Replikasi seluruh **halaman depan publik** website:

1. Beranda (ringkasan seluruh konten)
2. Pencarian dokumen + **Pencarian AI**
3. Dokumen Hukum: 4 kategori — Peraturan & Keputusan, Monografi Hukum, Artikel/Majalah Hukum, Putusan — daftar + filter + detail + unduh
4. Berita — daftar + detail
5. Pengumuman — daftar + detail (+ unduh lampiran)
6. Informasi Hukum — daftar per jenis + detail
7. Video — daftar (YouTube)
8. Profil — Sekilas Sejarah, Dasar Hukum, Visi, Misi, Struktur Organisasi (STO)
9. Layanan Disabilitas — daftar + detail + unduh
10. Pembentukan PUU — daftar per kategori + detail + unduh
11. Survei Kepuasan — form kirim
12. Multi‑bahasa (id/en/zh/ko)
13. Dark mode

### 4.2 Out‑of‑scope (tetap di website)
Login/akun, dashboard admin, CRUD/kelola konten, upload, master data, sirkulasi, integrasi & sinkronisasi JDIHN, generate wilayah, export database.

### 4.3 Nice‑to‑have (fase lanjut, bukan MVP)
Bookmark/favorit offline, riwayat baca, push notification pengumuman baru, share dokumen, buka PDF in‑app, mode offline penuh.

---

## 5. Daftar fitur & prioritas

Prioritas: **P0** = wajib MVP, **P1** = MVP jika sempat, **P2** = fase lanjut.

| # | Fitur | Prioritas | Catatan |
|---|-------|-----------|---------|
| F1 | Beranda ringkasan | P0 | 1 endpoint agregat (`/home`) |
| F2 | Pencarian dokumen (teks + filter) | P0 | server‑side, paginasi |
| F3 | Pencarian AI | P0 | endpoint `/ai-search`, tampilkan penjelasan + skor akurasi |
| F4 | Daftar dokumen per kategori + filter | P0 | jenis, tahun, status, nomor |
| F5 | Detail dokumen + metadata + unduh | P0 | lampiran PDF, peraturan terkait, status |
| F6 | Berita daftar + detail (HTML) | P0 | render HTML |
| F7 | Pengumuman daftar + detail + lampiran | P0 | badge tag, PDF |
| F8 | Informasi Hukum per jenis + detail | P1 | 4 jenis |
| F9 | Video (buka YouTube) | P1 | thumbnail + launch/embed |
| F10 | Profil (5 kategori) | P1 | konten HTML statis dari API |
| F11 | Layanan Disabilitas + detail + unduh | P1 | skema tabel tersendiri |
| F12 | Pembentukan PUU + detail + unduh | P1 | skema tabel tersendiri, per kategori |
| F13 | Survei Kepuasan (form) | P1 | satu‑satunya aksi tulis |
| F14 | Multi‑bahasa (id/en/zh/ko) | P1 | param `?lang=`, `tt()` di server |
| F15 | Dark mode + teks skalabel | P0 | ikut sistem, bisa override |
| F16 | Unduh & buka PDF | P1 | MVP: buka via browser/OS; P2: in‑app viewer |
| F17 | Bookmark/favorit lokal | P2 | shared_preferences/Hive |
| F18 | Push notification | P2 | butuh FCM + endpoint |
| F19 | Share dokumen (deep link) | P2 | share_plus |

---

## 6. User flows utama

**A. Menemukan peraturan yang berlaku (masyarakat umum)**
```
Buka app → Beranda → tap search bar → ketik "retribusi sampah"
  → tab "Tanya AI" → baca penjelasan AI + daftar hasil (skor akurasi)
  → tap dokumen → baca metadata + status "Berlaku" → Unduh PDF
```

**B. Riset akademik (mahasiswa)**
```
Beranda → menu "Dokumen" → kategori "Peraturan"
  → filter Tahun=2023, Jenis=PERWAL → scroll daftar (infinite)
  → tap → detail → lihat abstrak, pengarang, subjek → Unduh
```

**C. Cek pengumuman terbaru**
```
Beranda → section "Pengumuman" → lihat 3 terbaru → "Lihat semua"
  → daftar → tap → baca isi (HTML) + unduh lampiran
```

**D. Isi survei**
```
Menu/Drawer → "Survei Kepuasan" → isi rating 1–5 (5 aspek) + saran → Kirim → halaman terima kasih
```

---

## 7. Struktur navigasi (informasi arsitektur)

**Bottom Navigation (5 tab):**
1. **Beranda** — ringkasan
2. **Dokumen** — hub 4 kategori dokumen hukum
3. **Cari** — pencarian teks + AI (tab di dalamnya)
4. **Kabar** — Berita, Pengumuman, Informasi Hukum, Video (hub)
5. **Menu / Lainnya** — Profil, Layanan Disabilitas, Pembentukan PUU, Survei, Bahasa, Tema, Tentang

> Alternatif: 4 tab + Drawer. Detail keputusan di `04-SCREENS.md`.

---

## 8. Requirement non‑fungsional

- **Performa:** cold start < 3 dtk; daftar tampil < 1,5 dtk (4G); gambar lazy + cache disk.
- **Jaringan:** semua layar punya state loading / empty / error + retry. Timeout 15 dtk. Tangani offline dengan pesan ramah.
- **Kompatibilitas:** Android 7+ (min SDK 24), iOS 13+. Responsif dari 320dp s/d tablet.
- **Aksesibilitas:** kontras WCAG AA, dukung text scaling sistem, semantics label, target sentuh ≥ 48dp.
- **Keamanan:** hanya HTTPS di produksi; API key mobile disimpan sebagai konstanta build (bukan rahasia kritikal karena read‑only publik); tidak menyimpan data pribadi.
- **Lokalage:** angka/tanggal mengikuti locale aktif (mis. "21 Juli 2026").
- **Analitik (opsional):** event `search`, `open_document`, `download`, `ai_query`.

---

## 9. Asumsi & dependensi

- Backend Laravel menambahkan endpoint API mobile read‑only (lihat `02-API-CONTRACT.md`). **Ini prasyarat utama.**
- `storage:link` aktif; media dapat diakses publik via `/storage/...`.
- Video adalah **ID YouTube** (kolom `video.link`) → dibuka via YouTube/embed.
- Konten `isi` (berita/pengumuman/informasi) & `body` profil berupa **HTML** → perlu renderer HTML di Flutter.
- Pencarian AI memakai Gemini di server; jika gagal, server otomatis balas ringkasan fallback (app tidak perlu menangani kunci AI).

## 10. Risiko & mitigasi

| Risiko | Mitigasi |
|--------|----------|
| Endpoint mobile belum ada di backend | Sediakan kode Laravel siap tempel (`02`); implement Track A dulu |
| Konten HTML "berantakan" di mobile | Gunakan renderer HTML + style override; sanitasi tag |
| PDF besar boros kuota | MVP: unduh via OS/browser; tampilkan ukuran bila tersedia |
| Multi‑bahasa tak lengkap di DB | `tt()` fallback ke Indonesia otomatis (sudah ada di server) |
| YouTube diblokir/region | Fallback buka di browser eksternal |
