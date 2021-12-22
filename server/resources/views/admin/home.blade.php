@component('layouts.app')
    @slot('title', __('admin/home.title'))
    <div class="container">
        <h1 class="title">@lang('admin/home.header')</h1>

        <div class="buttons">
            <a class="button" href="{{ route('admin.users.crud') }}">@lang('admin/home.users')</a>
            <a class="button" href="{{ route('admin.worlds.crud') }}">@lang('admin/home.worlds')</a>
            <a class="button" href="{{ route('admin.textures.crud') }}">@lang('admin/home.textures')</a>
            <a class="button" href="{{ route('admin.objects.crud') }}">@lang('admin/home.objects')</a>
        </div>
    </div>
@endcomponent
