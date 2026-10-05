import 'dart:async';

import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:intl/intl.dart' as intl;

import 'app_localizations_en.dart';
import 'app_localizations_id.dart';
import 'app_localizations_ko.dart';
import 'app_localizations_zh.dart';

// ignore_for_file: type=lint

/// Callers can lookup localized strings with an instance of AppLocalizations
/// returned by `AppLocalizations.of(context)`.
///
/// Applications need to include `AppLocalizations.delegate()` in their app's
/// `localizationDelegates` list, and the locales they support in the app's
/// `supportedLocales` list. For example:
///
/// ```dart
/// import 'l10n/app_localizations.dart';
///
/// return MaterialApp(
///   localizationsDelegates: AppLocalizations.localizationsDelegates,
///   supportedLocales: AppLocalizations.supportedLocales,
///   home: MyApplicationHome(),
/// );
/// ```
///
/// ## Update pubspec.yaml
///
/// Please make sure to update your pubspec.yaml to include the following
/// packages:
///
/// ```yaml
/// dependencies:
///   # Internationalization support.
///   flutter_localizations:
///     sdk: flutter
///   intl: any # Use the pinned version from flutter_localizations
///
///   # Rest of dependencies
/// ```
///
/// ## iOS Applications
///
/// iOS applications define key application metadata, including supported
/// locales, in an Info.plist file that is built into the application bundle.
/// To configure the locales supported by your app, you’ll need to edit this
/// file.
///
/// First, open your project’s ios/Runner.xcworkspace Xcode workspace file.
/// Then, in the Project Navigator, open the Info.plist file under the Runner
/// project’s Runner folder.
///
/// Next, select the Information Property List item, select Add Item from the
/// Editor menu, then select Localizations from the pop-up menu.
///
/// Select and expand the newly-created Localizations item then, for each
/// locale your application supports, add a new item and select the locale
/// you wish to add from the pop-up menu in the Value field. This list should
/// be consistent with the languages listed in the AppLocalizations.supportedLocales
/// property.
abstract class AppLocalizations {
  AppLocalizations(String locale)
    : localeName = intl.Intl.canonicalizedLocale(locale.toString());

  final String localeName;

  static AppLocalizations of(BuildContext context) {
    return Localizations.of<AppLocalizations>(context, AppLocalizations)!;
  }

  static const LocalizationsDelegate<AppLocalizations> delegate =
      _AppLocalizationsDelegate();

  /// A list of this localizations delegate along with the default localizations
  /// delegates.
  ///
  /// Returns a list of localizations delegates containing this delegate along with
  /// GlobalMaterialLocalizations.delegate, GlobalCupertinoLocalizations.delegate,
  /// and GlobalWidgetsLocalizations.delegate.
  ///
  /// Additional delegates can be added by appending to this list in
  /// MaterialApp. This list does not have to be used at all if a custom list
  /// of delegates is preferred or required.
  static const List<LocalizationsDelegate<dynamic>> localizationsDelegates =
      <LocalizationsDelegate<dynamic>>[
        delegate,
        GlobalMaterialLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
      ];

  /// A list of this localizations delegate's supported locales.
  static const List<Locale> supportedLocales = <Locale>[
    Locale('en'),
    Locale('id'),
    Locale('ko'),
    Locale('zh'),
  ];

  /// No description provided for @appName.
  ///
  /// In id, this message translates to:
  /// **'JDIH Kota Kendari'**
  String get appName;

  /// No description provided for @navHome.
  ///
  /// In id, this message translates to:
  /// **'Beranda'**
  String get navHome;

  /// No description provided for @navDocuments.
  ///
  /// In id, this message translates to:
  /// **'Dokumen'**
  String get navDocuments;

  /// No description provided for @navAskAi.
  ///
  /// In id, this message translates to:
  /// **'Tanya AI'**
  String get navAskAi;

  /// No description provided for @navNews.
  ///
  /// In id, this message translates to:
  /// **'Kabar'**
  String get navNews;

  /// No description provided for @navMenu.
  ///
  /// In id, this message translates to:
  /// **'Menu'**
  String get navMenu;

  /// Label pembaca layar tombol Tanya AI
  ///
  /// In id, this message translates to:
  /// **'{label} dan pencarian'**
  String aiAndSearch(String label);

  /// No description provided for @errServer.
  ///
  /// In id, this message translates to:
  /// **'Server sedang bermasalah. Coba lagi sebentar lagi.'**
  String get errServer;

  /// No description provided for @errUnknownReply.
  ///
  /// In id, this message translates to:
  /// **'Server mengirim balasan yang tidak dikenali. Jika memakai Wi-Fi publik, pastikan sudah masuk (login) ke jaringannya.'**
  String get errUnknownReply;

  /// No description provided for @errTooMany.
  ///
  /// In id, this message translates to:
  /// **'Terlalu banyak permintaan dalam waktu singkat. Tunggu sebentar, lalu coba lagi.'**
  String get errTooMany;

  /// No description provided for @errInvalidInput.
  ///
  /// In id, this message translates to:
  /// **'Isian belum lengkap atau tidak sesuai. Periksa kembali, lalu kirim ulang.'**
  String get errInvalidInput;

  /// No description provided for @errNotFound.
  ///
  /// In id, this message translates to:
  /// **'Data yang dicari tidak ditemukan.'**
  String get errNotFound;

  /// No description provided for @errRequestFailed.
  ///
  /// In id, this message translates to:
  /// **'Permintaan tidak dapat diproses ({code}). Coba lagi.'**
  String errRequestFailed(int code);

  /// No description provided for @errTimeout.
  ///
  /// In id, this message translates to:
  /// **'Server lambat merespons. Coba lagi.'**
  String get errTimeout;

  /// No description provided for @errOffline.
  ///
  /// In id, this message translates to:
  /// **'Tidak dapat terhubung ke server JDIH. Periksa koneksi internet, lalu coba lagi.'**
  String get errOffline;

  /// No description provided for @minRead.
  ///
  /// In id, this message translates to:
  /// **'{n} menit baca'**
  String minRead(int n);

  /// No description provided for @linkOpenFailed.
  ///
  /// In id, this message translates to:
  /// **'Tidak dapat membuka tautan.'**
  String get linkOpenFailed;

  /// No description provided for @closeImage.
  ///
  /// In id, this message translates to:
  /// **'Tutup gambar'**
  String get closeImage;

  /// No description provided for @statusInForce.
  ///
  /// In id, this message translates to:
  /// **'Berlaku'**
  String get statusInForce;

  /// No description provided for @statusNotInForce.
  ///
  /// In id, this message translates to:
  /// **'Tidak Berlaku'**
  String get statusNotInForce;

  /// No description provided for @statusRevoked.
  ///
  /// In id, this message translates to:
  /// **'Dicabut'**
  String get statusRevoked;

