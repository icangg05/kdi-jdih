<div class="form-group field-{{ $key }} @error($key) has-error @enderror">
  <label class="control-label col-sm-2" for="{{ $key }}">
    {{ $label }}
    <span style="color: {{ !empty($required) && $required ? 'red' : 'gray' }}">*</span>
  </label>
  <div class="col-sm-8">
    <div id="{{ $key }}-container" class="form-control kv-editor-container">
      <textarea id="{{ $key }}" class="form-control" name="{{ $key }}" aria-required="true"
        data-krajee-summernote="summernote_d795d1b7">{{ old($key, $value) }}</textarea>
    </div>
    @error($key)
      <p class="help-block help-block-error">{{ $message }}</p>
    @enderror
  </div>
</div>

@push('script')
  <script>
    window.summernote_d795d1b7 = {
      "lang": "en-US",
      "placeholder": "{{ !empty($key) ? $placeholder : ''}}",
      "styleWithSpan": false,
      "height": 300,
      "dialogsFade": true,
      "toolbar": [
        ["style1", ["pengumumanstyle"]],
        ["style2", ["bold", "italic", "underline", "strikethrough", "superscript", "subscript"]],
        ["font", ["fontname", "fontsize", "color", "clear"]],
        ["para", ["ul", "ol", "paragraph", "height"]],
        ["insert", ["link", "picture", "video", "table", "hr"]],
        ["view", ["help", "codeview", "fullscreen"]]
      ],
      "fontSizes": ["8", "9", "10", "11", "12", "13", "14", "16", "18", "20", "24", "36", "48"],
      "codemirror": {
        "theme": "default",
        "lineNumbers": true,
        "styleActiveLine": true,
        "matchBrackets": true,
        "smartIndent": true
      },
      "hint": [{
        "match": /:([\-+\w]+)$/,
        "search": function(keyword, callback) {
          callback($.grep(kvEmojis, function(item) {
            return item.indexOf(keyword) === 0;
          }));
        },
        "template": function(item) {
          var content = kvEmojiUrls[item];
          return '<img src="' + content + '" width="20" /> :' + item + ':'
        },
        "content": function(item) {
          var url = kvEmojiUrls[item];
          if (url) {
            return $("<img />").attr("src", url).css("width", 20)[0];
          }
          return "";
        }
      }]
    };
  </script>

  <script>
    jQuery(function($) {
      $("#{{ $key }}").kvSummernote({
        "enableHintEmojis": true,
        "autoFormatCode": true
      });
      if (jQuery('#{{ $key }}').data('summernote')) {
        jQuery('#{{ $key }}').summernote('destroy');
      }
      jQuery('#{{ $key }}').summernote(summernote_d795d1b7);
    });
  </script>
@endpush
