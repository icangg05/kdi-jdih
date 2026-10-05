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
  static const bar = C.accent; // biru = data; 6,9:1 di atas putih

  /// Status hukum: makna, bukan identitas. Selalu disertai ikon + label.
  static const berlaku = Color(0xFF15803D);
  static const diubah = Color(0xFFC2700A); // 3,7:1 (dulu 2,7:1, gagal)
  static const dicabut = Color(0xFFC81E1E);

  /// Track batang: satu langkah dari permukaan, netral.
  static Color track = C.ink.withValues(alpha: .07);
}

NumberFormat get _n => NumberFormat.decimalPattern(langNotifier.value);

/// Sebuah irisan data bernama: (label, nilai).
typedef Slice = ({String label, int value});

/// Ringkasan angka koleksi — seluruhnya dari satu panggilan
/// `GET /api/jdih/statistics`.
class StatistikScreen extends StatelessWidget {
  const StatistikScreen({super.key});

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.collectionStats),
    body: LoadView(
      load: api.statistics,
      contoh: const {
        'total_dokumen': 1234,
        'total_views': 12345,
        'total_downloads': 1234,
        'dokumen_per_tahun': [
          {'tahun': '2021', 'total': 40},
          {'tahun': '2022', 'total': 60},
          {'tahun': '2023', 'total': 50},
          {'tahun': '2024', 'total': 80},
          {'tahun': '2025', 'total': 70},
        ],
        'dokumen_per_status': [
          {'status': 'Berlaku', 'total': 3},
          {'status': 'Dicabut', 'total': 1},
        ],
        'dokumen_per_tipe': [
          {'tipe': 'Peraturan Daerah', 'total': 60},
          {'tipe': 'Peraturan Wali Kota', 'total': 40},
          {'tipe': 'Keputusan Wali Kota', 'total': 25},
        ],
        'most_viewed': [kSkeletonItem, kSkeletonItem, kSkeletonItem],
      },
      builder: (context, d) => ListView(
        padding: padTengah(context, maks: 1000),
        children: [
          RingkasanCard(
            total: d.i('total_dokumen'),
            dilihat: d.i('total_views'),
            diunduh: d.i('total_downloads'),
          ),
          const SizedBox(height: AppSpacing.md),
          // layar lebar: grafik berpasangan, bukan satu grafik selebar
          // tablet yang batangnya jadi pita tipis panjang
          ..._berpasangan(context, [
            TahunChart(_slices(d.l('dokumen_per_tahun'), 'tahun')),
            StatusChart(statusBuckets(d.l('dokumen_per_status'))),
            JenisChart(_slices(d.l('dokumen_per_tipe'), 'tipe')),
            TerpopulerCard(d.l('most_viewed')),
          ]),
          const SizedBox(height: AppSpacing.lg),
          Text(
            '${context.l10n.updatedAt(d.sn('last_updated') ?? '-')} '
            '${context.l10n.statsSource}',
            style: T.isiKecil.copyWith(color: C.lightInkMuted),
          ),
        ],
      ),
    ),
  );

  /// Kartu berurutan, dua sebaris bila lebar cukup (>= 720dp).
  static List<Widget> _berpasangan(BuildContext context, List<Widget> kartu) {
    final dua = MediaQuery.sizeOf(context).width >= 720;
    return [
      for (var i = 0; i < kartu.length; i += dua ? 2 : 1) ...[
        dua
            ? BarisKartu(kolom: 2, children: kartu.sublist(i, i + 2))
            : kartu[i],
        const SizedBox(height: AppSpacing.md),
      ],
    ];
  }

  static List<Slice> _slices(List<Json> rows, String labelKey) =>
      [for (final r in rows) (label: r.s(labelKey), value: r.i('total'))]
          .where((s) => s.label.isNotEmpty && s.value > 0)
          .toList();
}

/// Teks status dari basis data bebas bentuk, jadi dipetakan lewat
/// [statusKindOf] — sumber kebenaran yang sama dengan badge di daftar.
Map<StatusKind, int> statusBuckets(List<Json> rows) {
  final out = <StatusKind, int>{};
  for (final r in rows) {
    final k = statusKindOf(r.sn('status'));
    out[k] = (out[k] ?? 0) + r.i('total');
  }
  return out;
}

// ============================================================
//  RINGKASAN — angka, bukan grafik
// ============================================================

/// Satu angka utama memimpin halaman; dua angka pendukung di sampingnya.
/// Nilai tunggal adalah pekerjaan angka besar, bukan grafik satu batang.
class RingkasanCard extends StatelessWidget {
  const RingkasanCard({
    super.key,
    required this.total,
    required this.dilihat,
    required this.diunduh,
  });
  final int total, dilihat, diunduh;

