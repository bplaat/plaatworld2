<div class="column is-one-quarter">
    <div class="card">
        <div class="card-image">
            <div style="position: relative; padding-top: 100%" wire:ignore>
                <canvas id="object-{{ $object->id}}-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"></canvas>
            </div>

            <div class="card-image-tags">
                @if ($object->type == App\Models\GameObject::TYPE_GROUP)
                    <span class="tag">{{ Str::upper(__('admin/objects.item.type_group')) }}</span>
                @endif

                @if (!$object->active)
                    <span class="tag is-warning">{{ Str::upper(__('admin/objects.item.inactive')) }}</span>
                @endif
            </div>
        </div>

        <div class="card-content content">
            <h4 class="mb-0">{{ $object->name }}</h4>
        </div>

        <div class="card-footer">
            @if ($object->type == App\Models\GameObject::TYPE_GROUP)
                <a class="card-footer-item" href="{{ route('admin.objects.editor', $object) }}">@lang('admin/objects.item.editor')</a>
            @endif
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/objects.item.edit')</a>
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/objects.item.delete')</a>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:load', () => {
            new ObjectViewer({
                OBJECT_TYPE_GROUP: @json(App\Models\GameObject::TYPE_GROUP),
                OBJECT_TYPE_SPRITE: @json(App\Models\GameObject::TYPE_SPRITE),
                OBJECT_TYPE_FIXED_SPRITE: @json(App\Models\GameObject::TYPE_FIXED_SPRITE),
                OBJECT_TYPE_CUBE: @json(App\Models\GameObject::TYPE_CUBE),
                OBJECT_TYPE_CYLINDER: @json(App\Models\GameObject::TYPE_CYLINDER),
                OBJECT_TYPE_SPHERE: @json(App\Models\GameObject::TYPE_SPHERE),
                OBJECT_TYPE_PYRAMID: @json(App\Models\GameObject::TYPE_PYRAMID),

                canvas: document.getElementById('object-' + @json($object->id) + '-canvas'),
                backgroundColor: getComputedStyle(document.querySelector('.card')).backgroundColor,
                canvasSize: () => document.querySelector('.card-image').offsetWidth,
                object: JSON.parse('@json($object)'),
                animated: true
            });
        });
    </script>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editObject" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/objects.item.edit_object')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="type">@lang('admin/objects.item.type')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('object.type') is-danger @enderror">
                                <select id="type" wire:model.defer="object.type">
                                    <option value="{{ App\Models\GameObject::TYPE_GROUP }}">@lang('admin/objects.item.type_group')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_SPRITE }}">@lang('admin/objects.item.type_sprite')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_FIXED_SPRITE }}">@lang('admin/objects.item.type_fixed_sprite')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_CUBE }}">@lang('admin/objects.item.type_cube')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_CYLINDER }}">@lang('admin/objects.item.type_cylinder')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_SPHERE }}">@lang('admin/objects.item.type_sphere')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_PYRAMID }}">@lang('admin/objects.item.type_pyramid')</option>
                                </select>
                            </div>
                        </div>
                        @error('object.type') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="name">@lang('admin/objects.item.name')</label>
                        <div class="control">
                            <input class="input @error('object.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="object.name" required>
                        </div>
                        @error('object.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="width">@lang('admin/objects.item.width')</label>
                                <div class="control">
                                    <input class="input @error('object.width') is-danger @enderror" type="number" step="0.001" id="width"
                                        wire:model.defer="object.width" required>
                                </div>
                                @error('object.width') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="height">@lang('admin/objects.item.height')</label>
                                <div class="control">
                                    <input class="input @error('object.height') is-danger @enderror" type="number" step="0.001" id="height"
                                        wire:model.defer="object.height" required>
                                </div>
                                @error('object.height') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="depth">@lang('admin/objects.item.depth')</label>
                                <div class="control">
                                    <input class="input @error('object.depth') is-danger @enderror" type="number" step="0.001" id="depth"
                                        wire:model.defer="object.depth" required>
                                </div>
                                @error('object.depth') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <livewire:components.texture-chooser name="item_texture" :textureId="$object->texture_id" includeInactive="true" />

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="texture_repeat_x">@lang('admin/objects.crud.texture_repeat_x')</label>
                                <div class="control">
                                    <input class="input @error('object.texture_repeat_x') is-danger @enderror" type="number" step="1" id="texture_repeat_x"
                                        wire:model.defer="object.texture_repeat_x" required>
                                </div>
                                @error('object.texture_repeat_x') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="texture_repeat_y">@lang('admin/objects.crud.texture_repeat_y')</label>
                                <div class="control">
                                    <input class="input @error('object.texture_repeat_y') is-danger @enderror" type="number" step="1" id="texture_repeat_y"
                                        wire:model.defer="object.texture_repeat_y" required>
                                </div>
                                @error('object.texture_repeat_y') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <livewire:components.item-chooser name="item_item" :itemId="$object->item_id" includeInactive="true" />

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="item_chance">@lang('admin/objects.crud.item_chance')</label>
                                <div class="control">
                                    <input class="input @error('object.item_chance') is-danger @enderror" type="number" step="1" id="item_chance"
                                        wire:model.defer="object.item_chance" required>
                                </div>
                                @error('object.item_chance') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="item_amount">@lang('admin/objects.crud.item_amount')</label>
                                <div class="control">
                                    <input class="input @error('object.item_amount') is-danger @enderror" type="number" step="1" id="item_amount"
                                        wire:model.defer="object.item_amount" required>
                                </div>
                                @error('object.item_amount') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="active">@lang('admin/objects.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="object.active">
                            @lang('admin/objects.item.active_object')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/objects.item.edit_object')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/objects.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/objects.item.delete_object')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/objects.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteObject()" wire:loading.attr="disabled">@lang('admin/objects.item.delete_object')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/objects.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
