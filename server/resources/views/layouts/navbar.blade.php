@php
    $isLight = Auth::check() && Auth::user()->theme == App\Models\User::THEME_LIGHT;
@endphp
<div class="navbar is-light is-fixed-top">
    <div class="container">
        <div class="navbar-brand">
            <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'home', 'has-text-weight-bold']) href="{{ route('home') }}">
                PlaatWorld II
            </a>
            <a class="navbar-burger burger"><span></span><span></span><span></span></a>
        </div>
        <div class="navbar-menu">
            @auth
                <div class="navbar-start">
                    @if (Auth::user()->role == App\Models\User::ROLE_ADMIN)
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a @class(['navbar-link', 'is-active' => Route::currentRouteName() == 'admin.home', 'is-arrowless'])
                                href="{{ route('admin.home') }}">@lang('layout.navbar.admin_home')</a>
                            <div class="navbar-dropdown">
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.users.crud']) href="{{ route('admin.users.crud') }}">@lang('layout.navbar.admin_users')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.worlds.crud']) href="{{ route('admin.worlds.crud') }}">@lang('layout.navbar.admin_worlds')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.textures.crud']) href="{{ route('admin.textures.crud') }}">@lang('layout.navbar.admin_textures')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.objects.crud']) href="{{ route('admin.objects.crud') }}">@lang('layout.navbar.admin_objects')</a>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="navbar-end">
                    <div @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'balance']) style="display: flex; align-items: center;">
                        <div class="image is-medium is-round is-inline" style="background-image: url(/storage/avatars/{{ Auth::user()->avatar ?? 'default.png' }});"></div>
                        {{ Auth::user()->username }}
                    </div>

                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-link" href="{{ route('settings') }}">@lang('layout.navbar.settings')</a>
                            <a class="button" href="{{ route('auth.logout') }}">@lang('layout.navbar.logout')</a>
                        </div>
                    </div>
                </div>
            @else
                <div class="navbar-end">
                    <div class="navbar-item">
                        <div class="buttons">
                            <a class="button is-link" href="{{ route('auth.login') }}">@lang('layout.navbar.login')</a>
                            <a @class(['button', 'is-dark' => $isLight]) href="{{ route('auth.register') }}">@lang('layout.navbar.register')</a>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
