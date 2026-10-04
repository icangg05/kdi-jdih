import 'dart:io';

import 'package:flutter_test/flutter_test.dart';

/// Setiap teks memakai peran `T` di theme.dart. Ukuran font literal di layar
/// lain adalah awal kembalinya skala acak (dulu 21 ukuran berbeda).
void main() {
  test('tidak ada ukuran font literal di luar theme.dart', () {
    final pelanggar = [
      for (final f in Directory('lib').listSync(recursive: true))
        if (f is File &&
            f.path.endsWith('.dart') &&
            !f.path.endsWith('theme.dart'))
          for (final (i, baris) in f.readAsLinesSync().indexed)
            if (RegExp(r'fontSize:\s*[0-9]').hasMatch(baris))
              '${f.path}:${i + 1}',
    ];
    expect(pelanggar, isEmpty, reason: 'pakai peran T.*, bukan fontSize');
  });
}
