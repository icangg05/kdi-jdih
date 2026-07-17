<div class="form-group @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}">
    {{ $label }}
    <span style="color: {{ !empty($required) && $required ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    <div id="{{ $key }}-disp-kvdate" class="input-group date">
      <span class="input-group-addon kv-date-picker" title="Select date">
        <i class="glyphicon glyphicon-calendar kv-dp-icon"></i>
      </span>
      <span class="input-group-addon kv-date-remove" title="Clear field">
        <i class="glyphicon glyphicon-remove kv-dp-icon"></i>
      </span>

      @php
        $formatDate = Carbon\Carbon::make($value)?->format('d-F-Y') ?? '';
      @endphp
      <input @required(!empty($required)) value="{{ $formatDate }}" type="text" id="{{ $key }}-disp"
        class="form-control krajee-datepicker" name="{{ $key }}" value="{{ old($key) }}"
        placeholder="{{ !empty($placeholder) ? $placeholder : '' }}" data-krajee-datecontrol="datecontrol_3bd44a6c"
        data-datepicker-source="{{ $key }}-disp-kvdate" data-datepicker-type="2"
        data-krajee-kvDatepicker="kvDatepicker_d567b497">
    </div>

    @error($key)
      <p class="help-block help-block-error">{{ $message }}</p>
    @enderror
  </div>
</div>

@push('script')
  <script>
    window.datecontrol_3bd44a6c = {
      "idSave": "{{ $key }}",
      "url": "\/backend\/datecontrol\/parse\/convert",
      "type": "date",
      "saveFormat": "Y-m-d",
      "dispFormat": "d-F-Y",
      "saveTimezone": null,
      "dispTimezone": null,
      "asyncRequest": true,
      "language": "en"
    };

    window.kvDatepicker_d567b497 = {
      "autoclose": true,
      "format": "dd-MM-yyyy"
    };
  </script>

  <script>
    jQuery(function($) {
      jQuery && jQuery.pjax && (jQuery.pjax.defaults.maxCacheLength = 0);
      if (jQuery('#{{ $key }}').data('datecontrol')) {
        jQuery('#{{ $key }}').datecontrol('destroy');
      }
      jQuery('#{{ $key }}-disp').datecontrol(datecontrol_3bd44a6c);

      jQuery.fn.kvDatepicker.dates = {};
      if (jQuery('#{{ $key }}-disp').data('kvDatepicker')) {
        jQuery('#{{ $key }}-disp').kvDatepicker('destroy');
      }
      jQuery('#{{ $key }}-disp-kvdate').kvDatepicker(kvDatepicker_d567b497);

      initDPRemove('{{ $key }}-disp');
      initDPAddon('{{ $key }}-disp');
    });
  </script>
@endpush
