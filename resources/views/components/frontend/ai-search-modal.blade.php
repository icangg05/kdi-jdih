<!-- MODAL TANYA AI -->
<div id="aiSearchModal" class="ai-modal" role="dialog" aria-modal="true" aria-labelledby="aiModalTitle">
    <div class="ai-modal__backdrop" data-ai-close></div>

    <div class="ai-modal__panel">
        <!-- HEADER -->
        <header class="ai-modal__header">
            <div class="ai-modal__brand">
                <span class="ai-modal__avatar"><i class="fas fa-wand-magic-sparkles"></i></span>
                <div>
                    <h2 id="aiModalTitle" class="ai-modal__title">{{ __('Tanya AI') }}</h2>
                    <p class="ai-modal__subtitle">{{ __('Asisten dokumen hukum JDIH Kota Kendari') }}</p>
                </div>
            </div>
            <div class="ai-modal__actions">
                <button type="button" id="aiNewChat" class="ai-modal__close" title="{{ __('Percakapan baru') }}"
                    aria-label="{{ __('Percakapan baru') }}">
                    <i class="fas fa-rotate-left"></i>
                </button>
                <button type="button" id="closeAiModal" class="ai-modal__close" aria-label="{{ __('Tutup') }}">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </header>

        <!-- PERCAKAPAN -->
        <div class="ai-modal__body">
            <div id="aiThread" class="ai-thread" role="log" aria-live="polite"></div>
        </div>

        <!-- INPUT LANJUTAN -->
        <footer class="ai-modal__footer">
            <div class="ai-ask">
                <i class="fas fa-comment-dots ai-ask__icon"></i>
                <input type="text" id="aiModalInput" class="ai-ask__input" autocomplete="off"
                    placeholder="{{ __('Tulis pertanyaan Anda...') }}">
                <button type="button" id="aiModalSend" class="ai-ask__btn">
                    <i class="fas fa-paper-plane"></i>
                    <span>{{ __('Kirim') }}</span>
                </button>
            </div>
            <p class="ai-modal__disclaimer">
                <i class="fas fa-circle-info"></i>
                {{ __('Jawaban dibuat otomatis oleh AI. Selalu periksa dokumen aslinya.') }}
            </p>
        </footer>
    </div>
</div>

