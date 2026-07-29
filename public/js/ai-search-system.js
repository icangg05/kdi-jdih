// AI SEARCH SYSTEM — modal "Tanya AI" (mode percakapan)
(function () {
    'use strict';

    const ENDPOINT = '/ai-search';
    const MAX_HISTORY = 12; // giliran yang ikut dikirim ke server (server memotong lagi)

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    // Markdown seadanya: **tebal**, baris "- " jadi <ul>, sisanya <br>.
    // Selalu di-escape lebih dulu, jadi tag dari model tidak pernah dieksekusi.
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
            html += line.trim() ? `${line}<br>` : '';
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

            this.onKeydown = (e) => {
                if (e.key === 'Escape' && this.modal.classList.contains('show')) this.closeModal();
            };

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

            this.modal.querySelectorAll('[data-ai-close], #closeAiModal').forEach((el) =>
                el.addEventListener('click', () => this.closeModal())
            );

            document.addEventListener('keydown', this.onKeydown);
        }

        /* ---------- percakapan ---------- */

        async ask(rawQuery) {
            const query = (rawQuery || '').trim();
            if (this.busy) return;

            this.openModal();

            if (!query) {
                this.modalInput?.focus();
                return;
            }

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
                this.fillAnswer(pending, data.explanation || '', data.documents || []);

                this.history.push({ role: 'user', text: query });
                this.history.push({ role: 'ai', text: data.explanation || '' });
            } catch (err) {
                console.error('AI Search error:', err);
                this.fillError(pending);
            } finally {
                this.busy = false;
                if (this.modalSend) this.modalSend.disabled = false;
                this.modalInput?.focus();
            }
        }

        reset() {
            this.history = [];
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
            this.scrollToBottom();
        }

        /* ---------- render bagian pesan ---------- */

        appendUser(text) {
            const el = document.createElement('div');
            el.className = 'ai-msg ai-msg--user';
            el.innerHTML = `<div class="ai-bubble"><i class="fas fa-user"></i><span>${esc(text)}</span></div>`;
            this.thread?.appendChild(el);
            this.scrollToBottom();
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
            this.scrollToBottom();
            return el;
        }

        fillAnswer(el, explanation, docs) {
            el.querySelector('.ai-answer__body').innerHTML =
                mdLite(explanation) || 'Maaf, saya belum bisa menjawab pertanyaan itu.';

            if (docs.length) el.insertAdjacentHTML('beforeend', this.docsHtml(docs));
            this.scrollToBottom();
        }

        fillError(el) {
            el.querySelector('.ai-answer__body').textContent =
                'Gagal menghubungi layanan AI. Silakan coba lagi sebentar lagi.';
            this.scrollToBottom();
        }

        docsHtml(docs) {
            const cards = docs.map((doc) => {
                const statusClass = /berlaku/i.test(doc.status || '') ? 'ai-tag--ok' : 'ai-tag--off';
                return `
                <a href="${esc(doc.url)}" class="ai-doc">
                    <div class="ai-doc__title">${esc(doc.title || 'Tanpa Judul')}</div>
                    <div class="ai-doc__meta">
                        ${doc.type ? `<span class="ai-tag">${esc(doc.type)}</span>` : ''}
                        ${doc.year ? `<span class="ai-tag ai-tag--year">Tahun ${esc(doc.year)}</span>` : ''}
                        ${doc.status ? `<span class="ai-tag ${statusClass}">${esc(doc.status)}</span>` : ''}
                    </div>
                    ${doc.description ? `<p class="ai-doc__desc">${esc(doc.description)}</p>` : ''}
                    <div class="ai-doc__foot">
                        <span class="ai-relevance">
                            <span class="ai-relevance__bar"><span style="width:${Number(doc.accuracy) || 0}%"></span></span>
                            ${Number(doc.accuracy) || 0}% relevan
                        </span>
                        <span class="ai-doc__cta">Lihat dokumen <i class="fas fa-arrow-right"></i></span>
                    </div>
                </a>`;
            }).join('');

            return `<div class="ai-docs__head"><h3>Dokumen Terkait</h3>
                        <span class="ai-chip">${docs.length} dokumen</span>
                    </div>
                    <div class="ai-docs">${cards}</div>`;
        }

        scrollToBottom() {
            if (this.body) this.body.scrollTop = this.body.scrollHeight;
        }

        /* ---------- modal ---------- */

        openModal() {
            this.modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(() => { this.modalInput?.focus(); this.scrollToBottom(); }, 120);
        }

        closeModal() {
            this.modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        destroy() {
            document.removeEventListener('keydown', this.onKeydown);
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
