<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['has-navbar-fixed-top' => true, 'is-immersive' => isset($immersive) && $immersive])>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title }} - PlaatWorld II</title>
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/icon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/icon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#111">
    <meta name="theme-color" content="#242424">
    <link rel="manifest" href="/manifest.json">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Merriweather:400,600,700">
    @if (Auth::check() && Auth::user()->theme == App\Models\User::THEME_LIGHT)
        <link rel="stylesheet" href="/css/bulma-light.min.css">
    @else
        <link rel="stylesheet" href="/css/bulma-dark.min.css">
    @endif
    <link rel="stylesheet" href="/css/style.css?v={{ config('app.version') }}">
    @livewireStyles
    @if (isset($vuejs) && $vuejs)
        <script src="/js/vue.min.js"></script>
    @endif
    @if (isset($threejs) && $threejs)
        <script src="/js/three.min.js"></script>
    @endif
    @if (isset($statsjs) && $statsjs)
        <script src="/js/stats.min.js"></script>
    @endif
    @if (isset($orbitcontrolsjs) && $orbitcontrolsjs)
        <script src="/js/OrbitControls.min.js"></script>
    @endif
</head>
<body>
    @include('layouts.navbar')

    @if (!isset($immersive) || !$immersive)
    <div class="section">
    @endif
        {{ $slot }}
    @if (!isset($immersive) || !$immersive)
    </div>
    @endif

    @if (!isset($immersive) || !$immersive)
        <div class="footer">
            <div class="content has-text-centered">
                <p>@lang('layout.footer.authors')</p>
                <p><span class="tag mr-1">v{{ config('app.version') }}</span> @lang('layout.footer.source')</p>
            </div>
        </div>
    @endif

    <script src="/js/script.js?v={{ config('app.version') }}"></script>
    @livewireScripts
</body>
</html>
