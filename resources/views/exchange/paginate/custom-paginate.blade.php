@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center space-x-1">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1 rounded border text-gray-400 cursor-not-allowed">
                ‹
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-1 rounded border text-gray-700 hover:bg-orange-100 hover:text-orange-600 transition">
                ‹
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-1 rounded border text-gray-400">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1 rounded border border-orange-500 bg-orange-50 text-orange-600 font-bold">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1 rounded border text-gray-700 hover:bg-orange-100 hover:text-orange-600 transition">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-1 rounded border text-gray-700 hover:bg-orange-100 hover:text-orange-600 transition">
                ›
            </a>
        @else
            <span class="px-3 py-1 rounded border text-gray-400 cursor-not-allowed">
                ›
            </span>
        @endif
    </nav>
@endif

