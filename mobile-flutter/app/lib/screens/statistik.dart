import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
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
}

List<Slice> _slices(List<Json> rows, String labelKey) =>
    [for (final r in rows) (label: r.s(labelKey), value: r.i('total'))]
        .where((s) => s.label.isNotEmpty && s.value > 0)
        .toList();

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
    (
      StatusKind.diubah,
      l.statusAmended,
      Icons.edit_outlined,
      ChartColors.diubah,
    ),
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
    ].where((e) => e.value > 0).toList();
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

// ============================================================
//  PANEL KOLEKSI — ringkasan interaktif di beranda
// ============================================================

enum _Lensa { tahun, jenis, status }

/// Statistik koleksi di beranda: satu angka utama, angka pendukung sebaris
/// kalimat (bukan deret kotak KPI), lalu satu grafik yang dijelajahi lewat
/// tiga lensa: kapan terbit, jenis apa, masih berlaku atau tidak. Tahun
/// diketuk atau digeser untuk dibaca satu per satu; jenis diketuk untuk
/// membuka dokumennya.
class PanelKoleksi extends StatefulWidget {
  const PanelKoleksi(this.d, {super.key});

  /// Respons `GET /api/jdih/statistics`.
  final Json d;

  /// Jenis teratas yang tampil sendiri; ekornya dilipat jadi "Lainnya".
  static const jenisTampil = 3;

  /// Bentuk data untuk kerangka muat.
  static const contoh = {
    'total_dokumen': 1234,
    'total_views': 12345,
    'total_downloads': 123,
    'dokumen_per_tahun': [
      {'tahun': '2021', 'total': 4},
      {'tahun': '2022', 'total': 6},
      {'tahun': '2023', 'total': 5},
      {'tahun': '2024', 'total': 8},
      {'tahun': '2025', 'total': 7},
    ],
    'dokumen_per_tipe': [],
    'dokumen_per_status': [],
  };

  @override
  State<PanelKoleksi> createState() => _PanelKoleksiState();
}

class _PanelKoleksiState extends State<PanelKoleksi> {
  var _lensa = _Lensa.tahun;

  /// Indeks tahun terpilih; null = tahun terbaru.
  int? _tahun;

  Duration _durasi(int ms) => MediaQuery.of(context).disableAnimations
      ? Duration.zero
      : Duration(milliseconds: ms);

