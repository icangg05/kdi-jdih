// AI SEARCH SYSTEM — modal "Tanya AI" (mode percakapan)
(function () {
    'use strict';

    const ENDPOINT = '/ai-search';
    const MAX_HISTORY = 12; // giliran yang ikut dikirim ke server (server memotong lagi)
    const MAX_TURNS = 10;   // pertanyaan per percakapan; server hanya mengingat 4 giliran terakhir
    // Lama mengetik mengikuti panjang jawaban, dijepit agar yang pendek tidak
    // bertele-tele dan yang panjang tidak menyandera pembaca.
    const MS_PER_CHAR = 12;
    const TYPE_MIN_MS = 800;
    const TYPE_MAX_MS = 6000;
    const TICK_MS = 30;     // jeda antar-tick; makin besar makin terasa "berdenyut"

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    // Markdown seadanya: **tebal**, baris "- " jadi <ul>, sisanya <p> (bukan <br>,
    // supaya antar paragraf ada jarak). Selalu di-escape lebih dulu, jadi tag dari
    // model tidak pernah dieksekusi.
    const mdLite = (text) => {
        const lines = esc(text).split('\n');
        let html = '', inList = false;

        for (const line of lines) {
            const item = line.match(/^\s*[-*]\s+(.*)$/);
            if (item) {
                if (!inList) { html += '<ul>'; inList = true; }
                html += `<li>${item[1]}</li>`;
                continue;
            }
            if (inList) { html += '</ul>'; inList = false; }
            html += line.trim() ? `<p>${line}</p>` : '';
        }
        if (inList) html += '</ul>';

        return html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
    };

    class AiSearchSystem {
        constructor() {
            this.modal = document.getElementById('aiSearchModal');
            if (!this.modal) return;

            this.thread = document.getElementById('aiThread');
            this.body = this.modal.querySelector('.ai-modal__body');
            this.modalInput = document.getElementById('aiModalInput');
            this.modalSend = document.getElementById('aiModalSend');
            this.newChatBtn = document.getElementById('aiNewChat');

            // input tersembunyi di hero (mirror dari kolom pencarian utama)
            this.heroInput = document.getElementById('aiSearchInput');
            this.heroBtn = document.getElementById('performAiSearch');

            this.history = [];
            this.busy = false;
            this.stick = true; // ikut turun otomatis selama pengguna masih di dasar thread
            this.lastTop = 0;  // pembanding arah gulir (lihat bind())

            this.bind();
            this.reset();
        }

        bind() {
            this.heroBtn?.addEventListener('click', () => this.ask(this.heroInput?.value));
            this.modalSend?.addEventListener('click', () => this.ask(this.modalInput?.value));
            this.modalInput?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); this.ask(this.modalInput.value); }
            });
            this.newChatBtn?.addEventListener('click', () => this.reset());

            // Hanya tombol X yang menutup — klik backdrop & Escape sengaja tidak
            // dipasang supaya percakapan tidak hilang karena salah sentuh.
            document.getElementById('closeAiModal')?.addEventListener('click', () => this.closeModal());

            // Sentuhan/klik/roda di area thread = pengguna mau membaca sendiri:
            // penempelan langsung dilepas walau posisinya masih di dasar. Mengetik
            // tetap jalan, hanya ikut-turunnya yang berhenti. Kejadian yang dimulai
            // pengguna sendiri (kirim pertanyaan) memasang ulang lewat scrollToBottom(true).
            ['pointerdown', 'wheel'].forEach((ev) =>
                this.body?.addEventListener(ev, () => { this.stick = false; }, { passive: true }));

            // Arah gulir yang membedakan pengguna dari scroll programatik: scroll
            // otomatis kita selalu turun, jadi begitu posisi bergerak NAIK itu pasti
            // pengguna — penempelan langsung dilepas, apa pun sumber gerakannya
            // (roda, sentuh, seret scrollbar, tombol panah).
            this.body?.addEventListener('scroll', () => {
                const top = this.body.scrollTop;
                if (top < this.lastTop - 2) this.stick = false;
                this.lastTop = top;

                // Menempel lagi hanya kalau pengguna benar-benar kembali ke dasar.
                const sisa = this.body.scrollHeight - top - this.body.clientHeight;
                if (sisa <= 4) this.stick = true;
            }, { passive: true });

            // Satu listener untuk semua lipatan dokumen, termasuk yang belum ada
            this.thread?.addEventListener('click', (e) => {
                const head = e.target.closest('.ai-docs__head');
                if (!head) return;
                e.preventDefault(); // <details> jangan buka/tutup seketika
                this.toggleDocs(head.parentElement);
            });
        }

        /** <details> bawaan membuka/menutup seketika. Tingginya dianimasikan
         *  supaya tidak menyentak, dan panelnya baru benar-benar ditutup setelah
         *  animasinya selesai. */
        toggleDocs(box) {
            const panel = box?.querySelector('.ai-docs');
            if (!panel || box.dataset.animating) return;

            const menutup = box.open;

            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                box.open = !menutup;
                return;
            }

            box.dataset.animating = '1';
            if (!menutup) box.open = true; // dibuka dulu agar tingginya bisa diukur

            const tinggi = panel.scrollHeight;
            panel.style.overflow = 'hidden';

            const anim = panel.animate(
                {
                    height: menutup ? [`${tinggi}px`, '0px'] : ['0px', `${tinggi}px`],
                    opacity: menutup ? [1, 0] : [0, 1],
                },
                { duration: 220, easing: 'cubic-bezier(.22,1,.36,1)' }
            );

            anim.onfinish = () => {
                box.open = !menutup;
                panel.style.overflow = '';
                delete box.dataset.animating;
                // sengaja tanpa scrollToBottom: posisi baca pengguna tidak boleh dipindah
            };
        }

        /** Fokus otomatis hanya di layar lebar — di ponsel keyboard langsung
         *  menutup separuh layar sebelum pengguna sempat membaca. */
        focusInput() {
            if (window.matchMedia('(min-width: 768px)').matches) this.modalInput?.focus();
        }

        /* ---------- percakapan ---------- */

        async ask(rawQuery) {
            const query = (rawQuery || '').trim();
            if (this.busy) return;

            this.openModal();

            if (!query || this.turns() >= MAX_TURNS) {
                this.focusInput();
                return;
            }

            // Mobile: lepas fokus (kolom modal maupun kolom hero) supaya keyboard
            // layar menutup begitu pertanyaan dikirim — kebalikan dari focusInput().
            if (!window.matchMedia('(min-width: 768px)').matches) document.activeElement?.blur();

            this.appendUser(query);
            const pending = this.appendTyping();

            this.busy = true;
            if (this.modalSend) this.modalSend.disabled = true;
            if (this.modalInput) this.modalInput.value = '';

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const res = await fetch(ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
                    },
                    body: JSON.stringify({ query, history: this.history.slice(-MAX_HISTORY) })
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);

                const data = await res.json();

                this.history.push({ role: 'user', text: query });
                this.history.push({ role: 'ai', text: data.explanation || '' });

                // ditunggu supaya tombol kirim baru aktif setelah selesai "diketik"
                await this.fillAnswer(pending, data.explanation || '', data.documents || []);
            } catch (err) {
                console.error('AI Search error:', err);
                this.fillError(pending);
            } finally {
                this.busy = false;
                if (this.modalSend) this.modalSend.disabled = false;
                this.applyLimit();
                this.focusInput();
            }
        }

        turns() {
            return this.history.filter((h) => h.role === 'user').length;
        }

        /** Percakapan panjang dihentikan, bukan dibiarkan menua: konteks yang
         *  dikirim ke model cuma 4 giliran terakhir, jadi giliran ke-11 dst.
         *  terasa "nyambung" padahal awalnya sudah terlupakan. */
        applyLimit() {
            if (this.turns() < MAX_TURNS) return;

            if (this.modalInput) {
                this.modalInput.disabled = true;
                this.modalInput.placeholder = `Batas ${MAX_TURNS} pertanyaan tercapai`;
            }
            if (this.modalSend) this.modalSend.disabled = true;

            const el = document.createElement('div');
            el.className = 'ai-msg ai-limit';
            el.innerHTML = `<i class="fas fa-circle-info"></i>
                <span>Percakapan ini sudah mencapai ${MAX_TURNS} pertanyaan.
                Mulai percakapan baru untuk bertanya lagi.</span>
                <button type="button" class="ai-limit__btn">Percakapan baru</button>`;
            el.querySelector('button').addEventListener('click', () => this.reset());
            this.thread?.appendChild(el);
            this.scrollToBottom(true);
        }

        reset() {
            this.history = [];
            clearTimeout(this.typer);

            if (this.modalInput) {
                this.modalInput.disabled = false;
                this.modalInput.placeholder = 'Tulis pertanyaan Anda...';
            }
            if (this.modalSend) this.modalSend.disabled = false;

            if (!this.thread) return;

            this.thread.innerHTML = '';
            const el = document.createElement('div');
            el.className = 'ai-msg ai-msg--ai';
            el.innerHTML = `
                <section class="ai-answer">
                    <div class="ai-answer__head"><i class="fas fa-robot"></i><span>Asisten JDIH</span></div>
                    <div class="ai-answer__body">Halo! Tanyakan apa saja seputar hukum atau peraturan Kota Kendari.
                        Saya akan menjawab sekaligus menunjukkan dokumen JDIH yang terkait.<br>
                        Contoh: <strong>retribusi parkir</strong>, <strong>syarat izin mendirikan bangunan</strong>,
                        atau <strong>apa itu peraturan daerah?</strong></div>
                </section>`;
            this.thread.appendChild(el);
            this.scrollToBottom(true);
        }

        /* ---------- render bagian pesan ---------- */

        appendUser(text) {
            const el = document.createElement('div');
            el.className = 'ai-msg ai-msg--user';
            el.innerHTML = `<div class="ai-bubble"><i class="fas fa-user"></i><span>${esc(text)}</span></div>`;
            this.thread?.appendChild(el);
            this.scrollToBottom(true);
        }

        appendTyping() {
            const el = document.createElement('div');
            el.className = 'ai-msg ai-msg--ai';
            el.innerHTML = `
                <section class="ai-answer">
                    <div class="ai-answer__head"><i class="fas fa-robot"></i><span>Asisten JDIH</span></div>
                    <div class="ai-answer__body"><span class="ai-typing"><i></i><i></i><i></i></span></div>
                </section>`;
            this.thread?.appendChild(el);
            this.scrollToBottom(true);
            return el;
        }

        async fillAnswer(el, explanation, docs) {
            const teks = explanation || 'Maaf, saya belum bisa menjawab pertanyaan itu.';

            await this.typeInto(el.querySelector('.ai-answer__body'), teks);

            if (docs.length) el.insertAdjacentHTML('beforeend', this.docsHtml(docs));
            this.scrollToBottom();
        }

        /** Efek mengetik, durasinya sepanjang jawaban (dijepit 0,8–6 detik). */
        typeInto(el, text) {
            return new Promise((resolve) => {
                if (!el) return resolve();

                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                    el.innerHTML = mdLite(text);
                    return resolve();
                }

                const durasi = Math.min(TYPE_MAX_MS, Math.max(TYPE_MIN_MS, text.length * MS_PER_CHAR));
                const step = Math.max(1, Math.ceil(text.length / (durasi / TICK_MS)));
                let i = 0;

                const tick = () => {
                    i = Math.min(text.length, i + step);
                    // buang penanda yang belum lengkap di ujung, lalu tutup sementara
                    // '**' yang belum berpasangan — bintangnya tidak boleh terlihat mentah
                    let partial = text.slice(0, i).replace(/\*{1,2}$/, '');
                    if ((partial.match(/\*\*/g) || []).length % 2) partial += '**';

                    el.innerHTML = mdLite(partial);
                    this.scrollToBottom();

                    if (i < text.length) {
                        this.typer = setTimeout(tick, TICK_MS);
                    } else {
                        resolve();
                    }
                };

                tick();
            });
        }

        fillError(el) {
            el.querySelector('.ai-answer__body').textContent =
                'Gagal menghubungi layanan AI. Silakan coba lagi sebentar lagi.';
            this.scrollToBottom();
        }

        docsHtml(docs) {
            const cards = docs.map((doc) => {
                // "Tidak Berlaku" mengandung kata "berlaku", jadi yang dicek justru
                // penyangkalannya — kalau tidak, dokumen yang sudah dicabut ikut hijau.
                const dicabut = /tidak\s*berlaku/i.test(doc.status || '');
                const statusClass = dicabut ? 'ai-tag--off' : 'ai-tag--ok';
                const statusIcon = dicabut ? 'fa-circle-xmark' : 'fa-circle-check';
                const skor = Math.max(0, Math.min(100, Number(doc.accuracy) || 0));
                const level = skor >= 70 ? 'tinggi' : (skor >= 40 ? 'sedang' : 'rendah');
                return `
                <a href="${esc(doc.url)}" target="_blank" rel="noopener" class="ai-doc">
                    <div class="ai-doc__title">${esc(doc.title || 'Tanpa Judul')}</div>
                    <div class="ai-doc__meta">
                        ${doc.type ? `<span class="ai-tag">${esc(doc.type)}</span>` : ''}
                        ${doc.year ? `<span class="ai-tag ai-tag--year">Tahun ${esc(doc.year)}</span>` : ''}
                        ${doc.status ? `<span class="ai-tag ${statusClass}"><i class="fas ${statusIcon}"></i>${esc(doc.status)}</span>` : ''}
                    </div>
                    ${doc.description ? `<p class="ai-doc__desc">${esc(doc.description)}</p>` : ''}
                    <div class="ai-doc__foot">
                        <span class="ai-relevance">
                            <span class="ai-relevance__bar" data-level="${level}"><span style="width:${skor}%"></span></span>
                            <span class="ai-relevance__val" data-level="${level}">${skor}% relevan</span>
                        </span>
                        <span class="ai-doc__cta">Lihat dokumen <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>`;
            }).join('');

            // <details> bawaan browser: tertutup secara default supaya thread tidak
            // dipenuhi daftar dokumen yang sama tiap giliran.
            return `<details class="ai-docs-box">
                        <summary class="ai-docs__head">
                            <i class="fas fa-chevron-right ai-docs__caret"></i>
                            <h3>Dokumen Terkait</h3>
                            <span class="ai-chip">${docs.length} dokumen</span>
                        </summary>
                        <div class="ai-docs">${cards}</div>
                    </details>`;
        }

        /** paksa = true dipakai untuk kejadian yang dimulai pengguna sendiri
         *  (mengirim pertanyaan, percakapan baru) — selain itu hormati posisi bacanya. */
        scrollToBottom(paksa = false) {
            if (paksa) this.stick = true;
            if (!this.stick || !this.body) return;
            this.body.scrollTop = this.body.scrollHeight;
            // dicatat di sini juga: kalau thread menyusut (percakapan baru) posisi
            // turun tanpa campur tangan pengguna, jangan terbaca sebagai gulir naik
            this.lastTop = this.body.scrollTop;
        }

        /* ---------- modal ---------- */

        openModal() {
            this.modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            // tanpa scrollToBottom: modal dibuka pada posisi terakhir yang dibaca
            setTimeout(() => this.focusInput(), 120);
        }

        closeModal() {
            this.modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        destroy() {
            clearTimeout(this.typer); // tidak ada listener di luar modal yang perlu dilepas
        }
    }

    window.AiSearchManager = {
        instance: null,
        initializeIfNeeded() {
            if (document.getElementById('aiSearchModal') && !this.instance) {
                this.instance = new AiSearchSystem();
            }
            return this.instance;
        },
        destroyInstance() {
            this.instance?.destroy();
            this.instance = null;
        }
    };

    const boot = () => AiSearchManager.initializeIfNeeded();
    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', boot) : boot();
    document.addEventListener('livewire:navigating', () => AiSearchManager.destroyInstance());
    document.addEventListener('livewire:navigated', () => setTimeout(boot, 50));

    // dipakai tombol [data-ai-search-open] di layout
    window.openAiSearchModal = function () {
        const app = AiSearchManager.initializeIfNeeded();
        app?.openModal();
        return !!app;
    };
})();
