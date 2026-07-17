@php
    $isCreate = request()->routeIs('backend.pembentukan-puu.create') ? true : false;
    $title = ($isCreate ? 'Tambah' : 'Edit') . ' Data Pembentukan PUU';
@endphp

<x-layouts.backend
    :title="$title"
    :listNav="[['label' => 'Pembentukan PUU', 'route' => route('backend.pembentukan-puu.index')], ['label' => $title]]">

    <div class="box-body no-padding">
        <div class="section">
            <form
                class="form-horizontal"
                action="{{ $isCreate ? route('backend.pembentukan-puu.store') : route('backend.pembentukan-puu.update', $puu->id) }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf
                @if (!$isCreate)
                    @method('PUT')
                @endif

                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <b>Informasi Dasar Dokumen</b>
                    </div>
                    <div class="box-body">
                        {{-- Jenis Dokumen PUU --}}
                        <x-backend.input.select
                            label="Jenis Dokumen PUU"
                            key="jenis_dokumen"
                            placeholder="--Pilih Jenis Dokumen--"
                            required
                            :value="$puu->jenis_dokumen ?? ''"
                            :data="[
                                ['label' => 'Naskah Akademik', 'value' => 'naskah_akademik'],
                                ['label' => 'Rancangan PUU', 'value' => 'rancangan_puu'],
                                ['label' => 'Penelitian Hukum', 'value' => 'penelitian_hukum'],
                                ['label' => 'Pengkajian Hukum', 'value' => 'pengkajian_hukum'],
                                ['label' => 'Pengkajian Konstitusi', 'value' => 'pengkajian_konstitusi'],
                                ['label' => 'Analisis Evaluasi', 'value' => 'analisis_evaluasi'],
                            ]" />

                        {{-- Judul Dokumen --}}
                        <x-backend.input.textarea
                            label="Judul Dokumen"
                            key="judul"
                            placeholder="Tulis lengkap judul dokumen"
                            required
                            :value="$puu->judul ?? ''" />

                        {{-- Nomor Dokumen --}}
                        <x-backend.input.text
                            label="Nomor Dokumen"
                            key="nomor_dokumen"
                            placeholder="Tulis nomor dokumen"
                            :value="$puu->nomor_dokumen ?? ''" />

                        {{-- Tahun --}}
                        <x-backend.input.text
                            label="Tahun"
                            key="tahun"
                            placeholder="Tahun dokumen"
                            required
                            type="number"
                            min="1900"
                            max="{{ date('Y') + 5 }}"
                            :value="$puu->tahun ?? date('Y')" />

                        {{-- Lembaga Pemrakarsa --}}
                        <x-backend.input.text
                            label="Lembaga Pemrakarsa"
                            key="lembaga_pemrakarsa"
                            placeholder="Nama lembaga pemrakarsa"
                            required
                            :value="$puu->lembaga_pemrakarsa ?? ''" />

                        {{-- Status Dokumen --}}
                        <x-backend.input.select
                            label="Status Dokumen"
                            key="status_dokumen"
                            placeholder="--Pilih Status--"
                            required
                            :value="$puu->status_dokumen ?? ''"
                            :data="[
                                ['label' => 'Draft', 'value' => 'draft'],
                                ['label' => 'Final', 'value' => 'final'],
                                ['label' => 'Revisi', 'value' => 'revisi'],
                                ['label' => 'Disahkan', 'value' => 'disahkan'],
                            ]" />

                        {{-- Tahapan Pembentukan --}}
                        <x-backend.input.select
                            label="Tahapan Pembentukan"
                            key="tahapan_pembentukan"
                            placeholder="--Pilih Tahapan--"
                            :value="$puu->tahapan_pembentukan ?? ''"
                            :data="[
                                ['label' => 'Pra-Legislasi', 'value' => 'pra_legislasi'],
                                ['label' => 'Penyusunan RUU', 'value' => 'penyusunan_ruu'],
                                ['label' => 'Pembahasan DPR', 'value' => 'pembahasan_dpr'],
                                ['label' => 'Pengundangan', 'value' => 'pengundangan'],
                                ['label' => 'Evaluasi', 'value' => 'evaluasi'],
                            ]" />
                    </div>
                </div>

                {{-- FIELD SPESIFIK BERDASARKAN JENIS DOKUMEN --}}
                <div id="field-spesifik" style="display: none;">
                    {{-- Field untuk Naskah Akademik --}}
                    <div class="box box-info box-solid field-group" id="field-naskah_akademik" style="display: none;">
                        <div class="box-header with-border">
                            <b>Informasi Naskah Akademik</b>
                        </div>
                        <div class="box-body">
                            <x-backend.input.textarea
                                label="Rumusan Masalah"
                                key="rumusan_masalah"
                                placeholder="Rumusan masalah penelitian"
                                :value="$puu->rumusan_masalah ?? ''" />

                            <x-backend.input.textarea
                                label="Tujuan Penelitian"
                                key="tujuan_penelitian"
                                placeholder="Tujuan penelitian/penyusunan"
                                :value="$puu->tujuan_penelitian ?? ''" />

                            <x-backend.input.textarea
                                label="Metodologi Penelitian"
                                key="metodologi_penelitian"
                                placeholder="Metode penelitian yang digunakan"
                                :value="$puu->metodologi_penelitian ?? ''" />

                            <x-backend.input.text
                                label="Tim Penyusun"
                                key="tim_penyusun"
                                placeholder="Nama-nama tim penyusun"
                                :value="$puu->tim_penyusun ?? ''" />

                            <x-backend.input.date
                                label="Tanggal Penyelesaian"
                                key="tanggal_penyelesaian"
                                placeholder="Tanggal penyelesaian naskah"
                                :value="$puu->tanggal_penyelesaian ?? ''" />
                        </div>
                    </div>

                    {{-- Field untuk Rancangan PUU --}}
                    <div class="box box-info box-solid field-group" id="field-rancangan_puu" style="display: none;">
                        <div class="box-header with-border">
                            <b>Informasi Rancangan PUU</b>
                        </div>
                        <div class="box-body">
                            <x-backend.input.select
                                label="Jenis Rancangan"
                                key="jenis_rancangan"
                                placeholder="--Pilih Jenis Rancangan--"
                                :value="$puu->jenis_rancangan ?? ''"
                                :data="[
                                    ['label' => 'RUU', 'value' => 'ruu'],
                                    ['label' => 'RPerppu', 'value' => 'rperppu'],
                                    ['label' => 'RUU tentang Pencabutan', 'value' => 'ruu_pencabutan'],
                                    ['label' => 'Rancangan Peraturan Pemerintah', 'value' => 'rpp'],
                                ]" />

                            <x-backend.input.text
                                label="Program Legislasi Nasional (Prolegnas)"
                                key="prolegnas"
                                placeholder="Tahun Prolegnas"
                                type="number"
                                :value="$puu->prolegnas ?? ''" />

                            <x-backend.input.select
                                label="Inisiator"
                                key="inisiator"
                                placeholder="--Pilih Inisiator--"
                                :value="$puu->inisiator ?? ''"
                                :data="[
                                    ['label' => 'DPR', 'value' => 'dpr'],
                                    ['label' => 'Pemerintah', 'value' => 'pemerintah'],
                                    ['label' => 'DPD', 'value' => 'dpd'],
                                ]" />

                            <x-backend.input.text
                                label="Pansus/Panja"
                                key="pansus_panja"
                                placeholder="Nama Pansus/Panja"
                                :value="$puu->pansus_panja ?? ''" />

                            <x-backend.input.date
                                label="Tanggal Pengajuan"
                                key="tanggal_pengajuan"
                                placeholder="Tanggal pengajuan ke DPR"
                                :value="$puu->tanggal_pengajuan ?? ''" />
                        </div>
                    </div>

                    {{-- Field untuk Penelitian Hukum --}}
                    <div class="box box-info box-solid field-group" id="field-penelitian_hukum" style="display: none;">
                        <div class="box-header with-border">
                            <b>Informasi Penelitian Hukum</b>
                        </div>
                        <div class="box-body">
                            <x-backend.input.textarea
                                label="Latar Belakang Penelitian"
                                key="latar_belakang"
                                placeholder="Latar belakang penelitian hukum"
                                :value="$puu->latar_belakang ?? ''" />

                            <x-backend.input.textarea
                                label="Fokus Penelitian"
                                key="fokus_penelitian"
                                placeholder="Fokus atau lingkup penelitian"
                                :value="$puu->fokus_penelitian ?? ''" />

                            <x-backend.input.textarea
                                label="Hasil Penelitian"
                                key="hasil_penelitian"
                                placeholder="Temuan hasil penelitian"
                                :value="$puu->hasil_penelitian ?? ''" />

                            <x-backend.input.textarea
                                label="Rekomendasi"
                                key="rekomendasi"
                                placeholder="Rekomendasi hasil penelitian"
                                :value="$puu->rekomendasi ?? ''" />

                            <x-backend.input.text
                                label="Lokasi Penelitian"
                                key="lokasi_penelitian"
                                placeholder="Lokasi/tempat penelitian"
                                :value="$puu->lokasi_penelitian ?? ''" />
                        </div>
                    </div>

                    {{-- Field untuk Pengkajian Hukum --}}
                    <div class="box box-info box-solid field-group" id="field-pengkajian_hukum" style="display: none;">
                        <div class="box-header with-border">
                            <b>Informasi Pengkajian Hukum</b>
                        </div>
                        <div class="box-body">
                            <x-backend.input.textarea
                                label="Objek Pengkajian"
                                key="objek_pengkajian"
                                placeholder="Objek atau materi yang dikaji"
                                :value="$puu->objek_pengkajian ?? ''" />

                            <x-backend.input.select
                                label="Jenis Pengkajian"
                                key="jenis_pengkajian"
                                placeholder="--Pilih Jenis Pengkajian--"
                                :value="$puu->jenis_pengkajian ?? ''"
                                :data="[
                                    ['label' => 'Kajian Komparatif', 'value' => 'komparatif'],
                                    ['label' => 'Kajian Teoritis', 'value' => 'teoritis'],
                                    ['label' => 'Kajian Empiris', 'value' => 'empiris'],
                                    ['label' => 'Kajian Normatif', 'value' => 'normatif'],
                                ]" />

                            <x-backend.input.textarea
                                label="Tujuan Pengkajian"
                                key="tujuan_pengkajian"
                                placeholder="Tujuan pengkajian hukum"
                                :value="$puu->tujuan_pengkajian ?? ''" />

                            <x-backend.input.textarea
                                label="Kesimpulan Pengkajian"
                                key="kesimpulan_pengkajian"
                                placeholder="Kesimpulan hasil pengkajian"
                                :value="$puu->kesimpulan_pengkajian ?? ''" />

                            <x-backend.input.date
                                label="Tanggal Pengkajian"
                                key="tanggal_pengkajian"
                                placeholder="Tanggal pelaksanaan pengkajian"
                                :value="$puu->tanggal_pengkajian ?? ''" />
                        </div>
                    </div>

                    {{-- Field untuk Pengkajian Konstitusi --}}
                    <div class="box box-info box-solid field-group" id="field-pengkajian_konstitusi" style="display: none;">
                        <div class="box-header with-border">
                            <b>Informasi Pengkajian Konstitusi</b>
                        </div>
                        <div class="box-body">
                            <x-backend.input.textarea
                                label="Aspek Konstitusi yang Dikaji"
                                key="aspek_konstitusi"
                                placeholder="Aspek konstitusi yang menjadi fokus"
                                :value="$puu->aspek_konstitusi ?? ''" />

                            <x-backend.input.select
                                label="Jenis Pengkajian Konstitusi"
                                key="jenis_pengkajian_konstitusi"
                                placeholder="--Pilih Jenis--"
                                :value="$puu->jenis_pengkajian_konstitusi ?? ''"
                                :data="[
                                    ['label' => 'Kajian Konstitusionalitas', 'value' => 'konstitusionalitas'],
                                    ['label' => 'Kajian Amandemen', 'value' => 'amandemen'],
                                    ['label' => 'Kajian Putusan MK', 'value' => 'putusan_mk'],
                                    ['label' => 'Kajian Prinsip Konstitusi', 'value' => 'prinsip_konstitusi'],
                                ]" />

                            <x-backend.input.textarea
                                labelImplode=""
                                label="Dasar Hukum Pengkajian"
                                key="dasar_hukum_pengkajian"
                                placeholder="Dasar hukum pelaksanaan pengkajian"
                                :value="$puu->dasar_hukum_pengkajian ?? ''" />

                            <x-backend.input.text
                                label="Instansi Pengkaji"
                                key="instansi_pengkaji"
                                placeholder="Instansi yang melakukan pengkajian"
                                :value="$puu->instansi_pengkaji ?? ''" />

                            <x-backend.input.textarea
                                label="Implikasi Konstitusional"
                                key="implikasi_konstitusional"
                                placeholder="Implikasi hasil pengkajian"
                                :value="$puu->implikasi_konstitusional ?? ''" />
                        </div>
                    </div>

                    {{-- Field untuk Analisis Evaluasi --}}
                    <div class="box box-info box-solid field-group" id="field-analisis_evaluasi" style="display: none;">
                        <div class="box-header with-border">
                            <b>Informasi Analisis Evaluasi</b>
                        </div>
                        <div class="box-body">
                            <x-backend.input.textarea
                                label="Objek Evaluasi"
                                key="objek_evaluasi"
                                placeholder="Peraturan/kebijakan yang dievaluasi"
                                :value="$puu->objek_evaluasi ?? ''" />

                            <x-backend.input.select
                                label="Metode Evaluasi"
                                key="metode_evaluasi"
                                placeholder="--Pilih Metode Evaluasi--"
                                :value="$puu->metode_evaluasi ?? ''"
                                :data="[
                                    ['label' => 'Evaluasi Efektivitas', 'value' => 'efektivitas'],
                                    ['label' => 'Evaluasi Efisiensi', 'value' => 'efisiensi'],
                                    ['label' => 'Evaluasi Dampak', 'value' => 'dampak'],
                                    ['label' => 'Evaluasi Implementasi', 'value' => 'implementasi'],
                                ]" />

                            <x-backend.input.textarea
                                label="Indikator Evaluasi"
                                key="indikator_evaluasi"
                                placeholder="Indikator yang digunakan"
                                :value="$puu->indikator_evaluasi ?? ''" />

                            <x-backend.input.textarea
                                label="Temuan Evaluasi"
                                key="temuan_evaluasi"
                                placeholder="Temuan hasil evaluasi"
                                :value="$puu->temuan_evaluasi ?? ''" />

                            <x-backend.input.textarea
                                label="Rekomendasi Perbaikan"
                                key="rekomendasi_perbaikan"
                                placeholder="Rekomendasi untuk perbaikan"
                                :value="$puu->rekomendasi_perbaikan ?? ''" />

                            <x-backend.input.date
                                label="Periode Evaluasi"
                                key="periode_evaluasi"
                                placeholder="Periode pelaksanaan evaluasi"
                                :value="$puu->periode_evaluasi ?? ''" />
                        </div>
                    </div>
                </div>

                {{-- INFORMASI UMUM (Tampil untuk semua jenis) --}}
                <div class="box box-success box-solid">
                    <div class="box-header with-border">
                        <b>Informasi Umum</b>
                    </div>
                    <div class="box-body">
                        {{-- Abstrak/Ringkasan --}}
                        <x-backend.input.textarea
                            label="Abstrak/Ringkasan"
                            key="abstrak"
                            placeholder="Ringkasan isi dokumen"
                            rows="3"
                            :value="$puu->abstrak ?? ''" />

                        {{-- Kata Kunci --}}
                        <x-backend.input.text
                            label="Kata Kunci"
                            key="kata_kunci"
                            placeholder="Pisahkan dengan koma"
                            :value="$puu->kata_kunci ?? ''" />

                        {{-- Penulis/Penyusun --}}
                        <x-backend.input.text
                            label="Penulis/Penyusun"
                            key="penulis"
                            placeholder="Nama penulis/penyusun"
                            :value="$puu->penulis ?? ''" />

                        {{-- Editor/Reviewer --}}
                        <x-backend.input.text
                            label="Editor/Reviewer"
                            key="editor"
                            placeholder="Nama editor/reviewer"
                            :value="$puu->editor ?? ''" />
                    </div>
                </div>

                {{-- UPLOAD DOKUMEN --}}
                <div class="box box-warning box-solid">
                    <div class="box-header with-border">
                        <b>Upload Dokumen</b>
                    </div>
                    <div class="box-body">
                        {{-- Dokumen Utama --}}
                        <x-backend.input.file
                            label="Dokumen Utama (PDF)"
                            key="dokumen_utama"
                            placeholder="Unggah file PDF"
                            required="{{ $isCreate }}"
                            :value="$puu->dokumen_utama ?? ''"
                            mime="application/pdf"
                            accept=".pdf" />

                        {{-- Cover --}}
                        <x-backend.input.file
                            label="Cover/Gambar (Opsional)"
                            key="cover"
                            placeholder="Unggah gambar cover"
                            :value="$puu->cover ?? ''"
                            mime="image/*"
                            accept="image/*" />

                        {{-- Lampiran --}}
                        <x-backend.input.file
                            label="Lampiran (Opsional)"
                            key="lampiran"
                            placeholder="Unggah lampiran pendukung"
                            :value="$puu->lampiran ?? ''"
                            accept=".pdf,.doc,.docx,.xls,.xlsx" />

                        @if(!$isCreate && $puu->dokumen_utama)
                            <div class="form-group">
                                <label class="control-label col-md-3"></label>
                                <div class="col-md-9">
                                    <a href="{{ Storage::url($puu->dokumen_utama) }}" target="_blank" class="btn btn-xs btn-info">
                                        <i class="fa fa-eye"></i> Lihat Dokumen
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- PENGELOLAAN DOKUMEN --}}
                <div class="box box-danger box-solid">
                    <div class="box-header with-border">
                        <b>Pengelolaan Dokumen</b>
                    </div>
                    <div class="box-body">
                        {{-- Status Publikasi --}}
                        <x-backend.input.select
                            label="Status Publikasi"
                            key="status_publikasi"
                            placeholder="--Pilih Status--"
                            required
                            :value="$puu->status_publikasi ?? 'draft'"
                            :data="[
                                ['label' => 'Draft', 'value' => 'draft'],
                                ['label' => 'Published', 'value' => 'published'],
                                ['label' => 'Archived', 'value' => 'archived'],
                            ]" />

                        {{-- Hak Akses --}}
                        <x-backend.input.select
                            label="Hak Akses"
                            key="hak_akses"
                            placeholder="--Pilih Hak Akses--"
                            required
                            :value="$puu->hak_akses ?? 'private'"
                            :data="[
                                ['label' => 'Private', 'value' => 'private'],
                                ['label' => 'Public', 'value' => 'public'],
                            ]" />

                        {{-- Kategori --}}
                        <x-backend.input.select
                            label="Kategori"
                            key="kategori"
                            placeholder="--Pilih Kategori--"
                            :value="$puu->kategori ?? ''"
                            :data="[
                                ['label' => 'Pembentukan', 'value' => 'pembentukan'],
                                ['label' => 'Evaluasi', 'value' => 'evaluasi'],
                                ['label' => 'Penelitian', 'value' => 'penelitian'],
                                ['label' => 'Kajian', 'value' => 'kajian'],
                            ]" />

                        {{-- Keterangan --}}
                        <x-backend.input.textarea
                            label="Keterangan"
                            key="keterangan"
                            placeholder="Catatan tambahan"
                            rows="2"
                            :value="$puu->keterangan ?? ''" />
                    </div>
                </div>

                {{-- INFORMASI ADMINISTRASI --}}
                <div class="box box-info box-solid">
                    <div class="box-header with-border">
                        <b>Informasi Administrasi</b>
                    </div>
                    <div class="box-body">
                        {{-- Pengunggah --}}
                        <x-backend.input.text
                            label="Pengunggah"
                            key="pengunggah"
                            placeholder="Nama admin yang mengunggah"
                            required
                            :value="$puu->pengunggah ?? ''" />

                        {{-- Tanggal Unggah --}}
                        <div class="form-group">
                            <label for="tanggal_unggah" class="control-label col-md-3">
                                Tanggal Unggah <span class="text-red">*</span>
                            </label>
                            <div class="col-md-9">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" 
                                           class="form-control datepicker" 
                                           id="tanggal_unggah" 
                                           name="tanggal_unggah"
                                           placeholder="Pilih tanggal unggah"
                                           value="{{ old('tanggal_unggah', $puu->tanggal_unggah ?? date('d-m-Y')) }}"
                                           required
                                           autocomplete="off">
                                </div>
                                @error('tanggal_unggah')
                                    <span class="help-block text-red">{{ $message }}</span>
                                @enderror
                                <small class="help-block">Klik ikon kalender untuk memilih tanggal</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-success btn-flat">
                            <i class="fa fa-save"></i> Simpan Data
                        </button>
                        <a href="{{ route('backend.pembentukan-puu.index') }}" class="btn btn-danger btn-flat">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <style>
        .datepicker {
            z-index: 9999 !important;
        }
        .datepicker-dropdown {
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .datepicker table {
            width: 100%;
        }
        .datepicker table tr td,
        .datepicker table tr th {
            text-align: center;
            padding: 5px;
        }
        .datepicker table tr td.day:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }
        .datepicker table tr td.active,
        .datepicker table tr td.active:hover {
            background-color: #337ab7;
            color: white;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.id.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi datepicker untuk tanggal unggah
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                language: 'id',
                weekStart: 1,
                daysOfWeekHighlighted: "0,6",
                todayBtn: "linked",
                clearBtn: true,
                orientation: "auto"
            });

            // Fungsi untuk menampilkan field sesuai jenis dokumen
            function showFieldByType() {
                // Sembunyikan semua field group dan field-spesifik container
                $('.field-group').hide();
                $('#field-spesifik').hide();
                
                // Ambil jenis dokumen yang dipilih
                const jenisDokumen = $('#jenis_dokumen').val();
                
                if (jenisDokumen) {
                    // Tampilkan container field-spesifik
                    $('#field-spesifik').show();
                    
                    // Tampilkan field group sesuai jenis
                    $(`#field-${jenisDokumen}`).show();
                    
                    // Beri judul dinamis pada bagian field spesifik
                    const jenisLabel = $('#jenis_dokumen option:selected').text();
                    $(`#field-${jenisDokumen} .box-header b`).text(`Informasi ${jenisLabel}`);
                }
            }
            
            // Event handler untuk perubahan jenis dokumen
            $('#jenis_dokumen').change(function() {
                showFieldByType();
            });
            
            // Inisialisasi saat halaman load (untuk edit mode)
            const initialJenis = @json($puu->jenis_dokumen ?? '');
            if (initialJenis) {
                setTimeout(() => {
                    showFieldByType();
                }, 100);
            }
            
            // Validasi form
            $('form').submit(function(e) {
                // Validasi required fields
                let isValid = true;
                
                // Cek jenis dokumen
                if (!$('#jenis_dokumen').val()) {
                    alert('Jenis Dokumen PUU harus dipilih');
                    $('#jenis_dokumen').focus();
                    isValid = false;
                }
                
                // Cek dokumen utama untuk create
                const isCreate = @json($isCreate);
                if (isCreate && !$('#dokumen_utama').val()) {
                    alert('Dokumen utama harus diunggah');
                    $('#dokumen_utama').focus();
                    isValid = false;
                }
                
                // Cek pengunggah
                if (!$('#pengunggah').val()) {
                    alert('Nama pengunggah harus diisi');
                    $('#pengunggah').focus();
                    isValid = false;
                }
                
                // Cek tanggal unggah
                if (!$('#tanggal_unggah').val()) {
                    alert('Tanggal unggah harus dipilih');
                    $('#tanggal_unggah').focus();
                    isValid = false;
                }
                
                // Validasi format tanggal
                const tanggalUnggah = $('#tanggal_unggah').val();
                const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
                if (!dateRegex.test(tanggalUnggah)) {
                    alert('Format tanggal unggah tidak valid. Gunakan format YYYY-MM-DD');
                    $('#tanggal_unggah').focus();
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });
            
            // Format tahun otomatis
            $('#tahun').on('input', function() {
                let year = $(this).val();
                if (year.length > 4) {
                    $(this).val(year.substring(0, 4));
                }
            });
            
            // Auto-generate kata kunci dari judul
            $('#judul').on('blur', function() {
                if (!$('#kata_kunci').val()) {
                    const judul = $(this).val();
                    if (judul.length > 0) {
                        // Ambil 5-7 kata pertama sebagai kata kunci
                        const words = judul.split(' ').slice(0, 7);
                        $('#kata_kunci').val(words.join(', '));
                    }
                }
            });
            
            // Set nilai default untuk pengunggah jika kosong
            if (!$('#pengunggah').val()) {
                $('#pengunggah').val('admin');
            }
            
            // Tombol untuk set tanggal hari ini
            $('#set-today-btn').on('click', function() {
                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                $('#tanggal_unggah').val(`${yyyy}-${mm}-${dd}`);
                $('.datepicker').datepicker('update', today);
            });
        });
    </script>
    @endpush

</x-layouts.backend>