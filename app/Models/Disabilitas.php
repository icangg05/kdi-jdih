<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disabilitas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'disabilitas';
    
    protected $fillable = [
        'jenis_dokumen',
        'judul',
        'nomor_dokumen',
        'tahun',
        'tempat_penetapan',
        'tanggal_penetapan',
        'lembaga_penetap',
        'status_dokumen',
        'dokumen_terkait',
        'jenis_disabilitas',
        'ruang_lingkup',
        'sektor_kebijakan',
        'abstrak',
        'kata_kunci',
        'jumlah_halaman',
        'bahasa',
        'dokumen_utama',
        'cover',
        'lampiran',
        'penulis',
        'penerbit',
        'isbn_issn',
        'doi',
        'sumber',
        'url_referensi',
        'tanggal_unggah',
        'pengunggah',
        'status_publikasi',
        'hak_akses',
        'keterangan',
    ];

    protected $casts = [
        'jenis_disabilitas' => 'array',
        'sektor_kebijakan' => 'array',
        'tanggal_penetapan' => 'datetime',
        'tanggal_unggah' => 'datetime',
        'tahun' => 'integer',
        'jumlah_halaman' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Accessor untuk format tampilan
    public function getJenisDisabilitasFormattedAttribute()
    {
        if (!$this->jenis_disabilitas) return '-';
        
        $labels = [
            'fisik' => 'Disabilitas Fisik',
            'intelektual' => 'Disabilitas Intelektual',
            'mental' => 'Disabilitas Mental',
            'sensorik' => 'Disabilitas Sensorik',
            'ganda' => 'Disabilitas Ganda',
            'lainnya' => 'Lainnya',
        ];
        
        $jenisArray = is_array($this->jenis_disabilitas) ? $this->jenis_disabilitas : json_decode($this->jenis_disabilitas, true);
        
        if (!is_array($jenisArray) || empty($jenisArray)) {
            return '-';
        }
        
        return collect($jenisArray)
            ->map(function($item) use ($labels) {
                return $labels[$item] ?? ucfirst($item);
            })
            ->implode(', ');
    }

    public function getSektorKebijakanFormattedAttribute()
    {
        if (!$this->sektor_kebijakan) return '-';
        
        $labels = [
            'pendidikan' => 'Pendidikan',
            'kesehatan' => 'Kesehatan',
            'ketenagakerjaan' => 'Ketenagakerjaan',
            'sosial' => 'Sosial',
            'aksesibilitas' => 'Aksesibilitas',
            'hukum' => 'Hukum & HAM',
            'politik' => 'Politik',
            'lainnya' => 'Lainnya',
        ];
        
        $sektorArray = is_array($this->sektor_kebijakan) ? $this->sektor_kebijakan : json_decode($this->sektor_kebijakan, true);
        
        if (!is_array($sektorArray) || empty($sektorArray)) {
            return '-';
        }
        
        return collect($sektorArray)
            ->map(function($item) use ($labels) {
                return $labels[$item] ?? ucfirst($item);
            })
            ->implode(', ');
    }

    // Accessor untuk jenis dokumen lengkap
    public function getJenisDokumenFormattedAttribute()
    {
        $labels = [
            'uu' => 'Undang-Undang',
            'pp' => 'Peraturan Pemerintah',
            'perpres' => 'Peraturan Presiden',
            'permen' => 'Peraturan Menteri',
            'perda' => 'Peraturan Daerah',
            'keppres' => 'Keputusan Presiden',
            'kepmen' => 'Keputusan Menteri',
            'se' => 'Surat Edaran',
            'juknis' => 'Petunjuk Teknis',
            'panduan' => 'Panduan',
            'laporan' => 'Laporan',
            'kajian' => 'Studi/Kajian',
            'naskah_akademik' => 'Naskah Akademik',
            'rancangan' => 'Rancangan Peraturan',
            'lainnya' => 'Lainnya',
        ];
        
        return $labels[$this->jenis_dokumen] ?? ucfirst($this->jenis_dokumen) ?? '-';
    }

    // Accessor untuk ruang lingkup
    public function getRuangLingkupFormattedAttribute()
    {
        $labels = [
            'nasional' => 'Nasional',
            'provinsi' => 'Provinsi',
            'kabupaten_kota' => 'Kabupaten/Kota',
            'internasional' => 'Internasional',
        ];
        
        return $labels[$this->ruang_lingkup] ?? ucfirst($this->ruang_lingkup) ?? '-';
    }

    // Accessor untuk status dokumen
    public function getStatusDokumenFormattedAttribute()
    {
        $labels = [
            'berlaku' => 'Berlaku',
            'tidak_berlaku' => 'Tidak Berlaku',
            'mencabut' => 'Mencabut',
            'diubah' => 'Diubah',
            'dicabut' => 'Dicabut',
            'draft' => 'Draft',
        ];
        
        return $labels[$this->status_dokumen] ?? ucfirst($this->status_dokumen) ?? '-';
    }

    // Accessor untuk status publikasi
    public function getStatusPublikasiFormattedAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'uploaded' => 'Telah Diunggah',
            'reviewed' => 'Telah Direview',
            'published' => 'Dipublikasikan',
            'pending' => 'Ditunda',
            'deleted' => 'Dihapus',
        ];
        
        return $labels[$this->status_publikasi] ?? ucfirst(str_replace('_', ' ', $this->status_publikasi)) ?? '-';
    }

    // Accessor untuk hak akses
    public function getHakAksesFormattedAttribute()
    {
        $labels = [
            'public' => 'Publik (Semua Orang)',
            'restricted' => 'Terbatas (Login)',
            'admin' => 'Admin Only',
            'internal' => 'Internal Only',
        ];
        
        return $labels[$this->hak_akses] ?? ucfirst($this->hak_akses) ?? '-';
    }

    // Accessor untuk bahasa
    public function getBahasaFormattedAttribute()
    {
        $labels = [
            'indonesia' => 'Indonesia',
            'inggris' => 'Inggris',
            'daerah' => 'Daerah',
            'lainnya' => 'Lainnya',
        ];
        
        return $labels[$this->bahasa] ?? ucfirst($this->bahasa) ?? '-';
    }

    // Accessor untuk tanggal penetapan format Indonesia
    public function getTanggalPenetapanFormattedAttribute()
    {
        if (!$this->tanggal_penetapan) {
            return '-';
        }
        
        try {
            return \Carbon\Carbon::parse($this->tanggal_penetapan)->translatedFormat('d F Y');
        } catch (\Exception $e) {
            return $this->tanggal_penetapan;
        }
    }

    // Accessor untuk tanggal unggah format Indonesia
    public function getTanggalUnggahFormattedAttribute()
    {
        if (!$this->tanggal_unggah) {
            return '-';
        }
        
        try {
            return \Carbon\Carbon::parse($this->tanggal_unggah)->translatedFormat('d F Y, H:i:s');
        } catch (\Exception $e) {
            return $this->tanggal_unggah;
        }
    }

    // Accessor untuk kata kunci sebagai array
    public function getKataKunciArrayAttribute()
    {
        if (!$this->kata_kunci) {
            return [];
        }
        
        $keywords = explode(',', $this->kata_kunci);
        return array_map('trim', $keywords);
    }
}