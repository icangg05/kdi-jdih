import 'dart:convert';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';

enum _Mode { teks, ai }

/// Pintu Tanya AI dari mana pun: navigasi bawah, rail, dan tombol-tombol di
/// beranda. Satu rute (naik dari bawah menutupi layar, turun lagi saat
/// ditutup) dan satu percakapan ([percakapanAi]) untuk semuanya.
/// [pertanyaan] langsung dikirim.
void bukaTanyaAi(BuildContext context, {String pertanyaan = ''}) {
  // ketukan ganda tidak menumpuk dua lembar AI
  if (ModalRoute.isCurrentOf(context) == false) return;
  Navigator.push(
    context,
    ruteNaik(
      SearchScreen(showBack: true, initialAi: true, initialQuery: pertanyaan),
    ),
  );
}

class SearchScreen extends StatefulWidget {
  const SearchScreen({
    super.key,
    this.initialAi = false,
    this.showBack = false,
    this.initialQuery = '',
  });
  final bool initialAi;

  /// Kata kunci yang sudah diketik di layar sebelumnya (mis. kolom cari beranda).
  final String initialQuery;

  /// true bila dibuka via push.
  final bool showBack;

  @override
  State<SearchScreen> createState() => _SearchScreenState();
}

class _SearchScreenState extends State<SearchScreen> {
  late _Mode _mode = widget.initialAi ? _Mode.ai : _Mode.teks;

  /// Percakapan tetap hidup saat berpindah mode; pertanyaan dari pencarian
  /// teks yang buntu masuk sebagai giliran baru, bukan mereset percakapan.
  final _chat = GlobalKey<_AiChatState>();

  void _tanyaAi(String q) {
    setState(() => _mode = _Mode.ai);
    _chat.currentState?.ask(q);
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: BrandAppBar(context.l10n.smartSearch, showBack: widget.showBack),
    body: Column(
      children: [
        _ModeSwitch(mode: _mode, onChanged: (m) => setState(() => _mode = m)),
        // kedua mode tetap hidup agar hasil tidak hilang saat berpindah
        Expanded(
          child: IndexedStack(
            index: _mode.index,
            children: [
              _DocSearchTab(query: widget.initialQuery, onTanyaAi: _tanyaAi),
              _AiChat(
                key: _chat,
                query: widget.initialAi ? widget.initialQuery : '',
              ),
            ],
          ),
        ),
      ],
    ),
  );
}

/// Pemilih mode: dua segmen dalam satu kotak, penanda aktif bergeser mulus.
/// Menggantikan SegmentedButton bawaan yang terbaca seperti tombol sistem.
class _ModeSwitch extends StatelessWidget {
  const _ModeSwitch({required this.mode, required this.onChanged});
  final _Mode mode;
  final ValueChanged<_Mode> onChanged;

  @override
  Widget build(BuildContext context) => Padding(
    // sejajar kolom percakapan di layar lebar
    padding: padTengah(context, atas: AppSpacing.md, bawah: AppSpacing.md),
    child: Container(
      // segmen 48dp (target sentuh minimum) + bingkai 2dp
      height: 52,
      padding: const EdgeInsets.all(2),
      decoration: BoxDecoration(
        color: C.lightSubtle,
        borderRadius: BorderRadius.circular(AppRadius.card),
        border: Border.all(color: C.lightLine),
      ),
      child: LayoutBuilder(
        builder: (context, c) {
          final w = (c.maxWidth - 4) / 2;
          return Stack(
            children: [
              AnimatedAlign(
                alignment: mode == _Mode.teks
                    ? Alignment.centerLeft
                    : Alignment.centerRight,
                duration: const Duration(milliseconds: 220),
                curve: Curves.easeOutCubic,
                // Segmen terpilih = permukaan putih yang terangkat tipis di
                // atas jalur slate, bukan blok oranye: oranye di layar ini
                // disisakan untuk tombol kirim. Sudut dalam 2dp = 4dp kotak
                // luar dikurangi celah 2dp, supaya lengkungnya sejajar.
                child: Container(
                  width: w,
                  height: 48,
                  decoration: BoxDecoration(
                    color: C.lightSurface,
                    borderRadius: BorderRadius.circular(2),
                    border: Border.all(color: C.lightLine),
                    boxShadow: [
                      BoxShadow(
                        color: C.ink.withValues(alpha: .06),
                        blurRadius: 3,
                        offset: const Offset(0, 1),
                      ),
                    ],
                  ),
                ),
              ),
              Row(
                children: [
                  _pil(w, _Mode.teks, Icons.search, context.l10n.textSearch),
                  _pil(w, _Mode.ai, Icons.auto_awesome, context.l10n.navAskAi),
                ],
              ),
            ],
          );
        },
      ),
    ),
  );

