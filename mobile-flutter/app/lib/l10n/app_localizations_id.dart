// ignore: unused_import
import 'package:intl/intl.dart' as intl;

import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for Indonesian (`id`).
class AppLocalizationsId extends AppLocalizations {
  AppLocalizationsId([String locale = 'id']) : super(locale);

  @override
  String get appName => 'JDIH Kota Kendari';

  @override
  String get navHome => 'Beranda';

  @override
  String get navDocuments => 'Dokumen';

  @override
  String get navAskAi => 'Tanya AI';

  @override
  String get navNews => 'Kabar';

  @override
  String get navMenu => 'Menu';

  @override
  String aiAndSearch(String label) {
    return '$label dan pencarian';
  }

  @override
  String get errServer => 'Server sedang bermasalah. Coba lagi sebentar lagi.';

  @override
  String get errUnknownReply =>
      'Server mengirim balasan yang tidak dikenali. Jika memakai Wi-Fi publik, pastikan sudah masuk (login) ke jaringannya.';

  @override
  String get errTooMany =>
      'Terlalu banyak permintaan dalam waktu singkat. Tunggu sebentar, lalu coba lagi.';

  @override
  String get errInvalidInput =>
      'Isian belum lengkap atau tidak sesuai. Periksa kembali, lalu kirim ulang.';

  @override
  String get errNotFound => 'Data yang dicari tidak ditemukan.';

  @override
  String errRequestFailed(int code) {
    return 'Permintaan tidak dapat diproses ($code). Coba lagi.';
  }

  @override
  String get errTimeout => 'Server lambat merespons. Coba lagi.';

  @override
  String get errOffline =>
      'Tidak dapat terhubung ke server JDIH. Periksa koneksi internet, lalu coba lagi.';

  @override
  String minRead(int n) {
    return '$n menit baca';
  }

  @override
  String get linkOpenFailed => 'Tidak dapat membuka tautan.';

  @override
  String get closeImage => 'Tutup gambar';

  @override
  String get statusInForce => 'Berlaku';

  @override
  String get statusNotInForce => 'Tidak Berlaku';

  @override
  String get statusRevoked => 'Dicabut';

  @override
  String get statusAmended => 'Diubah';

  @override
  String get legalDocument => 'Dokumen Peraturan';

  @override
  String get seeAll => 'Lihat semua';

  @override
  String get language => 'Bahasa';

  @override
  String get notFound => 'Tidak ditemukan';

  @override
  String noMatch(String query) {
    return 'Tidak ada yang cocok dengan \"$query\".';
  }

  @override
  String get noMatchHint =>
      'Coba kata kunci yang lebih pendek, periksa ejaannya, atau ganti kategori.';

  @override
  String get aiPromoBody =>
      'Tanyakan dengan bahasa sehari-hari. Jawabannya berupa percakapan, lengkap dengan dokumen hukum yang relevan.';

  @override
  String get aiPromoStart => 'Mulai bertanya';

  @override
  String get latest => 'Terbaru';

  @override
  String get share => 'Bagikan';

  @override
  String get readLess => 'Tutup';

  @override
  String get readMore => 'Selengkapnya';

  @override
  String get retry => 'Coba lagi';

  @override
  String get noData => 'Tidak ada data.';

  @override
  String get emptyTitle => 'Belum ada isinya';

  @override
  String get reload => 'Muat ulang';

  @override
  String get loadMore => 'Muat lanjutan';

  @override
  String get catRegulations => 'Peraturan & Keputusan';

  @override
  String get catRegulationsShort => 'Peraturan';

  @override
  String get catRegulationsSub => 'Perda, Perwali, dan Keputusan Wali Kota';

  @override
  String get catMonographs => 'Monografi Hukum';

  @override
  String get catMonographsShort => 'Monografi';

  @override
  String get catMonographsSub => 'Buku dan kajian hukum daerah';

  @override
  String get catArticles => 'Artikel / Majalah Hukum';

  @override
  String get catArticlesShort => 'Artikel';

  @override
  String get catArticlesSub => 'Artikel dan majalah hukum';

  @override
  String get catRulings => 'Putusan';

  @override
  String get catRulingsShort => 'Putusan';

  @override
  String get catRulingsSub => 'Putusan pengadilan terkait daerah';

