{{-- resources/views/backend/disabilitas/edit.blade.php --}}
@php
    $isCreate = false;
    $title = 'Edit Data Disabilitas';
@endphp

<x-layouts.backend
    :title="$title"
    :listNav="[['label' => 'Disabilitas', 'route' => route('backend.disabilitas.index')], ['label' => $title]]">

    <div class="box-body no-padding">
        <div class="section">
            <form
                class="form-horizontal"
                action="{{ route('backend.disabilitas.update', $disabilitas->id) }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Informasi Dasar Dokumen Disabilitas -->
                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <b><i class="fa fa-info-circle"></i> Informasi Dasar Dokumen</b>
                    </div>
                    <div class="box-body">
                        {{-- Jenis Dokumen Disabilitas --}}
                        <x-backend.input.select
                            label="Jenis Dokumen"
                            key="jenis_dokumen"
                            placeholder="--Pilih jenis dokumen--"
                            required
                            :value="old('jenis_dokumen', $disabilitas->jenis_dokumen)"
                            :data="[
                                ['label' => 'Undang-Undang', 'value' => 'uu'],
                                ['label' => 'Peraturan Pemerintah', 'value' => 'pp'],
                                ['label' => 'Peraturan Presiden', 'value' => 'perpres'],
                                ['label' => 'Peraturan Menteri', 'value' => 'permen'],
                                ['label' => 'Peraturan Daerah', 'value' => 'perda'],
                                ['label' => 'Keputusan Presiden', 'value' => 'keppres'],
                                ['label' => 'Keputusan Menteri', 'value' => 'kepmen'],
                                ['label' => 'Surat Edaran', 'value' => 'se'],
                                ['label' => 'Petunjuk Teknis', 'value' => 'juknis'],
                                ['label' => 'Panduan', 'value' => 'panduan'],
                                ['label' => 'Laporan', 'value' => 'laporan'],
                                ['label' => 'Studi/Kajian', 'value' => 'kajian'],
                                ['label' => 'Naskah Akademik', 'value' => 'naskah_akademik'],
                                ['label' => 'Rancangan Peraturan', 'value' => 'rancangan'],
                                ['label' => 'Lainnya', 'value' => 'lainnya'],
                            ]" />

                        {{-- Judul Dokumen --}}
                        <x-backend.input.textarea
                            label="Judul Dokumen"
                            key="judul"
                            placeholder="Tulis lengkap judul dokumen disabilitas"
                            required
                            rows="2"
                            :value="old('judul', $disabilitas->judul)" />

                        {{-- Nomor Dokumen --}}
                        <x-backend.input.text
                            label="Nomor Dokumen"
                            key="nomor_dokumen"
                            placeholder="Contoh: 8 Tahun 2016, 52 Tahun 2019"
                            required
                            :value="old('nomor_dokumen', $disabilitas->nomor_dokumen)" />

                        {{-- Tahun Dokumen --}}
                        <x-backend.input.text
                            label="Tahun"
                            key="tahun"
                            placeholder="Tahun dokumen"
                            required
                            type="number"
                            min="1900"
                            max="{{ date('Y') + 5 }}"
                            :value="old('tahun', $disabilitas->tahun)" />

                        {{-- Tempat Penetapan --}}
                        <x-backend.input.text
                            label="Tempat Penetapan"
                            key="tempat_penetapan"
                            placeholder="Contoh: Jakarta"
                            :value="old('tempat_penetapan', $disabilitas->tempat_penetapan ?? '')" />

                        {{-- TANGGAL PENETAPAN - PERBAIKAN: Ganti dengan datepicker --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="tanggal_penetapan">
                                Tanggal Penetapan
                            </label>
                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"
                                           class="form-control datepicker"
                                           id="tanggal_penetapan"
                                           name="tanggal_penetapan"
                                           value="{{ old('tanggal_penetapan', $disabilitas->tanggal_penetapan_formatted ?? '') }}"
                                           placeholder="Tanggal ditetapkan (d/m/yyyy)"
                                           autocomplete="off">
                                </div>
                                <small class="text-muted">Format: d/m/yyyy (contoh: 13/2/2020)</small>
                                @error('tanggal_penetapan')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Lembaga Penetap --}}
                        <x-backend.input.text
                            label="Lembaga Penetap"
                            key="lembaga_penetap"
                            placeholder="Contoh: Presiden RI, DPR RI, Kementerian Sosial"
                            required
                            :value="old('lembaga_penetap', $disabilitas->lembaga_penetap)" />

                        {{-- Status Dokumen --}}
                        <x-backend.input.select
                            label="Status Dokumen"
                            key="status_dokumen"
                            placeholder="--Pilih status--"
                            required
                            :value="old('status_dokumen', $disabilitas->status_dokumen)"
                            :data="[
                                ['label' => 'Berlaku', 'value' => 'berlaku'],
                                ['label' => 'Tidak Berlaku', 'value' => 'tidak_berlaku'],
                                ['label' => 'Mencabut', 'value' => 'mencabut'],
                                ['label' => 'Diubah', 'value' => 'diubah'],
                                ['label' => 'Dicabut', 'value' => 'dicabut'],
                                ['label' => 'Draft', 'value' => 'draft'],
                            ]" />

                        {{-- Dokumen yang Dicabut/Diamandemen --}}
                        <x-backend.input.text
                            label="Terkait dengan Dokumen"
                            key="dokumen_terkait"
                            placeholder="Nomor dokumen yang dicabut/diamandemen"
                            :value="old('dokumen_terkait', $disabilitas->dokumen_terkait ?? '')" />
                    </div>
                </div>

                <!-- Klasifikasi Disabilitas -->
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <b><i class="fa fa-wheelchair"></i> Klasifikasi Disabilitas</b>
                    </div>
                    <div class="box-body">
                        {{-- Jenis Disabilitas --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Jenis Disabilitas</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    @php
                                        $jenisDisabilitas = old('jenis_disabilitas', 
                                            isset($disabilitas->jenis_disabilitas) ? 
                                                json_decode($disabilitas->jenis_disabilitas, true) : []
                                        );
                                    @endphp
                                    <label>
                                        <input type="checkbox" name="jenis_disabilitas[]" value="fisik" 
                                            {{ in_array('fisik', $jenisDisabilitas) ? 'checked' : '' }}>
                                        Disabilitas Fisik
                                    </label>
                                    <label style="margin-left: 20px;">
                                        <input type="checkbox" name="jenis_disabilitas[]" value="intelektual" 
                                            {{ in_array('intelektual', $jenisDisabilitas) ? 'checked' : '' }}>
                                        Disabilitas Intelektual
                                    </label>
                                    <label style="margin-left: 20px;">
                                        <input type="checkbox" name="jenis_disabilitas[]" value="mental" 
                                            {{ in_array('mental', $jenisDisabilitas) ? 'checked' : '' }}>
                                        Disabilitas Mental
                                    </label>
                                    <label style="margin-left: 20px;">
                                        <input type="checkbox" name="jenis_disabilitas[]" value="sensorik" 
                                            {{ in_array('sensorik', $jenisDisabilitas) ? 'checked' : '' }}>
                                        Disabilitas Sensorik
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="jenis_disabilitas[]" value="ganda" 
                                            {{ in_array('ganda', $jenisDisabilitas) ? 'checked' : '' }}>
                                        Disabilitas Ganda/Majemuk
                                    </label>
                                    <label style="margin-left: 20px;">
                                        <input type="checkbox" name="jenis_disabilitas[]" value="lainnya" 
                                            {{ in_array('lainnya', $jenisDisabilitas) ? 'checked' : '' }}>
                                        Lainnya
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Ruang Lingkup --}}
                        <x-backend.input.select
                            label="Ruang Lingkup"
                            key="ruang_lingkup"
                            placeholder="--Pilih ruang lingkup--"
                            required
                            :value="old('ruang_lingkup', $disabilitas->ruang_lingkup)"
                            :data="[
                                ['label' => 'Nasional', 'value' => 'nasional'],
                                ['label' => 'Provinsi', 'value' => 'provinsi'],
                                ['label' => 'Kabupaten/Kota', 'value' => 'kabupaten_kota'],
                                ['label' => 'Internasional', 'value' => 'internasional'],
                            ]" />

                        {{-- Sektor/Kebijakan --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Sektor Kebijakan</label>
                            <div class="col-sm-10">
                                @php
                                    $sektor = old('sektor_kebijakan', 
                                        isset($disabilitas->sektor_kebijakan) ? 
                                            json_decode($disabilitas->sektor_kebijakan, true) : []
                                    );
                                @endphp
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="pendidikan" 
                                                {{ in_array('pendidikan', $sektor) ? 'checked' : '' }}>
                                            Pendidikan
                                        </label><br>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="kesehatan" 
                                                {{ in_array('kesehatan', $sektor) ? 'checked' : '' }}>
                                            Kesehatan
                                        </label><br>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="ketenagakerjaan" 
                                                {{ in_array('ketenagakerjaan', $sektor) ? 'checked' : '' }}>
                                            Ketenagakerjaan
                                        </label><br>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="sosial" 
                                                {{ in_array('sosial', $sektor) ? 'checked' : '' }}>
                                            Sosial
                                        </label>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="aksesibilitas" 
                                                {{ in_array('aksesibilitas', $sektor) ? 'checked' : '' }}>
                                            Aksesibilitas
                                        </label><br>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="hukum" 
                                                {{ in_array('hukum', $sektor) ? 'checked' : '' }}>
                                            Hukum & HAM
                                        </label><br>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="politik" 
                                                {{ in_array('politik', $sektor) ? 'checked' : '' }}>
                                            Politik
                                        </label><br>
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="sektor_kebijakan[]" value="lainnya" 
                                                {{ in_array('lainnya', $sektor) ? 'checked' : '' }}>
                                            Lainnya
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Konten dan Dokumen -->
                <div class="box box-warning box-solid">
                    <div class="box-header with-border">
                        <b><i class="fa fa-file-text"></i> Konten dan Dokumen</b>
                    </div>
                    <div class="box-body">
                        {{-- Abstrak/Sinopsis --}}
                        <x-backend.input.textarea
                            label="Abstrak/Sinopsis"
                            key="abstrak"
                            rows="4"
                            placeholder="Ringkasan isi dokumen (maksimal 500 kata)"
                            :value="old('abstrak', $disabilitas->abstrak ?? '')" />

                        {{-- Kata Kunci --}}
                        <x-backend.input.text
                            label="Kata Kunci"
                            key="kata_kunci"
                            placeholder="Pisahkan dengan koma, contoh: disabilitas, inklusi, aksesibilitas, hak"
                            :value="old('kata_kunci', $disabilitas->kata_kunci ?? '')" />

                        {{-- Jumlah Halaman --}}
                        <x-backend.input.text
                            label="Jumlah Halaman"
                            key="jumlah_halaman"
                            placeholder="Jumlah halaman dokumen"
                            type="number"
                            min="1"
                            :value="old('jumlah_halaman', $disabilitas->jumlah_halaman ?? '')" />

                        {{-- Bahasa --}}
                        <x-backend.input.select
                            label="Bahasa"
                            key="bahasa"
                            placeholder="--Pilih bahasa--"
                            :value="old('bahasa', $disabilitas->bahasa ?? '')"
                            :data="[
                                ['label' => 'Indonesia', 'value' => 'indonesia'],
                                ['label' => 'Inggris', 'value' => 'inggris'],
                                ['label' => 'Daerah', 'value' => 'daerah'],
                                ['label' => 'Lainnya', 'value' => 'lainnya'],
                            ]" />

                        {{-- Dokumen Utama --}}
                        <x-backend.input.file-small
                            label="Dokumen Utama (PDF)"
                            key="dokumen_utama"
                            :value="$disabilitas->dokumen_utama ?? ''"
                            :mimes="['pdf']"
                            :required="false" />

                        @if($disabilitas->dokumen_utama)
                            <div class="form-group">
                                <label class="col-sm-2 control-label">File Saat Ini</label>
                                <div class="col-sm-10">
                                    <div class="alert alert-info">
                                        <i class="fa fa-file-pdf-o"></i> 
                                        <a href="{{ Storage::url($disabilitas->dokumen_utama) }}" target="_blank">
                                            Lihat Dokumen
                                        </a>
                                        <br>
                                        <small>Centang di bawah untuk menghapus file saat ini</small>
                                    </div>
                                    <label class="checkbox">
                                        <input type="checkbox" name="hapus_dokumen_utama" value="1">
                                        Hapus file saat ini dan upload file baru
                                    </label>
                                </div>
                            </div>
                        @endif

                        {{-- Cover/Thumbnail --}}
                        <x-backend.input.file-small
                            label="Cover/Thumbnail (Gambar)"
                            key="cover"
                            :value="$disabilitas->cover ?? ''"
                            :mimes="['jpg', 'jpeg', 'png', 'gif']" />

                        @if($disabilitas->cover)
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cover Saat Ini</label>
                                <div class="col-sm-10">
                                    <img src="{{ Storage::url($disabilitas->cover) }}" 
                                         alt="Cover" 
                                         style="max-width: 150px; max-height: 150px;" 
                                         class="img-thumbnail">
                                    <br>
                                    <label class="checkbox">
                                        <input type="checkbox" name="hapus_cover" value="1">
                                        Hapus cover saat ini
                                    </label>
                                </div>
                            </div>
                        @endif

                        {{-- Lampiran (opsional) --}}
                        <x-backend.input.file-small
                            label="Lampiran (PDF/Doc)"
                            key="lampiran"
                            :value="$disabilitas->lampiran ?? ''"
                            :mimes="['pdf', 'doc', 'docx']" />

                        @if($disabilitas->lampiran)
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Lampiran Saat Ini</label>
                                <div class="col-sm-10">
                                    <div class="alert alert-info">
                                        <i class="fa fa-file"></i> 
                                        <a href="{{ Storage::url($disabilitas->lampiran) }}" target="_blank">
                                            Lihat Lampiran
                                        </a>
                                        <br>
                                        <small>Centang di bawah untuk menghapus lampiran saat ini</small>
                                    </div>
                                    <label class="checkbox">
                                        <input type="checkbox" name="hapus_lampiran" value="1">
                                        Hapus lampiran saat ini
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hak Akses dan Status -->
                <div class="box box-danger box-solid">
                    <div class="box-header with-border">
                        <b><i class="fa fa-lock"></i> Hak Akses dan Status</b>
                    </div>
                    <div class="box-body">
                        {{-- Status Publikasi --}}
                        <x-backend.input.select
                            label="Status Publikasi"
                            key="status_publikasi"
                            placeholder="--Pilih status publikasi--"
                            required
                            :value="old('status_publikasi', $disabilitas->status_publikasi)"
                            :data="[
                                ['label' => 'Draft', 'value' => 'draft'],
                                ['label' => 'Telah Diunggah', 'value' => 'uploaded'],
                                ['label' => 'Telah Direview', 'value' => 'reviewed'],
                                ['label' => 'Dipublikasikan', 'value' => 'published'],
                                ['label' => 'Ditunda', 'value' => 'pending'],
                                ['label' => 'Dihapus', 'value' => 'deleted'],
                            ]" />

                        {{-- Hak Akses --}}
                        <x-backend.input.select
                            label="Hak Akses"
                            key="hak_akses"
                            placeholder="--Pilih hak akses--"
                            required
                            :value="old('hak_akses', $disabilitas->hak_akses)"
                            :data="[
                                ['label' => 'Publik (Semua Orang)', 'value' => 'public'],
                                ['label' => 'Terbatas (Login)', 'value' => 'restricted'],
                                ['label' => 'Admin Only', 'value' => 'admin'],
                                ['label' => 'Internal Only', 'value' => 'internal'],
                            ]" />

                        {{-- TANGGAL UNGGAH - TAMBAH DEBUGGING --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="tanggal_unggah">
                                Tanggal Unggah <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-10">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text"
                                           class="form-control datepicker"
                                           id="tanggal_unggah"
                                           name="tanggal_unggah"
                                           value="{{ old('tanggal_unggah', $disabilitas->tanggal_unggah_formatted ?? \Carbon\Carbon::now()->format('j/n/Y')) }}"
                                           placeholder="Pilih tanggal unggah"
                                           required
                                           autocomplete="off">
                                </div>
                                
                                
                                @error('tanggal_unggah')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Keterangan Tambahan --}}
                        <x-backend.input.textarea
                            label="Keterangan Tambahan"
                            key="keterangan"
                            rows="3"
                            placeholder="Catatan internal atau keterangan lainnya"
                            :value="old('keterangan', $disabilitas->keterangan ?? '')" />

                        {{-- Pengunggah --}}
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Pengunggah</label>
                            <div class="col-sm-10">
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $disabilitas->pengunggah ?? auth()->user()->username ?? 'admin' }}"
                                       readonly>
                                <small class="text-muted">Tidak dapat diubah</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="box-footer">
                    <button
                        type="submit"
                        class="btn btn-success btn-flat">
                        <i class="fa fa-save"></i> Perbarui Data
                    </button>
                    <a
                        href="{{ route('backend.disabilitas.index') }}"
                        class="btn btn-danger btn-flat">
                        <i class="fa fa-times"></i> Batal
                    </a>
                    <button
                        type="reset"
                        class="btn btn-default btn-flat">
                        <i class="fa fa-refresh"></i> Reset Form
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('script')
        <script>
            $(document).ready(function() {
                console.log('Document ready - Inisialisasi form edit');
                
                // Initialize datepicker dengan format d/m/Y
                $('.datepicker').datepicker({
                    format: 'd/m/yyyy', // format: 13/2/2020
                    autoclose: true,
                    todayHighlight: true,
                    todayBtn: "linked",
                    clearBtn: true,
                    orientation: "auto",
                    language: 'id',
                    weekStart: 1,
                    daysOfWeekHighlighted: "0,6"
                });
                
                // Pastikan icon kalender juga berfungsi
                $('.input-group-addon').on('click', function() {
                    $(this).closest('.input-group').find('input').datepicker('show');
                });

                // Format kata kunci
                $('#kata_kunci').on('blur', function() {
                    let value = $(this).val();
                    if (value) {
                        // Hapus spasi berlebihan dan format
                        let keywords = value.split(',')
                            .map(k => k.trim())
                            .filter(k => k !== '')
                            .join(', ');
                        $(this).val(keywords);
                    }
                });

                // Toggle required untuk file upload jika ada checkbox hapus
                $('input[type="checkbox"][name^="hapus_"]').change(function() {
                    const inputName = this.name.replace('hapus_', '');
                    const fileInput = $('input[type="file"][name="' + inputName + '"]');
                    
                    if (this.checked) {
                        fileInput.attr('required', true);
                    } else {
                        fileInput.removeAttr('required');
                    }
                });
                
                // Validasi form sebelum submit
                $('form').submit(function(e) {
                    console.log('Form edit disubmit');
                    
                    // Debug: log nilai tanggal
                    console.log('Tanggal unggah:', $('#tanggal_unggah').val());
                    console.log('Tanggal penetapan:', $('#tanggal_penetapan').val());
                    
                    // Validasi format tanggal
                    const tanggalUnggah = $('#tanggal_unggah').val();
                    const tanggalPenetapan = $('#tanggal_penetapan').val();
                    
                    // Validasi format tanggal unggah (d/m/Y)
                    if (tanggalUnggah) {
                        const dateRegex = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                        if (!dateRegex.test(tanggalUnggah)) {
                            e.preventDefault();
                            alert('Format tanggal unggah harus d/m/yyyy (contoh: 13/2/2020)!');
                            $('#tanggal_unggah').focus();
                            return false;
                        }
                    }
                    
                    // Validasi format tanggal penetapan jika ada
                    if (tanggalPenetapan) {
                        const dateRegex = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
                        if (!dateRegex.test(tanggalPenetapan)) {
                            e.preventDefault();
                            alert('Format tanggal penetapan harus d/m/yyyy (contoh: 13/2/2020)!');
                            $('#tanggal_penetapan').focus();
                            return false;
                        }
                    }
                    
                    return true;
                });
                
                // Debug: log nilai awal
                console.log('Tanggal unggah awal:', $('#tanggal_unggah').val());
                console.log('Tanggal penetapan awal:', $('#tanggal_penetapan').val());
            });
        </script>
        
        <style>
            .box-header {
                background-color: #3c8dbc !important;
                color: white;
            }
            .box-success .box-header {
                background-color: #00a65a !important;
            }
            .box-warning .box-header {
                background-color: #f39c12 !important;
            }
            .box-info .box-header {
                background-color: #00c0ef !important;
            }
            .box-danger .box-header {
                background-color: #dd4b39 !important;
            }
            .checkbox label, .checkbox-inline {
                margin-right: 15px;
                font-weight: normal;
            }
            .form-group {
                margin-bottom: 20px;
            }
            input[readonly], input[readonly]:focus {
                background-color: #f5f5f5;
                cursor: not-allowed;
                border-color: #d2d6de;
            }
            /* Datepicker styles */
            .datepicker {
                border-radius: 4px;
                border: 1px solid #d2d6de;
            }
            .datepicker-dropdown {
                padding: 10px;
                box-shadow: 0 6px 12px rgba(0,0,0,.175);
            }
            .datepicker table tr td.today {
                background-color: #ffdb99;
                border-color: #ffb733;
            }
            .datepicker table tr td.active,
            .datepicker table tr td.active:hover {
                background-color: #337ab7;
                background-image: none;
                border-color: #2e6da4;
            }
            .input-group-addon {
                background-color: #eee;
                border: 1px solid #d2d6de;
                border-right: none;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            .input-group-addon:hover {
                background-color: #ddd;
            }
            .input-group .form-control {
                border-left: none;
            }
            /* Debug info */
            .text-muted small code {
                background-color: #f8f9fa;
                padding: 2px 4px;
                border-radius: 3px;
                font-family: monospace;
            }
            .img-thumbnail {
                margin-top: 10px;
                border: 1px solid #ddd;
                padding: 5px;
                max-width: 150px;
                max-height: 150px;
            }
        </style>
    @endpush
</x-layouts.backend>