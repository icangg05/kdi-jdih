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

/// Unduh ke perangkat tanpa keluar aplikasi.
///
/// Android/web: diserahkan ke DownloadManager/browser, file masuk folder Download
/// dan progresnya tampil di notifikasi sistem — tanpa izin storage tambahan.
/// iOS tidak punya folder Download publik, jadi memakai dialog "Simpan ke File".
Future<void> downloadDoc(BuildContext context, String url) async {
  final messenger = ScaffoldMessenger.of(context);
  final name = docFileName(url);
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

/// Pratinjau dokumen di dalam aplikasi.
class DocPreviewScreen extends StatelessWidget {
  const DocPreviewScreen({super.key, required this.url, required this.title});
  final String url, title;

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(
          title: Text(title, maxLines: 1, overflow: TextOverflow.ellipsis),
          actions: [
            IconButton(
              tooltip: 'Unduh',
              icon: const Icon(Icons.download_outlined),
              onPressed: () => downloadDoc(context, url),
            ),
          ],
        ),
        body: PdfViewer.uri(
          Uri.parse(url),
          params: PdfViewerParams(
            backgroundColor: C.lightSubtle,
            loadingBannerBuilder: (_, done, total) => Center(
              child: CircularProgressIndicator(
                  value: (total ?? 0) > 0 ? done / total! : null),
            ),
            errorBannerBuilder: (_, error, __, ___) => ErrorRetry(
              'Dokumen tidak dapat ditampilkan.\n$error',
              onRetry: () => downloadDoc(context, url),
              retryLabel: 'Unduh saja',
            ),
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
                Text(docFileName(url),
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
              onPressed: () {
                onOpen?.call();
                Navigator.push(
                    context,
                    MaterialPageRoute(
                        builder: (_) =>
                            DocPreviewScreen(url: url, title: title)));
              },
            ),
          IconButton.filled(
            tooltip: 'Unduh',
            icon: const Icon(Icons.download_outlined),
            onPressed: () {
              onOpen?.call();
              downloadDoc(context, url);
            },
          ),
        ]),
      ),
    );
  }
}