  /// No description provided for @statusAmended.
  ///
  /// In id, this message translates to:
  /// **'Diubah'**
  String get statusAmended;

  /// No description provided for @legalDocument.
  ///
  /// In id, this message translates to:
  /// **'Dokumen Peraturan'**
  String get legalDocument;

  /// No description provided for @seeAll.
  ///
  /// In id, this message translates to:
  /// **'Lihat semua'**
  String get seeAll;

  /// No description provided for @language.
  ///
  /// In id, this message translates to:
  /// **'Bahasa'**
  String get language;

  /// No description provided for @notFound.
  ///
  /// In id, this message translates to:
  /// **'Tidak ditemukan'**
  String get notFound;

  /// No description provided for @noMatch.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada yang cocok dengan \"{query}\".'**
  String noMatch(String query);

  /// No description provided for @noMatchHint.
  ///
  /// In id, this message translates to:
  /// **'Coba kata kunci yang lebih pendek, periksa ejaannya, atau ganti kategori.'**
  String get noMatchHint;

  /// No description provided for @aiPromoBody.
  ///
  /// In id, this message translates to:
  /// **'Tanyakan dengan bahasa sehari-hari. Jawabannya berupa percakapan, lengkap dengan dokumen hukum yang relevan.'**
  String get aiPromoBody;

  /// No description provided for @aiPromoStart.
  ///
  /// In id, this message translates to:
  /// **'Mulai bertanya'**
  String get aiPromoStart;

  /// No description provided for @latest.
  ///
  /// In id, this message translates to:
  /// **'Terbaru'**
  String get latest;

  /// No description provided for @share.
  ///
  /// In id, this message translates to:
  /// **'Bagikan'**
  String get share;

  /// No description provided for @readLess.
  ///
  /// In id, this message translates to:
  /// **'Tutup'**
  String get readLess;

  /// No description provided for @readMore.
  ///
  /// In id, this message translates to:
  /// **'Selengkapnya'**
  String get readMore;

  /// No description provided for @retry.
  ///
  /// In id, this message translates to:
  /// **'Coba lagi'**
  String get retry;

  /// No description provided for @noData.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada data.'**
  String get noData;

  /// No description provided for @emptyTitle.
  ///
  /// In id, this message translates to:
  /// **'Belum ada isinya'**
  String get emptyTitle;

  /// No description provided for @reload.
  ///
  /// In id, this message translates to:
  /// **'Muat ulang'**
  String get reload;

  /// No description provided for @loadMore.
  ///
  /// In id, this message translates to:
  /// **'Muat lanjutan'**
  String get loadMore;

  /// No description provided for @catRegulations.
  ///
  /// In id, this message translates to:
  /// **'Peraturan & Keputusan'**
  String get catRegulations;

  /// No description provided for @catRegulationsShort.
  ///
  /// In id, this message translates to:
  /// **'Peraturan'**
  String get catRegulationsShort;

  /// No description provided for @catRegulationsSub.
  ///
  /// In id, this message translates to:
  /// **'Perda, Perwali, dan Keputusan Wali Kota'**
  String get catRegulationsSub;

  /// No description provided for @catMonographs.
  ///
  /// In id, this message translates to:
  /// **'Monografi Hukum'**
  String get catMonographs;

  /// No description provided for @catMonographsShort.
  ///
  /// In id, this message translates to:
  /// **'Monografi'**
  String get catMonographsShort;

  /// No description provided for @catMonographsSub.
  ///
  /// In id, this message translates to:
  /// **'Buku dan kajian hukum daerah'**
  String get catMonographsSub;

  /// No description provided for @catArticles.
  ///
  /// In id, this message translates to:
  /// **'Artikel / Majalah Hukum'**
  String get catArticles;

  /// No description provided for @catArticlesShort.
  ///
  /// In id, this message translates to:
  /// **'Artikel'**
  String get catArticlesShort;

  /// No description provided for @catArticlesSub.
  ///
  /// In id, this message translates to:
  /// **'Artikel dan majalah hukum'**
  String get catArticlesSub;

  /// No description provided for @catRulings.
  ///
  /// In id, this message translates to:
  /// **'Putusan'**
  String get catRulings;

  /// No description provided for @catRulingsShort.
  ///
  /// In id, this message translates to:
  /// **'Putusan'**
  String get catRulingsShort;

  /// No description provided for @catRulingsSub.
  ///
  /// In id, this message translates to:
  /// **'Putusan pengadilan terkait daerah'**
  String get catRulingsSub;

  /// No description provided for @latestDocuments.
  ///
  /// In id, this message translates to:
  /// **'Dokumen Terbaru'**
  String get latestDocuments;

  /// No description provided for @jdihNews.
  ///
  /// In id, this message translates to:
  /// **'Kabar JDIH'**
  String get jdihNews;

  /// No description provided for @collectionStats.
  ///
  /// In id, this message translates to:
  /// **'Statistik Koleksi'**
  String get collectionStats;

  /// No description provided for @greetMorning.
  ///
  /// In id, this message translates to:
  /// **'Selamat pagi'**
  String get greetMorning;

  /// No description provided for @greetMidday.
  ///
  /// In id, this message translates to:
  /// **'Selamat siang'**
  String get greetMidday;

  /// No description provided for @greetAfternoon.
  ///
  /// In id, this message translates to:
  /// **'Selamat sore'**
  String get greetAfternoon;

  /// No description provided for @greetEvening.
  ///
  /// In id, this message translates to:
  /// **'Selamat malam'**
  String get greetEvening;

  /// No description provided for @logoJdihn.
  ///
  /// In id, this message translates to:
  /// **'Logo JDIHN'**
  String get logoJdihn;

  /// No description provided for @homeHeadline.
  ///
  /// In id, this message translates to:
  /// **'Cari aturan apa hari ini?'**
  String get homeHeadline;

  /// No description provided for @kendariEmblem.
  ///
  /// In id, this message translates to:
  /// **'Lambang Kota Kendari'**
  String get kendariEmblem;

  /// Arti semboyan adat Tolaki
  ///
  /// In id, this message translates to:
  /// **'Siapa yang menghargai adat, ia akan dihormati'**
  String get adatMeaning;

  /// No description provided for @homeSearchHint.
  ///
  /// In id, this message translates to:
  /// **'Judul, nomor, atau pertanyaan...'**
  String get homeSearchHint;

  /// No description provided for @search.
  ///
  /// In id, this message translates to:
  /// **'Cari'**
  String get search;

  /// No description provided for @noAbbr.
  ///
  /// In id, this message translates to:
  /// **'No.'**
  String get noAbbr;

  /// No description provided for @views.
  ///
  /// In id, this message translates to:
  /// **'dilihat'**
  String get views;

  /// No description provided for @downloads.
  ///
  /// In id, this message translates to:
  /// **'diunduh'**
  String get downloads;

