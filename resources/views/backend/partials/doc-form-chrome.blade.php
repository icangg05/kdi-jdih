@push('link')
	<style>
		/* ── Chrome form dokumen backend — scoped ke .doc-form, mengikuti DESIGN.md ── */
		.doc-form {
			--ink:#1e293b; --muted:#475569; --soft:#64748b;
			--line:#d8dde3; --hair:#e8ecf0; --panel:#f8fafc;
			--accent:#b3241d; --accent-deep:#93190f; --accent-soft:#fdecea;
		}
		.doc-form { color:var(--ink); }

		/* Kop dokumen — satu-satunya permukaan berornamen */
		.doc-form .doc-hero {
			position:relative; overflow:hidden; background:#fff;
			border:1px solid var(--line); border-radius:8px;
			padding:20px 24px; margin-bottom:16px;
			box-shadow:0 1px 3px rgba(15,23,42,.06);
		}
		/* garis diagonal tipis, memudar ke kiri */
		.doc-form .doc-hero::before {
			content:''; position:absolute; top:0; right:0; bottom:0; width:38%;
			background-image:repeating-linear-gradient(115deg, var(--hair) 0 1px, transparent 1px 9px);
			-webkit-mask-image:linear-gradient(270deg, #000, transparent);
			mask-image:linear-gradient(270deg, #000, transparent);
			pointer-events:none;
		}
		.doc-form .doc-hero > * { position:relative; }
		.doc-form .doc-hero .head { display:flex; align-items:flex-start; gap:14px; }
		.doc-form .doc-hero .mark {
			flex:0 0 auto; width:38px; height:38px; border-radius:8px;
			background:var(--accent-soft); color:var(--accent);
			display:flex; align-items:center; justify-content:center; font-size:17px;
		}
		.doc-form .doc-hero h2 {
			margin:0; font-size:19px; font-weight:700; line-height:1.35;
			letter-spacing:-.01em; color:var(--ink); text-wrap:balance;
		}
		.doc-form .doc-hero .sub { margin:6px 0 0; color:var(--muted); font-size:13px; line-height:1.6; max-width:70ch; }
		.doc-form .doc-hero .sub .req { color:var(--accent); font-weight:700; }
		.doc-form .doc-hero .chips { margin-top:14px; padding-top:13px; border-top:1px solid var(--hair); display:flex; gap:7px; flex-wrap:wrap; }
		.doc-form .chip {
			display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600;
			padding:4px 10px; border-radius:6px; background:var(--panel); color:var(--muted);
			border:1px solid var(--hair); font-variant-numeric:tabular-nums;
		}
		.doc-form .chip b { font-weight:700; color:var(--ink); }
		.doc-form .chip-id { background:var(--accent-soft); border-color:#f7d5d1; color:var(--accent); }

		/* Ringkasan error */
		.doc-form .doc-errors {
			background:#fff; border:1px solid #f0c4bf; border-radius:8px;
			padding:14px 16px; margin-bottom:16px; box-shadow:0 1px 3px rgba(15,23,42,.06);
		}
		.doc-form .doc-errors h4 { margin:0 0 8px; font-size:14px; font-weight:700; color:var(--accent); }
		.doc-form .doc-errors ul { margin:0; padding-left:18px; color:var(--muted); font-size:13px; line-height:1.7; }
		.doc-form .doc-errors a { color:var(--accent); text-decoration:underline; }

		/* Panel seksi */
		.doc-form .box { border:1px solid var(--line); border-top:none; border-radius:8px; box-shadow:0 1px 3px rgba(15,23,42,.06); margin-bottom:16px; }
		.doc-form .box.box-solid > .box-header {
			background:#fff; color:var(--ink); border-bottom:1px solid var(--hair);
			padding:15px 20px; border-radius:8px 8px 0 0;
		}
		.doc-form .box-header .sec { display:flex; align-items:center; gap:11px; }
		.doc-form .box-header .sec > i { color:var(--soft); font-size:15px; width:18px; text-align:center; }
		.doc-form .box-header .sec b { display:block; font-size:15px; font-weight:700; letter-spacing:-.01em; }
		.doc-form .box-header .sec .hint { display:block; font-size:12.5px; font-weight:400; color:var(--muted); margin-top:2px; }
		.doc-form .box-header .sec .rule { flex:1 1 auto; height:1px; background:var(--hair); }
		.doc-form .box-body { padding:16px 16px 8px; }

		/* Baris field */
		.doc-form .box-body .form-group {
			margin:0 0 6px; padding:9px 12px; border-radius:6px;
			transition:background .16s ease, box-shadow .16s ease;
		}
		.doc-form .box-body .form-group:hover { background:var(--panel); }
		.doc-form .box-body .form-group:focus-within { background:var(--panel); box-shadow:inset 0 0 0 1px var(--line); }
		.doc-form .box-body .form-group.has-error { background:#fdf6f5; box-shadow:inset 0 0 0 1px #f0c4bf; }
		.doc-form .control-label {
			font-size:13px; font-weight:600; color:var(--muted);
			padding-top:8px; text-align:left; line-height:1.45;
		}
		.doc-form .box-body .form-group:focus-within .control-label { color:var(--ink); }
		/* asterisk abu-abu pada field opsional menyesatkan — sembunyikan */
		.doc-form .control-label span[style*="gray"] { display:none; }
		.doc-form .control-label span[style*="red"] { color:var(--accent) !important; margin-left:1px; }

		/* Kontrol */
		.doc-form .form-control {
			border-color:#cbd5e1; border-radius:6px; box-shadow:none; color:var(--ink);
			transition:border-color .16s ease, box-shadow .16s ease;
		}
		.doc-form .form-control::placeholder { color:var(--soft); }
		.doc-form .form-control:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(179,36,29,.13); }
		.doc-form input[type=number].form-control, .doc-form .krajee-datepicker { font-variant-numeric:tabular-nums; }
		.doc-form .input-group-addon { background:var(--panel); border-color:#cbd5e1; color:var(--soft); }
		.doc-form .select2-container--default .select2-selection--single { border-color:#cbd5e1; border-radius:6px; }
		.doc-form .select2-container--default.select2-container--focus .select2-selection--single,
		.doc-form .select2-container--default.select2-container--open .select2-selection--single {
			border-color:var(--accent); box-shadow:0 0 0 3px rgba(179,36,29,.13);
		}
		.doc-form .file-caption-main .form-control:focus,
		.doc-form .btn-file:focus-within { outline:2px solid var(--accent); outline-offset:2px; }
		.doc-form .help-block-error { font-size:12.5px; color:var(--accent); margin:6px 0 0; }
		.doc-form .btn-dark { background:var(--panel); border:1px solid var(--line); color:var(--muted); border-radius:6px; }
		.doc-form .btn-dark:hover { color:var(--ink); border-color:#cbd5e1; }

		/* Terjemahan otomatis */
		.doc-form .doc-translate {
			background:#fff; border:1px solid var(--line); border-radius:8px;
			padding:14px 20px; margin-bottom:16px; box-shadow:0 1px 3px rgba(15,23,42,.06);
		}
		.doc-form .doc-translate .form-group { margin:0 !important; }
		.doc-form .doc-translate label { color:var(--ink); font-size:13.5px; }
		.doc-form .doc-translate .help-block { color:var(--muted); font-size:12.5px; }

		/* Bar aksi */
		.doc-form .doc-actions {
			position:sticky; bottom:0; z-index:2;
			display:flex; align-items:center; gap:10px; flex-wrap:wrap;
			background:#fff; border:1px solid var(--line); border-radius:8px;
			padding:12px 16px; box-shadow:0 -2px 14px rgba(15,23,42,.07);
		}
		.doc-form .doc-actions .note { margin-left:auto; color:var(--muted); font-size:12.5px; }
		.doc-form .doc-actions .btn {
			border-radius:6px; font-weight:600; font-size:13.5px; padding:8px 20px; border:1px solid transparent;
			transition:background .16s ease, border-color .16s ease, color .16s ease, box-shadow .16s ease, transform .1s ease;
		}
		.doc-form .doc-actions .btn:focus { outline:2px solid var(--accent); outline-offset:2px; }
		.doc-form .btn-simpan { background:var(--accent); border-color:var(--accent); color:#fff; }
		.doc-form .btn-simpan:hover, .doc-form .btn-simpan:focus { background:var(--accent-deep); border-color:var(--accent-deep); color:#fff; }
		.doc-form .btn-simpan:active { transform:translateY(1px); }
		.doc-form .btn-simpan[disabled] { background:var(--soft); border-color:var(--soft); cursor:progress; transform:none; }
		.doc-form .btn-batal { background:#fff; border-color:#cbd5e1; color:var(--muted); }
		.doc-form .btn-batal:hover, .doc-form .btn-batal:focus { background:var(--panel); color:var(--ink); }

		@media (max-width:767px) {
			.doc-form .control-label { padding-top:0; margin-bottom:5px; }
			.doc-form .doc-actions .note { display:none; }
			.doc-form .doc-actions .btn { flex:1 1 auto; }
		}
		@media (prefers-reduced-motion: reduce) {
			.doc-form *, .doc-form *::before, .doc-form *::after { transition:none !important; animation:none !important; }
		}
	</style>
@endpush

@push('script')
	<script>
		$(function() {
			const $form = $('.doc-form form').first();
			if (!$form.length) return;

			const $btn = $form.find('.btn-simpan');
			const $note = $form.find('.doc-note');
			const noteIdle = $note.text();
			let dirty = false;

			$form.on('input change', ':input', function() {
				if (dirty) return;
				dirty = true;
				$note.text('Ada perubahan yang belum disimpan.');
			});

			$form.on('submit', function() {
				dirty = false;
				if ($btn.prop('disabled')) return false;
				$btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan…');
			});

			$(window).on('beforeunload', function() {
				if (dirty) return 'Perubahan belum disimpan.';
			});

			$('.doc-errors').focus();
		});
	</script>
@endpush
