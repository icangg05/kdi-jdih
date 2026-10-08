import 'package:flutter/material.dart';
import 'package:package_info_plus/package_info_plus.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'disability.dart';
import 'puu.dart';
import 'statistik.dart';
import 'survey.dart';

List<({String slug, String label, IconData icon})> _profilItems(
  AppLocalizations l,
) => [
  (slug: 'sekilas-sejarah', label: l.profileHistory, icon: Icons.history_edu),
  (slug: 'dasar-hukum', label: l.profileLegalBasis, icon: Icons.gavel),
  (slug: 'visi', label: l.profileVision, icon: Icons.visibility_outlined),
  (slug: 'misi', label: l.profileMission, icon: Icons.flag_outlined),
  (slug: 'sto', label: l.profileStructure, icon: Icons.account_tree_outlined),
];

/// Tab "Lainnya": menu dikelompokkan per tujuan (profil, layanan, aplikasi),
/// tiap kelompok satu panel berjudul.
class LainnyaScreen extends StatelessWidget {
  const LainnyaScreen({super.key});

  void _push(BuildContext context, Widget page) =>
      Navigator.push(context, MaterialPageRoute(builder: (_) => page));

  @override
  Widget build(BuildContext context) {
    final l = context.l10n;
    return Scaffold(
      appBar: BrandAppBar(l.more),
      body: ListView(
        padding: padTengah(context, maks: 640, atas: 0),
        children: [
          _Kelompok(l.jdihProfile, [
            for (final p in _profilItems(l))
              _Menu(
                icon: p.icon,
                label: p.label,
                onTap: () => _push(
                  context,
                  ProfilScreen(kategori: p.slug, title: p.label),
                ),
              ),
          ]),
          _Kelompok(l.menuServices, [
            _Menu(
              icon: Icons.accessible,
              label: l.disabilityServices,
              onTap: () => _push(context, const DisabilityListScreen()),
            ),
            _Menu(
              icon: Icons.balance,
              label: l.lawMaking,
              onTap: () => _push(context, const PuuListScreen()),
            ),
            _Menu(
              icon: Icons.bar_chart,
              label: l.collectionStats,
              onTap: () => _push(context, const StatistikScreen()),
            ),
            _Menu(
              icon: Icons.star_outline,
              label: l.satisfactionSurvey,
              onTap: () => _push(context, const SurveyScreen()),
            ),
          ]),
          _Kelompok(l.menuApp, [
            _Menu(
              icon: Icons.translate,
              label: l.language,
              nilai: kLangs[langNotifier.value],
              onTap: () => pickLang(context),
            ),
            _Menu(
              icon: Icons.public,
              label: l.aboutJdih,
              onTap: () => _push(context, const AboutScreen()),
            ),
          ]),
        ],
      ),
    );
  }
}

/// Satu kelompok menu: judul seksi, lalu panel berisi barisnya.
class _Kelompok extends StatelessWidget {
  const _Kelompok(this.judul, this.menu);
  final String judul;
  final List<Widget> menu;

  @override
  Widget build(BuildContext context) => Column(
    crossAxisAlignment: CrossAxisAlignment.stretch,
    children: [
      SectionHeader(judul),
      Card(
        clipBehavior: Clip.antiAlias,
        child: Column(
          children: [
            for (final (i, m) in menu.indexed) ...[
              // garis mulai sejajar teks, bukan ikon: baris tetap terbaca
              // satu kelompok
              if (i > 0) const Divider(height: 1, indent: 68),
              m,
            ],
          ],
        ),
      ),
    ],
  );
}

class _Menu extends StatelessWidget {
  const _Menu({
    required this.icon,
    required this.label,
    required this.onTap,
    this.nilai,
  });
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  /// Nilai saat ini (mis. bahasa), menggantikan panah.
  final String? nilai;

  @override
  Widget build(BuildContext context) => ListTile(
    leading: Container(
      width: 36,
      height: 36,
      decoration: BoxDecoration(
        color: C.accent.withValues(alpha: .08),
        borderRadius: BorderRadius.circular(AppRadius.chip),
      ),
      child: Icon(icon, size: 20, color: C.accent),
    ),
    title: Text(label),
    trailing: nilai != null
        ? Text(nilai!, style: T.isiKecil.copyWith(color: C.lightInkMuted))
        : const Icon(Icons.chevron_right, color: C.lightInkMuted),
    onTap: onTap,
  );
}

class ProfilScreen extends StatelessWidget {
  const ProfilScreen({super.key, required this.kategori, required this.title});
  final String kategori, title;

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(title),
    body: kategori == 'sto'
        ? const _BaganOrganisasi()
        : LoadView(
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

/// Bagan struktur organisasi, sama dengan web (public/assets/img). Untuk
/// sementara gambar bawaan app: API profil 'sto' belum membawa isinya.
const _bagan = ['assets/img/struktur-1.png', 'assets/img/struktur-2.png'];

class _BaganOrganisasi extends StatelessWidget {
  const _BaganOrganisasi();

  @override
  Widget build(BuildContext context) => ListView(
    padding: padTengah(context),
    children: [
      for (final (i, path) in _bagan.indexed) ...[
        if (i > 0) const SizedBox(height: AppSpacing.lg),
        _Bagan(path, judul: context.l10n.orgChart(i + 1)),
      ],
    ],
  );
}

/// Satu bagan: tulisannya kecil di layar HP, jadi diketuk untuk dibuka
/// layar penuh dan dicubit untuk diperbesar.
class _Bagan extends StatelessWidget {
  const _Bagan(this.path, {required this.judul});
  final String path, judul;

  @override
  Widget build(BuildContext context) => Semantics(
    button: true,
    label: '$judul. ${context.l10n.tapToEnlarge}',
    excludeSemantics: true,
    child: Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: () => openAssetImage(context, path, tag: path),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Padding(
              padding: const EdgeInsets.all(AppSpacing.md),
              child: Hero(
                tag: path,
                child: Image.asset(path, fit: BoxFit.contain),
              ),
            ),
            const Divider(height: 1),
            Padding(
              padding: const EdgeInsets.symmetric(
                horizontal: AppSpacing.md,
                vertical: AppSpacing.sm,
              ),
              child: Row(
                children: [
                  const Icon(
                    Icons.account_tree_outlined,
                    size: 16,
                    color: C.accent,
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  Expanded(
                    child: Text(
                      judul,
                      style: T.isiKecil.copyWith(color: C.lightInkMuted),
                    ),
                  ),
                  const Icon(Icons.zoom_in, size: 20, color: C.lightInkMuted),
                ],
              ),
            ),
          ],
        ),
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