  @override
  Widget build(BuildContext context) {
    final l = context.l10n;
    final d = widget.d;
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.lg),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            // angka proporsional: angka tabular membuat "1.032" renggang di
            // ukuran display
            Text(
              _n.format(d.i('total_dokumen')),
              style: T.display.copyWith(fontFeatures: const []),
            ),
            Text(
              l.statsDocsLabel,
              style: T.isi.copyWith(color: C.lightInkMuted),
            ),
            const SizedBox(height: AppSpacing.xs),
            Text(
              l.statsReach(
                _n.format(d.i('total_views')),
                _n.format(d.i('total_downloads')),
              ),
              style: T.isiKecil.copyWith(color: C.lightInkMuted),
            ),
            const SizedBox(height: AppSpacing.lg),
            _PilihLensa(
              lensa: _lensa,
              onPilih: (v) {
                if (v == _lensa) return;
                HapticFeedback.selectionClick();
                setState(() => _lensa = v);
              },
            ),
            const SizedBox(height: AppSpacing.lg),
            // tinggi tiap lensa berbeda: kartu memanjang/memendek mulus,
            // isinya bersilang pudar
            AnimatedSize(
              duration: _durasi(220),
              curve: Curves.easeOutCubic,
              alignment: Alignment.topCenter,
              child: AnimatedSwitcher(
                duration: _durasi(180),
                layoutBuilder: (cur, prev) => Stack(
                  alignment: Alignment.topCenter,
                  children: [...prev, ?cur],
                ),
                child: KeyedSubtree(
                  key: ValueKey(_lensa),
                  child: switch (_lensa) {
                    _Lensa.tahun => _lensaTahun(),
                    _Lensa.jenis => _lensaJenis(),
                    _Lensa.status => _lensaStatus(),
                  },
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _lensaTahun() {
    final l = context.l10n;
    final rows = _slices(widget.d.l('dokumen_per_tahun'), 'tahun')
      ..sort(
        (a, b) =>
            (int.tryParse(a.label) ?? 0).compareTo(int.tryParse(b.label) ?? 0),
      );
    if (rows.isEmpty) return const SizedBox.shrink();
    final i = (_tahun ?? rows.length - 1).clamp(0, rows.length - 1);
    // tahun berjalan belum selesai: angkanya kecil bukan karena menurun
    final ini = '${DateTime.now().year}';
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        // semua pembacaan dibangun, satu ditampilkan: tingginya ikut yang
        // terpanjang, jadi grafik tidak naik-turun saat tahun digeser
        IndexedStack(
          index: i,
          children: [
            for (final e in rows)
              _Bacaan(
                angka: _n.format(e.value),
                teks: e.label == ini
                    ? l.statsYearSoFar(e.value, e.label)
                    : l.statsYearDocs(e.value, e.label),
              ),
          ],
        ),
        const SizedBox(height: AppSpacing.md),
        _KolomTahun(
          rows: rows,
          dipilih: i,
          onPilih: (j) {
            if (j == i) return;
            HapticFeedback.selectionClick();
            setState(() => _tahun = j);
          },
        ),
        const SizedBox(height: AppSpacing.sm),
        Text(
          l.statsYearHint,
          style: T.labelKecil.copyWith(
            color: C.lightInkMuted,
            fontWeight: FontWeight.w400,
          ),
        ),
      ],
    );
  }

  Widget _lensaJenis() {
    final l = context.l10n;
    final urut = _slices(widget.d.l('dokumen_per_tipe'), 'tipe')
      ..sort((a, b) => b.value.compareTo(a.value));
    if (urut.isEmpty) return const SizedBox.shrink();
    final sisa = urut.skip(PanelKoleksi.jenisTampil).toList();
    final rows = <({String label, int value, String? jenis})>[
      for (final e in urut.take(PanelKoleksi.jenisTampil))
        (label: titleCase(e.label), value: e.value, jenis: e.label),
      if (sisa.isNotEmpty)
        (
          label: l.otherTypes(sisa.length),
          value: sisa.fold(0, (a, e) => a + e.value),
          jenis: null,
        ),
    ];
    final maks = rows.map((e) => e.value).reduce(math.max);
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        for (final r in rows)
          _BarisJenis(
            label: r.label,
            value: r.value,
            max: maks,
            // jenis teratas seluruhnya peraturan; "Lainnya" campuran
            // kategori, jadi tidak punya satu daftar tujuan
            onTap: r.jenis == null
                ? null
                : () => Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => DocumentListScreen(
                        category: 'peraturan',
                        jenis: r.jenis!,
                      ),
                    ),
                  ),
          ),
      ],
    );
  }

  Widget _lensaStatus() {
    final l = context.l10n;
    final status = statusBuckets(widget.d.l('dokumen_per_status'));
    final data = [
      for (final r in StatusChart._rows(l)) (row: r, value: status[r.$1] ?? 0),
    ].where((e) => e.value > 0).toList();
    final total = data.fold(0, (a, e) => a + e.value);
    if (total == 0) {
      return Text(
        l.statusNoData,
        style: T.isiKecil.copyWith(color: C.lightInkMuted),
      );
    }
    final berlaku = status[StatusKind.berlaku] ?? 0;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _Bacaan(
          angka: '${(berlaku / total * 100).round()}%',
          teks: l.statsInForceShare,
        ),
        const SizedBox(height: AppSpacing.md),
        // bagian terhadap keseluruhan: segmen dipisah celah 2dp warna
        // permukaan, terbuka dari kiri sekali saat lensa dibuka
        Align(
          alignment: Alignment.centerLeft,
          child: TweenAnimationBuilder<double>(
            tween: Tween(begin: _durasi(1) == Duration.zero ? 1 : 0, end: 1),
            duration: _durasi(420),
            curve: Curves.easeOutCubic,
            builder: (_, t, strip) => ClipRect(
              child: Align(
                alignment: Alignment.centerLeft,
                widthFactor: t,
                child: strip,
              ),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(AppRadius.chip),
              child: SizedBox(
                height: 12,
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    for (final (i, e) in data.indexed) ...[
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
          ),
        ),
        const SizedBox(height: AppSpacing.md),
        for (final (i, e) in data.indexed) ...[
          if (i > 0) const SizedBox(height: AppSpacing.sm),
          Semantics(
            label: l.labelDocs(e.row.$2, e.value),
            excludeSemantics: true,
            // ikon berwarna membawa identitas; teks tetap bertinta teks
            child: Row(
              children: [
                Icon(e.row.$3, size: 18, color: e.row.$4),
                const SizedBox(width: AppSpacing.sm),
                Expanded(child: Text(e.row.$2, style: T.isiKecil)),
                Text(
                  _n.format(e.value),
                  style: T.label.copyWith(
                    fontWeight: FontWeight.w700,
                    fontFeatures: [FontFeature.tabularFigures()],
                  ),
                ),
              ],
            ),
          ),
        ],
      ],
    );
  }
}