  /// No description provided for @pickDocument.
  ///
  /// In id, this message translates to:
  /// **'Pilih dokumen'**
  String get pickDocument;

  /// No description provided for @pickDocumentHint.
  ///
  /// In id, this message translates to:
  /// **'Ketuk dokumen di daftar untuk membaca rinciannya di sini, berdampingan dengan hasil lainnya.'**
  String get pickDocumentHint;

  /// No description provided for @legalDocuments.
  ///
  /// In id, this message translates to:
  /// **'Dokumen Hukum'**
  String get legalDocuments;

  /// No description provided for @latestLegalProducts.
  ///
  /// In id, this message translates to:
  /// **'Produk Hukum Terbaru'**
  String get latestLegalProducts;

  /// No description provided for @docsAvailable.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, other{{count} dokumen tersedia}}'**
  String docsAvailable(int count);

  /// No description provided for @filterAllTypes.
  ///
  /// In id, this message translates to:
  /// **'Semua Jenis'**
  String get filterAllTypes;

  /// No description provided for @filterAllYears.
  ///
  /// In id, this message translates to:
  /// **'Semua Tahun'**
  String get filterAllYears;

  /// No description provided for @filterAllStatus.
  ///
  /// In id, this message translates to:
  /// **'Semua Status'**
  String get filterAllStatus;

  /// No description provided for @type.
  ///
  /// In id, this message translates to:
  /// **'Jenis'**
  String get type;

  /// No description provided for @year.
  ///
  /// In id, this message translates to:
  /// **'Tahun'**
  String get year;

  /// No description provided for @status.
  ///
  /// In id, this message translates to:
  /// **'Status'**
  String get status;

  /// No description provided for @number.
  ///
  /// In id, this message translates to:
  /// **'Nomor'**
  String get number;

  /// No description provided for @searchTitleHint.
  ///
  /// In id, this message translates to:
  /// **'Cari judul...'**
  String get searchTitleHint;

  /// No description provided for @reset.
  ///
  /// In id, this message translates to:
  /// **'Reset'**
  String get reset;

  /// No description provided for @docCount.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, other{{count} dokumen}}'**
  String docCount(int count);

  /// No description provided for @docCountFiltered.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, other{{count} dokumen sesuai filter}}'**
  String docCountFiltered(int count);

  /// No description provided for @emptyCategory.
  ///
  /// In id, this message translates to:
  /// **'Belum ada dokumen pada kategori ini.'**
  String get emptyCategory;

  /// No description provided for @loosenFilters.
  ///
  /// In id, this message translates to:
  /// **'Longgarkan filternya atau periksa ejaan kata kuncinya.'**
  String get loosenFilters;

  /// No description provided for @resetFilter.
  ///
  /// In id, this message translates to:
  /// **'Reset filter'**
  String get resetFilter;

  /// No description provided for @back.
  ///
  /// In id, this message translates to:
  /// **'Kembali'**
  String get back;

  /// No description provided for @numberValue.
  ///
  /// In id, this message translates to:
  /// **'Nomor {nomor}'**
  String numberValue(String nomor);

  /// No description provided for @yearValue.
  ///
  /// In id, this message translates to:
  /// **'Tahun {tahun}'**
  String yearValue(String tahun);

  /// No description provided for @tabAbout.
  ///
  /// In id, this message translates to:
  /// **'Tentang'**
  String get tabAbout;

  /// No description provided for @tabFiles.
  ///
  /// In id, this message translates to:
  /// **'Berkas ({count})'**
  String tabFiles(int count);

  /// No description provided for @tabRelated.
  ///
  /// In id, this message translates to:
  /// **'Terkait'**
  String get tabRelated;

  /// No description provided for @aboutDocument.
  ///
  /// In id, this message translates to:
  /// **'Tentang Dokumen'**
  String get aboutDocument;

  /// No description provided for @abstract.
  ///
  /// In id, this message translates to:
  /// **'Abstrak'**
  String get abstract;

  /// No description provided for @abstractOf.
  ///
  /// In id, this message translates to:
  /// **'Abstrak {judul}'**
  String abstractOf(String judul);

  /// Dasar nama berkas unduhan
  ///
  /// In id, this message translates to:
  /// **'abstrak {judul}'**
  String fileAbstract(String judul);

  /// No description provided for @metaForm.
  ///
  /// In id, this message translates to:
  /// **'Bentuk'**
  String get metaForm;

  /// No description provided for @metaPlacePublished.
  ///
  /// In id, this message translates to:
  /// **'Tempat Terbit'**
  String get metaPlacePublished;

  /// No description provided for @publisher.
  ///
  /// In id, this message translates to:
  /// **'Penerbit'**
  String get publisher;

  /// No description provided for @metaDateEnacted.
  ///
  /// In id, this message translates to:
  /// **'Tgl. Penetapan'**
  String get metaDateEnacted;

  /// No description provided for @metaDatePromulgated.
  ///
  /// In id, this message translates to:
  /// **'Tgl. Pengundangan'**
  String get metaDatePromulgated;

  /// No description provided for @source.
  ///
  /// In id, this message translates to:
  /// **'Sumber'**
  String get source;

  /// No description provided for @metaLegalField.
  ///
  /// In id, this message translates to:
  /// **'Bidang Hukum'**
  String get metaLegalField;

  /// No description provided for @metaSignatory.
  ///
  /// In id, this message translates to:
  /// **'Penandatangan'**
  String get metaSignatory;

  /// No description provided for @metaPhysicalDesc.
  ///
  /// In id, this message translates to:
  /// **'Deskripsi Fisik'**
  String get metaPhysicalDesc;

  /// No description provided for @metaCourt.
  ///
  /// In id, this message translates to:
  /// **'Lembaga Peradilan'**
  String get metaCourt;

  /// No description provided for @metaPetitioner.
  ///
  /// In id, this message translates to:
  /// **'Pemohon'**
  String get metaPetitioner;

  /// No description provided for @metaRespondent.
  ///
  /// In id, this message translates to:
  /// **'Termohon'**
  String get metaRespondent;

  /// No description provided for @metaCaseType.
  ///
  /// In id, this message translates to:
  /// **'Jenis Perkara'**
  String get metaCaseType;

  /// No description provided for @metaTeu.
  ///
  /// In id, this message translates to:
  /// **'T.E.U.'**
  String get metaTeu;

  /// No description provided for @subject.
  ///
  /// In id, this message translates to:
  /// **'Subjek'**
  String get subject;

  /// No description provided for @author.
  ///
  /// In id, this message translates to:
  /// **'Pengarang'**
  String get author;

  /// No description provided for @noFiles.
  ///
  /// In id, this message translates to:
  /// **'Belum ada berkas'**
  String get noFiles;

