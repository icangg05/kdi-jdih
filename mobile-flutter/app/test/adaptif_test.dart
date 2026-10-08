import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:jdih_kendari/api.dart';
import 'package:jdih_kendari/main.dart';
import 'package:jdih_kendari/screens/documents.dart';
import 'package:jdih_kendari/screens/home.dart';
import 'package:jdih_kendari/screens/search.dart';
import 'package:jdih_kendari/theme.dart';
import 'package:jdih_kendari/widgets.dart';

const _doc = {
  'id': 7,
  'judul': 'PERATURAN WALI KOTA KENDARI NOMOR 21 TAHUN 2026',
  'nomor_peraturan': '21',
  'tahun_terbit': '2026',
  'status': 'Berlaku',
  'singkatan_jenis': 'PERWALI',
  'hit_see': 10,
  'hit_download': 2,
};

/// API tiruan per jalur: cukup untuk beranda, daftar, dan detail dokumen.
http.Client _api() => MockClient((req) async {
  final p = req.url.path;
  final Object body = switch (p) {
    '/api/v1/home' => {
      'data': {
        'statistik': {'peraturan': 996},
        'peraturan_terbaru': [_doc],
        'berita': [],
        'pengumuman': [],
      },
    },
    '/api/v1/documents' => {
      'data': [
        _doc,
        {..._doc, 'id': 8, 'nomor_peraturan': '20'},
      ],
      'pagination': {'has_more': false, 'total': 2},
    },
    '/api/v1/documents/7' => {
      'data': {..._doc, 'lampiran': [], 'statistik': {}},
    },
    _ => {'data': {}},
  };
  return http.Response(
    jsonEncode(body),
    200,
    headers: {'content-type': 'application/json'},
  );
});

Future<void> _tablet(
  WidgetTester tester, [
  Size size = const Size(1280, 800),
]) async {
  tester.view.physicalSize = size;
  tester.view.devicePixelRatio = 1;
  addTearDown(tester.view.reset);
}

Future<void> _tunggu(WidgetTester tester) async {
  for (var i = 0; i < 4; i++) {
    await tester.pump(const Duration(milliseconds: 500));
  }
}

void main() {
  setUpAll(() => initializeDateFormatting('id'));

  testWidgets('tablet: rail samping menggantikan navigasi bawah', (
    tester,
  ) async {
    await _tablet(tester);
    await http.runWithClient(() async {
      rootTab.value = 0;
      await tester.pumpWidget(
        MaterialApp(
          locale: const Locale('id'),
          localizationsDelegates: AppLocalizations.localizationsDelegates,
          supportedLocales: AppLocalizations.supportedLocales,
          theme: appTheme(Brightness.light),
          home: const RootShell(),
        ),
      );
      await _tunggu(tester);
      expect(find.byType(NavigationRail), findsOneWidget);
      expect(find.byType(NavBawah), findsNothing);

      await tester.tap(find.text('Dokumen').last);
      await _tunggu(tester);
      expect(rootTab.value, 1);

      // Tanya AI di puncak rail: lembar yang sama dengan pintu lain
      await tester.tap(find.bySemanticsLabel('Tanya AI dan pencarian'));
      await _tunggu(tester);
      expect(find.byType(SearchScreen), findsOneWidget);
      expect(rootTab.value, 1);
      expect(tester.takeException(), isNull);
    }, _api);
  });

  testWidgets('HP lanskap: rail muat di tinggi 412dp', (tester) async {
    await _tablet(tester, const Size(892, 412));
    await http.runWithClient(() async {
      rootTab.value = 0;
      await tester.pumpWidget(
        MaterialApp(
          locale: const Locale('id'),
          localizationsDelegates: AppLocalizations.localizationsDelegates,
          supportedLocales: AppLocalizations.supportedLocales,
          theme: appTheme(Brightness.light),
          home: const RootShell(),
        ),
      );
      await _tunggu(tester);
      expect(find.byType(NavigationRail), findsOneWidget);
      expect(tester.takeException(), isNull);
      await tester.tap(find.text('Menu'));
      await _tunggu(tester);
      expect(rootTab.value, 4);
    }, _api);
  });

  testWidgets('HP lanskap + huruf 200%: rail tetap utuh', (tester) async {
    await _tablet(tester, const Size(892, 412));
    await tester.pumpWidget(
      MaterialApp(
        locale: const Locale('id'),
        localizationsDelegates: AppLocalizations.localizationsDelegates,
        supportedLocales: AppLocalizations.supportedLocales,
        theme: appTheme(Brightness.light),
        home: MediaQuery(
          data: const MediaQueryData(
            size: Size(892, 412),
            textScaler: TextScaler.linear(2),
          ),
          child: Scaffold(
            body: Row(
              children: [
                RailSamping(index: 0, onTap: (_) {}),
                const Expanded(child: SizedBox()),
              ],
            ),
          ),
        ),
      ),
    );
    expect(tester.takeException(), isNull);
  });

  testWidgets('tablet: daftar + detail dokumen berdampingan', (tester) async {
    await _tablet(tester);
    await http.runWithClient(() async {
      await tester.pumpWidget(
        MaterialApp(
          locale: const Locale('id'),
          localizationsDelegates: AppLocalizations.localizationsDelegates,
          supportedLocales: AppLocalizations.supportedLocales,
          theme: appTheme(Brightness.light),
          home: const DocumentListScreen(category: 'peraturan'),
        ),
      );
      await _tunggu(tester);
      expect(find.text('Pilih dokumen'), findsOneWidget);

      await tester.tap(find.byType(DocumentCard).first);
      await _tunggu(tester);
      // detail tampil di panel kanan, bukan di halaman yang di-push
      expect(find.text('Pilih dokumen'), findsNothing);
      expect(find.byType(DocumentDetailScreen), findsOneWidget);
      expect(find.byType(DocumentListScreen), findsOneWidget);
      expect(find.byTooltip('Kembali'), findsNothing);
      expect(tester.takeException(), isNull);
    }, _api);
  });

  testWidgets('tablet: beranda dua kolom, tablet potret satu kolom', (
    tester,
  ) async {
    Future<double> geser(Size size) async {
      await _tablet(tester, size);
      await tester.pumpWidget(
        MaterialApp(
          locale: const Locale('id'),
          localizationsDelegates: AppLocalizations.localizationsDelegates,
          supportedLocales: AppLocalizations.supportedLocales,
          theme: appTheme(Brightness.light),
          home: const HomeScreen(),
        ),
      );
      await _tunggu(tester);
      expect(tester.takeException(), isNull);
      return tester.getTopLeft(find.text('Dokumen Terbaru')).dx -
          tester.getTopLeft(find.byType(AiPromoCard)).dx;
    }

    await http.runWithClient(() async {
      // lanskap 1280: dokumen terbaru di kolom kanan
      expect(await geser(const Size(1280, 800)), greaterThan(300));
      // potret 800 (< 840): satu kolom, sejajar di kiri
      expect(await geser(const Size(800, 1280)), lessThan(40));
      await tester.pumpWidget(const SizedBox());
      await _tunggu(tester);
    }, _api);
  });
}
