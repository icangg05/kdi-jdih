import 'package:flutter/material.dart';

import '../api.dart';
import '../widgets.dart';

/// Slug + label — samakan dengan backend (MobileApiController::PUU_LABEL).
const puuCategories = [
  (slug: 'naskah-akademik', label: 'Naskah Akademik'),
  (slug: 'naskah-keterangan-penjelasan', label: 'Naskah Keterangan/Penjelasan'),
  (slug: 'rancangan-puu', label: 'Rancangan PUU'),
  (slug: 'penelitian-hukum', label: 'Penelitian Hukum'),
  (slug: 'pengkajian-hukum', label: 'Pengkajian Hukum'),
  (slug: 'pengkajian-konstitusi', label: 'Pengkajian Konstitusi'),
  (slug: 'analisis-evaluasi', label: 'Analisis & Evaluasi'),
];

class PuuListScreen extends StatefulWidget {
  const PuuListScreen({super.key});

  @override
  State<PuuListScreen> createState() => _PuuListScreenState();
}

class _PuuListScreenState extends State<PuuListScreen> {
  String _category = '';

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Pembentukan PUU'),
        body: Column(
          children: [
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
              child: Row(children: [
                for (final c in puuCategories)
                  Padding(
                    padding: const EdgeInsets.only(right: 8),
                    child: ChoiceChip(
                      label: Text(c.label),
                      selected: _category == c.slug,
                      onSelected: (sel) =>
                          setState(() => _category = sel ? c.slug : ''),
                    ),
                  ),
              ]),
            ),
            Expanded(
              child: PagedListView(
                key: ValueKey(_category),
                fetch: (page) => api.puu(category: _category, page: page),
                itemBuilder: (context, x) => Card(
                  child: ListTile(
                    leading: const IconSquircle(Icons.balance),
                    title: Text(x.s('judul'),
                        maxLines: 2, overflow: TextOverflow.ellipsis),
                    subtitle: Text([
                      puuCategories
                              .where((c) => c.slug == x.sn('kategori'))
                              .firstOrNull
                              ?.label ??
                          x.s('jenis_dokumen'),
                      if (x.sn('tahun') != null) x.s('tahun'),
                    ].join(' · ')),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => PuuDetailScreen(id: x.i('id')))),
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
        appBar: const BrandAppBar('Pembentukan PUU'),
        body: LoadView(
          load: () => api.puuDetail(id),
          builder: (context, x) {
            // section teks panjang; hanya tampil bila terisi
            final sections = <String, String?>{
              'Abstrak': x.sn('abstrak'),
              'Latar Belakang': x.sn('latar_belakang'),
              'Rumusan Masalah': x.sn('rumusan_masalah'),
              'Tujuan Penelitian': x.sn('tujuan_penelitian'),
              'Metodologi': x.sn('metodologi_penelitian'),
              'Fokus Penelitian': x.sn('fokus_penelitian'),
              'Hasil Penelitian': x.sn('hasil_penelitian'),
              'Objek Pengkajian': x.sn('objek_pengkajian'),
              'Kesimpulan Pengkajian': x.sn('kesimpulan_pengkajian'),
              'Aspek Konstitusi': x.sn('aspek_konstitusi'),
              'Temuan Evaluasi': x.sn('temuan_evaluasi'),
              'Rekomendasi': x.sn('rekomendasi'),
              'Rekomendasi Perbaikan': x.sn('rekomendasi_perbaikan'),
              'Keterangan': x.sn('keterangan'),
            };
            return ListView(
              padding: const EdgeInsets.all(16),
              children: [
                if (x.sn('cover_url') != null)
                  AspectRatio(
                      aspectRatio: 16 / 9,
                      child: NetImage(x.sn('cover_url'), radius: 12)),
                const SizedBox(height: 16),
                Row(children: [
                  JenisChip(puuCategories
                          .where((c) => c.slug == x.sn('kategori'))
                          .firstOrNull
                          ?.label ??
                      x.sn('jenis_dokumen')),
                  const SizedBox(width: 8),
                  StatusBadge(x.sn('status_dokumen')),
                ]),
                const SizedBox(height: 8),
                Text(x.s('judul'),
                    style:
                        const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
                const SizedBox(height: 16),
                MetaTable({
                  'Nomor': x.sn('nomor_dokumen'),
                  'Tahun': x.sn('tahun'),
                  'Lembaga Pemrakarsa': x.sn('lembaga_pemrakarsa'),
                  'Tahapan': x.sn('tahapan_pembentukan'),
                  'Penulis': x.sn('penulis'),
                  'Editor': x.sn('editor'),
                  'Kata Kunci': x.sn('kata_kunci'),
                  'Dilihat': '${x.i('views')} kali',
                }),
                for (final e in sections.entries)
                  if (e.value != null) ...[
                    SectionHeader(e.key),
                    Text(e.value!, style: const TextStyle(height: 1.6)),
                  ],
                const SizedBox(height: 16),
                if (x.sn('dokumen_url') != null)
                  Padding(
                    padding: const EdgeInsets.only(bottom: 8),
                    child: FilledButton.icon(
                        icon: const Icon(Icons.download),
                        label: const Text('Unduh Dokumen Utama'),
                        onPressed: () => openUrl(context, x.sn('dokumen_url'))),
                  ),
                if (x.sn('lampiran_url') != null)
                  OutlinedButton.icon(
                      icon: const Icon(Icons.attach_file),
                      label: const Text('Unduh Lampiran'),
                      onPressed: () => openUrl(context, x.sn('lampiran_url'))),
                const SizedBox(height: 24),
              ],
            );
          },
        ),
      );
}
