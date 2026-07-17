<x-layouts.backend :title="$title" :listNav="[['label' => $title]]">

	<div class="box-body no-padding">
		<form class="form-horizontal"
			action="{{ route('backend.narasi.update', $data->id) }}"
			method="POST" enctype="multipart/form-data">
			@csrf
			@method('PATCH')

			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<b>Form {{ $title }}</b>
				</div>
				<div class="box-body">

					<x-backend.input.editor
						label="Narasi & Qoute"
						key="text"
						:value="$data->text ?? ''"
						required
						placeholder="Tulis isi narasi..." />

				</div>
				<div class="box-footer" style="height: 400px !important;">
					<button type="submit" class="btn btn-success btn-flat">
						<i class="fa fa-save"></i> Simpan</button>
				</div>
			</div>
		</form>
	</div>
</x-layouts.backend>
