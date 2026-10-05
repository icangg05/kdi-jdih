import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'doc_view.dart';

class DisabilityListScreen extends StatefulWidget {
  const DisabilityListScreen({super.key});

  @override
  State<DisabilityListScreen> createState() => _DisabilityListScreenState();
}

class _DisabilityListScreenState extends State<DisabilityListScreen> {
  String _q = '';

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.disabilityServices),
    body: Column(
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
          child: TextField(
            decoration: InputDecoration(
              hintText: context.l10n.searchDisabilityHint,
              prefixIcon: const Icon(Icons.search),
            ),
            textInputAction: TextInputAction.search,
            onSubmitted: (v) => setState(() => _q = v),
          ),
        ),
        Expanded(
          child: PagedListView(
            key: ValueKey(_q),
            fetch: (page) => api.disability(page: page, q: _q),
            itemBuilder: (context, x, _) => Card(
              child: ListTile(
                leading: const IconTile(Icons.accessible),
                title: Text(
                  x.s('judul'),
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
                subtitle: Text(
                  [
                    x.s('jenis_dokumen').toUpperCase(),
                    if (x.sn('tahun') != null) x.s('tahun'),
                  ].join(' · '),
                ),
                trailing: StatusBadge(x.sn('status_dokumen')),
                onTap: () => Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (_) => DisabilityDetailScreen(id: x.i('id')),
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

class DisabilityDetailScreen extends StatelessWidget {
  const DisabilityDetailScreen({super.key, required this.id});
  final int id;

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.disabilityServices),
    body: LoadView(
      load: () => api.disabilityDetail(id),
      contoh: const {
        'judul':
            'Peraturan Daerah tentang Penghormatan Hak Penyandang Disabilitas',
        'jenis_dokumen': 'Peraturan Daerah',
        'status_dokumen': 'Berlaku',
        'nomor_dokumen': '3',
        'tahun': '2022',
        'lembaga_penetap': 'Pemerintah Kota Kendari',
        'jenis_disabilitas': 'Semua ragam',
        'bahasa': 'Indonesia',
        'abstrak': kSkeletonTeks,
      },
      builder: (context, x) {
        final l = context.l10n;
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
              JenisChip(x.sn('jenis_dokumen')),
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
            l.metaPlaceEnacted: x.sn('tempat_penetapan'),
            l.metaDateEnacted: fmtDate(x.sn('tanggal_penetapan')),
            l.metaEnactedBy: x.sn('lembaga_penetap'),
            l.metaDisabilityType: x.sn('jenis_disabilitas'),
            l.metaScope: x.sn('ruang_lingkup'),
            l.metaPolicySector: x.sn('sektor_kebijakan'),
            l.metaKeywords: x.sn('kata_kunci'),
            l.metaPages: x.sn('jumlah_halaman'),
            l.language: x.sn('bahasa'),
            l.metaWriter: x.sn('penulis'),
            l.publisher: x.sn('penerbit'),
            l.source: x.sn('sumber'),
          }),
          if (x.sn('abstrak') != null) ...[
            SectionHeader(l.abstract),
            Text(x.s('abstrak'), style: T.bacaan),
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
