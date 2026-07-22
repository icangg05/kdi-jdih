@props(['label', 'key', 'value' => '', 'required' => false, 'placeholder' => '', 'height' => 320])

<div class="form-group field-{{ $key }} @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}-editor">
    {{ $label }}
    <span style="color: {{ $required ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    <div class="q-shell">
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
    .q-shell { border: 1px solid #cbd5e1; border-radius: 6px; overflow: hidden; background: #fff; transition: border-color .16s ease, box-shadow .16s ease; }
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
    .has-error .q-shell { border-color: #b3241d; }
    .q-sink { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0; }
  </style>
@endpush

@push('script')
  <script src="{{ asset('assets/backend/quill/quill.js') }}"></script>
  <script>
    (function() {
      var sink = document.getElementById('{{ $key }}');
      var editor = new Quill('#{{ $key }}-editor', {
        theme: 'snow',
        placeholder: @json($placeholder),
        modules: {
          toolbar: '#{{ $key }}-toolbar',
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
