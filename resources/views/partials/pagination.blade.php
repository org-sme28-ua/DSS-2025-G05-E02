@php
    $canPaginate = isset($paginator)
        && is_object($paginator)
        && method_exists($paginator, 'hasPages')
        && method_exists($paginator, 'currentPage')
        && method_exists($paginator, 'lastPage');
@endphp

@if ($canPaginate && $paginator->hasPages())
    @php
        $currentPage = (int) $paginator->currentPage();
        $lastPage = (int) $paginator->lastPage();
        $startPage = max(1, $currentPage - 1);
        $endPage = min($lastPage, $currentPage + 1);

        if ($currentPage <= 2) {
            $endPage = min($lastPage, 3);
        }

        if ($currentPage >= $lastPage - 1) {
            $startPage = max(1, $lastPage - 2);
        }
    @endphp

    <nav class="custom-pagination" aria-label="Paginación">
        <div class="pagination-summary">
            Mostrando {{ $paginator->firstItem() ?? 0 }} a {{ $paginator->lastItem() ?? 0 }} de {{ $paginator->total() ?? 0 }} resultados
        </div>

        <div class="pagination-buttons">
            @if ($paginator->onFirstPage())
                <span class="page-btn disabled">‹ Anterior</span>
            @else
                <a class="page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Anterior</a>
            @endif

            @if ($startPage > 1)
                <a class="page-btn" href="{{ $paginator->url(1) }}">1</a>
                @if ($startPage > 2)
                    <span class="page-btn disabled">…</span>
                @endif
            @endif

            @for ($page = $startPage; $page <= $endPage; $page++)
                @if ($page === $currentPage)
                    <span class="page-btn active" aria-current="page">{{ $page }}</span>
                @else
                    <a class="page-btn" href="{{ $paginator->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            @if ($endPage < $lastPage)
                @if ($endPage < $lastPage - 1)
                    <span class="page-btn disabled">…</span>
                @endif
                <a class="page-btn" href="{{ $paginator->url($lastPage) }}">{{ $lastPage }}</a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente ›</a>
            @else
                <span class="page-btn disabled">Siguiente ›</span>
            @endif
        </div>
    </nav>
@endif
