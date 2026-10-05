import 'package:file_saver/file_saver.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:pdfrx/pdfrx.dart';
import 'package:skeletonizer/skeletonizer.dart';

import '../api.dart';
import '../theme.dart';
import '../widgets.dart';

/// ".../storage/dokumen/perda-5-2023.pdf" -> "perda-5-2023.pdf"
String docFileName(String url) {
  final seg = Uri.tryParse(url)?.pathSegments ?? const [];
  final name = seg.isEmpty ? '' : Uri.decodeComponent(seg.last);
  return name.isEmpty ? 'dokumen.pdf' : name;
}

bool isPdfUrl(String url) => docFileName(url).toLowerCase().endsWith('.pdf');

/// Keterangan berkas untuk pembaca. Nama file mentah dari basis data
/// ("1690_pengumuman_final(1).pdf") tidak bermakna, jadi yang ditampilkan
/// jenis berkas + cara membukanya.
String docKindLabel(String url) {
  final l = l10nAktif;
  if (isPdfUrl(url)) return l.fileKindPdf;
  final name = docFileName(url);
  final dot = name.lastIndexOf('.');
  final ext = dot > 0 ? name.substring(dot + 1).toUpperCase() : l.fileGeneric;
  return l.fileKindOther(ext);
}

/// Nama berkas bersih, aturan yang sama dengan `Str::slug` di web: huruf
/// kecil, selain huruf/angka jadi satu tanda hubung. Huruf non-Latin (judul
/// terjemahan zh/ko) dipertahankan, bukan dibuang. Dipangkas di batas kata
/// sekitar 100 karakter: judul peraturan bisa satu paragraf, sedangkan nama
/// berkas dibatasi 255 byte.
String slugBerkas(String? teks) {
  var s = (teks ?? '')
      .toLowerCase()
      .replaceAll(RegExp(r'[^\p{L}\p{N}]+', unicode: true), '-')
      .replaceAll(RegExp(r'^-+|-+$'), '');
  if (s.length > 100) {
    s = s.substring(0, 100);
    final kata = s.lastIndexOf('-');
    if (kata > 60) s = s.substring(0, kata);
  }
  return s;
}

/// Nama berkas unduhan = slug judul + ekstensi asli, bukan nama di server
/// ("2026pw7416021.pdf"). Jatuh kembali ke nama asli bila judulnya kosong.
String downloadFileName(String url, String? title) {
  final asli = docFileName(url);
  final dot = asli.lastIndexOf('.');
  final ext = dot > 0 ? asli.substring(dot) : '.pdf';
  final slug = slugBerkas(title);
  return slug.isEmpty ? asli : '$slug$ext';
}

/// Unduh ke perangkat tanpa keluar aplikasi.
///
/// Android/web: diserahkan ke DownloadManager/browser, file masuk folder Download
/// dan progresnya tampil di notifikasi sistem — tanpa izin storage tambahan.
/// iOS tidak punya folder Download publik, jadi memakai dialog "Simpan ke File".
/// Berkas yang sedang diserahkan ke pengunduh. Ketukan beruntun pada tombol
/// Unduh tidak boleh memasukkan berkas yang sama berkali-kali ke antrean.
final _sedangDiunduh = <String>{};

Future<void> downloadDoc(
  BuildContext context,
  String url, {
  String? title,
}) async {
  final messenger = ScaffoldMessenger.of(context);
  final l = context.l10n;
  if (!_sedangDiunduh.add(url)) return;
  final name = downloadFileName(url, title);
  final dot = name.lastIndexOf('.');
  final base = dot > 0 ? name.substring(0, dot) : name;
  final ext = dot > 0 ? name.substring(dot + 1) : 'pdf';
  final android = !kIsWeb && defaultTargetPlatform == TargetPlatform.android;

  messenger.showSnackBar(SnackBar(content: Text(l.downloading(name))));
  try {
    if (kIsWeb || android) {
      await FileSaver.instance.downloadLink(
        link: LinkDetails(link: url),
        name: name,
      );
    } else {
      await FileSaver.instance.saveAs(
        name: base,
        link: LinkDetails(link: url),
        fileExtension: ext,
        mimeType: ext.toLowerCase() == 'pdf' ? MimeType.pdf : MimeType.other,
      );
    }
  } catch (e) {
    // galat pengunduh berisi jejak teknis; yang berguna bagi pengguna adalah
    // langkah berikutnya
    if (kDebugMode) debugPrint('downloadDoc: $e');
    messenger.showSnackBar(SnackBar(content: Text(l.downloadFailed)));
  } finally {
    _sedangDiunduh.remove(url);
  }
}

