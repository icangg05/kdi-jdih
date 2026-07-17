<div class="form-group field-pengumuman-status @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}">
    {{ $label }}
    <span style="color: {{ !empty($required) && $required ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    <select @required(!empty($required)) id="{{ $key }}" class="{{ $key }} form-control" name="{{ $key }}"
      style="height: 34px">
      <option value="">{{ !empty($placeholder) ? $placeholder : 'Pilih...' }}</option>
      @foreach ($data as $item)
        <option @selected(old($key, $value ?? '') != '' && old($key, $value ?? '') == $item['value']) value="{{ $item['value'] }}">{{ $item['label'] }}</option>
      @endforeach
    </select>

    @error($key)
      <p class="help-block help-block-error">{{ $message }}</p>
    @enderror
  </div>

</div>

@push('link')
  @error($key)
    <style>
      [aria-labelledby*="select2-{{ $key }}-container"] {
        border: 1px solid red !important;
      }
    </style>
  @enderror
@endpush

@push('script')
  <script>
    $(document).ready(function() {
      $('.{{ $key }}').select2();
    });
  </script>
@endpush
