@php
    $isCreate = false;
    $title = 'Edit Data Pembentukan PUU';
@endphp

<x-layouts.backend
    :title="$title"
    :listNav="[['label' => 'Pembentukan PUU', 'route' => route('backend.pembentukan-puu.index')], ['label' => $title]]">

@push('link')
<style>
    /* ── Scoped ke halaman form ini (selaras dengan .puu-index / .puu-show) ── */
    .puu-form { --ink:#1f2328; --muted:#6b7075; --soft:#8b9096; --accent:#c0392b; --line:#e7e4df; max-width:960px; }

    /* Box → seragam, buang solid warna yang ramai */
    .puu-form .box.box-solid { border:1px solid var(--line); border-top:3px solid var(--accent); border-radius:9px; box-shadow:0 1px 2px rgba(31,35,40,.05); background:#fff; margin-bottom:18px; }
    .puu-form .box.box-solid > .box-header { background:#fff; color:var(--ink); border-bottom:1px solid var(--line); padding:15px 20px; display:flex; align-items:center; gap:11px; border-radius:9px 9px 0 0; }
    .puu-form .box.box-solid > .box-header::before { content:''; width:4px; height:16px; background:var(--accent); border-radius:2px; flex:none; }
    .puu-form .box.box-solid > .box-header b { font-size:14.5px; font-weight:600; letter-spacing:-.01em; color:var(--ink); }
    .puu-form .box.box-solid > .box-body { padding:20px 22px 6px; }

    /* Aksen per peran box, halus */
    .puu-form .box.box-info    { border-top-color:#2f6fb0; }
    .puu-form .box.box-info    > .box-header::before { background:#2f6fb0; }
    .puu-form .box.box-success { border-top-color:#2b9348; }
    .puu-form .box.box-success > .box-header::before { background:#2b9348; }
    .puu-form .box.box-warning { border-top-color:#c77d0a; }
    .puu-form .box.box-warning > .box-header::before { background:#c77d0a; }
    .puu-form .box.box-danger,
    .puu-form .box.box-primary { border-top-color:var(--accent); }

    /* Label & input */
    .puu-form .control-label { color:var(--muted); font-weight:600; font-size:13px; padding-top:8px; }
    .puu-form .form-control { border-color:#dcd9d3; box-shadow:none; border-radius:6px; height:38px; transition:border-color .14s ease, box-shadow .14s ease; }
    .puu-form textarea.form-control { height:auto; }
    .puu-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }
    .puu-form .form-group { margin-bottom:16px; }
    .puu-form .help-block { font-size:12px; color:var(--soft); }
    .puu-form .text-red { color:var(--accent); }
    .puu-form .input-group-addon { background:#faf9f7; border-color:#dcd9d3; color:var(--muted); }

    /* File lama + checkbox hapus */
    .puu-form .checkbox label { color:var(--ink); font-size:13px; }
    .puu-form small.text-info { color:var(--muted) !important; display:inline-block; margin-top:4px; }

    /* Select2 selaras */
    .puu-form .select2-container--default .select2-selection--single { border-color:#dcd9d3 !important; border-radius:6px; }
    .puu-form .select2-container--default.select2-container--focus .select2-selection--single { border-color:var(--accent); box-shadow:0 0 0 3px rgba(192,57,43,.10); }

    /* Baris aksi */
    .puu-form .btn-flat { border-radius:6px; padding:9px 20px; font-weight:600; }
    .puu-form .btn-success.btn-flat { background:var(--accent); border-color:var(--accent); }
    .puu-form .btn-success.btn-flat:hover, .puu-form .btn-success.btn-flat:focus { background:#a5271b; border-color:#a5271b; }
    .puu-form .btn-danger.btn-flat { background:#fff; color:var(--muted); border:1px solid #dcd9d3; }
    .puu-form .btn-danger.btn-flat:hover { background:#f6f4f1; color:var(--ink); }
    .puu-form .btn-info { border-radius:5px; }
</style>
@endpush

    <div class="box-body no-padding">
        <div class="section puu-form">
            <form
                class="form-horizontal"
                action="{{ route('backend.pembentukan-puu.update', $puu->id) }}"
                method="POST"
                enctype="multipart/form-data"
                id="edit-puu-form">
                @csrf
                @method('PUT')

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
                                ['label' => 'Naskah Keterangan dan/atau Penjelasan', 'value' => 'naskah_keterangan_penjelasan'],
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

                {{-- INFORMASI UMUM --}}
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
                            required="false"
                            :value="$puu->dokumen_utama ?? ''"
                            mime="application/pdf"
                            accept=".pdf" />

                        @if($puu->dokumen_utama)
                            <div class="form-group">
                                <label class="control-label col-md-3"></label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="hapus_dokumen_utama" value="1">
                                            Hapus dokumen utama yang ada
                                        </label>
                                    </div>
                                    <small class="text-info">
                                        <i class="fa fa-file-pdf-o"></i>
                                        File saat ini: {{ basename($puu->dokumen_utama) }}
                                    </small>
                                    <a href="{{ Storage::url($puu->dokumen_utama) }}" target="_blank" class="btn btn-xs btn-info ml-2">
                                        <i class="fa fa-eye"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Cover --}}
                        <x-backend.input.file
                            label="Cover/Gambar (Opsional)"
                            key="cover"
                            placeholder="Unggah gambar cover"
                            :value="$puu->cover ?? ''"
                            mime="image/*"
                            accept="image/*" />

                        @if($puu->cover)
                            <div class="form-group">
                                <label class="control-label col-md-3"></label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="hapus_cover" value="1">
                                            Hapus cover yang ada
                                        </label>
                                    </div>
                                    <small class="text-info">
                                        <i class="fa fa-image"></i>
                                        File saat ini: {{ basename($puu->cover) }}
                                    </small>
                                    <a href="{{ Storage::url($puu->cover) }}" target="_blank" class="btn btn-xs btn-info ml-2">
                                        <i class="fa fa-eye"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        @endif

                        {{-- Lampiran --}}
                        <x-backend.input.file
                            label="Lampiran (Opsional)"
                            key="lampiran"
                            placeholder="Unggah lampiran pendukung"
                            :value="$puu->lampiran ?? ''"
                            accept=".pdf,.doc,.docx,.xls,.xlsx" />

                        @if($puu->lampiran)
                            <div class="form-group">
                                <label class="control-label col-md-3"></label>
                                <div class="col-md-9">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="hapus_lampiran" value="1">
                                            Hapus lampiran yang ada
                                        </label>
                                    </div>
                                    <small class="text-info">
                                        <i class="fa fa-paperclip"></i>
                                        File saat ini: {{ basename($puu->lampiran) }}
                                    </small>
                                    <a href="{{ Storage::url($puu->lampiran) }}" target="_blank" class="btn btn-xs btn-info ml-2">
                                        <i class="fa fa-eye"></i> Lihat
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

                        <x-backend.input.date
                            label="Tanggal Unggah"
                            key="tanggal_unggah"
                            :required="true"
                            :value="$puu->tanggal_unggah ?? now()" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-3 col-md-9">
                        <button type="submit" class="btn btn-success btn-flat">
                            <i class="fa fa-save"></i> Update Data
                        </button>
                        <a href="{{ route('backend.pembentukan-puu.index') }}" class="btn btn-danger btn-flat">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('script')
    <script>
        $(document).ready(function() {
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
            const initialJenis = '{{ $puu->jenis_dokumen ?? "" }}';
            if (initialJenis) {
                // Set nilai dropdown ke jenis dokumen yang sudah ada
                $('#jenis_dokumen').val(initialJenis);

                // Tunggu sebentar agar DOM selesai render
                setTimeout(() => {
                    showFieldByType();
                }, 100);
            }

            // Validasi form
            $('#edit-puu-form').submit(function(e) {
                // Validasi required fields
                let isValid = true;

                // Cek jenis dokumen
                if (!$('#jenis_dokumen').val()) {
                    alert('Jenis Dokumen PUU harus dipilih');
                    $('#jenis_dokumen').focus();
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

                // Konfirmasi sebelum submit (optional)
                const isConfirmed = confirm('Apakah Anda yakin ingin mengupdate data ini?');
                if (!isConfirmed) {
                    e.preventDefault();
                    return false;
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
        });
    </script>
    @endpush

</x-layouts.backend>
