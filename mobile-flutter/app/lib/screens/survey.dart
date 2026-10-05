import 'package:flutter/material.dart';

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
  final _rating = <String, int>{
    for (final (k, _) in _aspek(l10nAktif)) k: 0,
  };
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

  String? get _galatRating => _rating.values.any((v) => v == 0)
      ? context.l10n.errRateAll
      : null;

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
        Scrollable.ensureVisible(
          ctx,
          alignment: .1,
          duration: MediaQuery.of(context).disableAnimations
              ? Duration.zero
              : const Duration(milliseconds: 250),
        );
      }
      return;
    }
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

  /// [maks] = batas kolom di server; penghitung baru muncul saat mendekat.
  Widget _field(
    String label,
    TextEditingController c, {
    Key? key,
    int lines = 1,
    int maks = 255,
    TextInputType? type,
    String? galat,
  }) => Padding(
    key: key,
    padding: const EdgeInsets.only(bottom: 12),
    child: TextField(
      controller: c,
      maxLines: lines,
      maxLength: maks,
      keyboardType: type,
      onChanged: _dicoba ? (_) => setState(() {}) : null,
      buildCounter: (
        _, {
        required currentLength,
        required isFocused,
        maxLength,
      }) => currentLength > maks * .8 ? Text('$currentLength/$maks') : null,
      decoration: InputDecoration(
        labelText: label,
        errorText: _dicoba ? galat : null,
      ),
    ),
  );

  Widget _galat(String? pesan) => !_dicoba || pesan == null
      ? const SizedBox.shrink()
      : Padding(
          padding: const EdgeInsets.only(top: 4, bottom: 8),
          child: Text(
            pesan,
            style: T.isiKecil.copyWith(
              color: Theme.of(context).colorScheme.error,
            ),
          ),
        );

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.satisfactionSurvey),
    // Column, bukan ListView: formulir pendek ini dibangun utuh, sehingga
    // isian bermasalah di atas tetap bisa dijangkau Scrollable.ensureVisible
    // saat tombol kirim di bawah ditekan
    body: SingleChildScrollView(
      padding: padTengah(context, maks: 640),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _field(
            context.l10n.fieldName,
            _nama,
            key: _kNama,
            galat: _galatNama,
          ),
          _field(
            context.l10n.email,
            _email,
            key: _kEmail,
            type: TextInputType.emailAddress,
            galat: _galatEmail,
          ),
          _field(context.l10n.fieldInstitution, _instansi),
          Text(
            context.l10n.fieldUserType,
            key: _kJenis,
            style: T.labelBesar,
          ),
          RadioGroup<String>(
            groupValue: _jenis,
            onChanged: (v) => setState(() => _jenis = v),
            child: Column(
              children: [
                for (final (nilai, label) in _jenisPengguna(context.l10n))
                  RadioListTile<String>(
                    title: Text(label),
                    value: nilai,
                    contentPadding: EdgeInsets.zero,
                  ),
              ],
            ),
          ),
          _galat(_galatJenis),
          const SizedBox(height: 8),
          Text(context.l10n.fieldRating, key: _kRating, style: T.labelBesar),
          const SizedBox(height: 4),
          for (final (k, label) in _aspek(context.l10n))
            _BarisNilai(
              label: label,
              nilai: _rating[k]!,
              tandai: _dicoba && _rating[k] == 0,
              onPilih: (i) => setState(() => _rating[k] = i),
            ),
          _galat(_galatRating),
          const SizedBox(height: 8),
          _field(
            context.l10n.fieldSuggestions,
            _saran,
            lines: 3,
            maks: 1000,
          ),
          _field(context.l10n.fieldFeatures, _fitur, lines: 3, maks: 1000),
          CheckboxListTile(
            title: Text(context.l10n.fieldContactOk),
            value: _bersedia,
            contentPadding: EdgeInsets.zero,
            onChanged: (v) => setState(() => _bersedia = v ?? false),
          ),
          if (_bersedia)
            _field(
              context.l10n.fieldContact,
              _kontak,
              type: TextInputType.phone,
            ),
          const SizedBox(height: 8),
          FilledButton(
            // nonaktif selama mengirim: ketukan beruntun tidak menggandakan
            onPressed: _sending ? null : _submit,
            child: _sending
                ? const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      color: C.ink,
                    ),
                  )
                : Text(context.l10n.submitSurvey),
          ),
          const SizedBox(height: 24),
        ],
      ),
    ),
  );
}

/// Satu aspek penilaian: label di barisnya sendiri (label panjang dan huruf
/// besar tidak berebut tempat dengan bintang), lalu lima bintang 48dp.
class _BarisNilai extends StatelessWidget {
  const _BarisNilai({
    required this.label,
    required this.nilai,
    required this.tandai,
    required this.onPilih,
  });
  final String label;
  final int nilai;

  /// Belum dinilai saat pengguna mencoba mengirim.
  final bool tandai;
  final ValueChanged<int> onPilih;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(top: 8),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          nilai == 0 ? label : context.l10n.ratingOf(label, nilai),
          style: TextStyle(
            color: tandai ? Theme.of(context).colorScheme.error : null,
            fontWeight: tandai ? FontWeight.w600 : null,
          ),
        ),
        Row(
          children: [
            for (var i = 1; i <= 5; i++)
              IconButton(
                // pembaca layar: "Kemudahan Akses, 3 dari 5", terpilih
                tooltip: context.l10n.ratingTooltip(label, i),
                isSelected: nilai == i,
                icon: Icon(
                  nilai >= i ? Icons.star : Icons.star_border,
                  color: C.primaryInk,
                ),
                onPressed: () => onPilih(i),
              ),
          ],
        ),
      ],
    ),
  );
}
