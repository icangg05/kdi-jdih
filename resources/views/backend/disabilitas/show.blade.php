{{-- resources/views/backend/disabilitas/show.blade.php --}}
<x-backend.section-document-view
    :title="$title"
    :data="$disabilitas"
    :listNav="[['label' => 'Disabilitas', 'route' => route('backend.disabilitas.index')], ['label' => $title]]">

    <x-slot:tabPane>
        <li class="tab-item" data-tab="dataUtama" onclick="localStorage.setItem('tabActive', 'dataUtama');">
            <a href="#tab_1" data-toggle="tab">Data Utama</a>
        </li>
        <li class="tab-item" data-tab="dataKlasifikasi" onclick="localStorage.setItem('tabActive', 'dataKlasifikasi');">
            <a href="#tab_2" data-toggle="tab">Klasifikasi Disabilitas</a>
        </li>
        <li class="tab-item" data-tab="dataDokumen" onclick="localStorage.setItem('tabActive', 'dataDokumen');">
            <a href="#tab_3" data-toggle="tab">Dokumen</a>
        </li>
        <li class="tab-item" data-tab="dataPengunggah" onclick="localStorage.setItem('tabActive', 'dataPengunggah');">
            <a href="#tab_4" data-toggle="tab">Data Pengunggah</a>
        </li>
    </x-slot:tabPane>

    <!-- Tab 1: Data Utama -->
    <div class="tab-pane tab-item" data-tab="dataUtama" id="tab_1">
        <div class="box-header">
            <a class="btn btn-success btn-flat" href="{{ route('backend.disabilitas.index') }}">
                <i class="fa fa-mail-reply"></i> Kembali ke Daftar
            </a>&nbsp;
            <a class="btn btn-primary btn-flat" href="{{ route('backend.disabilitas.edit', $disabilitas->id) }}">
                <i class="fa fa-pencil"></i> Ubah Data Disabilitas
            </a>
            <p></p>
            <table id="w0" class="table table-striped table-bordered detail-view">
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Informasi Dasar Dokumen" />
                    </td>
                </tr>
                <tr>
                    <th>Jenis Dokumen</th>
                    <td>
                        @php
                            $jenisDokumenLabels = [
                                'Undang-Undang' => 'Undang-Undang',
                                'Peraturan Pemerintah' => 'Peraturan Pemerintah',
                                'Peraturan Presiden' => 'Peraturan Presiden',
                                'Peraturan Menteri' => 'Peraturan Menteri',
                                'Peraturan Daerah' => 'Peraturan Daerah',
                                'Keputusan Presiden' => 'Keputusan Presiden',
                                'eputusan Menteri' => 'Keputusan Menteri',
                                'Surat Edaran' => 'Surat Edaran',
                                'Petunjuk Teknis' => 'Petunjuk Teknis',
                                'Panduan' => 'Panduan',
                                'Laporan' => 'Laporan',
                                'Studi/Kajian' => 'Studi/Kajian',
                                'Naskah Akademik' => 'Naskah Akademik',
                                'Rancangan Peraturan' => 'Rancangan Peraturan',
                                'lainnya' => 'Lainnya'
                            ];
                        @endphp
                        {{ $jenisDokumenLabels[$disabilitas->jenis_dokumen] ?? ucfirst($disabilitas->jenis_dokumen) ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <th>Judul Dokumen</th>
                    <td>{{ $disabilitas->judul ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Nomor Dokumen</th>
                    <td>{{ $disabilitas->nomor_dokumen ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Tahun</th>
                    <td>{{ $disabilitas->tahun ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Tempat Penetapan</th>
                    <td>{{ $disabilitas->tempat_penetapan ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Penetapan</th>
                    <td>
                        @if($disabilitas->tanggal_penetapan)
                            {{ \Carbon\Carbon::parse($disabilitas->tanggal_penetapan)->translatedFormat('d F Y') }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Lembaga Penetap</th>
                    <td>{{ $disabilitas->lembaga_penetap ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Status Dokumen</th>
                    <td>
                        @php
                            $statusColors = [
                                'berlaku' => 'success',
                                'tidak_berlaku' => 'danger',
                                'mencabut' => 'warning',
                                'diubah' => 'info',
                                'dicabut' => 'dark',
                                'draft' => 'secondary'
                            ];
                            
                            $statusLabels = [
                                'berlaku' => 'Berlaku',
                                'tidak_berlaku' => 'Tidak Berlaku',
                                'mencabut' => 'Mencabut',
                                'diubah' => 'Diubah',
                                'dicabut' => 'Dicabut',
                                'draft' => 'Draft'
                            ];
                            
                            $status = $disabilitas->status_dokumen ?? '';
                            $color = $statusColors[$status] ?? 'secondary';
                            $label = $statusLabels[$status] ?? ucfirst($status);
                        @endphp
                        <span class="badge badge-{{ $color }}">
                            {{ $label ?: '—' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Dokumen Terkait</th>
                    <td>{{ $disabilitas->dokumen_terkait ?? '—' }}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Informasi Konten" />
                    </td>
                </tr>
                <tr>
                    <th>Abstrak/Sinopsis</th>
                    <td>
                        @if($disabilitas->abstrak)
                            <div style="white-space: pre-line; max-height: 300px; overflow-y: auto;">
                                {{ $disabilitas->abstrak }}
                            </div>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Kata Kunci</th>
                    <td>
                        @if($disabilitas->kata_kunci)
                            @php
                                $keywords = explode(',', $disabilitas->kata_kunci);
                            @endphp
                            @foreach($keywords as $keyword)
                                <span class="badge badge-secondary mr-1 mb-1">
                                    {{ trim($keyword) }}
                                </span>
                            @endforeach
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Jumlah Halaman</th>
                    <td>{{ $disabilitas->jumlah_halaman ? $disabilitas->jumlah_halaman . ' halaman' : '—' }}</td>
                </tr>
                <tr>
                    <th>Bahasa</th>
                    <td>
                        @php
                            $bahasaLabels = [
                                'indonesia' => 'Indonesia',
                                'inggris' => 'Inggris',
                                'daerah' => 'Daerah',
                                'lainnya' => 'Lainnya'
                            ];
                        @endphp
                        {{ $bahasaLabels[$disabilitas->bahasa] ?? ucfirst($disabilitas->bahasa) ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Status Publikasi" />
                    </td>
                </tr>
                <tr>
                    <th>Status Publikasi</th>
                    <td>
                        @php
                            $publikasiColors = [
                                'draft' => 'secondary',
                                'uploaded' => 'info',
                                'reviewed' => 'warning',
                                'published' => 'success',
                                'pending' => 'warning',
                                'deleted' => 'danger'
                            ];
                            
                            $publikasiLabels = [
                                'draft' => 'Draft',
                                'uploaded' => 'Telah Diunggah',
                                'reviewed' => 'Telah Direview',
                                'published' => 'Dipublikasikan',
                                'pending' => 'Ditunda',
                                'deleted' => 'Dihapus'
                            ];
                            
                            $publikasi = $disabilitas->status_publikasi ?? '';
                            $publikasiColor = $publikasiColors[$publikasi] ?? 'secondary';
                            $publikasiLabel = $publikasiLabels[$publikasi] ?? ucfirst(str_replace('_', ' ', $publikasi));
                        @endphp
                        <span class="badge badge-{{ $publikasiColor }}">
                            {{ $publikasiLabel ?: '—' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Hak Akses</th>
                    <td>
                        @php
                            $hakAksesLabels = [
                                'public' => 'Publik (Semua Orang)',
                                'restricted' => 'Terbatas (Login)',
                                'admin' => 'Admin Only',
                                'internal' => 'Internal Only'
                            ];
                        @endphp
                        {{ $hakAksesLabels[$disabilitas->hak_akses] ?? ucfirst($disabilitas->hak_akses) ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <th>Keterangan Tambahan</th>
                    <td>
                        @if($disabilitas->keterangan)
                            <div style="white-space: pre-line;">
                                {{ $disabilitas->keterangan }}
                            </div>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tab 2: Klasifikasi Disabilitas -->
    <div class="tab-pane tab-item" data-tab="dataKlasifikasi" id="tab_2">
        <div class="box-header">
            <table class="table table-striped table-bordered detail-view">
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Klasifikasi Disabilitas" />
                    </td>
                </tr>
                <tr>
                    <th>Jenis Disabilitas</th>
                    <td>
                        @php
                            $jenisLabels = [
                                'fisik' => 'Disabilitas Fisik',
                                'intelektual' => 'Disabilitas Intelektual',
                                'mental' => 'Disabilitas Mental',
                                'sensorik' => 'Disabilitas Sensorik',
                                'ganda' => 'Disabilitas Ganda/Majemuk',
                                'lainnya' => 'Lainnya'
                            ];
                            
                            $jenisDisabilitas = [];
                            if ($disabilitas->jenis_disabilitas) {
                                $jenisDisabilitas = json_decode($disabilitas->jenis_disabilitas, true) ?: [];
                            }
                        @endphp
                        
                        @if(!empty($jenisDisabilitas))
                            @foreach($jenisDisabilitas as $jenis)
                                <span class="badge badge-primary mr-1 mb-1">
                                    {{ $jenisLabels[$jenis] ?? ucfirst($jenis) }}
                                </span>
                            @endforeach
                        @else
                            —
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Ruang Lingkup</th>
                    <td>
                        @php
                            $ruangLingkupLabels = [
                                'nasional' => 'Nasional',
                                'provinsi' => 'Provinsi',
                                'kabupaten_kota' => 'Kabupaten/Kota',
                                'internasional' => 'Internasional'
                            ];
                        @endphp
                        {{ $ruangLingkupLabels[$disabilitas->ruang_lingkup] ?? ucfirst($disabilitas->ruang_lingkup) ?? '—' }}
                    </td>
                </tr>
                <tr>
                    <th>Sektor Kebijakan</th>
                    <td>
                        @php
                            $sektorLabels = [
                                'pendidikan' => 'Pendidikan',
                                'kesehatan' => 'Kesehatan',
                                'ketenagakerjaan' => 'Ketenagakerjaan',
                                'sosial' => 'Sosial',
                                'aksesibilitas' => 'Aksesibilitas',
                                'hukum' => 'Hukum & HAM',
                                'politik' => 'Politik',
                                'lainnya' => 'Lainnya'
                            ];
                            
                            $sektorKebijakan = [];
                            if ($disabilitas->sektor_kebijakan) {
                                $sektorKebijakan = json_decode($disabilitas->sektor_kebijakan, true) ?: [];
                            }
                        @endphp
                        
                        @if(!empty($sektorKebijakan))
                            <div class="row">
                                @foreach($sektorKebijakan as $sektor)
                                    <div class="col-md-6">
                                        <span class="badge badge-info mr-1 mb-1">
                                            <i class="fa fa-check"></i> {{ $sektorLabels[$sektor] ?? ucfirst($sektor) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tab 3: Dokumen -->
    <div class="tab-pane tab-item" data-tab="dataDokumen" id="tab_3">
        <div class="box-header">
            <table class="table table-striped table-bordered detail-view">
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Dokumen Utama" />
                    </td>
                </tr>
                <tr>
                    <th>Dokumen Utama</th>
                    <td>
                        @if($disabilitas->dokumen_utama)
                            <div class="d-flex align-items-center">
                                <a href="{{ Storage::url($disabilitas->dokumen_utama) }}" 
                                   target="_blank" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> Lihat Dokumen
                                </a>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fa fa-file-pdf-o"></i> Format: PDF
                            </small>
                        @else
                            —
                        @endif
                    </td>
                </tr>
                
                @if($disabilitas->cover)
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Cover/Thumbnail" />
                    </td>
                </tr>
                <tr>
                    <th>Cover</th>
                    <td>
                        <div class="d-flex flex-column">
                            <img src="{{ Storage::url($disabilitas->cover) }}" 
                                 alt="Cover" 
                                 style="max-width: 200px; max-height: 200px;" 
                                 class="img-thumbnail mb-2">
                            <div>
                                <a href="{{ Storage::url($disabilitas->cover) }}" 
                                   target="_blank" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-eye"></i> Lihat Gambar
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                @endif
                
                @if($disabilitas->lampiran)
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Lampiran" />
                    </td>
                </tr>
                <tr>
                    <th>Lampiran</th>
                    <td>
                        <div class="d-flex align-items-center">
                            <a href="{{ Storage::url($disabilitas->lampiran) }}" 
                               target="_blank" 
                               class="btn btn-primary btn-sm">
                                <i class="fa fa-eye"></i> Lihat Lampiran
                            </a>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            @php
                                $extension = pathinfo($disabilitas->lampiran, PATHINFO_EXTENSION);
                                $extLabels = [
                                    'pdf' => 'PDF',
                                    'doc' => 'DOC',
                                    'docx' => 'DOCX'
                                ];
                            @endphp
                            <i class="fa fa-file"></i> Format: {{ $extLabels[$extension] ?? strtoupper($extension) }}
                        </small>
                    </td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- Tab 4: Data Pengunggah -->
    <div class="tab-pane tab-item" data-tab="dataPengunggah" id="tab_4">
        <div class="box-header">
            <table class="table table-striped table-bordered detail-view">
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Data Pengunggah" />
                    </td>
                </tr>
                <tr>
                    <th>Pengunggah</th>
                    <td>{{ $disabilitas->pengunggah ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Tanggal Unggah</th>
                    <td>
                        @if($disabilitas->tanggal_unggah_formatted)
                        <dt>Tanggal Unggah</dt>
                        <dd>{{ $disabilitas->tanggal_unggah_formatted }}</dd>
                          @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <x-backend.line-with-title title="Data Sistem" />
                    </td>
                </tr>
                <tr>
                    <th>Dibuat Pada</th>
                    <td>
                        {{ \Carbon\Carbon::parse($disabilitas->created_at)->translatedFormat('d F Y, H:i:s') }}
                    </td>
                </tr>
                <tr>
                    <th>Diperbarui Pada</th>
                    <td>
                        {{ \Carbon\Carbon::parse($disabilitas->updated_at)->translatedFormat('d F Y, H:i:s') }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

</x-backend.section-document-view>

@push('styles')
<style>
    .badge {
        font-size: 0.85em;
        padding: 5px 10px;
    }
    .img-thumbnail {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 5px;
        background-color: #fff;
    }
    .tab-item.active {
        background-color: #f8f9fa;
    }
    .detail-view th {
        width: 30%;
        background-color: #f8f9fa;
    }
    .detail-view td {
        width: 70%;
    }
    .pre-line {
        white-space: pre-line;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set tab aktif berdasarkan localStorage
        const tabActive = localStorage.getItem('tabActive') || 'dataUtama';
        const tabElement = document.querySelector(`[data-tab="${tabActive}"]`);
        
        if (tabElement) {
            // Aktifkan tab
            const tabLink = tabElement.querySelector('a');
            if (tabLink) {
                $(tabLink).tab('show');
            }
        }
        
        // Simpan tab aktif ketika diklik
        document.querySelectorAll('.tab-item').forEach(function(tab) {
            tab.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');
                localStorage.setItem('tabActive', tabName);
            });
        });
    });
</script>
@endpush