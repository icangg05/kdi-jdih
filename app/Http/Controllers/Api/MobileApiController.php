<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AiSearchController;
use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * API read-only untuk aplikasi mobile (frontend publik).
 * Mengembalikan JSON dengan URL media absolut. Backend/admin tetap di web.
 * Lihat kontrak lengkap: mobile-flutter/02-API-CONTRACT.md
 */
class MobileApiController extends Controller
{
    /** tipe_dokumen => slug kategori */
    private const CATS = [1 => 'peraturan', 2 => 'monografi', 3 => 'artikel', 4 => 'putusan'];

    private const CAT_LABEL = [
        'peraturan' => 'Peraturan dan Keputusan',
        'monografi' => 'Monografi Hukum',
        'artikel'   => 'Artikel / Majalah Hukum',
        'putusan'   => 'Putusan',
    ];

    /** kolom pembentukan_puu.jenis_dokumen => slug kategori (samakan dengan PembentukanPuuIndex) */
    private const PUU_CATS = [
        'naskah_akademik'              => 'naskah-akademik',
        'naskah_keterangan_penjelasan' => 'naskah-keterangan-penjelasan',
        'rancangan_puu'                => 'rancangan-puu',
        'penelitian_hukum'             => 'penelitian-hukum',
        'pengkajian_hukum'             => 'pengkajian-hukum',
        'pengkajian_konstitusi'        => 'pengkajian-konstitusi',
        'analisis_evaluasi'            => 'analisis-evaluasi',
    ];

    private const PUU_LABEL = [
        'naskah-akademik'              => 'Naskah Akademik',
        'naskah-keterangan-penjelasan' => 'Naskah Keterangan/Penjelasan',
        'rancangan-puu'                => 'Rancangan PUU',
        'penelitian-hukum'             => 'Penelitian Hukum',
        'pengkajian-hukum'             => 'Pengkajian Hukum',
        'pengkajian-konstitusi'        => 'Pengkajian Konstitusi',
        'analisis-evaluasi'            => 'Analisis & Evaluasi',
    ];

    /* ============================================================
     |  Helper
     * ============================================================ */

    /** URL gambar (disk public, dir 'gambar/'); null bila file tak ada. Mirror card blade web. */
    private function imgUrl(?string $file): ?string
    {
        $dir = config('app.img_directory'); // 'gambar/'
        return checkFilePath($dir, $file) ? asset('storage/' . $dir . $file) : null;
    }

    /** URL dokumen/PDF (disk public, dir 'dokumen/'); null bila file tak ada. */
    private function docUrl(?string $file): ?string
    {
        $dir = config('app.doc_directory'); // 'dokumen/'
        return checkFilePath($dir, $file) ? asset('storage/' . $dir . $file) : null;
    }

