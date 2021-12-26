@component('layouts.app')
    @slot('title', __('play.title'))
    @slot('immersive', true)
    @slot('threejs', true)
    @slot('statsjs', true)
    @slot('gamejs', true)

    <canvas id="game-canvas"></canvas>

    <script>
        window.onload = () => {
            new Game({
                OBJECT_TYPE_GROUP: @json(App\Models\GameObject::TYPE_GROUP),
                OBJECT_TYPE_SPRITE: @json(App\Models\GameObject::TYPE_SPRITE),
                OBJECT_TYPE_FIXED_SPRITE: @json(App\Models\GameObject::TYPE_FIXED_SPRITE),
                OBJECT_TYPE_CUBE: @json(App\Models\GameObject::TYPE_CUBE),
                OBJECT_TYPE_CYLINDER: @json(App\Models\GameObject::TYPE_CYLINDER),
                OBJECT_TYPE_SPHERE: @json(App\Models\GameObject::TYPE_SPHERE),
                OBJECT_TYPE_PYRAMID: @json(App\Models\GameObject::TYPE_PYRAMID),

                WEBSOCKETS_URL: @json(config('websockets.url')),
                WEBSOCKETS_RECONNECT_TIMEOUT: @json(config('websockets.reconnect_timeout')),

                worldId: @json(App\Models\World::where('name', 'PlaatWorld')->first()->id),
                userId: @json(Auth::id()),
                authToken: @json(explode('|', Auth::user()->authToken(), 2)[1])
            });
        };
    </script>
@endcomponent
