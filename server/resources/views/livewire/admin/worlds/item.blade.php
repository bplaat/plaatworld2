<div class="column is-one-third">
    <div class="card">
        <div class="card-content content">
            <h4 class="mb-0">
                {{ $world->name }}
                @if (!$world->active)
                    <span class="tag is-warning is-pulled-right">{{ Str::upper(__('admin/worlds.item.inactive')) }}</span>
                @endif
            </h4>
        </div>

        <div class="card-footer">
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/worlds.item.edit')</a>
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/worlds.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editWorld" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/worlds.item.edit_world')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/worlds.item.name')</label>
                        <div class="control">
                            <input class="input @error('world.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="world.name" tabindex="1" required>
                        </div>
                        @error('world.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="width">@lang('admin/worlds.item.width')</label>
                                <div class="control">
                                    <input class="input @error('world.width') is-danger @enderror" type="number" step="0.001" id="width"
                                        wire:model.defer="world.width" required>
                                </div>
                                @error('world.width') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="height">@lang('admin/worlds.item.height')</label>
                                <div class="control">
                                    <input class="input @error('world.height') is-danger @enderror" type="number" step="0.001" id="height"
                                        wire:model.defer="world.height" required>
                                </div>
                                @error('world.height') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_position_x">@lang('admin/worlds.item.spawn_position_x')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_position_x') is-danger @enderror" type="number" step="0.001" id="spawn_position_x"
                                        wire:model.defer="world.spawn_position_x" required>
                                </div>
                                @error('world.spawn_position_x') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_position_y">@lang('admin/worlds.item.spawn_position_y')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_position_y') is-danger @enderror" type="number" step="0.001" id="spawn_position_y"
                                        wire:model.defer="world.spawn_position_y" required>
                                </div>
                                @error('world.spawn_position_y') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_position_z">@lang('admin/worlds.item.spawn_position_z')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_position_z') is-danger @enderror" type="number" step="0.001" id="spawn_position_z"
                                        wire:model.defer="world.spawn_position_z" required>
                                </div>
                                @error('world.spawn_position_z') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_rotation_x">@lang('admin/worlds.item.spawn_rotation_x')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_rotation_x') is-danger @enderror" type="number" step="0.001" id="spawn_rotation_x"
                                        wire:model.defer="world.spawn_rotation_x" required>
                                </div>
                                @error('world.spawn_rotation_x') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_rotation_y">@lang('admin/worlds.item.spawn_rotation_y')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_rotation_y') is-danger @enderror" type="number" step="0.001" id="spawn_rotation_y"
                                        wire:model.defer="world.spawn_rotation_y" required>
                                </div>
                                @error('world.spawn_rotation_y') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_rotation_z">@lang('admin/worlds.item.spawn_rotation_z')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_rotation_z') is-danger @enderror" type="number" step="0.001" id="spawn_rotation_z"
                                        wire:model.defer="world.spawn_rotation_z" required>
                                </div>
                                @error('world.spawn_rotation_z') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label" for="active">@lang('admin/worlds.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="world.active">
                            @lang('admin/worlds.item.active_world')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/worlds.item.edit_world')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/worlds.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/worlds.item.delete_world')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/worlds.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteWorld()" wire:loading.attr="disabled">@lang('admin/worlds.item.delete_world')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/worlds.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