  @override
  Widget build(BuildContext context) => Card(
    child: Padding(
      padding: const EdgeInsets.all(AppSpacing.lg),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            context.l10n.totalLegalDocs,
            style: T.isiKecil.copyWith(color: C.lightInkMuted),
          ),
          Text(_n.format(total), style: T.display),
          const SizedBox(height: AppSpacing.lg),
          Row(
            children: [
              Expanded(
                child: _Kpi(
                  Icons.visibility_outlined,
                  context.l10n.metaViews,
                  dilihat,
                ),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: _Kpi(
                  Icons.download_outlined,
                  context.l10n.metaDownloads,
                  diunduh,
                ),
              ),
            ],
          ),
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
        horizontal: AppSpacing.md,
        vertical: AppSpacing.md,
      ),
      decoration: BoxDecoration(
        color: C.lightSubtle,
        borderRadius: BorderRadius.circular(AppRadius.card),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(icon, size: 14, color: C.lightInkMuted),
              const SizedBox(width: 4),
              Flexible(
                child: Text(
                  label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: T.isiKecil.copyWith(color: C.lightInkMuted),
                ),
              ),
            ],
          ),
          const SizedBox(height: 2),
          Text(
            _n.format(value),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
            style: T.angka,
          ),
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
            ChartTitle(context.l10n.docsPerYear, context.l10n.docsPerYearSub),
            const SizedBox(height: AppSpacing.lg),
            SizedBox(
              height: 152,
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  for (final (i, e) in rows.indexed)
                    Expanded(
                      child: Semantics(
                        label: context.l10n.labelDocs(e.label, e.value),
                        excludeSemantics: true,
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            // label nilai hanya pada puncak — angka di setiap
                            // kolom jadi kekacauan dan tidak terbaca
                            SizedBox(
                              height: 16 * skalaTeks(context),
                              child: i == puncak
                                  ? Text(
                                      _n.format(e.value),
                                      style: T.labelKecil.copyWith(
                                        fontFeatures: [
                                          FontFeature.tabularFigures(),
                                        ],
                                      ),
                                    )
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
                                      end: e.value / maks,
                                    ),
                                    duration: Duration(
                                      milliseconds: reduce ? 0 : 320,
                                    ),
                                    curve: Curves.easeOutCubic,
                                    builder: (_, t, _) => FractionallySizedBox(
                                      heightFactor: t.clamp(0.02, 1.0),
                                      alignment: Alignment.bottomCenter,
                                      child: const DecoratedBox(
                                        decoration: BoxDecoration(
                                          color: ChartColors.bar,
                                          // ujung data membulat, pangkal siku
                                          borderRadius: BorderRadius.vertical(
                                            top: Radius.circular(
                                              AppRadius.chip,
                                            ),
                                          ),
                                        ),
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              e.label,
                              maxLines: 1,
                              overflow: TextOverflow.clip,
                              style: T.labelKecil.copyWith(
                                fontWeight: FontWeight.w400,
                                color: C.lightInkMuted,
                              ),
                            ),
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
      if (sisa > 0)
        (label: context.l10n.otherTypes(urut.length - tampil), value: sisa),
    ];
    final maks = rows.map((e) => e.value).reduce((a, b) => a > b ? a : b);

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ChartTitle(context.l10n.docsPerType, context.l10n.docsPerTypeSub),
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
  const BarRow({
    super.key,
    required this.label,
    required this.value,
    required this.max,
  });
  final String label;
  final int value, max;

  @override
  Widget build(BuildContext context) {
    final ratio = max <= 0 ? 0.0 : value / max;
    final reduce = MediaQuery.of(context).disableAnimations;
    return Semantics(
      label: context.l10n.labelDocs(label, value),
      excludeSemantics: true,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: T.label,
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              // nilai memakai token teks, bukan warna data
              Text(
                _n.format(value),
                style: T.label.copyWith(
                  fontWeight: FontWeight.w700,
                  fontFeatures: [FontFeature.tabularFigures()],
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          ClipRRect(
            borderRadius: BorderRadius.circular(AppRadius.chip),
            child: Container(
              height: 10,
              color: ChartColors.track,
              child: Align(
                alignment: Alignment.centerLeft,
                child: TweenAnimationBuilder<double>(
                  tween: Tween(begin: reduce ? ratio : 0, end: ratio),
                  duration: Duration(milliseconds: reduce ? 0 : 320),
                  curve: Curves.easeOutCubic,
                  // heightFactor wajib: Align memberi batas tinggi longgar,
                  // dan DecoratedBox tanpa anak lalu setinggi 0 (batang tak
                  // pernah tampil)
                  builder: (_, t, _) => FractionallySizedBox(
                    widthFactor: t.clamp(0.0, 1.0),
                    heightFactor: 1,
                    child: const DecoratedBox(
                      decoration: BoxDecoration(
                        color: ChartColors.bar,
                        borderRadius: BorderRadius.horizontal(
                          right: Radius.circular(AppRadius.chip),
                        ),
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

  static List<(StatusKind, String, IconData, Color)> _rows(
    AppLocalizations l,
  ) => [
    (
      StatusKind.berlaku,
      l.statusInForce,
      Icons.check_circle_outline,
      ChartColors.berlaku,
    ),
    (StatusKind.diubah, l.statusAmended, Icons.edit_outlined, ChartColors.diubah),
    (
      StatusKind.dicabut,
      l.statusNotInForce,
      Icons.cancel_outlined,
      ChartColors.dicabut,
    ),
  ];

  @override
  Widget build(BuildContext context) {
    final data = [
      for (final r in _rows(context.l10n)) (row: r, value: status[r.$1] ?? 0),
    ]
        .where((e) => e.value > 0)
        .toList();
    final total = data.fold(0, (a, e) => a + e.value);

    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ChartTitle(context.l10n.statusTitle, context.l10n.statusSub),
            const SizedBox(height: AppSpacing.lg),
            if (total == 0)
              Text(
                context.l10n.statusNoData,
                style: T.isiKecil.copyWith(color: C.lightInkMuted),
              )
            else ...[
              ClipRRect(
                borderRadius: BorderRadius.circular(AppRadius.chip),
                child: SizedBox(
                  height: 14,
                  // stretch: ColoredBox tanpa anak mengambil tinggi minimum,
                  // dan di Row biasa itu 0 — segmen tidak tergambar sama sekali
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      for (final (i, e) in data.indexed) ...[
                        // celah 2px berwarna permukaan memisahkan segmen —
                        // bukan garis tepi, yang menambah tinta non-data
                        if (i > 0) const SizedBox(width: 2),
                        Expanded(
                          flex: e.value,
                          child: ColoredBox(color: e.row.$4),
                        ),
                      ],
                    ],
                  ),
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
  const _LegendRow({
    required this.color,
    required this.icon,
    required this.label,
    required this.value,
    required this.percent,
  });
  final Color color;
  final IconData icon;
  final String label;
  final int value;
  final double percent;

  @override
  Widget build(BuildContext context) {
    final pct = '${(percent * 100).round()}%';
    return Semantics(
      label: context.l10n.labelDocsPct(label, value, pct),
      excludeSemantics: true,
      child: Row(
        children: [
          Icon(icon, size: 16, color: color),
          const SizedBox(width: 6),
          Container(
            width: 10,
            height: 10,
            decoration: BoxDecoration(color: color, shape: BoxShape.circle),
          ),
          const SizedBox(width: AppSpacing.sm),
          Expanded(
            child: Text(
              label,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: T.isiKecil,
            ),
          ),
          Text(
            '${_n.format(value)}  ·  $pct',
            style: T.label.copyWith(
              fontWeight: FontWeight.w700,
              fontFeatures: [FontFeature.tabularFigures()],
            ),
          ),
        ],
      ),
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
          ChartTitle(context.l10n.mostViewed, context.l10n.mostViewedSub),
          const SizedBox(height: AppSpacing.md),
          if (items.isEmpty)
            Padding(
              padding: EdgeInsets.symmetric(vertical: AppSpacing.md),
              child: Text(
                context.l10n.mostViewedEmpty,
                style: T.isiKecil.copyWith(color: C.lightInkMuted),
              ),
            )
          else
            for (final (i, d) in items.indexed)
              ListTile(
                contentPadding: EdgeInsets.zero,
                minLeadingWidth: 28,
                leading: Text(
                  '${i + 1}',
                  style: T.judulItem.copyWith(color: C.lightInkMuted),
                ),
                title: Text(
                  d.s('judul'),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: T.label,
                ),
                subtitle: Text(
                  context.l10n.viewedTimes(d.i('views')),
                  style: T.isiKecil,
                ),
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => DocumentDetailScreen(id: d.i('id')),
                  ),
                ),
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
      Text(title, style: T.subjudul),
      const SizedBox(height: 2),
      Text(subtitle, style: T.isiKecil.copyWith(color: C.lightInkMuted)),
    ],
  );
}
