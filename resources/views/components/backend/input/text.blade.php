<div class="form-group @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}">
    {{ $label }}
    <span style="color: {{ !empty($required) && $required ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    <input @required(!empty($required)) placeholder="{{ $placeholder ?? '' }}" value="{{ old($key, $value ?? '') }}"
      type="{{ $type ?? 'text' }}" maxlength="{{ $length ?? 255 }}" id="{{ $key }}" class="form-control no-spinner"
      name="{{ $key }}" maxlength="255">

    @error($key)
      <p class="help-block help-block-error">{{ $message }}</p>
    @enderror
  </div>
</div>