  Widget _pil(double w, _Mode m, IconData icon, String label) {
    final on = m == mode;
    return SizedBox(
      width: w,
      height: 48,
      child: Semantics(
        selected: on,
        button: true,
        child: InkWell(
          borderRadius: BorderRadius.circular(2),
          onTap: () => onChanged(m),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 18, color: on ? C.primaryInk : C.lightInkMuted),
              const SizedBox(width: 6),
              Flexible(
                child: Text(
                  label,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: T.labelBesar.copyWith(
                    fontWeight: on ? FontWeight.w700 : FontWeight.w500,
                    color: on ? C.ink : C.lightInkMuted,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

/// Kata kunci contoh: satu ketukan untuk mencoba pencarian, jauh lebih ramah
/// daripada halaman kosong yang menyuruh "ketik kata kunci". Tidak
/// diterjemahkan: pencarian teks mencocokkan judul berbahasa Indonesia.
const _contohKata = ['Retribusi', 'Pajak Daerah', 'Perwali 2024', 'APBD'];

class _DocSearchTab extends StatefulWidget {
  const _DocSearchTab({this.query = '', required this.onTanyaAi});
  final String query;

  /// Jalan keluar saat pencarian teks buntu: lempar kata kuncinya ke tab AI.
  final ValueChanged<String> onTanyaAi;

  @override
  State<_DocSearchTab> createState() => _DocSearchTabState();
}

class _DocSearchTabState extends State<_DocSearchTab> {
  late String _q = widget.query.trim();
  String _category = 'peraturan';
  late final _ctrl = TextEditingController(text: _q);
  int? _total;

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  void _cari(String v) => setState(() {
    _q = v.trim();
    _total = null;
  });

  @override
  // jendela lebar: hasil + detail dokumen berdampingan
  Widget build(BuildContext context) => DaftarDetail(
    daftar: Column(
      children: [
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: AppSpacing.lg),
          child: TextField(
            controller: _ctrl,
            decoration: InputDecoration(
              hintText: context.l10n.searchDocsHint,
              prefixIcon: const Icon(Icons.search),
              suffixIcon: _q.isEmpty
                  ? null
                  : IconButton(
                      icon: const Icon(Icons.close),
                      tooltip: context.l10n.clearSearch,
                      onPressed: () {
                        _ctrl.clear();
                        _cari('');
                      },
                    ),
            ),
            textInputAction: TextInputAction.search,
            onSubmitted: _cari,
          ),
        ),
        Padding(
          padding: const EdgeInsets.fromLTRB(
            AppSpacing.lg,
            AppSpacing.md,
            AppSpacing.lg,
            0,
          ),
          child: FilterPills(
            labels: [
              for (final c in docCategories)
                docCategoryText(context, c.slug).short,
            ],
            selected: docCategories.indexWhere((c) => c.slug == _category),
            onSelected: (i) => setState(() {
              _category = docCategories[i].slug;
              _total = null;
            }),
          ),
        ),
        if (_q.isNotEmpty && _total != null)
          Padding(
            padding: const EdgeInsets.fromLTRB(
              AppSpacing.lg,
              AppSpacing.sm,
              AppSpacing.lg,
              0,
            ),
            child: Align(
              alignment: Alignment.centerLeft,
              child: Text(
                context.l10n.resultsFor(_total!, _q),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: T.labelKecil.copyWith(color: C.lightInkMuted),
              ),
            ),
          ),
        Expanded(
          child: _q.isEmpty
              ? _mulai()
              : PagedListView(
                  key: ValueKey('$_category|$_q'),
                  fetch: (page) =>
                      api.documents(category: _category, q: _q, page: page),
                  onTotal: (t) => setState(() => _total = t),
                  emptyView: NoResults(
                    _q,
                    saran: context.l10n.noMatchHintAi,
                    actions: [
                      OutlinedButton.icon(
                        onPressed: () {
                          _ctrl.clear();
                          _cari('');
                        },
                        icon: const Icon(Icons.close, size: 18),
                        label: Text(context.l10n.clearSearch),
                      ),
                      FilledButton.icon(
                        onPressed: () => widget.onTanyaAi(_q),
                        icon: const Icon(Icons.auto_awesome, size: 18),
                        label: Text(context.l10n.navAskAi),
                      ),
                    ],
                  ),
                  itemBuilder: (_, d, _) => DocumentCard(d),
                ),
        ),
      ],
    ),
  );

  /// Layar awal: penjelasan singkat + kata kunci contoh yang bisa diketuk.
  Widget _mulai() => ListView(
    padding: padTengah(context, maks: 560, atas: AppSpacing.xxl),
    children: [
      Center(
        child: Container(
          width: 72,
          height: 72,
          decoration: BoxDecoration(
            color: C.accent.withValues(alpha: .10),
            borderRadius: BorderRadius.circular(AppRadius.card),
          ),
          child: const Icon(Icons.search, color: C.accent, size: 32),
        ),
      ),
      const SizedBox(height: AppSpacing.lg),
      Text(
        context.l10n.searchLegalProducts,
        textAlign: TextAlign.center,
        style: T.judul,
      ),
      const SizedBox(height: AppSpacing.sm),
      Text(
        context.l10n.searchIntro,
        textAlign: TextAlign.center,
        style: T.isiKecil.copyWith(color: C.lightInkMuted),
      ),
      const SizedBox(height: AppSpacing.xl),
      // animasi sama dengan saran Tanya AI; kata kunci melesat ke atas,
      // ke arah kolom cari yang akan diisinya
      _Saran(
        items: [for (final k in _contohKata) (label: k, tanya: k)],
        rata: WrapAlignment.center,
        arah: const Offset(0, -96),
        onPick: (k) {
          _ctrl.text = k;
          _cari(k);
        },
      ),
    ],
  );
}

/// Pertanyaan per percakapan, sama dengan web: server hanya mengingat
/// beberapa giliran terakhir, jadi percakapan yang lebih panjang terasa
/// nyambung padahal awalnya sudah terlupakan. Sekaligus batas riwayat yang
/// disimpan di perangkat: pertanyaan berikutnya memulai percakapan baru.
const _maxTurns = 10;

/// Kunci SharedPreferences riwayat Tanya AI.
const _kunciRiwayat = 'tanya_ai';

/// Giliran yang ikut dikirim ke server (server memotong lagi).
const _maxHistory = 12;

/// Panjang pertanyaan maksimum (validasi `query` di AiSearchController).
const _maxPanjang = 500;

/// Saran pertanyaan: label pendek di chip (beberapa muat sebaris), kalimat
/// lengkapnya yang dikirim ke AI.
List<({String label, String tanya})> _contohPertanyaan(AppLocalizations l) => [
  (label: l.aiSuggest1, tanya: l.aiSuggest1Ask),
  (label: l.aiSuggest2, tanya: l.aiSuggest2Ask),
  (label: l.aiSuggest3, tanya: l.aiSuggest3Ask),
  (label: l.aiSuggest4, tanya: l.aiSuggest4Ask),
  (label: l.aiSuggest5, tanya: l.aiSuggest5Ask),
  (label: l.aiSuggest6, tanya: l.aiSuggest6Ask),
];

/// Satu pesan percakapan. Mutable: balasan AI diisi setelah respons tiba.
class _Msg {
  _Msg(this.ai, [this.text = '']);
  final bool ai;
  String text;
  List<Json> docs = const [];
  bool pending = false;

  /// Pertanyaan yang gagal dijawab, untuk tombol kirim ulang.
  String? failed;

  /// Animasi ketik sudah selesai: jangan diulang saat pesan dibangun ulang
  /// (mis. tergulir keluar layar lalu kembali).
  bool typed = false;

  /// Animasi masuk gelembung pengguna sudah diputar.
  bool muncul = false;
}

/// Percakapan Tanya AI untuk seluruh app.
final percakapanAi = PercakapanAi();

/// Satu percakapan untuk semua pintu Tanya AI (lihat [bukaTanyaAi]): dulu
/// tiap pintu punya percakapannya sendiri, sehingga riwayatnya tampak
/// berbeda tergantung tombol yang diketuk. Hidup di luar layar, jadi jawaban
/// yang tiba setelah layar ditutup tetap tercatat. Giliran yang sudah
/// dijawab disimpan di perangkat setelah [muat].
class PercakapanAi extends ChangeNotifier {
  SharedPreferences? _prefs;
  final _msgs = <_Msg>[];
  final _history = <Json>[];
  bool _busy = false;

  /// Naik tiap percakapan baru; jawaban percakapan lama yang tiba terlambat
  /// dibuang alih-alih masuk ke riwayat yang baru.
  int _sesi = 0;

  /// Jawaban sedang ditunggu dari server.
  bool get sibuk => _busy;

  /// Pertanyaan yang sudah dijawab di percakapan ini.
  int get jumlahPertanyaan => _history.where((h) => h['role'] == 'user').length;

  /// Batas tercapai: pertanyaan berikutnya memulai percakapan baru.
  bool get penuh => jumlahPertanyaan >= _maxTurns;

  /// Pulihkan riwayat dari perangkat, lalu simpan setiap perubahannya. Tanpa
  /// ini (mis. di tes) percakapan hanya hidup di memori.
  void muat(SharedPreferences prefs) {
    _prefs = prefs;
    _kosongkan();
    try {
      final giliran = jsonDecode(prefs.getString(_kunciRiwayat) ?? '[]');
      // melewati batas = bukan riwayat yang ditulis app ini: mulai bersih
      if (giliran is! List || giliran.length > _maxTurns) {
        throw const FormatException();
      }
      for (final g in giliran.cast<Map>()) {
        final q = g['q'] as String, a = g['a'] as String;
        _msgs
          ..add(_Msg(false, q)..muncul = true)
          ..add(
            _Msg(true, a)
              ..typed = true
              ..docs = [
                for (final d in g['docs'] as List? ?? const [])
                  if (d is Map) Map<String, dynamic>.from(d),
              ],
          );
        _catat(q, a);
      }
    } catch (_) {
      _kosongkan();
      prefs.remove(_kunciRiwayat);
    }
    notifyListeners();
  }

  void _catat(String q, String a) => _history
    ..add({'role': 'user', 'text': q})
    // server menolak teks riwayat > 4000 karakter; jangan sampai satu
    // jawaban panjang mematahkan seluruh percakapan
    ..add({'role': 'ai', 'text': a.length > 4000 ? a.substring(0, 4000) : a});

  /// Hanya giliran yang sudah dijawab; yang gagal atau masih ditunggu cukup
  /// hidup di memori.
  void _simpan() {
    final prefs = _prefs;
    if (prefs == null) return;
    final giliran = [
      for (var i = 0; i + 1 < _msgs.length; i += 2)
        if (!_msgs[i + 1].pending && _msgs[i + 1].failed == null)
          {
            'q': _msgs[i].text,
            'a': _msgs[i + 1].text,
            'docs': _msgs[i + 1].docs,
          },
    ];
    giliran.isEmpty
        ? prefs.remove(_kunciRiwayat)
        : prefs.setString(_kunciRiwayat, jsonEncode(giliran));
  }

  void _kosongkan() {
    _sesi++;
    _busy = false;
    _msgs.clear();
    _history.clear();
  }

  /// Kirim [q]; [tanpaJawaban] tampil bila server tidak memberi penjelasan.
  Future<void> tanya(String q, {required String tanpaJawaban}) async {
    if (q.isEmpty || _busy) return;
    if (penuh) _kosongkan();
    final sesi = _sesi;
    final reply = _Msg(true)..pending = true;
    final history = _history.length > _maxHistory
        ? _history.sublist(_history.length - _maxHistory)
        : List.of(_history);
    _busy = true;
    _msgs
      ..add(_Msg(false, q))
      ..add(reply);
    notifyListeners();
    try {
      final r = await api.aiSearch(q, history: history);
      if (sesi != _sesi) return;
      reply.text = r.sn('explanation') ?? tanpaJawaban;
      reply.docs = r.l('documents');
      _catat(q, reply.text);
    } catch (e) {
      if (sesi != _sesi) return;
      reply
        ..text = '$e'
        ..failed = q;
    }
    reply.pending = false;
    _busy = false;
    _simpan();
    notifyListeners();
  }

  /// Mulai percakapan baru.
  void reset() {
    _kosongkan();
    _simpan();
    notifyListeners();
  }

  /// [reset] yang bisa diurungkan: fungsi yang dikembalikan memulihkan
  /// percakapan, selama belum ada pertanyaan baru.
  VoidCallback bersihkan() {
    final msgs = List.of(_msgs), history = List.of(_history);
    reset();
    return () {
      if (_msgs.isNotEmpty) return;
      _msgs.addAll(msgs);
      _history.addAll(history);
      _simpan();
      notifyListeners();
    };
  }
}

/// Tanya AI mode percakapan, mekanismenya sama dengan modal web: riwayat
/// dikirim ulang tiap giliran, jawaban "diketik", lalu dokumen yang relevan
/// dilampirkan di bawahnya. Isinya milik [percakapanAi].
class _AiChat extends StatefulWidget {
  const _AiChat({super.key, this.query = ''});

  /// Pertanyaan bawaan dari beranda: langsung dijawab.
  final String query;

  @override
  State<_AiChat> createState() => _AiChatState();
}

class _AiChatState extends State<_AiChat> with WidgetsBindingObserver {
  final _ctrl = TextEditingController();
  final _scroll = ScrollController();
  final _p = percakapanAi;

  List<_Msg> get _msgs => _p._msgs;

  /// Jawaban belum utuh: masih ditunggu dari server ATAU masih diketik.
  /// Bagi pembaca keduanya sama-sama "sedang diproses".
  bool get _proses =>
      _p.sibuk || _msgs.any((m) => m.ai && !m.typed && m.failed == null);

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _p.addListener(_berubah);
    final q = widget.query.trim();
    if (q.isNotEmpty) {
      WidgetsBinding.instance.addPostFrameCallback((_) => ask(q));
    } else if (_msgs.isNotEmpty) {
      _bukaDiDasar();
    }
  }

  @override
  void dispose() {
    _p.removeListener(_berubah);
    WidgetsBinding.instance.removeObserver(this);
    _ctrl.dispose();
    _scroll.dispose();
    super.dispose();
  }

  void _berubah() {
    final ikut = _diDasar;
    setState(() {});
    if (ikut) _keDasar();
  }

  // Daftar TIDAK dibalik: isi yang tumbuh (lampiran dibuka) memanjang ke
  // bawah dan posisi layar tetap. Menempel ke dasar dilakukan manual, hanya
  // untuk pertumbuhan yang memang milik percakapan: jawaban yang sedang
  // diketik dan papan ketik yang muncul.

  /// Dicek SEBELUM tata letak berubah: pembaca yang sedang menggulir ke atas
  /// tidak ditarik paksa ke bawah.
  bool get _diDasar => !_scroll.hasClients || _scroll.position.extentAfter < 64;

  void _keDasar({bool animasi = false}) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!_scroll.hasClients) return;
      final p = _scroll.position;
      animasi
          ? p.animateTo(
              p.maxScrollExtent,
              duration: const Duration(milliseconds: 250),
              curve: Curves.easeOutCubic,
            )
          : p.jumpTo(p.maxScrollExtent);
    });
  }

