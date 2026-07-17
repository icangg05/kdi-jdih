<header class="main-header" style="z-index: 9">
	<!-- Logo -->
	<a href="index" class="logo">
		<!-- mini logo for sidebar mini 50x50 pixels -->
		<span class="logo-mini"><b>JDIH</b></span>
		<!-- logo for regular state and mobile devices -->
		<span class="logo-lg"><b>JDIH</b></span>
	</a>
	<!-- Header Navbar: style can be found in header.less -->
	<nav class="navbar navbar-static-top" role="navigation">
		<!-- Sidebar toggle button-->
		<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
			<span class="sr-only">Toggle navigation</span>
		</a>
		<div class="navbar-custom-menu">
			<ul class="nav navbar-nav">
				<!-- Messages: style can be found in dropdown.less-->


				<!-- User Account: style can be found in dropdown.less -->
				<li class="dropdown user user-menu">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">

						@php
							$imgProfil = checkFilePath(config('app.img_directory'), auth()->user()->picture)
							    ? asset('storage/' . config('app.img_directory') . auth()->user()->picture)
							    : asset('assets/img/default-user.jpg');
						@endphp
						<img class="user-image" src="{{ $imgProfil }}" width="160"
							height="auto" alt="myImage">
						<span class="hidden-xs">
							{{ ucfirst(auth()->user()->username) }}</span>
					</a>
					<ul class="dropdown-menu">
						<!-- User image -->
						<li class="user-header">
							<img class="img-circle" src="{{ $imgProfil }}" alt="User Image">
							<p>{{ ucfirst(auth()->user()->username) }}</p>
							<p>{{ Auth::user()->email }}</p>
						</li>
						<!-- Menu Body -->

						<!-- Menu Footer-->
						<li class="user-footer">
							<div class="pull-left">
								<a class="btn btn-default btn-flat" href="{{ route('backend.user.show', auth()->user()->id) }}">Profile</a>
							</div>
							<div class="pull-right">
								<a class="btn btn-default btn-flat" href="{{ route('logout') }}">Sign out</a>
							</div>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</nav>
</header>
