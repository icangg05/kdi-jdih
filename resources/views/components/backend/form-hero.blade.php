@props([
	'icon' => 'fa-file-text-o',
	'heading',
	'sub' => '',
	'chips' => [],
	'viewUrl' => null,
])

<header class="doc-hero">
	<div class="head">
		<span class="mark" aria-hidden="true"><i class="fa {{ $icon }}"></i></span>
		<div>
			<h2>{{ $heading }}</h2>
			<p class="sub">
				{{ $sub }}
				Kolom bertanda <span class="req">*</span> wajib diisi, sisanya opsional.
			</p>
		</div>
	</div>

	@if (!empty($chips) || $viewUrl)
		<div class="chips">
			@foreach ($chips as $chip)
				@continue(blank($chip['value'] ?? null))
				<span class="chip {{ $chip['tone'] ?? '' }}">
					@if (!empty($chip['label']))
						{{ $chip['label'] }}
					@endif
					<b>{{ $chip['value'] }}</b>
				</span>
			@endforeach

			@if ($viewUrl)
				<a class="chip" href="{{ $viewUrl }}"><i class="fa fa-eye"></i> Lihat detail</a>
			@endif
		</div>
	@endif
</header>

@if ($errors->any())
	<div class="doc-errors" role="alert" tabindex="-1">
		<h4>{{ $errors->count() }} kolom belum benar</h4>
		<ul>
			@foreach ($errors->getMessages() as $field => $messages)
				<li><a href="#{{ $field }}">{{ $messages[0] }}</a></li>
			@endforeach
		</ul>
	</div>
@endif