  /// No description provided for @noFilesHint.
  ///
  /// In id, this message translates to:
  /// **'Dokumen ini belum memiliki file yang bisa dilihat atau diunduh. Coba periksa peraturan terkait.'**
  String get noFilesHint;

  /// No description provided for @document.
  ///
  /// In id, this message translates to:
  /// **'Dokumen'**
  String get document;

  /// Dasar nama berkas unduhan
  ///
  /// In id, this message translates to:
  /// **'{judul} lampiran {n}'**
  String fileAttachmentN(String judul, int n);

  /// No description provided for @noRelated.
  ///
  /// In id, this message translates to:
  /// **'Tidak ada peraturan terkait'**
  String get noRelated;

  /// No description provided for @noRelatedHint.
  ///
  /// In id, this message translates to:
  /// **'Dokumen ini berdiri sendiri: tidak mengubah, mencabut, atau diubah oleh peraturan lain.'**
  String get noRelatedHint;

  /// No description provided for @statistics.
  ///
  /// In id, this message translates to:
  /// **'Statistik'**
  String get statistics;

  /// No description provided for @viewsDownloads.
  ///
  /// In id, this message translates to:
  /// **'{views} dilihat · {downloads} unduh'**
  String viewsDownloads(String views, String downloads);

  /// No description provided for @fileUnavailable.
  ///
  /// In id, this message translates to:
  /// **'Berkas tidak tersedia'**
  String get fileUnavailable;

  /// No description provided for @viewDocument.
  ///
  /// In id, this message translates to:
  /// **'Lihat Dokumen'**
  String get viewDocument;

  /// No description provided for @downloadDocument.
  ///
  /// In id, this message translates to:
  /// **'Unduh Dokumen'**
  String get downloadDocument;

  /// No description provided for @fileKindPdf.
  ///
  /// In id, this message translates to:
  /// **'Dokumen PDF · dapat dibaca di aplikasi'**
  String get fileKindPdf;

  /// No description provided for @fileKindOther.
  ///
  /// In id, this message translates to:
  /// **'Berkas {ext} · unduh untuk membuka'**
  String fileKindOther(String ext);

  /// No description provided for @fileGeneric.
  ///
  /// In id, this message translates to:
  /// **'Berkas'**
  String get fileGeneric;

  /// No description provided for @downloading.
  ///
  /// In id, this message translates to:
  /// **'Mengunduh {name}...'**
  String downloading(String name);

  /// No description provided for @downloadFailed.
  ///
  /// In id, this message translates to:
  /// **'Berkas gagal diunduh. Periksa koneksi dan ruang penyimpanan, lalu coba lagi.'**
  String get downloadFailed;

  /// No description provided for @goToPage.
  ///
  /// In id, this message translates to:
  /// **'Buka halaman'**
  String get goToPage;

  /// No description provided for @pageNumber.
  ///
  /// In id, this message translates to:
  /// **'Nomor halaman'**
  String get pageNumber;

  /// No description provided for @cancel.
  ///
  /// In id, this message translates to:
  /// **'Batal'**
  String get cancel;

  /// No description provided for @go.
  ///
  /// In id, this message translates to:
  /// **'Buka'**
  String get go;

  /// No description provided for @pageOutOfRange.
  ///
  /// In id, this message translates to:
  /// **'Dokumen ini hanya punya halaman 1 sampai {total}.'**
  String pageOutOfRange(int total);

  /// No description provided for @download.
  ///
  /// In id, this message translates to:
  /// **'Unduh'**
  String get download;

  /// No description provided for @pdfCannotDisplay.
  ///
  /// In id, this message translates to:
  /// **'Dokumen ini tidak dapat ditampilkan di aplikasi, tetapi masih bisa diunduh dan dibuka dengan aplikasi pembaca PDF.'**
  String get pdfCannotDisplay;

  /// No description provided for @downloadInstead.
  ///
  /// In id, this message translates to:
  /// **'Unduh saja'**
  String get downloadInstead;

  /// No description provided for @prevPage.
  ///
  /// In id, this message translates to:
  /// **'Halaman sebelumnya'**
  String get prevPage;

  /// No description provided for @nextPage.
  ///
  /// In id, this message translates to:
  /// **'Halaman berikutnya'**
  String get nextPage;

  /// No description provided for @pageOf.
  ///
  /// In id, this message translates to:
  /// **'Halaman {page} dari {total}'**
  String pageOf(int page, int total);

  /// No description provided for @view.
  ///
  /// In id, this message translates to:
  /// **'Lihat'**
  String get view;

  /// No description provided for @newsAndInfo.
  ///
  /// In id, this message translates to:
  /// **'Kabar & Informasi'**
  String get newsAndInfo;

  /// No description provided for @news.
  ///
  /// In id, this message translates to:
  /// **'Berita'**
  String get news;

  /// No description provided for @announcements.
  ///
  /// In id, this message translates to:
  /// **'Pengumuman'**
  String get announcements;

  /// No description provided for @announcement.
  ///
  /// In id, this message translates to:
  /// **'Pengumuman'**
  String get announcement;

  /// No description provided for @video.
  ///
  /// In id, this message translates to:
  /// **'Video'**
  String get video;

  /// No description provided for @legalInfoShort.
  ///
  /// In id, this message translates to:
  /// **'Info Hukum'**
  String get legalInfoShort;

  /// No description provided for @legalInfo.
  ///
  /// In id, this message translates to:
  /// **'Informasi Hukum'**
  String get legalInfo;

  /// No description provided for @clearSearch.
  ///
  /// In id, this message translates to:
  /// **'Hapus pencarian'**
  String get clearSearch;

  /// No description provided for @noMatchHintShort.
  ///
  /// In id, this message translates to:
  /// **'Coba kata kunci yang lebih pendek, atau periksa ejaannya.'**
  String get noMatchHintShort;

  /// No description provided for @searchNewsHint.
  ///
  /// In id, this message translates to:
  /// **'Cari judul berita...'**
  String get searchNewsHint;

  /// No description provided for @emptyNews.
  ///
  /// In id, this message translates to:
  /// **'Belum ada berita yang dipublikasikan.'**
  String get emptyNews;

  /// No description provided for @searchAnnouncementsHint.
  ///
  /// In id, this message translates to:
  /// **'Cari judul pengumuman...'**
  String get searchAnnouncementsHint;

  /// No description provided for @emptyAnnouncements.
  ///
  /// In id, this message translates to:
  /// **'Belum ada pengumuman.'**
  String get emptyAnnouncements;

  /// No description provided for @emptyVideos.
  ///
  /// In id, this message translates to:
  /// **'Belum ada video di kanal JDIH.'**
  String get emptyVideos;

  /// No description provided for @moreNews.
  ///
  /// In id, this message translates to:
  /// **'Berita Lainnya'**
  String get moreNews;

  /// No description provided for @moreAnnouncements.
  ///
  /// In id, this message translates to:
  /// **'Pengumuman Lainnya'**
  String get moreAnnouncements;

