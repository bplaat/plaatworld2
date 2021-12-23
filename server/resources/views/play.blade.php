@component('layouts.app')
    @slot('title', __('play.title'))
    @slot('immersive', true)
    @slot('threejs', true)
    @slot('statsjs', true)

    <canvas id="game-canvas"></canvas>

    <script src="/js/Game.js"></script>
    <script>
        new Game({
            authToken: @json(Auth::user()->authToken())
        });
    </script>
@endcomponent
