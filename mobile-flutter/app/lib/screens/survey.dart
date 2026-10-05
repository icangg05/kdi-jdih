import 'package:flutter/material.dart';
import 'package:flutter/services.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';

/// (nilai, label). Nilai dikirim ke server apa adanya, label diterjemahkan.
List<(String, String)> _jenisPengguna(AppLocalizations l) => [
  ('Mahasiswa', l.userStudent),
  ('Akademisi', l.userAcademic),
  ('Praktisi Hukum', l.userPractitioner),
  ('Masyarakat Umum', l.userPublic),
  ('Lainnya', l.userOther),
];

/// (kunci API, label) aspek penilaian.
List<(String, String)> _aspek(AppLocalizations l) => [
  ('kemudahan_akses', l.aspectAccess),
  ('kelengkapan_informasi', l.aspectCompleteness),
  ('kecepatan_loading', l.aspectSpeed),
  ('tampilan_antarmuka', l.aspectInterface),
  ('relevansi_pencarian', l.aspectRelevance),
];

/// Formulir survei: tiga bagian berpanel (tentang Anda, penilaian, masukan),
/// label di atas kolom, dan bilah kirim yang menempel di bawah beserta
/// hitungan isian wajib, supaya tujuan akhirnya selalu terlihat.
class SurveyScreen extends StatefulWidget {
  const SurveyScreen({super.key});

  @override
  State<SurveyScreen> createState() => _SurveyScreenState();
}

class _SurveyScreenState extends State<SurveyScreen> {
  final _nama = TextEditingController();
  final _email = TextEditingController();
  final _instansi = TextEditingController();
  final _saran = TextEditingController();
  final _fitur = TextEditingController();
  final _kontak = TextEditingController();
  String? _jenis;
  final _rating = <String, int>{for (final (k, _) in _aspek(l10nAktif)) k: 0};
  bool _bersedia = false, _sending = false;

  /// Galat isian baru ditampilkan setelah percobaan kirim pertama: kolom
  /// yang belum disentuh tidak dimarahi duluan.
  bool _dicoba = false;

  // jangkar gulir ke isian pertama yang bermasalah
  final _kNama = GlobalKey(), _kEmail = GlobalKey();
  final _kJenis = GlobalKey(), _kRating = GlobalKey();

  static final _polaEmail = RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');

  String? get _galatNama =>
      _nama.text.trim().isEmpty ? context.l10n.errNameRequired : null;

  String? get _galatEmail {
    final e = _email.text.trim();
    return e.isEmpty || _polaEmail.hasMatch(e)
        ? null
        : context.l10n.errEmailFormat;
  }

  String? get _galatJenis =>
      _jenis == null ? context.l10n.errPickUserType : null;

  String? get _galatRating =>
      _rating.values.any((v) => v == 0) ? context.l10n.errRateAll : null;

  /// Isian wajib yang sudah terisi, dari [_wajib]: nama, jenis, lima aspek.
  int get _terisi =>
      (_nama.text.trim().isEmpty ? 0 : 1) +
      (_jenis == null ? 0 : 1) +
      _rating.values.where((v) => v > 0).length;
  int get _wajib => 2 + _rating.length;

  Duration get _gerak => MediaQuery.of(context).disableAnimations
      ? Duration.zero
      : const Duration(milliseconds: 200);