  /// No description provided for @moreVideos.
  ///
  /// In id, this message translates to:
  /// **'Video Lainnya'**
  String get moreVideos;

  /// No description provided for @enlargeCover.
  ///
  /// In id, this message translates to:
  /// **'Perbesar gambar sampul'**
  String get enlargeCover;

  /// No description provided for @tapToEnlarge.
  ///
  /// In id, this message translates to:
  /// **'Ketuk untuk perbesar'**
  String get tapToEnlarge;

  /// No description provided for @attachment.
  ///
  /// In id, this message translates to:
  /// **'Lampiran'**
  String get attachment;

  /// No description provided for @announcementAttachment.
  ///
  /// In id, this message translates to:
  /// **'Lampiran Pengumuman'**
  String get announcementAttachment;

  /// Dasar nama berkas unduhan
  ///
  /// In id, this message translates to:
  /// **'lampiran {judul}'**
  String fileAttachmentOf(String judul);

  /// Dasar nama berkas unduhan
  ///
  /// In id, this message translates to:
  /// **'dokumen {judul}'**
  String fileDocumentOf(String judul);

  /// No description provided for @all.
  ///
  /// In id, this message translates to:
  /// **'Semua'**
  String get all;

  /// No description provided for @emptyLegalInfo.
  ///
  /// In id, this message translates to:
  /// **'Naskah akademik, rancangan peraturan, dan kajian hukum akan tampil di sini begitu dipublikasikan.'**
  String get emptyLegalInfo;

  /// No description provided for @emptyCategoryTitle.
  ///
  /// In id, this message translates to:
  /// **'Kategori ini masih kosong'**
  String get emptyCategoryTitle;

  /// No description provided for @emptySelectedCategory.
  ///
  /// In id, this message translates to:
  /// **'Belum ada dokumen pada kategori yang dipilih.'**
  String get emptySelectedCategory;

  /// No description provided for @emptyTypeNamed.
  ///
  /// In id, this message translates to:
  /// **'Belum ada dokumen berjenis {jenis}. Kategori lain mungkin sudah terisi.'**
  String emptyTypeNamed(String jenis);

  /// No description provided for @seeAllCategories.
  ///
  /// In id, this message translates to:
  /// **'Lihat semua kategori'**
  String get seeAllCategories;

  /// No description provided for @linkCopied.
  ///
  /// In id, this message translates to:
  /// **'Tautan video disalin.'**
  String get linkCopied;

  /// No description provided for @videoNotPlayable.
  ///
  /// In id, this message translates to:
  /// **'Video ini tersimpan di luar YouTube dan belum bisa diputar di aplikasi.'**
  String get videoNotPlayable;

  /// No description provided for @openInYoutube.
  ///
  /// In id, this message translates to:
  /// **'Buka di YouTube'**
  String get openInYoutube;

  /// No description provided for @openVideoLink.
  ///
  /// In id, this message translates to:
  /// **'Buka tautan video'**
  String get openVideoLink;

  /// No description provided for @copy.
  ///
  /// In id, this message translates to:
  /// **'Salin'**
  String get copy;

  /// No description provided for @profileHistory.
  ///
  /// In id, this message translates to:
  /// **'Sekilas Sejarah'**
  String get profileHistory;

  /// No description provided for @profileLegalBasis.
  ///
  /// In id, this message translates to:
  /// **'Dasar Hukum'**
  String get profileLegalBasis;

  /// No description provided for @profileVision.
  ///
  /// In id, this message translates to:
  /// **'Visi'**
  String get profileVision;

  /// No description provided for @profileMission.
  ///
  /// In id, this message translates to:
  /// **'Misi'**
  String get profileMission;

  /// No description provided for @profileStructure.
  ///
  /// In id, this message translates to:
  /// **'Struktur Organisasi'**
  String get profileStructure;

  /// No description provided for @more.
  ///
  /// In id, this message translates to:
  /// **'Lainnya'**
  String get more;

  /// No description provided for @jdihProfile.
  ///
  /// In id, this message translates to:
  /// **'Profil JDIH'**
  String get jdihProfile;

  /// No description provided for @disabilityServices.
  ///
  /// In id, this message translates to:
  /// **'Layanan Disabilitas'**
  String get disabilityServices;

  /// No description provided for @lawMaking.
  ///
  /// In id, this message translates to:
  /// **'Pembentukan PUU'**
  String get lawMaking;

  /// No description provided for @satisfactionSurvey.
  ///
  /// In id, this message translates to:
  /// **'Survei Kepuasan'**
  String get satisfactionSurvey;

  /// No description provided for @aboutJdih.
  ///
  /// In id, this message translates to:
  /// **'Tentang JDIH'**
  String get aboutJdih;

  /// No description provided for @orgStructureOnWeb.
  ///
  /// In id, this message translates to:
  /// **'Konten struktur organisasi tersedia di situs web JDIH Kota Kendari.'**
  String get orgStructureOnWeb;

  /// No description provided for @jdihFull.
  ///
  /// In id, this message translates to:
  /// **'Jaringan Dokumentasi dan Informasi Hukum'**
  String get jdihFull;

  /// No description provided for @phone.
  ///
  /// In id, this message translates to:
  /// **'Telepon'**
  String get phone;

  /// No description provided for @email.
  ///
  /// In id, this message translates to:
  /// **'Email'**
  String get email;

  /// No description provided for @address.
  ///
  /// In id, this message translates to:
  /// **'Alamat'**
  String get address;

  /// No description provided for @socialMedia.
  ///
  /// In id, this message translates to:
  /// **'Media Sosial'**
  String get socialMedia;

  /// No description provided for @appLabel.
  ///
  /// In id, this message translates to:
  /// **'Aplikasi JDIH Kota Kendari'**
  String get appLabel;

  /// No description provided for @loadingApp.
  ///
  /// In id, this message translates to:
  /// **'{app}, sedang memuat'**
  String loadingApp(String app);

  /// No description provided for @kendariGov.
  ///
  /// In id, this message translates to:
  /// **'Pemerintah Kota Kendari'**
  String get kendariGov;

  /// No description provided for @puuAcademicPaper.
  ///
  /// In id, this message translates to:
  /// **'Naskah Akademik'**
  String get puuAcademicPaper;

  /// No description provided for @puuExplanatory.
  ///
  /// In id, this message translates to:
  /// **'Naskah Keterangan/Penjelasan'**
  String get puuExplanatory;

  /// No description provided for @puuDraft.
  ///
  /// In id, this message translates to:
  /// **'Rancangan PUU'**
  String get puuDraft;

  /// No description provided for @puuResearch.
  ///
  /// In id, this message translates to:
  /// **'Penelitian Hukum'**
  String get puuResearch;