  void _ikuti() {
    if (_diDasar) _keDasar();
  }

  /// Percakapan yang dilanjutkan dibuka di pesan terakhirnya. Tinggi pesan
  /// yang belum dilukis hanya taksiran, jadi lompatan diulang sampai benar
  /// di dasar.
  void _bukaDiDasar([int sisa = 5]) {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!_scroll.hasClients) return;
      final p = _scroll.position;
      if (p.extentAfter == 0 || sisa == 0) return;
      p.jumpTo(p.maxScrollExtent);
      _bukaDiDasar(sisa - 1);
    });
  }

  /// Papan ketik memperpendek layar dari bawah; tanpa ini pesan terakhir
  /// tertutup begitu kolom pertanyaan disentuh.
  @override
  void didChangeMetrics() => _ikuti();

  void ask([String? preset]) {
    final q = (preset ?? _ctrl.text).trim();
    if (q.isEmpty || !mounted) return;
    // jawaban sebelumnya masih ditunggu: pertanyaan dari layar lain tidak
    // hilang, ia menunggu di kolom isian
    if (_p.sibuk) {
      if (preset != null) _ctrl.text = q;
      return;
    }
    FocusScope.of(context).unfocus();
    _ctrl.clear();
    _p.tanya(q, tanpaJawaban: context.l10n.aiNoAnswer);
    _keDasar(animasi: true);
  }

  /// Bersihkan percakapan, dengan jalan pulang: satu ketukan tak sengaja
  /// tidak boleh menghapus seluruh tanya-jawab tanpa bisa dikembalikan.
  /// Hanya bisa saat jawaban terakhir sudah utuh (lihat [_proses]), jadi
  /// tidak ada balasan tertunda yang ikut tersimpan.
  void _bersihkan() {
    final pulihkan = _p.bersihkan();
    _ctrl.clear();
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: Text(context.l10n.chatCleared),
          action: SnackBarAction(
            label: context.l10n.undo,
            onPressed: () {
              pulihkan();
              if (mounted) _keDasar();
            },
          ),
        ),
      );
  }

  @override
  Widget build(BuildContext context) {
    final items = <(Key, Widget)>[
      (const ValueKey('sapaan'), _Sapaan(onPick: _msgs.isEmpty ? ask : null)),
      for (final m in _msgs)
        (
          ObjectKey(m),
          m.ai
              ? _AiBubble(
                  m,
                  onRetry: ask,
                  onTumbuh: _ikuti,
                  onSelesai: () {
                    if (mounted) setState(() {});
                  },
                )
              : _Masuk(m, child: _UserBubble(m.text)),
        ),
      if (_p.penuh && !_p.sibuk)
        (const ValueKey('batas'), _Batas(onReset: _p.reset)),
    ];
    return Column(
      children: [
        Expanded(
          // kunci per pesan menjaga state animasi ketik saat daftar tumbuh
          // tablet: kolom percakapan ±720dp di tengah, gulir tetap selebar layar
          child: ListView(
            controller: _scroll,
            padding: padTengah(context, atas: AppSpacing.sm),
            children: [
              for (final (key, w) in items)
                Padding(
                  key: key,
                  padding: const EdgeInsets.only(top: AppSpacing.md),
                  child: w,
                ),
            ],
          ),
        ),
        _composer(),
      ],
    );
  }

  /// Komposer menetap di bawah: idiom percakapan, mudah dijangkau ibu jari.
  Widget _composer() => Material(
    color: C.lightSurface,
    child: SafeArea(
      top: false,
      child: Container(
        decoration: const BoxDecoration(
          border: Border(top: BorderSide(color: C.lightLine)),
        ),
        padding: const EdgeInsets.fromLTRB(
          AppSpacing.md,
          AppSpacing.sm,
          AppSpacing.md,
          AppSpacing.sm,
        ),
        // kolom isian sejajar kolom percakapan di atasnya
        child: Kolom(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                children: [
                  // sapu, bukan ikon plus: tombol ini menghapus, bukan menambah
                  if (_msgs.isNotEmpty)
                    IconButton(
                      tooltip: context.l10n.clearChat,
                      icon: const Icon(Icons.cleaning_services_outlined),
                      // mati sampai jawaban terakhir utuh: membersihkan di
                      // tengah ketikan membuang jawaban yang belum terbaca
                      onPressed: _proses ? null : _bersihkan,
                    ),
                  Expanded(
                    child: TextField(
                      controller: _ctrl,
                      minLines: 1,
                      maxLines: 4,
                      // batas server 500 karakter; penghitung baru muncul saat
                      // mendekat, supaya pertanyaan yang ditempel tidak
                      // terpotong diam-diam
                      maxLength: _maxPanjang,
                      buildCounter:
                          (
                            _, {
                            required currentLength,
                            required isFocused,
                            maxLength,
                          }) => currentLength > _maxPanjang - 50
                          ? Text('$currentLength/$_maxPanjang')
                          : null,
                      textInputAction: TextInputAction.send,
                      decoration: InputDecoration(
                        hintText: _p.penuh
                            ? context.l10n.chatLimitReached
                            : context.l10n.askHint,
                      ),
                      onSubmitted: (_) => ask(),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  IconButton.filled(
                    tooltip: context.l10n.send,
                    style: IconButton.styleFrom(
                      minimumSize: const Size(48, 48),
                    ),
                    icon: const Icon(Icons.send, size: 20),
                    onPressed: _p.sibuk ? null : ask,
                  ),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                context.l10n.aiDisclaimer,
                textAlign: TextAlign.center,
                style: T.isiKecil.copyWith(color: C.lightInkMuted),
              ),
            ],
          ),
        ),
      ),
    ),
  );
}

