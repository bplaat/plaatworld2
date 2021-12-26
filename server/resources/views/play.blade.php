@component('layouts.app')
    @slot('title', __('play.title'))
    @slot('immersive', true)
    @slot('vuejs', true)
    @slot('threejs', true)
    @slot('statsjs', true)
    @slot('tweenjs', true)
    @slot('gamejs', true)

    <div class="immersive">
        <canvas id="game-canvas"></canvas>

        <div id="game" v-cloak>
            <svg v-if="pointerlock" style="position:absolute;top:0;left:0;right:0;bottom:0;margin:auto;" width="24" height="24">
                <rect fill="rgba(255, 255, 255, 0.9)" x="0" y="10" width="24" height="4" />
                <rect fill="rgba(255, 255, 255, 0.9)" x="10" y="0" width="4" height="24" />
            </svg>
            <div v-else style="position:absolute;top:0;left:0;right:0;bottom:0;background-color:rgba(0,0,0,0.5);pointer-events:none"></div>

            <div class="m-3 p-3 has-background-light" style="position:absolute;top:0;left:0;border-radius:3px;">
                <div v-if="connection.connected">
                    <div v-if="worldLoaded">
                        <div style="display:flex" :class="{'mb-3': index != sortedUsers.length - 1}" v-for="user, index in sortedUsers" :key="user.id">
                            <div class="media-left mr-0">
                            <div class="image is-medium is-round is-inline" :style="{backgroundImage: 'url(/storage/avatars/' + (user.avatar || 'default.png') + ')'}"></div>
                            </div>
                            <div class="media-content">
                                @{{ user.username }}
                            </div>
                        </div>
                    </div>
                    <p v-else><i>@lang('play.loading')</i></p>
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
                PLAYER_MOVE_SEND_TIMEOUT: @json(config('game.player_move_send_timeout')),
                CHAT_FADE_TIMEOUT: @json(config('game.chat_fade_timeout')),

                worldId: @json(request('world_id', App\Models\World::where('name', 'PlaatWorld')->first()->id)),
                userId: @json(Auth::id()),
                authToken: @json(explode('|', Auth::user()->authToken(), 2)[1])
            });
        };
    </script>
@endcomponent
