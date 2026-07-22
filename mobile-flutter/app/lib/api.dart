import 'dart:async';
import 'dart:convert';

import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;

/// Ganti saat run/build: --dart-define=BASE_URL=https://domain-produksi
const kBaseUrl =
    String.fromEnvironment('BASE_URL', defaultValue: 'http://10.0.2.2:6992');
const kApiKey = String.fromEnvironment('MOBILE_API_KEY', defaultValue: '');

/// Bahasa konten aktif (id/en/zh/ko); diubah dari tab Lainnya.
final langNotifier = ValueNotifier<String>('id');

typedef Json = Map<String, dynamic>;

extension JsonX on Json {
  String s(String k) => this[k]?.toString() ?? '';

  /// String bernilai; null bila kosong.
  String? sn(String k) {
    final v = this[k]?.toString();
    return (v == null || v.isEmpty || v == 'null') ? null : v;
  }

  int i(String k) => (this[k] as num?)?.toInt() ?? 0;

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
  Map<String, String> get _headers => {
        'Accept': 'application/json',
        if (kApiKey.isNotEmpty) 'X-API-Key': kApiKey,
      };

  Uri _uri(String path, [Map<String, String?>? query]) =>
      Uri.parse('$kBaseUrl/api/v1$path').replace(queryParameters: {
        'lang': langNotifier.value,
        if (query != null)
          for (final e in query.entries)
            if (e.value != null && e.value!.isNotEmpty) e.key: e.value!,
      });

  Json _decode(http.Response res) {
    final dynamic body =
        res.body.isEmpty ? <String, dynamic>{} : jsonDecode(res.body);
    final json = body is Map
        ? body.cast<String, dynamic>()
        : <String, dynamic>{'data': body};
    if (res.statusCode >= 400) {
      throw ApiException(
          json.sn('message') ?? 'Gagal memuat data (${res.statusCode}).',
          res.statusCode);
    }
    return json;
  }

  /// Pesan error jaringan yang menyebut alamat server, agar salah BASE_URL
  /// (mis. default 10.0.2.2 dijalankan di Chrome/HP fisik) langsung ketahuan.
  Never _fail(Object e) {
    if (e is TimeoutException) {
      throw ApiException('Server lambat merespons. Coba lagi.');
    }
    throw ApiException(
        'Tidak dapat terhubung ke $kBaseUrl. Periksa koneksi dan alamat server (BASE_URL).');
  }

  Future<Json> _get(String path, [Map<String, String?>? query]) async {
    try {
      return _decode(await http
          .get(_uri(path, query), headers: _headers)
          .timeout(const Duration(seconds: 30)));
    } on ApiException {
      rethrow;
    } catch (e) {
      _fail(e);
    }
  }

  Future<Json> _post(String path, Json body) async {
    try {
      return _decode(await http
          .post(_uri(path),
              headers: {..._headers, 'Content-Type': 'application/json'},
              body: jsonEncode(body))
          .timeout(const Duration(seconds: 45))); // AI (Gemini) bisa lama
    } on ApiException {
      rethrow;
    } catch (e) {
      _fail(e);
    }
  }

  // ---------- endpoints (kontrak: mobile-flutter/02-API-CONTRACT.md) ----------

  Future<Json> home() async => (await _get('/home')).m('data');
  Future<Json> meta() async => (await _get('/meta')).m('data');

  Future<Paginated> documents(
          {required String category,
          String? q,
          String? jenis,
          String? tahun,
          String? status,
          int page = 1}) async =>
      Paginated.of(await _get('/documents', {
        'category': category,
        'q': q,
        'jenis': jenis,
        'tahun': tahun,
        'status': status,
        'page': '$page',
      }));

  Future<Json> documentDetail(int id) async =>
      (await _get('/documents/$id')).m('data');

  Future<Json> documentDownload(int id) async =>
      (await _get('/documents/$id/download')).m('data');

  Future<Json> documentFilters(String category) async =>
      (await _get('/document-filters', {'category': category})).m('data');

  Future<Json> aiSearch(String query) =>
      _post('/ai-search', {'query': query, 'lang': langNotifier.value});

  Future<Paginated> news({int page = 1, String? q}) async =>
      Paginated.of(await _get('/news', {'page': '$page', 'q': q}));

  Future<Json> newsDetail(int id) async => (await _get('/news/$id')).m('data');

  Future<Paginated> announcements({int page = 1}) async =>
      Paginated.of(await _get('/announcements', {'page': '$page'}));

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
          await _get('/puu', {'category': category, 'page': '$page', 'q': q}));

  Future<Json> puuDetail(int id) async => (await _get('/puu/$id')).m('data');

  Future<Json> submitSurvey(Json body) => _post('/survey', body);
}

final api = Api();
