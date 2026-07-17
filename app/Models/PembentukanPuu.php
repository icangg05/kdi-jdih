<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PembentukanPuu extends Model
{
    use HasFactory;

    protected $table = 'pembentukan_puu';
    
    protected $primaryKey = 'id';
    
    public $timestamps = true;
    
    protected $fillable = [
        // Informasi Dasar
        'jenis_dokumen',
        'judul',
        'nomor_dokumen',
        'tahun',
        'lembaga_pemrakarsa',
        'status_dokumen',
        'tahapan_pembentukan',
        
        // Field Spesifik berdasarkan jenis dokumen
        // Naskah Akademik
        'rumusan_masalah',
        'tujuan_penelitian',
        'metodologi_penelitian',
        'tim_penyusun',
        'tanggal_penyelesaian',
        
        // Rancangan PUU
        'jenis_rancangan',
        'prolegnas',
        'inisiator',
        'pansus_panja',
        'tanggal_pengajuan',
        
        // Penelitian Hukum
        'latar_belakang',
        'fokus_penelitian',
        'hasil_penelitian',
        'rekomendasi',
        'lokasi_penelitian',
        
        // Pengkajian Hukum
        'objek_pengkajian',
        'jenis_pengkajian',
        'tujuan_pengkajian',
        'kesimpulan_pengkajian',
        'tanggal_pengkajian',
        
        // Pengkajian Konstitusi
        'aspek_konstitusi',
        'jenis_pengkajian_konstitusi',
        'dasar_hukum_pengkajian',
        'instansi_pengkaji',
        'implikasi_konstitusional',
        
        // Analisis Evaluasi
        'objek_evaluasi',
        'metode_evaluasi',
        'indikator_evaluasi',
        'temuan_evaluasi',
        'rekomendasi_perbaikan',
        'periode_evaluasi',
        
        // Informasi Umum
        'abstrak',
        'kata_kunci',
        'penulis',
        'editor',
        
        // Upload Dokumen
        'dokumen_utama',
        'cover',
        'lampiran',
        
        // Pengelolaan Dokumen
        'status_publikasi',
        'hak_akses',
        'kategori',
        'keterangan',
        
        // Metadata
        'pengunggah',
        'tanggal_unggah',
        'jumlah_download',
        'views',
    ];
    
    protected $casts = [
        'tahun' => 'integer',
        'prolegnas' => 'integer',
        'tanggal_penyelesaian' => 'date',
        'tanggal_pengajuan' => 'date',
        'tanggal_pengkajian' => 'date',
        'periode_evaluasi' => 'date',
        'tanggal_unggah' => 'date',
        'jumlah_download' => 'integer',
        'views' => 'integer',
    ];
    
    // Scope untuk filter
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('judul', 'like', '%' . $search . '%')
              ->orWhere('nomor_dokumen', 'like', '%' . $search . '%')
              ->orWhere('lembaga_pemrakarsa', 'like', '%' . $search . '%')
              ->orWhere('kata_kunci', 'like', '%' . $search . '%')
              ->orWhere('penulis', 'like', '%' . $search . '%');
        });
    }
    
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_dokumen', $jenis);
    }
    
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_publikasi', $status);
    }
    
    // Accessor untuk URL file
    public function getDokumenUtamaUrlAttribute()
    {
        return $this->dokumen_utama ? asset('storage/' . $this->dokumen_utama) : null;
    }
    
    public function getCoverUrlAttribute()
    {
        return $this->cover ? asset('storage/' . $this->cover) : null;
    }
    
    public function getLampiranUrlAttribute()
    {
        return $this->lampiran ? asset('storage/' . $this->lampiran) : null;
    }
}