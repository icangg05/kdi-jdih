import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';

enum _Mode { teks, ai }

class SearchScreen extends StatefulWidget {
  const SearchScreen(
      {super.key,
      this.initialAi = false,
      this.showBack = false,
      this.initialQuery = '',
      this.onExit});
  final bool initialAi;

  /// Kata kunci yang sudah diketik di layar sebelumnya (mis. kolom cari beranda).
  final String initialQuery;

  /// true bila dibuka via push (bukan sebagai tab).
  final bool showBack;

  /// Dipakai saat layar ini jadi tab tanpa navigasi bawah: tidak ada yang bisa
  /// di-pop, jadi jalan keluarnya harus disediakan shell lewat callback ini.
  final VoidCallback? onExit;

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  late _Mode _mode = widget.initialAi ? _Mode.ai : _Mode.teks;

  /// Pertanyaan yang dioper ke tab AI. Ganti nilainya = tab AI dibangun ulang
  /// dengan pertanyaan itu dan langsung menjawab.
  late String _aiQuery = widget.initialAi ? widget.initialQuery : '';

  void _tanyaAi(String q) => setState(() {
        _aiQuery = q;
        _mode = _Mode.ai;
      });

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: BrandAppBar(
          'Cari Pintar',
          showBack: widget.showBack,
          leading: widget.onExit == null
              ? null
              : IconButton(
                  icon: const Icon(Icons.arrow_back),
                  tooltip: 'Kembali',
                  onPressed: widget.onExit,
                ),
        ),
        body: Column(children: [
          _ModeSwitch(mode: _mode, onChanged: (m) => setState(() => _mode = m)),
          // kedua mode tetap hidup agar hasil tidak hilang saat berpindah
          Expanded(
            child: IndexedStack(
              index: _mode.index,
              children: [
                _DocSearchTab(
                    query: widget.initialQuery, onTanyaAi: _tanyaAi),
                _AiSearchTab(key: ValueKey(_aiQuery), query: _aiQuery),
              ],
            ),
          ),
        ]),
      );
}

/// Pemilih mode: dua pil dalam satu kapsul, penanda aktif bergeser mulus.
/// Menggantikan SegmentedButton bawaan yang terbaca seperti tombol sistem.
class _ModeSwitch extends StatelessWidget {
  const _ModeSwitch({required this.mode, required this.onChanged});
  final _Mode mode;
  final ValueChanged<_Mode> onChanged;

  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsets.fromLTRB(
            AppSpacing.lg, AppSpacing.md, AppSpacing.lg, AppSpacing.md),
        child: Container(
          height: 52,
          padding: const EdgeInsets.all(4),
          decoration: BoxDecoration(
            color: C.lightSubtle,
            borderRadius: BorderRadius.circular(999),
            border: Border.all(color: C.lightLine),
          ),
          child: LayoutBuilder(
            builder: (context, c) {
              final w = (c.maxWidth - 8) / 2;
              return Stack(children: [
                AnimatedAlign(
                  alignment: mode == _Mode.teks
                      ? Alignment.centerLeft
                      : Alignment.centerRight,
                  duration: const Duration(milliseconds: 220),
                  curve: Curves.easeOutCubic,
                  child: Container(
                    width: w,
                    height: 44,
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                          colors: [C.primary, C.gold],
                          begin: Alignment.centerLeft,
                          end: Alignment.centerRight),
                      borderRadius: BorderRadius.circular(999),
                    ),
                  ),
                ),
                Row(children: [
                  _pil(w, _Mode.teks, Icons.search, 'Pencarian Teks'),
                  _pil(w, _Mode.ai, Icons.auto_awesome, 'Tanya AI'),
                ]),
              ]);
            },
          ),
        ),
      );

  Widget _pil(double w, _Mode m, IconData icon, String label) {
    final on = m == mode;
    return SizedBox(
      width: w,
      height: 44,
      child: Semantics(
        selected: on,
        button: true,
        child: InkWell(
          borderRadius: BorderRadius.circular(999),
          onTap: () => onChanged(m),
          child: Row(mainAxisAlignment: MainAxisAlignment.center, children: [
            Icon(icon, size: 18, color: on ? C.ink : C.lightInkMuted),
            const SizedBox(width: 6),
            Flexible(
              child: Text(label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: on ? C.ink : C.lightInkMuted)),
            ),
          ]),
        ),
      ),
    );
  }
}

/// Kata kunci contoh: satu ketukan untuk mencoba pencarian, jauh lebih ramah
/// daripada halaman kosong yang menyuruh "ketik kata kunci".
const _contohKata = ['Retribusi', 'Pajak Daerah', 'Perwali 2024', 'APBD'];

