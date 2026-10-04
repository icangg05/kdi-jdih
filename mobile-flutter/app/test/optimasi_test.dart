import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:http/http.dart' as http;
import 'package:http/testing.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:jdih_kendari/main.dart';
import 'package:jdih_kendari/theme.dart';
import 'package:jdih_kendari/widgets.dart';

void main() {
  setUpAll(() => initializeDateFormatting('id'));

  testWidgets('cold start hanya memuat beranda; tab tersembunyi tidak '
      'meminta frame', (tester) async {
    final diminta = <String>[];
    await http.runWithClient(
      () async {
        rootTab.value = 0;
        await tester.pumpWidget(
          MaterialApp(
            theme: appTheme(Brightness.light),
            home: const RootShell(),
          ),
        );
        await tester.pump(const Duration(seconds: 1));
        // dulu 5: /home dua kali, /documents, /news, statistik
        expect(diminta, ['/api/v1/home', '/api/jdih/statistics']);

        // Kabar dibuka saat jaringan lambat (kerangka muat berkilau), lalu
        // pengguna kembali ke Beranda sebelum isinya tiba
        rootTab.value = 3;
        await tester.pump(const Duration(seconds: 1));
        expect(diminta, contains('/api/v1/news'));
        rootTab.value = 0;
        // fade antar-tab 200 ms selesai; sesudahnya layar diam
        for (var i = 0; i < 30; i++) {
          await tester.pump(const Duration(milliseconds: 16));
        }
        expect(SchedulerBinding.instance.hasScheduledFrame, isFalse);

        await tester.pumpWidget(const SizedBox());
        await tester.pump(const Duration(seconds: 30));
      },
      () => MockClient((req) async {
        diminta.add(req.url.path);
        final cepat =
            req.url.path.endsWith('/home') ||
            req.url.path.endsWith('/statistics');
        if (!cepat) await Future.delayed(const Duration(seconds: 20));
        return http.Response(
          jsonEncode({
            'data': cepat ? {'statistik': {}} : [],
          }),
          200,
          headers: {'content-type': 'application/json'},
        );
      }),
    );
  });
}
