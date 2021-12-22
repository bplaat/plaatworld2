@component('layouts.app')
    @slot('title', __('home.title'))
    <div class="container content">
        <h1 class="title">@lang('home.title')</h1>
        <p>@lang('home.description')</p>

        @if (Auth::check())
            <div class="buttons">
                <a class="button is-link" href="{{ route('play') }}">@Lang('home.play')</a>
            </div>
        @endif
    </div>
@endcomponent
