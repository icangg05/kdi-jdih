import 'dart:async';
import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:jdih_kendari/api.dart';
import 'package:jdih_kendari/main.dart';
import 'package:jdih_kendari/screens/doc_view.dart';
import 'package:jdih_kendari/screens/documents.dart';
import 'package:jdih_kendari/screens/kabar.dart';
import 'package:jdih_kendari/screens/search.dart';
import 'package:jdih_kendari/screens/statistik.dart';
import 'package:jdih_kendari/theme.dart';
import 'package:jdih_kendari/widgets.dart';
import 'package:skeletonizer/skeletonizer.dart';

const _doc = <String, dynamic>{
  'id': 1,
  'judul': 'Peraturan Daerah tentang Retribusi Pelayanan Persampahan',
  'nomor_peraturan': '5',
  'tahun_terbit': '2023',
  'status': 'Berlaku',
  'singkatan_jenis': 'PERDA',
  'bidang_hukum': 'Lingkungan Hidup',
  'hit_see': 120,
  'hit_download': 34,
};

const _berita = <String, dynamic>{
  'id': 9,
  'judul': 'Sosialisasi JDIH Terintegrasi Tingkat Kecamatan',
  'tanggal': '2023-10-12',
  'tag': 'Pemerintahan',
  'ringkasan':
      'Kegiatan sosialisasi diikuti seluruh kecamatan se-Kota Kendari.',
};