  /// No description provided for @puuLegalReview.
  ///
  /// In id, this message translates to:
  /// **'Pengkajian Hukum'**
  String get puuLegalReview;

  /// No description provided for @puuConstitutionalReview.
  ///
  /// In id, this message translates to:
  /// **'Pengkajian Konstitusi'**
  String get puuConstitutionalReview;

  /// No description provided for @puuAnalysis.
  ///
  /// In id, this message translates to:
  /// **'Analisis & Evaluasi'**
  String get puuAnalysis;

  /// No description provided for @secBackground.
  ///
  /// In id, this message translates to:
  /// **'Latar Belakang'**
  String get secBackground;

  /// No description provided for @secProblem.
  ///
  /// In id, this message translates to:
  /// **'Rumusan Masalah'**
  String get secProblem;

  /// No description provided for @secObjective.
  ///
  /// In id, this message translates to:
  /// **'Tujuan Penelitian'**
  String get secObjective;

  /// No description provided for @secMethod.
  ///
  /// In id, this message translates to:
  /// **'Metodologi'**
  String get secMethod;

  /// No description provided for @secFocus.
  ///
  /// In id, this message translates to:
  /// **'Fokus Penelitian'**
  String get secFocus;

  /// No description provided for @secResults.
  ///
  /// In id, this message translates to:
  /// **'Hasil Penelitian'**
  String get secResults;

  /// No description provided for @secReviewObject.
  ///
  /// In id, this message translates to:
  /// **'Objek Pengkajian'**
  String get secReviewObject;

  /// No description provided for @secReviewConclusion.
  ///
  /// In id, this message translates to:
  /// **'Kesimpulan Pengkajian'**
  String get secReviewConclusion;

  /// No description provided for @secConstitutional.
  ///
  /// In id, this message translates to:
  /// **'Aspek Konstitusi'**
  String get secConstitutional;

  /// No description provided for @secEvalFindings.
  ///
  /// In id, this message translates to:
  /// **'Temuan Evaluasi'**
  String get secEvalFindings;

  /// No description provided for @secRecommendation.
  ///
  /// In id, this message translates to:
  /// **'Rekomendasi'**
  String get secRecommendation;

  /// No description provided for @secImprovement.
  ///
  /// In id, this message translates to:
  /// **'Rekomendasi Perbaikan'**
  String get secImprovement;

  /// No description provided for @secNotes.
  ///
  /// In id, this message translates to:
  /// **'Keterangan'**
  String get secNotes;

  /// No description provided for @metaInitiator.
  ///
  /// In id, this message translates to:
  /// **'Lembaga Pemrakarsa'**
  String get metaInitiator;

  /// No description provided for @metaStage.
  ///
  /// In id, this message translates to:
  /// **'Tahapan'**
  String get metaStage;

  /// No description provided for @metaWriter.
  ///
  /// In id, this message translates to:
  /// **'Penulis'**
  String get metaWriter;

  /// No description provided for @metaEditor.
  ///
  /// In id, this message translates to:
  /// **'Editor'**
  String get metaEditor;

  /// No description provided for @metaKeywords.
  ///
  /// In id, this message translates to:
  /// **'Kata Kunci'**
  String get metaKeywords;

  /// No description provided for @metaViews.
  ///
  /// In id, this message translates to:
  /// **'Dilihat'**
  String get metaViews;

  /// No description provided for @mainDocument.
  ///
  /// In id, this message translates to:
  /// **'Dokumen Utama'**
  String get mainDocument;

  /// No description provided for @timesCount.
  ///
  /// In id, this message translates to:
  /// **'{n} kali'**
  String timesCount(int n);

  /// No description provided for @searchDisabilityHint.
  ///
  /// In id, this message translates to:
  /// **'Cari dokumen disabilitas...'**
  String get searchDisabilityHint;

  /// No description provided for @metaPlaceEnacted.
  ///
  /// In id, this message translates to:
  /// **'Tempat Penetapan'**
  String get metaPlaceEnacted;

  /// No description provided for @metaEnactedBy.
  ///
  /// In id, this message translates to:
  /// **'Lembaga Penetap'**
  String get metaEnactedBy;

  /// No description provided for @metaDisabilityType.
  ///
  /// In id, this message translates to:
  /// **'Jenis Disabilitas'**
  String get metaDisabilityType;

  /// No description provided for @metaScope.
  ///
  /// In id, this message translates to:
  /// **'Ruang Lingkup'**
  String get metaScope;

  /// No description provided for @metaPolicySector.
  ///
  /// In id, this message translates to:
  /// **'Sektor Kebijakan'**
  String get metaPolicySector;

  /// No description provided for @metaPages.
  ///
  /// In id, this message translates to:
  /// **'Jumlah Halaman'**
  String get metaPages;

  /// No description provided for @smartSearch.
  ///
  /// In id, this message translates to:
  /// **'Cari Pintar'**
  String get smartSearch;

  /// No description provided for @textSearch.
  ///
  /// In id, this message translates to:
  /// **'Pencarian Teks'**
  String get textSearch;

  /// No description provided for @searchDocsHint.
  ///
  /// In id, this message translates to:
  /// **'Cari judul, nomor, atau topik...'**
  String get searchDocsHint;

  /// No description provided for @resultsFor.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, other{{count} hasil untuk \"{query}\"}}'**
  String resultsFor(int count, String query);

  /// No description provided for @noMatchHintAi.
  ///
  /// In id, this message translates to:
  /// **'Coba kata kunci yang lebih pendek, ganti kategori di atas, atau tanyakan langsung ke AI.'**
  String get noMatchHintAi;

  /// No description provided for @searchLegalProducts.
  ///
  /// In id, this message translates to:
  /// **'Cari Produk Hukum'**
  String get searchLegalProducts;

  /// No description provided for @searchIntro.
  ///
  /// In id, this message translates to:
  /// **'Ketik judul, nomor, atau topik peraturan lalu tekan tombol cari pada papan ketik. Kategori di atas mempersempit hasilnya.'**
  String get searchIntro;

  /// No description provided for @aiSuggest1.
  ///
  /// In id, this message translates to:
  /// **'Retribusi sampah'**
  String get aiSuggest1;

  /// No description provided for @aiSuggest1Ask.
  ///
  /// In id, this message translates to:
  /// **'Apa aturan retribusi sampah?'**
  String get aiSuggest1Ask;

  /// No description provided for @aiSuggest2.
  ///
  /// In id, this message translates to:
  /// **'Perwali terbaru'**
  String get aiSuggest2;

  /// No description provided for @aiSuggest2Ask.
  ///
  /// In id, this message translates to:
  /// **'Perwali terbaru tentang apa?'**
  String get aiSuggest2Ask;

  /// No description provided for @aiSuggest3.
  ///
  /// In id, this message translates to:
  /// **'Mengurus IMB/PBG'**
  String get aiSuggest3;

