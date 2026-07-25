import 'package:flutter/material.dart';
import 'package:intl/intl.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';

/// Palet grafik — lolos `validate_palette.js` terhadap permukaan putih:
/// pita terang, lantai kroma, pemisahan buta warna, dan kontras.
class ChartColors {
  /// Satu hue untuk seluruh batang. Kategori dokumen tidak punya urutan
  /// alami, jadi warna TIDAK ikut nilainya — panjang batang sudah
  /// menyampaikan besarannya.
  static const bar = Color(0xFFEA580C); // 3,56:1 di atas putih

  /// Status hukum: makna, bukan identitas. Selalu disertai ikon + label.
  static const berlaku = Color(0xFF15803D);
  static const diubah = Color(0xFFDE8C0A);
  static const dicabut = Color(0xFFC81E1E);

  /// Track batang: satu langkah dari permukaan, netral.
  static Color track = C.ink.withValues(alpha: .07);
}

final _n = NumberFormat.decimalPattern('id');

/// Sebuah irisan data bernama: (label, nilai).
typedef Slice = ({String label, int value});

/// Ringkasan angka koleksi — seluruhnya dari satu panggilan
/// `GET /api/jdih/statistics`.
class StatistikScreen extends StatefulWidget {
  const StatistikScreen({super.key});

  @override
  State<StatistikScreen> createState() => _StatistikScreenState();
}

class _StatistikScreenState extends State<StatistikScreen> {
  late Future<Json> _future = api.statistics();

  void _reload() {
    setState(() {
      _future = api.statistics();
    });
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Statistik Koleksi'),
        body: FutureBuilder<Json>(
          future: _future,
          builder: (context, snap) {
            if (snap.hasError) {
              return ErrorRetry('${snap.error}', onRetry: _reload);
            }
            if (!snap.hasData) return const SkeletonList(count: 4, height: 170);
            final d = snap.data!;
            return RefreshIndicator(
              onRefresh: () async => _reload(),
              child: ListView(
                padding: const EdgeInsets.all(AppSpacing.lg),
                children: [
                  RingkasanCard(
                    total: d.i('total_dokumen'),
                    dilihat: d.i('total_views'),
                    diunduh: d.i('total_downloads'),
                  ),
                  const SizedBox(height: AppSpacing.md),
                  TahunChart(_slices(d.l('dokumen_per_tahun'), 'tahun')),
                  const SizedBox(height: AppSpacing.md),
                  JenisChart(_slices(d.l('dokumen_per_tipe'), 'tipe')),
                  const SizedBox(height: AppSpacing.md),
                  StatusChart(_statusBuckets(d.l('dokumen_per_status'))),
                  const SizedBox(height: AppSpacing.md),
                  TerpopulerCard(d.l('most_viewed')),
                  const SizedBox(height: AppSpacing.lg),
                  Text(
                      'Diperbarui ${d.sn('last_updated') ?? '-'}. '
                      'Angka dihitung langsung dari basis data JDIH Kota Kendari.',
                      style: const TextStyle(
                          fontSize: 12, height: 1.5, color: C.lightInkMuted)),
                ],
              ),
            );
          },
        ),
      );

  static List<Slice> _slices(List<Json> rows, String labelKey) =>
      [for (final r in rows) (label: r.s(labelKey), value: r.i('total'))]
          .where((s) => s.label.isNotEmpty && s.value > 0)
          .toList();

  /// Teks status dari basis data bebas bentuk, jadi dipetakan lewat
  /// [statusKindOf] — sumber kebenaran yang sama dengan badge di daftar.
  static Map<StatusKind, int> _statusBuckets(List<Json> rows) {
    final out = <StatusKind, int>{};
    for (final r in rows) {
      final k = statusKindOf(r.sn('status'));
      out[k] = (out[k] ?? 0) + r.i('total');
    }
    return out;
  }
}

// ============================================================
//  RINGKASAN — angka, bukan grafik
// ============================================================

/// Satu angka utama memimpin halaman; dua angka pendukung di sampingnya.
/// Nilai tunggal adalah pekerjaan angka besar, bukan grafik satu batang.
class RingkasanCard extends StatelessWidget {
  const RingkasanCard(
      {super.key,
      required this.total,
      required this.dilihat,
      required this.diunduh});
  final int total, dilihat, diunduh;

