import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';

const docCategories = [
  (slug: 'peraturan', label: 'Peraturan & Keputusan', short: 'Peraturan', icon: Icons.account_balance),
  (slug: 'monografi', label: 'Monografi Hukum', short: 'Monografi', icon: Icons.menu_book),
  (slug: 'artikel', label: 'Artikel / Majalah Hukum', short: 'Artikel', icon: Icons.article),
  (slug: 'putusan', label: 'Putusan', short: 'Putusan', icon: Icons.gavel),
];

String docCategoryLabel(String slug) =>
    docCategories.where((c) => c.slug == slug).firstOrNull?.label ?? slug;

class DocumentCard extends StatelessWidget {
  const DocumentCard(this.d, {super.key, this.accuracy});
  final Json d;

  /// Skor akurasi 0-100 (hasil AI); null = sembunyikan.
  final int? accuracy;

  @override
  Widget build(BuildContext context) {
    final muted = Theme.of(context).brightness == Brightness.dark
        ? C.darkInkMuted
        : C.lightInkMuted;
    final nomor = d.sn('nomor_peraturan');
    final tahun = d.sn('tahun_terbit');
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
                JenisChip(d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan')),
                if (tahun != null) ...[
                  const SizedBox(width: 8),
                  Text(tahun, style: TextStyle(fontSize: 12, color: muted)),
                ],
                const Spacer(),
                StatusBadge(d.sn('status') ?? d.sn('status_terakhir')),
              ]),
              const SizedBox(height: 8),
              Text(d.s('judul'),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w600)),
              const SizedBox(height: 6),
              Text(
                [
                  if (nomor != null) 'No. $nomor',
                  if (d.sn('bidang_hukum') != null) d.s('bidang_hukum'),
                ].join(' · '),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(fontSize: 12, color: muted),
              ),
              const SizedBox(height: 6),
              if (accuracy != null)
                Row(children: [
                  Expanded(
                      child: ClipRRect(
                          borderRadius: BorderRadius.circular(4),
                          child: LinearProgressIndicator(
                              value: accuracy! / 100,
                              minHeight: 6,
                              backgroundColor: C.primary.withValues(alpha: .12)))),
                  const SizedBox(width: 8),
                  Text('$accuracy%',
                      style: const TextStyle(
                          fontSize: 12, fontWeight: FontWeight.w700, color: C.primary)),
                ])
              else
                Row(children: [
                  Icon(Icons.visibility_outlined, size: 14, color: muted),
                  const SizedBox(width: 4),
                  Text('${d.i('hit_see')}', style: TextStyle(fontSize: 12, color: muted)),
                  const SizedBox(width: 12),
                  Icon(Icons.download_outlined, size: 14, color: muted),
                  const SizedBox(width: 4),
                  Text('${d.i('hit_download')}',
                      style: TextStyle(fontSize: 12, color: muted)),
                ]),
            ],
          ),
        ),
      ),
    ),
    );
  }
}

/// Tab "Dokumen": hub asimetris — kartu utama color-block + tile sekunder.
class DocumentsHubScreen extends StatelessWidget {
  const DocumentsHubScreen({super.key});

  void _open(BuildContext context, String slug) => Navigator.push(context,
      MaterialPageRoute(builder: (_) => DocumentListScreen(category: slug)));