/// Kartu jawaban AI: putih, tepi kiri oranye, label penutur di atas.
class _KartuAi extends StatelessWidget {
  const _KartuAi({required this.child});
  final Widget child;

  @override
  // Kartu polos bertepi tipis. Penutur ditandai label berikon bintang, bukan
  // garis oranye tebal di tepi kiri yang membuat setiap jawaban berteriak.
  Widget build(BuildContext context) => Container(
    decoration: BoxDecoration(
      color: C.lightSurface,
      borderRadius: BorderRadius.circular(AppRadius.card),
      border: Border.all(color: C.lightLine),
    ),
    padding: const EdgeInsets.fromLTRB(14, 12, 14, 14),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(Icons.auto_awesome, size: 16, color: C.primaryInk),
            SizedBox(width: 6),
            Text(
              context.l10n.jdihAssistant,
              style: T.label.copyWith(color: C.lightInkMuted),
            ),
          ],
        ),
        const SizedBox(height: 6),
        child,
      ],
    ),
  );
}

/// Gelembung pertanyaan meluncur masuk dari kanan sambil membesar dari
/// sudut kanan bawah, sekali per pesan (tidak diulang saat tergulir kembali).
class _Masuk extends StatefulWidget {
  const _Masuk(this.m, {required this.child});
  final _Msg m;
  final Widget child;

