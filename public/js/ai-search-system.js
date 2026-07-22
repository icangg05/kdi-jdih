// AI SEARCH SYSTEM — modal "Tanya AI"
(function () {
    'use strict';

    const ENDPOINT = '/ai-search';

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[c]));

    class AiSearchSystem {
        constructor() {
            this.modal = document.getElementById('aiSearchModal');
            if (!this.modal) return;

            this.resultsContainer = document.getElementById('aiResults');
            this.resultsCount = document.getElementById('aiResultsCount');
            this.queryText = document.getElementById('aiQueryText');
            this.explanationContent = document.getElementById('aiExplanationContent');
            this.modalInput = document.getElementById('aiModalInput');
            this.modalSend = document.getElementById('aiModalSend');

            // input tersembunyi di hero (mirror dari kolom pencarian utama)
            this.heroInput = document.getElementById('aiSearchInput');
            this.heroBtn = document.getElementById('performAiSearch');

            this.onKeydown = (e) => {
                if (e.key === 'Escape' && this.modal.classList.contains('show')) this.closeModal();
            };

            this.bind();
        }

        bind() {
            this.heroBtn?.addEventListener('click', () => this.ask(this.heroInput?.value));
            this.modalSend?.addEventListener('click', () => this.ask(this.modalInput?.value));
            this.modalInput?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); this.ask(this.modalInput.value); }
            });

            this.modal.querySelectorAll('[data-ai-close], #closeAiModal').forEach((el) =>
                el.addEventListener('click', () => this.closeModal())
            );

            document.addEventListener('keydown', this.onKeydown);
        }

        async ask(rawQuery) {
            const query = (rawQuery || '').trim();

            if (query.length < 3) {
                this.openModal();
                this.modalInput?.focus();
                this.renderEmptyPrompt();
                return;
            }

            this.openModal();
            this.renderLoading(query);
            if (this.modalInput) { this.modalInput.value = ''; this.modalSend.disabled = true; }

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
                    body: JSON.stringify({ query })
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                this.render(query, await res.json());
            } catch (err) {
                console.error('AI Search error:', err);
                this.renderError();
            } finally {
                if (this.modalSend) this.modalSend.disabled = false;
            }
        }

        renderEmptyPrompt() {
            if (this.queryText) this.queryText.innerHTML = '';
            if (this.resultsCount) this.resultsCount.textContent = '';
            if (this.explanationContent) {
                this.explanationContent.textContent =
                    'Tulis pertanyaan Anda (minimal 3 huruf), misalnya "retribusi parkir" atau "izin mendirikan bangunan".';
            }
            if (this.resultsContainer) this.resultsContainer.innerHTML = '';
        }

        renderLoading(query) {
            if (this.queryText) this.queryText.innerHTML = `<i class="fas fa-user"></i><span>${esc(query)}</span>`;
            if (this.resultsCount) this.resultsCount.textContent = '';
            if (this.explanationContent) {
                this.explanationContent.innerHTML =
                    '<span class="ai-relevance"><i class="fas fa-spinner fa-spin"></i> AI sedang membaca dokumen JDIH...</span>';
            }
            if (this.resultsContainer) {
                this.resultsContainer.innerHTML = '<div class="ai-skeleton"></div>'.repeat(3);
            }
        }

        render(query, data) {
            const docs = data.documents || [];

            if (this.queryText) this.queryText.innerHTML = `<i class="fas fa-user"></i><span>${esc(query)}</span>`;
            if (this.explanationContent) this.explanationContent.textContent = data.explanation || '';
            if (this.resultsCount) this.resultsCount.textContent = docs.length ? `${docs.length} dokumen` : '';

            if (!this.resultsContainer) return;

            if (!docs.length) {
                this.resultsContainer.innerHTML = `
                    <div class="ai-state ai-state--center">
                        <i class="fas fa-file-circle-question fa-2x"></i>
                        <p>Tidak ada dokumen yang cocok.</p>
                        <p>Coba kata kunci lain yang lebih umum.</p>
                    </div>`;
                return;
            }

            this.resultsContainer.innerHTML = docs.map((doc) => {
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
        }

        renderError() {
            if (this.explanationContent) {
                this.explanationContent.textContent = 'Gagal menghubungi layanan AI. Silakan coba lagi sebentar lagi.';
            }
            if (this.resultsCount) this.resultsCount.textContent = '';
            if (this.resultsContainer) {
                this.resultsContainer.innerHTML = `
                    <div class="ai-state ai-state--center">
                        <i class="fas fa-triangle-exclamation fa-2x"></i>
                        <p>Koneksi terputus atau layanan sedang sibuk.</p>
                    </div>`;
            }
        }

        openModal() {
            this.modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            setTimeout(() => this.modalInput?.focus(), 120);
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
