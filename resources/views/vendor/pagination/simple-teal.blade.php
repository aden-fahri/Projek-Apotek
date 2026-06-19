<div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
    <div class="text-[13px] text-gray-500 font-medium">
        @if (method_exists($paginator, 'total') && $paginator->total() > 0)
            Menampilkan <span class="font-bold text-gray-700">{{ $paginator->firstItem() }}</span> sampai <span class="font-bold text-gray-700">{{ $paginator->lastItem() }}</span> dari <span class="font-bold text-gray-700">{{ number_format($paginator->total(), 0, ',', '.') }}</span> entri
        @else
            Menampilkan 0 entri
        @endif
    </div>

    @if ($paginator->hasPages())
        <nav>
            <ul class="pagination" style="margin: 0; display: flex; align-items: center; gap: 8px; list-style: none; padding: 0;">
                {{-- Prev --}}
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    @if ($paginator->onFirstPage())
                        <span class="page-link">Sebelumnya</span>
                    @else
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                    @endif
                </li>

                {{-- Page Numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li class="page-item disabled px-1 flex items-center justify-center h-[36px]"><span class="text-gray-400 font-semibold">{{ $element }}</span></li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                                @if ($page == $paginator->currentPage())
                                    <span class="page-link">{{ $page }}</span>
                                @else
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    @if ($paginator->hasMorePages())
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Selanjutnya</a>
                    @else
                        <span class="page-link">Selanjutnya</span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif
</div>