void main() {
  // fmtDate memakai locale 'id'; sama seperti main() aplikasi.
  setUpAll(() => initializeDateFormatting('id'));

  test('nama berkas & deteksi PDF dari URL', () {
    expect(
      docFileName('https://h/storage/dokumen/perda-5-2023.pdf'),
      'perda-5-2023.pdf',
    );
    expect(docFileName('https://h/a/Perwali%20No%201.pdf'), 'Perwali No 1.pdf');
    expect(docFileName('https://h'), 'dokumen.pdf');
    expect(isPdfUrl('https://h/a/x.PDF'), true);
    expect(isPdfUrl('https://h/a/x.docx'), false);
    expect(docKindLabel('https://h/a/x.pdf'), startsWith('Dokumen PDF'));
    expect(
      docKindLabel('https://h/a/x.docx'),
      'Berkas DOCX · unduh untuk membuka',
    );
  });

  test('nama berkas unduhan dibersihkan dan dipangkas', () {
    const url = 'https://h/storage/dokumen/1690_final(1).pdf';
    expect(
      downloadFileName(url, 'Perda No. 5/2023 : Retribusi*'),
      'Perda No. 5 2023 Retribusi.pdf',
    );
    // judul kosong / habis dibersihkan -> pakai nama asli di server
    expect(downloadFileName(url, '   '), '1690_final(1).pdf');
    expect(downloadFileName(url, '///'), '1690_final(1).pdf');
    final panjang = downloadFileName(url, 'A' * 200);
    expect(panjang.length, 64);
    expect(panjang.endsWith('.pdf'), true);
  });

  testWidgets('kartu utama render tanpa overflow', (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        theme: appTheme(Brightness.light),
        home: Scaffold(
          body: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              const DocumentCard(_doc),
              const SizedBox(height: 12),
              NewsTile(_berita, featured: true, onTap: () {}),
              const SizedBox(height: 12),
              // sama seperti karusel beranda: ListView mendatar, tinggi terkunci
              SizedBox(
                height: 244,
                child: ListView(
                  scrollDirection: Axis.horizontal,
                  children: [NewsTile(_berita, width: 224, onTap: () {})],
                ),
              ),
              const SizedBox(height: 12),
              AiPromoCard(onTap: () {}),
              const SizedBox(height: 12),
              const DocFileTile(
                'https://h/storage/dokumen/perda-5-2023.pdf',
                title: 'Dokumen Utama',
              ),
            ],
          ),
        ),
      ),
    );
    await tester.pump();
    // JenisChip menormalkan jenis ke Title Case: 'PERDA' -> 'Perda'
    expect(find.text('Perda'), findsOneWidget);
    // gulir sampai tile berkas: ListView membangun item secara malas.
    // Nama file mentah diganti keterangan jenis berkas (docKindLabel).
    final berkas = find.text('Dokumen PDF · dapat dibaca di aplikasi');
    await tester.scrollUntilVisible(
      berkas,
      300,
      scrollable: find.byType(Scrollable).first,
    );
    expect(berkas, findsOneWidget);
    expect(find.text('Mulai bertanya'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets(
    'kerangka daftar: kartu asli berisi data contoh di Skeletonizer',
    (tester) async {
      await tester.pumpWidget(
        MaterialApp(
          theme: appTheme(Brightness.light),
          home: Scaffold(
            body: PagedListView(
              // tidak pernah selesai: daftar tertahan di keadaan memuat
              fetch: (_) => Completer<Paginated>().future,
              itemBuilder: (_, d, _) => DocumentCard(d),
            ),
          ),
        ),
      );
      await tester.pump();
      expect(find.byWidgetPredicate((w) => w is Skeletonizer), findsOneWidget);
      expect(find.byType(DocumentCard), findsWidgets);
      expect(tester.takeException(), isNull);
      await tester.pump(const Duration(seconds: 1)); // habiskan jeda minimum
    },
  );

  testWidgets('kartu kabar: tinggi seragam berapa pun panjang isinya', (
    tester,
  ) async {
    const pendek = <String, dynamic>{
      'id': 1,
      'judul': 'Rapat',
      'tanggal': '2025-06-08',
    };
    final panjang = {..._berita, 'id': 2, 'tag': 'Pemerintahan'};
    await tester.pumpWidget(
      MaterialApp(
        theme: appTheme(Brightness.light),
        home: Scaffold(
          body: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              MediaCard(pendek, onTap: () {}),
              const SizedBox(height: 12),
              MediaCard(panjang, badge: 'Pemerintahan', onTap: () {}),
            ],
          ),
        ),
      ),
    );
    await tester.pump();
    expect(
      tester.getSize(find.byType(MediaCard).at(0)).height,
      tester.getSize(find.byType(MediaCard).at(1)).height,
    );
    expect(tester.takeException(), isNull);
  });

  testWidgets('empty state menampilkan judul, pesan, dan aksi', (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        theme: appTheme(Brightness.light),
        home: Scaffold(
          body: EmptyState(
            icon: Icons.search,
            title: 'Cari produk hukum',
            message: 'Ketik kata kunci lalu tekan cari.',
            action: OutlinedButton(
              onPressed: () {},
              child: const Text('Muat ulang'),
            ),
          ),
        ),
      ),
    );
    expect(find.text('Cari produk hukum'), findsOneWidget);
    expect(find.text('Muat ulang'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets('Tanya AI: percakapan mengirim riwayat dan melampirkan dokumen', (
    tester,
  ) async {
    final bodies = <Map<String, dynamic>>[];
    final client = MockClient((req) async {
      bodies.add(jsonDecode(req.body) as Map<String, dynamic>);
      return http.Response(
        jsonEncode({
          // cukup panjang untuk melampaui tinggi layar uji
          'explanation':
              'Diatur dalam **Perda 5/2018**.\n'
              '${[for (var i = 1; i <= 30; i++) '- tarif golongan $i'].join('\n')}',
          'documents': [
            {
              'id': 1,
              'title': 'PERDA RETRIBUSI PELAYANAN PERSAMPAHAN',
              'type': 'PERATURAN DAERAH',
              'year': '2018',
              'status': 'Berlaku',
              'accuracy': 87,
            },
          ],
          'total': 1,
        }),
        200,
        headers: {'content-type': 'application/json'},
      );
    });

    await http.runWithClient(() async {
      await tester.pumpWidget(
        MaterialApp(
          theme: appTheme(Brightness.light),
          home: const SearchScreen(
            initialAi: true,
            initialQuery: 'retribusi sampah',
          ),
        ),
      );
      await tester.pumpAndSettle(); // jawab -> animasi ketik selesai
      expect(tester.takeException(), isNull);

      expect(bodies.single['query'], 'retribusi sampah');
      expect(bodies.single['history'], isEmpty);
      // **tebal** dirender sebagai teks, bintangnya tidak tampil
      expect(
        find.textContaining('Perda 5/2018', findRichText: true),
        findsOneWidget,
      );
      expect(find.textContaining('**', findRichText: true), findsNothing);

      // lampiran tertutup secara bawaan, dibuka dengan sekali ketuk — dan
      // membukanya tidak menggeser layar: judulnya tetap di tempat
      expect(find.text('87% relevan'), findsNothing);
      final judul = find.text('Dokumen terkait');
      final sebelum = tester.getTopLeft(judul);
      await tester.tap(judul);
      await tester.pumpAndSettle();
      expect(tester.getTopLeft(judul), sebelum);
      expect(find.text('87% relevan'), findsOneWidget);
      expect(
        find.text('Perda Retribusi Pelayanan Persampahan'),
        findsOneWidget,
      );

      // giliran kedua membawa riwayat giliran pertama
      await tester.enterText(find.byType(TextField).last, 'tarifnya berapa?');
      await tester.tap(find.byTooltip('Kirim'));
      await tester.pumpAndSettle();
      expect(bodies, hasLength(2));
      expect(bodies[1]['query'], 'tarifnya berapa?');
      expect(
        [for (final h in bodies[1]['history'] as List) h['role']],
        ['user', 'ai'],
      );
      expect((bodies[1]['history'] as List).first['text'], 'retribusi sampah');

      // bersihkan bisa diurungkan: ketukan tak sengaja tidak menghapus semua.
      // Tombol bersihkan hanya ada selama percakapan berisi.
      final bersihkan = find.byTooltip('Bersihkan percakapan');
      await tester.tap(bersihkan);
      await tester.pumpAndSettle();
      expect(bersihkan, findsNothing);
      expect(find.text('Apa aturan retribusi sampah?'), findsOneWidget);
      await tester.tap(find.text('Urungkan'));
      await tester.pumpAndSettle();
      expect(bersihkan, findsOneWidget);
      expect(find.text('Dokumen terkait'), findsWidgets);
    }, () => client);
  });

  testWidgets('navigasi bawah: muat di ponsel sempit dengan teks 200%', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(320, 640);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);
    var dipilih = -1;
    await tester.pumpWidget(
      MaterialApp(
        theme: appTheme(Brightness.light),
        home: MediaQuery(
          data: const MediaQueryData(
            size: Size(320, 640),
            textScaler: TextScaler.linear(2),
          ),
          child: Scaffold(
            bottomNavigationBar: NavBawah(index: 0, onTap: (i) => dipilih = i),
          ),
        ),
      ),
    );
    expect(tester.takeException(), isNull);
    await tester.tap(find.text('Tanya AI'));
    expect(dipilih, 2);
  });

  test('animasi ketik tidak pernah menampilkan penanda tebal mentah', () {
    expect(ketikSebagian('Lihat **Perda**', 1), 'Lihat **Perda**');
    // potongan berhenti di tengah penanda: bintang di ujung dibuang
    expect(ketikSebagian('Lihat **Perda**', 7 / 15), 'Lihat ');
    // penanda belum berpasangan: sisa teks sudah tebal
    final spans = mdSpans('Lihat **Per');
    expect(spans.map((s) => s.text), ['Lihat ', 'Per']);
    expect(spans.last.style?.fontWeight, FontWeight.w700);
  });

  test('pemetaan status: "tidak berlaku" harus menang atas "berlaku"', () {
    expect(statusKindOf('Berlaku'), StatusKind.berlaku);
    expect(statusKindOf('Tidak Berlaku'), StatusKind.dicabut);
    expect(statusKindOf('Dicabut'), StatusKind.dicabut);
    expect(statusKindOf('Berlaku, Diubah'), StatusKind.diubah);
    expect(statusKindOf('Mencabut sebagian'), StatusKind.diubah);
    expect(statusKindOf(null), StatusKind.lain);
    expect(statusKindOf(''), StatusKind.lain);
  });

  testWidgets('grafik statistik: nilai, persentase, dan legenda berikon', (
    tester,
  ) async {
    await tester.pumpWidget(
      const MaterialApp(
        home: Scaffold(
          body: SingleChildScrollView(
            padding: EdgeInsets.all(16),
            child: Column(
              children: [
                RingkasanCard(total: 1008, dilihat: 2450, diunduh: 124),
                SizedBox(height: 12),
                TahunChart([
                  // API mengirim menurun; grafik harus membacanya naik
                  (label: '2026', value: 31),
                  (label: '2025', value: 144),
                  (label: '2024', value: 121),
                  (label: '2023', value: 67),
                ]),
                SizedBox(height: 12),
                JenisChart([
                  (label: 'PERATURAN WALIKOTA', value: 610),
                  (label: 'PERATURAN DAERAH KOTA', value: 182),
                  (label: 'KEPUTUSAN WALIKOTA', value: 177),
                ], tampil: 2),
                SizedBox(height: 12),
                StatusChart({
                  StatusKind.berlaku: 300,
                  StatusKind.diubah: 100,
                  StatusKind.dicabut: 100,
                }),
              ],
            ),
          ),
        ),
      ),
    );
    await tester.pumpAndSettle();

    // angka diformat gaya Indonesia
    expect(find.text('1.008'), findsOneWidget);
    expect(find.text('610'), findsOneWidget);

    // segmen batang status benar-benar setinggi batangnya, bukan 0
    final segmen = find.descendant(
      of: find.byType(StatusChart),
      matching: find.byType(ColoredBox),
    );
    expect(segmen, findsNWidgets(3));
    expect(tester.getSize(segmen.first).height, 14);

    // ekor daftar jenis dilipat, tidak diberi warna baru
    expect(find.text('Lainnya (1 jenis)'), findsOneWidget);
    expect(find.text('177'), findsOneWidget);

    // hanya kolom puncak yang diberi label nilai
    expect(find.text('144'), findsOneWidget);
    expect(find.text('121'), findsNothing);

    // status: warna tidak pernah sendirian — ikon + label + nilai + persen
    expect(find.text('Berlaku'), findsOneWidget);
    expect(find.byIcon(Icons.check_circle_outline), findsOneWidget);
    expect(find.text('300  ·  60%'), findsOneWidget);
    expect(find.text('100  ·  20%'), findsNWidgets(2));

    expect(tester.takeException(), isNull);
  });

  testWidgets('grafik tahun: 10 kolom muat di lebar ponsel sempit', (
    tester,
  ) async {
    // data asli dari /api/jdih/statistics — 10 tahun adalah batas endpoint
    const tahun = [
      (label: '2026', value: 31),
      (label: '2025', value: 144),
      (label: '2024', value: 121),
      (label: '2023', value: 67),
      (label: '2022', value: 87),
      (label: '2021', value: 68),
      (label: '2020', value: 67),
      (label: '2019', value: 57),
      (label: '2018', value: 57),
      (label: '2017', value: 20),
    ];
    tester.view.physicalSize = const Size(360, 720);
    tester.view.devicePixelRatio = 1.0;
    addTearDown(tester.view.reset);

    await tester.pumpWidget(
      MaterialApp(
        theme: appTheme(Brightness.light),
        home: const Scaffold(
          body: SingleChildScrollView(
            padding: EdgeInsets.all(16),
            child: TahunChart(tahun),
          ),
        ),
      ),
    );
    await tester.pumpAndSettle();
    expect(find.text('2017'), findsOneWidget);
    expect(find.text('2026'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  test('JsonX helpers', () {
    final j = <String, dynamic>{
      'a': 'x',
      'b': null,
      'c': 5,
      'd': [
        {'id': 1},
      ],
      'e': {'k': 'v'},
      'f': ['p', 'q'],
    };
    expect(j.s('a'), 'x');
    expect(j.sn('b'), null);
    expect(j.sn('zz'), null);
    expect(j.i('c'), 5);
    expect(j.l('d').first.i('id'), 1);
    expect(j.m('e').s('k'), 'v');
    expect(j.ls('f'), ['p', 'q']);
  });

  test('Paginated parsing', () {
    final p = Paginated.of({
      'data': [
        {'id': 1},
        {'id': 2},
      ],
      'pagination': {'has_more': true, 'total': 10},
    });
    expect(p.items.length, 2);
    expect(p.hasMore, true);
    expect(p.total, 10);
  });
}
