@component('layouts.app')
    @slot('title', __('play.title'))
    @slot('immersive', true)
    @slot('threejs', true)
    @slot('statsjs', true)

    <canvas id="game-canvas"></canvas>

    <script src="/js/Game.js"></script>
    <script>
        new Game({
            OBJECT_TYPE_GROUP: @json(App\Models\GameObject::TYPE_GROUP),
            OBJECT_TYPE_SPRITE: @json(App\Models\GameObject::TYPE_SPRITE),
            OBJECT_TYPE_FIXED_SPRITE: @json(App\Models\GameObject::TYPE_FIXED_SPRITE),
            OBJECT_TYPE_CUBE: @json(App\Models\GameObject::TYPE_CUBE),
            OBJECT_TYPE_CYLINDER: @json(App\Models\GameObject::TYPE_CYLINDER),
            OBJECT_TYPE_SPHERE: @json(App\Models\GameObject::TYPE_SPHERE),
            OBJECT_TYPE_PYRAMID: @json(App\Models\GameObject::TYPE_PYRAMID),
            authToken: @json(Auth::user()->authToken())
        });
    </script>
@endcomponent
