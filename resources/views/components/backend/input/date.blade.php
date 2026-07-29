<div class="form-group @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}-disp">
    {{ $label }}
    <span style="color: {{ !empty($required) && $required ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    @php
      // Yang dikirim ke server selalu Y-m-d (input hidden), yang dilihat user bahasa Indonesia.
      $saveDate = Carbon\Carbon::make(old($key, $value))?->format('Y-m-d') ?? '';
      // locale('id') wajib eksplisit: aplikasi ini tidak pernah memanggil Carbon::setLocale(),
      // jadi translatedFormat() tanpa ini menghasilkan "12 December 2025".
      $dispDate = $saveDate ? Carbon\Carbon::parse($saveDate)->locale('id')->translatedFormat('d F Y') : '';
    @endphp

    <input type="hidden" id="{{ $key }}" name="{{ $key }}" value="{{ $saveDate }}">

    <div id="{{ $key }}-disp-kvdate" class="input-group date">
      <span class="input-group-addon kv-date-picker" title="Pilih tanggal">
        <i class="glyphicon glyphicon-calendar kv-dp-icon"></i>
      </span>
      <span class="input-group-addon kv-date-remove" title="Kosongkan">
        <i class="glyphicon glyphicon-remove kv-dp-icon"></i>
      </span>

      {{-- Tanpa atribut name: hanya input hidden di atas yang ikut terkirim.
           readonly supaya tanggal hanya bisa dipilih dari kalender, tidak diketik. --}}
      <input type="text" id="{{ $key }}-disp" value="{{ $dispDate }}" readonly
        class="form-control krajee-datepicker" style="background-color:#fff;cursor:pointer"
        placeholder="{{ !empty($placeholder) ? $placeholder : 'Pilih tanggal' }}">
    </div>

    @error($key)
      <p class="help-block help-block-error">{{ $message }}</p>
    @enderror
  </div>
</div>

@push('script')
  <script>
    jQuery(function($) {
      var $disp = $('#{{ $key }}-disp'),
        $group = $('#{{ $key }}-disp-kvdate'),
        $save = $('#{{ $key }}');

      if ($disp.data('kvDatepicker')) {
        $disp.kvDatepicker('destroy');
      }
      $group.kvDatepicker({
        format: 'dd MM yyyy',
        language: 'id',
        autoclose: true,
        todayHighlight: true,
        todayBtn: 'linked',
        orientation: 'auto'
      });

      // Jaga input hidden tetap Y-m-d. Dibangun dari komponen tanggal lokal,
      // bukan toISOString(), yang menggeser hari di timezone WIB/WITA/WIT.
      $group.on('changeDate clearDate', function(e) {
        var d = e.date;
        $save.val(d ? d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate())
          .slice(-2) : '');
      });

      initDPRemove('{{ $key }}-disp');
      initDPAddon('{{ $key }}-disp');

      // Input readonly, jadi klik di kolomnya juga membuka kalender.
      $disp.on('click', function() {
        $group.kvDatepicker('show');
      });
    });
  </script>
@endpush
