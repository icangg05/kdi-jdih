<div>
	@php
		$meta = [
			'sekilas-sejarah' => ['icon' => 'fa-clock-rotate-left', 'sub' => __('profil.tentang-kami')],
			'dasar-hukum'     => ['icon' => 'fa-scale-balanced',    'sub' => __('profil.tentang-kami')],
			'visi'            => ['icon' => 'fa-eye',                'sub' => __('profil.tentang-kami')],
			'misi'            => ['icon' => 'fa-bullseye',           'sub' => __('profil.tentang-kami')],
			'sto'             => ['icon' => 'fa-sitemap',            'sub' => __('profil.tentang-kami')],
		];
		$m = $meta[$kategori] ?? ['icon' => 'fa-circle-info', 'sub' => ''];
		$isStatement = in_array($kategori, ['visi', 'misi'], true);
	@endphp

	<style>
		/* ===== DASAR HUKUM: daftar jadi kartu bernomor ===== */
		.profil-dasar-hukum ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: .75rem; counter-reset: dh; }
		.profil-dasar-hukum li { margin: 0; }
		.profil-dasar-hukum li a {
			display: flex; gap: 1rem; align-items: flex-start;
			padding: 1rem 1.15rem; background: #fff;
			border: 1px solid rgb(226 232 240); border-radius: .25rem;
			text-decoration: none; transition: all .25s ease;
		}
		.profil-dasar-hukum li a:hover {
			border-color: color-mix(in oklab, var(--color-primary) 45%, transparent);
			box-shadow: 0 12px 32px -20px rgba(2,6,23,.55); transform: translateY(-2px);
		}
		.profil-dasar-hukum li a::before {
			counter-increment: dh; content: counter(dh, decimal-leading-zero);
			flex: none; display: grid; place-items: center; width: 2.25rem; height: 2.25rem;
			border-radius: .25rem; background: color-mix(in oklab, var(--color-primary) 12%, white);
			color: var(--color-primary); font-weight: 700; font-size: .78rem; font-variant-numeric: tabular-nums;
		}
		.profil-dasar-hukum li a p { margin: 0; font-size: .9rem; line-height: 1.55; font-weight: 500; color: rgb(30 41 59); }
		.profil-dasar-hukum li a:hover p { color: var(--color-primary); }

		/* ===== SEJARAH: marker brand + drop cap ===== */
		.profil-sekilas-sejarah :where(ol, ul) > li::marker { color: var(--color-primary); font-weight: 700; }
		.profil-sekilas-sejarah > p:first-of-type::first-letter {
			float: left; font-family: var(--font-serif); font-size: 3.1rem; line-height: .82;
			font-weight: 800; color: var(--color-primary); margin: .15rem .6rem 0 0;
		}

		/* ===== VISI/MISI: statement ===== */
		.profil-statement { text-align: center; }
		.profil-statement > p:first-of-type { color: rgb(100 116 139); }
		.profil-statement ul { list-style: none; padding: 0; margin: 1.25rem auto; display: flex; flex-direction: column; gap: .55rem; max-width: 34rem; }
		.profil-statement ul li {
			background: color-mix(in oklab, var(--color-accent) 6%, white);
			border: 1px solid color-mix(in oklab, var(--color-accent) 16%, transparent);
			border-radius: .25rem; padding: .65rem 1rem; font-size: .9rem; color: rgb(51 65 85);
		}
		.profil-statement p strong {
			display: inline-block; margin-top: .75rem; letter-spacing: .18em; text-transform: uppercase;
			font-size: .7rem; color: var(--color-primary); font-weight: 700;
		}
		.profil-statement > p:last-of-type {
			font-family: var(--font-serif); font-size: 1.4rem; line-height: 1.5; font-weight: 700;
			font-style: italic; color: var(--color-accent); max-width: 38rem; margin: .5rem auto 0;
		}
	</style>

	<x-frontend.breadcrumb
		:title="$title"
		:listNav="[['label' => __('profil.tentang-kami')]]" />

	<section class="relative bg-linear-to-b from-white to-gray-50 py-12 lg:py-16">

		{{-- ORNAMEN ABSTRAK LEMBUT --}}
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 overflow-hidden">
			<div class="absolute -top-28 -right-16 h-80 w-80 rounded bg-primary/5 blur-3xl"></div>
			<div class="absolute top-1/3 -left-24 h-96 w-96 rounded bg-accent/5 blur-3xl"></div>
			<div class="absolute inset-0 opacity-60"
				style="background-image: radial-gradient(rgba(1,91,165,0.06) 1px, transparent 1px); background-size: 26px 26px; -webkit-mask-image: radial-gradient(ellipse 70% 55% at 50% 20%, black, transparent 80%); mask-image: radial-gradient(ellipse 70% 55% at 50% 20%, black, transparent 80%);"></div>
			<i class="fa-solid fa-scale-balanced absolute right-[6%] top-40 text-[9rem] text-accent/4 -rotate-6"></i>
			<i class="fa-solid fa-book-open absolute -left-6 bottom-16 text-[8rem] text-primary/4 rotate-6"></i>
		</div>

		<div class="relative z-10 max-w-4xl mx-auto px-4 lg:px-0" x-data="{ zoom: null }">
			<article class="animate-rise relative overflow-hidden bg-white rounded ring-1 ring-slate-200 shadow-sm shadow-slate-200/60">

				{{-- aksen atas --}}
				<div aria-hidden="true" class="absolute inset-x-0 top-0 h-1 bg-linear-to-r from-primary via-primary/60 to-accent"></div>

				{{-- Header --}}
				<div class="flex items-start gap-4 p-6 lg:p-8 border-b border-slate-100">
					<div class="flex h-12 w-12 shrink-0 items-center justify-center rounded bg-linear-to-br from-primary to-primary-hover text-white shadow-sm shadow-primary/30">
						<i class="fa-solid {{ $m['icon'] }} text-lg"></i>
					</div>
					<div class="min-w-0">
						<p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-accent">{{ $m['sub'] }}</p>
						<h1 class="mt-1 text-xl lg:text-2xl font-bold text-slate-900 leading-tight">
							@if ($isStatement)
								{{ $title }} {{ __('profil.suffix') }}
							@else
								{{ $title }}
							@endif
						</h1>
						<div class="mt-3 flex items-center gap-1">
							<span class="h-1 w-10 rounded bg-primary"></span>
							<span class="h-1 w-1.5 rounded bg-accent"></span>
						</div>
					</div>
				</div>

				{{-- Body --}}
				<div class="relative p-6 lg:p-8">

					@if ($kategori === 'sto')
						{{-- STRUKTUR ORGANISASI: bagan; lightbox di-teleport ke body agar benar-benar fullscreen --}}
						<div class="grid gap-6">
							@foreach (['struktur-1.png', 'struktur-2.png'] as $i => $img)
								<figure class="group overflow-hidden rounded ring-1 ring-slate-200 bg-slate-50">
									<button type="button"
										@click="zoom = '{{ asset('assets/img/' . $img) }}'"
										class="block w-full cursor-zoom-in relative">
										<img src="{{ asset('assets/img/' . $img) }}"
											alt="{{ __('Struktur Organisasi') }} {{ $i + 1 }}"
											class="w-full object-contain p-4 lg:p-8 transition-transform duration-500 group-hover:scale-[1.02]">
										<span class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded bg-white/90 text-slate-600 shadow-sm opacity-0 group-hover:opacity-100 transition">
											<i class="fa-solid fa-magnifying-glass-plus text-xs"></i>
										</span>
									</button>
									<figcaption class="border-t border-slate-100 bg-white px-4 py-3 text-center text-xs lg:text-sm text-slate-500">
										<i class="fa-solid fa-sitemap text-primary mr-1.5"></i>
										{{ __('Bagan Struktur Organisasi') }} ({{ $i + 1 }})
									</figcaption>
								</figure>
							@endforeach
						</div>
					@else
						<div class="prose prose-slate max-w-none prose-headings:text-slate-900 prose-a:text-accent prose-a:no-underline hover:prose-a:underline prose-strong:text-slate-900
							profil-prose profil-{{ $kategori }} {{ $isStatement ? 'profil-statement' : '' }}">
							@if ($isStatement)
								<i class="fa-solid fa-quote-left pointer-events-none absolute right-6 top-6 text-5xl text-primary/5" aria-hidden="true"></i>
							@endif
							{!! $data !!}
						</div>
					@endif

				</div>
			</article>

			{{-- Lightbox STO (teleport ke body: lepas dari overflow-hidden/transform ancestor) --}}
			@if ($kategori === 'sto')
				<template x-teleport="body">
					<div x-show="zoom" x-cloak @click="zoom = null"
						x-transition.opacity
						@keydown.escape.window="zoom = null"
						class="fixed inset-0 z-100 flex items-center justify-center bg-black/80 p-4 cursor-zoom-out">
						<img :src="zoom" alt="{{ __('Struktur Organisasi') }}"
							class="max-h-[90vh] max-w-[95vw] rounded bg-white shadow-2xl">
						<button type="button" @click.stop="zoom = null"
							class="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded bg-white/10 text-white hover:bg-white/20 transition">
							<i class="fa-solid fa-xmark"></i>
						</button>
					</div>
				</template>
			@endif
		</div>
	</section>
</div>
