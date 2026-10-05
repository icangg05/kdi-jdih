import 'package:flutter/material.dart';
import 'package:package_info_plus/package_info_plus.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'disability.dart';
import 'puu.dart';
import 'statistik.dart';
import 'survey.dart';

List<({String slug, String label})> _profilItems(AppLocalizations l) => [
  (slug: 'sekilas-sejarah', label: l.profileHistory),
  (slug: 'dasar-hukum', label: l.profileLegalBasis),
  (slug: 'visi', label: l.profileVision),
  (slug: 'misi', label: l.profileMission),
  (slug: 'sto', label: l.profileStructure),
];

/// Tab "Lainnya": profil, layanan, pengaturan, tentang.
class LainnyaScreen extends StatelessWidget {
  const LainnyaScreen({super.key});

  void _push(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.more),
    body: ListView(
      padding: padTengah(context, maks: 640),
      children: [
        Card(
          child: ExpansionTile(
            leading: const Icon(Icons.info_outline, color: C.accent),
            title: Text(context.l10n.jdihProfile),
            shape: const Border(),
            children: [
              for (final p in _profilItems(context.l10n))
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
                title: Text(context.l10n.disabilityServices),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const DisabilityListScreen()),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.balance, color: C.accent),
                title: Text(context.l10n.lawMaking),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const PuuListScreen()),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.bar_chart, color: C.accent),
                title: Text(context.l10n.collectionStats),
                trailing: const Icon(Icons.chevron_right),
                onTap: () => _push(context, const StatistikScreen()),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.star_outline, color: C.accent),
                title: Text(context.l10n.satisfactionSurvey),
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
                title: Text(context.l10n.language),
                trailing: Text(kLangs[langNotifier.value] ?? ''),
                onTap: () => pickLang(context),
              ),
              const Divider(height: 1),
              ListTile(
                leading: const Icon(Icons.public, color: C.accent),
                title: Text(context.l10n.aboutJdih),
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
      contoh: const {
        'body':
            '<p>$kSkeletonTeks</p><p>$kSkeletonTeks</p><p>$kSkeletonTeks</p>',
      },
      builder: (context, d) => ListView(
        padding: padTengah(context),
        children: [
          d.sn('body') != null
              ? HtmlBody(d.s('body'))
              : Text(context.l10n.orgStructureOnWeb),
        ],
      ),
    ),
  );
}

class AboutScreen extends StatelessWidget {
  const AboutScreen({super.key});

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.aboutJdih),
    body: LoadView(
      load: api.meta,
      contoh: const {
        'app_name': 'JDIH Kota Kendari',
        'contact': {
          'phone': '(0401) 312 3456',
          'email': 'jdih@kendarikota.go.id',
          'address': 'Jl. Drs. H. Abdullah Silondae No. 8, Kendari',
        },
        'social': [
          {'label': 'Facebook', 'url': '-'},
          {'label': 'Instagram', 'url': '-'},
          {'label': 'YouTube', 'url': '-'},
        ],
      },
      builder: (context, d) {
        final contact = d.m('contact');
        return ListView(
          padding: padTengah(context, maks: 640),
          children: [
            Text(context.l10n.appName, style: T.judul),
            Text(
              context.l10n.jdihFull,
              style: const TextStyle(color: C.lightInkMuted),
            ),
            const SizedBox(height: 16),
            MetaTable({
              context.l10n.phone: contact.sn('phone'),
              context.l10n.email: contact.sn('email'),
              context.l10n.address: contact.sn('address'),
            }),
            SectionHeader(context.l10n.socialMedia),
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
                  '${context.l10n.appLabel}'
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
