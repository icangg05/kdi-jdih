# 02 — Kontrak API Mobile + Implementasi Laravel

Aplikasi mobile mengonsumsi **REST JSON read‑only** dari Laravel. Layar publik website saat ini di‑render server (Livewire, query DB langsung), jadi belum ada JSON yang bisa dipakai app. Dokumen ini menetapkan **kontrak API** dan menyediakan **kode Laravel siap tempel** untuk membuatnya.

- **Base URL (dev):** `http://10.0.2.2:6902` (emulator Android → host) atau IP LAN untuk device fisik.
- **Base URL (prod):** `https://<domain-jdih-kendari>`
- **Prefix:** semua endpoint mobile di bawah `/api/v1`.
- **Format:** JSON UTF‑8. Semua URL media dikembalikan **absolut** (app tak perlu tahu struktur storage).
- **Auth:** header `X-API-Key: <key>` (opsional tapi disarankan untuk gating ringan + rate‑limit). Read‑only publik.
- **Bahasa:** query `?lang=id|en|zh|ko` (default `id`). Server memakai `tt()`/`__()` untuk menerjemahkan.

> **Kenapa tidak pakai `/api/jdih/*` yang sudah ada?** Endpoint itu khusus integrasi JDIHN, hanya meng‑cover dokumen, membungkus data seadanya, dan mewajibkan key `jdih_`. Kita buat namespace `/api/v1` yang bersih, mencakup semua konten frontend, dan bentuk responsnya kita kontrol penuh.

---

## 0. Konvensi umum

### Envelope daftar (paginated)
```json
{
  "data": [ /* array item */ ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 117,
    "last_page": 12,
    "has_more": true
  }
}
```

### Envelope tunggal
```json
{ "data": { /* objek */ } }
```

### Error
```json
{ "message": "Dokumen tidak ditemukan", "errors": null }
```
HTTP status dipakai bermakna: `200` ok, `404` tidak ada, `422` validasi, `429` rate limit, `500` server.

### Tipe & format
- Tanggal: string `YYYY-MM-DD` (format ke tampilan di sisi Flutter mengikuti locale).
- URL media: absolut & siap pakai, atau `null` bila tidak ada.
- Field HTML (`isi`, `abstrak`, `body`): string HTML — dirender dengan HTML widget di app.
- ID: integer mentah (mobile **tidak** memakai Hashids; itu hanya untuk URL web).

### Kategori dokumen (`tipe_dokumen`)
| tipe_dokumen | slug `category` | Label |
|---|---|---|
| 1 | `peraturan` | Peraturan dan Keputusan |
| 2 | `monografi` | Monografi Hukum |
| 3 | `artikel` | Artikel / Majalah Hukum |
| 4 | `putusan` | Putusan |

### Jenis Informasi Hukum (`informasi_hukum.jenis`)
| id | singkatan | nama |
|---|---|---|
| 1 | Propemperda | Program Pembentukan Peraturan Daerah |
| 2 | Propemperkada | Program Pembentukan Peraturan Kepala Daerah |
| 3 | Ranperda | Rancangan Peraturan Daerah |
| 4 | Ranperwali | Rancangan Peraturan Walikota |

### Kategori Profil (`profil/{kategori}`)
`sekilas-sejarah`, `dasar-hukum`, `visi`, `misi`, `sto`.

---

