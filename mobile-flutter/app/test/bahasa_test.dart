import 'dart:convert';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:jdih_kendari/api.dart';
import 'package:jdih_kendari/main.dart';
import 'package:jdih_kendari/widgets.dart';

/// Teks UI multi-bahasa (lib/l10n/app_*.arb).
void main() {
  setUpAll(initializeDateFormatting);
  tearDown(() => langNotifier.value = 'id');

  test('ARB: tiap bahasa punya semua kunci dan placeholder yang sama', () {
    Map<String, dynamic> baca(String l) =>
        jsonDecode(File('lib/l10n/app_$l.arb').readAsStringSync());
    final id = baca('id');
    final kunci = id.keys.where((k) => !k.startsWith('@')).toSet();
    // nama placeholder saja, bukan isi cabang plural ({count} dokumen)
    Set<String> ph(String k) => {
      ...((id['@$k']?['placeholders'] as Map?)?.keys.cast<String>() ?? []),
    };
    for (final l in ['en', 'zh', 'ko']) {
      final arb = baca(l);
      expect(
        arb.keys.where((k) => !k.startsWith('@')).toSet(),
        kunci,
        reason: l,
      );
      for (final k in kunci) {
        final teks = arb[k] as String;
        expect(teks.trim(), isNotEmpty, reason: '$l.$k');
        for (final p in ph(k)) {
          expect(teks, contains('{$p'), reason: '$l.$k kehilangan {$p}');
        }
      }
    }
  });

  test('kode tanpa context (galat API, tanggal, waktu baca) ikut bahasa', () {
    expect(fmtDate('2023-05-01'), '1 Mei 2023');
    expect(readTime('kata'), '1 menit baca');
    langNotifier.value = 'en';
    expect(
      l10nAktif.errTimeout,
      'The server is slow to respond. Please try again.',
    );
    expect(fmtDate('2023-05-01'), 'May 1, 2023');
    expect(readTime('kata'), '1 min read');
    langNotifier.value = 'zh';
    expect(fmtDate('2023-05-01'), '2023年5月1日');
  });

  testWidgets('navigasi & status mengikuti bahasa aktif', (tester) async {
    Future<void> pasang(String lang) => tester.pumpWidget(
      MaterialApp(
        locale: Locale(lang),
        localizationsDelegates: AppLocalizations.localizationsDelegates,
        supportedLocales: AppLocalizations.supportedLocales,
        home: Scaffold(
          body: const Column(
            children: [StatusBadge('BERLAKU'), StatusBadge('Diubah dgn Perda')],
          ),
          bottomNavigationBar: NavBawah(index: 0, onTap: (_) {}),
        ),
      ),
    );

    await pasang('en');
    expect(find.text('Home'), findsOneWidget);
    expect(find.text('Ask AI'), findsOneWidget);
    expect(find.text('In force'), findsOneWidget);
    // status bebas-isi dari basis data tampil apa adanya
    expect(find.text('Diubah dgn Perda'), findsOneWidget);

    await pasang('ko');
    expect(find.text('홈'), findsOneWidget);
    expect(find.text('시행 중'), findsOneWidget);
  });

  testWidgets('navigasi bawah tidak meluap di bahasa lain (320dp, teks 200%)', (
    tester,
  ) async {
    tester.view.physicalSize = const Size(320, 640);
    tester.view.devicePixelRatio = 1;
    addTearDown(tester.view.reset);
    for (final lang in ['en', 'zh', 'ko']) {
      await tester.pumpWidget(
        MaterialApp(
          locale: Locale(lang),
          localizationsDelegates: AppLocalizations.localizationsDelegates,
          supportedLocales: AppLocalizations.supportedLocales,
          home: MediaQuery(
            data: const MediaQueryData(
              size: Size(320, 640),
              textScaler: TextScaler.linear(2),
            ),
            child: Scaffold(
              bottomNavigationBar: NavBawah(index: 0, onTap: (_) {}),
            ),
          ),
        ),
      );
      expect(tester.takeException(), isNull, reason: lang);
    }
  });
}