  @override
  void dispose() {
    for (final c in [_nama, _email, _instansi, _saran, _fitur, _kontak]) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _submit() async {
    final pertama = [
      (_galatNama, _kNama),
      (_galatEmail, _kEmail),
      (_galatJenis, _kJenis),
      (_galatRating, _kRating),
    ].where((e) => e.$1 != null).firstOrNull;
    if (pertama != null) {
      setState(() => _dicoba = true);
      // isian yang bermasalah bisa berada jauh di atas tombol kirim
      final ctx = pertama.$2.currentContext;
      if (ctx != null) {
        Scrollable.ensureVisible(ctx, alignment: .1, duration: _gerak);
      }
      return;
    }
    FocusScope.of(context).unfocus();
    setState(() => _sending = true);
    try {
      await api.submitSurvey({
        'nama': _nama.text.trim(),
        if (_email.text.trim().isNotEmpty) 'email': _email.text.trim(),
        if (_instansi.text.trim().isNotEmpty) 'instansi': _instansi.text.trim(),
        'jenis_pengguna': _jenis,
        ..._rating,
        if (_saran.text.trim().isNotEmpty)
          'saran_perbaikan': _saran.text.trim(),
        if (_fitur.text.trim().isNotEmpty) 'fitur_harapan': _fitur.text.trim(),
        'bersedia_dihubungi': _bersedia,
        if (_bersedia && _kontak.text.trim().isNotEmpty)
          'kontak': _kontak.text.trim(),
      });
      if (!mounted) return;
      await showDialog(
        context: context,
        builder: (c) => AlertDialog(
          icon: const Icon(Icons.check_circle, color: C.statusActive, size: 48),
          title: Text(c.l10n.thankYou),
          content: Text(c.l10n.surveySent),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(c),
              child: Text(c.l10n.ok),
            ),
          ],
        ),
      );
      if (mounted) Navigator.pop(context);
    } catch (e) {
      // isian dipertahankan; pengguna cukup menekan kirim lagi
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text('$e')));
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  /// Kolom isian dengan label di atasnya. [maks] = batas kolom di server;
  /// penghitung baru muncul saat mendekat.
  Widget _kolom(
    String label,
    TextEditingController c, {
    Key? key,
    bool opsional = false,
    int baris = 1,
    int maks = 255,
    TextInputType? jenis,
    Iterable<String>? isiOtomatis,
    TextCapitalization kapital = TextCapitalization.none,
    String? galat,
  }) => Padding(
    key: key,
    padding: const EdgeInsets.only(bottom: AppSpacing.lg),
    // satu simpul aksesibilitas: label di atas dibacakan sebagai nama kolom
    child: MergeSemantics(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _Label(label, opsional: opsional),
          const SizedBox(height: 6),
          TextField(
            controller: c,
            minLines: baris,
            maxLines: baris == 1 ? 1 : baris + 3,
            maxLength: maks,
            keyboardType: baris > 1 ? TextInputType.multiline : jenis,
            textCapitalization: kapital,
            autofillHints: isiOtomatis,
            onChanged: (_) => setState(() {}),
            buildCounter:
                (_, {required currentLength, required isFocused, maxLength}) =>
                    currentLength > maks * .8
                    ? Text('$currentLength/$maks')
                    : null,
            decoration: InputDecoration(errorText: _dicoba ? galat : null),
          ),
        ],
      ),
    ),
  );

  Widget _galat(String? pesan) => AnimatedSize(
    duration: _gerak,
    alignment: Alignment.topLeft,
    child: !_dicoba || pesan == null
        ? const SizedBox(width: double.infinity)
        : Padding(
            padding: const EdgeInsets.only(top: AppSpacing.sm),
            // gaya sama dengan galat di bawah kolom isian
            child: Text(
              pesan,
              style: T.isiKecil.copyWith(
                color: Theme.of(context).colorScheme.error,
              ),
            ),
          ),
  );

  @override
  Widget build(BuildContext context) {
    final l = context.l10n;
    return Scaffold(
      appBar: BrandAppBar(l.satisfactionSurvey),
      // bilah kirim di bottomNavigationBar: tertutup papan ketik saat mengetik
      // (ruang layar untuk kolom), muncul lagi begitu papan ketik turun
      bottomNavigationBar: _BilahKirim(
        terisi: _terisi,
        wajib: _wajib,
        mengirim: _sending,
        gerak: _gerak,
        onKirim: _submit,
      ),
      // Column, bukan ListView: formulir pendek ini dibangun utuh, sehingga
      // isian bermasalah di atas tetap bisa dijangkau Scrollable.ensureVisible
      // saat tombol kirim di bawah ditekan
      body: SingleChildScrollView(
        padding: padTengah(context, maks: 640, atas: AppSpacing.xl),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(l.surveyHeading, style: T.judul),
            const SizedBox(height: AppSpacing.xs),
            Text(l.surveyIntro, style: T.isi.copyWith(color: C.lightInkMuted)),
            const SizedBox(height: AppSpacing.xl),
            _Bagian(
              ikon: Icons.person_outline,
              judul: l.surveyAbout,
              children: [
                _kolom(
                  l.fieldName,
                  _nama,
                  key: _kNama,
                  isiOtomatis: const [AutofillHints.name],
                  kapital: TextCapitalization.words,
                  galat: _galatNama,
                ),
                _kolom(
                  l.email,
                  _email,
                  key: _kEmail,
                  opsional: true,
                  jenis: TextInputType.emailAddress,
                  isiOtomatis: const [AutofillHints.email],
                  galat: _galatEmail,
                ),
                _kolom(
                  l.fieldInstitution,
                  _instansi,
                  opsional: true,
                  isiOtomatis: const [AutofillHints.organizationName],
                  kapital: TextCapitalization.words,
                ),
                _Label(
                  l.fieldUserType,
                  key: _kJenis,
                  galat: _dicoba && _jenis == null,
                ),
                const SizedBox(height: AppSpacing.sm),
                Wrap(
                  spacing: AppSpacing.sm,
                  runSpacing: AppSpacing.sm,
                  children: [
                    for (final (nilai, label) in _jenisPengguna(l))
                      ChoiceChip(
                        label: Text(label),
                        selected: _jenis == nilai,
                        onSelected: (_) => setState(() => _jenis = nilai),
                      ),
                  ],
                ),
                _galat(_galatJenis),
                const SizedBox(height: AppSpacing.lg),
              ],
            ),
            _Bagian(
              key: _kRating,
              ikon: Icons.star_outline,
              judul: l.fieldRating,
              // patokan skala sekali di kepala bagian, bukan di tiap aspek
              keterangan: '1 = ${l.ratingWord('1')}   5 = ${l.ratingWord('5')}',
              children: [
                for (final (k, label) in _aspek(l))
                  _BarisNilai(
                    label: label,
                    nilai: _rating[k]!,
                    tandai: _dicoba && _rating[k] == 0,
                    gerak: _gerak,
                    onPilih: (i) {
                      HapticFeedback.selectionClick();
                      setState(() => _rating[k] = i);
                    },
                  ),
                _galat(_galatRating),
                const SizedBox(height: AppSpacing.lg),
              ],
            ),
            _Bagian(
              ikon: Icons.chat_bubble_outline,
              judul: l.surveyFeedback,
              children: [
                _kolom(
                  l.fieldSuggestions,
                  _saran,
                  opsional: true,
                  baris: 3,
                  maks: 1000,
                  kapital: TextCapitalization.sentences,
                ),
                _kolom(
                  l.fieldFeatures,
                  _fitur,
                  opsional: true,
                  baris: 3,
                  maks: 1000,
                  kapital: TextCapitalization.sentences,
                ),
                SwitchListTile(
                  title: Text(l.fieldContactOk, style: T.isi),
                  value: _bersedia,
                  contentPadding: EdgeInsets.zero,
                  onChanged: (v) => setState(() => _bersedia = v),
                ),
                AnimatedSize(
                  duration: _gerak,
                  alignment: Alignment.topCenter,
                  child: _bersedia
                      ? Padding(
                          padding: const EdgeInsets.only(top: AppSpacing.sm),
                          child: _kolom(
                            l.fieldContact,
                            _kontak,
                            opsional: true,
                            jenis: TextInputType.phone,
                            isiOtomatis: const [AutofillHints.telephoneNumber],
                          ),
                        )
                      : const SizedBox(
                          width: double.infinity,
                          height: AppSpacing.sm,
                        ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

/// Label isian: tebal, dengan tanda "Opsional" redup untuk isian yang boleh
/// dilewati. Yang wajib tidak diberi bintang: yang dikecualikan lebih sedikit.
class _Label extends StatelessWidget {
  const _Label(
    this.teks, {
    super.key,
    this.opsional = false,
    this.galat = false,
  });
  final String teks;
  final bool opsional, galat;

  @override
  Widget build(BuildContext context) => Text.rich(
    TextSpan(
      text: teks,
      children: [
        if (opsional)
          TextSpan(
            text: '  ${context.l10n.optional}',
            style: T.labelKecil.copyWith(
              color: C.lightInkMuted,
              fontWeight: FontWeight.w400,
            ),
          ),
      ],
    ),
    style: T.labelBesar.copyWith(
      color: galat ? Theme.of(context).colorScheme.error : C.lightInk,
    ),
  );
}

/// Panel putih satu bagian formulir: ikon biru bertint + judul, lalu isinya.
class _Bagian extends StatelessWidget {
  const _Bagian({
    super.key,
    required this.ikon,
    required this.judul,
    this.keterangan,
    required this.children,
  });
  final IconData ikon;
  final String judul;
  final String? keterangan;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(bottom: AppSpacing.lg),
    // Material, bukan DecoratedBox: riak sentuh SwitchListTile digambar di
    // Material terdekat dan tertutup bila latar putihnya kotak dekorasi
    child: Material(
      color: C.lightSurface,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(AppRadius.card),
        side: const BorderSide(color: C.lightLine),
      ),
      child: Padding(
        padding: const EdgeInsets.fromLTRB(
          AppSpacing.lg,
          AppSpacing.lg,
          AppSpacing.lg,
          0,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Row(
              children: [
                Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: C.accent.withValues(alpha: .08),
                    borderRadius: BorderRadius.circular(AppRadius.chip),
                  ),
                  child: Icon(ikon, size: 18, color: C.accent),
                ),
                const SizedBox(width: AppSpacing.md),
                Expanded(
                  child: Semantics(
                    header: true,
                    child: Text(judul, style: T.subjudul),
                  ),
                ),
              ],
            ),
            if (keterangan != null) ...[
              const SizedBox(height: AppSpacing.sm),
              Text(
                keterangan!,
                style: T.isiKecil.copyWith(color: C.lightInkMuted),
              ),
            ],
            const SizedBox(height: AppSpacing.lg),
            ...children,
          ],
        ),
      ),
    ),
  );
}

/// Satu aspek penilaian: label (dan kata nilainya setelah dipilih), lalu
/// skala lima kotak bernomor 48dp. Kotak terpilih amber penuh, kotak di
/// bawahnya bertint, sehingga skala terbaca seperti takaran.
class _BarisNilai extends StatelessWidget {
  const _BarisNilai({
    required this.label,
    required this.nilai,
    required this.tandai,
    required this.gerak,
    required this.onPilih,
  });
  final String label;
  final int nilai;

  /// Belum dinilai saat pengguna mencoba mengirim.
  final bool tandai;
  final Duration gerak;
  final ValueChanged<int> onPilih;

  @override
  Widget build(BuildContext context) {
    final galat = Theme.of(context).colorScheme.error;
    return Padding(
      padding: const EdgeInsets.only(bottom: AppSpacing.lg),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Expanded(
                child: Text(
                  label,
                  style: T.isi.copyWith(
                    fontWeight: FontWeight.w600,
                    color: tandai ? galat : C.lightInk,
                  ),
                ),
              ),
              if (nilai > 0)
                Text(
                  context.l10n.ratingWord('$nilai'),
                  style: T.label.copyWith(color: C.primaryInk),
                ),
            ],
          ),
          const SizedBox(height: AppSpacing.sm),
          Row(
            children: [
              for (var i = 1; i <= 5; i++) ...[
                if (i > 1) const SizedBox(width: 6),
                Expanded(child: _kotak(context, i, galat)),
              ],
            ],
          ),
        ],
      ),
    );
  }

  Widget _kotak(BuildContext context, int i, Color galat) {
    final dipilih = nilai == i;
    return Semantics(
      // pembaca layar: "Kemudahan Akses, 3 dari 5", terpilih
      button: true,
      selected: dipilih,
      label: context.l10n.ratingTooltip(label, i),
      excludeSemantics: true,
      child: AnimatedContainer(
        duration: gerak,
        curve: Curves.easeOutCubic,
        height: 48,
        decoration: BoxDecoration(
          color: dipilih
              ? C.primary
              : i < nilai
              ? C.primary.withValues(alpha: .16)
              : C.lightSurface,
          borderRadius: BorderRadius.circular(AppRadius.button),
          border: Border.all(
            color: dipilih || i < nilai
                ? C.primary
                : tandai
                ? galat
                : C.lightLineField,
          ),
        ),
        child: Material(
          type: MaterialType.transparency,
          child: InkWell(
            borderRadius: BorderRadius.circular(AppRadius.button),
            onTap: () => onPilih(i),
            child: Center(
              child: AnimatedScale(
                duration: gerak,
                curve: Curves.easeOutBack,
                scale: dipilih ? 1.15 : 1,
                child: Text(
                  '$i',
                  style: T.labelBesar.copyWith(
                    // teks gelap di atas amber: 7,5:1
                    color: C.ink,
                    fontWeight: dipilih ? FontWeight.w700 : FontWeight.w600,
                    fontFeatures: const [FontFeature.tabularFigures()],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

/// Bilah bawah: hitungan isian wajib dengan takaran tipis, dan tombol kirim.
class _BilahKirim extends StatelessWidget {
  const _BilahKirim({
    required this.terisi,
    required this.wajib,
    required this.mengirim,
    required this.gerak,
    required this.onKirim,
  });
  final int terisi, wajib;
  final bool mengirim;
  final Duration gerak;
  final VoidCallback onKirim;

  @override
  Widget build(BuildContext context) {
    final lengkap = terisi == wajib;
    return DecoratedBox(
      decoration: const BoxDecoration(
        color: C.lightSurface,
        border: Border(top: BorderSide(color: C.lightLine)),
      ),
      child: SafeArea(
        top: false,
        child: Padding(
          padding: const EdgeInsets.symmetric(
            horizontal: AppSpacing.lg,
            vertical: AppSpacing.md,
          ),
          child: Row(
            children: [
              Expanded(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        if (lengkap) ...[
                          const Icon(
                            Icons.check_circle,
                            size: 16,
                            color: C.statusActive,
                          ),
                          const SizedBox(width: 4),
                        ],
                        Flexible(
                          child: Text(
                            context.l10n.surveyProgress(terisi, wajib),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: T.label.copyWith(
                              color: lengkap ? C.statusActive : C.lightInkMuted,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    ClipRRect(
                      borderRadius: BorderRadius.circular(2),
                      child: TweenAnimationBuilder<double>(
                        tween: Tween(end: terisi / wajib),
                        duration: gerak,
                        curve: Curves.easeOutCubic,
                        builder: (_, v, _) => LinearProgressIndicator(
                          value: v,
                          minHeight: 4,
                          color: lengkap ? C.statusActive : C.primary,
                          backgroundColor: C.lightLine,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: AppSpacing.lg),
              FilledButton.icon(
                // nonaktif selama mengirim: ketukan beruntun tidak menggandakan
                onPressed: mengirim ? null : onKirim,
                icon: mengirim
                    ? const SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: C.ink,
                        ),
                      )
                    : const Icon(Icons.send, size: 18),
                label: Text(context.l10n.submitSurvey),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
