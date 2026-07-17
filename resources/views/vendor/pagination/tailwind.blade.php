@if ($paginator->hasPages())
	<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">

		{{-- Mobile --}}
		<div class="text-sm flex justify-between flex-1 sm:hidden">
			{{-- Prev --}}
			@if ($paginator->onFirstPage())
				<span
					class="inline-flex items-center px-3 py-2 text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed">
					<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd"
							d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
							clip-rule="evenodd" />
					</svg>
					<span>Prev</span>
				</span>
			@else
				<a href="{{ $paginator->previousPageUrl() }}"
					class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition">
					<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd"
							d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
							clip-rule="evenodd" />
					</svg>
					<span>Prev</span>
				</a>
			@endif

			{{-- Next --}}
			@if ($paginator->hasMorePages())
				<a href="{{ $paginator->nextPageUrl() }}"
					class="inline-flex items-center px-3 py-2 ml-3 bg-white border border-gray-300 rounded-md hover:bg-gray-100 transition">
          <span>Next</span>
					<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd"
							d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
							clip-rule="evenodd" />
					</svg>
				</a>
			@else
				<span
					class="inline-flex items-center px-3 py-2 ml-3 text-gray-400 bg-white border border-gray-300 rounded-md cursor-not-allowed">
          <span>Next</span>
					<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd"
							d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
							clip-rule="evenodd" />
					</svg>
				</span>
			@endif
		</div>

		{{-- Desktop --}}
		<div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
			<p class="text-sm text-gray-600">
				Showing
				<span class="font-medium">{{ $paginator->firstItem() }}</span>
				to
				<span class="font-medium">{{ $paginator->lastItem() }}</span>
				of
				<span class="font-medium">{{ $paginator->total() }}</span>
				results
			</p>

			<span class="inline-flex shadow-sm rounded-md">

				{{-- Prev --}}
				@if ($paginator->onFirstPage())
					<span
						class="inline-flex items-center px-2 py-2 text-gray-400 bg-white border border-gray-300 rounded-l-md cursor-not-allowed">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
								clip-rule="evenodd" />
						</svg>
					</span>
				@else
					<a href="{{ $paginator->previousPageUrl() }}"
						class="inline-flex items-center px-2 py-2 bg-white border border-gray-300 rounded-l-md hover:bg-gray-100 transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
								clip-rule="evenodd" />
						</svg>
					</a>
				@endif

				{{-- Pages --}}
				@foreach ($elements as $element)
					@if (is_string($element))
						<span class="inline-flex items-center px-4 py-2 text-gray-500 bg-white border border-gray-300">
							{{ $element }}
						</span>
					@endif

					@if (is_array($element))
						@foreach ($element as $page => $url)
							@if ($page == $paginator->currentPage())
								<span class="inline-flex items-center px-4 py-2 text-white bg-primary border border-primary">
									{{ $page }}
								</span>
							@else
								<a href="{{ $url }}"
									class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 transition">
									{{ $page }}
								</a>
							@endif
						@endforeach
					@endif
				@endforeach

				{{-- Next --}}
				@if ($paginator->hasMorePages())
					<a href="{{ $paginator->nextPageUrl() }}"
						class="inline-flex items-center px-2 py-2 bg-white border border-gray-300 rounded-r-md hover:bg-gray-100 transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
								clip-rule="evenodd" />
						</svg>
					</a>
				@else
					<span
						class="inline-flex items-center px-2 py-2 text-gray-400 bg-white border border-gray-300 rounded-r-md cursor-not-allowed">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
							<path fill-rule="evenodd"
								d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
								clip-rule="evenodd" />
						</svg>
					</span>
				@endif

			</span>
		</div>
	</nav>
@endif
