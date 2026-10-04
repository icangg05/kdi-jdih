import 'package:flutter/material.dart';
import 'package:package_info_plus/package_info_plus.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'disability.dart';
import 'puu.dart';
import 'statistik.dart';
import 'survey.dart';

const _profilItems = [
  (slug: 'sekilas-sejarah', label: 'Sekilas Sejarah'),
  (slug: 'dasar-hukum', label: 'Dasar Hukum'),
  (slug: 'visi', label: 'Visi'),
  (slug: 'misi', label: 'Misi'),
  (slug: 'sto', label: 'Struktur Organisasi'),
];

/// Tab "Lainnya": profil, layanan, pengaturan, tentang.
class LainnyaScreen extends StatelessWidget {
  const LainnyaScreen({super.key});

  void _push(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: const BrandAppBar('Lainnya'),
    body: ListView(
      padding: padTengah(context, maks: 640),
      children: [
        Card(
          child: ExpansionTile(
            leading: const Icon(Icons.info_outline, color: C.accent),
            title: const Text('Profil JDIH'),
            shape: const Border(),
            children: [
              for (final p in _profilItems)
                ListTile(
                  title: Text(p.label),
                  trailing: const Icon(Icons.chevron_right),
                  onTap: () => _push(
                    context,
                    ProfilScreen(kategori: p.slug, title: p.label),
                  ),
                ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        Card(
          child: Column(
            children: [
              ListTile(
                leading: const Icon(Icons.accessible, color: C.accent),
                title: const Text('Layanan Disabilitas'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const DisabilityListScreen()),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.balance, color: C.accent),
                title: const Text('Pembentukan PUU'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const PuuListScreen()),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.bar_chart, color: C.accent),
                title: const Text('Statistik Koleksi'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const StatistikScreen()),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.star_outline, color: C.accent),
                title: const Text('Survei Kepuasan'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const SurveyScreen()),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        Card(
          child: Column(
            children: [
              ListTile(
                leading: const Icon(Icons.translate, color: C.accent),
                title: const Text('Bahasa Konten'),
                trailing: Text(kLangs[langNotifier.value] ?? ''),
                onTap: () => pickLang(context),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.public, color: C.accent),
                title: const Text('Tentang JDIH'),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const AboutScreen()),
              ),
            ],
          ),
        ),
      ],
    ),
  );
}

class ProfilScreen extends StatelessWidget {
  const ProfilScreen({super.key, required this.kategori, required this.title});
  final String kategori, title;

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(title),
    body: LoadView(
      load: () => api.profile(kategori),
      builder: (context, d) => ListView(
        padding: padTengah(context),
        children: [
          d.sn('body') != null
              ? HtmlBody(d.s('body'))
              : const Text(
                  'Konten struktur organisasi tersedia di situs web JDIH Kota Kendari.',
                ),
        ],
      ),
    ),
  );
}

class AboutScreen extends StatelessWidget {
  const AboutScreen({super.key});

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: const BrandAppBar('Tentang JDIH'),
    body: LoadView(
      load: api.meta,
      builder: (context, d) {
        final contact = d.m('contact');
        return ListView(
          padding: padTengah(context, maks: 640),
          children: [
            Text(d.s('app_name'), style: T.judul),
            const Text(
              'Jaringan Dokumentasi dan Informasi Hukum',
              style: TextStyle(color: C.lightInkMuted),
            ),
            const SizedBox(height: 16),
            MetaTable({
              'Telepon': contact.sn('phone'),
              'Email': contact.sn('email'),
              'Alamat': contact.sn('address'),
            }),
            const SectionHeader('Media Sosial'),
            Card(
              child: Column(
                children: [
                  for (final s in d.l('social'))
                    if (s.sn('url') != null)
                      ListTile(
                        leading: const Icon(Icons.link, color: C.accent),
                        title: Text(s.s('label')),
                        onTap: () => openUrl(context, s.sn('url')),
                      ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            // versi dibaca dari build, bukan ditulis tangan yang lupa diubah
            FutureBuilder<PackageInfo>(
              future: PackageInfo.fromPlatform(),
              builder: (_, s) => Center(
                child: Text(
                  'Aplikasi JDIH Kota Kendari'
                  '${s.hasData ? ' v${s.data!.version}' : ''}',
                  style: T.isiKecil.copyWith(color: C.lightInkMuted),
                ),
              ),
            ),
          ],
        );
      },
    ),
  );
}
