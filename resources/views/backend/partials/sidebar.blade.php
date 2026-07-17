<aside class="main-sidebar" style="z-index: 8">
	<section class="sidebar">
		<div class="user-panel">
			<div class="pull-left image">
				@php
					$imgProfil = checkFilePath(config('app.img_directory'), auth()->user()->picture)
					    ? asset('storage/' . config('app.img_directory') . auth()->user()->picture)
					    : asset('assets/img/default-user.jpg');
				@endphp
				<img class="img-circle" src="{{ $imgProfil }}" width="160" height="160"
					alt="myImage" style="object-fit: cover; aspect-ratio: 1/1;">
			</div>
			<div class="pull-left info">
				<p>{{ ucfirst(auth()->user()->username) }}</p>
				<a href="{{ route('backend.dashboard') }}"><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>

		<ul class="sidebar-menu">
			<li class="header"><span>MAIN NAVIGATION</span></li>
		</ul>
		<ul class="sidebar-menu">
			@foreach (config('sidebar') as $item)
				@if (empty($item['subMenu']))
					<li @class(['active' => request()->is($item['isActive'])])>
						<a href="{{ route($item['route']) }}">
							<i class="{{ $item['icon'] }}"></i>
							<span>{{ $item['label'] }}</span>
						</a>
					</li>
				@else
					<li @class(['active' => request()->is($item['isActive'])])>
						<a href="#">
							<i class="{{ $item['icon'] }}"></i>
							<span>{{ $item['label'] }}</span>
							<i class="fa fa-angle-left pull-right"></i>
						</a>
						<ul class='treeview-menu'>
							@foreach ($item['subMenu'] as $subMenu)
								<li @class(['active' => request()->is($subMenu['isActive'])])>
									<a href="{{ route($subMenu['route']) }}">
										<i class="{{ $subMenu['icon'] }}"></i>
										<span>{{ $subMenu['label'] }}</span>
									</a>
								</li>
							@endforeach
						</ul>
					</li>
				@endif
			@endforeach
		</ul>
	</section>
</aside>
