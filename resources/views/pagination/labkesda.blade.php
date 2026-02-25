@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="d-flex align-items-center">
        {{-- Info "Showing ... of ..." --}}
        <div class="text-muted small mr-3 d-none d-md-block">
            Menampilkan
            <strong>{{ $paginator->firstItem() }}</strong>
            –
            <strong>{{ $paginator->lastItem() }}</strong>
            dari
            <strong>{{ $paginator->total() }}</strong>
            data
        </div>

        {{-- Links --}}
        <ul class="pagination pagination-sm mb-0 shadow-sm" style="border-radius:999px; overflow:hidden;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link px-3">
                        ‹
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link px-3" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                        ‹
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link px-3">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link px-3"
                                      style="
                                        background: linear-gradient(135deg,#3529d4,#08b4e4);
                                        border-color: transparent;
                                        color: #fff;
                                      ">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link px-3" href="{{ $url }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link px-3" href="{{ $paginator->nextPageUrl() }}" rel="next">
                        ›
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link px-3">
                        ›
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