/// Pratinjau dokumen di dalam aplikasi, dengan navigasi halaman di bawah.
class DocPreviewScreen extends StatefulWidget {
  const DocPreviewScreen({super.key, required this.url, required this.title});
  final String url, title;

  @override
  State<DocPreviewScreen> createState() => _DocPreviewScreenState();
}

class _DocPreviewScreenState extends State<DocPreviewScreen> {
  final _ctrl = PdfViewerController();
  int _page = 1;
  int _total = 0;

  void _go(int p) {
    if (p < 1 || p > _total) return;
    _ctrl.goToPage(pageNumber: p);
  }

  /// Lompat ke halaman tertentu: menggulir 200 halaman dengan tombol ‹ › tidak
  /// masuk akal untuk dokumen tebal.
  Future<void> _pilihHalaman() async {
    // TextFormField, bukan TextField + controller sendiri: controller yang
    // dibuang tepat setelah showDialog masih dipakai field yang animasi
    // tutupnya belum selesai, dan itu memicu assert _dependents.isEmpty
    var teks = '$_page';
    final pilihan = await showDialog<int>(
      context: context,
      builder: (c) => AlertDialog(
        title: Text(c.l10n.goToPage),
        content: TextFormField(
          initialValue: teks,
          autofocus: true,
          keyboardType: TextInputType.number,
          decoration: InputDecoration(
            labelText: c.l10n.pageNumber,
            hintText: '1 - $_total',
          ),
          onChanged: (v) => teks = v,
          onFieldSubmitted: (v) => Navigator.pop(c, int.tryParse(v.trim())),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(c),
            child: Text(c.l10n.cancel),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(c, int.tryParse(teks.trim())),
            child: Text(c.l10n.go),
          ),
        ],
      ),
    );
    if (pilihan == null || !mounted) return;
    if (pilihan < 1 || pilihan > _total) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(context.l10n.pageOutOfRange(_total)),
        ),
      );
      return;
    }
    _go(pilihan);
  }

  @override
  Widget build(BuildContext context) => Scaffold(
    appBar: AppBar(
      // nama berkas yang sama dengan hasil unduhan, bukan kode di server
      title: Text(
        downloadFileName(widget.url, widget.title),
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
      ),
      actions: [
        IconButton(
          tooltip: context.l10n.download,
          icon: const Icon(Icons.download_outlined),
          onPressed: () =>
              downloadDoc(context, widget.url, title: widget.title),
        ),
      ],
    ),
    body: PdfViewer.uri(
      Uri.parse(widget.url),
      controller: _ctrl,
      params: PdfViewerParams(
        backgroundColor: C.lightSubtle,
        // fisika gulir bawaan platform: lemparan jari meluncur jauh seperti
        // daftar biasa. Tanpa ini InteractiveViewer mengerem fling dengan cepat
        scrollPhysics: PdfViewerParams.getScrollPhysics(context),
        loadingBannerBuilder: (_, done, total) =>
            _HalamanMemuat((total ?? 0) > 0 ? done / total! : null),
        errorBannerBuilder: (_, error, _, _) => ErrorRetry(
          // galat pdfium tidak bermakna bagi pembaca; berkasnya masih bisa
          // dibuka di aplikasi lain lewat unduhan
          context.l10n.pdfCannotDisplay,
          onRetry: () => downloadDoc(context, widget.url, title: widget.title),
          retryLabel: context.l10n.downloadInstead,
        ),
        onViewerReady: (doc, _) {
          if (mounted) setState(() => _total = doc.pages.length);
        },
        onPageChanged: (p) {
          if (mounted) setState(() => _page = p ?? 1);
        },
      ),
    ),
    // muncul setelah dokumen siap; sebelum itu jumlah halaman belum diketahui
    bottomNavigationBar: _total == 0
        ? null
        : BottomAppBar(
            height: 56,
            padding: EdgeInsets.zero,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                IconButton(
                  tooltip: context.l10n.prevPage,
                  icon: const Icon(Icons.chevron_left),
                  onPressed: _page > 1 ? () => _go(_page - 1) : null,
                ),
                TextButton(
                  onPressed: _pilihHalaman,
                  child: Text(
                    context.l10n.pageOf(_page, _total),
                    style: T.label,
                  ),
                ),
                IconButton(
                  tooltip: context.l10n.nextPage,
                  icon: const Icon(Icons.chevron_right),
                  onPressed: _page < _total ? () => _go(_page + 1) : null,
                ),
              ],
            ),
          ),
  );
}

