@component('layouts.app')
    @slot('title', __('home.title'))
    <div class="container content">
        <h1 class="title">@lang('home.title')</h1>
        <p>@lang('home.description')</p>
    </div>
@endcomponent
