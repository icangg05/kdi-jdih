import 'package:flutter/material.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';
import 'documents.dart';

enum _Mode { teks, ai }

class SearchScreen extends StatefulWidget {
  const SearchScreen({
    super.key,
    this.initialAi = false,
    this.showBack = false,
    this.initialQuery = '',
    this.onExit,
  });
  final bool initialAi;

  /// Kata kunci yang sudah diketik di layar sebelumnya (mis. kolom cari beranda).
  final String initialQuery;

  /// true bila dibuka via push (bukan sebagai tab).
  final bool showBack;

  /// Dipakai saat layar ini jadi tab tanpa navigasi bawah: tidak ada yang bisa
  /// di-pop, jadi jalan keluarnya harus disediakan shell lewat callback ini.
  final VoidCallback? onExit;

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
    appBar: BrandAppBar(
      'Cari Pintar',
      showBack: widget.showBack,
      leading: widget.onExit == null
          ? null
          : IconButton(
              icon: const Icon(Icons.arrow_back),
              tooltip: 'Kembali',
              onPressed: widget.onExit,
            ),
    ),
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
                  _pil(w, _Mode.teks, Icons.search, 'Pencarian Teks'),
                  _pil(w, _Mode.ai, Icons.auto_awesome, 'Tanya AI'),
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
/// daripada halaman kosong yang menyuruh "ketik kata kunci".
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
              hintText: 'Cari judul, nomor, atau topik...',
              prefixIcon: const Icon(Icons.search),
              suffixIcon: _q.isEmpty
                  ? null
                  : IconButton(
                      icon: const Icon(Icons.close),
                      tooltip: 'Hapus pencarian',
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
            labels: [for (final c in docCategories) c.short],
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
                '$_total hasil untuk "$_q"',
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
                    saran:
                        'Coba kata kunci yang lebih pendek, ganti '
                        'kategori di atas, atau tanyakan langsung ke AI.',
                    actions: [
                      OutlinedButton.icon(
                        onPressed: () {
                          _ctrl.clear();
                          _cari('');
                        },
                        icon: const Icon(Icons.close, size: 18),
                        label: const Text('Hapus pencarian'),
                      ),
                      FilledButton.icon(
                        onPressed: () => widget.onTanyaAi(_q),
                        icon: const Icon(Icons.auto_awesome, size: 18),
                        label: const Text('Tanya AI'),
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
      const Text(
        'Cari Produk Hukum',
        textAlign: TextAlign.center,
        style: T.judul,
      ),
      const SizedBox(height: AppSpacing.sm),
      Text(
        'Ketik judul, nomor, atau topik peraturan lalu tekan tombol cari '
        'pada papan ketik. Kategori di atas mempersempit hasilnya.',
        textAlign: TextAlign.center,
        style: T.isiKecil.copyWith(color: C.lightInkMuted),
      ),
      const SizedBox(height: AppSpacing.xl),
      Wrap(
        alignment: WrapAlignment.center,
        spacing: AppSpacing.sm,
        runSpacing: AppSpacing.sm,
        children: [
          for (final k in _contohKata)
            ActionChip(
              label: Text(k, style: T.isi),
              avatar: const Icon(Icons.north_east, size: 14),
              onPressed: () {
                _ctrl.text = k;
                _cari(k);
              },
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(AppRadius.chip),
                side: const BorderSide(color: C.lightLine),
              ),
              backgroundColor: C.lightSurface,
            ),
        ],
      ),
    ],
  );
}

/// Pertanyaan per percakapan, sama dengan web: server hanya mengingat
/// beberapa giliran terakhir, jadi percakapan yang lebih panjang terasa
/// nyambung padahal awalnya sudah terlupakan.
const _maxTurns = 10;

/// Giliran yang ikut dikirim ke server (server memotong lagi).
const _maxHistory = 12;

/// Panjang pertanyaan maksimum (validasi `query` di AiSearchController).
const _maxPanjang = 500;

