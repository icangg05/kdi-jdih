import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';

class SearchScreen extends StatelessWidget {
  const SearchScreen({super.key, this.initialAi = false, this.showBack = false});
  final bool initialAi;

  /// true bila dibuka via push (bukan sebagai tab).
  final bool showBack;

  @override
  Widget build(BuildContext context) => DefaultTabController(
        length: 2,
        initialIndex: initialAi ? 1 : 0,
        child: Scaffold(
          appBar: BrandAppBar(
            'Pencarian',
            showBack: showBack,
            tabs: const TabBar(
              labelColor: C.primary,
              indicatorColor: C.primary,
              tabs: [Tab(text: 'Dokumen'), Tab(text: 'Tanya AI')],
            ),
          ),
          body: const TabBarView(children: [_DocSearchTab(), _AiSearchTab()]),
        ),
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
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: TextField(
              autofocus: false,
              decoration: const InputDecoration(
                  hintText: 'Cari judul dokumen...',
                  prefixIcon: Icon(Icons.search)),
              textInputAction: TextInputAction.search,
              onSubmitted: (v) => setState(() => _q = v),
            ),
          ),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Row(children: [
              for (final c in docCategories)
                Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: ChoiceChip(
                    label: Text(c.label),
                    selected: _category == c.slug,
                    onSelected: (_) => setState(() => _category = c.slug),
                  ),
                ),
            ]),
          ),
          Expanded(
            child: _q.isEmpty
                ? const Center(
                    child: Text('Ketik kata kunci lalu tekan cari.',
                        style: TextStyle(color: Colors.grey)))
                : PagedListView(
                    key: ValueKey('$_category|$_q'),
                    fetch: (page) =>
                        api.documents(category: _category, q: _q, page: page),
                    empty: 'Tidak ada hasil untuk "$_q".',
                    itemBuilder: (_, d) => DocumentCard(d),
                  ),
          ),
        ],
      );
}

class _AiSearchTab extends StatefulWidget {
  const _AiSearchTab();

  @override
  State<_AiSearchTab> createState() => _AiSearchTabState();
}

class _AiSearchTabState extends State<_AiSearchTab> {
  final _ctrl = TextEditingController();
  Future<Json>? _result;

  void _ask() {
    final q = _ctrl.text.trim();
    if (q.length < 3) {
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Minimal 3 karakter.')));
      return;
    }
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
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
            child: TextField(
              controller: _ctrl,
              decoration: InputDecoration(
                hintText: 'Tanya apa saja, mis. "aturan retribusi sampah"',
                prefixIcon: const Icon(Icons.auto_awesome, color: C.accent),
                suffixIcon: IconButton(
                    icon: const Icon(Icons.send, color: C.primary),
                    onPressed: _ask),
              ),
              textInputAction: TextInputAction.search,
              onSubmitted: (_) => _ask(),
            ),
          ),
          Expanded(
            child: _result == null
                ? const Center(
                    child: Text('Jawaban AI berdasarkan dokumen JDIH Kendari.',
                        style: TextStyle(color: Colors.grey)))
                : FutureBuilder<Json>(
                    future: _result,
                    builder: (context, snap) {
                      if (snap.hasError) {
                        return ErrorRetry('${snap.error}', onRetry: _ask);
                      }
                      if (!snap.hasData) {
                        return const Center(
                            child: Column(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                              CircularProgressIndicator(),
                              SizedBox(height: 12),
                              Text('Menganalisis...'),
                            ]));
                      }
                      final r = snap.data!;
                      final docs = r.l('documents');
                      return ListView(
                        padding: const EdgeInsets.all(16),
                        children: [
                          Rise(
                            child: Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(16),
                                gradient: LinearGradient(colors: [
                                  C.accent.withValues(alpha: .1),
                                  C.primary.withValues(alpha: .07),
                                ]),
                                border: Border.all(
                                    color: C.accent.withValues(alpha: .3)),
                              ),
                              padding: const EdgeInsets.all(16),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Row(children: [
                                    IconSquircle(Icons.auto_awesome,
                                        color: C.primary, size: 34),
                                    SizedBox(width: 10),
                                    Text('Jawaban AI',
                                        style: TextStyle(
                                            fontWeight: FontWeight.w800,
                                            fontSize: 15,
                                            color: C.accent)),
                                  ]),
                                  const SizedBox(height: 10),
                                  Text(r.s('explanation'),
                                      style: const TextStyle(height: 1.55)),
                                ],
                              ),
                            ),
                          ),
                          if (docs.isNotEmpty) ...[
                            SectionHeader('Hasil (${r.i('total')})'),
                            for (final (i, d) in docs.indexed) ...[
                              Rise(delayMs: i * 50, child: _AiDocTile(d)),
                              const SizedBox(height: 12),
                            ],
                          ],
                        ],
                      );
                    },
                  ),
          ),
        ],
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
        borderRadius: BorderRadius.circular(16),
        onTap: () => Navigator.push(
            context,
            MaterialPageRoute(
                builder: (_) => DocumentDetailScreen(id: d.i('id')))),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(children: [
                JenisChip(d.sn('type')),
                const Spacer(),
                StatusBadge(d.sn('status')),
              ]),
              const SizedBox(height: 8),
              Text(d.s('title'),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontWeight: FontWeight.w600)),
              if (d.sn('description') != null) ...[
                const SizedBox(height: 4),
                Text(d.s('description'),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 13, color: Colors.grey)),
              ],
              const SizedBox(height: 8),
              Row(children: [
                Expanded(
                    child: ClipRRect(
                        borderRadius: BorderRadius.circular(4),
                        child: LinearProgressIndicator(
                            value: acc / 100,
                            minHeight: 6,
                            backgroundColor: C.primary.withValues(alpha: .12)))),
                const SizedBox(width: 8),
                Text('$acc%',
                    style: const TextStyle(
                        fontSize: 12,
                        fontWeight: FontWeight.w700,
                        color: C.primary)),
              ]),
            ],
          ),
        ),
      ),
    ),
    );
  }
}
