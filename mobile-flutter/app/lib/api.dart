import 'dart:async';
import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:flutter/widgets.dart' show BuildContext, Locale;
import 'package:http/http.dart' as http;

import 'l10n/app_localizations.dart';

export 'l10n/app_localizations.dart';

/// Ganti saat run/build: --dart-define=BASE_URL=https://domain-lain.
/// Default rilis = server produksi (cleartext HTTP hanya diizinkan di debug);
/// debug/profile = server dev lokal dilihat dari emulator Android.
const kBaseUrl = String.fromEnvironment(
  'BASE_URL',
  defaultValue: kReleaseMode
      ? 'https://jdih.kendarikota.go.id'
      : 'http://10.0.2.2:6992',
);
const kApiKey = String.fromEnvironment('MOBILE_API_KEY', defaultValue: '');

/// Kunci untuk endpoint /api/jdih/* (integrasi JDIHN). Backend hanya
/// memeriksa awalan "jdih_", bukan nilainya.
const kJdihApiKey = String.fromEnvironment(
  'JDIH_API_KEY',
  defaultValue: 'jdih_mobile',
);

/// Bahasa konten aktif (id/en/zh/ko); diubah dari tab Lainnya.
final langNotifier = ValueNotifier<String>('id');

extension L10nX on BuildContext {
  /// Teks UI bahasa aktif (lib/l10n/app_*.arb).
  AppLocalizations get l10n => AppLocalizations.of(this);
}

/// Teks UI bahasa aktif untuk kode tanpa BuildContext (pesan galat API).
AppLocalizations get l10nAktif =>
    lookupAppLocalizations(Locale(langNotifier.value));

typedef Json = Map<String, dynamic>;

extension JsonX on Json {
  String s(String k) => this[k]?.toString() ?? '';

  /// String bernilai; null bila kosong.
  String? sn(String k) {
    final v = this[k]?.toString();
    return (v == null || v.isEmpty || v == 'null') ? null : v;
  }

  /// Toleran: sebagian endpoint mengirim angka sebagai string.
  int i(String k) {
    final v = this[k];
    if (v is num) return v.toInt();
    return int.tryParse(v?.toString() ?? '') ?? 0;
  }

  List<Json> l(String k) => ((this[k] as List?) ?? const [])
      .whereType<Map>()
      .map((e) => e.cast<String, dynamic>())
      .toList();

  List<String> ls(String k) =>
      ((this[k] as List?) ?? const []).map((e) => e.toString()).toList();

  Json m(String k) => (this[k] as Map?)?.cast<String, dynamic>() ?? {};
}

class ApiException implements Exception {
  final String message;
  final int? status;
  ApiException(this.message, [this.status]);
  @override
  String toString() => message;
}

class Paginated {
  final List<Json> items;
  final bool hasMore;
  final int total;
  const Paginated(this.items, this.hasMore, this.total);
  factory Paginated.of(Json j) {
    final p = j.m('pagination');
    return Paginated(j.l('data'), p['has_more'] == true, p.i('total'));
  }
}

class Api {
  /// Satu klien untuk semua permintaan: koneksi TLS ke server dipakai ulang
  /// (keep-alive). http.get membuka dan menutup koneksi baru tiap panggilan;
  /// diukur ke server produksi: 6 permintaan 840 ms -> 485 ms. Dibuat ulang
  /// bila zona berganti, agar http.runWithClient di tes tetap berlaku.
  http.Client get _klien {
    if (!identical(_zona, Zone.current)) {
      _zona = Zone.current;
      _klienZona = http.Client();
    }
    return _klienZona!;
  }

  Zone? _zona;
  http.Client? _klienZona;

  Map<String, String> get _headers => {
    'Accept': 'application/json',
    if (kApiKey.isNotEmpty) 'X-API-Key': kApiKey,
  };

  Uri _uri(String path, [Map<String, String?>? query]) =>
      Uri.parse('$kBaseUrl/api/v1$path').replace(
        queryParameters: {
          'lang': langNotifier.value,
          if (query != null)
            for (final e in query.entries)
              if (e.value != null && e.value!.isNotEmpty) e.key: e.value!,
        },
      );

  Json _decode(http.Response res) {
    final code = res.statusCode;
    final dynamic body;
    try {
      body = res.body.isEmpty ? <String, dynamic>{} : jsonDecode(res.body);
    } on FormatException {
      // HTML, bukan JSON: halaman galat proxy (502/503), atau halaman login
      // Wi-Fi publik yang menyadap semua permintaan sebelum pengguna masuk
      throw ApiException(
        code >= 500 ? l10nAktif.errServer : l10nAktif.errUnknownReply,
        code,
      );
    }
    final json = body is Map
        ? body.cast<String, dynamic>()
        : <String, dynamic>{'data': body};
    if (code >= 400) throw ApiException(_pesanGagal(code, json), code);
    return json;
  }

  /// Hanya 404 yang membawa pesan server apa adanya ("Berita tidak
  /// ditemukan"). Sisanya diganti kalimat sendiri: 5xx bisa berisi jejak
  /// teknis (query SQL, path file), 422 produksi berupa kunci mentah
  /// ("validation.required (and 6 more errors)"), dan 429 dari throttle
  /// Laravel berbahasa Inggris. Pesan server selalu berbahasa Indonesia,
  /// jadi hanya dipakai saat bahasa aktif id.
  static String _pesanGagal(int code, Json json) {
    final l = l10nAktif;
    return switch (code) {
      >= 500 => l.errServer,
      429 => l.errTooMany,
      422 => l.errInvalidInput,
      404 when langNotifier.value == 'id' =>
        json.sn('message') ?? l.errNotFound,
      404 => l.errNotFound,
      _ => l.errRequestFailed(code),
    };
  }

