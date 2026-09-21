@props(['label', 'key', 'value' => '', 'required' => false, 'placeholder' => '', 'height' => 320])

<div class="form-group field-{{ $key }} @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}-editor">
    {{ $label }}
    <span style="color: {{ $required ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    <div class="q-shell">
      <div class="q-sticky">
        <div id="{{ $key }}-toolbar">
          <span class="ql-formats">
            <select class="ql-header">
              <option value="2">Judul</option>
              <option value="3">Subjudul</option>
              <option selected>Paragraf</option>
            </select>
          </span>
          <span class="ql-formats">
            <button class="ql-bold" type="button"></button>
            <button class="ql-italic" type="button"></button>
            <button class="ql-underline" type="button"></button>
            <button class="ql-strike" type="button"></button>
          </span>
          <span class="ql-formats">
            <select class="ql-color"></select>
            <select class="ql-background"></select>
          </span>
          <span class="ql-formats">
            <select class="ql-align"></select>
          </span>
          <span class="ql-formats">
            <button class="ql-list" value="ordered" type="button"></button>
            <button class="ql-list" value="bullet" type="button"></button>
            <button class="ql-indent" value="-1" type="button"></button>
            <button class="ql-indent" value="+1" type="button"></button>
            <button class="ql-blockquote" type="button"></button>
            <button class="ql-code-block" type="button"></button>
          </span>
          <span class="ql-formats">
            <button class="ql-link" type="button"></button>
            <button class="ql-image" type="button"></button>
          </span>
          <span class="ql-formats">
            <button class="ql-clean" type="button"></button>
          </span>
        </div>
        <div id="{{ $key }}-imgbar" class="q-imgbar" hidden>
          <label for="{{ $key }}-imgw">Lebar gambar</label>
          <input type="range" id="{{ $key }}-imgw" min="10" max="100" step="5">
          <output for="{{ $key }}-imgw"></output>
          <button type="button">Ukuran asli</button>
        </div>
      </div>
      <div id="{{ $key }}-editor" style="min-height: {{ $height }}px">{!! old($key, $value) !!}</div>
    </div>

    <textarea id="{{ $key }}" name="{{ $key }}" class="sr-only q-sink" tabindex="-1" aria-hidden="true">{{ old($key, $value) }}</textarea>

    @error($key)
      <p class="help-block help-block-error">{{ $message }}</p>
    @enderror
  </div>
</div>