## 1. Daftar endpoint (ringkas)

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/api/v1/meta` | Konfigurasi app: kategori, jenis, sosial media, kontak |
| GET | `/api/v1/home` | Agregat Beranda (narasi, dokumen terbaru, statistik, pengumuman, video, berita, pejabat) |
| GET | `/api/v1/documents` | Daftar dokumen (filter + paginasi) |
| GET | `/api/v1/documents/{id}` | Detail dokumen |
| GET | `/api/v1/documents/{id}/download` | Catat unduhan + kembalikan URL file |
| GET | `/api/v1/document-filters` | Opsi filter (jenis/tahun/status) per kategori |
| POST | `/api/v1/ai-search` | Pencarian AI (penjelasan + hasil berperingkat) |
| GET | `/api/v1/news` | Daftar berita |
| GET | `/api/v1/news/{id}` | Detail berita |
| GET | `/api/v1/announcements` | Daftar pengumuman |
| GET | `/api/v1/announcements/{id}` | Detail pengumuman |
| GET | `/api/v1/legal-info/types` | Daftar jenis informasi hukum |
| GET | `/api/v1/legal-info` | Daftar informasi hukum (per jenis) |
| GET | `/api/v1/legal-info/{id}` | Detail informasi hukum |
| GET | `/api/v1/videos` | Daftar video |
| GET | `/api/v1/profile/{kategori}` | Konten profil |
| GET | `/api/v1/disability` | Daftar layanan disabilitas |
| GET | `/api/v1/disability/{id}` | Detail disabilitas |
| GET | `/api/v1/puu` | Daftar pembentukan PUU |
| GET | `/api/v1/puu/{id}` | Detail pembentukan PUU |
| POST | `/api/v1/survey` | Kirim survei kepuasan |

---

## 2. Spesifikasi respons per endpoint

### 2.1 `GET /api/v1/meta`
Statik‑ish; boleh di‑cache app 24 jam.
```json
{
  "data": {
    "app_name": "JDIH Kota Kendari",
    "document_categories": [
      { "slug": "peraturan", "label": "Peraturan dan Keputusan" },
      { "slug": "monografi", "label": "Monografi Hukum" },
      { "slug": "artikel",  "label": "Artikel / Majalah Hukum" },
      { "slug": "putusan",  "label": "Putusan" }
    ],
    "legal_info_types": [
      { "id": 1, "singkatan": "Propemperda", "nama": "Program Pembentukan Peraturan Daerah" }
    ],
    "profile_categories": ["sekilas-sejarah","dasar-hukum","visi","misi","sto"],
    "languages": ["id","en","zh","ko"],
    "contact": {
      "phone": "0821-3527-3000",
      "email": "pemkot@kendarikota.go.id",
      "address": "Jl. Drs. H. Abd. Silondae No. 8, Kel. Mandonga Kec. Mandonga 93111"
    },
    "social": [
      { "label": "Facebook",  "url": "https://www.facebook.com/share/1armp948ym/" },
      { "label": "Instagram", "url": "https://www.instagram.com/jdihkotakendari2" },
      { "label": "YouTube",   "url": "https://www.youtube.com/@jdihkotakendari" },
      { "label": "TikTok",    "url": "https://www.tiktok.com/@jdihkotakendari" }
    ]
  }
}
```

### 2.2 `GET /api/v1/home?lang=id`
```json
{
  "data": {
    "narasi": { "text": "Selamat datang di JDIH ..." },
    "statistik": { "peraturan": 812, "monografi": 90, "artikel": 61, "putusan": 45 },
    "peraturan_terbaru": [ /* DocumentListItem x4 */ ],
    "monografi_highlight": { /* DocumentListItem + pengarang[], subjek[] */ },
    "pengumuman": [ /* AnnouncementListItem x3 */ ],
    "berita": [ /* NewsListItem x3 */ ],
    "video": [ /* VideoItem x3 */ ],
    "pejabat": [
      { "nama": "dr. Hj. SISKA KARINA IMRAN, SKM", "jabatan": "Wali Kota Kendari", "gambar_url": "https://.../assets/img/1.webp" }
    ]
  }
}
```

### 2.3 `GET /api/v1/documents`
**Query:** `category` (wajib: peraturan|monografi|artikel|putusan), `q`, `jenis`, `tahun`, `status`, `nomor`, `page` (default 1), `per_page` (default 10, max 50), `lang`.

**DocumentListItem:**
```json
{
  "id": 123,
  "category": "peraturan",
  "tipe_dokumen": 1,
  "judul": "Peraturan Daerah tentang Retribusi Persampahan",
  "jenis_peraturan": "PERATURAN DAERAH",
  "singkatan_jenis": "PERDA",
  "nomor_peraturan": "5",
  "tahun_terbit": "2023",
  "tanggal_penetapan": "2023-05-01",
  "status": "Berlaku",
  "bidang_hukum": "Retribusi Daerah",
  "abstrak_singkat": "Peraturan ini mengatur ...",
  "gambar_sampul_url": "https://.../storage/gambar/sampul.jpg",
  "hit_see": 120,
  "hit_download": 34,
  "has_file": true
}
```
Respons memakai envelope daftar (`data` + `pagination`).

### 2.4 `GET /api/v1/documents/{id}?lang=id`
**DocumentDetail** (field yang null dihilangkan/di‑null‑kan sesuai kategori):
```json
{
  "data": {
    "id": 123,
    "category": "peraturan",
    "tipe_dokumen": 1,
    "judul": "...",
    "teu": "Pemerintah Kota Kendari",
    "nomor_peraturan": "5",
    "jenis_peraturan": "PERATURAN DAERAH",
    "singkatan_jenis": "PERDA",
    "bentuk_peraturan": "PERATURAN DAERAH",
    "tempat_terbit": "Kendari",
    "penerbit": "Pemerintah Kota Kendari",
    "tahun_terbit": "2023",
    "tanggal_penetapan": "2023-05-01",
    "tanggal_pengundangan": "2023-05-10",
    "sumber": "...",
    "bahasa": "Indonesia",
    "bidang_hukum": "Retribusi Daerah",
    "penandatanganan": "Wali Kota Kendari",
    "status": "Berlaku",
    "status_terakhir": null,
    "abstrak": "<p>...</p>",
    "gambar_sampul_url": "https://.../storage/gambar/sampul.jpg",

    "isbn": null,
    "deskripsi_fisik": null,
    "lembaga_peradilan": null,
    "pemohon": null,
    "termohon": null,
    "jenis_perkara": null,
    "amar_status": null,

    "subjek": ["Retribusi", "Persampahan"],
    "pengarang": [ { "nama": "Bagian Hukum", "tipe": "Penyusun", "jenis": "Badan/Organisasi" } ],

    "statistik": { "dilihat": 121, "diunduh": 34 },
    "lampiran": [
      { "judul": "Dokumen Utama", "url": "https://.../storage/dokumen/perda-5-2023.pdf", "tipe": "pdf" }
    ],
    "peraturan_terkait": [ { "id": 88, "judul": "Perda No. 2 Tahun 2019 ..." } ],

    "created_at": "2023-05-12T09:00:00Z",
    "updated_at": "2023-06-01T10:00:00Z"
  }
}
```
> Catatan: memanggil endpoint ini **menaikkan `hit_see`** (view counter) — sama seperti web.

### 2.5 `GET /api/v1/documents/{id}/download`
Menaikkan `hit_download`, mengembalikan URL file (app lalu buka/unduh URL tsb).
```json
{ "data": { "id": 123, "judul": "...", "download_count": 35,
  "files": [ { "judul": "Dokumen Utama", "url": "https://.../storage/dokumen/perda-5-2023.pdf" } ] } }