  /// Pesan untuk pengguna tidak menyebut alamat server; mode debug menambah
  /// alamatnya, agar salah BASE_URL (mis. 10.0.2.2 di HP fisik) langsung
  /// ketahuan saat pengembangan.
  Never _fail(Object e) {
    if (e is TimeoutException) {
      throw ApiException(l10nAktif.errTimeout);
    }
    throw ApiException(
      '${l10nAktif.errOffline}'
      '${kDebugMode ? '\n[debug] BASE_URL: $kBaseUrl' : ''}',
    );
  }

  Future<Json> _get(String path, [Map<String, String?>? query]) async {
    try {
      return _decode(
        await _klien
            .get(_uri(path, query), headers: _headers)
            .timeout(const Duration(seconds: 30)),
      );
    } on ApiException {
      rethrow;
    } catch (e) {
      _fail(e);
    }
  }

  Future<Json> _post(String path, Json body) async {
    try {
      return _decode(
        await _klien
            .post(
              _uri(path),
              headers: {..._headers, 'Content-Type': 'application/json'},
              body: jsonEncode(body),
            )
            .timeout(const Duration(seconds: 45)),
      ); // AI (Gemini) bisa lama
    } on ApiException {
      rethrow;
    } catch (e) {
      _fail(e);
    }
  }

  // ---------- endpoints (kontrak: mobile-flutter/02-API-CONTRACT.md) ----------

  Future<Json> home() async => (await _get('/home')).m('data');

  /// Statistik agregat dari API JDIHN (`/api/jdih/statistics`) — prefix dan
  /// kunci berbeda dari /api/v1, jadi tidak lewat `_get`.
  Future<Json> statistics() async {
    final uri = Uri.parse('$kBaseUrl/api/jdih/statistics');
    try {
      final res = await _klien
          .get(
            uri,
            headers: {'Accept': 'application/json', 'X-API-Key': kJdihApiKey},
          )
          .timeout(const Duration(seconds: 30));
      return _decode(res);
    } on ApiException {
      rethrow;
    } catch (e) {
      _fail(e);
    }
  }

  Future<Json> meta() async => (await _get('/meta')).m('data');

  Future<Paginated> documents({
    required String category,
    String? q,
    String? jenis,
    String? tahun,
    String? status,
    String? nomor,
    int page = 1,
  }) async => Paginated.of(
    await _get('/documents', {
      'category': category,
      'q': q,
      'jenis': jenis,
      'tahun': tahun,
      'status': status,
      'nomor': nomor,
      'page': '$page',
    }),
  );

  Future<Json> documentDetail(int id) async =>
      (await _get('/documents/$id')).m('data');

  Future<Json> documentDownload(int id) async =>
      (await _get('/documents/$id/download')).m('data');

  Future<Json> documentFilters(String category) async =>
      (await _get('/document-filters', {'category': category})).m('data');

  /// [history] = giliran sebelumnya `{role: user|ai, text}`; server tidak
  /// menyimpan percakapan, jadi klien mengirim ulang tiap giliran (seperti web).
  Future<Json> aiSearch(String query, {List<Json> history = const []}) => _post(
    '/ai-search',
    {'query': query, 'lang': langNotifier.value, 'history': history},
  );

  Future<Paginated> news({int page = 1, String? q}) async =>
      Paginated.of(await _get('/news', {'page': '$page', 'q': q}));

  Future<Json> newsDetail(int id) async => (await _get('/news/$id')).m('data');

  Future<Paginated> announcements({int page = 1, String? q}) async =>
      Paginated.of(await _get('/announcements', {'page': '$page', 'q': q}));

  Future<Json> announcementDetail(int id) async =>
      (await _get('/announcements/$id')).m('data');

  Future<List<Json>> legalInfoTypes() async =>
      (await _get('/legal-info/types')).l('data');

  Future<Paginated> legalInfo({String? type, int page = 1}) async =>
      Paginated.of(await _get('/legal-info', {'type': type, 'page': '$page'}));

  Future<Json> legalInfoDetail(int id) async =>
      (await _get('/legal-info/$id')).m('data');

  Future<Paginated> videos({int page = 1}) async =>
      Paginated.of(await _get('/videos', {'page': '$page'}));

  Future<Json> profile(String kategori) async =>
      (await _get('/profile/$kategori')).m('data');

  Future<Paginated> disability({int page = 1, String? q}) async =>
      Paginated.of(await _get('/disability', {'page': '$page', 'q': q}));

  Future<Json> disabilityDetail(int id) async =>
      (await _get('/disability/$id')).m('data');

  Future<Paginated> puu({String? category, int page = 1, String? q}) async =>
      Paginated.of(
        await _get('/puu', {'category': category, 'page': '$page', 'q': q}),
      );

  Future<Json> puuDetail(int id) async => (await _get('/puu/$id')).m('data');

  Future<Json> submitSurvey(Json body) => _post('/survey', body);
}

final api = Api();