  @override
  String get latestDocuments => 'Dokumen Terbaru';

  @override
  String get jdihNews => 'Kabar JDIH';

  @override
  String get collectionStats => 'Statistik Koleksi';

  @override
  String get greetMorning => 'Selamat pagi';

  @override
  String get greetMidday => 'Selamat siang';

  @override
  String get greetAfternoon => 'Selamat sore';

  @override
  String get greetEvening => 'Selamat malam';

  @override
  String get logoJdihn => 'Logo JDIHN';

  @override
  String get homeHeadline => 'Cari aturan apa hari ini?';

  @override
  String get adatMeaning => 'Siapa yang menghargai adat, ia akan dihormati';

  @override
  String get homeSearchHint => 'Judul, nomor, atau pertanyaan...';

  @override
  String get search => 'Cari';

  @override
  String get noAbbr => 'No.';

  @override
  String get views => 'dilihat';

  @override
  String get downloads => 'diunduh';

  @override
  String get pickDocument => 'Pilih dokumen';

  @override
  String get pickDocumentHint =>
      'Ketuk dokumen di daftar untuk membaca rinciannya di sini, berdampingan dengan hasil lainnya.';

  @override
  String get legalDocuments => 'Dokumen Hukum';

  @override
  String get latestLegalProducts => 'Produk Hukum Terbaru';

