<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="has-navbar-fixed-top">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title }} - PlaatWorld II</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather:400,600,700">
    @if (Auth::check() && Auth::user()->theme == App\Models\User::THEME_LIGHT)
        <link rel="stylesheet" href="/css/bulma-light.min.css">
    @else
        <link rel="stylesheet" href="/css/bulma-dark.min.css">
    @endif
    <link rel="stylesheet" href="/css/style.css?v={{ config('app.version') }}">
    @livewireStyles
</head>
<body>
    @include('layouts.navbar')

    <div class="section">
        {{ $slot }}
    </div>

    <div class="footer">
        <div class="content has-text-centered">
            <p>@lang('layout.footer.authors')</p>
            <p><span class="tag mr-1">v{{ config('app.version') }}</span> @lang('layout.footer.source')</p>
        </div>
    </div>

    <script src="/js/script.js?v={{ config('app.version') }}"></script>
    @livewireScripts
</body>
</html>
