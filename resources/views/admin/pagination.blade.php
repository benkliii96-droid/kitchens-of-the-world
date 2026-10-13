@if ($paginator->hasPages())
    <p class="pagination">
        @if ($paginator->onFirstPage())
            <span>« Назад</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}">« Назад</a>
        @endif

        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
            @if ($page === $paginator->currentPage())
                <b>{{ $page }}</b>
            @else
                <a href="{{ $paginator->url($page) }}">{{ $page }}</a>
            @endif
        @endfor

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}">Вперёд »</a>
        @else
            <span>Вперёд »</span>
        @endif
    </p>
@endif
