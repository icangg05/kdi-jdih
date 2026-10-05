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
import 'package:jdih_kendari/screens/kabar.dart';
import 'package:jdih_kendari/screens/search.dart';
import 'package:jdih_kendari/screens/statistik.dart';
import 'package:jdih_kendari/screens/survey.dart';
import 'package:jdih_kendari/theme.dart';
import 'package:jdih_kendari/widgets.dart';

/// Ketahanan terhadap data, masukan, dan galat dunia nyata.
void main() {
  setUpAll(() => initializeDateFormatting('id'));

  test('ID YouTube diambil dari ID polos maupun tautan', () {
    expect(youtubeId('wPS9R3G6rZc'), 'wPS9R3G6rZc');
    expect(youtubeId(' https://youtu.be/wPS9R3G6rZc?t=3 '), 'wPS9R3G6rZc');
    expect(
      youtubeId('https://www.youtube.com/watch?v=_wOIKRBBnrQ&list=x'),
      '_wOIKRBBnrQ',
    );
    expect(youtubeId('https://youtube.com/shorts/aaeiA-rzq-M'), 'aaeiA-rzq-M');
    // ID video Facebook / isian rusak: bukan YouTube
    expect(youtubeId('1234567890123456'), isNull);
    expect(youtubeId('https://www.facebook.com/watch?v=1234567890'), isNull);
    expect(youtubeId(''), isNull);
  });

  test('abstrak berupa nama berkas PDF dijadikan berkas, bukan teks', () {
    final berkas = abstrakDokumen({'abstrak': ' fEroj123.PDF '});
    expect(berkas.teks, isNull);
    expect(berkas.url, endsWith('/storage/dokumen/fEroj123.PDF'));
    // server baru: abstrak_url dipakai apa adanya
    expect(
      abstrakDokumen({'abstrak_url': 'https://h/x.pdf'}).url,
      'https://h/x.pdf',
    );
    // teks biasa tetap teks, termasuk yang menyebut ".pdf" di tengah kalimat
    final teks = abstrakDokumen({'abstrak': 'Lihat lampiran.pdf untuk rinci'});
    expect(teks.teks, 'Lihat lampiran.pdf untuk rinci');
    expect(teks.url, isNull);
  });

  Future<String> galat(http.Response res) => http.runWithClient(() async {
    try {
      await api.newsDetail(1);
      return 'tidak gagal';
    } on ApiException catch (e) {
      return e.message;
    }
  }, () => MockClient((_) async => res));

  test('galat API berbahasa pengguna, bukan pesan mentah server', () async {
    // kunci validasi mentah dari produksi tidak boleh tampil
    expect(
      await galat(
        http.Response(
          '{"message":"validation.required (and 6 more errors)"}',
          422,
        ),
      ),
      startsWith('Isian belum lengkap'),
    );
    expect(
      await galat(http.Response('{"message":"Too Many Attempts."}', 429)),
      startsWith('Terlalu banyak permintaan'),
    );
    // 404 membawa pesan fungsional server apa adanya
    expect(
      await galat(http.Response('{"message":"Berita tidak ditemukan"}', 404)),
      'Berita tidak ditemukan',
    );
    // halaman HTML: login Wi-Fi publik / galat proxy
    expect(
      await galat(http.Response('<html>Login hotspot</html>', 200)),
      contains('Wi-Fi publik'),
    );
    expect(
      await galat(http.Response('<html>Bad Gateway</html>', 502)),
      startsWith('Server sedang bermasalah'),
    );
  });

  testWidgets('Back sistem: pulang ke tab asal/Beranda, baru keluar', (
    tester,
  ) async {
    // semua permintaan gagal: yang diuji hanya navigasinya
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
      await tester.pump(const Duration(seconds: 2));

      // Kabar -> Back -> Beranda
      await tester.tap(find.text('Kabar'));
      await tester.pump(const Duration(seconds: 1));
      expect(rootTab.value, 3);
      await tester.binding.handlePopRoute();
      await tester.pump(const Duration(seconds: 1));
      expect(rootTab.value, 0);

      // Dokumen -> Tanya AI -> Back -> kembali ke Dokumen (tab asal)
      await tester.tap(find.text('Dokumen'));
      await tester.pump(const Duration(seconds: 1));
      await tester.tap(find.text('Tanya AI'));
      await tester.pump(const Duration(seconds: 1));
      expect(rootTab.value, 2);
      await tester.binding.handlePopRoute();
      await tester.pump(const Duration(seconds: 1));
      expect(rootTab.value, 1);
      await tester.pump(const Duration(seconds: 2));
    }, () => MockClient((_) async => http.Response('{}', 500)));
  });

  testWidgets('Tanya AI naik menutupi tab asal, turun lagi saat keluar', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(390, 844);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);
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
      await tester.pump(const Duration(seconds: 2));
      final beranda = find.byType(HomeScreen);
      final ai = find.byType(SearchScreen);
      double puncak() => tester.getTopLeft(ai).dy;

      await tester.tap(
        find.descendant(
          of: find.byType(NavBawah),
          matching: find.text('Tanya AI'),
        ),
      );
      await tester.pump(const Duration(milliseconds: 120));
      // di tengah gerak: beranda masih terlihat di bawah lembar yang naik
      expect(beranda, findsOneWidget);
      expect(puncak(), greaterThan(0));
      await tester.pump(const Duration(seconds: 1));
      expect(puncak(), 0);
      expect(beranda, findsNothing); // offstage

      await tester.binding.handlePopRoute();
      await tester.pump(); // frame pertama = awal gerak
      await tester.pump(const Duration(milliseconds: 120));
      expect(beranda, findsOneWidget);
      expect(puncak(), greaterThan(0)); // lembar AI turun
      await tester.pump(const Duration(seconds: 1));
      expect(ai, findsNothing);
      expect(rootTab.value, 0);
      await tester.pump(const Duration(seconds: 2));
    }, () => MockClient((_) async => http.Response('{}', 500)));
  });

  testWidgets('survei: galat tampil di isian, tidak ada yang terkirim', (
    tester,
  ) async {
    var terkirim = 0;
    await http.runWithClient(
      () async {
        await tester.pumpWidget(
          MaterialApp(
            locale: const Locale('id'),
            localizationsDelegates: AppLocalizations.localizationsDelegates,
            supportedLocales: AppLocalizations.supportedLocales,
            theme: appTheme(Brightness.light),
            home: const SurveyScreen(),
          ),
        );
        final kirim = find.text('Kirim Survei');
        await tester.scrollUntilVisible(
          kirim,
          300,
          scrollable: find.byType(Scrollable).first,
        );
        await tester.tap(kirim);
        await tester.pumpAndSettle();
        expect(terkirim, 0);
        // digulir kembali ke isian pertama yang bermasalah
        expect(find.text('Nama wajib diisi.'), findsOneWidget);

        // email salah format ditandai begitu diketik
        await tester.enterText(find.widgetWithText(TextField, 'Email'), 'abc');
        await tester.pump();
        expect(find.textContaining('Format email belum benar'), findsOneWidget);
        await tester.enterText(
          find.widgetWithText(TextField, 'Email'),
          'a@b.go.id',
        );
        await tester.pump();
        expect(find.textContaining('Format email belum benar'), findsNothing);
        expect(tester.takeException(), isNull);
      },
      () => MockClient((_) async {
        terkirim++;
        return http.Response('{}', 201);
      }),
    );
  });

  testWidgets('huruf sistem 200%: kartu dan data panjang tidak meluap', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(360, 1600);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);

    const panjang = <String, dynamic>{
      'id': 1,
      'judul':
          'Peraturan Daerah Kota Kendari tentang Perubahan Kedua atas '
          'Peraturan Daerah Nomor 2 Tahun 2012 tentang Retribusi Jasa Umum',
      'nomor_peraturan': '1234/KEP/2024',
      'tahun_terbit': '2024',
      'status': 'Berlaku, diubah sebagian oleh Perda Nomor 6 Tahun 2018',
      'singkatan_jenis': 'PERATURAN WALI KOTA',
      'bidang_hukum':
          'Hukum Administrasi Negara dan Tata Usaha Pemerintahan Daerah',
      'hit_see': 1234567,
      'hit_download': 89012,
    };
    const berita = <String, dynamic>{
      'id': 2,
      'judul': 'Tim IKK Kota Kendari Gelar Rapat Feedback Laporan IKK 2026',
      'tanggal': '2026-09-10',
      'ringkasan': 'Ringkasan kegiatan rapat pembahasan laporan.',
    };

    await tester.pumpWidget(
      MaterialApp(
        locale: const Locale('id'),
        localizationsDelegates: AppLocalizations.localizationsDelegates,
        supportedLocales: AppLocalizations.supportedLocales,
        theme: appTheme(Brightness.light),
        home: MediaQuery(
          data: const MediaQueryData(
            size: Size(360, 1600),
            textScaler: TextScaler.linear(2),
          ),
          child: Scaffold(
            body: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                const DocumentCard(panjang),
                const SizedBox(height: 12),
                MediaCard(berita, badge: 'Pemerintahan', onTap: () {}),
                const SizedBox(height: 12),
                FilterPills(
                  labels: const ['Peraturan', 'Monografi', 'Artikel'],
                  selected: 0,
                  onSelected: (_) {},
                ),
              ],
            ),
          ),
        ),
      ),
    );
    await tester.pump(const Duration(seconds: 1));
    expect(tester.takeException(), isNull);
    // angka besar dipisah titik
    expect(find.text('1.234.567'), findsOneWidget);
  });

  testWidgets('kartu dokumen: badge status rata kanan', (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        locale: const Locale('id'),
        localizationsDelegates: AppLocalizations.localizationsDelegates,
        supportedLocales: AppLocalizations.supportedLocales,
        theme: appTheme(Brightness.light),
        home: const Scaffold(
          body: Padding(
            padding: EdgeInsets.all(16),
            child: DocumentCard({
              'id': 1,
              'judul': 'Perda',
              'nomor_peraturan': '5',
              'status': 'Berlaku',
              'singkatan_jenis': 'PERDA',
            }),
          ),
        ),
      ),
    );
    await tester.pump(const Duration(seconds: 1));
    final kartu = tester.getTopRight(find.byType(DocumentCard)).dx;
    // kanan badge sejajar tepi isi kartu (padding 12 + garis 1)
    expect(
      tester.getTopRight(find.byType(StatusBadge)).dx,
      closeTo(kartu - 13, 1),
    );
  });

  testWidgets('beranda: ubin kategori dua kolom saat huruf diperbesar', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(360, 2400);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);
    const beranda = {
      'data': {
        'statistik': {'peraturan': 996, 'monografi': 13},
        'peraturan_terbaru': [],
        'berita': [],
        'pengumuman': [],
      },
    };
    Future<double> selisihBaris(double skala) async {
      await tester.pumpWidget(
        MaterialApp(
          locale: const Locale('id'),
          localizationsDelegates: AppLocalizations.localizationsDelegates,
          supportedLocales: AppLocalizations.supportedLocales,
          theme: appTheme(Brightness.light),
          home: MediaQuery(
            data: MediaQueryData(
              size: const Size(360, 2400),
              textScaler: TextScaler.linear(skala),
            ),
            child: const HomeScreen(),
          ),
        ),
      );
      await tester.pump(const Duration(seconds: 1));
      await tester.pump(const Duration(seconds: 1));
      expect(tester.takeException(), isNull);
      return tester.getTopLeft(find.text('Artikel')).dy -
          tester.getTopLeft(find.text('Peraturan')).dy;
    }

    await http.runWithClient(
      () async {
        expect(await selisihBaris(1), 0); // empat sebaris
        expect(await selisihBaris(2), greaterThan(0)); // pindah baris
        await tester.pumpWidget(const SizedBox());
        await tester.pump(const Duration(seconds: 2));
      },
      () => MockClient(
        (_) async => http.Response(
          jsonEncode(beranda),
          200,
          headers: {'content-type': 'application/json'},
        ),
      ),
    );
  });

  testWidgets('judul seksi: tautan "Lihat semua" rata kanan', (tester) async {
    await tester.pumpWidget(
      MaterialApp(
        locale: const Locale('id'),
        localizationsDelegates: AppLocalizations.localizationsDelegates,
        supportedLocales: AppLocalizations.supportedLocales,
        theme: appTheme(Brightness.light),
        home: Scaffold(body: SectionHeader('Dokumen', onSeeAll: () {})),
      ),
    );
    expect(
      tester.getTopRight(find.byType(TextButton)).dx,
      tester.getTopRight(find.byType(SectionHeader)).dx,
    );
    expect(tester.takeException(), isNull);
  });

  testWidgets('detail dokumen 200%: hero, spesifikasi, bilah aksi utuh', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(360, 780);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);
    final detail = {
      'data': {
        'id': 7,
        'judul':
            'PERATURAN WALI KOTA KENDARI NOMOR 21 TAHUN 2026 TENTANG STANDAR '
            'PELAYANAN MINIMAL PADA BADAN LAYANAN UMUM DAERAH',
        'jenis_peraturan': 'PERATURAN WALI KOTA KENDARI',
        'singkatan_jenis': 'PERWALI',
        'nomor_peraturan': '21',
        'tahun_terbit': '2026',
        'status': 'Berlaku',
        'bahasa': 'Indonesia',
        'lampiran': [
          {'judul': 'Dokumen', 'url': 'https://h/storage/dokumen/a.pdf'},
        ],
        'statistik': {'dilihat': 1234567, 'diunduh': 89012},
      },
    };
    await http.runWithClient(
      () async {
        await tester.pumpWidget(
          MaterialApp(
            locale: const Locale('id'),
            localizationsDelegates: AppLocalizations.localizationsDelegates,
            supportedLocales: AppLocalizations.supportedLocales,
            theme: appTheme(Brightness.light),
            home: MediaQuery(
              data: const MediaQueryData(
                size: Size(360, 780),
                textScaler: TextScaler.linear(2),
              ),
              child: const DocumentDetailScreen(id: 7),
            ),
          ),
        );
        await tester.pump(const Duration(seconds: 1));
        await tester.pump(const Duration(seconds: 1));
        expect(tester.takeException(), isNull);
        expect(find.textContaining('1.234.567 dilihat'), findsOneWidget);
      },
      () => MockClient(
        (_) async => http.Response(
          jsonEncode(detail),
          200,
          headers: {'content-type': 'application/json'},
        ),
      ),
    );
  });

  testWidgets('statistik: batang per jenis benar-benar tergambar', (
    tester,
  ) async {
    await tester.pumpWidget(
      MaterialApp(
        locale: const Locale('id'),
        localizationsDelegates: AppLocalizations.localizationsDelegates,
        supportedLocales: AppLocalizations.supportedLocales,
        theme: appTheme(Brightness.light),
        home: const Scaffold(
          body: SizedBox(
            width: 300,
            child: BarRow(label: 'PERATURAN WALIKOTA', value: 620, max: 620),
          ),
        ),
      ),
    );
    await tester.pumpAndSettle();
    // dulu setinggi 0: DecoratedBox tanpa anak di bawah batas tinggi longgar
    final batang = tester.getSize(
      find.descendant(
        of: find.byType(FractionallySizedBox),
        matching: find.byType(DecoratedBox),
      ),
    );
    expect(batang.height, greaterThan(0));
    expect(batang.width, greaterThan(250));
  });
}