/// Tiga lensa sebagai tab teks bergaris bawah amber, padanan penanda aktif
/// navigasi bawah: amber berarti "di sini".
class _PilihLensa extends StatelessWidget {
  const _PilihLensa({required this.lensa, required this.onPilih});
  final _Lensa lensa;
  final ValueChanged<_Lensa> onPilih;

  @override
  Widget build(BuildContext context) {
    final l = context.l10n;
    final label = [l.statsLensYear, l.statsLensType, l.statsLensStatus];
    return DecoratedBox(
      decoration: const BoxDecoration(
        border: Border(bottom: BorderSide(color: C.lightLine)),
      ),
      child: Stack(
        children: [
          Row(
            children: [
              for (final v in _Lensa.values)
                Expanded(child: _tab(v, label[v.index])),
            ],
          ),
          Positioned.fill(
            child: IgnorePointer(
              child: AnimatedAlign(
                alignment: Alignment(lensa.index - 1.0, 1),
                duration: MediaQuery.of(context).disableAnimations
                    ? Duration.zero
                    : const Duration(milliseconds: 220),
                curve: Curves.easeOutCubic,
                child: FractionallySizedBox(
                  widthFactor: 1 / _Lensa.values.length,
                  child: Container(height: 2, color: C.primary),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _tab(_Lensa v, String teks) {
    final on = v == lensa;
    return Semantics(
      selected: on,
      button: true,
      inMutuallyExclusiveGroup: true,
      child: InkWell(
        onTap: () => onPilih(v),
        child: SizedBox(
          height: 48,
          child: Center(
            child: Text(
              teks,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: T.label.copyWith(
                fontWeight: on ? FontWeight.w700 : FontWeight.w500,
                color: on ? C.ink : C.lightInkMuted,
              ),
            ),
          ),
        ),
      ),
    );
  }
}

/// Pembacaan lensa: nilai di depan dan tebal, keterangannya redup di
/// belakang. Angka tabular: saat tahun digeser, keterangan tidak bergoyang.
class _Bacaan extends StatelessWidget {
  const _Bacaan({required this.angka, required this.teks});
  final String angka, teks;

  @override
  Widget build(BuildContext context) => Text.rich(
    TextSpan(
      children: [
        TextSpan(text: angka, style: T.angka),
        const TextSpan(text: '  '),
        TextSpan(
          text: teks,
          style: T.isiKecil.copyWith(color: C.lightInkMuted),
        ),
      ],
    ),
  );
}

/// Kolom per tahun. Penekanan, bukan warna per kategori: tahun terpilih biru
/// penuh, sisanya tint biru. Seluruh bidang grafik jadi sasaran sentuh:
/// ketuk atau geser, kolom terdekat terpilih. Pembaca layar mengaturnya
/// seperti slider (usap atas/bawah).
class _KolomTahun extends StatelessWidget {
  const _KolomTahun({
    required this.rows,
    required this.dipilih,
    required this.onPilih,
  });
  final List<Slice> rows;
  final int dipilih;
  final ValueChanged<int> onPilih;

  static const _tinggi = 112.0;

  @override
  Widget build(BuildContext context) {
    final l = context.l10n;
    final maks = rows.map((e) => e.value).reduce(math.max);
    final diam = MediaQuery.of(context).disableAnimations;
    final redup = Color.alphaBlend(
      ChartColors.bar.withValues(alpha: .22),
      C.lightSurface,
    );
    String baca(int i) => l.labelDocs(rows[i].label, rows[i].value);
    final sumbu = T.labelKecil.copyWith(
      color: C.lightInkMuted,
      fontWeight: FontWeight.w400,
      fontFeatures: [FontFeature.tabularFigures()],
    );
    return Semantics(
      label: l.statsLensYear,
      value: baca(dipilih),
      increasedValue: dipilih + 1 < rows.length ? baca(dipilih + 1) : null,
      decreasedValue: dipilih > 0 ? baca(dipilih - 1) : null,
      onIncrease: dipilih + 1 < rows.length ? () => onPilih(dipilih + 1) : null,
      onDecrease: dipilih > 0 ? () => onPilih(dipilih - 1) : null,
      child: ExcludeSemantics(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            LayoutBuilder(
              builder: (context, box) {
                final slot = box.maxWidth / rows.length;
                int di(Offset p) =>
                    (p.dx / slot).floor().clamp(0, rows.length - 1);
                // onTapUp, bukan onTapDown: jari yang mulai menggulir
                // halaman tidak ikut memilih tahun
                return GestureDetector(
                  behavior: HitTestBehavior.opaque,
                  onTapUp: (d) => onPilih(di(d.localPosition)),
                  onHorizontalDragStart: (d) => onPilih(di(d.localPosition)),
                  onHorizontalDragUpdate: (d) => onPilih(di(d.localPosition)),
                  child: SizedBox(
                    height: _tinggi,
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        for (final (i, e) in rows.indexed)
                          SizedBox(
                            width: slot,
                            child: Align(
                              alignment: Alignment.bottomCenter,
                              child: SizedBox(
                                // kolom tipis, sisanya udara
                                width: math.min(18, slot * .6),
                                child: TweenAnimationBuilder<double>(
                                  tween: Tween(
                                    begin: diam ? e.value / maks : 0,
                                    end: e.value / maks,
                                  ),
                                  duration: Duration(
                                    milliseconds: diam ? 0 : 420 + i * 24,
                                  ),
                                  curve: Curves.easeOutCubic,
                                  builder: (_, t, _) => FractionallySizedBox(
                                    heightFactor: t.clamp(.03, 1.0),
                                    alignment: Alignment.bottomCenter,
                                    child: AnimatedContainer(
                                      duration: Duration(
                                        milliseconds: diam ? 0 : 140,
                                      ),
                                      decoration: BoxDecoration(
                                        color: i == dipilih
                                            ? ChartColors.bar
                                            : redup,
                                        // ujung data membulat, pangkal siku
                                        borderRadius:
                                            const BorderRadius.vertical(
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
                      ],
                    ),
                  ),
                );
              },
            ),
            const Divider(height: 1, color: C.lightLine),
            const SizedBox(height: 6),
            Row(
              children: [
                Text(rows.first.label, style: sumbu),
                const Spacer(),
                if (rows.length > 1) Text(rows.last.label, style: sumbu),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

/// Satu jenis: nama dan jumlah di atas, batang tanpa lintasan di bawahnya
/// (panjangnya sudah membawa besaran). Panah menandai baris yang membuka
/// daftar dokumennya.
class _BarisJenis extends StatelessWidget {
  const _BarisJenis({
    required this.label,
    required this.value,
    required this.max,
    this.onTap,
  });
  final String label;
  final int value, max;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final diam = MediaQuery.of(context).disableAnimations;
    final ratio = max <= 0 ? 0.0 : value / max;
    final isi = Padding(
      padding: const EdgeInsets.symmetric(vertical: AppSpacing.sm),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
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
              Text(
                _n.format(value),
                style: T.label.copyWith(
                  fontWeight: FontWeight.w700,
                  fontFeatures: [FontFeature.tabularFigures()],
                ),
              ),
              SizedBox(
                width: 24,
                child: onTap == null
                    ? null
                    : const Icon(
                        Icons.chevron_right,
                        size: 20,
                        color: C.lightInkMuted,
                      ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          Align(
            alignment: Alignment.centerLeft,
            child: TweenAnimationBuilder<double>(
              tween: Tween(begin: diam ? ratio : 0, end: ratio),
              duration: Duration(milliseconds: diam ? 0 : 420),
              curve: Curves.easeOutCubic,
              builder: (_, t, _) => FractionallySizedBox(
                widthFactor: t.clamp(.01, 1.0),
                child: Container(
                  height: 8,
                  decoration: const BoxDecoration(
                    color: ChartColors.bar,
                    borderRadius: BorderRadius.horizontal(
                      right: Radius.circular(AppRadius.chip),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
    return Semantics(
      button: onTap != null,
      label: context.l10n.labelDocs(label, value),
      excludeSemantics: true,
      child: onTap == null
          ? isi
          : InkWell(
              onTap: onTap,
              borderRadius: BorderRadius.circular(AppRadius.card),
              child: isi,
            ),
    );
  }
}