  @override
  Widget build(BuildContext context) {
    final utama = docCategories.first;
    final lainnya = docCategories.sublist(1);
    return Scaffold(
      appBar: const BrandAppBar('Dokumen Hukum'),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // kartu utama: peraturan (koleksi terbesar) sebagai color-block
          Rise(
            child: Pressable(
              child: ClipRRect(
                borderRadius: BorderRadius.circular(20),
                child: Stack(children: [
                  Positioned.fill(
                    child: DecoratedBox(
                      decoration: const BoxDecoration(
                        gradient: LinearGradient(
                            colors: [C.accent, C.accentDeep],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight),
                      ),
                    ),
                  ),
                  const Positioned.fill(child: GeoPattern(opacity: .12)),
                  Material(
                    color: Colors.transparent,
                    child: InkWell(
                      onTap: () => _open(context, utama.slug),
                      child: Padding(
                        padding: const EdgeInsets.all(20),
                        child: Row(children: [
                          Container(
                            width: 56,
                            height: 56,
                            decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: .16),
                              borderRadius: BorderRadius.circular(18),
                            ),
                            child: Icon(utama.icon, color: Colors.white, size: 30),
                          ),
                          const SizedBox(width: 16),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(utama.label,
                                    style: const TextStyle(
                                        color: Colors.white,
                                        fontSize: 18,
                                        fontWeight: FontWeight.w800)),
                                const SizedBox(height: 4),
                                Text('Perda, Perwali, dan Keputusan Wali Kota',
                                    style: TextStyle(
                                        color: Colors.white.withValues(alpha: .8),
                                        fontSize: 13)),
                              ],
                            ),
                          ),
                          const Icon(Icons.arrow_forward, color: Colors.white),
                        ]),
                      ),
                    ),
                  ),
                ]),
              ),
            ),
          ),
          const SizedBox(height: 12),
          // tile sekunder: 3 kolom ringkas
          Row(children: [
            for (final (i, c) in lainnya.indexed) ...[
              Expanded(
                child: Rise(
                  delayMs: 80 + i * 60,
                  child: Pressable(
                    child: Card(
                      child: InkWell(
                        borderRadius: BorderRadius.circular(16),
                        onTap: () => _open(context, c.slug),
                        child: Padding(
                          padding: const EdgeInsets.symmetric(vertical: 18),
                          child: Column(children: [
                            IconSquircle(c.icon),
                            const SizedBox(height: 10),
                            Text(c.short,
                                style: const TextStyle(
                                    fontSize: 13, fontWeight: FontWeight.w700)),
                          ]),
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              if (c != lainnya.last) const SizedBox(width: 12),
            ],
          ]),
        ],
      ),
    );
  }
}

class DocumentListScreen extends StatefulWidget {
  const DocumentListScreen({super.key, required this.category});
  final String category;

  @override
  State<DocumentListScreen> createState() => _DocumentListScreenState();
}

class _DocumentListScreenState extends State<DocumentListScreen> {
  String _q = '', _jenis = '', _tahun = '', _status = '';
  Json _filters = {};

  @override
  void initState() {
    super.initState();
    api.documentFilters(widget.category).then((f) {
      if (mounted) setState(() => _filters = f);
    }).catchError((_) {}); // filter gagal -> daftar tetap jalan
  }

  Widget _dropdown(String hint, String value, String key, void Function(String) set) {
    final opts = _filters.ls(key);
    if (opts.isEmpty) return const SizedBox.shrink();
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: DropdownButton<String>(
        hint: Text(hint),
        value: value.isEmpty ? null : value,
        underline: const SizedBox.shrink(),
        borderRadius: BorderRadius.circular(12),
        items: opts
            .map((o) => DropdownMenuItem(
                value: o, child: Text(o, overflow: TextOverflow.ellipsis)))
            .toList(),
        onChanged: (v) => setState(() => set(v ?? '')),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final hasFilter =
        _q.isNotEmpty || _jenis.isNotEmpty || _tahun.isNotEmpty || _status.isNotEmpty;
    return Scaffold(
      appBar: BrandAppBar(docCategoryLabel(widget.category)),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
            child: TextField(
              decoration: const InputDecoration(
                  hintText: 'Cari judul...', prefixIcon: Icon(Icons.search)),
              textInputAction: TextInputAction.search,
              onSubmitted: (v) => setState(() => _q = v),
            ),
          ),
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
            child: Row(children: [
              _dropdown('Jenis', _jenis, 'jenis', (v) => _jenis = v),
              _dropdown('Tahun', _tahun, 'tahun', (v) => _tahun = v),
              _dropdown('Status', _status, 'status', (v) => _status = v),
              if (hasFilter)
                TextButton(
                    onPressed: () => setState(() {
                          _q = '';
                          _jenis = '';
                          _tahun = '';
                          _status = '';
                        }),
                    child: const Text('Reset')),
            ]),
          ),
          Expanded(
            child: PagedListView(
              key: ValueKey('${widget.category}|$_q|$_jenis|$_tahun|$_status'),
              fetch: (page) => api.documents(
                  category: widget.category,
                  q: _q,
                  jenis: _jenis,
                  tahun: _tahun,
                  status: _status,
                  page: page),
              empty: 'Tidak ada dokumen sesuai filter.',
              itemBuilder: (_, d) => DocumentCard(d),
            ),
          ),
        ],
      ),
    );
  }
}

