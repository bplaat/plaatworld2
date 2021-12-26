<div class="immersive" wire:ignore>
    <canvas id="world-editor-canvas"></canvas>

    <div id="world-editor" v-cloak>
        <div class="m-3" style="position:absolute;top:0;left:0">
            <div class="p-3 mb-3 has-background-light" style="min-width:16rem;height:6.5rem;border-radius:3px;">
                <h1 class="title is-5 mb-3">@lang('admin/worlds.editor.header') - {{ $world->name }}</h1>
                <div class="buttons mt-3 mb-0">
                    <button class="button is-link" @click="saveWorld()">@{{ saved ? @json(__('admin/worlds.editor.saved')) : @json(__('admin/worlds.editor.save')) }}</button>
                    @if ($world->sky_texture_id != null)
                        <button class="button" @click="skybox = !skybox">@{{ skybox ? @json(__('admin/worlds.editor.skybox_hide')) : @json(__('admin/worlds.editor.skybox_show')) }}</button>
                    @endif
                    <a class="button is-danger" href="{{ route('admin.worlds.crud') }}">@lang('admin/worlds.editor.exit')</a>
                </div>
            </div>

            <div class="p-3 has-background-light" style="min-width:16rem;max-height:calc(100vh - 23rem);overflow-y:auto;border-radius:3px;">
                <div class="menu">
                    <p class="menu-label">@lang('admin/worlds.editor.objects')</p>
                    <ul v-if="world.objects.length > 0" class="menu-list">
                        <li v-for="object in world.objects" :key="object.pivot.id">
                            <a :class="{'is-active': object.pivot.id == selectedObjectId}" style="display: flex;"
                                @click.prevent="selectObjectId(object.pivot.id)">
                                <span style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">@{{ object.pivot.name }} (@{{ object.name }})</span>
                                <button class="delete ml-3" @click.prevent="deleteObject(object.pivot.id)"></button>
                            </a>
                        </li>
                    </ul>
                    <p v-else><i>@lang('admin/objects.editor.objects_empty')</i></p>
                </div>
            </div>
        </div>

        <div v-if="selectedObjectId != null" class="m-3 p-3 has-background-light"
            style="position:absolute;top:0;right:0;width:16rem;max-height:calc(100vh - 19.5rem);overflow-y:auto;border-radius:3px;">
            <div class="field">
                <h2 class="title is-6">
                    House (Cube)
                    <button class="delete is-small is-pulled-right" @click="selectedObjectId = null"></button>
                </h2>
            </div>
            <div class="field">
                <label class="label is-small" for="name">@lang('admin/worlds.editor.name')</label>
                <div class="control">
                    <input class="input is-small" type="text" id="name" v-model="selectedObject.name">
                </div>
            </div>

            <div class="field">
                <h3 class="title is-6">@lang('admin/worlds.editor.position')</h3>
            </div>
            <div class="field">
                <label class="label is-small" for="position_x">@lang('admin/worlds.editor.position_x')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="position_x" v-model="selectedObject.position_x">
                </div>
            </div>
            <div class="field">
                <label class="label is-small" for="position_y">@lang('admin/worlds.editor.position_y')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="position_y" v-model="selectedObject.position_y">
                </div>
            </div>
            <div class="field">
                <label class="label is-small" for="position_z">@lang('admin/worlds.editor.position_z')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="position_z" v-model="selectedObject.position_z">
                </div>
            </div>

            <div class="field">
                <h3 class="title is-6">@lang('admin/worlds.editor.rotation')</h3>
            </div>
            <div class="field">
                <label class="label is-small" for="rotation_x">@lang('admin/worlds.editor.rotation_x')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="rotation_x" v-model="selectedObject.rotation_x">
                </div>
            </div>
            <div class="field" v-if="selectedObject.type != {{ App\Models\GameObject::TYPE_SPRITE }}">
                <label class="label is-small" for="rotation_y">@lang('admin/worlds.editor.rotation_y')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="rotation_y" v-model="selectedObject.rotation_y">
                </div>
            </div>
            <div class="field">
                <label class="label is-small" for="rotation_z">@lang('admin/worlds.editor.rotation_z')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="rotation_z" v-model="selectedObject.rotation_z">
                </div>
            </div>
        </div>

        <div id="objects-selector" class="px-1 py-2 has-background-light" style="position:absolute;left:0;bottom:0;right:0;overflow-x:scroll;display:flex;height:10rem">
            <div id="object-button" class="has-background-grey-lighter mx-1" style="text-align: center;border-radius:3px;" v-for="object in objects" @click="addObject(object)" :key="object.id">
                <canvas :id="'object-' + object.id + '-canvas'" class="is-block" width="100" height="100"></canvas>
                <div style="width: 100px;white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">@{{ object.name }}</div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:load', () => {
            new WorldEditor({
                OBJECT_TYPE_GROUP: @json(App\Models\GameObject::TYPE_GROUP),
                OBJECT_TYPE_SPRITE: @json(App\Models\GameObject::TYPE_SPRITE),
                OBJECT_TYPE_FIXED_SPRITE: @json(App\Models\GameObject::TYPE_FIXED_SPRITE),
                OBJECT_TYPE_CUBE: @json(App\Models\GameObject::TYPE_CUBE),
                OBJECT_TYPE_CYLINDER: @json(App\Models\GameObject::TYPE_CYLINDER),
                OBJECT_TYPE_SPHERE: @json(App\Models\GameObject::TYPE_SPHERE),
                OBJECT_TYPE_PYRAMID: @json(App\Models\GameObject::TYPE_PYRAMID),

                PLAYER_HEIGHT: @json(config('game.player_height')),

                livewire: @this,
                editorUser: JSON.parse('@json($editorUser)'),
                textures: JSON.parse('@json(App\Models\Texture::all())'),
                objects: JSON.parse('@json($objects)'),
                world: JSON.parse('@json($world)')
            });
        });
    </script>
</div>
