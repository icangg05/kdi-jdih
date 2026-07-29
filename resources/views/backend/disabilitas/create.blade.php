{{-- resources/views/backend/disabilitas/create.blade.php --}}
@php
    $isCreate = true; // Karena ini adalah halaman create
    $title = 'Tambah Data Disabilitas';
    $disabilitas = null; // Set null karena tidak ada data untuk edit
@endphp

<x-layouts.backend
    :title="$title"
    :listNav="[['label' => 'Disabilitas', 'route' => route('backend.disabilitas.index')], ['label' => $title]]">

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras dengan .dis-index / .dis-show) ── */
    .dis-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:960px; }

    /* Box → seragam, buang solid warna yang ramai */
    .dis-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .dis-form .box.box-solid > .box-header { background:#fff !important; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .dis-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .dis-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .dis-form .box.box-solid > .box-header b i { color:var(--soft); font-weight:400; }
    .dis-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    /* Aksen per peran box, halus */
    .dis-form .box.box-info    { border-top-color:#2f6fb0; }
    .dis-form .box.box-info    > .box-header::before { background:#2f6fb0; }
    .dis-form .box.box-success { border-top-color:#2b9348; }
    .dis-form .box.box-success > .box-header::before { background:#2b9348; }
    .dis-form .box.box-warning { border-top-color:#c77d0a; }
    .dis-form .box.box-warning > .box-header::before { background:#c77d0a; }
    .dis-form .box.box-danger,
    .dis-form .box.box-primary { border-top-color:var(--accent); }

    /* Label & input */
    .dis-form .control-label { color:var(--muted); font-weight:600; font-size:13px; padding-top:8px; }
    .dis-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; height:38px; transition:border-color .14s ease, box-shadow .14s ease; }
    .dis-form textarea.form-control { height:auto; }
    .dis-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .dis-form .form-group { margin-bottom:16px; }
    .dis-form .help-block, .dis-form .text-muted { font-size:12px; color:var(--soft); }
    .dis-form .text-danger, .dis-form .text-red { color:var(--accent); }
    .dis-form .input-group-addon { background:#faf9f7; border-color:#dcd9d3; color:var(--muted); }
    .dis-form input[readonly] { background:#f6f4f1; cursor:not-allowed; }

    /* Checkbox groups (jenis disabilitas / sektor kebijakan) */
    .dis-form .checkbox label, .dis-form .checkbox-inline { font-size:13.5px; color:var(--ink); font-weight:500; margin-right:18px; margin-bottom:6px; }
    .dis-form .checkbox input[type=checkbox], .dis-form .checkbox-inline input[type=checkbox] { accent-color:var(--accent); }

    /* Select2 selaras */
    .dis-form .select2-container--default .select2-selection--single { border-color:#dcd9d3 !important; border-radius:6px; }
    .dis-form .select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }

    /* Baris aksi */
    .dis-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .dis-form .box-footer { background:transparent; border:none; padding:6px 0 0; }
    .dis-form .btn-success.btn-flat { background:var(--accent); border-color:var(--accent); }
    .dis-form .btn-success.btn-flat:hover, .dis-form .btn-success.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
    .dis-form .btn-danger.btn-flat { background:#fff; color:var(--muted); border:1px solid #dcd9d3; }
    .dis-form .btn-danger.btn-flat:hover { background:#f6f4f1; color:var(--ink); }
    .dis-form .btn-default.btn-flat { border-radius:6px; }
</style>
@endpush

    <div class="box-body no-padding">
        <div class="section dis-form">
            <form
                class="form-horizontal"
                action="{{ route('backend.disabilitas.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf

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
                            :value="old('jenis_dokumen')"
                            :data="[
                                ['label' => 'Undang-Undang', 'value' => 'uu'],
                                ['label' => 'Peraturan Pemerintah', 'value' => 'pp'],
                                ['label' => 'Peraturan Presiden', 'value' => 'perpres'],
                                ['label' => 'Peraturan Menteri', 'value' => 'permen'],
                                ['label' => 'Peraturan Daerah', 'value' => 'perda'],
                                ['label' => 'Keputusan Presiden', 'value' => 'keppres'],
                                ['label' => 'Keputusan Menteri', 'value' => 'kepmen'],
                                ['label' => 'Peraturan Walikota', 'value' => 'perwal'],
                                ['label' => 'Keputusan Walikota', 'value' => 'kepwal'],
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
                            :value="old('judul')" />

                        {{-- Nomor Dokumen --}}
                        <x-backend.input.text
                            label="Nomor Dokumen"
                            key="nomor_dokumen"
                            placeholder="Contoh: 8 Tahun 2016, 52 Tahun 2019"
                            required
                            :value="old('nomor_dokumen')" />

                        {{-- Tahun Dokumen --}}
                        <x-backend.input.text
                            label="Tahun"
                            key="tahun"
                            placeholder="Tahun dokumen"
                            required
                            type="number"
                            min="1900"
                            max="{{ date('Y') + 5 }}"
                            :value="old('tahun')" />

                        {{-- Tempat Penetapan --}}
                        <x-backend.input.text
                            label="Tempat Penetapan"
                            key="tempat_penetapan"
                            placeholder="Contoh: Jakarta"
                            :value="old('tempat_penetapan')" />

                        {{-- Tanggal Penetapan --}}
                        <x-backend.input.date
                            label="Tanggal Penetapan"
                            key="tanggal_penetapan"
                            placeholder="Tanggal ditetapkan"
                            :value="old('tanggal_penetapan')" />

                        {{-- Lembaga Penetap --}}
                        <x-backend.input.text
                            label="Lembaga Penetap"
                            key="lembaga_penetap"
                            placeholder="Contoh: Presiden RI, DPR RI, Kementerian Sosial"
                            required
                            :value="old('lembaga_penetap')" />

                        {{-- Status Dokumen --}}
                        <x-backend.input.select
                            label="Status Dokumen"
                            key="status_dokumen"
                            placeholder="--Pilih status--"
                            required
                            :value="old('status_dokumen')"
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
                            :value="old('dokumen_terkait')" />
                    </div>
                </div>

                <!-- Informasi Tambahan Dokumen -->
                <div class="box box-info box-solid">
                    <div class="box-header with-border">
                        <b><i class="fa fa-info-circle"></i> Informasi Tambahan Dokumen</b>
                    </div>
                    <div class="box-body">
                        {{-- Penulis --}}
                        <x-backend.input.text
                            label="Penulis"
                            key="penulis"
                            placeholder="Nama penulis dokumen"
                            :value="old('penulis')" />

                        {{-- Penerbit --}}
                        <x-backend.input.text
                            label="Penerbit"
                            key="penerbit"
                            placeholder="Nama penerbit dokumen"
                            :value="old('penerbit')" />

                        {{-- ISBN/ISSN --}}
                        <x-backend.input.text
                            label="ISBN/ISSN"
                            key="isbn_issn"
                            placeholder="Nomor ISBN/ISSN"
                            :value="old('isbn_issn')" />

                        {{-- DOI --}}
                        <x-backend.input.text
                            label="DOI"
                            key="doi"
                            placeholder="Digital Object Identifier"
                            :value="old('doi')" />

                        {{-- Sumber --}}
                        <x-backend.input.text
                            label="Sumber"
                            key="sumber"
                            placeholder="Sumber dokumen"
                            :value="old('sumber')" />

                        {{-- URL Referensi --}}
                        <x-backend.input.text
                            label="URL Referensi"
                            key="url_referensi"
                            placeholder="https://example.com"
                            type="url"
                            :value="old('url_referensi')" />
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
                                        $jenisDisabilitas = old('jenis_disabilitas', []);
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
                            :value="old('ruang_lingkup')"
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
                                    $sektor = old('sektor_kebijakan', []);
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
                            :value="old('abstrak')" />

                        {{-- Kata Kunci --}}
                        <x-backend.input.text
                            label="Kata Kunci"
                            key="kata_kunci"
                            placeholder="Pisahkan dengan koma, contoh: disabilitas, inklusi, aksesibilitas, hak"
                            :value="old('kata_kunci')" />

                        {{-- Jumlah Halaman --}}
                        <x-backend.input.text
                            label="Jumlah Halaman"
                            key="jumlah_halaman"
                            placeholder="Jumlah halaman dokumen"
                            type="number"
                            min="1"
                            :value="old('jumlah_halaman')" />

                        {{-- Bahasa --}}
                        <x-backend.input.select
                            label="Bahasa"
                            key="bahasa"
                            placeholder="--Pilih bahasa--"
                            :value="old('bahasa')"
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
                            :value="''"
                            :mimes="['pdf']"
                            required />

                        {{-- Cover/Thumbnail --}}
                        <x-backend.input.file-small
                            label="Cover/Thumbnail (Gambar)"
                            key="cover"
                            :value="''"
                            :mimes="['jpg', 'jpeg', 'png', 'gif']" />

                        {{-- Lampiran (opsional) --}}
                        <x-backend.input.file-small
                            label="Lampiran (PDF/Doc)"
                            key="lampiran"
                            :value="''"
                            :mimes="['pdf', 'doc', 'docx']" />
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
                            :value="old('status_publikasi', 'draft')"
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
                            :value="old('hak_akses', 'public')"
                            :data="[
                                ['label' => 'Publik (Semua Orang)', 'value' => 'public'],
                                ['label' => 'Terbatas (Login)', 'value' => 'restricted'],
                                ['label' => 'Admin Only', 'value' => 'admin'],
                                ['label' => 'Internal Only', 'value' => 'internal'],
                            ]" />

                        <x-backend.input.date
                            label="Tanggal Unggah"
                            key="tanggal_unggah"
                            :required="true"
                            :value="now()" />

                        {{-- Keterangan Tambahan --}}
                        <x-backend.input.textarea
                            label="Keterangan Tambahan"
                            key="keterangan"
                            rows="3"
                            placeholder="Catatan internal atau keterangan lainnya"
                            :value="old('keterangan')" />

                        {{-- Pengunggah --}}
                        <x-backend.input.text
                            label="Pengunggah"
                            key="pengunggah"
                            placeholder="Nama pengunggah"
                            :value="old('pengunggah', auth()->user()->username ?? '')"
                            readonly />
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="box-footer">
                    <button
                        type="submit"
                        class="btn btn-success btn-flat">
                        <i class="fa fa-save"></i> Simpan Data
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
                console.log('Document ready - Inisialisasi form create');
                
                // Semua field tanggal ditangani komponen x-backend.input.date.

                // Auto-fill tahun saat ini jika kosong
                if (!$('#tahun').val()) {
                    $('#tahun').val(new Date().getFullYear());
                }

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

                // Preview file sebelum upload
                $('input[type="file"]').change(function() {
                    const file = this.files[0];
                    const inputId = $(this).attr('id');
                    const previewDiv = $('#' + inputId + '-preview');
                    
                    if (file) {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                previewDiv.html(
                                    '<div class="alert alert-info">' +
                                    '<i class="fa fa-image"></i> Preview: ' + file.name + 
                                    '<br><small>Size: ' + (file.size / 1024).toFixed(2) + ' KB</small>' +
                                    '</div>'
                                );
                            }
                            reader.readAsDataURL(file);
                        } else {
                            previewDiv.html(
                                '<div class="alert alert-info">' +
                                '<i class="fa fa-file"></i> File: ' + file.name + 
                                '<br><small>Size: ' + (file.size / 1024).toFixed(2) + ' KB</small>' +
                                '</div>'
                            );
                        }
                    }
                });

                $('form').submit(function(e) {
                    // Validasi form
                    let isValid = true;
                    let errorFields = [];
                    
                    // Cek required fields
                    $('[required]').each(function() {
                        const $field = $(this);
                        const fieldValue = $field.val();
                        const fieldName = $field.attr('name') || $field.attr('id');
                        
                        if (!$field.val() && $field.attr('type') !== 'file') {
                            isValid = false;
                            errorFields.push(fieldName);
                            $field.addClass('error');
                            $field.closest('.form-group').addClass('has-error');
                        } else {
                            $field.removeClass('error');
                            $field.closest('.form-group').removeClass('has-error');
                        }
                    });

                    // Validasi file PDF untuk dokumen utama
                    const dokumenUtama = $('#dokumen_utama')[0];
                    if (dokumenUtama && dokumenUtama.files.length > 0) {
                        const file = dokumenUtama.files[0];
                        if (file.type !== 'application/pdf') {
                            isValid = false;
                            alert('Dokumen utama harus berformat PDF!');
                        }
                    } else {
                        isValid = false;
                        alert('Dokumen utama harus diunggah!');
                    }

                    // Debug: log data yang akan disubmit
                    console.log('Data form yang akan disubmit:');
                    const formData = new FormData(this);
                    for (let [key, value] of formData.entries()) {
                        console.log(key + ':', value);
                    }

                    if (!isValid) {
                        e.preventDefault();
                        alert('Harap lengkapi semua field yang wajib diisi!');
                        console.log('Field yang error:', errorFields);
                        
                        // Scroll ke field error pertama
                        if (errorFields.length > 0) {
                            $('html, body').animate({
                                scrollTop: $('.error').first().offset().top - 100
                            }, 500);
                        }
                    } else {
                        console.log('Form valid, submit akan diproses');
                    }
                });

                // Handle URL Referensi validation
                $('#url_referensi').on('blur', function() {
                    const url = $(this).val();
                    if (url && !isValidUrl(url)) {
                        alert('URL tidak valid. Format harus: https://example.com');
                        $(this).val('');
                    }
                });

                function isValidUrl(string) {
                    try {
                        new URL(string);
                        return true;
                    } catch (_) {
                        return false;
                    }
                }
            });
        </script>
    @endpush
</x-layouts.backend>
