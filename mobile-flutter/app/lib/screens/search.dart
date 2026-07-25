import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';

enum _Mode { teks, ai }

class SearchScreen extends StatefulWidget {
  const SearchScreen({super.key, this.initialAi = false, this.showBack = false});
  final bool initialAi;

  /// true bila dibuka via push (bukan sebagai tab).
  final bool showBack;

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  late _Mode _mode = widget.initialAi ? _Mode.ai : _Mode.teks;

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: BrandAppBar('Cari Pintar', showBack: widget.showBack),
        body: Column(children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, AppSpacing.md, AppSpacing.lg, AppSpacing.md),
            child: SegmentedButton<_Mode>(
              segments: const [
                ButtonSegment(
                    value: _Mode.teks,
                    icon: Icon(Icons.search, size: 18),
                    label: Text('Pencarian Teks')),
                ButtonSegment(
                    value: _Mode.ai,
                    icon: Icon(Icons.auto_awesome, size: 18),
                    label: Text('Tanya AI')),
              ],
              selected: {_mode},
              showSelectedIcon: false,
              onSelectionChanged: (s) => setState(() => _mode = s.first),
              style: SegmentedButton.styleFrom(
                backgroundColor: C.lightSurface,
                selectedBackgroundColor: C.primary,
                selectedForegroundColor: C.ink,
                foregroundColor: C.lightInkMuted,
                side: const BorderSide(color: C.lightLine),
                shape: const StadiumBorder(),
                // Material 3 default 40dp; target sentuh minimal 48dp
                minimumSize: const Size(0, 48),
              ),
            ),
          ),
          // kedua mode tetap hidup agar hasil tidak hilang saat berpindah
          Expanded(
            child: IndexedStack(
              index: _mode.index,
              children: const [_DocSearchTab(), _AiSearchTab()],
            ),
          ),
        ]),
      );
}

class _DocSearchTab extends StatefulWidget {
  const _DocSearchTab();

  @override
  State<_DocSearchTab> createState() => _DocSearchTabState();
}

class _DocSearchTabState extends State<_DocSearchTab> {
  String _q = '', _category = 'peraturan';

  @override
  Widget build(BuildContext context) => Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
            child: TextField(
              decoration: const InputDecoration(
                  hintText: 'Cari judul dokumen...',
                  prefixIcon: Icon(Icons.search)),
              textInputAction: TextInputAction.search,
              onSubmitted: (v) => setState(() => _q = v.trim()),
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(
                AppSpacing.lg, AppSpacing.md, AppSpacing.lg, 0),
            child: FilterPills(
              labels: [for (final c in docCategories) c.short],
              selected: docCategories.indexWhere((c) => c.slug == _category),
              onSelected: (i) =>
                  setState(() => _category = docCategories[i].slug),
            ),
          ),
          Expanded(
            child: _q.isEmpty
                ? const EmptyState(
                    icon: Icons.search,
                    title: 'Cari produk hukum',
                    message:
                        'Ketik kata kunci — nomor, judul, atau topik — lalu tekan '
                        'tombol cari pada papan ketik. Pilih kategori di atas '
                        'untuk mempersempit hasil.',
                  )
                : PagedListView(
                    key: ValueKey('$_category|$_q'),
                    fetch: (page) =>
                        api.documents(category: _category, q: _q, page: page),
                    empty: 'Tidak ada hasil untuk "$_q".',
                    itemBuilder: (_, d, __) => DocumentCard(d),
                  ),
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
  const _AiSearchTab();

  @override
  State<_AiSearchTab> createState() => _AiSearchTabState();
}

class _AiSearchTabState extends State<_AiSearchTab> {
  final _ctrl = TextEditingController();
  Future<Json>? _result;

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
            spacing: AppSpacing.sm,
            runSpacing: AppSpacing.sm,
            children: [
              for (final q in _contohPertanyaan)
                ActionChip(
                  label: Text(q),
                  avatar: const Icon(Icons.north_east, size: 14),
                  onPressed: () => _ask(q),
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
                    hintText: 'Tanya apa pun tentang hukum Kota Kendari...',
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
                      style:
                          const TextStyle(fontSize: 13, color: C.lightInkMuted)),
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