```

### 2.6 `GET /api/v1/document-filters?category=peraturan`
Untuk mengisi dropdown filter.
```json
{ "data": {
  "jenis": ["PERATURAN DAERAH", "PERATURAN WALIKOTA", "KEPUTUSAN WALIKOTA"],
  "tahun": ["2024","2023","2022","2021"],
  "status": ["Berlaku","Dicabut","Diubah","Tidak Berlaku"]
} }
```

### 2.7 `POST /api/v1/ai-search`
**Body:** `{ "query": "retribusi sampah", "lang": "id" }` (1–500 char). Sejajar dengan `AiSearchController` web.

Mode percakapan (opsional): kirim `history` agar pertanyaan susulan nyambung — server tidak menyimpan state.
```json
{ "query": "kalau tarifnya berapa?",
  "history": [{ "role": "user", "text": "retribusi sampah" }, { "role": "ai", "text": "..." }] }
```
Maks 20 item (server memakai 8 terakhir), `role` = `user` | `ai`. Query < 3 huruf tidak memicu pencarian
dokumen — `documents` akan kosong dan AI menjawab dari konteks percakapan.
```json
{
  "query": "retribusi sampah",
  "explanation": "Ditemukan 6 dokumen terkait ... (jawaban AI / fallback).",
  "documents": [
    {
      "id": 123,
      "title": "Perda Retribusi Persampahan",
      "type": "PERATURAN DAERAH",
      "year": "2023",
      "number": "5",
      "status": "Berlaku",
      "description": "Ringkasan singkat ...",
      "category": "peraturan",
      "accuracy": 92
    }
  ],
  "total": 6
}
```
> `category` ditambahkan (dari `tipe_dokumen`) agar app bisa navigasi ke detail. Field `url` web tidak dipakai app.

### 2.8 Berita — `GET /api/v1/news` & `/news/{id}`
**NewsListItem:**
```json
{ "id": 45, "tanggal": "2024-02-26", "judul": "...", "image_url": "https://.../storage/gambar/img.jpg", "ringkasan": "180 char teks polos ..." }
```
**NewsDetail:** `{ "id", "tanggal", "judul", "isi": "<p>HTML</p>", "image_url" }`.

### 2.9 Pengumuman — `GET /api/v1/announcements` & `/announcements/{id}`
**AnnouncementListItem:**
```json
{ "id": 12, "tanggal": "2024-01-10", "judul": "...", "tag": "Putusan", "image_url": "https://.../storage/gambar/x.jpg", "ringkasan": "..." }
```
**AnnouncementDetail:** tambahkan `"isi": "<p>HTML</p>"` dan `"dokumen_url": "https://.../storage/dokumen/x.pdf"` (nullable).

### 2.10 Informasi Hukum
- `GET /api/v1/legal-info/types` → `{ "data": [ {id, singkatan, nama} ] }`
- `GET /api/v1/legal-info?type=1&page=1&q=` → daftar `{ id, tanggal, judul, jenis_singkatan, image_url, dokumen_url }`
- `GET /api/v1/legal-info/{id}` → detail + `isi` (HTML) + `dokumen_url`.

### 2.11 Video — `GET /api/v1/videos?page=1`
**VideoItem:**
```json
{
  "id": 3, "tanggal": "2024-03-01", "judul": "Sosialisasi JDIH",
  "youtube_id": "JB0cGooePOc",
  "thumbnail_url": "https://img.youtube.com/vi/JB0cGooePOc/hqdefault.jpg",
  "watch_url": "https://www.youtube.com/watch?v=JB0cGooePOc",
  "embed_url": "https://www.youtube.com/embed/JB0cGooePOc"
}
```

### 2.12 Profil — `GET /api/v1/profile/{kategori}?lang=id`
```json
{ "data": { "kategori": "visi", "title": "Visi", "body": "<p>HTML konten ...</p>" } }
```
> `sto` (Struktur Organisasi) `body` bisa `null` + sertakan `image_url` bagan bila ada.

### 2.13 Layanan Disabilitas — `GET /api/v1/disability` & `/disability/{id}`
List: `{ id, jenis_dokumen, judul, nomor_dokumen, tahun, status_dokumen, cover_url }`.
Detail: seluruh field relevan + `dokumen_url`, `cover_url`, `lampiran_url`, `abstrak`, `kata_kunci`, `jenis_disabilitas`, `sektor_kebijakan`, dll (lihat skema di §4).

### 2.14 Pembentukan PUU — `GET /api/v1/puu?category=&page=` & `/puu/{id}`
`category` opsional. Slug valid (dari `PembentukanPuuIndex`, dipetakan ke kolom `jenis_dokumen`):
`naskah-akademik`, `naskah-keterangan-penjelasan`, `rancangan-puu`, `penelitian-hukum`, `pengkajian-hukum`, `pengkajian-konstitusi`, `analisis-evaluasi`. Daftar lengkap slug+label juga tersedia di `/meta` (`puu_categories`).
List: `{ id, jenis_dokumen, judul, nomor_dokumen, tahun, status_dokumen, kategori, cover_url }`.
Detail: field sesuai `kategori` + `dokumen_url`, `cover_url`, `lampiran_url`, `abstrak`.

### 2.15 `POST /api/v1/survey`
**Body:**
```json
{
  "nama": "Andi", "email": "a@mail.com", "instansi": "Univ X",
  "jenis_pengguna": "Mahasiswa",
  "kemudahan_akses": 5, "kelengkapan_informasi": 4, "kecepatan_loading": 4,
  "tampilan_antarmuka": 5, "relevansi_pencarian": 4,
  "saran_perbaikan": "...", "fitur_harapan": "...",
  "bersedia_dihubungi": true, "kontak": "0812..."
}
```
Validasi identik `SurveyController@store`: `jenis_pengguna ∈ {Mahasiswa, Akademisi, Praktisi Hukum, Masyarakat Umum, Lainnya}`; lima rating `integer 1..5`. Respons `201 { "data": { "message": "Terima kasih atas partisipasi Anda." } }`.

---

## 3. Aturan pembuatan URL media (server)

| Sumber kolom | Direktori | URL |
|---|---|---|
| `berita.image`, `pengumuman.image`, `informasi_hukum.image`, `document.gambar_sampul` | `gambar/` (`config('app.img_directory')`) | `asset('storage/gambar/'.$file)` bila `Storage::exists('gambar/'.$file)`, else `null` |
| `pengumuman.dokumen`, `informasi_hukum.dokumen`, `data_lampiran.dokumen_lampiran` | `dokumen/` (`config('app.doc_directory')`) | `asset('storage/dokumen/'.$file)` |
| `disabilitas.*`, `pembentukan_puu.*` (dokumen_utama/cover/lampiran) | disimpan lewat `Storage` | `Storage::url($value)` |
| `video.link` | — (ID YouTube) | thumbnail `https://img.youtube.com/vi/{id}/hqdefault.jpg`, watch `https://www.youtube.com/watch?v={id}` |
| `pejabat gambar` (`assets/img/1.webp`) | public assets | `asset($path)` |

