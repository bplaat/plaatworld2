@if ($paginator->hasPages())
    <div class="pagination is-centered" role="navigation">
        <a wire:click="_previousPage({{ !$paginator->onFirstPage() ? 'false' : 'true' }})" wire:loading.attr="disabled"
            rel="previous" @class(['pagination-previous', 'disabled' => $paginator->onFirstPage()])>@lang('pagination.previous')</a>
        <a wire:click="_nextPage({{ $paginator->hasMorePages() ? 'false' : 'true' }})" wire:loading.attr="disabled"
            rel="next" @class(['pagination-next', 'disabled' => !$paginator->hasMorePages()])>@lang('pagination.next')</a>

        <ul class="pagination-list">
            @php
                $totalPages = ceil($paginator->total() / $paginator->count());
            @endphp
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-ellipsis">&hellip;</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            <a @class(['pagination-link', 'is-current' => $page == $paginator->currentPage()])
                                wire:click="gotoPage({{ $page }})"
                                wire:loading.attr="disabled">{{ $page }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>
    </div>
@endif
