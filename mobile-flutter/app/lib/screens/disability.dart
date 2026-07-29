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
        appBar: const BrandAppBar('Layanan Disabilitas'),
        body: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 8),
              child: TextField(
                decoration: const InputDecoration(
                    hintText: 'Cari dokumen disabilitas...',
                    prefixIcon: Icon(Icons.search)),
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
                    leading: const IconSquircle(Icons.accessible),
                    title: Text(x.s('judul'),
                        maxLines: 2, overflow: TextOverflow.ellipsis),
                    subtitle: Text([
                      x.s('jenis_dokumen').toUpperCase(),
                      if (x.sn('tahun') != null) x.s('tahun'),
                    ].join(' · ')),
                    trailing: StatusBadge(x.sn('status_dokumen')),
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) =>
                                DisabilityDetailScreen(id: x.i('id')))),
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
        appBar: const BrandAppBar('Layanan Disabilitas'),
        body: LoadView(
          load: () => api.disabilityDetail(id),
          builder: (context, x) => ListView(
            padding: const EdgeInsets.all(16),
            children: [
              if (x.sn('cover_url') != null)
                AspectRatio(
                    aspectRatio: 16 / 9, child: NetImage(x.sn('cover_url'), radius: 12)),
              const SizedBox(height: 16),
              Row(children: [
                JenisChip(x.sn('jenis_dokumen')),
                const SizedBox(width: 8),
                StatusBadge(x.sn('status_dokumen')),
              ]),
              const SizedBox(height: 8),
              Text(x.s('judul'),
                  style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
              const SizedBox(height: 16),
              MetaTable({
                'Nomor': x.sn('nomor_dokumen'),
                'Tahun': x.sn('tahun'),
                'Tempat Penetapan': x.sn('tempat_penetapan'),
                'Tgl. Penetapan': fmtDate(x.sn('tanggal_penetapan')),
                'Lembaga Penetap': x.sn('lembaga_penetap'),
                'Jenis Disabilitas': x.sn('jenis_disabilitas'),
                'Ruang Lingkup': x.sn('ruang_lingkup'),
                'Sektor Kebijakan': x.sn('sektor_kebijakan'),
                'Kata Kunci': x.sn('kata_kunci'),
                'Jumlah Halaman': x.sn('jumlah_halaman'),
                'Bahasa': x.sn('bahasa'),
                'Penulis': x.sn('penulis'),
                'Penerbit': x.sn('penerbit'),
                'Sumber': x.sn('sumber'),
              }),
              if (x.sn('abstrak') != null) ...[
                const SectionHeader('Abstrak'),
                Text(x.s('abstrak'), style: const TextStyle(height: 1.6)),
              ],
              const SizedBox(height: 16),
              if (x.sn('dokumen_url') != null) ...[
                const SectionHeader('Dokumen'),
                DocFileTile(x.s('dokumen_url'), title: 'Dokumen Utama'),
              ],
              if (x.sn('lampiran_url') != null) ...[
                const SizedBox(height: AppSpacing.md),
                DocFileTile(x.s('lampiran_url'), title: 'Lampiran'),
              ],
              const SizedBox(height: 24),
            ],
          ),
        ),
      );
}