    /** URL file yang disimpan di disk 'public' (disabilitas/puu), path relatif dari root disk.
     *  Kembalikan null bila file tak ada (seragam dgn imgUrl/docUrl); pakai asset() → berbasis host request. */
    private function storageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }
        $path = ltrim($path, '/');
        return Storage::exists($path) ? asset('storage/' . $path) : null;
    }

    private function setLang(Request $r): void
    {
        $lang = $r->get('lang', 'id');
        if (in_array($lang, ['id', 'en', 'zh', 'ko'], true)) {
            App::setLocale($lang);
        }
    }

    /** Paginasi seragam dari query builder. */
    private function paginated($builder, Request $r, callable $map): array
    {
        $perPage = min(max((int) $r->get('per_page', 10), 1), 50);
        $p = $builder->paginate($perPage)->withQueryString();

        return [
            'data' => collect($p->items())->map($map)->values(),
            'pagination' => [
                'current_page' => $p->currentPage(),
                'per_page'     => $p->perPage(),
                'total'        => $p->total(),
                'last_page'    => $p->lastPage(),
                'has_more'     => $p->hasMorePages(),
            ],
        ];
    }

    private function docListItem($d): array
    {
        return [
            'id'                => (int) $d->id,
            'category'          => self::CATS[$d->tipe_dokumen] ?? 'peraturan',
            'tipe_dokumen'      => (int) $d->tipe_dokumen,
            'judul'             => tt($d, 'judul'),
            'jenis_peraturan'   => $d->jenis_peraturan,
            'singkatan_jenis'   => $d->singkatan_jenis,
            'nomor_peraturan'   => $d->nomor_peraturan,
            'tahun_terbit'      => $d->tahun_terbit,
            'tanggal_penetapan' => $d->tanggal_penetapan,
            'status'            => $d->status,
            'status_terakhir'   => $d->status_terakhir,
            'bidang_hukum'      => $d->bidang_hukum,
            'abstrak_singkat'   => $this->excerpt(tt($d, 'abstrak')),
            'gambar_sampul_url' => $this->imgUrl($d->gambar_sampul),
            'hit_see'           => (int) ($d->hit_see ?? 0),
            'hit_download'      => (int) ($d->hit_download ?? 0),
            'has_file'          => DB::table('data_lampiran')->where('id_dokumen', $d->id)->exists(),
        ];
    }

    /// Ringkasan teks dari HTML. Tag diganti spasi lebih dulu supaya
    /// "</p><p>" tidak membuat akhir & awal paragraf menempel jadi satu kata.
    private function excerpt($html): string
    {
        return Str::limit(Str::squish(strip_tags(str_replace('<', ' <', (string) $html))), 180);
    }

    private function newsListItem($b): array
    {
        return [
            'id'        => (int) $b->id,
            'tanggal'   => $b->tanggal,
            'judul'     => tt($b, 'judul'),
            'image_url' => $this->imgUrl($b->image),
            'ringkasan' => $this->excerpt(tt($b, 'isi')),
        ];
    }

    private function annListItem($p): array
    {
        return [
            'id'        => (int) $p->id,
            'tanggal'   => $p->tanggal,
            'judul'     => tt($p, 'judul'),
            'tag'       => $p->tag,
            'image_url' => $this->imgUrl($p->image),
            'ringkasan' => $this->excerpt(tt($p, 'isi')),
        ];
    }

    private function videoItem($v): array
    {
        $yid = trim((string) $v->link);
        return [
            'id'            => (int) $v->id,
            'tanggal'       => $v->tanggal,
            'judul'         => $v->judul,
            'youtube_id'    => $yid,
            'thumbnail_url' => "https://img.youtube.com/vi/{$yid}/hqdefault.jpg",
            'watch_url'     => "https://www.youtube.com/watch?v={$yid}",
            'embed_url'     => "https://www.youtube.com/embed/{$yid}",
        ];
    }

    /* ============================================================
     |  META & HOME
     * ============================================================ */

    public function meta()
    {
        $types = DB::table('jenis_informasi_hukum')->select('id', 'singkatan', 'name as nama')->get();

        return response()->json(['data' => [
            'app_name'            => config('app.name'),
            'document_categories' => collect(self::CAT_LABEL)->map(fn ($l, $s) => ['slug' => $s, 'label' => $l])->values(),
            'legal_info_types'    => $types,
            'profile_categories'  => ['sekilas-sejarah', 'dasar-hukum', 'visi', 'misi', 'sto'],
            'puu_categories'      => collect(self::PUU_LABEL)->map(fn ($l, $s) => ['slug' => $s, 'label' => $l])->values(),
            'languages'           => ['id', 'en', 'zh', 'ko'],
            'contact'             => [
                'phone'   => config('app.contact'),
                'email'   => config('app.email'),
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

    /**
     * Urutan "paling baru" yang tahan data hasil impor: banyak baris lama
     * punya created_at kosong atau kembar, dan ORDER BY satu kolom seperti itu
     * mengembalikan urutan yang tidak tentu. Tanggal terbit dipakai lebih dulu
     * bila ada, lalu created_at, dan id sebagai pemutus supaya urutannya sama
     * di setiap permintaan (halaman 2 tidak mengulang isi halaman 1).
     *
     * @param  string|null  $tanggal  kolom tanggal terbit; 'tanggal_penetapan'
     *                                untuk dokumen, 'tanggal' untuk kabar
     * @param  string       $prefix   awalan tabel bila kueri memakai join
     */
    private function terbaru($q, ?string $tanggal = null, string $prefix = '')
    {
        $created = $prefix . 'created_at';
        // COALESCE saja, tanpa NULLIF: membandingkan kolom DATE dengan '' ditolak
        // MySQL strict mode (SQLSTATE[HY000] 1525 Incorrect DATE value). Baris
        // tanpa tanggal tetap turun ke bawah karena NULL selalu terakhir di DESC.
        $q->orderByRaw($tanggal ? "COALESCE($tanggal, $created) DESC" : "$created DESC");

        return $q->orderByDesc($prefix . 'id');
    }

    public function home(Request $r)
    {
        $this->setLang($r);

        $narasi = DB::table('narasi')->first();

        $peraturan = $this->terbaru(DB::table('document')->where('tipe_dokumen', 1), 'tanggal_penetapan')
            ->limit(4)->get()
            ->map(fn ($d) => $this->docListItem($d));

        $mono = $this->terbaru(DB::table('document')->where('tipe_dokumen', 2), 'tanggal_penetapan')->first();

        $pengumuman = $this->terbaru(DB::table('pengumuman')->where('status', 1), 'tanggal')
            ->limit(3)->get()
            ->map(fn ($p) => $this->annListItem($p));

        $berita = $this->terbaru(DB::table('berita')->where('status', 1), 'tanggal')
            ->limit(3)->get()
            ->map(fn ($b) => $this->newsListItem($b));

        $video = $this->terbaru(DB::table('video'))->limit(3)->get()
            ->map(fn ($v) => $this->videoItem($v));

        $count = fn ($t) => DB::table('document')->where('tipe_dokumen', $t)->count();

        return response()->json(['data' => [
            'narasi'    => ['text' => $narasi ? tt($narasi, 'text') : null],
            'statistik' => [
                'peraturan' => $count(1),
                'monografi' => $count(2),
                'artikel'   => $count(3),
                'putusan'   => $count(4),
            ],
            'peraturan_terbaru'   => $peraturan,
            'monografi_highlight' => $mono ? $this->docListItem($mono) : null,
            'pengumuman'          => $pengumuman,
            'berita'              => $berita,
            'video'               => $video,
            'pejabat'             => [
                ['nama' => 'dr. Hj. SISKA KARINA IMRAN, SKM', 'jabatan' => 'Wali Kota Kendari', 'gambar_url' => asset('assets/img/1.webp')],
                ['nama' => 'SUDIRMAN', 'jabatan' => 'Wakil Wali Kota Kendari', 'gambar_url' => asset('assets/img/2.webp')],
                ['nama' => 'AMIR HASAN, STP, SH, M.Si', 'jabatan' => 'Sekretaris Daerah Kota Kendari', 'gambar_url' => asset('assets/img/3.webp')],
            ],
        ]]);
    }

    /* ============================================================
     |  DOCUMENTS
     * ============================================================ */

    public function documents(Request $r)
    {
        $this->setLang($r);
        $tipe = array_search($r->get('category', 'peraturan'), self::CATS, true) ?: 1;

        $q = DB::table('document')->where('tipe_dokumen', $tipe);
        if ($r->filled('q')) {
            // per kata (AND), bukan frasa utuh — "retribusi sampah" tetap
            // menemukan judul "TARIF RETRIBUSI PERSAMPAHAN"
            foreach (preg_split('/\s+/', trim($r->q)) as $word) {
                if ($word !== '') $q->where('judul', 'like', "%{$word}%");
            }
        }
        if ($r->filled('jenis'))  $q->where('jenis_peraturan', $r->jenis);
        if ($r->filled('tahun'))  $q->where('tahun_terbit', $r->tahun);
        if ($r->filled('status')) $q->where('status_terakhir', $r->status);
        if ($r->filled('nomor'))  $q->where('nomor_peraturan', $r->nomor);
        $this->terbaru($q, 'tanggal_penetapan');

        return response()->json($this->paginated($q, $r, fn ($d) => $this->docListItem($d)));
    }

    public function documentShow(Request $r, $id)
    {
        $this->setLang($r);
        $d = DB::table('document')->where('id', $id)->first();
        if (!$d) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        hitDocument($id, 'hit_see');

        $subjek = DB::table('data_subyek')->where('id_dokumen', $id)->pluck('subyek')->filter()->values();

        $pengarang = DB::table('data_pengarang')->where('data_pengarang.id_dokumen', $id)
            ->join('pengarang', 'data_pengarang.nama_pengarang', 'pengarang.id')
            ->select('pengarang.name as nama')->get()
            ->map(fn ($p) => ['nama' => $p->nama])->values();

        $lampiran = DB::table('data_lampiran')->where('id_dokumen', $id)->orderBy('urutan')->get()
            ->map(fn ($l) => ['judul' => $l->judul_lampiran ?: 'Dokumen', 'url' => $this->docUrl($l->dokumen_lampiran), 'tipe' => 'pdf'])
            ->filter(fn ($l) => $l['url'])->values();

        $terkait = DB::table('peraturan_terkait')->where('id_dokumen', $id)
            ->leftJoin('document', 'peraturan_terkait.peraturan_terkait', 'document.id')
            ->select('document.id', 'document.judul')->get()
            ->filter(fn ($t) => $t->id)
            ->map(fn ($t) => ['id' => (int) $t->id, 'judul' => $t->judul])->values();

        return response()->json(['data' => [
            'id'                   => (int) $d->id,
            'category'             => self::CATS[$d->tipe_dokumen] ?? 'peraturan',
            'tipe_dokumen'         => (int) $d->tipe_dokumen,
            'judul'                => tt($d, 'judul'),
            'teu'                  => $d->teu,
            'nomor_peraturan'      => $d->nomor_peraturan,
            'jenis_peraturan'      => $d->jenis_peraturan,
            'singkatan_jenis'      => $d->singkatan_jenis,
            'bentuk_peraturan'     => $d->bentuk_peraturan,
            'tempat_terbit'        => $d->tempat_terbit,
            'penerbit'             => $d->penerbit,
            'tahun_terbit'         => $d->tahun_terbit,
            'tanggal_penetapan'    => $d->tanggal_penetapan,
            'tanggal_pengundangan' => $d->tanggal_pengundangan,
            'sumber'               => $d->sumber,
            'bahasa'               => $d->bahasa,
            'bidang_hukum'         => $d->bidang_hukum,
            'penandatanganan'      => $d->penandatanganan,
            'status'               => $d->status,
            'status_terakhir'      => $d->status_terakhir,
            'abstrak'              => tt($d, 'abstrak'),
            'gambar_sampul_url'    => $this->imgUrl($d->gambar_sampul),
            // monografi/artikel
            'isbn'                 => $d->isbn,
            'deskripsi_fisik'      => $d->deskripsi_fisik,
            // putusan
            'lembaga_peradilan'    => $d->lembaga_peradilan,
            'pemohon'              => $d->pemohon,
            'termohon'             => $d->termohon,
            'jenis_perkara'        => $d->jenis_perkara,
            'amar_status'          => $d->amar_status,
            // relasi
            'subjek'               => $subjek,
            'pengarang'            => $pengarang,
            'statistik'            => ['dilihat' => (int) ($d->hit_see ?? 0) + 1, 'diunduh' => (int) ($d->hit_download ?? 0)],
            'lampiran'             => $lampiran,
            'peraturan_terkait'    => $terkait,
            'created_at'           => $d->created_at,
            'updated_at'           => $d->updated_at,
        ]]);
    }

    public function documentDownload($id)
    {
        $d = DB::table('document')->where('id', $id)->first();
        if (!$d) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        hitDocument($id, 'hit_download');

        $files = DB::table('data_lampiran')->where('id_dokumen', $id)->orderBy('urutan')->get()
            ->map(fn ($l) => ['judul' => $l->judul_lampiran ?: 'Dokumen', 'url' => $this->docUrl($l->dokumen_lampiran)])
            ->filter(fn ($l) => $l['url'])->values();

        return response()->json(['data' => [
            'id'             => (int) $d->id,
            'judul'          => $d->judul,
            'download_count' => (int) ($d->hit_download ?? 0) + 1,
            'files'          => $files,
        ]]);
    }

    public function documentFilters(Request $r)
    {
        $tipe = array_search($r->get('category', 'peraturan'), self::CATS, true) ?: 1;
        $base = DB::table('document')->where('tipe_dokumen', $tipe);

        return response()->json(['data' => [
            'jenis'  => (clone $base)->whereNotNull('jenis_peraturan')->where('jenis_peraturan', '!=', '-')->where('jenis_peraturan', '!=', '')->distinct()->orderBy('jenis_peraturan')->pluck('jenis_peraturan'),
            'tahun'  => (clone $base)->whereNotNull('tahun_terbit')->where('tahun_terbit', '!=', '')->distinct()->orderByDesc('tahun_terbit')->pluck('tahun_terbit'),
            'status' => (clone $base)->whereNotNull('status_terakhir')->where('status_terakhir', '!=', '')->distinct()->orderBy('status_terakhir')->pluck('status_terakhir'),
        ]]);
    }

    /* ============================================================
     |  AI SEARCH (reuse logika web)
     * ============================================================ */

    public function aiSearch(Request $r, AiSearchController $ai)
    {
        $this->setLang($r);
        // AiSearchController@search memvalidasi 'query' (min 3) & mengembalikan
        // {query, explanation, documents[], total}. Tiap dokumen memuat 'category'.
        return $ai->search($r);
    }

    /* ============================================================
     |  NEWS (berita)
     * ============================================================ */

    public function news(Request $r)
    {
        $this->setLang($r);
        $q = DB::table('berita')->where('status', 1);
        if ($r->filled('q')) $q->where('judul', 'like', '%' . $r->q . '%');
        $this->terbaru($q, 'tanggal');

        return response()->json($this->paginated($q, $r, fn ($b) => $this->newsListItem($b)));
    }

    public function newsShow(Request $r, $id)
    {
        $this->setLang($r);
        $b = DB::table('berita')->where('id', $id)->where('status', 1)->first();
        if (!$b) {
            return response()->json(['message' => 'Berita tidak ditemukan'], 404);
        }

        return response()->json(['data' => [
            'id'        => (int) $b->id,
            'tanggal'   => $b->tanggal,
            'judul'     => tt($b, 'judul'),
            'isi'       => tt($b, 'isi'),
            'image_url' => $this->imgUrl($b->image),
        ]]);
    }

    /* ============================================================
     |  ANNOUNCEMENTS (pengumuman)
     * ============================================================ */

    public function announcements(Request $r)
    {
        $this->setLang($r);
        $q = DB::table('pengumuman')->where('status', 1);
        if ($r->filled('q')) $q->where('judul', 'like', '%' . $r->q . '%');
        $this->terbaru($q, 'tanggal');

        return response()->json($this->paginated($q, $r, fn ($p) => $this->annListItem($p)));
    }

    public function announcementShow(Request $r, $id)
    {
        $this->setLang($r);
        $p = DB::table('pengumuman')->where('id', $id)->first();
        if (!$p) {
            return response()->json(['message' => 'Pengumuman tidak ditemukan'], 404);
        }

        return response()->json(['data' => [
            'id'          => (int) $p->id,
            'tanggal'     => $p->tanggal,
            'judul'       => tt($p, 'judul'),
            'tag'         => $p->tag,
            'isi'         => tt($p, 'isi'),
            'image_url'   => $this->imgUrl($p->image),
            'dokumen_url' => $this->docUrl($p->dokumen),
        ]]);
    }

    /* ============================================================
     |  LEGAL INFO (informasi hukum)
     * ============================================================ */

    public function legalInfoTypes()
    {
        return response()->json([
            'data' => DB::table('jenis_informasi_hukum')->select('id', 'singkatan', 'name as nama')->get(),
        ]);
    }

    public function legalInfo(Request $r)
    {
        $this->setLang($r);
        $q = DB::table('informasi_hukum')->where('informasi_hukum.status', 1)
            ->join('jenis_informasi_hukum', 'informasi_hukum.jenis', 'jenis_informasi_hukum.id')
            ->select('informasi_hukum.*', 'jenis_informasi_hukum.singkatan as jenis_singkatan');
        if ($r->filled('type')) $q->where('informasi_hukum.jenis', $r->type);
        if ($r->filled('q'))    $q->where('informasi_hukum.judul', 'like', '%' . $r->q . '%');
        $this->terbaru($q, 'informasi_hukum.tanggal', 'informasi_hukum.');

        return response()->json($this->paginated($q, $r, fn ($x) => [
            'id'              => (int) $x->id,
            'tanggal'         => $x->tanggal,
            'judul'           => tt($x, 'judul'),
            'jenis_singkatan' => $x->jenis_singkatan,
            'image_url'       => $this->imgUrl($x->image),
            'dokumen_url'     => $this->docUrl($x->dokumen),
        ]));
    }

    public function legalInfoShow(Request $r, $id)
    {
        $this->setLang($r);
        $x = DB::table('informasi_hukum')->where('id', $id)->first();
        if (!$x) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['data' => [
            'id'          => (int) $x->id,
            'tanggal'     => $x->tanggal,
            'judul'       => tt($x, 'judul'),
            'isi'         => tt($x, 'isi'),
            'image_url'   => $this->imgUrl($x->image),
            'dokumen_url' => $this->docUrl($x->dokumen),
        ]]);
    }

    /* ============================================================
     |  VIDEOS
     * ============================================================ */

    public function videos(Request $r)
    {
        $q = $this->terbaru(DB::table('video'));

        return response()->json($this->paginated($q, $r, fn ($v) => $this->videoItem($v)));
    }

    /* ============================================================
     |  PROFILE
     * ============================================================ */

    public function profile(Request $r, $kategori)
    {
        $this->setLang($r);
        if (!in_array($kategori, ['sekilas-sejarah', 'dasar-hukum', 'visi', 'misi', 'sto'], true)) {
            return response()->json(['message' => 'Kategori tidak ditemukan'], 404);
        }

        $body = __("profil.{$kategori}.body");

        return response()->json(['data' => [
            'kategori' => $kategori,
            'title'    => __("profil.{$kategori}.title"),
            'body'     => is_string($body) ? $body : null,
        ]]);
    }

    /* ============================================================
     |  DISABILITAS
     * ============================================================ */

    public function disability(Request $r)
    {
        $q = DB::table('disabilitas')->whereNull('deleted_at')
            ->where('status_publikasi', 'published')
            ->where('hak_akses', 'public');

        if ($r->filled('q')) {
            $term = $r->q;
            $q->where(function ($sub) use ($term) {
                $sub->where('judul', 'like', "%{$term}%")
                    ->orWhere('abstrak', 'like', "%{$term}%")
                    ->orWhere('kata_kunci', 'like', "%{$term}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$term}%");
            });
        }
        if ($r->filled('jenis')) $q->where('jenis_dokumen', $r->jenis);
        if ($r->filled('tahun')) $q->where('tahun', $r->tahun);
        $q->orderByDesc('created_at');

        return response()->json($this->paginated($q, $r, fn ($x) => [
            'id'            => (int) $x->id,
            'jenis_dokumen' => $x->jenis_dokumen,
            'judul'         => $x->judul,
            'nomor_dokumen' => $x->nomor_dokumen,
            'tahun'         => $x->tahun,
            'status_dokumen' => $x->status_dokumen,
            'cover_url'     => $this->storageUrl($x->cover),
        ]));
    }

    public function disabilityShow(Request $r, $id)
    {
        $x = DB::table('disabilitas')->whereNull('deleted_at')->where('id', $id)
            ->where('status_publikasi', 'published')->where('hak_akses', 'public')->first();
        if (!$x) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        return response()->json(['data' => [
            'id'                => (int) $x->id,
            'jenis_dokumen'     => $x->jenis_dokumen,
            'judul'             => $x->judul,
            'nomor_dokumen'     => $x->nomor_dokumen,
            'tahun'             => $x->tahun,
            'tempat_penetapan'  => $x->tempat_penetapan,
            'tanggal_penetapan' => $x->tanggal_penetapan,
            'lembaga_penetap'   => $x->lembaga_penetap,
            'status_dokumen'    => $x->status_dokumen,
            'dokumen_terkait'   => $x->dokumen_terkait,
            'jenis_disabilitas' => $x->jenis_disabilitas,
            'ruang_lingkup'     => $x->ruang_lingkup,
            'sektor_kebijakan'  => $x->sektor_kebijakan,
            'abstrak'           => $x->abstrak,
            'kata_kunci'        => $x->kata_kunci,
            'jumlah_halaman'    => $x->jumlah_halaman,
            'bahasa'            => $x->bahasa,
            'penulis'           => $x->penulis,
            'penerbit'          => $x->penerbit,
            'sumber'            => $x->sumber,
            'keterangan'        => $x->keterangan,
            'cover_url'         => $this->storageUrl($x->cover),
            'dokumen_url'       => $this->storageUrl($x->dokumen_utama),
            'lampiran_url'      => $this->storageUrl($x->lampiran),
        ]]);
    }

    /* ============================================================
     |  PEMBENTUKAN PUU
     * ============================================================ */

    public function puu(Request $r)
    {
        $q = DB::table('pembentukan_puu')->whereNull('deleted_at')
            ->where('status_publikasi', 'published')
            ->where('hak_akses', 'public');

        // Filter kategori (slug -> nilai kolom jenis_dokumen)
        if ($r->filled('category')) {
            $db = array_search($r->get('category'), self::PUU_CATS, true);
            if ($db) $q->where('jenis_dokumen', $db);
        }
        if ($r->filled('q')) {
            $term = $r->q;
            $q->where(function ($sub) use ($term) {
                $sub->where('judul', 'like', "%{$term}%")
                    ->orWhere('abstrak', 'like', "%{$term}%")
                    ->orWhere('kata_kunci', 'like', "%{$term}%")
                    ->orWhere('penulis', 'like', "%{$term}%")
                    ->orWhere('lembaga_pemrakarsa', 'like', "%{$term}%")
                    ->orWhere('nomor_dokumen', 'like', "%{$term}%");
            });
        }
        if ($r->filled('tahun')) $q->where('tahun', $r->tahun);
        $q->orderByDesc('created_at');

        return response()->json($this->paginated($q, $r, fn ($x) => [
            'id'             => (int) $x->id,
            'jenis_dokumen'  => $x->jenis_dokumen,
            'kategori'       => self::PUU_CATS[$x->jenis_dokumen] ?? null,
            'judul'          => $x->judul,
            'nomor_dokumen'  => $x->nomor_dokumen,
            'tahun'          => $x->tahun,
            'status_dokumen' => $x->status_dokumen,
            'cover_url'      => $this->storageUrl($x->cover),
        ]));
    }

    public function puuShow(Request $r, $id)
    {
        $x = DB::table('pembentukan_puu')->whereNull('deleted_at')->where('id', $id)
            ->where('status_publikasi', 'published')->where('hak_akses', 'public')->first();
        if (!$x) {
            return response()->json(['message' => 'Dokumen tidak ditemukan'], 404);
        }

        DB::table('pembentukan_puu')->where('id', $id)->increment('views');

        return response()->json(['data' => [
            'id'                 => (int) $x->id,
            'jenis_dokumen'      => $x->jenis_dokumen,
            'kategori'           => self::PUU_CATS[$x->jenis_dokumen] ?? null,
            'judul'              => $x->judul,
            'nomor_dokumen'      => $x->nomor_dokumen,
            'tahun'              => $x->tahun,
            'lembaga_pemrakarsa' => $x->lembaga_pemrakarsa,
            'status_dokumen'     => $x->status_dokumen,
            'tahapan_pembentukan' => $x->tahapan_pembentukan,
            'abstrak'            => $x->abstrak,
            'kata_kunci'         => $x->kata_kunci,
            'penulis'            => $x->penulis,
            'editor'             => $x->editor,
            // field spesifik kategori (null bila tak relevan)
            'rumusan_masalah'    => $x->rumusan_masalah,
            'tujuan_penelitian'  => $x->tujuan_penelitian,
            'metodologi_penelitian' => $x->metodologi_penelitian,
            'latar_belakang'     => $x->latar_belakang,
            'fokus_penelitian'   => $x->fokus_penelitian,
            'hasil_penelitian'   => $x->hasil_penelitian,
            'rekomendasi'        => $x->rekomendasi,
            'objek_pengkajian'   => $x->objek_pengkajian,
            'kesimpulan_pengkajian' => $x->kesimpulan_pengkajian,
            'aspek_konstitusi'   => $x->aspek_konstitusi,
            'temuan_evaluasi'    => $x->temuan_evaluasi,
            'rekomendasi_perbaikan' => $x->rekomendasi_perbaikan,
            'keterangan'         => $x->keterangan,
            'views'              => (int) ($x->views ?? 0) + 1,
            'cover_url'          => $this->storageUrl($x->cover),
            'dokumen_url'        => $this->storageUrl($x->dokumen_utama),
            'lampiran_url'       => $this->storageUrl($x->lampiran),
        ]]);
    }

    /* ============================================================
     |  SURVEY (satu-satunya aksi tulis)
     * ============================================================ */

    public function survey(Request $r)
    {
        $v = $r->validate([
            'nama'                  => 'required|string|max:255',
            'email'                 => 'nullable|email|max:255',
            'instansi'              => 'nullable|string|max:255',
            'jenis_pengguna'        => 'required|in:Mahasiswa,Akademisi,Praktisi Hukum,Masyarakat Umum,Lainnya',
            'kemudahan_akses'       => 'required|integer|between:1,5',
            'kelengkapan_informasi' => 'required|integer|between:1,5',
            'kecepatan_loading'     => 'required|integer|between:1,5',
            'tampilan_antarmuka'    => 'required|integer|between:1,5',
            'relevansi_pencarian'   => 'required|integer|between:1,5',
            'saran_perbaikan'       => 'nullable|string|max:1000',
            'fitur_harapan'         => 'nullable|string|max:1000',
            'bersedia_dihubungi'    => 'boolean',
            'kontak'                => 'nullable|string|max:255',
        ]);

        $v['ip_address']         = $r->ip();
        $v['user_agent']         = $r->userAgent();
        $v['bersedia_dihubungi'] = $r->boolean('bersedia_dihubungi');

        Survey::create($v);

        return response()->json(['data' => ['message' => 'Terima kasih atas partisipasi Anda.']], 201);
    }
}