  @override
  Widget build(BuildContext context) => Card(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text('Total dokumen hukum',
                  style: TextStyle(fontSize: 13, color: C.lightInkMuted)),
              Text(_n.format(total),
                  style: const TextStyle(
                      fontSize: 40,
                      height: 1.1,
                      fontWeight: FontWeight.w800,
                      fontFeatures: [FontFeature.tabularFigures()])),
              const SizedBox(height: AppSpacing.lg),
              Row(children: [
                Expanded(
                    child: _Kpi(Icons.visibility_outlined, 'Dilihat', dilihat)),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                    child: _Kpi(Icons.download_outlined, 'Diunduh', diunduh)),
              ]),
            ],
          ),
        ),
      );
}

class _Kpi extends StatelessWidget {
  const _Kpi(this.icon, this.label, this.value);
  final IconData icon;
  final String label;
  final int value;

  @override
  Widget build(BuildContext context) => Semantics(
        label: '$label: ${_n.format(value)}',
        excludeSemantics: true,
        child: Container(
          padding: const EdgeInsets.symmetric(
              horizontal: AppSpacing.md, vertical: AppSpacing.md),
          decoration: BoxDecoration(
            color: C.lightSubtle,
            borderRadius: BorderRadius.circular(AppRadius.card),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(children: [
                Icon(icon, size: 14, color: C.lightInkMuted),
                const SizedBox(width: 4),
                Text(label,
                    style: const TextStyle(
                        fontSize: 12, color: C.lightInkMuted)),
              ]),
              const SizedBox(height: 2),
              Text(_n.format(value),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w700,
                      fontFeatures: [FontFeature.tabularFigures()])),
            ],
          ),
        ),
      );
}

// ============================================================
//  DOKUMEN PER TAHUN — kolom, urut naik
// ============================================================

/// Tren jumlah dokumen antar tahun. API mengirim urutan menurun; deret waktu
/// harus dibaca kiri ke kanan, jadi dibalik.
class TahunChart extends StatelessWidget {
  const TahunChart(this.data, {super.key});
  final List<Slice> data;

