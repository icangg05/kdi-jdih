import 'package:flutter_test/flutter_test.dart';
import 'package:jdih_kendari/widgets.dart';

void main() {
  test('readTime: tag HTML tidak dihitung sebagai kata', () {
    expect(readTime('<p>satu dua tiga</p>'), '1 menit baca');
    expect(readTime('<p>${'kata ' * 400}</p>'), '2 menit baca');
    expect(readTime(''), '1 menit baca');
  });
}
