{{-- Parameter terakhir key 'route' optional --}}
<section class="content-header">
  <h1>{{ $title }}</h1>
  <ul class="breadcrumb">
    <li><a href="{{ route('backend.dashboard') }}">Home</a></li>

    @foreach ($listNav as $index => $item)
      @if ($index === count($listNav) - 1)
        <li class="active">{{ $item['label'] }}</li>
      @else
        <li><a href="{{ $item['route'] }}">{{ $item['label'] }}</a></li>
      @endif
    @endforeach
  </ul>
</section>
