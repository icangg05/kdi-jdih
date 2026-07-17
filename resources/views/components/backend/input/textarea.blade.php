<div class="form-group @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}">
    {{ $label }}
    <span style="color: {{ !empty($required) ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    <textarea @required(!empty($required)) rows="{{ $rows ?? '3' }}" id="{{ $key }}" class="form-control" name="{{ $key }}" placeholder="{{ $placeholder ?? '' }}">{{ old($key, $value) }}</textarea>

    @error($key)
      <p class="help-block help-block-error">{{ $message }}</p>
    @enderror
  </div>
</div>

@push('link')
  <style>
    textarea.form-control {
      resize: none;
    }
  </style>
@endpush
