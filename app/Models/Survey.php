<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'instansi',
        'jenis_pengguna',
        'kemudahan_akses',
        'kelengkapan_informasi',
        'kecepatan_loading',
        'tampilan_antarmuka',
        'relevansi_pencarian',
        'saran_perbaikan',
        'fitur_harapan',
        'bersedia_dihubungi',
        'kontak',
        'ip_address',
        'user_agent'
    ];
}