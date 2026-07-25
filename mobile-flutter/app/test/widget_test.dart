import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:jdih_kendari/api.dart';
import 'package:jdih_kendari/screens/doc_view.dart';
import 'package:jdih_kendari/screens/documents.dart';
import 'package:jdih_kendari/screens/statistik.dart';
import 'package:jdih_kendari/theme.dart';
import 'package:jdih_kendari/widgets.dart';

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
  'ringkasan': 'Kegiatan sosialisasi diikuti seluruh kecamatan se-Kota Kendari.',
};

void main() {
  // fmtDate memakai locale 'id'; sama seperti main() aplikasi.
  setUpAll(() => initializeDateFormatting('id'));

  test('nama berkas & deteksi PDF dari URL', () {
    expect(docFileName('https://h/storage/dokumen/perda-5-2023.pdf'),
        'perda-5-2023.pdf');
    expect(docFileName('https://h/a/Perwali%20No%201.pdf'), 'Perwali No 1.pdf');
    expect(docFileName('https://h'), 'dokumen.pdf');
    expect(isPdfUrl('https://h/a/x.PDF'), true);
    expect(isPdfUrl('https://h/a/x.docx'), false);
  });

  testWidgets('kartu utama render tanpa overflow', (tester) async {
    await tester.pumpWidget(MaterialApp(
      theme: appTheme(Brightness.light),
      home: Scaffold(
        body: ListView(padding: const EdgeInsets.all(16), children: [
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
          const DocFileTile('https://h/storage/dokumen/perda-5-2023.pdf',
              title: 'Dokumen Utama'),
        ]),
      ),
    ));
    await tester.pump();
    expect(find.text('PERDA'), findsOneWidget);
    // gulir sampai tile berkas: ListView membangun item secara malas
    await tester.scrollUntilVisible(find.text('perda-5-2023.pdf'), 300,
        scrollable: find.byType(Scrollable).first);
    expect(find.text('perda-5-2023.pdf'), findsOneWidget);
    expect(find.text('Tanya AI Sekarang'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets('empty state menampilkan judul, pesan, dan aksi', (tester) async {
    await tester.pumpWidget(MaterialApp(
      theme: appTheme(Brightness.light),
      home: Scaffold(
        body: EmptyState(
          icon: Icons.search,
          title: 'Cari produk hukum',
          message: 'Ketik kata kunci lalu tekan cari.',
          action: OutlinedButton(onPressed: () {}, child: const Text('Muat ulang')),
        ),
      ),
    ));
    expect(find.text('Cari produk hukum'), findsOneWidget);
    expect(find.text('Muat ulang'), findsOneWidget);
    expect(tester.takeException(), isNull);
  });

  testWidgets('navigasi bawah: tombol tengah yang menonjol tetap bisa ditekan',
      (tester) async {
    var picked = -1;
    const items = [
      (icon: Icons.home_outlined, activeIcon: Icons.home, label: 'Beranda'),
      (
        icon: Icons.description_outlined,
        activeIcon: Icons.description,
        label: 'Dokumen'
      ),
      (icon: Icons.auto_awesome, activeIcon: Icons.auto_awesome, label: 'Cari AI'),
      (
        icon: Icons.newspaper_outlined,
        activeIcon: Icons.newspaper,
        label: 'Kabar'
      ),
      (icon: Icons.menu, activeIcon: Icons.menu, label: 'Menu'),
    ];
    await tester.pumpWidget(MaterialApp(
      theme: appTheme(Brightness.light),
      home: Scaffold(
        body: const SizedBox.expand(),
        bottomNavigationBar: FloatingNavBar(
          index: 0,
          onChanged: (i) => picked = i,
          items: items,
        ),
      ),
    ));
    await tester.pumpAndSettle();
    expect(tester.takeException(), isNull);

    // bagian atas lingkaran berada di luar kotak pil; hit test harus tetap kena
    final circle = tester.getRect(find.byIcon(Icons.auto_awesome));
    await tester.tapAt(Offset(circle.center.dx, circle.top + 2));
    expect(picked, 2);

    // tujuan biasa juga masih bekerja
    await tester.tap(find.byIcon(Icons.menu));
    expect(picked, 4);
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

  testWidgets('grafik statistik: nilai, persentase, dan legenda berikon',
      (tester) async {
    await tester.pumpWidget(const MaterialApp(
      home: Scaffold(
        body: SingleChildScrollView(
          padding: EdgeInsets.all(16),
          child: Column(children: [
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
          ]),
        ),
      ),
    ));
    await tester.pumpAndSettle();

    // angka diformat gaya Indonesia
    expect(find.text('1.008'), findsOneWidget);
    expect(find.text('610'), findsOneWidget);

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

  testWidgets('grafik tahun: 10 kolom muat di lebar ponsel sempit',
      (tester) async {
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

    await tester.pumpWidget(MaterialApp(
      theme: appTheme(Brightness.light),
      home: const Scaffold(
        body: SingleChildScrollView(
          padding: EdgeInsets.all(16),
          child: TahunChart(tahun),
        ),
      ),
    ));
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
        {'id': 1}
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
        {'id': 2}
      ],
      'pagination': {'has_more': true, 'total': 10},
    });
    expect(p.items.length, 2);
    expect(p.hasMore, true);
    expect(p.total, 10);
  });
}