class _DocSearchTab extends StatefulWidget {
  const _DocSearchTab({this.query = '', required this.onTanyaAi});
  final String query;

  /// Jalan keluar saat pencarian teks buntu: lempar kata kuncinya ke tab AI.
  final ValueChanged<String> onTanyaAi;

  @override
  State<_DocSearchTab> createState() => _DocSearchTabState();
}

class _DocSearchTabState extends State<_DocSearchTab> {
  late String _q = widget.query.trim();
  String _category = 'peraturan';
  late final _ctrl = TextEditingController(text: _q);
  int? _total;

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  void _cari(String v) => setState(() {
        _q = v.trim();
        _total = null;
      });

  @override
  Widget build(BuildContext context) => Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
            child: TextField(
              controller: _ctrl,
              decoration: InputDecoration(
                hintText: 'Cari judul, nomor, atau topik...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _q.isEmpty
                    ? null
                    : IconButton(
                        icon: const Icon(Icons.close),
                        tooltip: 'Hapus pencarian',
                        onPressed: () {
                          _ctrl.clear();
                          _cari('');
                        },
                      ),
              ),
              textInputAction: TextInputAction.search,
              onSubmitted: _cari,
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, AppSpacing.md, AppSpacing.lg, 0),
            child: FilterPills(
              labels: [for (final c in docCategories) c.short],
              selected: docCategories.indexWhere((c) => c.slug == _category),
              onSelected: (i) => setState(() {
                _category = docCategories[i].slug;
                _total = null;
              }),
            ),
          ),
          if (_q.isNotEmpty && _total != null)
            Padding(
              padding: const EdgeInsets.fromLTRB(
                  AppSpacing.lg, AppSpacing.sm, AppSpacing.lg, 0),
              child: Align(
                alignment: Alignment.centerLeft,
                child: Text('$_total hasil untuk "$_q"',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: C.lightInkMuted)),
              ),
            ),
          Expanded(
            child: _q.isEmpty
                ? _mulai()
                : PagedListView(
                    key: ValueKey('$_category|$_q'),
                    fetch: (page) =>
                        api.documents(category: _category, q: _q, page: page),
                    onTotal: (t) => setState(() => _total = t),
                    emptyView: NoResults(
                      _q,
                      saran: 'Coba kata kunci yang lebih pendek, ganti '
                          'kategori di atas, atau tanyakan langsung ke AI.',
                      actions: [
                        OutlinedButton.icon(
                          onPressed: () {
                            _ctrl.clear();
                            _cari('');
                          },
                          icon: const Icon(Icons.close, size: 18),
                          label: const Text('Hapus pencarian'),
                        ),
                        FilledButton.icon(
                          onPressed: () => widget.onTanyaAi(_q),
                          icon: const Icon(Icons.auto_awesome, size: 18),
                          label: const Text('Tanya AI'),
                        ),
                      ],
                    ),
                    itemBuilder: (_, d, __) => DocumentCard(d),
                  ),
          ),
        ],
      );

  /// Layar awal: penjelasan singkat + kata kunci contoh yang bisa diketuk.
  Widget _mulai() => ListView(
        padding: const EdgeInsets.fromLTRB(
            AppSpacing.xl, AppSpacing.xxl, AppSpacing.xl, AppSpacing.lg),
        children: [
          Center(
            child: Container(
              width: 72,
              height: 72,
              decoration: BoxDecoration(
                color: C.primary.withValues(alpha: .12),
                borderRadius: BorderRadius.circular(24),
              ),
              child: const Icon(Icons.search, color: C.primaryInk, size: 32),
            ),
          ),
          const SizedBox(height: AppSpacing.lg),
          const Text('Cari Produk Hukum',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          const SizedBox(height: AppSpacing.sm),
          const Text(
              'Ketik judul, nomor, atau topik peraturan lalu tekan tombol cari '
              'pada papan ketik. Kategori di atas mempersempit hasilnya.',
              textAlign: TextAlign.center,
              style:
                  TextStyle(fontSize: 13, height: 1.6, color: C.lightInkMuted)),
          const SizedBox(height: AppSpacing.xl),
          Wrap(
            alignment: WrapAlignment.center,
            spacing: AppSpacing.sm,
            runSpacing: AppSpacing.sm,
            children: [
              for (final k in _contohKata)
                ActionChip(
                  label: Text(k, style: const TextStyle(fontSize: 12.5)),
                  avatar: const Icon(Icons.north_east, size: 14),
                  onPressed: () {
                    _ctrl.text = k;
                    _cari(k);
                  },
                  shape:
                      const StadiumBorder(side: BorderSide(color: C.lightLine)),
                  backgroundColor: C.lightSurface,
                ),
            ],
          ),
        ],
      );
}