<style>
    .ai-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: flex-end;
        justify-content: center;
        padding: 0;
    }

    .ai-modal.show {
        display: flex;
    }

    .ai-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(2, 6, 23, .6);
        backdrop-filter: blur(3px);
        animation: aiFade .2s ease;
    }

    .ai-modal__panel {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 100%;
        max-height: 92vh;
        background: #fff;
        border-radius: 4px 4px 0 0;
        box-shadow: 0 -10px 40px rgba(2, 6, 23, .35);
        animation: aiSlide .25s cubic-bezier(.22, 1, .36, 1);
        overflow: hidden;
    }

    @media (min-width: 768px) {
        .ai-modal {
            align-items: center;
            padding: 24px;
        }

        .ai-modal__panel {
            max-width: 860px;
            max-height: 86vh;
            border-radius: 4px;
            box-shadow: 0 30px 60px rgba(2, 6, 23, .4);
        }
    }

    @keyframes aiFade {
        from { opacity: 0 }
        to { opacity: 1 }
    }

    @keyframes aiSlide {
        from { opacity: 0; transform: translateY(18px) }
        to { opacity: 1; transform: translateY(0) }
    }

    /* HEADER */
    .ai-modal__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 20px;
        background: linear-gradient(120deg, var(--color-accent, #015BA5), #0b7bd0);
        color: #fff;
    }

    .ai-modal__brand {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .ai-modal__avatar {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        flex: none;
        border-radius: 4px;
        background: rgba(255, 255, 255, .16);
        border: 1px solid rgba(255, 255, 255, .25);
        font-size: 1rem;
    }

    .ai-modal__title {
        font-size: 1.05rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .ai-modal__subtitle {
        font-size: .75rem;
        opacity: .8;
        margin-top: 2px;
    }

    .ai-modal__actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .ai-modal__close {
        flex: none;
        width: 36px;
        height: 36px;
        border-radius: 4px;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        transition: background .2s;
    }

    .ai-modal__close:hover {
        background: rgba(255, 255, 255, .3);
    }

    /* BODY */
    .ai-modal__body {
        flex: 1;
        overflow-y: auto;
        padding: 18px 20px 22px;
        background: #f8fafc;
        scrollbar-width: thin;
    }

    /* THREAD PERCAKAPAN */
    .ai-thread {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .ai-msg--ai .ai-answer {
        margin-top: 0;
    }

    /* Titik-titik "sedang mengetik" */
    .ai-typing {
        display: inline-flex;
        gap: 4px;
        padding: 2px 0;
    }

    .ai-typing i {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--color-primary, #ff891e);
        animation: aiBlink 1.2s infinite ease-in-out;
    }

    .ai-typing i:nth-child(2) { animation-delay: .18s }
    .ai-typing i:nth-child(3) { animation-delay: .36s }

    @keyframes aiBlink {
        0%, 80%, 100% { opacity: .25; transform: translateY(0) }
        40% { opacity: 1; transform: translateY(-3px) }
    }

    /* Markdown ringan di jawaban AI */
    .ai-answer__body strong { font-weight: 700; color: #0f172a }
    .ai-answer__body ul { margin: 6px 0 0; padding-left: 18px; list-style: disc }
    .ai-answer__body li { margin-top: 3px }

    .ai-bubble {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        max-width: 100%;
        margin-left: auto;
        padding: 10px 14px;
        border-radius: 4px;
        background: var(--color-accent, #015BA5);
        color: #fff;
        font-size: .875rem;
        font-weight: 500;
        word-break: break-word;
    }

    .ai-bubble:not(:empty) {
        display: flex;
        width: fit-content;
    }

    .ai-answer {
        margin-top: 14px;
        padding: 16px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-left: 4px solid var(--color-primary, #ff891e);
        border-radius: 4px;
    }

    .ai-answer__head {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--color-primary, #ff891e);
    }

    .ai-answer__body {
        margin-top: 8px;
        font-size: .925rem;
        line-height: 1.65;
        color: #334155;
        white-space: pre-line;
    }

    .ai-docs__head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 14px 0 10px;
    }

    .ai-docs__head h3 {
        font-size: .95rem;
        font-weight: 700;
        color: #0f172a;
    }

    .ai-chip {
        padding: 2px 9px;
        border-radius: 4px;
        background: #e2e8f0;
        font-size: .7rem;
        font-weight: 600;
        color: #475569;
    }

    .ai-chip:empty {
        display: none;
    }

    /* KARTU DOKUMEN */
    .ai-doc {
        display: block;
        padding: 14px 16px;
        margin-bottom: 10px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        transition: border-color .2s, box-shadow .2s, transform .2s;
    }

    a.ai-doc:hover {
        border-color: var(--color-accent, #015BA5);
        box-shadow: 0 8px 20px rgba(1, 91, 165, .12);
        transform: translateY(-2px);
    }

    .ai-doc__title {
        font-size: .9rem;
        font-weight: 600;
        line-height: 1.45;
        color: #0f172a;
    }

    .ai-doc__meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: .7rem;
    }

    .ai-tag {
        padding: 3px 9px;
        border-radius: 4px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 600;
    }

    .ai-tag--year {
        background: #f1f5f9;
        color: #475569;
    }

    .ai-tag--ok {
        background: #ecfdf5;
        color: #047857;
    }

    .ai-tag--off {
        background: #fef2f2;
        color: #b91c1c;
    }

    .ai-doc__desc {
        margin-top: 8px;
        font-size: .8rem;
        line-height: 1.6;
        color: #64748b;
    }

    .ai-doc__foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 10px;
    }

    .ai-relevance {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .68rem;
        color: #94a3b8;
        white-space: nowrap;
    }

    .ai-relevance__bar {
        width: 64px;
        height: 4px;
        border-radius: 4px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .ai-relevance__bar span {
        display: block;
        height: 100%;
        border-radius: 4px;
        background: var(--color-accent, #015BA5);
    }

    .ai-doc__cta {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: .75rem;
        font-weight: 600;
        color: var(--color-accent, #015BA5);
    }

    /* STATE */
    .ai-state {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 4px;
        color: #64748b;
        font-size: .85rem;
    }

    .ai-state--center {
        flex-direction: column;
        text-align: center;
        gap: 6px;
        padding: 28px 18px;
    }

    .ai-skeleton {
        height: 62px;
        margin-bottom: 10px;
        border-radius: 4px;
        background: linear-gradient(90deg, #eef2f7 25%, #e2e8f0 37%, #eef2f7 63%);
        background-size: 400% 100%;
        animation: aiShimmer 1.3s ease infinite;
    }

    @keyframes aiShimmer {
        from { background-position: 100% 50% }
        to { background-position: 0 50% }
    }

    /* FOOTER */
    .ai-modal__footer {
        padding: 12px 20px calc(12px + env(safe-area-inset-bottom));
        background: #fff;
        border-top: 1px solid #e2e8f0;
    }

    .ai-modal__disclaimer {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
        font-size: .68rem;
        color: #94a3b8;
    }

    .ai-ask {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 6px 6px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        transition: border-color .2s, box-shadow .2s;
    }

    .ai-ask:focus-within {
        border-color: var(--color-accent, #015BA5);
        box-shadow: 0 0 0 3px rgba(1, 91, 165, .12);
    }

    .ai-ask__icon {
        color: #94a3b8;
        font-size: .85rem;
    }

    .ai-ask__input {
        flex: 1;
        min-width: 0;
        border: 0;
        outline: 0;
        background: transparent;
        font-size: .875rem;
        color: #0f172a;
        padding: 8px 0;
    }

    .ai-ask__btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 16px;
        border-radius: 4px;
        background: var(--color-primary, #ff891e);
        color: #fff;
        font-size: .8rem;
        font-weight: 600;
        transition: background .2s, transform .1s;
    }

    .ai-ask__btn:hover {
        background: var(--color-primary-hover, #ea8221);
    }

    .ai-ask__btn:active {
        transform: scale(.97);
    }

    .ai-ask__btn:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    /* DARK MODE */
    .dark .ai-modal__panel { background: #0f172a }
    .dark .ai-modal__body { background: #0b1220 }
    .dark .ai-answer,
    .dark .ai-doc,
    .dark .ai-state,
    .dark .ai-modal__footer { background: #111c33; border-color: #1e293b }
    .dark .ai-answer__body { color: #cbd5e1 }
    .dark .ai-answer__body strong { color: #f1f5f9 }
    .dark .ai-docs__head h3,
    .dark .ai-doc__title,
    .dark .ai-ask__input { color: #e2e8f0 }
    .dark .ai-chip { background: #1e293b; color: #cbd5e1 }
    .dark .ai-tag--year { background: #1e293b; color: #cbd5e1 }
    .dark .ai-relevance__bar { background: #1e293b }
    .dark .ai-ask { border-color: #334155 }
    .dark .ai-skeleton { background: linear-gradient(90deg, #111c33 25%, #1e293b 37%, #111c33 63%); background-size: 400% 100% }

    @media (prefers-reduced-motion: reduce) {
        .ai-modal__panel, .ai-modal__backdrop, .ai-skeleton { animation: none }
        a.ai-doc:hover { transform: none }
    }
</style>