  @override
  String docsAvailable(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count dokumen tersedia',
    );
    return '$_temp0';
  }

  @override
  String get filterAllTypes => 'Semua Jenis';

  @override
  String get filterAllYears => 'Semua Tahun';

  @override
  String get filterAllStatus => 'Semua Status';

  @override
  String get type => 'Jenis';

  @override
  String get year => 'Tahun';

  @override
  String get status => 'Status';

  @override
  String get number => 'Nomor';

  @override
  String get searchTitleHint => 'Cari judul...';

  @override
  String get reset => 'Reset';

  @override
  String docCount(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count dokumen',
    );
    return '$_temp0';
  }

  @override
  String docCountFiltered(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count dokumen sesuai filter',
    );
    return '$_temp0';
  }

  @override
  String get emptyCategory => 'Belum ada dokumen pada kategori ini.';

  @override
  String get loosenFilters =>
      'Longgarkan filternya atau periksa ejaan kata kuncinya.';

  @override
  String get resetFilter => 'Reset filter';

  @override
  String get back => 'Kembali';

  @override
  String numberValue(String nomor) {
    return 'Nomor $nomor';
  }

  @override
  String yearValue(String tahun) {
    return 'Tahun $tahun';
  }

  @override
  String get tabAbout => 'Tentang';

  @override
  String tabFiles(int count) {
    return 'Berkas ($count)';
  }

  @override
  String get tabRelated => 'Terkait';

  @override
  String get tabImplementing => 'Pelaksana';

  @override
  String get aboutDocument => 'Tentang Dokumen';

  @override
  String get abstract => 'Abstrak';

  @override
  String abstractOf(String judul) {
    return 'Abstrak $judul';
  }

  @override
  String fileAbstract(String judul) {
    return 'abstrak $judul';
  }

  @override
  String get metaForm => 'Bentuk';

  @override
  String get metaPlacePublished => 'Tempat Terbit';

  @override
  String get publisher => 'Penerbit';

  @override
  String get metaDateEnacted => 'Tgl. Penetapan';

  @override
  String get metaDatePromulgated => 'Tgl. Pengundangan';

  @override
  String get source => 'Sumber';

  @override
  String get metaLegalField => 'Bidang Hukum';

  @override
  String get metaSignatory => 'Penandatangan';

  @override
  String get metaPhysicalDesc => 'Deskripsi Fisik';

  @override
  String get metaCourt => 'Lembaga Peradilan';

  @override
  String get metaPetitioner => 'Pemohon';

  @override
  String get metaRespondent => 'Termohon';

  @override
  String get metaCaseType => 'Jenis Perkara';

  @override
  String get metaTeu => 'T.E.U.';

  @override
  String get subject => 'Subjek';

  @override
  String get author => 'Pengarang';

  @override
  String get noFiles => 'Belum ada berkas';

  @override
  String get noFilesHint =>
      'Dokumen ini belum memiliki file yang bisa dilihat atau diunduh. Coba periksa peraturan terkait.';

  @override
  String get document => 'Dokumen';

  @override
  String fileAttachmentN(String judul, int n) {
    return '$judul lampiran $n';
  }

  @override
  String get noRelated => 'Tidak ada peraturan terkait';

  @override
  String get noRelatedHint =>
      'Dokumen ini berdiri sendiri: tidak mengubah, mencabut, atau diubah oleh peraturan lain.';

  @override
  String get noImplementing => 'Belum ada peraturan pelaksana';

  @override
  String get noImplementingHint =>
      'Peraturan turunan yang diterbitkan untuk melaksanakan peraturan ini akan tampil di sini.';

  @override
  String get statistics => 'Statistik';

  @override
  String viewsDownloads(String views, String downloads) {
    return '$views dilihat · $downloads unduh';
  }

  @override
  String get fileUnavailable => 'Berkas tidak tersedia';

  @override
  String get viewDocument => 'Lihat Dokumen';

  @override
  String get downloadDocument => 'Unduh Dokumen';

  @override
  String get fileKindPdf => 'Dokumen PDF · dapat dibaca di aplikasi';

  @override
  String fileKindOther(String ext) {
    return 'Berkas $ext · unduh untuk membuka';
  }

  @override
  String get fileGeneric => 'Berkas';

  @override
  String downloading(String name) {
    return 'Mengunduh $name...';
  }

  @override
  String get downloadFailed =>
      'Berkas gagal diunduh. Periksa koneksi dan ruang penyimpanan, lalu coba lagi.';

  @override
  String get goToPage => 'Buka halaman';

  @override
  String get pageNumber => 'Nomor halaman';

  @override
  String get cancel => 'Batal';

  @override
  String get go => 'Buka';

  @override
  String pageOutOfRange(int total) {
    return 'Dokumen ini hanya punya halaman 1 sampai $total.';
  }

  @override
  String get download => 'Unduh';

  @override
  String get pdfCannotDisplay =>
      'Dokumen ini tidak dapat ditampilkan di aplikasi, tetapi masih bisa diunduh dan dibuka dengan aplikasi pembaca PDF.';

  @override
  String get downloadInstead => 'Unduh saja';

  @override
  String get prevPage => 'Halaman sebelumnya';

  @override
  String get nextPage => 'Halaman berikutnya';

  @override
  String pageOf(int page, int total) {
    return 'Halaman $page dari $total';
  }

  @override
  String get view => 'Lihat';

  @override
  String get newsAndInfo => 'Kabar & Informasi';

  @override
  String get news => 'Berita';

  @override
  String get announcements => 'Pengumuman';

  @override
  String get announcement => 'Pengumuman';

  @override
  String get video => 'Video';

  @override
  String get legalInfoShort => 'Info Hukum';

  @override
  String get legalInfo => 'Informasi Hukum';

  @override
  String get clearSearch => 'Hapus pencarian';

  @override
  String get noMatchHintShort =>
      'Coba kata kunci yang lebih pendek, atau periksa ejaannya.';

  @override
  String get searchNewsHint => 'Cari judul berita...';

  @override
  String get emptyNews => 'Belum ada berita yang dipublikasikan.';

  @override
  String get searchAnnouncementsHint => 'Cari judul pengumuman...';

  @override
  String get emptyAnnouncements => 'Belum ada pengumuman.';

  @override
  String get emptyVideos => 'Belum ada video di kanal JDIH.';

  @override
  String get moreNews => 'Berita Lainnya';

  @override
  String get moreAnnouncements => 'Pengumuman Lainnya';

  @override
  String get moreVideos => 'Video Lainnya';

  @override
  String get enlargeCover => 'Perbesar gambar sampul';

  @override
  String get tapToEnlarge => 'Ketuk untuk perbesar';

  @override
  String get attachment => 'Lampiran';

  @override
  String get announcementAttachment => 'Lampiran Pengumuman';

  @override
  String fileAttachmentOf(String judul) {
    return 'lampiran $judul';
  }

  @override
  String fileDocumentOf(String judul) {
    return 'dokumen $judul';
  }

  @override
  String get all => 'Semua';

  @override
  String get emptyLegalInfo =>
      'Naskah akademik, rancangan peraturan, dan kajian hukum akan tampil di sini begitu dipublikasikan.';

  @override
  String get emptyCategoryTitle => 'Kategori ini masih kosong';

  @override
  String get emptySelectedCategory =>
      'Belum ada dokumen pada kategori yang dipilih.';

  @override
  String emptyTypeNamed(String jenis) {
    return 'Belum ada dokumen berjenis $jenis. Kategori lain mungkin sudah terisi.';
  }

  @override
  String get seeAllCategories => 'Lihat semua kategori';

  @override
  String get linkCopied => 'Tautan video disalin.';

  @override
  String get videoNotPlayable =>
      'Video ini tersimpan di luar YouTube dan belum bisa diputar di aplikasi.';

  @override
  String get openInYoutube => 'Buka di YouTube';

  @override
  String get openVideoLink => 'Buka tautan video';

  @override
  String get copy => 'Salin';

  @override
  String get profileHistory => 'Sekilas Sejarah';

  @override
  String get profileLegalBasis => 'Dasar Hukum';

  @override
  String get profileVision => 'Visi';

  @override
  String get profileMission => 'Misi';

  @override
  String get profileStructure => 'Struktur Organisasi';

  @override
  String get more => 'Lainnya';

  @override
  String get jdihProfile => 'Profil JDIH';

  @override
  String get disabilityServices => 'Layanan Disabilitas';

  @override
  String get lawMaking => 'Pembentukan PUU';

  @override
  String get satisfactionSurvey => 'Survei Kepuasan';

  @override
  String get aboutJdih => 'Tentang JDIH';

  @override
  String get menuServices => 'Layanan & Informasi';

  @override
  String get menuApp => 'Aplikasi';

  @override
  String get orgStructureOnWeb =>
      'Konten struktur organisasi tersedia di situs web JDIH Kota Kendari.';

  @override
  String get jdihFull => 'Jaringan Dokumentasi dan Informasi Hukum';

  @override
  String get phone => 'Telepon';

  @override
  String get email => 'Email';

  @override
  String get address => 'Alamat';

  @override
  String get socialMedia => 'Media Sosial';

  @override
  String get appLabel => 'Aplikasi JDIH Kota Kendari';

  @override
  String loadingApp(String app) {
    return '$app, sedang memuat';
  }

  @override
  String get kendariGov => 'Pemerintah Kota Kendari';

  @override
  String get puuAcademicPaper => 'Naskah Akademik';

  @override
  String get puuExplanatory => 'Naskah Keterangan/Penjelasan';

  @override
  String get puuDraft => 'Rancangan PUU';

  @override
  String get puuResearch => 'Penelitian Hukum';

  @override
  String get puuLegalReview => 'Pengkajian Hukum';

  @override
  String get puuConstitutionalReview => 'Pengkajian Konstitusi';

  @override
  String get puuAnalysis => 'Analisis & Evaluasi';

  @override
  String get secBackground => 'Latar Belakang';

  @override
  String get secProblem => 'Rumusan Masalah';

  @override
  String get secObjective => 'Tujuan Penelitian';

  @override
  String get secMethod => 'Metodologi';

  @override
  String get secFocus => 'Fokus Penelitian';

  @override
  String get secResults => 'Hasil Penelitian';

  @override
  String get secReviewObject => 'Objek Pengkajian';

  @override
  String get secReviewConclusion => 'Kesimpulan Pengkajian';

  @override
  String get secConstitutional => 'Aspek Konstitusi';

  @override
  String get secEvalFindings => 'Temuan Evaluasi';

  @override
  String get secRecommendation => 'Rekomendasi';

  @override
  String get secImprovement => 'Rekomendasi Perbaikan';

  @override
  String get secNotes => 'Keterangan';

  @override
  String get metaInitiator => 'Lembaga Pemrakarsa';

  @override
  String get metaStage => 'Tahapan';

  @override
  String get metaWriter => 'Penulis';

  @override
  String get metaEditor => 'Editor';

  @override
  String get metaKeywords => 'Kata Kunci';

  @override
  String get metaViews => 'Dilihat';

  @override
  String get mainDocument => 'Dokumen Utama';

  @override
  String timesCount(int n) {
    return '$n kali';
  }

  @override
  String get searchDisabilityHint => 'Cari dokumen disabilitas...';

  @override
  String get metaPlaceEnacted => 'Tempat Penetapan';

  @override
  String get metaEnactedBy => 'Lembaga Penetap';

  @override
  String get metaDisabilityType => 'Jenis Disabilitas';

  @override
  String get metaScope => 'Ruang Lingkup';

  @override
  String get metaPolicySector => 'Sektor Kebijakan';

  @override
  String get metaPages => 'Jumlah Halaman';

  @override
  String get smartSearch => 'Cari Pintar';

  @override
  String get textSearch => 'Pencarian Teks';

  @override
  String get searchDocsHint => 'Cari judul, nomor, atau topik...';

  @override
  String resultsFor(int count, String query) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count hasil untuk \"$query\"',
    );
    return '$_temp0';
  }

  @override
  String get noMatchHintAi =>
      'Coba kata kunci yang lebih pendek, ganti kategori di atas, atau tanyakan langsung ke AI.';

  @override
  String get searchLegalProducts => 'Cari Produk Hukum';

  @override
  String get searchIntro =>
      'Ketik judul, nomor, atau topik peraturan lalu tekan tombol cari pada papan ketik. Kategori di atas mempersempit hasilnya.';

  @override
  String get aiSuggest1 => 'Retribusi sampah';

  @override
  String get aiSuggest1Ask => 'Apa aturan retribusi sampah?';

  @override
  String get aiSuggest2 => 'Perwali terbaru';

  @override
  String get aiSuggest2Ask => 'Perwali terbaru tentang apa?';

  @override
  String get aiSuggest3 => 'Mengurus IMB/PBG';

  @override
  String get aiSuggest3Ask => 'Bagaimana cara mengurus IMB?';

  @override
  String get aiSuggest4 => 'Pajak daerah';

  @override
  String get aiSuggest4Ask => 'Apa saja jenis pajak daerah Kota Kendari?';

  @override
  String get aiSuggest5 => 'APBD';

  @override
  String get aiSuggest5Ask => 'Apa isi peraturan daerah tentang APBD terbaru?';

  @override
  String get aiSuggest6 => 'Hak disabilitas';

  @override
  String get aiSuggest6Ask => 'Apa aturan tentang hak penyandang disabilitas?';

  @override
  String get aiNoAnswer => 'Maaf, saya belum bisa menjawab pertanyaan itu.';

  @override
  String get chatCleared => 'Percakapan dibersihkan.';

  @override
  String get undo => 'Urungkan';

  @override
  String get clearChat => 'Bersihkan percakapan';

  @override
  String chatLimitReached(int max) {
    return 'Batas $max pertanyaan tercapai';
  }

  @override
  String get askHint => 'Tulis pertanyaan Anda...';

  @override
  String get send => 'Kirim';

  @override
  String get aiDisclaimer =>
      'Jawaban dibuat otomatis oleh AI. Selalu periksa dokumen aslinya.';

  @override
  String get jdihAssistant => 'Asisten JDIH';

  @override
  String get aiGreeting =>
      'Halo! Tanyakan apa saja seputar hukum atau peraturan Kota Kendari. Saya akan menjawab sekaligus menunjukkan dokumen JDIH yang terkait.';

  @override
  String get tryAsking => 'Coba tanyakan';

  @override
  String get resend => 'Kirim ulang';

  @override
  String get aiStepUnderstand => 'Memahami pertanyaan';

  @override
  String get aiStepSearch => 'Menelusuri dokumen JDIH';

  @override
  String get aiStepMatch => 'Mencocokkan pasal terkait';

  @override
  String get aiStepCompose => 'Menyusun jawaban';

  @override
  String get aiSearching => 'Menelusuri dokumen';

  @override
  String get relatedDocs => 'Dokumen terkait';

  @override
  String docsAttached(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count dokumen dilampirkan',
    );
    return '$_temp0';
  }

  @override
  String percentRelevant(int pct) {
    return '$pct% relevan';
  }

  @override
  String chatFull(int max) {
    return 'Percakapan ini sudah mencapai $max pertanyaan. Mulai percakapan baru untuk bertanya lagi.';
  }

  @override
  String get newChat => 'Percakapan baru';

  @override
  String updatedAt(String date) {
    return 'Diperbarui $date.';
  }

  @override
  String get statsSource =>
      'Angka dihitung langsung dari basis data JDIH Kota Kendari.';

  @override
  String get totalLegalDocs => 'Total dokumen hukum';

  @override
  String get metaDownloads => 'Diunduh';

  @override
  String get docsPerYear => 'Dokumen per tahun';

  @override
  String get docsPerYearSub => 'Jumlah dokumen menurut tahun terbit';

  @override
  String labelDocs(String label, int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString dokumen',
    );
    return '$label: $_temp0';
  }

  @override
  String otherTypes(int n) {
    return 'Lainnya ($n jenis)';
  }

  @override
  String get docsPerType => 'Dokumen per jenis';

  @override
  String get docsPerTypeSub => 'Tujuh jenis dengan koleksi terbanyak';

  @override
  String get statusTitle => 'Status keberlakuan';

  @override
  String get statusSub => 'Bagian koleksi yang masih berlaku';

  @override
  String get statusNoData => 'Data status belum tersedia.';

  @override
  String labelDocsPct(String label, int count, String pct) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString dokumen',
    );
    return '$label: $_temp0, $pct';
  }

  @override
  String get mostViewed => 'Paling banyak dilihat';

  @override
  String get mostViewedSub => 'Lima dokumen dengan pembaca terbanyak';

  @override
  String get mostViewedEmpty =>
      'Belum ada dokumen yang tercatat dibaca. Angka ini mulai terisi setelah pengunjung membuka halaman dokumen.';

  @override
  String viewedTimes(int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString kali dilihat',
    );
    return '$_temp0';
  }

  @override
  String get userStudent => 'Mahasiswa';

  @override
  String get userAcademic => 'Akademisi';

  @override
  String get userPractitioner => 'Praktisi Hukum';

  @override
  String get userPublic => 'Masyarakat Umum';

  @override
  String get userOther => 'Lainnya';

  @override
  String get aspectAccess => 'Kemudahan Akses';

  @override
  String get aspectCompleteness => 'Kelengkapan Informasi';

  @override
  String get aspectSpeed => 'Kecepatan Loading';

  @override
  String get aspectInterface => 'Tampilan Antarmuka';

  @override
  String get aspectRelevance => 'Relevansi Pencarian';

  @override
  String get errNameRequired => 'Nama wajib diisi.';

  @override
  String get errEmailFormat =>
      'Format email belum benar, contoh: nama@contoh.go.id';

  @override
  String get errPickUserType => 'Pilih salah satu jenis pengguna.';

  @override
  String get errRateAll => 'Beri nilai untuk setiap aspek.';

  @override
  String get thankYou => 'Terima kasih!';

  @override
  String get surveySent => 'Survei Anda telah terkirim.';

  @override
  String get ok => 'OK';

  @override
  String get fieldName => 'Nama';

  @override
  String get fieldInstitution => 'Instansi';

  @override
  String get fieldUserType => 'Jenis pengguna';

  @override
  String get fieldRating => 'Penilaian layanan';

  @override
  String get fieldSuggestions => 'Saran Perbaikan';

  @override
  String get fieldFeatures => 'Fitur yang Diharapkan';

  @override
  String get fieldContactOk => 'Bersedia dihubungi';

  @override
  String get fieldContact => 'Kontak (HP/WA)';

  @override
  String get submitSurvey => 'Kirim Survei';

  @override
  String get surveyHeading => 'Bantu kami melayani lebih baik';

  @override
  String get surveyIntro =>
      'Penilaian Anda membantu kami memperbaiki layanan JDIH. Isian bertanda Opsional boleh dilewati.';

  @override
  String get surveyAbout => 'Tentang Anda';

  @override
  String get surveyFeedback => 'Masukan';

  @override
  String get optional => 'Opsional';

  @override
  String ratingWord(String n) {
    String _temp0 = intl.Intl.selectLogic(n, {
      '1': 'Sangat kurang',
      '2': 'Kurang',
      '3': 'Cukup',
      '4': 'Baik',
      'other': 'Sangat baik',
    });
    return '$_temp0';
  }

  @override
  String surveyProgress(int done, int total) {
    return '$done dari $total isian wajib';
  }

  @override
  String ratingTooltip(String label, int n) {
    return '$label, $n dari 5';
  }
}
