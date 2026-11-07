{{-- Sitenin sayfalama şablonu (Bootstrap teması + Türkçe). Laravel'in varsayılanı Tailwind
     sınıfları kullanıyor ve ön yüzde stil almadığı için dev oklar çiziyordu. --}}
@if ($paginator->hasPages())
<div class="site-pagination-wrap">
    <nav class="site-pagination" aria-label="Sayfalar">
        @if ($paginator->onFirstPage())
            <span class="pg pg-arrow disabled" aria-disabled="true"><i class="fa-solid fa-chevron-left"></i><span>Önceki</span></span>
        @else
            <a class="pg pg-arrow" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-chevron-left"></i><span>Önceki</span></a>
        @endif

        <span class="pg-pages">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pg disabled">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pg active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="pg" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </span>

        @if ($paginator->hasMorePages())
            <a class="pg pg-arrow" href="{{ $paginator->nextPageUrl() }}" rel="next"><span>Sonraki</span><i class="fa-solid fa-chevron-right"></i></a>
        @else
            <span class="pg pg-arrow disabled" aria-disabled="true"><span>Sonraki</span><i class="fa-solid fa-chevron-right"></i></span>
        @endif
    </nav>
    <p class="text-center text-muted small mt-2 mb-0">{{ $paginator->total() }} yazıdan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} arası</p>
</div>
@endif
