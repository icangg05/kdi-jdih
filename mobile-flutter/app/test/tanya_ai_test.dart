import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:jdih_kendari/api.dart';
import 'package:jdih_kendari/main.dart';
import 'package:jdih_kendari/screens/lainnya.dart';
import 'package:jdih_kendari/screens/search.dart';
import 'package:jdih_kendari/theme.dart';
import 'package:jdih_kendari/widgets.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Jawaban AI tiruan; jalur lain kosong.
http.Client _api() => MockClient((req) async {
  final Object body = req.url.path.endsWith('/ai-search')
      ? {'explanation': 'Jawaban.', 'documents': []}
      : {'data': {}};
  return http.Response(
    jsonEncode(body),
    200,
    headers: {'content-type': 'application/json'},
  );
});

Widget _app(Widget home) => MaterialApp(
  locale: const Locale('id'),
  localizationsDelegates: AppLocalizations.localizationsDelegates,
  supportedLocales: AppLocalizations.supportedLocales,
  theme: appTheme(Brightness.light),
  home: home,
);

void main() {
  setUpAll(() => initializeDateFormatting('id'));

  test('riwayat tersimpan; pertanyaan ke-11 memulai percakapan baru', () async {
    SharedPreferences.setMockInitialValues({});
    final prefs = await SharedPreferences.getInstance();
    final p = PercakapanAi()..muat(prefs);
    List<dynamic> tersimpan() => jsonDecode(prefs.getString('tanya_ai')!);

    await http.runWithClient(() async {
      for (var i = 1; i <= 10; i++) {
        await p.tanya('t$i', tanpaJawaban: '-');
      }
      expect(p.jumlahPertanyaan, 10);
      expect(p.penuh, isTrue);
      expect(tersimpan(), hasLength(10));

      await p.tanya('t11', tanpaJawaban: '-');
      expect(p.jumlahPertanyaan, 1);
      expect(tersimpan().single['q'], 't11');
    }, _api);

    // app dibuka lagi: percakapan pulih
    expect((PercakapanAi()..muat(prefs)).jumlahPertanyaan, 1);

    // melewati batas (bukan tulisan app ini): mulai bersih
    await prefs.setString(
      'tanya_ai',
      jsonEncode(List.filled(11, {'q': 'x', 'a': 'y'})),
    );
    expect((PercakapanAi()..muat(prefs)).jumlahPertanyaan, 0);
    expect(prefs.getString('tanya_ai'), isNull);
  });

  testWidgets('navigasi bawah dan beranda membuka percakapan yang sama', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(390, 844);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);
    SharedPreferences.setMockInitialValues({});
    percakapanAi.muat(await SharedPreferences.getInstance());
    addTearDown(percakapanAi.reset);

    await http.runWithClient(() async {
      rootTab.value = 0;
      await tester.pumpWidget(_app(const RootShell()));
      await tester.pump(const Duration(seconds: 1));

      await tester.tap(
        find.descendant(
          of: find.byType(NavBawah),
          matching: find.text('Tanya AI'),
        ),
      );
      await tester.pump(); // frame pertama = awal gerak
      await tester.pump(const Duration(seconds: 1));
      await tester.enterText(find.byType(TextField), 'Apa itu Perwali?');
      await tester.tap(find.byTooltip('Kirim'));
      await tester.pump(const Duration(seconds: 1));
      await tester.pump(const Duration(seconds: 7)); // efek ketik
      expect(find.text('Apa itu Perwali?'), findsOneWidget);

      await tester.binding.handlePopRoute();
      await tester.pump();
      await tester.pump(const Duration(seconds: 1));
      expect(find.byType(SearchScreen), findsNothing);

      // pintu beranda: isinya sama, tidak mulai dari kosong
      final mulai = find.text('Mulai bertanya');
      await tester.scrollUntilVisible(
        mulai,
        200,
        scrollable: find.byType(Scrollable).first,
      );
      await tester.tap(mulai);
      await tester.pump(); // frame pertama = awal gerak
      await tester.pump(const Duration(seconds: 1));
      expect(find.text('Apa itu Perwali?'), findsOneWidget);
      expect(find.text('Jawaban.'), findsOneWidget);

      await tester.binding.handlePopRoute();
      await tester.pump(const Duration(seconds: 2));
    }, _api);
  });

  testWidgets('struktur organisasi: bagan bawaan, tanpa memanggil API', (
    tester,
  ) async {
    var diminta = 0;
    await http.runWithClient(
      () async {
        await tester.pumpWidget(
          _app(
            const ProfilScreen(kategori: 'sto', title: 'Struktur Organisasi'),
          ),
        );
        await tester.pump();
        expect(diminta, 0);
        expect(find.text('Bagan Struktur Organisasi (1)'), findsOneWidget);

        // ketuk: layar penuh yang bisa dicubit
        await tester.tap(find.text('Bagan Struktur Organisasi (1)'));
        await tester.pumpAndSettle();
        expect(find.byType(InteractiveViewer), findsOneWidget);
      },
      () => MockClient((_) async {
        diminta++;
        return http.Response('{}', 500);
      }),
    );
  });
}
