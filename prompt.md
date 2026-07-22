# Prompt redesign halaman frontend

Redesign [nama komponen, mis. resources/views/components/frontend/peraturan-section.blade.php]
di halaman depan (Beranda) situs JDIH.

Gunakan skill: redesign-existing-projects + impeccable (audit)
dan design-taste-frontend + high-end-visual-design (arah gaya).

Alur:
1. Audit dulu — pola generik, hierarki, spacing, kontras, dan state yang hilang (loading/empty/hover). Pemetaan audit mencakup file target beserta semua komponen turunan yang di-include/di-render di dalamnya (@include, <x-...>, partial, @livewire). Beri ringkasan sebelum mengubah.
2. Terapkan redesign dengan rasa premium tapi tetap formal & kredibel (situs hukum pemerintah, bukan startup).

Batasan wajib:
- Tetap pakai stack yang ada (Tailwind v4 + Alpine + AOS) — jangan migrasi.
- Pakai palet brand saja: primary #ff891e (oranye) & accent #015BA5 (biru). Buang warna liar (ungu/hijau/biru-generik).
- Jangan ubah route, query, name input, ID, atau logika JS/Livewire — hanya markup tampilan + CSS.
- Scope pengerjaan = file target + komponen turunan yang dirender di dalamnya. Kalau tampilan file target bergantung pada sub-komponen (mis. card, badge, tombol, partial), sub-komponen itu wajib ikut dicek dan boleh diredesign agar hasilnya konsisten. Namun:
  - Sebelum menyentuh komponen turunan, sebutkan dulu daftarnya di ringkasan audit.
  - Jika sebuah komponen turunan dipakai bersama oleh halaman/komponen lain (shared), konfirmasi dulu sebelum mengubahnya — karena perubahan bisa berdampak ke tempat lain.
  - Jangan melebar ke komponen lain yang tidak dirender oleh file target tanpa konfirmasi.



# Prompt redesign halaman backend

Redesign halaman [path halaman, mis. backend/pembentukan-puu/show] 
menggunakan skill redesign-existing-projects, impeccable, 
high-end-visual-design, dan design-taste-frontend.

Alurnya:
1. Audit dulu (impeccable + redesign-existing): list masalah desain, 
   hierarki, spacing, state yang hilang, dan pola generik.
2. Redesign dengan rasa high-end/agency (design-taste + high-end): 
   tipografi berkarakter, spacing compact, depth & motion halus, 
   hover/active state, komponen yang tidak terlihat template.

Batasan wajib:
- Tetap pakai stack yang ada (AdminLTE / Bootstrap 3), jangan migrasi framework.
- Jangan pasang font/CDN eksternal (server offline) — pakai yang sudah ada.
- Jangan ubah route/fungsi/logic.


# Prompt redesign secara umum

/redesign-existing-projects /design-taste-frontend /high-end-visual-design /impeccable redesign footer, buat lebih menarik dengan menambahkan garis, bentuk, ornamen, animasi, ataupun lainnya.


# Prompt redesign mobile app

/redesign-existing-projects /design-taste-frontend /high-end-visual-design /impeccable

Redesign tampilan UI aplikasi Flutter saya ini agar terasa lebih bold, eye-catching, 
dan hidup, tanpa mengubah fungsionalitas atau state management yang sudah ada.

Scope:
- Semua screen utama aplikasi (Home, list/detail, profil, dan screen inti lainnya)

Style/mood yang diinginkan:
- Bold & eye-catching — berani main warna (kontras kuat, bisa gradient atau color-block), 
  bentuk-bentuk geometris yang tegas, jangan takut ambil ruang visual
- Tetap terasa premium & rapi, bukan berantakan — bold di sini artinya berani secara visual, 
  bukan norak

Elemen visual yang ingin ditambahkan/diperkuat:
1. Garis & divider dekoratif dengan warna aksen untuk memisahkan section
2. Bentuk geometris (shape) sebagai elemen background, card, atau accent visual
3. Ornamen/pattern yang punya karakter kuat tapi tidak mengganggu keterbacaan konten
4. Micro-animation & transisi yang terasa energik (page transition, button press feedback, 
   loading state, scroll-based animation)

Batasan teknis:
- Pertahankan struktur widget dan logic yang sudah ada, cukup ubah layer presentasi/UI
- Gunakan pendekatan idiomatik Flutter (Material/Cupertino sesuai yang dipakai project)
- Bangun satu color palette & design token (warna aksen, radius, spacing) yang konsisten 
  dipakai di semua screen — jangan tiap screen punya gaya sendiri-sendiri
- Pastikan tetap responsif di berbagai ukuran layar dan kontras warna tetap accessible 
  (teks tetap terbaca)

Cara kerja yang diharapkan:
1. Audit dulu tampilan yang ada sekarang di tiap screen utama (identifikasi bagian yang 
   flat/kurang menarik)
2. Usulkan design language baru — palette warna, shape language, style animasi — sebelum eksekusi
3. Implementasikan perubahan screen per screen, sambil jaga konsistensi ke design language 
   yang sudah disepakati