  @override
  State<_Masuk> createState() => _MasukState();
}

class _MasukState extends State<_Masuk> with SingleTickerProviderStateMixin {
  late final _c = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 380),
    value: widget.m.muncul ? 1 : 0,
  );

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (widget.m.muncul) return;
    widget.m.muncul = true;
    MediaQuery.of(context).disableAnimations ? _c.value = 1 : _c.forward();
  }

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final a = CurvedAnimation(parent: _c, curve: Curves.easeOutBack);
    return FadeTransition(
      opacity: CurvedAnimation(parent: _c, curve: Curves.easeOut),
      child: SlideTransition(
        position: Tween(
          begin: const Offset(.25, .1),
          end: Offset.zero,
        ).animate(CurvedAnimation(parent: _c, curve: Curves.easeOutCubic)),
        child: ScaleTransition(
          scale: Tween(begin: .85, end: 1.0).animate(a),
          alignment: Alignment.bottomRight,
          child: widget.child,
        ),
      ),
    );
  }
}

class _UserBubble extends StatelessWidget {
  const _UserBubble(this.text);
  final String text;

  @override
  Widget build(BuildContext context) => Align(
    alignment: Alignment.centerRight,
    child: ConstrainedBox(
      constraints: BoxConstraints(
        maxWidth: MediaQuery.sizeOf(context).width * .8,
      ),
      // biru brand, teks putih 6,9:1: suara penanya beda dari kartu jawaban
      // putih bertepi amber
      child: DecoratedBox(
        decoration: BoxDecoration(
          color: C.accent,
          borderRadius: BorderRadius.circular(AppRadius.card),
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
          child: Text(text, style: T.isi.copyWith(color: Colors.white)),
        ),
      ),
    ),
  );
}

/// Pesan pembuka dari asisten (teks sama dengan web) + contoh pertanyaan.
class _Sapaan extends StatelessWidget {
  const _Sapaan({this.onPick});

  /// null setelah percakapan dimulai: contoh pertanyaan disembunyikan.
  final ValueChanged<String>? onPick;

  @override
  Widget build(BuildContext context) => Column(
    crossAxisAlignment: CrossAxisAlignment.stretch,
    children: [
      _KartuAi(child: MdLite(context.l10n.aiGreeting)),
      if (onPick != null) ...[
        const SizedBox(height: AppSpacing.lg),
        Text(
          context.l10n.tryAsking,
          style: T.labelKecil.copyWith(color: C.lightInkMuted),
        ),
        const SizedBox(height: AppSpacing.sm),
        _Saran(items: _contohPertanyaan(context.l10n), onPick: onPick!),
      ],
    ],
  );
}

/// Chip saran (pertanyaan AI / kata kunci cari). Diketuk: chip itu menyala
/// biru, membesar, lalu melesat ke [arah] seperti terkirim, sementara chip
/// lain memudar — baru kemudian [onPick] dijalankan.
class _Saran extends StatefulWidget {
  const _Saran({
    required this.items,
    required this.onPick,
    this.rata = WrapAlignment.start,
    this.arah = const Offset(48, -28),
  });
  final List<({String label, String tanya})> items;
  final ValueChanged<String> onPick;
  final WrapAlignment rata;
  final Offset arah;

  @override
  State<_Saran> createState() => _SaranState();
}