const _contohPertanyaan = [
  'Apa aturan retribusi sampah?',
  'Perwali terbaru tentang apa?',
  'Bagaimana cara mengurus IMB?',
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
}

/// Tanya AI mode percakapan, mekanismenya sama dengan modal web: riwayat
/// dikirim ulang tiap giliran, jawaban "diketik", lalu dokumen yang relevan
/// dilampirkan di bawahnya.
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
  final _msgs = <_Msg>[];
  final _history = <Json>[];
  bool _busy = false;

  /// Naik tiap percakapan baru; jawaban percakapan lama yang tiba terlambat
  /// dibuang alih-alih masuk ke riwayat yang baru.
  int _sesi = 0;

  bool get _penuh =>
      _history.where((h) => h['role'] == 'user').length >= _maxTurns;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    final q = widget.query.trim();
    if (q.isNotEmpty) {
      WidgetsBinding.instance.addPostFrameCallback((_) => ask(q));
    }
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _ctrl.dispose();
    _scroll.dispose();
    super.dispose();
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

  /// Papan ketik memperpendek layar dari bawah; tanpa ini pesan terakhir
  /// tertutup begitu kolom pertanyaan disentuh.
  @override
  void didChangeMetrics() => _ikuti();

  Future<void> ask([String? preset]) async {
    final q = (preset ?? _ctrl.text).trim();
    if (q.isEmpty || _busy || _penuh || !mounted) return;
    FocusScope.of(context).unfocus();
    final sesi = _sesi;
    final reply = _Msg(true)..pending = true;
    final history = _history.length > _maxHistory
        ? _history.sublist(_history.length - _maxHistory)
        : List.of(_history);
    setState(() {
      _busy = true;
      _ctrl.clear();
      _msgs
        ..add(_Msg(false, q))
        ..add(reply);
    });
    _keDasar(animasi: true);
    try {
      final r = await api.aiSearch(q, history: history);
      if (sesi != _sesi) return;
      reply.text =
          r.sn('explanation') ??
          'Maaf, saya belum bisa menjawab pertanyaan itu.';
      reply.docs = r.l('documents');
      // server menolak teks riwayat > 4000 karakter; jangan sampai satu
      // jawaban panjang mematahkan seluruh percakapan
      final simpan = reply.text.length > 4000
          ? reply.text.substring(0, 4000)
          : reply.text;
      _history
        ..add({'role': 'user', 'text': q})
        ..add({'role': 'ai', 'text': simpan});
    } catch (e) {
      if (sesi != _sesi) return;
      reply
        ..text = '$e'
        ..failed = q;
    }
    if (!mounted) return;
    final ikut = _diDasar;
    setState(() {
      reply.pending = false;
      _busy = false;
    });
    if (ikut) _keDasar();
  }

  void _reset() => setState(() {
    _sesi++;
    _busy = false;
    _msgs.clear();
    _history.clear();
    _ctrl.clear();
  });

  /// Bersihkan percakapan, dengan jalan pulang: satu ketukan tak sengaja
  /// tidak boleh menghapus seluruh tanya-jawab tanpa bisa dikembalikan.
  /// Hanya bisa saat tidak menunggu jawaban, jadi tidak ada balasan
  /// tertunda yang ikut tersimpan.
  void _bersihkan() {
    final msgs = List.of(_msgs), history = List.of(_history);
    _reset();
    ScaffoldMessenger.of(context)
      ..hideCurrentSnackBar()
      ..showSnackBar(
        SnackBar(
          content: const Text('Percakapan dibersihkan.'),
          action: SnackBarAction(
            label: 'Urungkan',
            onPressed: () {
              if (!mounted || _msgs.isNotEmpty) return;
              setState(() {
                _msgs.addAll(msgs);
                _history.addAll(history);
              });
              _keDasar();
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
              ? _AiBubble(m, onRetry: ask, onTumbuh: _ikuti)
              : _UserBubble(m.text),
        ),
      if (_penuh && !_busy) (const ValueKey('batas'), _Batas(onReset: _reset)),
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
                      tooltip: 'Bersihkan percakapan',
                      icon: const Icon(Icons.cleaning_services_outlined),
                      onPressed: _busy ? null : _bersihkan,
                    ),
                  Expanded(
                    child: TextField(
                      controller: _ctrl,
                      enabled: !_penuh,
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
                        hintText: _penuh
                            ? 'Batas $_maxTurns pertanyaan tercapai'
                            : 'Tulis pertanyaan Anda...',
                      ),
                      onSubmitted: (_) => ask(),
                    ),
                  ),
                  const SizedBox(width: AppSpacing.sm),
                  IconButton.filled(
                    tooltip: 'Kirim',
                    style: IconButton.styleFrom(
                      minimumSize: const Size(48, 48),
                    ),
                    icon: const Icon(Icons.send, size: 20),
                    onPressed: _busy || _penuh ? null : ask,
                  ),
                ],
              ),
              const SizedBox(height: 6),
              Text(
                'Jawaban dibuat otomatis oleh AI. Selalu periksa dokumen aslinya.',
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
              'Asisten JDIH',
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
      const _KartuAi(
        child: MdLite(
          'Halo! Tanyakan apa saja seputar hukum atau '
          'peraturan Kota Kendari. Saya akan menjawab sekaligus '
          'menunjukkan dokumen JDIH yang terkait.',
        ),
      ),
      if (onPick != null) ...[
        const SizedBox(height: AppSpacing.md),
        Wrap(
          spacing: AppSpacing.sm,
          runSpacing: AppSpacing.sm,
          children: [
            for (final q in _contohPertanyaan)
              ActionChip(
                label: Text(q, style: T.isi),
                onPressed: () => onPick!(q),
                backgroundColor: C.lightSurface,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(AppRadius.chip),
                  side: const BorderSide(color: C.lightLine),
                ),
              ),
          ],
        ),
      ],
    ],
  );
}

