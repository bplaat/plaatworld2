@component('layouts.app')
    @slot('title', __('play.title'))
    @slot('immersive', true)
    @slot('vuejs', true)
    @slot('threejs', true)
    @slot('statsjs', true)
    @slot('gamejs', true)

    <div class="immersive">
        <canvas id="game-canvas"></canvas>

        <div id="game" v-cloak>
            <div class="m-3 p-3 has-background-light" style="position:absolute;top:0;left:0;border-radius:3px;">
                <div v-if="connection.connected">
                    <div class="menu mb-2">
                        <p class="menu-label">@lang('play.users')</p>
                    </div>
                    <div style="display:flex" class="mt-3" v-for="user in sortedUsers" :key="user.id">
                        <div class="media-left mr-0">
                            <div class="image is-medium is-round is-inline" :style="{backgroundImage: 'url(/storage/avatars/' + (user.avatar || 'default.png') + ')'}"></div>
                        </div>
                        <div class="media-content">
                            @{{ user.username }}
                        </div>
                    </div>
                </div>
                <p v-else><i>@lang('play.connecting')</i></p>
            </div>

            <div class="m-3 p-3 has-background-light" style="position:absolute;left:0;bottom:0;border-radius:3px;" v-if="connection.connected">
                <div style="display:flex" class="mb-3" v-for="chat in chats">
                    <div class="media-left mr-0">
                        <div class="image is-medium is-round is-inline" :style="{backgroundImage: 'url(/storage/avatars/' + (chat.user.avatar || 'default.png') + ')'}"></div>
                    </div>
                    <div class="media-content">
                        <b>@{{ chat.user.username }}</b>: @{{ chat.message }}
                    </div>
                </div>

                <div class="control">
                    <input class="input" type="text" placeholder="@lang('play.chat_placeholder')" v-model="chatMessage" @change="sendChat">
                </div>
            </div>
        </div>
    </div>

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

                PLAYER_HEIGHT: @json(config('game.player_height')),
                CHAT_FADE_TIMEOUT: @json(config('game.chat_fade_time')),

                worldId: @json(App\Models\World::where('name', 'PlaatWorld')->first()->id),
                userId: @json(Auth::id()),
                authToken: @json(explode('|', Auth::user()->authToken(), 2)[1])
            });
        };
    </script>
@endcomponent