class _SaranState extends State<_Saran> with SingleTickerProviderStateMixin {
  late final _c = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 460),
  );
  int? _dipilih;

  void _pilih(int i) {
    if (_dipilih != null) return;
    final tanya = widget.items[i].tanya;
    if (MediaQuery.of(context).disableAnimations) return widget.onPick(tanya);
    HapticFeedback.selectionClick();
    setState(() => _dipilih = i);
    _c.forward().whenComplete(() {
      if (mounted) widget.onPick(tanya);
    });
  }

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) => AnimatedBuilder(
    animation: _c,
    builder: (context, _) {
      final t = _c.value;
      double fase(double a, double b, [Curve c = Curves.easeOutCubic]) =>
          c.transform(((t - a) / (b - a)).clamp(0.0, 1.0));
      // runSpacing 0: chip sudah membawa target sentuh 48dp di sekelilingnya
      return Wrap(
        alignment: widget.rata,
        spacing: AppSpacing.sm,
        children: [
          for (final (i, q) in widget.items.indexed)
            if (_dipilih == null)
              _chip(i, q.label, q.tanya, false)
            else if (i == _dipilih)
              Transform.translate(
                offset: widget.arah * fase(.42, 1, Curves.easeInCubic),
                child: Transform.scale(
                  scale:
                      1 +
                      .08 * fase(0, .3, Curves.easeOutBack) -
                      .2 * fase(.42, 1, Curves.easeInCubic),
                  child: Opacity(
                    opacity: 1 - fase(.6, 1),
                    child: _chip(i, q.label, q.tanya, true),
                  ),
                ),
              )
            else
              Opacity(
                opacity: 1 - fase(0, .4),
                child: Transform.scale(
                  scale: 1 - .08 * fase(0, .4),
                  child: _chip(i, q.label, q.tanya, false),
                ),
              ),
        ],
      );
    },
  );

  Widget _chip(int i, String label, String tanya, bool aktif) => ActionChip(
    avatar: Icon(
      aktif ? Icons.send : Icons.north_east,
      size: 16,
      color: aktif ? Colors.white : C.accent,
    ),
    label: Text(
      label,
      style: T.label.copyWith(
        fontWeight: FontWeight.w500,
        color: aktif ? Colors.white : null,
      ),
    ),
    tooltip: tanya,
    onPressed: () => _pilih(i),
    backgroundColor: aktif ? C.accent : C.lightSurface,
    side: BorderSide(color: aktif ? C.accent : C.accent.withValues(alpha: .3)),
    labelPadding: const EdgeInsets.only(right: 4),
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(AppRadius.chip),
    ),
  );
}

/// Balasan AI: menunggu -> diketik -> lampiran dokumen muncul setelahnya.
class _AiBubble extends StatefulWidget {
  const _AiBubble(
    this.m, {
    required this.onRetry,
    required this.onTumbuh,
    required this.onSelesai,
  });
  final _Msg m;
  final ValueChanged<String> onRetry;

  /// Dipanggil sekali saat efek ketik selesai (atau dilewati): induk
  /// menghidupkan lagi tombol bersihkan.
  final VoidCallback onSelesai;

  /// Dipanggil tiap kali jawaban bertambah panjang (efek ketik, lampiran
  /// muncul), sebelum tata letak frame itu dihitung.
  final VoidCallback onTumbuh;

  @override
  State<_AiBubble> createState() => _AiBubbleState();
}

class _AiBubbleState extends State<_AiBubble>
    with SingleTickerProviderStateMixin {
  AnimationController? _c;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    _mulai();
  }

  @override
  void didUpdateWidget(_AiBubble old) {
    super.didUpdateWidget(old);
    _mulai();
  }

  /// Efek ketik sepanjang jawaban, 12 ms per huruf dijepit 0,8-6 detik
  /// (angka web): yang pendek tidak bertele-tele, yang panjang tidak
  /// menyandera pembaca.
  void _mulai() {
    final m = widget.m;
    if (m.pending || m.failed != null || m.typed || _c != null) return;
    if (MediaQuery.of(context).disableAnimations) {
      m.typed = true;
      // sedang di tengah build: induk diberi tahu sesudah frame ini
      WidgetsBinding.instance.addPostFrameCallback((_) => widget.onSelesai());
      return;
    }
    _c =
        AnimationController(
            vsync: this,
            duration: Duration(
              milliseconds: (m.text.length * 12).clamp(800, 6000),
            ),
          )
          ..addListener(() => widget.onTumbuh())
          ..addStatusListener((s) {
            if (s != AnimationStatus.completed) return;
            setState(() => m.typed = true);
            widget.onTumbuh(); // lampiran dokumen muncul di bawah jawaban
            widget.onSelesai();
          })
          ..forward();
  }

  @override
  void dispose() {
    // keluar layar di tengah ketikan: saat kembali, tampilkan utuh
    if (_c != null && !widget.m.typed) {
      widget.m.typed = true;
      final selesai = widget.onSelesai;
      WidgetsBinding.instance.addPostFrameCallback((_) => selesai());
    }
    _c?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final m = widget.m;
    final Widget isi;
    if (m.pending) {
      isi = const _Menelusuri();
    } else if (m.failed != null) {
      isi = Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(m.text, style: T.isi.copyWith(color: C.statusRevoked)),
          TextButton.icon(
            onPressed: () => widget.onRetry(m.failed!),
            icon: const Icon(Icons.refresh, size: 18),
            label: Text(context.l10n.resend),
          ),
        ],
      );
    } else if (!m.typed && _c != null) {
      isi = AnimatedBuilder(
        animation: _c!,
        builder: (_, _) => MdLite(ketikSebagian(m.text, _c!.value)),
      );
    } else {
      isi = MdLite(m.text);
    }
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        _KartuAi(child: isi),
        if (m.typed && m.docs.isNotEmpty) ...[
          const SizedBox(height: AppSpacing.sm),
          Rise(child: _Lampiran(m.docs)),
        ],
      ],
    );
  }
}

/// Penanda menunggu jawaban: halaman dokumen yang dipindai berkas amber,
/// tahap kerja yang berganti, dan pita kemajuan. Tahap berganti tiap 1,8
/// detik lalu berhenti di "Menyusun jawaban" sampai respons tiba — ia
/// menceritakan apa yang dikerjakan server, bukan persen palsu.
class _Menelusuri extends StatefulWidget {
  const _Menelusuri();