class DocumentDetailScreen extends StatelessWidget {
  const DocumentDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) {
    final muted = Theme.of(context).brightness == Brightness.dark
        ? C.darkInkMuted
        : C.lightInkMuted;
    return Scaffold(
      appBar: const BrandAppBar('Detail Dokumen'),
      body: LoadView(
        load: () => api.documentDetail(id),
        builder: (context, d) {
          final nomor = d.sn('nomor_peraturan');
          final tahun = d.sn('tahun_terbit');
          final abstrak = d.sn('abstrak');
          final subjek = d.ls('subjek');
          final pengarang = d.l('pengarang');
          final lampiran = d.l('lampiran');
          final terkait = d.l('peraturan_terkait');
          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              Row(children: [
                JenisChip(d.sn('singkatan_jenis') ?? d.sn('jenis_peraturan')),
                const SizedBox(width: 8),
                StatusBadge(d.sn('status') ?? d.sn('status_terakhir')),
              ]),
              const SizedBox(height: 12),
              Text(d.s('judul'),
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
              if (nomor != null || tahun != null)
                Padding(
                  padding: const EdgeInsets.only(top: 4),
                  child: Text(
                      [
                        if (nomor != null) 'Nomor $nomor',
                        if (tahun != null) 'Tahun $tahun'
                      ].join(' · '),
                      style: TextStyle(color: muted)),
                ),
              const SizedBox(height: 16),
              MetaTable({
                'T.E.U.': d.sn('teu'),
                'Bentuk': d.sn('bentuk_peraturan'),
                'Jenis': d.sn('jenis_peraturan'),
                'Tempat Terbit': d.sn('tempat_terbit'),
                'Penerbit': d.sn('penerbit'),
                'Tgl. Penetapan': fmtDate(d.sn('tanggal_penetapan')),
                'Tgl. Pengundangan': fmtDate(d.sn('tanggal_pengundangan')),
                'Sumber': d.sn('sumber'),
                'Bahasa': d.sn('bahasa'),
                'Bidang Hukum': d.sn('bidang_hukum'),
                'Penandatangan': d.sn('penandatanganan'),
                'ISBN': d.sn('isbn'),
                'Deskripsi Fisik': d.sn('deskripsi_fisik'),
                'Lembaga Peradilan': d.sn('lembaga_peradilan'),
                'Pemohon': d.sn('pemohon'),
                'Termohon': d.sn('termohon'),
                'Jenis Perkara': d.sn('jenis_perkara'),
              }),
              if (abstrak != null) ...[
                const SectionHeader('Abstrak'),
                HtmlBody(abstrak),
              ],
              if (subjek.isNotEmpty) ...[
                const SectionHeader('Subjek'),
                Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [for (final s in subjek) JenisChip(s)]),
              ],
              if (pengarang.isNotEmpty) ...[
                const SectionHeader('Pengarang'),
                Text(pengarang.map((p) => p.s('nama')).join(', ')),
              ],
              if (lampiran.isNotEmpty) ...[
                const SectionHeader('Lampiran'),
                for (final l in lampiran)
                  Padding(
                    padding: const EdgeInsets.only(bottom: 8),
                    child: FilledButton.icon(
                      icon: const Icon(Icons.download),
                      label: Text(l.s('judul'), overflow: TextOverflow.ellipsis),
                      onPressed: () {
                        api.documentDownload(id).ignore(); // catat hit_download
                        openUrl(context, l.sn('url'));
                      },
                    ),
                  ),
              ],
              if (terkait.isNotEmpty) ...[
                const SectionHeader('Peraturan Terkait'),
                for (final t in terkait)
                  Card(
                    child: ListTile(
                      title: Text(t.s('judul'), maxLines: 2, overflow: TextOverflow.ellipsis),
                      trailing: const Icon(Icons.chevron_right),
                      onTap: () => Navigator.push(
                          context,
                          MaterialPageRoute(
                              builder: (_) => DocumentDetailScreen(id: t.i('id')))),
                    ),
                  ),
              ],
              const SizedBox(height: 16),
              Text(
                  '${d.m('statistik').i('dilihat')} dilihat · '
                  '${d.m('statistik').i('diunduh')} diunduh',
                  style: TextStyle(fontSize: 12, color: muted)),
              const SizedBox(height: 24),
            ],
          );
        },
      ),
    );
  }
}
