import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'disability.dart';
import 'puu.dart';
import 'survey.dart';

const _profilItems = [
  (slug: 'sekilas-sejarah', label: 'Sekilas Sejarah'),
  (slug: 'dasar-hukum', label: 'Dasar Hukum'),
  (slug: 'visi', label: 'Visi'),
  (slug: 'misi', label: 'Misi'),
  (slug: 'sto', label: 'Struktur Organisasi'),
];

const _langs = {'id': 'Indonesia', 'en': 'English', 'zh': '中文', 'ko': '한국어'};

/// Tab "Lainnya": profil, layanan, pengaturan, tentang.
class LainnyaScreen extends StatelessWidget {
  const LainnyaScreen({super.key});

  void _push(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  Future<void> _pickLang(BuildContext context) => showDialog(
        context: context,
        builder: (c) => SimpleDialog(
          title: const Text('Bahasa Konten'),
          children: [
            RadioGroup<String>(
              groupValue: langNotifier.value,
              onChanged: (v) {
                langNotifier.value = v!;
                Navigator.pop(c);
              },
              child: Column(children: [
                for (final e in _langs.entries)
                  RadioListTile<String>(title: Text(e.value), value: e.key),
              ]),
            ),
          ],
        ),
      );

  Future<void> _pickTheme(BuildContext context) => showDialog(
        context: context,
        builder: (c) => SimpleDialog(
          title: const Text('Tema'),
          children: [
            RadioGroup<ThemeMode>(
              groupValue: themeNotifier.value,
              onChanged: (v) {
                themeNotifier.value = v!;
                Navigator.pop(c);
              },
              child: const Column(children: [
                RadioListTile(title: Text('Ikuti Sistem'), value: ThemeMode.system),
                RadioListTile(title: Text('Terang'), value: ThemeMode.light),
                RadioListTile(title: Text('Gelap'), value: ThemeMode.dark),
              ]),
            ),
          ],
        ),
      );

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Lainnya'),
        body: ListView(
          padding: const EdgeInsets.all(16),
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
                      onTap: () => _push(context,
                          ProfilScreen(kategori: p.slug, title: p.label)),
                    ),
                ],
              ),
            ),
            const SizedBox(height: 12),
            Card(
              child: Column(children: [
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
                  leading: const Icon(Icons.star_outline, color: C.accent),
                  title: const Text('Survei Kepuasan'),
                  trailing: const Icon(Icons.chevron_right),
                  onTap: () => _push(context, const SurveyScreen()),
                ),
              ]),
            ),
            const SizedBox(height: 12),
            Card(
              child: Column(children: [
                ListTile(
                  leading: const Icon(Icons.translate, color: C.accent),
                  title: const Text('Bahasa Konten'),
                  trailing: Text(_langs[langNotifier.value] ?? ''),
                  onTap: () => _pickLang(context),
                ),
                const Divider(height: 1),
                ListTile(
                  leading: const Icon(Icons.dark_mode_outlined, color: C.accent),
                  title: const Text('Tema'),
                  onTap: () => _pickTheme(context),
                ),
                const Divider(height: 1),
                ListTile(
                  leading: const Icon(Icons.public, color: C.accent),
                  title: const Text('Tentang JDIH'),
                  trailing: const Icon(Icons.chevron_right),
                  onTap: () => _push(context, const AboutScreen()),
                ),
              ]),
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
            padding: const EdgeInsets.all(16),
            children: [
              d.sn('body') != null
                  ? HtmlBody(d.s('body'))
                  : const Text(
                      'Konten struktur organisasi tersedia di situs web JDIH Kota Kendari.'),
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
              padding: const EdgeInsets.all(16),
              children: [
                Text(d.s('app_name'),
                    style:
                        const TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
                const Text('Jaringan Dokumentasi dan Informasi Hukum',
                    style: TextStyle(color: Colors.grey)),
                const SizedBox(height: 16),
                MetaTable({
                  'Telepon': contact.sn('phone'),
                  'Email': contact.sn('email'),
                  'Alamat': contact.sn('address'),
                }),
                const SectionHeader('Media Sosial'),
                Card(
                  child: Column(children: [
                    for (final s in d.l('social'))
                      if (s.sn('url') != null)
                        ListTile(
                          leading: const Icon(Icons.link, color: C.accent),
                          title: Text(s.s('label')),
                          onTap: () => openUrl(context, s.sn('url')),
                        ),
                  ]),
                ),
                const SizedBox(height: 16),
                const Center(
                    child: Text('Aplikasi JDIH Kota Kendari v1.0.0',
                        style: TextStyle(fontSize: 12, color: Colors.grey))),
              ],
            );
          },
        ),
      );
}