  /// No description provided for @aiSuggest3Ask.
  ///
  /// In id, this message translates to:
  /// **'Bagaimana cara mengurus IMB?'**
  String get aiSuggest3Ask;

  /// No description provided for @aiSuggest4.
  ///
  /// In id, this message translates to:
  /// **'Pajak daerah'**
  String get aiSuggest4;

  /// No description provided for @aiSuggest4Ask.
  ///
  /// In id, this message translates to:
  /// **'Apa saja jenis pajak daerah Kota Kendari?'**
  String get aiSuggest4Ask;

  /// No description provided for @aiSuggest5.
  ///
  /// In id, this message translates to:
  /// **'APBD'**
  String get aiSuggest5;

  /// No description provided for @aiSuggest5Ask.
  ///
  /// In id, this message translates to:
  /// **'Apa isi peraturan daerah tentang APBD terbaru?'**
  String get aiSuggest5Ask;

  /// No description provided for @aiSuggest6.
  ///
  /// In id, this message translates to:
  /// **'Hak disabilitas'**
  String get aiSuggest6;

  /// No description provided for @aiSuggest6Ask.
  ///
  /// In id, this message translates to:
  /// **'Apa aturan tentang hak penyandang disabilitas?'**
  String get aiSuggest6Ask;

  /// No description provided for @aiNoAnswer.
  ///
  /// In id, this message translates to:
  /// **'Maaf, saya belum bisa menjawab pertanyaan itu.'**
  String get aiNoAnswer;

  /// No description provided for @chatCleared.
  ///
  /// In id, this message translates to:
  /// **'Percakapan dibersihkan.'**
  String get chatCleared;

  /// No description provided for @undo.
  ///
  /// In id, this message translates to:
  /// **'Urungkan'**
  String get undo;

  /// No description provided for @clearChat.
  ///
  /// In id, this message translates to:
  /// **'Bersihkan percakapan'**
  String get clearChat;

  /// No description provided for @chatLimitReached.
  ///
  /// In id, this message translates to:
  /// **'Batas {max} pertanyaan tercapai'**
  String chatLimitReached(int max);

  /// No description provided for @askHint.
  ///
  /// In id, this message translates to:
  /// **'Tulis pertanyaan Anda...'**
  String get askHint;

  /// No description provided for @send.
  ///
  /// In id, this message translates to:
  /// **'Kirim'**
  String get send;

  /// No description provided for @aiDisclaimer.
  ///
  /// In id, this message translates to:
  /// **'Jawaban dibuat otomatis oleh AI. Selalu periksa dokumen aslinya.'**
  String get aiDisclaimer;

  /// No description provided for @jdihAssistant.
  ///
  /// In id, this message translates to:
  /// **'Asisten JDIH'**
  String get jdihAssistant;

  /// No description provided for @aiGreeting.
  ///
  /// In id, this message translates to:
  /// **'Halo! Tanyakan apa saja seputar hukum atau peraturan Kota Kendari. Saya akan menjawab sekaligus menunjukkan dokumen JDIH yang terkait.'**
  String get aiGreeting;

  /// No description provided for @tryAsking.
  ///
  /// In id, this message translates to:
  /// **'Coba tanyakan'**
  String get tryAsking;

  /// No description provided for @resend.
  ///
  /// In id, this message translates to:
  /// **'Kirim ulang'**
  String get resend;

  /// No description provided for @aiStepUnderstand.
  ///
  /// In id, this message translates to:
  /// **'Memahami pertanyaan'**
  String get aiStepUnderstand;

  /// No description provided for @aiStepSearch.
  ///
  /// In id, this message translates to:
  /// **'Menelusuri dokumen JDIH'**
  String get aiStepSearch;

  /// No description provided for @aiStepMatch.
  ///
  /// In id, this message translates to:
  /// **'Mencocokkan pasal terkait'**
  String get aiStepMatch;

  /// No description provided for @aiStepCompose.
  ///
  /// In id, this message translates to:
  /// **'Menyusun jawaban'**
  String get aiStepCompose;

  /// No description provided for @aiSearching.
  ///
  /// In id, this message translates to:
  /// **'Menelusuri dokumen'**
  String get aiSearching;

  /// No description provided for @relatedDocs.
  ///
  /// In id, this message translates to:
  /// **'Dokumen terkait'**
  String get relatedDocs;

  /// No description provided for @docsAttached.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, other{{count} dokumen dilampirkan}}'**
  String docsAttached(int count);

  /// No description provided for @percentRelevant.
  ///
  /// In id, this message translates to:
  /// **'{pct}% relevan'**
  String percentRelevant(int pct);

  /// No description provided for @chatFull.
  ///
  /// In id, this message translates to:
  /// **'Percakapan ini sudah mencapai {max} pertanyaan. Mulai percakapan baru untuk bertanya lagi.'**
  String chatFull(int max);

  /// No description provided for @newChat.
  ///
  /// In id, this message translates to:
  /// **'Percakapan baru'**
  String get newChat;

  /// No description provided for @updatedAt.
  ///
  /// In id, this message translates to:
  /// **'Diperbarui {date}.'**
  String updatedAt(String date);

  /// No description provided for @statsSource.
  ///
  /// In id, this message translates to:
  /// **'Angka dihitung langsung dari basis data JDIH Kota Kendari.'**
  String get statsSource;

  /// No description provided for @totalLegalDocs.
  ///
  /// In id, this message translates to:
  /// **'Total dokumen hukum'**
  String get totalLegalDocs;

  /// No description provided for @metaDownloads.
  ///
  /// In id, this message translates to:
  /// **'Diunduh'**
  String get metaDownloads;

  /// No description provided for @docsPerYear.
  ///
  /// In id, this message translates to:
  /// **'Dokumen per tahun'**
  String get docsPerYear;

  /// No description provided for @docsPerYearSub.
  ///
  /// In id, this message translates to:
  /// **'Jumlah dokumen menurut tahun terbit'**
  String get docsPerYearSub;

  /// No description provided for @labelDocs.
  ///
  /// In id, this message translates to:
  /// **'{label}: {count, plural, other{{count} dokumen}}'**
  String labelDocs(String label, int count);

  /// No description provided for @otherTypes.
  ///
  /// In id, this message translates to:
  /// **'Lainnya ({n} jenis)'**
  String otherTypes(int n);

  /// No description provided for @docsPerType.
  ///
  /// In id, this message translates to:
  /// **'Dokumen per jenis'**
  String get docsPerType;

  /// No description provided for @docsPerTypeSub.
  ///
  /// In id, this message translates to:
  /// **'Tujuh jenis dengan koleksi terbanyak'**
  String get docsPerTypeSub;

  /// No description provided for @statusTitle.
  ///
  /// In id, this message translates to:
  /// **'Status keberlakuan'**
  String get statusTitle;

