@php
    $jenisLabels = [
        'naskah_akademik' => 'Naskah Akademik',
        'rancangan_puu' => 'Rancangan PUU',
        'penelitian_hukum' => 'Penelitian Hukum',
        'pengkajian_hukum' => 'Pengkajian Hukum',
        'pengkajian_konstitusi' => 'Pengkajian Konstitusi',
        'analisis_evaluasi' => 'Analisis Evaluasi',
    ];
@endphp

@switch($puu->jenis_dokumen)
    @case('naskah_akademik')
        <h4>Informasi Naskah Akademik</h4>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Rumusan Masalah</th>
                <td>{!! nl2br(e($puu->rumusan_masalah)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Tujuan Penelitian</th>
                <td>{!! nl2br(e($puu->tujuan_penelitian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Metodologi Penelitian</th>
                <td>{!! nl2br(e($puu->metodologi_penelitian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Tim Penyusun</th>
                <td>{{ $puu->tim_penyusun ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Penyelesaian</th>
                <td>{{ $puu->tanggal_penyelesaian ? \Carbon\Carbon::parse($puu->tanggal_penyelesaian)->format('d/m/Y') : '-' }}</td>
            </tr>
        </table>
        @break
        
    @case('rancangan_puu')
        <h4>Informasi Rancangan PUU</h4>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Jenis Rancangan</th>
                <td>{{ $puu->jenis_rancangan ?? '-' }}</td>
            </tr>
            <tr>
                <th>Program Legislasi Nasional (Prolegnas)</th>
                <td>{{ $puu->prolegnas ?? '-' }}</td>
            </tr>
            <tr>
                <th>Inisiator</th>
                <td>{{ $puu->inisiator ?? '-' }}</td>
            </tr>
            <tr>
                <th>Pansus/Panja</th>
                <td>{{ $puu->pansus_panja ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ $puu->tanggal_pengajuan ? \Carbon\Carbon::parse($puu->tanggal_pengajuan)->format('d/m/Y') : '-' }}</td>
            </tr>
        </table>
        @break
        
    @case('penelitian_hukum')
        <h4>Informasi Penelitian Hukum</h4>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Latar Belakang</th>
                <td>{!! nl2br(e($puu->latar_belakang)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Fokus Penelitian</th>
                <td>{!! nl2br(e($puu->fokus_penelitian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Hasil Penelitian</th>
                <td>{!! nl2br(e($puu->hasil_penelitian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Rekomendasi</th>
                <td>{!! nl2br(e($puu->rekomendasi)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Lokasi Penelitian</th>
                <td>{{ $puu->lokasi_penelitian ?? '-' }}</td>
            </tr>
        </table>
        @break
        
    @case('pengkajian_hukum')
        <h4>Informasi Pengkajian Hukum</h4>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Objek Pengkajian</th>
                <td>{!! nl2br(e($puu->objek_pengkajian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Jenis Pengkajian</th>
                <td>{{ $puu->jenis_pengkajian ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tujuan Pengkajian</th>
                <td>{!! nl2br(e($puu->tujuan_pengkajian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Kesimpulan Pengkajian</th>
                <td>{!! nl2br(e($puu->kesimpulan_pengkajian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Tanggal Pengkajian</th>
                <td>{{ $puu->tanggal_pengkajian ? \Carbon\Carbon::parse($puu->tanggal_pengkajian)->format('d/m/Y') : '-' }}</td>
            </tr>
        </table>
        @break
        
    @case('pengkajian_konstitusi')
        <h4>Informasi Pengkajian Konstitusi</h4>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Aspek Konstitusi yang Dikaji</th>
                <td>{!! nl2br(e($puu->aspek_konstitusi)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Jenis Pengkajian Konstitusi</th>
                <td>{{ $puu->jenis_pengkajian_konstitusi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Dasar Hukum Pengkajian</th>
                <td>{!! nl2br(e($puu->dasar_hukum_pengkajian)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Instansi Pengkaji</th>
                <td>{{ $puu->instansi_pengkaji ?? '-' }}</td>
            </tr>
            <tr>
                <th>Implikasi Konstitusional</th>
                <td>{!! nl2br(e($puu->implikasi_konstitusional)) ?? '-' !!}</td>
            </tr>
        </table>
        @break
        
    @case('analisis_evaluasi')
        <h4>Informasi Analisis Evaluasi</h4>
        <table class="table table-bordered">
            <tr>
                <th width="30%">Objek Evaluasi</th>
                <td>{!! nl2br(e($puu->objek_evaluasi)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Metode Evaluasi</th>
                <td>{{ $puu->metode_evaluasi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Indikator Evaluasi</th>
                <td>{!! nl2br(e($puu->indikator_evaluasi)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Temuan Evaluasi</th>
                <td>{!! nl2br(e($puu->temuan_evaluasi)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Rekomendasi Perbaikan</th>
                <td>{!! nl2br(e($puu->rekomendasi_perbaikan)) ?? '-' !!}</td>
            </tr>
            <tr>
                <th>Periode Evaluasi</th>
                <td>{{ $puu->periode_evaluasi ? \Carbon\Carbon::parse($puu->periode_evaluasi)->format('d/m/Y') : '-' }}</td>
            </tr>
        </table>
        @break
@endswitch