const _contohPertanyaan = [
  'Apa aturan retribusi sampah?',
  'Perwali terbaru tentang apa?',
  'Bagaimana cara mengurus IMB?',
];

class _AiSearchTab extends StatefulWidget {
  const _AiSearchTab({super.key, this.query = ''});
  final String query;

  @override
  State<_AiSearchTab> createState() => _AiSearchTabState();
}

class _AiSearchTabState extends State<_AiSearchTab> {
  late final _ctrl = TextEditingController(text: widget.query.trim());
  Future<Json>? _result;

  @override
  void initState() {
    super.initState();
    // pertanyaan bawaan dari beranda: langsung dijawab tanpa menekan kirim lagi
    if (_ctrl.text.length >= 3) _result = api.aiSearch(_ctrl.text);
  }

  void _ask([String? preset]) {
    if (preset != null) _ctrl.text = preset;
    final q = _ctrl.text.trim();
    if (q.length < 3) {
      ScaffoldMessenger.of(context)
          .showSnackBar(const SnackBar(content: Text('Minimal 3 karakter.')));
      return;
    }
    FocusScope.of(context).unfocus();
    // pakai blok, bukan arrow: callback setState tidak boleh mengembalikan Future
    setState(() {
      _result = api.aiSearch(q);
    });
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => Column(
        children: [
          Expanded(child: _result == null ? _intro() : _answer()),
          _composer(),
        ],
      );

  /// Empty state yang mengajarkan cara pakai, bukan sekadar "belum ada data".
  Widget _intro() => ListView(
        padding: const EdgeInsets.fromLTRB(
            AppSpacing.xl, AppSpacing.xxl, AppSpacing.xl, AppSpacing.lg),
        children: [
          Center(
            child: Container(
              width: 72,
              height: 72,
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                    colors: [C.ink, C.inkSoft],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight),
                borderRadius: BorderRadius.circular(24),
              ),
              child: const Icon(Icons.auto_awesome, color: C.gold, size: 32),
            ),
          ),
          const SizedBox(height: AppSpacing.lg),
          const Text('Tanya Asisten JDIH',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 20, fontWeight: FontWeight.w800)),
          const SizedBox(height: AppSpacing.sm),
          const Text(
              'Tulis pertanyaan dengan bahasa sehari-hari. Jawaban disusun '
              'dari dokumen hukum Kota Kendari, lengkap dengan peraturan '
              'rujukannya.',
              textAlign: TextAlign.center,
              style:
                  TextStyle(fontSize: 13, height: 1.6, color: C.lightInkMuted)),
          const SizedBox(height: AppSpacing.xl),
          Wrap(
            alignment: WrapAlignment.center,
            spacing: AppSpacing.xs,
            children: [
              for (final q in _contohPertanyaan)
                ActionChip(
                  label: Text(q, style: const TextStyle(fontSize: 12.5)),
                  onPressed: () => _ask(q),
                  visualDensity: VisualDensity.compact,
                  labelPadding:
                      const EdgeInsets.symmetric(horizontal: AppSpacing.xs),
                  shape:
                      const StadiumBorder(side: BorderSide(color: C.lightLine)),
                  backgroundColor: C.lightSurface,
                ),
            ],
          ),
        ],
      );

  Widget _answer() => FutureBuilder<Json>(
        future: _result,
        builder: (context, snap) {
          if (snap.hasError) return ErrorRetry('${snap.error}', onRetry: _ask);
          if (!snap.hasData) {
            return ListView(
              padding: const EdgeInsets.all(AppSpacing.lg),
              children: [
                Pulse(
                  child: Container(
                    height: 150,
                    decoration: BoxDecoration(
                      color: C.goldSoft.withValues(alpha: .45),
                      borderRadius: BorderRadius.circular(AppRadius.card),
                    ),
                    child: const Center(
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.auto_awesome, color: C.primaryInk),
                          SizedBox(height: AppSpacing.sm),
                          Text('Menelusuri dokumen...',
                              style: TextStyle(fontWeight: FontWeight.w600)),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: AppSpacing.md),
                const SkeletonList(count: 3, height: 96),
              ],
            );
          }
          final r = snap.data!;
          final docs = r.l('documents');
          return ListView(
            padding: const EdgeInsets.all(AppSpacing.lg),
            children: [
              Rise(
                child: Container(
                  decoration: BoxDecoration(
                    color: C.goldSoft.withValues(alpha: .45),
                    borderRadius: BorderRadius.circular(AppRadius.card),
                    border: Border.all(color: C.gold.withValues(alpha: .55)),
                  ),
                  padding: const EdgeInsets.all(AppSpacing.lg),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Row(children: [
                        Icon(Icons.auto_awesome, size: 18, color: C.primaryInk),
                        SizedBox(width: 6),
                        Text('Jawaban AI',
                            style: TextStyle(
                                fontWeight: FontWeight.w800, fontSize: 15)),
                      ]),
                      const SizedBox(height: AppSpacing.sm),
                      Text(r.s('explanation'),
                          style: const TextStyle(height: 1.6)),
                    ],
                  ),
                ),
              ),
              if (docs.isEmpty)
                const Padding(
                  padding: EdgeInsets.only(top: AppSpacing.xl),
                  child: EmptyState(
                    icon: Icons.folder_open_outlined,
                    title: 'Tidak ada dokumen yang cocok',
                    message:
                        'Coba pertanyaan yang lebih spesifik, atau gunakan '
                        'Pencarian Teks untuk menelusuri per kategori.',
                  ),
                )
              else ...[
                SectionHeader('Dokumen Rujukan (${r.i('total')})'),
                for (final (i, d) in docs.indexed) ...[
                  Rise(delayMs: i * 45, child: _AiDocTile(d)),
                  const SizedBox(height: AppSpacing.md),
                ],
              ],
            ],
          );
        },
      );

  /// Komposer menetap di bawah — idiom percakapan, mudah dijangkau ibu jari.
  Widget _composer() => Material(
        color: C.lightSurface,
        elevation: 8,
        shadowColor: C.ink.withValues(alpha: .12),
        child: SafeArea(
          top: false,
          child: Padding(
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, AppSpacing.sm, AppSpacing.lg, AppSpacing.sm),
            child: Row(children: [
              if (_result != null)
                IconButton(
                  tooltip: 'Mulai baru',
                  icon: const Icon(Icons.refresh),
                  onPressed: () => setState(() {
                    _result = null;
                    _ctrl.clear();
                  }),
                ),
              Expanded(
                child: TextField(
                  controller: _ctrl,
                  minLines: 1,
                  maxLines: 3,
                  textInputAction: TextInputAction.send,
                  decoration: const InputDecoration(
                    hintText: 'Tulis pertanyaan Anda...',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.all(Radius.circular(24)),
                      borderSide: BorderSide(color: C.lightLine),
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.all(Radius.circular(24)),
                      borderSide: BorderSide(color: C.lightLine),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.all(Radius.circular(24)),
                      borderSide: BorderSide(color: C.primary, width: 1.5),
                    ),
                  ),
                  onSubmitted: (_) => _ask(),
                ),
              ),
              const SizedBox(width: AppSpacing.sm),
              SizedBox(
                width: 48,
                height: 48,
                child: IconButton.filled(
                  tooltip: 'Kirim',
                  icon: const Icon(Icons.arrow_upward, size: 20),
                  onPressed: _ask,
                ),
              ),
            ]),
          ),
        ),
      );
}