  static List<String> tahap(AppLocalizations l) => [
    l.aiStepUnderstand,
    l.aiStepSearch,
    l.aiStepMatch,
    l.aiStepCompose,
  ];

  /// Jumlah [tahap].
  static const jumlahTahap = 4;

  @override
  State<_Menelusuri> createState() => _MenelusuriState();
}

class _MenelusuriState extends State<_Menelusuri>
    with SingleTickerProviderStateMixin {
  late final _c = AnimationController(
    vsync: this,
    duration: const Duration(milliseconds: 1400),
  )..repeat();

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final diam = MediaQuery.of(context).disableAnimations;
    if (diam) _c.stop();
    return Semantics(
      liveRegion: true,
      label: context.l10n.aiSearching,
      excludeSemantics: true,
      child: AnimatedBuilder(
        animation: _c,
        builder: (context, _) {
          // waktu ticker, bukan jam dinding: ikut berhenti saat tab tersembunyi
          final lewat = _c.lastElapsedDuration?.inMilliseconds ?? 0;
          final n = diam
              ? 1
              : math.min(lewat ~/ 1800, _Menelusuri.jumlahTahap - 1);
          return Row(
            children: [
              CustomPaint(
                size: const Size(40, 48),
                painter: _PindaiPainter(diam ? .5 : _c.value),
              ),
              const SizedBox(width: AppSpacing.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    AnimatedSwitcher(
                      duration: const Duration(milliseconds: 300),
                      transitionBuilder: (child, a) => FadeTransition(
                        opacity: a,
                        child: SlideTransition(
                          position: Tween(
                            begin: const Offset(0, .4),
                            end: Offset.zero,
                          ).animate(a),
                          child: child,
                        ),
                      ),
                      layoutBuilder: (cur, prev) => Stack(
                        alignment: Alignment.centerLeft,
                        children: [...prev, ?cur],
                      ),
                      child: Text(
                        '${_Menelusuri.tahap(context.l10n)[n]}'
                        '${diam ? '...' : _titik()}',
                        key: ValueKey(n),
                        style: T.isi.copyWith(color: C.lightInk),
                      ),
                    ),
                    const SizedBox(height: AppSpacing.sm),
                    // satu ruas per tahap: yang sedang berjalan berkilau
                    Row(
                      children: [
                        for (var i = 0; i < _Menelusuri.jumlahTahap; i++) ...[
                          if (i > 0) const SizedBox(width: 4),
                          Expanded(
                            child: Container(
                              height: 4,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(2),
                                color: i < n
                                    ? C.accent
                                    : i == n
                                    ? Color.lerp(
                                        C.accent.withValues(alpha: .35),
                                        C.primary,
                                        math.sin(_c.value * math.pi),
                                      )
                                    : C.lightLine,
                              ),
                            ),
                          ),
                        ],
                      ],
                    ),
                  ],
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  /// Elipsis yang bertambah satu titik tiap sepertiga putaran.
  String _titik() => '.' * (1 + (_c.value * 3).floor().clamp(0, 2));
}

/// Dua halaman bertumpuk; berkas amber menyapu halaman depan dari atas ke
/// bawah dan baris teks yang dilewatinya menyala.
class _PindaiPainter extends CustomPainter {
  _PindaiPainter(this.t);
  final double t;

  @override
  void paint(Canvas canvas, Size size) {
    final belakang = RRect.fromRectAndRadius(
      const Offset(6, 0) & Size(size.width - 6, size.height - 6),
      const Radius.circular(3),
    );
    canvas.drawRRect(
      belakang,
      Paint()..color = C.accent.withValues(alpha: .12),
    );
    final halaman = Rect.fromLTWH(0, 6, size.width - 6, size.height - 6);
    final depan = RRect.fromRectAndRadius(halaman, const Radius.circular(3));
    canvas.drawRRect(depan, Paint()..color = C.lightSurface);
    canvas.drawRRect(
      depan,
      Paint()
        ..style = PaintingStyle.stroke
        ..color = C.accent.withValues(alpha: .5),
    );
    // naik-turun: 0->1->0 dengan perlambatan di ujung
    final y =
        halaman.top +
        4 +
        (halaman.height - 8) *
            Curves.easeInOut.transform(t < .5 ? t * 2 : 2 - t * 2);
    final baris = Paint()
      ..strokeWidth = 3
      ..strokeCap = StrokeCap.round;
    for (var i = 0; i < 4; i++) {
      final by = halaman.top + 9 + i * 8;
      final dekat = (by - y).abs() < 5;
      baris.color = dekat ? C.primary : C.accent.withValues(alpha: .28);
      canvas.drawLine(
        Offset(halaman.left + 6, by),
        Offset(halaman.right - (i == 3 ? 14 : 6), by),
        baris,
      );
    }
    canvas.drawRect(
      Rect.fromLTRB(halaman.left, y - 6, halaman.right, y + 6),
      Paint()
        ..shader = LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [
            C.primary.withValues(alpha: 0),
            C.primary.withValues(alpha: .28),
            C.primary.withValues(alpha: 0),
          ],
        ).createShader(Rect.fromLTRB(0, y - 6, 1, y + 6)),
    );
    canvas.drawLine(
      Offset(halaman.left - 2, y),
      Offset(halaman.right + 2, y),
      Paint()
        ..color = C.primary
        ..strokeWidth = 1.5,
    );
  }

  @override
  bool shouldRepaint(_PindaiPainter old) => old.t != t;
}

/// Dokumen relevan sebagai lampiran jawaban. Tertutup secara bawaan, sama
/// dengan web: thread tidak dipenuhi daftar dokumen yang sama tiap giliran.
class _Lampiran extends StatelessWidget {
  const _Lampiran(this.docs);
  final List<Json> docs;

  @override
  Widget build(BuildContext context) => Material(
    color: C.lightSurface,
    clipBehavior: Clip.antiAlias,
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(AppRadius.card),
      side: const BorderSide(color: C.lightLine),
    ),
    child: ExpansionTile(
      shape: const Border(),
      collapsedShape: const Border(),
      tilePadding: const EdgeInsets.symmetric(horizontal: AppSpacing.md),
      childrenPadding: const EdgeInsets.fromLTRB(
        AppSpacing.md,
        0,
        AppSpacing.md,
        AppSpacing.md,
      ),
      leading: const Icon(Icons.attach_file, size: 20, color: C.accent),
      title: Text(context.l10n.relatedDocs, style: T.judulItem),
      subtitle: Text(
        context.l10n.docsAttached(docs.length),
        style: T.isiKecil.copyWith(color: C.lightInkMuted),
      ),
      children: [
        for (final (i, d) in docs.indexed)
          Padding(
            padding: EdgeInsets.only(top: i == 0 ? 0 : AppSpacing.sm),
            child: _DocLampiran(d),
          ),
      ],
    ),
  );
}

/// Satu dokumen lampiran (bentuk JSON hasil AI beda dari DocumentListItem).
class _DocLampiran extends StatelessWidget {
  const _DocLampiran(this.d);
  final Json d;

  @override
  Widget build(BuildContext context) => Material(
    color: C.lightBg,
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(AppRadius.card),
      side: const BorderSide(color: C.lightLine),
    ),
    child: InkWell(
      borderRadius: BorderRadius.circular(AppRadius.card),
      onTap: () => Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => DocumentDetailScreen(id: d.i('id'))),
      ),
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.md),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // judul di basis data KAPITAL SEMUA
            Text(
              titleCase(d.s('title')),
              maxLines: 3,
              overflow: TextOverflow.ellipsis,
              style: T.judulItem,
            ),
            const SizedBox(height: AppSpacing.sm),
            Wrap(
              spacing: 6,
              runSpacing: 6,
              crossAxisAlignment: WrapCrossAlignment.center,
              children: [
                if (d.sn('type') != null) JenisChip(d.sn('type')),
                if (d.sn('year') != null)
                  MetaPill(
                    Icons.event_outlined,
                    context.l10n.yearValue(d.s('year')),
                  ),
                StatusBadge(d.sn('status')),
              ],
            ),
            if (d.sn('description') != null) ...[
              const SizedBox(height: 6),
              Text(
                d.s('description'),
                maxLines: 3,
                overflow: TextOverflow.ellipsis,
                style: T.isiKecil.copyWith(color: C.lightInkMuted),
              ),
            ],
            const SizedBox(height: AppSpacing.sm),
            Row(
              children: [
                // keduanya boleh menyusut: huruf besar tidak meluapkan baris
                Expanded(
                  child: Text(
                    context.l10n.percentRelevant(d.i('accuracy').clamp(0, 100)),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: T.label.copyWith(color: C.lightInkMuted),
                  ),
                ),
                Flexible(
                  child: Text(
                    context.l10n.viewDocument,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: T.label.copyWith(
                      fontWeight: FontWeight.w700,
                      color: C.accent,
                    ),
                  ),
                ),
                const Icon(Icons.chevron_right, size: 18, color: C.accent),
              ],
            ),
          ],
        ),
      ),
    ),
  );
}