> Gunakan helper `checkFilePath()` (sudah ada di `app/Helpers/helpers.php`) untuk memastikan file ada sebelum membuat URL, else kembalikan `null` agar app pakai placeholder.

---

## 4. Implementasi Laravel (siap tempel)

### 4.1 Routes — tambahkan di `routes/api.php`
```php
use App\Http\Controllers\Api\MobileApiController;

Route::prefix('v1')
    ->middleware(['mobile.api', 'throttle:120,1']) // lihat middleware & throttle di bawah
    ->group(function () {
        Route::get('/meta',                 [MobileApiController::class, 'meta']);
        Route::get('/home',                 [MobileApiController::class, 'home']);

        Route::get('/documents',            [MobileApiController::class, 'documents']);
        Route::get('/documents/{id}',       [MobileApiController::class, 'documentShow'])->whereNumber('id');
        Route::get('/documents/{id}/download', [MobileApiController::class, 'documentDownload'])->whereNumber('id');
        Route::get('/document-filters',     [MobileApiController::class, 'documentFilters']);

        Route::post('/ai-search',           [MobileApiController::class, 'aiSearch']);

        Route::get('/news',                 [MobileApiController::class, 'news']);
        Route::get('/news/{id}',            [MobileApiController::class, 'newsShow'])->whereNumber('id');

        Route::get('/announcements',        [MobileApiController::class, 'announcements']);
        Route::get('/announcements/{id}',   [MobileApiController::class, 'announcementShow'])->whereNumber('id');

        Route::get('/legal-info/types',     [MobileApiController::class, 'legalInfoTypes']);
        Route::get('/legal-info',           [MobileApiController::class, 'legalInfo']);
        Route::get('/legal-info/{id}',      [MobileApiController::class, 'legalInfoShow'])->whereNumber('id');

        Route::get('/videos',               [MobileApiController::class, 'videos']);
        Route::get('/profile/{kategori}',   [MobileApiController::class, 'profile']);

        Route::get('/disability',           [MobileApiController::class, 'disability']);
        Route::get('/disability/{id}',      [MobileApiController::class, 'disabilityShow'])->whereNumber('id');

        Route::get('/puu',                  [MobileApiController::class, 'puu']);
        Route::get('/puu/{id}',             [MobileApiController::class, 'puuShow'])->whereNumber('id');

        Route::post('/survey',              [MobileApiController::class, 'survey']);
    });
```

> **CORS/CSRF:** rute `api.php` sudah stateless (tanpa CSRF) — cocok untuk mobile. Untuk device fisik, pastikan `APP_URL` & server dapat diakses dari jaringan.

### 4.2 Middleware API key opsional — `app/Http/Middleware/MobileApiKey.php`
```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MobileApiKey
{
    public function handle(Request $request, Closure $next)
    {
        // Gating ringan; read-only publik. Set MOBILE_API_KEY di .env untuk mengaktifkan.
        $expected = config('services.mobile_api.key');
        if ($expected) {
            $given = $request->header('X-API-Key');
            if (!hash_equals($expected, (string) $given)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }
        $request->headers->set('Accept', 'application/json');
        return $next($request);
    }
}
```
Daftarkan alias `mobile.api` di `bootstrap/app.php` (Laravel 11) pada `->withMiddleware(...)`:
```php
$middleware->alias(['mobile.api' => \App\Http\Middleware\MobileApiKey::class]);
```
Dan tambahkan `config/services.php`: `'mobile_api' => ['key' => env('MOBILE_API_KEY')],`.

