<div class="border-bottom padding-20px-bottom margin-30px-bottom">
  <div class="widget search mb-4">
    <form action="{{ route('frontend.dokumen.index', $kategori) }}" method="GET">
      @foreach (request()->except('q') as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
      @endforeach

      <div class="form-row align-items-center">
        <div class="col-md-10 my-1">
          <input type="text" class="form-control" name="q" value="{{ request()->q }}"
            placeholder="Cari dokumen hukum lainnya..." autocomplete="off">
        </div>
        <div class="col-md-2 my-1">
          <button type="submit" class="butn btn-block">Cari</button>
        </div>
      </div>
    </form>
  </div>
</div>