  @override
  Widget build(BuildContext context) {
    final rows = data.reversed.toList();
    if (rows.isEmpty) return const SizedBox.shrink();
    final maks = rows.map((e) => e.value).reduce((a, b) => a > b ? a : b);
    final puncak = rows.indexWhere((e) => e.value == maks);
    final reduce = MediaQuery.of(context).disableAnimations;

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const ChartTitle('Dokumen per tahun',
                'Jumlah dokumen menurut tahun terbit'),
            const SizedBox(height: AppSpacing.lg),
            SizedBox(
              height: 152,
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  for (final (i, e) in rows.indexed)
                    Expanded(
                      child: Semantics(
                        label: '${e.label}: ${_n.format(e.value)} dokumen',
                        excludeSemantics: true,
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            // label nilai hanya pada puncak — angka di setiap
                            // kolom jadi kekacauan dan tidak terbaca
                            SizedBox(
                              height: 16,
                              child: i == puncak
                                  ? Text(_n.format(e.value),
                                      style: const TextStyle(
                                          fontSize: 11,
                                          fontWeight: FontWeight.w700,
                                          fontFeatures: [
                                            FontFeature.tabularFigures()
                                          ]))
                                  : null,
                            ),
                            Expanded(
                              child: Align(
                                alignment: Alignment.bottomCenter,
                                child: SizedBox(
                                  width: 20, // batang tipis, sisanya udara
                                  child: TweenAnimationBuilder<double>(
                                    tween: Tween(
                                        begin: reduce ? e.value / maks : 0,
                                        end: e.value / maks),
                                    duration: Duration(
                                        milliseconds: reduce ? 0 : 320),
                                    curve: Curves.easeOutCubic,
                                    builder: (_, t, __) =>
                                        FractionallySizedBox(
                                      heightFactor: t.clamp(0.02, 1.0),
                                      alignment: Alignment.bottomCenter,
                                      child: const DecoratedBox(
                                        decoration: BoxDecoration(
                                          color: ChartColors.bar,
                                          // ujung data membulat, pangkal siku
                                          borderRadius: BorderRadius.vertical(
                                              top: Radius.circular(4)),
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(e.label,
                                maxLines: 1,
                                overflow: TextOverflow.clip,
                                style: const TextStyle(
                                    fontSize: 9.5, color: C.lightInkMuted)),
                          ],
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

// ============================================================
//  DOKUMEN PER JENIS — batang mendatar
// ============================================================

/// Perbandingan besaran antar jenis dokumen. Basis data punya belasan jenis;
/// di atas ~7 kelas warna dan baris sama-sama kehilangan makna, jadi ekor
/// daftar dilipat jadi "Lainnya" alih-alih ditambah warna baru.
class JenisChart extends StatelessWidget {
  const JenisChart(this.data, {super.key, this.tampil = 6});
  final List<Slice> data;
  final int tampil;

  @override
  Widget build(BuildContext context) {
    if (data.isEmpty) return const SizedBox.shrink();
    final urut = [...data]..sort((a, b) => b.value.compareTo(a.value));
    final atas = urut.take(tampil).toList();
    final sisa = urut.skip(tampil).fold(0, (a, e) => a + e.value);
    final rows = <Slice>[
      ...atas,
      if (sisa > 0) (label: 'Lainnya (${urut.length - tampil} jenis)', value: sisa),
    ];
    final maks = rows.map((e) => e.value).reduce((a, b) => a > b ? a : b);

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const ChartTitle('Dokumen per jenis',
                'Tujuh jenis dengan koleksi terbanyak'),
            const SizedBox(height: AppSpacing.lg),
            for (final (i, e) in rows.indexed) ...[
              if (i > 0) const SizedBox(height: AppSpacing.md),
              BarRow(label: e.label, value: e.value, max: maks),
            ],
          ],
        ),
      ),
    );
  }
}

/// Satu batang mendatar: nama di kiri, nilai di ujung, track netral.
class BarRow extends StatelessWidget {
  const BarRow(
      {super.key, required this.label, required this.value, required this.max});
  final String label;
  final int value, max;

  @override
  Widget build(BuildContext context) {
    final ratio = max <= 0 ? 0.0 : value / max;
    final reduce = MediaQuery.of(context).disableAnimations;
    return Semantics(
      label: '$label: ${_n.format(value)} dokumen',
      excludeSemantics: true,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(children: [
            Expanded(
              child: Text(label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                      fontSize: 13, fontWeight: FontWeight.w600)),
            ),
            const SizedBox(width: AppSpacing.sm),
            // nilai memakai token teks, bukan warna data
            Text(_n.format(value),
                style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w700,
                    fontFeatures: [FontFeature.tabularFigures()])),
          ]),
          const SizedBox(height: 6),
          ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: Container(
              height: 10,
              color: ChartColors.track,
              child: Align(
                alignment: Alignment.centerLeft,
                child: TweenAnimationBuilder<double>(
                  tween: Tween(begin: reduce ? ratio : 0, end: ratio),
                  duration: Duration(milliseconds: reduce ? 0 : 320),
                  curve: Curves.easeOutCubic,
                  builder: (_, t, __) => FractionallySizedBox(
                    widthFactor: t.clamp(0.0, 1.0),
                    child: const DecoratedBox(
                      decoration: BoxDecoration(
                        color: ChartColors.bar,
                        borderRadius:
                            BorderRadius.horizontal(right: Radius.circular(4)),
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

// ============================================================
//  STATUS KEBERLAKUAN — bagian terhadap keseluruhan
// ============================================================

/// Warna status tidak pernah berdiri sendiri: ikon dan label selalu
/// menyertai, karena hijau/merah adalah pasangan tersulit bagi pembaca
/// buta warna.
class StatusChart extends StatelessWidget {
  const StatusChart(this.status, {super.key});
  final Map<StatusKind, int> status;

  static const _rows = [
    (
      StatusKind.berlaku,
      'Berlaku',
      Icons.check_circle_outline,
      ChartColors.berlaku
    ),
    (StatusKind.diubah, 'Diubah', Icons.edit_outlined, ChartColors.diubah),
    (StatusKind.dicabut, 'Tidak berlaku', Icons.cancel_outlined, ChartColors.dicabut),
  ];

  @override
  Widget build(BuildContext context) {
    final data = [
      for (final r in _rows) (row: r, value: status[r.$1] ?? 0),
    ].where((e) => e.value > 0).toList();
    final total = data.fold(0, (a, e) => a + e.value);

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const ChartTitle('Status keberlakuan',
                'Bagian koleksi yang masih berlaku'),
            const SizedBox(height: AppSpacing.lg),
            if (total == 0)
              const Text('Data status belum tersedia.',
                  style: TextStyle(fontSize: 13, color: C.lightInkMuted))
            else ...[
              ClipRRect(
                borderRadius: BorderRadius.circular(4),
                child: SizedBox(
                  height: 14,
                  child: Row(children: [
                    for (final (i, e) in data.indexed) ...[
                      // celah 2px berwarna permukaan memisahkan segmen —
                      // bukan garis tepi, yang menambah tinta non-data
                      if (i > 0) const SizedBox(width: 2),
                      Expanded(
                          flex: e.value, child: ColoredBox(color: e.row.$4)),
                    ],
                  ]),
                ),
              ),
              const SizedBox(height: AppSpacing.lg),
              for (final (i, e) in data.indexed) ...[
                if (i > 0) const SizedBox(height: AppSpacing.sm),
                _LegendRow(
                  color: e.row.$4,
                  icon: e.row.$3,
                  label: e.row.$2,
                  value: e.value,
                  percent: e.value / total,
                ),
              ],
            ],
          ],
        ),
      ),
    );
  }
}

class _LegendRow extends StatelessWidget {
  const _LegendRow(
      {required this.color,
      required this.icon,
      required this.label,
      required this.value,
      required this.percent});
  final Color color;
  final IconData icon;
  final String label;
  final int value;
  final double percent;

  @override
  Widget build(BuildContext context) {
    final pct = '${(percent * 100).round()}%';
    return Semantics(
      label: '$label: ${_n.format(value)} dokumen, $pct',
      excludeSemantics: true,
      child: Row(children: [
        Icon(icon, size: 16, color: color),
        const SizedBox(width: 6),
        Container(
          width: 10,
          height: 10,
          decoration: BoxDecoration(color: color, shape: BoxShape.circle),
        ),
        const SizedBox(width: AppSpacing.sm),
        Expanded(
          child: Text(label,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 13)),
        ),
        Text('${_n.format(value)}  ·  $pct',
            style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                fontFeatures: [FontFeature.tabularFigures()])),
      ]),
    );
  }
}

// ============================================================
//  PALING BANYAK DILIHAT — daftar berperingkat, bukan grafik
// ============================================================

/// Judul dokumen terlalu panjang untuk sumbu grafik; daftar berperingkat
/// membaca lebih cepat dan sekaligus bisa diketuk.
class TerpopulerCard extends StatelessWidget {
  const TerpopulerCard(this.items, {super.key});
  final List<Json> items;

  @override
  Widget build(BuildContext context) => Card(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const ChartTitle('Paling banyak dilihat',
                  'Lima dokumen dengan pembaca terbanyak'),
              const SizedBox(height: AppSpacing.md),
              if (items.isEmpty)
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: AppSpacing.md),
                  child: Text(
                      'Belum ada dokumen yang tercatat dibaca. Angka ini mulai '
                      'terisi setelah pengunjung membuka halaman dokumen.',
                      style: TextStyle(
                          fontSize: 13, height: 1.5, color: C.lightInkMuted)),
                )
              else
                for (final (i, d) in items.indexed)
                  ListTile(
                    contentPadding: EdgeInsets.zero,
                    minLeadingWidth: 28,
                    leading: Text('${i + 1}',
                        style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w800,
                            color: C.lightInkMuted)),
                    title: Text(d.s('judul'),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                            fontSize: 13, fontWeight: FontWeight.w600)),
                    subtitle: Text('${_n.format(d.i('views'))} kali dilihat',
                        style: const TextStyle(fontSize: 12)),
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) =>
                                DocumentDetailScreen(id: d.i('id')))),
                  ),
            ],
          ),
        ),
      );
}

class ChartTitle extends StatelessWidget {
  const ChartTitle(this.title, this.subtitle, {super.key});
  final String title, subtitle;

  @override
  Widget build(BuildContext context) => Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(title,
              style:
                  const TextStyle(fontSize: 16, fontWeight: FontWeight.w700)),
          const SizedBox(height: 2),
          Text(subtitle,
              style: const TextStyle(fontSize: 12, color: C.lightInkMuted)),
        ],
      );
}
