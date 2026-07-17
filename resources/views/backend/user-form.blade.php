<x-layouts.backend :title="$title" :listNav="[['label' => 'User', 'route' => route('backend.user.index')], ['label' => $title]]">
	@php
		$isCreate = request()->routeIs('backend.user.create') ? true : false;
	@endphp

	<div class="box-body no-padding">
		<form class="form-horizontal"
			action="{{ $isCreate ? route('backend.user.store') : route('backend.user.update', $data->id) }}"
			method="POST" enctype="multipart/form-data">
			@csrf
			@if (!$isCreate)
				@method('PATCH')
			@endif

			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<b>Form {{ $title }}</b>
				</div>
				<div class="box-body">

					<x-backend.input.text
						label="Username"
						key="username"
						:value="$data->username ?? ''"
						required />

					<x-backend.input.text
						label="Email"
						key="email"
						:value="$data->email ?? ''"
						required />

					<x-backend.input.text
						label="Password"
						key="password"
						type="password"
						required />

					<x-backend.input.text
						label="Konfirmasi Password"
						key="password_confirmation"
						type="password"
						required />

				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary btn-flat">
						<i class="fa fa-save"></i> Sign Up</button>
					<a class="btn btn-danger btn-flat" href="{{ route('backend.user.index') }}">
						<i class="fa fa-remove"></i> Batal</a>
				</div>
			</div>
		</form>
	</div>
</x-layouts.backend>