### 4.3 Controller — `app/Http/Controllers/Api/MobileApiController.php`
Meniru query yang sudah dipakai Livewire, membungkus JSON, membangun URL media absolut.
```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AiSearchController;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MobileApiController extends Controller
{
    private const CATS = [1 => 'peraturan', 2 => 'monografi', 3 => 'artikel', 4 => 'putusan'];
    private const CAT_LABEL = [
        'peraturan' => 'Peraturan dan Keputusan', 'monografi' => 'Monografi Hukum',
        'artikel' => 'Artikel / Majalah Hukum', 'putusan' => 'Putusan',
    ];

    /* ---------- helper media ---------- */
    private function imgUrl(?string $file): ?string
    {
        $dir = config('app.img_directory'); // 'gambar/'
        return checkFilePath($dir, $file) ? asset('storage/' . $dir . $file) : null;
    }
    private function docUrl(?string $file): ?string
    {
        $dir = config('app.doc_directory'); // 'dokumen/'
        return checkFilePath($dir, $file) ? asset('storage/' . $dir . $file) : null;
    }
    private function paginate($q, Request $r): array
    {
        $perPage = min((int) $r->get('per_page', 10), 50);
        $p = $q->paginate($perPage);
        return [
            'pagination' => [
                'current_page' => $p->currentPage(), 'per_page' => $p->perPage(),
                'total' => $p->total(), 'last_page' => $p->lastPage(),
                'has_more' => $p->hasMorePages(),
            ],
            'items' => $p->items(),
        ];
    }
    private function setLang(Request $r): void
    {
        $lang = $r->get('lang', 'id');
        if (in_array($lang, ['id', 'en', 'zh', 'ko'], true)) App::setLocale($lang);
    }
    private function docListItem($d): array
    {
        return [
            'id' => (int) $d->id,
            'category' => self::CATS[$d->tipe_dokumen] ?? 'peraturan',
            'tipe_dokumen' => (int) $d->tipe_dokumen,
            'judul' => tt($d, 'judul'),
            'jenis_peraturan' => $d->jenis_peraturan,
            'singkatan_jenis' => $d->singkatan_jenis,
            'nomor_peraturan' => $d->nomor_peraturan,
            'tahun_terbit' => $d->tahun_terbit,
            'tanggal_penetapan' => $d->tanggal_penetapan,
            'status' => $d->status,
            'bidang_hukum' => $d->bidang_hukum,
            'abstrak_singkat' => Str::limit(trim(strip_tags((string) tt($d, 'abstrak'))), 180),
            'gambar_sampul_url' => $this->imgUrl($d->gambar_sampul),
            'hit_see' => (int) ($d->hit_see ?? 0),
            'hit_download' => (int) ($d->hit_download ?? 0),
            'has_file' => (bool) DB::table('data_lampiran')->where('id_dokumen', $d->id)->exists(),
        ];
    }

    /* ---------- META ---------- */
    public function meta()
    {
        $types = DB::table('jenis_informasi_hukum')->select('id', 'singkatan', 'name as nama')->get();
        return response()->json(['data' => [
            'app_name' => config('app.name'),
            'document_categories' => collect(self::CAT_LABEL)->map(fn($l, $s) => ['slug' => $s, 'label' => $l])->values(),
            'legal_info_types' => $types,
            'profile_categories' => ['sekilas-sejarah', 'dasar-hukum', 'visi', 'misi', 'sto'],
            'languages' => ['id', 'en', 'zh', 'ko'],
            'contact' => [
                'phone' => config('app.contact'),
                'email' => config('app.email'),
                'address' => config('app.address'),
            ],
            'social' => [
                ['label' => 'Facebook', 'url' => config('app.fb.url', '')],
                ['label' => 'Instagram', 'url' => config('app.ig.url', '')],
                ['label' => 'YouTube', 'url' => config('app.yt.url', '')],
                ['label' => 'TikTok', 'url' => config('app.tt.url', '')],
            ],
        ]]);
    }

    /* ---------- HOME ---------- */
    public function home(Request $r)
    {
        $this->setLang($r);
        $narasi = DB::table('narasi')->first();
        $latest = fn($tipe, $n) => DB::table('document')->where('tipe_dokumen', $tipe)
            ->orderByDesc('created_at')->limit($n)->get();

        $peraturan = $latest(1, 4)->map(fn($d) => $this->docListItem($d));
        $mono = DB::table('document')->where('tipe_dokumen', 2)->orderByDesc('created_at')->first();

        $pengumuman = DB::table('pengumuman')->orderByDesc('created_at')->limit(3)->get()
            ->map(fn($p) => $this->annListItem($p));
        $berita = DB::table('berita')->where('status', 1)->orderByDesc('created_at')->limit(3)->get()
            ->map(fn($b) => $this->newsListItem($b));
        $video = DB::table('video')->orderByDesc('created_at')->limit(3)->get()
            ->map(fn($v) => $this->videoItem($v));

        $count = fn($t) => DB::table('document')->where('tipe_dokumen', $t)->count();

        return response()->json(['data' => [
            'narasi' => ['text' => $narasi ? tt($narasi, 'text') : null],
            'statistik' => ['peraturan' => $count(1), 'monografi' => $count(2), 'artikel' => $count(3), 'putusan' => $count(4)],
            'peraturan_terbaru' => $peraturan,
            'monografi_highlight' => $mono ? $this->docListItem($mono) : null,
            'pengumuman' => $pengumuman,
            'berita' => $berita,
            'video' => $video,
            'pejabat' => [
                ['nama' => 'dr. Hj. SISKA KARINA IMRAN, SKM', 'jabatan' => 'Wali Kota Kendari', 'gambar_url' => asset('assets/img/1.webp')],
                ['nama' => 'SUDIRMAN', 'jabatan' => 'Wakil Wali Kota Kendari', 'gambar_url' => asset('assets/img/2.webp')],
                ['nama' => 'AMIR HASAN, STP, SH, M.Si', 'jabatan' => 'Sekretaris Daerah', 'gambar_url' => asset('assets/img/3.webp')],
            ],
        ]]);
    }

    /* ---------- DOCUMENTS ---------- */
    public function documents(Request $r)
    {
        $this->setLang($r);
        $cat = $r->get('category', 'peraturan');
        $tipe = array_search($cat, self::CATS, true) ?: 1;

        $q = DB::table('document')->where('tipe_dokumen', $tipe);
        if ($r->filled('q'))      $q->where('judul', 'like', '%' . $r->q . '%');
        if ($r->filled('jenis'))  $q->where('jenis_peraturan', $r->jenis);
        if ($r->filled('tahun'))  $q->where('tahun_terbit', $r->tahun);
        if ($r->filled('status')) $q->where('status_terakhir', $r->status);
        if ($r->filled('nomor'))  $q->where('nomor_peraturan', $r->nomor);
        $q->orderByDesc('created_at');

        $res = $this->paginate($q, $r);
        return response()->json([
            'data' => collect($res['items'])->map(fn($d) => $this->docListItem($d)),
            'pagination' => $res['pagination'],
        ]);
    }

    public function documentShow(Request $r, $id)
    {
        $this->setLang($r);
        $d = DB::table('document')->where('id', $id)->first();
        if (!$d) return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);

        hitDocument($id, 'hit_see'); // COALESCE — kolomnya nullable, increment() gagal di NULL

        $subjek = DB::table('data_subyek')->where('id_dokumen', $id)->pluck('subyek');
        $pengarang = DB::table('data_pengarang')->where('data_pengarang.id_dokumen', $id)
            ->join('pengarang', 'data_pengarang.nama_pengarang', 'pengarang.id')
            ->select('pengarang.name as nama')->get()
            ->map(fn($p) => ['nama' => $p->nama]);
        $lampiran = DB::table('data_lampiran')->where('id_dokumen', $id)->get()
            ->map(fn($l) => ['judul' => $l->judul_lampiran ?: 'Dokumen', 'url' => $this->docUrl($l->dokumen_lampiran), 'tipe' => 'pdf'])
            ->filter(fn($l) => $l['url'])->values();
        $terkait = DB::table('peraturan_terkait')->where('id_dokumen', $id)
            ->leftJoin('document', 'peraturan_terkait.peraturan_terkait', 'document.id')
            ->select('document.id', 'document.judul')->get()
            ->filter(fn($t) => $t->id)->map(fn($t) => ['id' => (int) $t->id, 'judul' => $t->judul])->values();

        return response()->json(['data' => [
            'id' => (int) $d->id,
            'category' => self::CATS[$d->tipe_dokumen] ?? 'peraturan',
            'tipe_dokumen' => (int) $d->tipe_dokumen,
            'judul' => tt($d, 'judul'),
            'teu' => $d->teu,
            'nomor_peraturan' => $d->nomor_peraturan,
            'jenis_peraturan' => $d->jenis_peraturan,
            'singkatan_jenis' => $d->singkatan_jenis,
            'bentuk_peraturan' => $d->bentuk_peraturan,
            'tempat_terbit' => $d->tempat_terbit,
            'penerbit' => $d->penerbit,
            'tahun_terbit' => $d->tahun_terbit,
            'tanggal_penetapan' => $d->tanggal_penetapan,
            'tanggal_pengundangan' => $d->tanggal_pengundangan,
            'sumber' => $d->sumber,
            'bahasa' => $d->bahasa,
            'bidang_hukum' => $d->bidang_hukum,
            'penandatanganan' => $d->penandatanganan,
            'status' => $d->status,
            'status_terakhir' => $d->status_terakhir,
            'abstrak' => tt($d, 'abstrak'),
            'gambar_sampul_url' => $this->imgUrl($d->gambar_sampul),
            'isbn' => $d->isbn,
            'deskripsi_fisik' => $d->deskripsi_fisik,
            'lembaga_peradilan' => $d->lembaga_peradilan,
            'pemohon' => $d->pemohon,
            'termohon' => $d->termohon,
            'jenis_perkara' => $d->jenis_perkara,
            'amar_status' => $d->amar_status,
            'subjek' => $subjek,
            'pengarang' => $pengarang,
            'statistik' => ['dilihat' => (int) ($d->hit_see ?? 0) + 1, 'diunduh' => (int) ($d->hit_download ?? 0)],
            'lampiran' => $lampiran,
            'peraturan_terkait' => $terkait,
            'created_at' => $d->created_at,
            'updated_at' => $d->updated_at,
        ]]);
    }

    public function documentDownload($id)
    {
        $d = DB::table('document')->where('id', $id)->first();
        if (!$d) return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        hitDocument($id, 'hit_download'); // COALESCE — lihat catatan di documentShow
        $files = DB::table('data_lampiran')->where('id_dokumen', $id)->get()
            ->map(fn($l) => ['judul' => $l->judul_lampiran ?: 'Dokumen', 'url' => $this->docUrl($l->dokumen_lampiran)])
            ->filter(fn($l) => $l['url'])->values();
        return response()->json(['data' => [
            'id' => (int) $d->id, 'judul' => $d->judul,
            'download_count' => (int) ($d->hit_download ?? 0) + 1, 'files' => $files,
        ]]);
    }

    public function documentFilters(Request $r)
    {
        $tipe = array_search($r->get('category', 'peraturan'), self::CATS, true) ?: 1;
        $base = DB::table('document')->where('tipe_dokumen', $tipe);
        return response()->json(['data' => [
            'jenis'  => (clone $base)->whereNotNull('jenis_peraturan')->where('jenis_peraturan', '!=', '-')->distinct()->orderBy('jenis_peraturan')->pluck('jenis_peraturan'),
            'tahun'  => (clone $base)->whereNotNull('tahun_terbit')->distinct()->orderByDesc('tahun_terbit')->pluck('tahun_terbit'),
            'status' => (clone $base)->whereNotNull('status_terakhir')->distinct()->orderBy('status_terakhir')->pluck('status_terakhir'),
        ]]);
    }

    /* ---------- AI SEARCH (reuse logika web) ---------- */
    public function aiSearch(Request $r, AiSearchController $ai)
    {
        $this->setLang($r);
        $r->validate(['query' => 'required|string|min:3|max:255']);
        // AiSearchController@search sudah mengembalikan JsonResponse {query,explanation,documents,total}.
        // Tambahkan 'category' pada tiap dokumen di dalam AiSearchController (lihat catatan di bawah).
        return $ai->search($r);
    }

    /* ---------- NEWS ---------- */
    private function newsListItem($b): array
    {
        return ['id' => (int) $b->id, 'tanggal' => $b->tanggal, 'judul' => tt($b, 'judul'),
            'image_url' => $this->imgUrl($b->image), 'ringkasan' => Str::limit(trim(strip_tags((string) tt($b, 'isi'))), 180)];
    }
    public function news(Request $r)
    {
        $this->setLang($r);
        $q = DB::table('berita')->where('status', 1);
        if ($r->filled('q')) $q->where('judul', 'like', '%' . $r->q . '%');
        $q->orderByDesc('created_at');
        $res = $this->paginate($q, $r);
        return response()->json(['data' => collect($res['items'])->map(fn($b) => $this->newsListItem($b)), 'pagination' => $res['pagination']]);
    }
    public function newsShow(Request $r, $id)
    {
        $this->setLang($r);
        $b = DB::table('berita')->where('id', $id)->where('status', 1)->first();
        if (!$b) return response()->json(['message' => 'Berita tidak ditemukan'], 404);
        return response()->json(['data' => ['id' => (int) $b->id, 'tanggal' => $b->tanggal,
            'judul' => tt($b, 'judul'), 'isi' => tt($b, 'isi'), 'image_url' => $this->imgUrl($b->image)]]);
    }

    /* ---------- ANNOUNCEMENTS ---------- */
    private function annListItem($p): array
    {
        return ['id' => (int) $p->id, 'tanggal' => $p->tanggal, 'judul' => tt($p, 'judul'),
            'tag' => $p->tag, 'image_url' => $this->imgUrl($p->image),
            'ringkasan' => Str::limit(trim(strip_tags((string) tt($p, 'isi'))), 180)];
    }
    public function announcements(Request $r)
    {
        $this->setLang($r);
        $q = DB::table('pengumuman')->where('status', 1);
        if ($r->filled('q')) $q->where('judul', 'like', '%' . $r->q . '%');
        $q->orderByDesc('created_at');
        $res = $this->paginate($q, $r);
        return response()->json(['data' => collect($res['items'])->map(fn($p) => $this->annListItem($p)), 'pagination' => $res['pagination']]);
    }
    public function announcementShow(Request $r, $id)
    {
        $this->setLang($r);
        $p = DB::table('pengumuman')->where('id', $id)->first();
        if (!$p) return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
        return response()->json(['data' => ['id' => (int) $p->id, 'tanggal' => $p->tanggal, 'judul' => tt($p, 'judul'),
            'tag' => $p->tag, 'isi' => tt($p, 'isi'), 'image_url' => $this->imgUrl($p->image), 'dokumen_url' => $this->docUrl($p->dokumen)]]);
    }

    /* ---------- LEGAL INFO ---------- */
    public function legalInfoTypes()
    {
        return response()->json(['data' => DB::table('jenis_informasi_hukum')->select('id', 'singkatan', 'name as nama')->get()]);
    }
    public function legalInfo(Request $r)
    {
        $this->setLang($r);
        $q = DB::table('informasi_hukum')->where('status', 1)
            ->join('jenis_informasi_hukum', 'informasi_hukum.jenis', 'jenis_informasi_hukum.id')
            ->select('informasi_hukum.*', 'jenis_informasi_hukum.singkatan as jenis_singkatan');
        if ($r->filled('type')) $q->where('informasi_hukum.jenis', $r->type);
        if ($r->filled('q')) $q->where('informasi_hukum.judul', 'like', '%' . $r->q . '%');
        $q->orderByDesc('informasi_hukum.created_at');
        $res = $this->paginate($q, $r);
        return response()->json(['data' => collect($res['items'])->map(fn($x) => [
            'id' => (int) $x->id, 'tanggal' => $x->tanggal, 'judul' => tt($x, 'judul'),
            'jenis_singkatan' => $x->jenis_singkatan, 'image_url' => $this->imgUrl($x->image), 'dokumen_url' => $this->docUrl($x->dokumen),
        ]), 'pagination' => $res['pagination']]);
    }
    public function legalInfoShow(Request $r, $id)
    {
        $this->setLang($r);
        $x = DB::table('informasi_hukum')->where('id', $id)->first();
        if (!$x) return response()->json(['message' => 'Data tidak ditemukan'], 404);
        return response()->json(['data' => ['id' => (int) $x->id, 'tanggal' => $x->tanggal, 'judul' => tt($x, 'judul'),
            'isi' => tt($x, 'isi'), 'image_url' => $this->imgUrl($x->image), 'dokumen_url' => $this->docUrl($x->dokumen)]]);
    }

    /* ---------- VIDEOS ---------- */
    private function videoItem($v): array
    {
        $yid = trim((string) $v->link);
        return ['id' => (int) $v->id, 'tanggal' => $v->tanggal, 'judul' => $v->judul, 'youtube_id' => $yid,
            'thumbnail_url' => "https://img.youtube.com/vi/{$yid}/hqdefault.jpg",
            'watch_url' => "https://www.youtube.com/watch?v={$yid}",
            'embed_url' => "https://www.youtube.com/embed/{$yid}"];
    }
    public function videos(Request $r)
    {
        $q = DB::table('video')->orderByDesc('created_at');
        $res = $this->paginate($q, $r);
        return response()->json(['data' => collect($res['items'])->map(fn($v) => $this->videoItem($v)), 'pagination' => $res['pagination']]);
    }

    /* ---------- PROFILE ---------- */
    public function profile(Request $r, $kategori)
    {
        $this->setLang($r);
        if (!in_array($kategori, ['sekilas-sejarah', 'dasar-hukum', 'visi', 'misi', 'sto'], true))
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        $body = __("profil.{$kategori}.body");
        return response()->json(['data' => ['kategori' => $kategori, 'title' => __("profil.{$kategori}.title"),
            'body' => is_string($body) ? $body : null]]);
    }

    /* ---------- DISABILITY & PUU ----------
     | Sudah diimplementasikan lengkap di file controller nyata:
     | app/Http/Controllers/Api/MobileApiController.php
     | Aturan visibilitas mengikuti frontend: where status_publikasi='published' AND hak_akses='public',
     | whereNull('deleted_at'), orderByDesc('created_at'). PUU: kolom jenis_dokumen dipetakan ke slug
     | kategori (naskah_akademik→naskah-akademik, dst). File cover/dokumen_utama/lampiran via storageUrl()
     | (disk public + cek exists → null bila tak ada). puuShow menaikkan `views`. */

    /* ---------- SURVEY ---------- */
    public function survey(Request $r)
    {
        $v = $r->validate([
            'nama' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'instansi' => 'nullable|string|max:255',
            'jenis_pengguna' => 'required|in:Mahasiswa,Akademisi,Praktisi Hukum,Masyarakat Umum,Lainnya',
            'kemudahan_akses' => 'required|integer|between:1,5',
            'kelengkapan_informasi' => 'required|integer|between:1,5',
            'kecepatan_loading' => 'required|integer|between:1,5',
            'tampilan_antarmuka' => 'required|integer|between:1,5',
            'relevansi_pencarian' => 'required|integer|between:1,5',
            'saran_perbaikan' => 'nullable|string|max:1000',
            'fitur_harapan' => 'nullable|string|max:1000',
            'bersedia_dihubungi' => 'boolean',
            'kontak' => 'nullable|string|max:255',
        ]);
        $v['ip_address'] = $r->ip();
        $v['user_agent'] = $r->userAgent();
        $v['bersedia_dihubungi'] = $r->boolean('bersedia_dihubungi');
        Survey::create($v);
        return response()->json(['data' => ['message' => 'Terima kasih atas partisipasi Anda.']], 201);
    }
}
```