/// Baris berkas: nama file + tombol Lihat (pratinjau) dan Unduh.
///
/// Berkas non-PDF tidak bisa dirender pdfium, jadi hanya menawarkan unduh.
class DocFileTile extends StatelessWidget {
  const DocFileTile(
    this.url, {
    super.key,
    required this.title,
    this.nama,
    this.onOpen,
  });
  final String url;

  /// Label baris; bisa sekadar "Dokumen Utama".
  final String title;

  /// Dasar nama berkas (judul dokumen), dijadikan slug untuk judul pratinjau
  /// dan nama unduhan. Default [title].
  final String? nama;

  /// Dipanggil sekali saat Lihat/Unduh ditekan (mis. mencatat hit_download).
  final VoidCallback? onOpen;

  @override
  Widget build(BuildContext context) {
    final pdf = isPdfUrl(url);
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.md),
        child: Row(
          children: [
            IconTile(
              pdf
                  ? Icons.picture_as_pdf_outlined
                  : Icons.insert_drive_file_outlined,
              size: 40,
            ),
            const SizedBox(width: AppSpacing.md),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: T.judulItem,
                  ),
                  Text(
                    docKindLabel(url),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: T.isiKecil.copyWith(color: C.lightInkMuted),
                  ),
                ],
              ),
            ),
            const SizedBox(width: AppSpacing.sm),
            if (pdf)
              IconButton.filledTonal(
                tooltip: context.l10n.view,
                icon: const Icon(Icons.visibility_outlined),
                // 48dp: target sentuh minimum Android
                iconSize: 20,
                onPressed: () {
                  onOpen?.call();
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) =>
                          DocPreviewScreen(url: url, title: nama ?? title),
                    ),
                  );
                },
              ),
            if (pdf) const SizedBox(width: AppSpacing.sm),
            IconButton.filled(
              tooltip: context.l10n.download,
              icon: const Icon(Icons.download_outlined),
              iconSize: 20,
              onPressed: () {
                onOpen?.call();
                downloadDoc(context, url, title: nama ?? title);
              },
            ),
          ],
        ),
      ),
    );
  }
}

/// Selagi PDF diunduh: kemajuan unduhan di tepi atas + kerangka satu halaman
/// A4 (judul, paragraf) di tempat halaman pertama akan muncul.
class _HalamanMemuat extends StatelessWidget {
  const _HalamanMemuat(this.kemajuan);
  final double? kemajuan;

  @override
  Widget build(BuildContext context) => Column(
    children: [
      LinearProgressIndicator(value: kemajuan, minHeight: 3),
      Expanded(
        child: Padding(
          padding: const EdgeInsets.all(AppSpacing.lg),
          child: Align(
            alignment: Alignment.topCenter,
            child: AspectRatio(
              aspectRatio: 1 / 1.414,
              child: Skeletonizer.zone(
                child: Container(
                  color: C.lightSurface,
                  padding: const EdgeInsets.all(AppSpacing.xl),
                  child: Column(
                    children: [
                      const Bone(width: 48, height: 48, uniRadius: 24),
                      const SizedBox(height: AppSpacing.lg),
                      const Bone.text(words: 4),
                      const SizedBox(height: AppSpacing.sm),
                      const Bone.text(words: 6),
                      const SizedBox(height: AppSpacing.xl),
                      for (var i = 0; i < 8; i++) ...[
                        Bone(
                          width: i % 4 == 3 ? 160 : double.infinity,
                          height: 10,
                          uniRadius: AppRadius.chip,
                        ),
                        const SizedBox(height: AppSpacing.md),
                      ],
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ],
  );
}
