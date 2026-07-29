// Relay Gemini untuk JDIH Kota Kendari.
//
// Masalah yang diobati: ada perangkat di jaringan server (ISP/firewall) yang
// memutus koneksi HTTPS begitu menganggur sekitar 1 detik. Gemini butuh 1-9
// detik untuk menjawab, jadi hampir semua panggilan mati sebelum jawabannya
// datang — terbukti dari uji /uji: respons instan lolos ~75%, respons yang
// menunggu 1,5 detik lolos ~17%, padahal host dan TLS-nya sama persis.
//
// Cara kerjanya: respons dikirim streaming dan diisi baris kosong tiap 250ms
// selama menunggu Google, sehingga koneksinya tidak pernah menganggur. Setelah
// jawaban Gemini tiba, JSON-nya ditulis apa adanya lalu stream ditutup.
// json_decode di sisi Laravel mengabaikan spasi/newline di depan, jadi detak
// itu tidak perlu dibersihkan.
//
// Kunci Gemini tetap milik Laravel (diteruskan lewat header x-goog-api-key).

const UPSTREAM = 'https://generativelanguage.googleapis.com';
const DETAK_MS = 250;

export default {
    async fetch(request, env, ctx) {
        if (request.method !== 'POST') {
            return new Response('ok', { status: 200 });
        }
        // Bukan relay terbuka: hanya pemegang token yang boleh lewat.
        // trim(): `wrangler secret put` dari stdin ikut menyimpan newline penutup.
        if (request.headers.get('x-relay-token') !== (env.RELAY_TOKEN ?? '').trim()) {
            return new Response('Forbidden', { status: 403 });
        }

        // Body dibaca utuh dulu, bukan diteruskan sebagai stream: meneruskan
        // request.body apa adanya kadang sampai ke Google dalam keadaan kosong
        // dan dijawab 400.
        const body = await request.text();
        const url = new URL(request.url);

        const enc = new TextEncoder();

        // start() sengaja tidak async: stream harus mulai mengalir seketika,
        // bukan setelah Google menjawab — justru menunggu itulah yang diputus.
        const stream = new ReadableStream({
            start(controller) {
                const detak = setInterval(() => {
                    try { controller.enqueue(enc.encode('\n')); } catch { /* sudah ditutup */ }
                }, DETAK_MS);

                const kerja = (async () => {
                    const t0 = Date.now();
                    let isi;
                    try {
                        const res = await Promise.race([
                            fetch(UPSTREAM + url.pathname + url.search, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'x-goog-api-key': request.headers.get('x-goog-api-key') ?? '',
                                },
                                body,
                            }),
                            // Batas keras: tanpa ini, fetch yang menggantung membuat
                            // stream tidak pernah ditutup dan klien menunggu selamanya.
                            // Jawaban sehat teramati 1,5-15 detik; lewat itu lebih
                            // baik menyerah cepat daripada menahan pengguna.
                            new Promise((_, tolak) => setTimeout(() => tolak(new Error('batas 18s ke Google')), 18000)),
                        ]);
                        isi = await res.text();
                    } catch (e) {
                        // Bentuknya disamakan dengan error Google supaya sisi
                        // Laravel cukup punya satu cara membaca kegagalan.
                        isi = JSON.stringify({ error: { code: 502, message: `relay: ${e} (${Date.now() - t0}ms)` } });
                    }

                    clearInterval(detak);
                    try {
                        controller.enqueue(enc.encode(isi));
                        controller.close();
                    } catch { /* klien sudah pergi */ }
                })();

                ctx.waitUntil(kerja);
            },
        });

        return new Response(stream, {
            status: 200,
            headers: { 'Content-Type': 'application/json' },
        });
    },
};
