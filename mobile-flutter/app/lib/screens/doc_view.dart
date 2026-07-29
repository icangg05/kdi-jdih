import 'package:file_saver/file_saver.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:pdfrx/pdfrx.dart';

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
  if (isPdfUrl(url)) return 'Dokumen PDF · dapat dibaca di aplikasi';
  final name = docFileName(url);
  final dot = name.lastIndexOf('.');
  final ext = dot > 0 ? name.substring(dot + 1).toUpperCase() : 'Berkas';
  return 'Berkas $ext · unduh untuk membuka';
}

/// Nama berkas unduhan diambil dari judul dokumen, bukan nama di server.
/// Karakter yang dilarang sistem berkas dibuang, spasi dirapatkan, dan
/// panjangnya dipangkas 60 karakter (judul peraturan bisa satu paragraf).
/// Jatuh kembali ke nama asli bila judulnya kosong atau habis dibersihkan.
String downloadFileName(String url, String? title) {
  final asli = docFileName(url);
  final dot = asli.lastIndexOf('.');
  final ext = dot > 0 ? asli.substring(dot) : '.pdf';
  var bersih = (title ?? '')
      .replaceAll(RegExp(r'[<>:"/\\|?*\x00-\x1F]'), ' ')
      .replaceAll(RegExp(r'\s+'), ' ')
      .trim();
  if (bersih.length > 60) bersih = bersih.substring(0, 60);
  // titik/spasi di ujung membuat berkas tidak bisa dibuat di Windows
  bersih = bersih.replaceAll(RegExp(r'[. ]+$'), '');
  return bersih.isEmpty ? asli : '$bersih$ext';
}

/// Unduh ke perangkat tanpa keluar aplikasi.
///
/// Android/web: diserahkan ke DownloadManager/browser, file masuk folder Download
/// dan progresnya tampil di notifikasi sistem — tanpa izin storage tambahan.
/// iOS tidak punya folder Download publik, jadi memakai dialog "Simpan ke File".
Future<void> downloadDoc(BuildContext context, String url,
    {String? title}) async {
  final messenger = ScaffoldMessenger.of(context);
  final name = downloadFileName(url, title);
  final dot = name.lastIndexOf('.');
  final base = dot > 0 ? name.substring(0, dot) : name;
  final ext = dot > 0 ? name.substring(dot + 1) : 'pdf';
  final android = !kIsWeb && defaultTargetPlatform == TargetPlatform.android;

  messenger.showSnackBar(SnackBar(content: Text('Mengunduh $name...')));
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
    messenger.showSnackBar(SnackBar(content: Text('Gagal mengunduh: $e')));
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
        title: const Text('Buka halaman'),
        content: TextFormField(
          initialValue: teks,
          autofocus: true,
          keyboardType: TextInputType.number,
          decoration: InputDecoration(
              labelText: 'Nomor halaman', hintText: '1 - $_total'),
          onChanged: (v) => teks = v,
          onFieldSubmitted: (v) => Navigator.pop(c, int.tryParse(v.trim())),
        ),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(c), child: const Text('Batal')),
          FilledButton(
            onPressed: () => Navigator.pop(c, int.tryParse(teks.trim())),
            child: const Text('Buka'),
          ),
        ],
      ),
    );
    if (pilihan == null || !mounted) return;
    if (pilihan < 1 || pilihan > _total) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text('Dokumen ini hanya punya halaman 1 sampai $_total.')));
      return;
    }
    _go(pilihan);
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
          title:
              Text(widget.title, maxLines: 1, overflow: TextOverflow.ellipsis),
          actions: [
            IconButton(
              tooltip: 'Unduh',
              icon: const Icon(Icons.download_outlined),
              onPressed: () => downloadDoc(context, widget.url, title: widget.title),
            ),
          ],
        ),
        body: PdfViewer.uri(
          Uri.parse(widget.url),
          controller: _ctrl,
          params: PdfViewerParams(
            backgroundColor: C.lightSubtle,
            loadingBannerBuilder: (_, done, total) => Center(
              child: CircularProgressIndicator(
                  value: (total ?? 0) > 0 ? done / total! : null),
            ),
            errorBannerBuilder: (_, error, __, ___) => ErrorRetry(
              'Dokumen tidak dapat ditampilkan.\n$error',
              onRetry: () => downloadDoc(context, widget.url, title: widget.title),
              retryLabel: 'Unduh saja',
            ),
            onViewerReady: (doc, __) {
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
                      tooltip: 'Halaman sebelumnya',
                      icon: const Icon(Icons.chevron_left),
                      onPressed: _page > 1 ? () => _go(_page - 1) : null,
                    ),
                    TextButton(
                      onPressed: _pilihHalaman,
                      child: Text('Halaman $_page dari $_total',
                          style: const TextStyle(
                              fontSize: 13, fontWeight: FontWeight.w600)),
                    ),
                    IconButton(
                      tooltip: 'Halaman berikutnya',
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
  const DocFileTile(this.url, {super.key, required this.title, this.onOpen});
  final String url;
  final String title;

  /// Dipanggil sekali saat Lihat/Unduh ditekan (mis. mencatat hit_download).
  final VoidCallback? onOpen;

  @override
  Widget build(BuildContext context) {
    final pdf = isPdfUrl(url);
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(AppSpacing.md),
        child: Row(children: [
          IconSquircle(
              pdf ? Icons.picture_as_pdf_outlined : Icons.insert_drive_file_outlined,
              size: 40),
          const SizedBox(width: AppSpacing.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontWeight: FontWeight.w600)),
                Text(docKindLabel(url),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                        fontSize: 12, color: C.lightInkMuted)),
              ],
            ),
          ),
          const SizedBox(width: AppSpacing.sm),
          if (pdf)
            IconButton.filledTonal(
              tooltip: 'Lihat',
              icon: const Icon(Icons.visibility_outlined),
              // ponytail: 40dp, di bawah anjuran 48 tapi masih nyaman disentuh
              iconSize: 18,
              constraints: const BoxConstraints.tightFor(width: 40, height: 40),
              padding: EdgeInsets.zero,
              onPressed: () {
                onOpen?.call();
                Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) =>
                            DocPreviewScreen(url: url, title: title)));
              },
            ),
          if (pdf) const SizedBox(width: AppSpacing.sm),
          IconButton.filled(
            tooltip: 'Unduh',
            icon: const Icon(Icons.download_outlined),
            iconSize: 18,
            constraints: const BoxConstraints.tightFor(width: 40, height: 40),
            padding: EdgeInsets.zero,
            onPressed: () {
              onOpen?.call();
              downloadDoc(context, url, title: title);
            },
          ),
        ]),
      ),
    );
  }
}
