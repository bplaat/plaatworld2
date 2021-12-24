<div class="immersive" wire:ignore>
    <canvas id="object-editor-canvas"></canvas>

    <div id="object-editor" v-cloak>
        <div class="m-3" style="position:absolute;top:0;left:0">
            <div class="p-3 mb-3 has-background-light" style="width:16rem;height:6.5rem;border-radius:3px;">
                <h1 class="title is-5 mb-3">@lang('admin/objects.editor.header') - {{ $object->name }}</h1>
                <div class="buttons mt-3 mb-0">
                    <button class="button is-link" @click="saveObject()">@{{ saved ? @json(__('admin/objects.editor.saved')) : @json(__('admin/objects.editor.save')) }}</button>
                    <a class="button is-danger" href="{{ route('admin.objects.crud') }}">@lang('admin/objects.editor.exit')</a>
                </div>
            </div>

            <div class="p-3 has-background-light" style="min-width:16rem;max-height:calc(100vh - 23rem);overflow-y:auto;border-radius:3px;">
                <div class="menu">
                    <p class="menu-label">@lang('admin/objects.editor.objects')</p>
                    <ul class="menu-list">
                        <li v-for="object in object.objects" :key="object.pivot.id">
                            <a :class="{'is-active': object.pivot.id == selectedObjectId}"
                                @click.prevent="selectObjectId(object.pivot.id)">
                                @{{ object.pivot.name }} (@{{ object.name }})
                                <button class="delete is-pulled-right ml-3" @click.prevent="deleteObject(object.pivot.id)"></button>
                            </a>
                        </li>
                    </ul>
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
                <label class="label is-small" for="name">@lang('admin/objects.editor.name')</label>
                <div class="control">
                    <input class="input is-small" type="text" id="name" v-model="selectedObject.name">
                </div>
            </div>

            <div class="field">
                <h3 class="title is-6">@lang('admin/objects.editor.position')</h3>
            </div>
            <div class="field">
                <label class="label is-small" for="position_x">@lang('admin/objects.editor.position_x')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="position_x" v-model="selectedObject.position_x">
                </div>
            </div>
            <div class="field">
                <label class="label is-small" for="position_y">@lang('admin/objects.editor.position_y')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="position_y" v-model="selectedObject.position_y">
                </div>
            </div>
            <div class="field">
                <label class="label is-small" for="position_z">@lang('admin/objects.editor.position_z')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="position_z" v-model="selectedObject.position_z">
                </div>
            </div>

            <div class="field">
                <h3 class="title is-6">@lang('admin/objects.editor.rotation')</h3>
            </div>
            <div class="field">
                <label class="label is-small" for="rotation_x">@lang('admin/objects.editor.rotation_x')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="rotation_x" v-model="selectedObject.rotation_x">
                </div>
            </div>
            <div class="field">
                <label class="label is-small" for="rotation_y">@lang('admin/objects.editor.rotation_y')</label>
                <div class="control">
                    <input class="input is-small" type="number" step="0.001" id="rotation_y" v-model="selectedObject.rotation_y">
                </div>
            </div>
            <div class="field">
                <label class="label is-small" for="rotation_z">@lang('admin/objects.editor.rotation_z')</label>
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

    <script src="/js/ObjectViewer.js"></script>
    <script src="/js/ObjectEditor.js"></script>
    <script>
        document.addEventListener('livewire:load', () => {
            new ObjectEditor({
                OBJECT_TYPE_GROUP: @json(App\Models\GameObject::TYPE_GROUP),
                OBJECT_TYPE_SPRITE: @json(App\Models\GameObject::TYPE_SPRITE),
                OBJECT_TYPE_FIXED_SPRITE: @json(App\Models\GameObject::TYPE_FIXED_SPRITE),
                OBJECT_TYPE_CUBE: @json(App\Models\GameObject::TYPE_CUBE),
                OBJECT_TYPE_CYLINDER: @json(App\Models\GameObject::TYPE_CYLINDER),
                OBJECT_TYPE_SPHERE: @json(App\Models\GameObject::TYPE_SPHERE),
                OBJECT_TYPE_PYRAMID: @json(App\Models\GameObject::TYPE_PYRAMID),
                livewire: @this,
                editorUser: @json($editorUser),
                textures: @json(App\Models\Texture::all()),
                objects: @json(App\Models\GameObject::where('type', '!=', App\Models\GameObject::TYPE_GROUP)->orderByRaw('LOWER(name)')->get()),
                object: @json($object)
            });
        });
    </script>
</div>
