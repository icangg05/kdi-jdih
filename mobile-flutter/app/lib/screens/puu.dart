import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'doc_view.dart';

/// Slug + label — samakan dengan backend (MobileApiController::PUU_LABEL).
List<({String slug, String label})> puuCategories(AppLocalizations l) => [
  (slug: 'naskah-akademik', label: l.puuAcademicPaper),
  (slug: 'naskah-keterangan-penjelasan', label: l.puuExplanatory),
  (slug: 'rancangan-puu', label: l.puuDraft),
  (slug: 'penelitian-hukum', label: l.puuResearch),
  (slug: 'pengkajian-hukum', label: l.puuLegalReview),
  (slug: 'pengkajian-konstitusi', label: l.puuConstitutionalReview),
  (slug: 'analisis-evaluasi', label: l.puuAnalysis),
];

/// Label kategori PUU dari slug; null bila slug tak dikenal.
String? puuCategoryLabel(BuildContext context, String? slug) => puuCategories(
  context.l10n,
).where((c) => c.slug == slug).firstOrNull?.label;

class PuuListScreen extends StatefulWidget {
  const PuuListScreen({super.key});

  @override
  State<PuuListScreen> createState() => _PuuListScreenState();
}

class _PuuListScreenState extends State<PuuListScreen> {
  String _category = '';

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.lawMaking),
    body: Column(
      children: [
        SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
          child: Row(
            children: [
              for (final c in puuCategories(context.l10n))
                Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: ChoiceChip(
                    label: Text(c.label),
                    selected: _category == c.slug,
                    onSelected: (sel) =>
                        setState(() => _category = sel ? c.slug : ''),
                  ),
                ),
            ],
          ),
        ),
        Expanded(
          child: PagedListView(
            key: ValueKey(_category),
            fetch: (page) => api.puu(category: _category, page: page),
            itemBuilder: (context, x, _) => Card(
              child: ListTile(
                leading: const IconTile(Icons.balance),
                title: Text(
                  x.s('judul'),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                subtitle: Text(
                  [
                    puuCategoryLabel(context, x.sn('kategori')) ??
                        x.s('jenis_dokumen'),
                    if (x.sn('tahun') != null) x.s('tahun'),
                  ].join(' · '),
                ),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => PuuDetailScreen(id: x.i('id')),
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

class PuuDetailScreen extends StatelessWidget {
  const PuuDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.lawMaking),
    body: LoadView(
      load: () => api.puuDetail(id),
      contoh: const {
        'judul':
            'Naskah Akademik Rancangan Peraturan Daerah tentang Pajak Daerah',
        'jenis_dokumen': 'Naskah Akademik',
        'status_dokumen': 'Selesai',
        'nomor_dokumen': '12',
        'tahun': '2024',
        'lembaga_pemrakarsa': 'Bagian Hukum Setda Kota Kendari',
        'penulis': 'Tim Penyusun',
        'abstrak': kSkeletonTeks,
        'latar_belakang': kSkeletonTeks,
      },
      builder: (context, x) {
        // section teks panjang; hanya tampil bila terisi
        final l = context.l10n;
        final sections = <String, String?>{
          l.abstract: x.sn('abstrak'),
          l.secBackground: x.sn('latar_belakang'),
          l.secProblem: x.sn('rumusan_masalah'),
          l.secObjective: x.sn('tujuan_penelitian'),
          l.secMethod: x.sn('metodologi_penelitian'),
          l.secFocus: x.sn('fokus_penelitian'),
          l.secResults: x.sn('hasil_penelitian'),
          l.secReviewObject: x.sn('objek_pengkajian'),
          l.secReviewConclusion: x.sn('kesimpulan_pengkajian'),
          l.secConstitutional: x.sn('aspek_konstitusi'),
          l.secEvalFindings: x.sn('temuan_evaluasi'),
          l.secRecommendation: x.sn('rekomendasi'),
          l.secImprovement: x.sn('rekomendasi_perbaikan'),
          l.secNotes: x.sn('keterangan'),
        };
        return ListView(
          padding: padTengah(context),
          children: [
            if (x.sn('cover_url') != null)
              AspectRatio(
                aspectRatio: 16 / 9,
                child: NetImage(x.sn('cover_url'), radius: 12),
              ),
            const SizedBox(height: 16),
            Row(
              children: [
                JenisChip(
                  puuCategoryLabel(context, x.sn('kategori')) ??
                      x.sn('jenis_dokumen'),
                ),
                const SizedBox(width: 8),
                StatusBadge(x.sn('status_dokumen')),
              ],
            ),
            const SizedBox(height: 8),
            Text(x.s('judul'), style: T.judulDetail),
            const SizedBox(height: 16),
            MetaTable({
              l.number: x.sn('nomor_dokumen'),
              l.year: x.sn('tahun'),
              l.metaInitiator: x.sn('lembaga_pemrakarsa'),
              l.metaStage: x.sn('tahapan_pembentukan'),
              l.metaWriter: x.sn('penulis'),
              l.metaEditor: x.sn('editor'),
              l.metaKeywords: x.sn('kata_kunci'),
              l.metaViews: l.timesCount(x.i('views')),
            }),
            for (final e in sections.entries)
              if (e.value != null) ...[
                SectionHeader(e.key),
                Text(e.value!, style: T.bacaan),
              ],
            const SizedBox(height: 16),
            if (x.sn('dokumen_url') != null) ...[
              SectionHeader(l.document),
              DocFileTile(
                x.s('dokumen_url'),
                title: l.mainDocument,
                nama: l.fileDocumentOf(x.s('judul')),
              ),
            ],
            if (x.sn('lampiran_url') != null) ...[
              const SizedBox(height: AppSpacing.md),
              DocFileTile(
                x.s('lampiran_url'),
                title: l.attachment,
                nama: l.fileAttachmentOf(x.s('judul')),
              ),
            ],
            const SizedBox(height: 24),
          ],
        );
      },
    ),
  );
}