  /// No description provided for @statusSub.
  ///
  /// In id, this message translates to:
  /// **'Bagian koleksi yang masih berlaku'**
  String get statusSub;

  /// No description provided for @statusNoData.
  ///
  /// In id, this message translates to:
  /// **'Data status belum tersedia.'**
  String get statusNoData;

  /// No description provided for @labelDocsPct.
  ///
  /// In id, this message translates to:
  /// **'{label}: {count, plural, other{{count} dokumen}}, {pct}'**
  String labelDocsPct(String label, int count, String pct);

  /// No description provided for @mostViewed.
  ///
  /// In id, this message translates to:
  /// **'Paling banyak dilihat'**
  String get mostViewed;

  /// No description provided for @mostViewedSub.
  ///
  /// In id, this message translates to:
  /// **'Lima dokumen dengan pembaca terbanyak'**
  String get mostViewedSub;

  /// No description provided for @mostViewedEmpty.
  ///
  /// In id, this message translates to:
  /// **'Belum ada dokumen yang tercatat dibaca. Angka ini mulai terisi setelah pengunjung membuka halaman dokumen.'**
  String get mostViewedEmpty;

  /// No description provided for @viewedTimes.
  ///
  /// In id, this message translates to:
  /// **'{count, plural, other{{count} kali dilihat}}'**
  String viewedTimes(int count);

  /// No description provided for @userStudent.
  ///
  /// In id, this message translates to:
  /// **'Mahasiswa'**
  String get userStudent;

  /// No description provided for @userAcademic.
  ///
  /// In id, this message translates to:
  /// **'Akademisi'**
  String get userAcademic;

  /// No description provided for @userPractitioner.
  ///
  /// In id, this message translates to:
  /// **'Praktisi Hukum'**
  String get userPractitioner;

  /// No description provided for @userPublic.
  ///
  /// In id, this message translates to:
  /// **'Masyarakat Umum'**
  String get userPublic;

  /// No description provided for @userOther.
  ///
  /// In id, this message translates to:
  /// **'Lainnya'**
  String get userOther;

  /// No description provided for @aspectAccess.
  ///
  /// In id, this message translates to:
  /// **'Kemudahan Akses'**
  String get aspectAccess;

  /// No description provided for @aspectCompleteness.
  ///
  /// In id, this message translates to:
  /// **'Kelengkapan Informasi'**
  String get aspectCompleteness;

  /// No description provided for @aspectSpeed.
  ///
  /// In id, this message translates to:
  /// **'Kecepatan Loading'**
  String get aspectSpeed;

  /// No description provided for @aspectInterface.
  ///
  /// In id, this message translates to:
  /// **'Tampilan Antarmuka'**
  String get aspectInterface;

  /// No description provided for @aspectRelevance.
  ///
  /// In id, this message translates to:
  /// **'Relevansi Pencarian'**
  String get aspectRelevance;

  /// No description provided for @errNameRequired.
  ///
  /// In id, this message translates to:
  /// **'Nama wajib diisi.'**
  String get errNameRequired;

  /// No description provided for @errEmailFormat.
  ///
  /// In id, this message translates to:
  /// **'Format email belum benar, contoh: nama@contoh.go.id'**
  String get errEmailFormat;

  /// No description provided for @errPickUserType.
  ///
  /// In id, this message translates to:
  /// **'Pilih salah satu jenis pengguna.'**
  String get errPickUserType;

  /// No description provided for @errRateAll.
  ///
  /// In id, this message translates to:
  /// **'Beri nilai 1–5 untuk setiap aspek.'**
  String get errRateAll;

  /// No description provided for @thankYou.
  ///
  /// In id, this message translates to:
  /// **'Terima kasih!'**
  String get thankYou;

  /// No description provided for @surveySent.
  ///
  /// In id, this message translates to:
  /// **'Survei Anda telah terkirim.'**
  String get surveySent;

  /// No description provided for @ok.
  ///
  /// In id, this message translates to:
  /// **'OK'**
  String get ok;

  /// No description provided for @fieldName.
  ///
  /// In id, this message translates to:
  /// **'Nama *'**
  String get fieldName;

  /// No description provided for @fieldInstitution.
  ///
  /// In id, this message translates to:
  /// **'Instansi'**
  String get fieldInstitution;

  /// No description provided for @fieldUserType.
  ///
  /// In id, this message translates to:
  /// **'Jenis Pengguna *'**
  String get fieldUserType;

  /// No description provided for @fieldRating.
  ///
  /// In id, this message translates to:
  /// **'Penilaian (1–5) *'**
  String get fieldRating;

  /// No description provided for @fieldSuggestions.
  ///
  /// In id, this message translates to:
  /// **'Saran Perbaikan'**
  String get fieldSuggestions;

  /// No description provided for @fieldFeatures.
  ///
  /// In id, this message translates to:
  /// **'Fitur yang Diharapkan'**
  String get fieldFeatures;

  /// No description provided for @fieldContactOk.
  ///
  /// In id, this message translates to:
  /// **'Bersedia dihubungi'**
  String get fieldContactOk;

  /// No description provided for @fieldContact.
  ///
  /// In id, this message translates to:
  /// **'Kontak (HP/WA)'**
  String get fieldContact;

  /// No description provided for @submitSurvey.
  ///
  /// In id, this message translates to:
  /// **'Kirim Survei'**
  String get submitSurvey;

  /// No description provided for @ratingOf.
  ///
  /// In id, this message translates to:
  /// **'{label} · {n} dari 5'**
  String ratingOf(String label, int n);

  /// No description provided for @ratingTooltip.
  ///
  /// In id, this message translates to:
  /// **'{label}, {n} dari 5'**
  String ratingTooltip(String label, int n);
}

class _AppLocalizationsDelegate
    extends LocalizationsDelegate<AppLocalizations> {
  const _AppLocalizationsDelegate();

  @override
  Future<AppLocalizations> load(Locale locale) {
    return SynchronousFuture<AppLocalizations>(lookupAppLocalizations(locale));
  }

  @override
  bool isSupported(Locale locale) =>
      <String>['en', 'id', 'ko', 'zh'].contains(locale.languageCode);

  @override
  bool shouldReload(_AppLocalizationsDelegate old) => false;
}

AppLocalizations lookupAppLocalizations(Locale locale) {
  // Lookup logic when only language code is specified.
  switch (locale.languageCode) {
    case 'en':
      return AppLocalizationsEn();
    case 'id':
      return AppLocalizationsId();
    case 'ko':
      return AppLocalizationsKo();
    case 'zh':
      return AppLocalizationsZh();
  }

  throw FlutterError(
    'AppLocalizations.delegate failed to load unsupported locale "$locale". This is likely '
    'an issue with the localizations generation tool. Please file an issue '
    'on GitHub with a reproducible sample app and the gen-l10n configuration '
    'that was used.',
  );
}