> **Catatan `aiSearch`:** tambahkan satu baris `'category' => self::KATEGORI[$d->tipe_dokumen] ?? 'peraturan',` pada array hasil di `AiSearchController::searchDocuments()` agar app bisa navigasi ke detail memakai `id` + `category` (tanpa perlu decode URL Hashids).

### 4.4 Uji cepat (setelah deploy)
```bash
BASE=http://localhost:6902
curl -s $BASE/api/v1/meta | jq .
curl -s "$BASE/api/v1/home?lang=id" | jq '.data.statistik'
curl -s "$BASE/api/v1/documents?category=peraturan&per_page=3" | jq '.pagination, .data[0]'
curl -s "$BASE/api/v1/documents/1" | jq '.data.judul'
curl -s -X POST "$BASE/api/v1/ai-search" -H 'Content-Type: application/json' -d '{"query":"retribusi"}' | jq '.total'
curl -s "$BASE/api/v1/videos?per_page=3" | jq '.data[0]'
```

---

## 5. Checklist backend (Track A) — ✅ SELESAI (2026-07-21)
- [x] Buat `MobileApiController` ([app/Http/Controllers/Api/MobileApiController.php](../app/Http/Controllers/Api/MobileApiController.php)) — semua endpoint termasuk `disability*` & `puu*` lengkap.
- [x] Buat middleware `MobileApiKey` ([app/Http/Middleware/MobileApiKey.php](../app/Http/Middleware/MobileApiKey.php)) + alias `mobile.api` di `bootstrap/app.php` + `config/services.php` (`mobile_api.key`).
- [x] Tambah grup route `/api/v1` di [routes/api.php](../routes/api.php) (21 rute, dengan `throttle:120,1`).
- [x] Tambah `'category'` di `AiSearchController::searchDocuments()`.
- [x] `php artisan storage:link` sudah jalan.
- [x] Uji semua endpoint (16 endpoint → 200, 404 & 422 sesuai; disability/puu/survey OK).
- [ ] **(Produksi)** Set `MOBILE_API_KEY` di `.env` bila ingin gating (kosong = publik).
- [ ] **(Produksi)** Pastikan `APP_URL` = domain HTTPS asli (mempengaruhi URL media via `asset()`), dan CORS untuk domain app bila perlu.

> **Catatan lingkungan dev:** file media (gambar/dokumen upload) belum ada di DB dump lokal, jadi `*_url` banyak yang `null` — ini benar (helper cek keberadaan file). Di produksi dengan file lengkap, URL akan resolve.
