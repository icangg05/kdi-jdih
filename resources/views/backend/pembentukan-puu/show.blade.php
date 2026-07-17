@php
    $title = 'Detail Data Pembentukan PUU';
@endphp

<x-layouts.backend :title="$title" :listNav="[['label' => 'Pembentukan PUU', 'route' => route('backend.pembentukan-puu.index')], ['label' => $title]]">

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $puu->judul }}</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.pembentukan-puu.edit', $puu->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('backend.pembentukan-puu.index') }}" class="btn btn-default btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>Informasi Dasar</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Jenis Dokumen</th>
                                <td>
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
                                    <span class="label label-primary">
                                        {{ $jenisLabels[$puu->jenis_dokumen] ?? $puu->jenis_dokumen }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>{{ $puu->judul }}</td>
                            </tr>
                            <tr>
                                <th>Nomor Dokumen</th>
                                <td>{{ $puu->nomor_dokumen ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tahun</th>
                                <td>{{ $puu->tahun }}</td>
                            </tr>
                            <tr>
                                <th>Lembaga Pemrakarsa</th>
                                <td>{{ $puu->lembaga_pemrakarsa }}</td>
                            </tr>
                            <tr>
                                <th>Status Dokumen</th>
                                <td>{{ $puu->status_dokumen }}</td>
                            </tr>
                            <tr>
                                <th>Tahapan Pembentukan</th>
                                <td>{{ $puu->tahapan_pembentukan ?? '-' }}</td>
                            </tr>
                        </table>
                        
                        <h4>Informasi Umum</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Abstrak/Ringkasan</th>
                                <td>{!! nl2br(e($puu->abstrak)) ?? '-' !!}</td>
                            </tr>
                            <tr>
                                <th>Kata Kunci</th>
                                <td>{{ $puu->kata_kunci ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Penulis/Penyusun</th>
                                <td>{{ $puu->penulis ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Editor/Reviewer</th>
                                <td>{{ $puu->editor ?? '-' }}</td>
                            </tr>
                        </table>
                        
                        {{-- Tampilkan field spesifik berdasarkan jenis --}}
                        @include('backend.pembentukan-puu.partials.field-spesifik')
                        
                    </div>
                    
                    <div class="col-md-4">
                        <h4>Pengelolaan Dokumen</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th width="40%">Status Publikasi</th>
                                <td>
                                    @if($puu->status_publikasi == 'published')
                                        <span class="label label-success">Published</span>
                                    @elseif($puu->status_publikasi == 'draft')
                                        <span class="label label-warning">Draft</span>
                                    @elseif($puu->status_publikasi == 'archived')
                                        <span class="label label-info">Arsip</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Hak Akses</th>
                                <td>
                                    @if($puu->hak_akses == 'public')
                                        <span class="label label-primary">Public</span>
                                    @else
                                        <span class="label label-default">Private</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>{{ $puu->kategori ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Pengunggah</th>
                                <td>{{ $puu->pengunggah }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Unggah</th>
                                <td>{{ \Carbon\Carbon::parse($puu->tanggal_unggah)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Keterangan</th>
                                <td>{{ $puu->keterangan ?? '-' }}</td>
                            </tr>
                        </table>
                        
                        <h4>File Dokumen</h4>
                        <div class="list-group">
                            @if($puu->dokumen_utama)
                            <a href="{{ route('backend.pembentukan-puu.download', ['id' => $puu->id, 'type' => 'dokumen']) }}" 
                               class="list-group-item list-group-item-success">
                                <i class="fa fa-file-pdf-o"></i> Dokumen Utama (PDF)
                                <span class="pull-right"><i class="fa fa-download"></i></span>
                            </a>
                            @endif
                            
                            @if($puu->cover)
                            <a href="{{ route('backend.pembentukan-puu.download', ['id' => $puu->id, 'type' => 'cover']) }}" 
                               class="list-group-item">
                                <i class="fa fa-image"></i> Cover/Gambar
                                <span class="pull-right"><i class="fa fa-download"></i></span>
                            </a>
                            @endif
                            
                            @if($puu->lampiran)
                            <a href="{{ route('backend.pembentukan-puu.download', ['id' => $puu->id, 'type' => 'lampiran']) }}" 
                               class="list-group-item">
                                <i class="fa fa-paperclip"></i> Lampiran
                                <span class="pull-right"><i class="fa fa-download"></i></span>
                            </a>
                            @endif
                        </div>
                        
                        <div class="box box-info">
                            <div class="box-header">
                                <h4 class="box-title">Statistik</h4>
                            </div>
                            <div class="box-body">
                                <p><i class="fa fa-download"></i> Download: {{ $puu->jumlah_download }} kali</p>
                                <p><i class="fa fa-eye"></i> Dilihat: {{ $puu->views }} kali</p>
                                <p><i class="fa fa-clock-o"></i> Dibuat: {{ $puu->created_at->format('d/m/Y H:i') }}</p>
                                <p><i class="fa fa-refresh"></i> Diupdate: {{ $puu->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.backend>