@push('link')
  <link rel="stylesheet" href="{{ asset('assets/backend/quill/quill.snow.css') }}">
  <style>
    /* Toolbar menempel di atas layar selama editor masih terlihat. overflow: clip (bukan
       hidden) karena hidden menjadikan elemen scroll container dan mematikan sticky —
       termasuk .wrapper AdminLTE; flow-root menjaga BFC yang tadinya dibuat hidden. */
    .wrapper { overflow: clip; display: flow-root; }
    .q-shell .q-sticky { position: sticky; top: 0; z-index: 3; }
    .q-shell { border: 1px solid #cbd5e1; border-radius: 6px; overflow: clip; background: #fff; transition: border-color .16s ease, box-shadow .16s ease; }
    .q-shell:focus-within { border-color: #b3241d; box-shadow: 0 0 0 3px rgba(179, 36, 29, .13); }
    .q-shell .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #e8ecf0; background: #f8fafc; padding: 7px 8px; }
    .q-shell .ql-container.ql-snow { border: none; font-size: 14px; font-family: inherit; }
    .q-shell .ql-editor { min-height: inherit; line-height: 1.7; color: #1e293b; padding: 14px 16px; }
    .q-shell .ql-editor.ql-blank::before { color: #64748b; font-style: normal; left: 16px; }
    .q-shell .ql-snow .ql-stroke { stroke: #475569; }
    .q-shell .ql-snow .ql-fill { fill: #475569; }
    .q-shell .ql-snow .ql-picker { color: #475569; }
    .q-shell .ql-snow button:hover .ql-stroke, .q-shell .ql-snow button.ql-active .ql-stroke,
    .q-shell .ql-snow .ql-picker-label:hover .ql-stroke { stroke: #b3241d; }
    .q-shell .ql-snow button:hover .ql-fill, .q-shell .ql-snow button.ql-active .ql-fill { fill: #b3241d; }
    .q-shell .ql-snow button:focus-visible { outline: 2px solid #b3241d; outline-offset: 1px; }
    .q-shell .ql-editor img { cursor: pointer; }
    .q-shell .q-imgbar { display: flex; align-items: center; gap: 10px; padding: 6px 12px; border-bottom: 1px solid #e8ecf0; background: #fff; font-size: 12.5px; color: #475569; }
    .q-shell .q-imgbar[hidden] { display: none; }
    .q-shell .q-imgbar label { margin: 0; font-weight: 600; }
    .q-shell .q-imgbar input { flex: 1; max-width: 240px; accent-color: #b3241d; }
    .q-shell .q-imgbar output { min-width: 3.5em; font-variant-numeric: tabular-nums; }
    .q-shell .q-imgbar button { border: 1px solid #cbd5e1; background: #fff; border-radius: 4px; padding: 2px 9px; font-size: 12px; }
    .has-error .q-shell { border-color: #b3241d; }
    .q-sink { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0; }
  </style>
@endpush

@push('script')
  <script src="{{ asset('assets/backend/quill/quill.js') }}"></script>
  <script>
    (function() {
      var sink = document.getElementById('{{ $key }}');
      var uploading = 0;

      // Gambar diupload ke server, bukan disimpan sebagai base64 (meluapkan kolom TEXT).
      // Kolom baru yang memakai editor ini wajib didaftarkan di EditorImages::COLUMNS.
      function upload(blob) {
        var maxKb = {{ \App\Services\EditorImages::MAX_KB }};
        if (blob.size > maxKb * 1024) {
          return Promise.reject(new Error('Ukuran gambar maksimal ' + maxKb / 1024 + ' MB, sedangkan file ini ' +
            (blob.size / 1048576).toFixed(1) + ' MB. Perkecil atau kompres gambarnya dulu.'));
        }
        var body = new FormData();
        body.append('image', blob, blob.name || 'gambar');
        body.append('_token', sink.form.querySelector('[name=_token]').value);
        uploading++;
        return fetch(@json(route('backend.editor.upload-image')), { method: 'POST', body: body, headers: { Accept: 'application/json' } })
          .then(function(r) {
            return r.json().catch(function() { return {}; }).then(function(j) {
              if (!r.ok || !j.url) throw new Error(j.message || 'Upload gambar gagal.');
              return j.url;
            });
          })
          .finally(function() { uploading--; });
      }

      var editor = new Quill('#{{ $key }}-editor', {
        theme: 'snow',
        placeholder: @json($placeholder),
        modules: {
          toolbar: '#{{ $key }}-toolbar',
          // Tombol gambar, serta drag-drop & paste file gambar.
          uploader: {
            mimetypes: ['image/png', 'image/jpeg', 'image/webp', 'image/gif'],
            handler: function(range, files) {
              Promise.all(files.map(upload)).then(function(urls) {
                editor.deleteText(range.index, range.length, 'user');
                urls.forEach(function(url, i) { editor.insertEmbed(range.index + i, 'image', url, 'user'); });
                editor.setSelection(range.index + urls.length, 'silent');
              }).catch(function(e) { alert(e.message); });
            },
          },
        },
      });

      function sync() {
        // getSemanticHTML() mengubah tiap spasi jadi &nbsp; — teks jadi tak bisa dipatahkan
        // dan meluber keluar kontainer di frontend. Kembalikan ke spasi biasa.
        var html = editor.getSemanticHTML().replace(/&nbsp;/g, ' ');
        sink.value = editor.getText().trim() === '' ? '' : html;
      }

      // Hanya menulis ulang saat penyunting benar-benar mengedit. Kalau form dibuka lalu
      // disimpan tanpa menyentuh isi, HTML asli lewat apa adanya — termasuk elemen yang
      // tidak dikenal Quill (mis. tabel) yang kalau tidak begini akan hilang.
      editor.on('text-change', function(delta, oldDelta, source) {
        if (source === 'user') sync();
      });

      // Paste HTML (salin dari web/Word) membawa gambar base64 tanpa lewat uploader:
      // upload juga lalu ganti src-nya dengan URL file. Yang gagal dibuang dari isi.
      var pending = new WeakSet();
      editor.on('text-change', function() {
        editor.root.querySelectorAll('img[src^="data:"]').forEach(function(el) {
          if (pending.has(el)) return;
          pending.add(el);
          fetch(el.src).then(function(r) { return r.blob(); }).then(upload)
            .then(function(url) {
              el.setAttribute('src', url);
              editor.update('user');
            })
            .catch(function(e) {
              var blot = Quill.find(el);
              if (blot) editor.deleteText(editor.getIndex(blot), 1, 'user');
              alert('Gambar tidak bisa diupload dan dihapus dari isi: ' + e.message);
            });
        });
      });

      sink.form.addEventListener('submit', function(e) {
        if (!uploading) return;
        e.preventDefault();
        alert('Tunggu sebentar, gambar masih diupload.');
      });

      // Lebar gambar: klik gambar → atur lewat slider. Disimpan sebagai width="50%"
      // (relatif ke lebar paragraf), jadi proporsinya sama di editor maupun halaman depan.
      var bar = document.getElementById('{{ $key }}-imgbar');
      var range = bar.querySelector('input');
      var out = bar.querySelector('output');
      var img = null;

      function setWidth(value) {
        var blot = img && Quill.find(img);
        if (blot) editor.formatText(editor.getIndex(blot), 1, 'width', value, 'user');
        out.textContent = value || 'asli';
      }

      editor.root.addEventListener('click', function(e) {
        img = e.target.tagName === 'IMG' ? e.target : null;
        bar.hidden = !img;
        if (!img) return;
        var block = img.closest('.ql-editor > *') || editor.root;
        range.value = parseInt(img.getAttribute('width'), 10) || Math.round(img.offsetWidth / block.clientWidth * 100);
        out.textContent = img.getAttribute('width') || 'asli';
      });
      range.addEventListener('input', function() { setWidth(range.value + '%'); });
      bar.querySelector('button').addEventListener('click', function() { setWidth(false); });
      editor.on('text-change', function() {
        if (img && !img.isConnected) { img = null; bar.hidden = true; }
      });

      @if ($required)
        sink.closest('form').addEventListener('submit', function(e) {
          if (sink.value !== '') return;
          e.preventDefault();
          editor.focus();
          document.getElementById('{{ $key }}-editor').closest('.form-group').classList.add('has-error');
        });
      @endif
    })();
  </script>
@endpush
