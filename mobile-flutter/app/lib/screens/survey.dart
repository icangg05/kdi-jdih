import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';

const _jenisPengguna = [
  'Mahasiswa',
  'Akademisi',
  'Praktisi Hukum',
  'Masyarakat Umum',
  'Lainnya'
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

  Future<void> _submit() async {
    if (_nama.text.trim().isEmpty ||
        _jenis == null ||
        _rating.values.any((v) => v == 0)) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
          content: Text('Isi nama, jenis pengguna, dan semua rating.')));
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
        if (_saran.text.trim().isNotEmpty) 'saran_perbaikan': _saran.text.trim(),
        if (_fitur.text.trim().isNotEmpty) 'fitur_harapan': _fitur.text.trim(),
        'bersedia_dihubungi': _bersedia,
        if (_kontak.text.trim().isNotEmpty) 'kontak': _kontak.text.trim(),
      });
      if (!mounted) return;
      await showDialog(
        context: context,
        builder: (c) => AlertDialog(
          icon: const Icon(Icons.check_circle, color: Colors.green, size: 48),
          title: const Text('Terima kasih!'),
          content: const Text('Survei Anda telah terkirim.'),
          actions: [
            TextButton(onPressed: () => Navigator.pop(c), child: const Text('OK'))
          ],
        ),
      );
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text('$e')));
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  Widget _field(String label, TextEditingController c,
          {int lines = 1, TextInputType? type}) =>
      Padding(
        padding: const EdgeInsets.only(bottom: 12),
        child: TextField(
          controller: c,
          maxLines: lines,
          keyboardType: type,
          decoration: InputDecoration(labelText: label),
        ),
      );

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: const BrandAppBar('Survei Kepuasan'),
        body: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            _field('Nama *', _nama),
            _field('Email', _email, type: TextInputType.emailAddress),
            _field('Instansi', _instansi),
            const Text('Jenis Pengguna *',
                style: TextStyle(fontWeight: FontWeight.w600)),
            RadioGroup<String>(
              groupValue: _jenis,
              onChanged: (v) => setState(() => _jenis = v),
              child: Column(children: [
                for (final j in _jenisPengguna)
                  RadioListTile<String>(
                    title: Text(j),
                    value: j,
                    dense: true,
                    contentPadding: EdgeInsets.zero,
                  ),
              ]),
            ),
            const SizedBox(height: 8),
            const Text('Penilaian (1-5) *',
                style: TextStyle(fontWeight: FontWeight.w600)),
            const SizedBox(height: 8),
            for (final e in _aspek.entries)
              Padding(
                padding: const EdgeInsets.only(bottom: 4),
                child: Row(children: [
                  Expanded(child: Text(e.value)),
                  for (var i = 1; i <= 5; i++)
                    IconButton(
                      padding: EdgeInsets.zero,
                      constraints:
                          const BoxConstraints(minWidth: 36, minHeight: 36),
                      icon: Icon(
                        _rating[e.key]! >= i ? Icons.star : Icons.star_border,
                        color: C.primaryInk,
                      ),
                      onPressed: () => setState(() => _rating[e.key] = i),
                    ),
                ]),
              ),
            const SizedBox(height: 8),
            _field('Saran Perbaikan', _saran, lines: 3),
            _field('Fitur yang Diharapkan', _fitur, lines: 3),
            CheckboxListTile(
              title: const Text('Bersedia dihubungi'),
              value: _bersedia,
              contentPadding: EdgeInsets.zero,
              onChanged: (v) => setState(() => _bersedia = v ?? false),
            ),
            if (_bersedia) _field('Kontak (HP/WA)', _kontak),
            const SizedBox(height: 8),
            FilledButton(
              onPressed: _sending ? null : _submit,
              child: _sending
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(
                          strokeWidth: 2, color: C.ink))
                  : const Text('Kirim Survei'),
            ),
            const SizedBox(height: 24),
          ],
        ),
      );
}
