@php
    $isLight = Auth::check() && Auth::user()->theme == App\Models\User::THEME_LIGHT;
@endphp
<div class="navbar is-light is-fixed-top">
    @if (!isset($immersive) || !$immersive)
    <div class="container">
    @endif
        <div class="navbar-brand">
            <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'home', 'has-text-weight-bold']) href="{{ route('home') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="logo" viewBox="0 0 24 24">
                    <circle fill="#e53935" cx="12" cy="12" r="12" />
                    <g transform="translate(3.6, 3.6) scale(0.7)">
                        <path fill="#fff" d="M6.2,2.44L18.1,14.34L20.22,12.22L21.63,13.63L19.16,16.1L22.34,19.28C22.73,19.67 22.73,20.3 22.34,20.69L21.63,21.4C21.24,21.79 20.61,21.79 20.22,21.4L17,18.23L14.56,20.7L13.15,19.29L15.27,17.17L3.37,5.27V2.44H6.2M15.89,10L20.63,5.26V2.44H17.8L13.06,7.18L15.89,10M10.94,15L8.11,12.13L5.9,14.34L3.78,12.22L2.37,13.63L4.84,16.1L1.66,19.29C1.27,19.68 1.27,20.31 1.66,20.7L2.37,21.41C2.76,21.8 3.39,21.8 3.78,21.41L7,18.23L9.44,20.7L10.85,19.29L8.73,17.17L10.94,15Z" />
                    </g>
                </svg>
                PlaatWorld II
            </a>
            <a class="navbar-burger burger"><span></span><span></span><span></span></a>
        </div>
        <div class="navbar-menu">
            @auth
                <div class="navbar-start">
                    <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'play']) href="{{ route('play') }}">@lang('layout.navbar.play')</a>

                    @if (Auth::user()->role == App\Models\User::ROLE_ADMIN)
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a @class(['navbar-link', 'is-active' => Route::currentRouteName() == 'admin.home', 'is-arrowless'])
                                href="{{ route('admin.home') }}">@lang('layout.navbar.admin_home')</a>
                            <div class="navbar-dropdown">
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.users.crud']) href="{{ route('admin.users.crud') }}">@lang('layout.navbar.admin_users')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.worlds.crud']) href="{{ route('admin.worlds.crud') }}">@lang('layout.navbar.admin_worlds')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.textures.crud']) href="{{ route('admin.textures.crud') }}">@lang('layout.navbar.admin_textures')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.objects.crud']) href="{{ route('admin.objects.crud') }}">@lang('layout.navbar.admin_objects')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.items.crud']) href="{{ route('admin.items.crud') }}">@lang('layout.navbar.admin_items')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.sounds.crud']) href="{{ route('admin.sounds.crud') }}">@lang('layout.navbar.admin_sounds')</a>
                                <a @class(['navbar-item', 'is-active' => Route::currentRouteName() == 'admin.taunts.crud']) href="{{ route('admin.taunts.crud') }}">@lang('layout.navbar.admin_taunts')</a>
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
    @if (!isset($immersive) || !$immersive)
    </div>
    @endif
</div>
