@if ($paginator->hasPages())
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 20px; padding: 15px 0; border-top: 1px solid rgba(212, 175, 55, 0.1);">
        {{-- Results Info --}}
        <div style="color: #aaa; font-size: 0.9rem;">
            Showing <strong>{{ $paginator->firstItem() }}</strong> to <strong>{{ $paginator->lastItem() }}</strong> of <strong>{{ $paginator->total() }}</strong> results
        </div>

        {{-- Pagination Links --}}
        <div style="display: flex; gap: 6px; align-items: center; flex-wrap: wrap;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span style="display: inline-flex; align-items: center; justify-content: center; width: auto; min-width: 44px; height: 34px; color: #888; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 6px; padding: 0 10px; font-size: 0.85rem; line-height: 1; white-space: nowrap; opacity: 0.5; cursor: not-allowed;">
                    &lsaquo;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display: inline-flex; align-items: center; justify-content: center; width: auto; min-width: 44px; height: 34px; color: #eee; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 6px; padding: 0 10px; font-size: 0.85rem; line-height: 1; white-space: nowrap; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(212, 175, 55, 0.15)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'">
                    &lsaquo;
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span style="display: inline-flex; align-items: center; justify-content: center; color: #aaa; font-size: 0.85rem;">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: auto; min-width: 44px; height: 34px; color: #1a1a2e; background: #d4af37; border: 1px solid #d4af37; border-radius: 6px; padding: 0 10px; font-size: 0.85rem; line-height: 1; white-space: nowrap; font-weight: 600;">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="display: inline-flex; align-items: center; justify-content: center; width: auto; min-width: 44px; height: 34px; color: #eee; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 6px; padding: 0 10px; font-size: 0.85rem; line-height: 1; white-space: nowrap; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(212, 175, 55, 0.15)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display: inline-flex; align-items: center; justify-content: center; width: auto; min-width: 44px; height: 34px; color: #eee; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 6px; padding: 0 10px; font-size: 0.85rem; line-height: 1; white-space: nowrap; text-decoration: none; transition: all 0.3s ease;" onmouseover="this.style.background='rgba(212, 175, 55, 0.15)'" onmouseout="this.style.background='rgba(255, 255, 255, 0.05)'">
                    &rsaquo;
                </a>
            @else
                <span style="display: inline-flex; align-items: center; justify-content: center; width: auto; min-width: 44px; height: 34px; color: #888; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 6px; padding: 0 10px; font-size: 0.85rem; line-height: 1; white-space: nowrap; opacity: 0.5; cursor: not-allowed;">
                    &rsaquo;
                </span>
            @endif
        </div>
    </div>
@endif