/// Item hasil AI (bentuk JSON-nya beda dari DocumentListItem).
class _AiDocTile extends StatelessWidget {
  const _AiDocTile(this.d);
  final Json d;

  @override
  Widget build(BuildContext context) {
    final acc = d.i('accuracy');
    return Pressable(
      child: Card(
        child: InkWell(
          borderRadius: BorderRadius.circular(AppRadius.card),
          onTap: () => Navigator.push(
              context,
              MaterialPageRoute(
                  builder: (_) => DocumentDetailScreen(id: d.i('id')))),
          child: Padding(
            padding: const EdgeInsets.all(AppSpacing.lg),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(children: [
                  JenisChip(d.sn('type')),
                  const Spacer(),
                  StatusBadge(d.sn('status')),
                ]),
                const SizedBox(height: AppSpacing.sm),
                Text(d.s('title'),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 15,
                        height: 1.35,
                        fontWeight: FontWeight.w600)),
                if (d.sn('description') != null) ...[
                  const SizedBox(height: AppSpacing.xs),
                  Text(d.s('description'),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                          fontSize: 13, color: C.lightInkMuted)),
                ],
                const SizedBox(height: AppSpacing.md),
                Row(children: [
                  Expanded(
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(4),
                      child: LinearProgressIndicator(
                          value: acc / 100,
                          minHeight: 6,
                          backgroundColor: C.primary.withValues(alpha: .12)),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Text('$acc% cocok',
                      style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: C.primaryInk)),
                ]),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
