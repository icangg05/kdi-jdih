{{-- Checkbox terjemahkan otomatis (EN / 中文 / 한국어) via Google Translate --}}
<div class="form-group" style="margin-top:10px;">
	<label style="font-weight:normal;cursor:pointer;">
		<input type="checkbox" name="auto_translate" value="1"
			{{ old('auto_translate') ? 'checked' : '' }}>
		Terjemahkan otomatis ke Bahasa Inggris, Mandarin &amp; Korea
	</label>
	<p class="help-block" style="margin:2px 0 0;">
		Judul/isi akan diterjemahkan otomatis saat disimpan. Kosongkan jika ingin isi manual.
	</p>
</div>
