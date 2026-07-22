import 'package:flutter_test/flutter_test.dart';
import 'package:jdih_kendari/api.dart';

void main() {
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