/// Batas [_maxTurns] pertanyaan tercapai: beri tahu bahwa pertanyaan
/// berikutnya memulai percakapan baru, atau mulai sekarang.
class _Batas extends StatelessWidget {
  const _Batas({required this.onReset});
  final VoidCallback onReset;

  @override
  Widget build(BuildContext context) => Container(
    padding: const EdgeInsets.all(AppSpacing.md),
    decoration: BoxDecoration(
      color: C.lightSurface,
      borderRadius: BorderRadius.circular(AppRadius.card),
      border: Border.all(color: C.lightLine),
    ),
    child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(Icons.info_outline, size: 18, color: C.accent),
            SizedBox(width: AppSpacing.sm),
            Expanded(
              child: Text(
                context.l10n.chatFull(_maxTurns),
                style: T.isi.copyWith(color: C.lightInkMuted),
              ),
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.sm),
        FilledButton.icon(
          onPressed: onReset,
          icon: const Icon(Icons.add_comment_outlined, size: 18),
          label: Text(context.l10n.newChat),
        ),
      ],
    ),
  );
}

/// Potongan jawaban selama animasi ketik. Bintang yang belum lengkap di ujung
/// dibuang supaya penanda tebal tidak pernah terlihat mentah.
String ketikSebagian(String text, double t) {
  final n = (text.length * t).round();
  return n >= text.length
      ? text
      : text.substring(0, n).replaceFirst(RegExp(r'\*+$'), '');
}

/// "a **b** c" -> [a, b(tebal), c]. Penanda yang belum berpasangan menebalkan
/// sisa teks, sama seperti web menutupnya sementara saat animasi ketik.
List<TextSpan> mdSpans(String s) => [
  for (final (i, p) in s.split('**').indexed)
    if (p.isNotEmpty)
      TextSpan(
        text: p,
        style: i.isOdd ? const TextStyle(fontWeight: FontWeight.w700) : null,
      ),
];

/// Markdown seadanya dari jawaban AI, sama dengan web: **tebal**, baris
/// "- " / "* " jadi butir daftar, sisanya paragraf berjarak.
class MdLite extends StatelessWidget {
  const MdLite(this.text, {super.key});
  final String text;

  static final _butir = RegExp(r'^\s*[-*]\s+(.*)$');

  @override
  Widget build(BuildContext context) {
    // jawaban asisten adalah teks bacaan: peran bacaan, bukan isi UI
    final base = T.bacaan.copyWith(color: C.lightInk);
    final blok = <Widget>[];
    var sebelumnyaButir = false;
    for (final line in text.split('\n')) {
      if (line.trim().isEmpty) continue;
      final butir = _butir.firstMatch(line);
      final isi = Text.rich(
        TextSpan(children: mdSpans(butir?.group(1) ?? line)),
        style: base,
      );
      blok.add(
        Padding(
          padding: EdgeInsets.only(
            top: blok.isEmpty ? 0 : (butir != null && sebelumnyaButir ? 4 : 10),
          ),
          child: butir == null
              ? isi
              : Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('•  ', style: base),
                    Expanded(child: isi),
                  ],
                ),
        ),
      );
      sebelumnyaButir = butir != null;
    }
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: blok);
  }
}
