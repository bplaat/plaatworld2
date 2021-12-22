<div class="column is-one-quarter">
    <div class="card">
        <div class="card-image">
            <div class="image is-square" style="background-image: url(/storage/textures/{{ $object->texture != null && $object->texture->image != null ? $object->texture->image : 'default.png' }});"></div>

            <div class="card-image-tags">
                @if (!$object->active)
                    <span class="tag is-warning">{{ Str::upper(__('admin/objects.item.inactive')) }}</span>
                @endif
            </div>
        </div>

        <div class="card-content content">
            <h4 class="mb-0">{{ $object->name }}</h4>
        </div>

        <div class="card-footer">
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/objects.item.edit')</a>
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/objects.item.delete')</a>
        </div>
    </div>

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
                                    <option value="{{ App\Models\GameObject::TYPE_SPRITE }}">@lang('admin/objects.item.type_sprite')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_CUBE }}">@lang('admin/objects.item.type_cube')</option>
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
