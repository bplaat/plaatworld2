<div style="position: relative;" wire:ignore>
    <canvas id="object-editor-canvas"></canvas>

    <div id="object-editor">
        <div class="m-3 p-3 has-background-light" style="position:absolute;top:0;left:0">
            <h1 class="title is-5 mb-3">Object Editor - {{ $object->name }}</h1>
            <div class="buttons mt-3 mb-0">
                <button class="button is-link" onclick="saveWorld()">Save</button>
                <a class="button is-danger" href="{{ route('admin.objects.crud') }}">Exit</a>
            </div>
        </div>

        <div class="content has-background-light" style="position:absolute;top:16px;right:16px;padding:16px;min-width:250px;max-height:80%;overflow-y:scroll">
            <ul class="mt-0">
                <li>Cube
                    <ul>
                        <li>Cube
                            <ul>
                                <li>Cube</li>
                                <li>Cube</li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>Cube</li>
                <li>Cube
                    <ul>
                        <li>Cube</li>
                        <li>Cube
                            <ul>
                                <li>Cube</li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>Cube</li>
                <li>Cube
                    <ul>
                        <li>Cube</li>
                        <li>Cube
                            <ul>
                                <li>Cube</li>
                            </ul>
                        </li>
                        <li>Cube</li>
                        <li>Cube</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <script src="/js/ObjectEditor.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            new ObjectEditor({
                OBJECT_TYPE_SPRITE: @json(App\Models\GameObject::TYPE_SPRITE),
                OBJECT_TYPE_FIXED_SPRITE: @json(App\Models\GameObject::TYPE_FIXED_SPRITE),
                OBJECT_TYPE_CUBE: @json(App\Models\GameObject::TYPE_CUBE),
                OBJECT_TYPE_CYLINDER: @json(App\Models\GameObject::TYPE_CYLINDER),
                OBJECT_TYPE_SPHERE: @json(App\Models\GameObject::TYPE_SPHERE),
                OBJECT_TYPE_PYRAMID: @json(App\Models\GameObject::TYPE_PYRAMID),
                textures: @json(App\Models\Texture::all()),
                objects: @json(App\Models\GameObject::where('active', true)->with('objects')->get()),
                object: @json($object)
            });
        });
    </script>
</div>
