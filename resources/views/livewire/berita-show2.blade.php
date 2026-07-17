<div>
	<x-frontend.breadcrumb title="Berita" :listNav="[['label' => 'Berita', 'route' => route('frontend.berita.index')], ['label' => 'Detail']]" />

	<section class="blogs">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-md-12 sm-margin-50px-bottom">
					<div class="posts">
						<div class="post">
							<div class="post-img">
								@php
									$image = checkFilePath(config('app.img_directory'), $data->image)
									    ? asset('storage/' . config('app.img_directory') . $data->image)
									    : asset('assets/img/default-img.jpg');
								@endphp
								<img class="rounded" src="{{ $image }}" alt="img">
							</div>
							<div class="content">
								<div class="blog-list-simple-text post-meta margin-20px-bottom">
									<div class="post-title">
										<h5>{{ $data->judul }}</h5>
									</div>
								</div>
								<div class="margin-30px-bottom" style="text-align: justify;">
									{!! $data->isi !!}
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4 col-md-12 padding-30px-left sm-padding-15px-left">
					<div class="side-bar">
						<div class="widget search">
							<div class="widget-title margin-35px-bottom">
								<h3>Cari Berita</h3>
							</div>
							<div class="input-group mb-3">
								<form id="w0" action="{{ route('frontend.berita.index') }}" method="get">
									<div class="form-group field-beritasearch-judul">
										<div class="input-group-append">
											<input type="text" id="beritasearch-judul" class="form-control" name="q"
												placeholder="Search...">
											<button type="submit" class="btn btn-primary">
												<span class="ti-search"></span>
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