/// Balasan AI: menunggu -> diketik -> lampiran dokumen muncul setelahnya.
class _AiBubble extends StatefulWidget {
  const _AiBubble(this.m, {required this.onRetry, required this.onTumbuh});
  final _Msg m;
  final ValueChanged<String> onRetry;

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
          })
          ..forward();
  }

  @override
  void dispose() {
    // keluar layar di tengah ketikan: saat kembali, tampilkan utuh
    if (_c != null) widget.m.typed = true;
    _c?.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final m = widget.m;
    final Widget isi;
    if (m.pending) {
      isi = Pulse(
        child: Text(
          'Menelusuri dokumen...',
          style: T.isi.copyWith(color: C.lightInkMuted),
        ),
      );
    } else if (m.failed != null) {
      isi = Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(m.text, style: T.isi.copyWith(color: C.statusRevoked)),
          TextButton.icon(
            onPressed: () => widget.onRetry(m.failed!),
            icon: const Icon(Icons.refresh, size: 18),
            label: const Text('Kirim ulang'),
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
      title: const Text('Dokumen terkait', style: T.judulItem),
      subtitle: Text(
        '${docs.length} dokumen dilampirkan',
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
                  MetaPill(Icons.event_outlined, 'Tahun ${d.s('year')}'),
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
                    '${d.i('accuracy').clamp(0, 100)}% relevan',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: T.label.copyWith(color: C.lightInkMuted),
                  ),
                ),
                Flexible(
                  child: Text(
                    'Lihat dokumen',
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

/// Batas [_maxTurns] pertanyaan tercapai: ajak memulai percakapan baru.
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
                'Percakapan ini sudah mencapai $_maxTurns pertanyaan. '
                'Mulai percakapan baru untuk bertanya lagi.',
                style: T.isi.copyWith(color: C.lightInkMuted),
              ),
            ),
          ],
        ),
        const SizedBox(height: AppSpacing.sm),
        FilledButton.icon(
          onPressed: onReset,
          icon: const Icon(Icons.add_comment_outlined, size: 18),
          label: const Text('Percakapan baru'),
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
    // serif: jawaban asisten adalah teks bacaan, beda suara dari pertanyaan
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
