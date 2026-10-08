import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:jdih_kendari/api.dart';
import 'package:jdih_kendari/screens/documents.dart';
import 'package:jdih_kendari/screens/home.dart';
import 'package:jdih_kendari/screens/statistik.dart';
import 'package:jdih_kendari/theme.dart';

/// Tahun lampau semua: pembacaan tahun berjalan ("sejauh ini") tidak ikut
/// bergantung pada tanggal tes dijalankan.
const _statistik = <String, dynamic>{
  'total_dokumen': 1032,
  'total_views': 30402,
  'total_downloads': 454,
  'dokumen_per_tahun': [
    {'tahun': '2024', 'total': 121},
    {'tahun': '2023', 'total': 67},
    {'tahun': '2022', 'total': 87},
    {'tahun': '2021', 'total': 68},
  ],
  'dokumen_per_tipe': [
    {'tipe': 'PERATURAN WALIKOTA', 'total': 620},
    {'tipe': 'KEPUTUSAN WALIKOTA', 'total': 190},
    {'tipe': 'PERATURAN DAERAH KOTA', 'total': 182},
    {'tipe': 'ARTIKEL HUKUM', 'total': 6},
    {'tipe': 'BUKU HUKUM', 'total': 3},
  ],
  'dokumen_per_status': [
    {'status': 'Berlaku', 'total': 958},
    {'status': 'Tidak Berlaku', 'total': 74},
  ],
};

Widget _app(Widget home) => MaterialApp(
  locale: const Locale('id'),
  localizationsDelegates: AppLocalizations.localizationsDelegates,
  supportedLocales: AppLocalizations.supportedLocales,
  theme: appTheme(Brightness.light),
  home: home,
);

Widget _panel() => _app(
  const Scaffold(
    body: SingleChildScrollView(
      padding: EdgeInsets.all(16),
      child: PanelKoleksi(_statistik),
    ),
  ),
);

Finder _bacaan(String teks) => find.textContaining(teks, findRichText: true);

