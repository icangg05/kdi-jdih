import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';

const _jenisPengguna = [
  'Mahasiswa',
  'Akademisi',
  'Praktisi Hukum',
  'Masyarakat Umum',
  'Lainnya',
];

const _aspek = {
  'kemudahan_akses': 'Kemudahan Akses',
  'kelengkapan_informasi': 'Kelengkapan Informasi',
  'kecepatan_loading': 'Kecepatan Loading',
  'tampilan_antarmuka': 'Tampilan Antarmuka',
  'relevansi_pencarian': 'Relevansi Pencarian',
};

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
  final _rating = <String, int>{for (final k in _aspek.keys) k: 0};
  bool _bersedia = false, _sending = false;

  /// Galat isian baru ditampilkan setelah percobaan kirim pertama: kolom
  /// yang belum disentuh tidak dimarahi duluan.
  bool _dicoba = false;

  // jangkar gulir ke isian pertama yang bermasalah
  final _kNama = GlobalKey(), _kEmail = GlobalKey();
  final _kJenis = GlobalKey(), _kRating = GlobalKey();

  static final _polaEmail = RegExp(r'^[^@\s]+@[^@\s]+\.[^@\s]+$');

  String? get _galatNama =>
      _nama.text.trim().isEmpty ? 'Nama wajib diisi.' : null;

  String? get _galatEmail {
    final e = _email.text.trim();
    return e.isEmpty || _polaEmail.hasMatch(e)
        ? null
        : 'Format email belum benar, contoh: nama@contoh.go.id';
  }

  String? get _galatJenis =>
      _jenis == null ? 'Pilih salah satu jenis pengguna.' : null;

  String? get _galatRating => _rating.values.any((v) => v == 0)
      ? 'Beri nilai 1–5 untuk setiap aspek.'
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
          title: const Text('Terima kasih!'),
          content: const Text('Survei Anda telah terkirim.'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(c),
              child: const Text('OK'),
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
    appBar: const BrandAppBar('Survei Kepuasan'),
    // Column, bukan ListView: formulir pendek ini dibangun utuh, sehingga
    // isian bermasalah di atas tetap bisa dijangkau Scrollable.ensureVisible
    // saat tombol kirim di bawah ditekan
    body: SingleChildScrollView(
      padding: padTengah(context, maks: 640),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _field('Nama *', _nama, key: _kNama, galat: _galatNama),
          _field(
            'Email',
            _email,
            key: _kEmail,
            type: TextInputType.emailAddress,
            galat: _galatEmail,
          ),
          _field('Instansi', _instansi),
          Text('Jenis Pengguna *', key: _kJenis, style: T.labelBesar),
          RadioGroup<String>(
            groupValue: _jenis,
            onChanged: (v) => setState(() => _jenis = v),
            child: Column(
              children: [
                for (final j in _jenisPengguna)
                  RadioListTile<String>(
                    title: Text(j),
                    value: j,
                    contentPadding: EdgeInsets.zero,
                  ),
              ],
            ),
          ),
          _galat(_galatJenis),
          const SizedBox(height: 8),
          Text('Penilaian (1–5) *', key: _kRating, style: T.labelBesar),
          const SizedBox(height: 4),
          for (final e in _aspek.entries)
            _BarisNilai(
              label: e.value,
              nilai: _rating[e.key]!,
              tandai: _dicoba && _rating[e.key] == 0,
              onPilih: (i) => setState(() => _rating[e.key] = i),
            ),
          _galat(_galatRating),
          const SizedBox(height: 8),
          _field('Saran Perbaikan', _saran, lines: 3, maks: 1000),
          _field('Fitur yang Diharapkan', _fitur, lines: 3, maks: 1000),
          CheckboxListTile(
            title: const Text('Bersedia dihubungi'),
            value: _bersedia,
            contentPadding: EdgeInsets.zero,
            onChanged: (v) => setState(() => _bersedia = v ?? false),
          ),
          if (_bersedia)
            _field('Kontak (HP/WA)', _kontak, type: TextInputType.phone),
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
                : const Text('Kirim Survei'),
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
          nilai == 0 ? label : '$label · $nilai dari 5',
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
                tooltip: '$label, $i dari 5',
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