void main() {
  setUpAll(() => initializeDateFormatting('id'));

  testWidgets('angka utama + kalimat pendukung, bukan kotak KPI', (
    tester,
  ) async {
    await tester.pumpWidget(_panel());
    await tester.pumpAndSettle();
    expect(find.text('1.032'), findsOneWidget);
    expect(find.text('Dilihat 30.402 kali, diunduh 454 kali.'), findsOneWidget);
    expect(find.byType(RingkasanCard), findsNothing);
  });

  testWidgets('tahun: ketuk, geser, dan pembaca layar memilih tahun', (
    tester,
  ) async {
    final semantik = tester.ensureSemantics();
    await tester.pumpWidget(_panel());
    await tester.pumpAndSettle();

    // bawaan: tahun terbaru, deret dibaca naik walau API mengirim menurun
    expect(_bacaan('terbit tahun 2024'), findsOneWidget);
    final kiri = tester.getTopLeft(find.text('2021'));
    final kanan = tester.getTopRight(find.text('2024'));
    final slot = (kanan.dx - kiri.dx) / 4;
    Offset kolom(int i) => Offset(kiri.dx + slot * (i + .5), kiri.dy - 30);

    await tester.tapAt(kolom(1));
    await tester.pump();
    expect(_bacaan('terbit tahun 2022'), findsOneWidget);
    expect(find.text('87', findRichText: true), findsNothing); // satu span
    expect(_bacaan('87'), findsOneWidget);

    // geser ke kiri sampai ujung: tahun pertama
    await tester.dragFrom(kolom(2), Offset(-slot * 3, 0));
    await tester.pump();
    expect(_bacaan('terbit tahun 2021'), findsOneWidget);

    // pembaca layar: usap atas = tahun berikutnya
    final grafik = find.semantics.byValue('2021: 68 dokumen');
    expect(grafik, findsOne);
    tester.semantics.performAction(grafik, SemanticsAction.increase);
    await tester.pump();
    expect(_bacaan('terbit tahun 2022'), findsOneWidget);
    semantik.dispose();
  });

  testWidgets('jenis: batang per jenis, ketuk membuka daftar terfilter', (
    tester,
  ) async {
    final diminta = <Uri>[];
    await http.runWithClient(
      () async {
        await tester.pumpWidget(_panel());
        await tester.pumpAndSettle();
        await tester.tap(find.text('Jenis'));
        await tester.pumpAndSettle();

        expect(find.text('Peraturan Walikota'), findsOneWidget);
        expect(find.text('620'), findsOneWidget);
        // ekor dilipat; campuran kategori, jadi tidak bisa dibuka
        expect(find.text('Lainnya (2 jenis)'), findsOneWidget);
        expect(find.text('9'), findsOneWidget);
        expect(find.byIcon(Icons.chevron_right), findsNWidgets(3));

        await tester.tap(find.text('Lainnya (2 jenis)'));
        await tester.pumpAndSettle();
        expect(find.byType(DocumentListScreen), findsNothing);

        await tester.tap(find.text('Peraturan Walikota'));
        await tester.pumpAndSettle();
        expect(find.byType(DocumentListScreen), findsOneWidget);
        final daftar = diminta.lastWhere((u) => u.path.endsWith('/documents'));
        expect(daftar.queryParameters['category'], 'peraturan');
        expect(daftar.queryParameters['jenis'], 'PERATURAN WALIKOTA');
        // filter terpasang tampil di dropdown walau daftar filter server
        // tidak memuatnya
        expect(find.text('Peraturan Walikota'), findsOneWidget);
        expect(tester.takeException(), isNull);
      },
      () => MockClient((req) async {
        diminta.add(req.url);
        return http.Response(
          jsonEncode({
            'data': [],
            'pagination': {'has_more': false, 'total': 0},
          }),
          200,
          headers: {'content-type': 'application/json'},
        );
      }),
    );
  });

  testWidgets('status: bagian berlaku + legenda berikon tanpa titik', (
    tester,
  ) async {
    await tester.pumpWidget(_panel());
    await tester.pumpAndSettle();
    await tester.tap(find.text('Status'));
    await tester.pumpAndSettle();

    expect(_bacaan('93%'), findsOneWidget);
    expect(find.byIcon(Icons.check_circle_outline), findsOneWidget);
    expect(find.byIcon(Icons.cancel_outlined), findsOneWidget);
    expect(find.text('958'), findsOneWidget);
    // dua segmen, dipisah celah 2dp
    final segmen = find.byWidgetPredicate(
      (w) =>
          w is ColoredBox &&
          (w.color == ChartColors.berlaku || w.color == ChartColors.dicabut),
    );
    expect(segmen, findsNWidgets(2));
    expect(
      tester.getTopLeft(segmen.last).dx - tester.getTopRight(segmen.first).dx,
      2,
    );
  });

  testWidgets('kepala beranda: logo JDIHN dan Pemkot Kendari berdampingan', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(360, 800);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);
    await http.runWithClient(() async {
      await tester.pumpWidget(_app(const HomeScreen()));
      await tester.pump(const Duration(seconds: 1));

      Finder logo(String label) =>
          find.byWidgetPredicate((w) => w is Image && w.semanticLabel == label);
      final jdihn = logo('Logo JDIHN'),
          kendari = logo('Logo Pemerintah Kota Kendari');
      expect(jdihn, findsOneWidget);
      expect(kendari, findsOneWidget);
      // sebaris, JDIHN dulu (urutan header web)
      expect(tester.getCenter(jdihn).dy, tester.getCenter(kendari).dy);
      expect(
        tester.getCenter(jdihn).dx,
        lessThan(tester.getCenter(kendari).dx),
      );
      // nama layanan utuh, tidak terpotong di HP 360dp
      final nama = tester.renderObject<RenderParagraph>(
        find.text('JDIH Kota Kendari'),
      );
      expect(nama.didExceedMaxLines, isFalse);
      expect(tester.takeException(), isNull);
      await tester.pump(const Duration(seconds: 2));
    }, () => MockClient((_) async => http.Response('{}', 500)));
  });
}
