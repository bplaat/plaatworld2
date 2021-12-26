<div class="container">
    <h2 class="title">@lang('admin/worlds.crud.header')</h2>

    <x-search-header :itemName="__('admin/worlds.crud.worlds')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/worlds.crud.create_world')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/worlds.crud.name_asc')</option>
            <option value="name_desc">@lang('admin/worlds.crud.name_desc')</option>
            <option value="created_at_desc">@lang('admin/worlds.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/worlds.crud.created_at_asc')</option>
        </x-slot>
    </x-search-header>

    @if ($worlds->count() > 0)
        {{ $worlds->links() }}

        <div class="columns is-multiline">
            @foreach ($worlds as $world)
                <livewire:admin.worlds.item :world="$world" :wire:key="$world->id" />
            @endforeach
        </div>

        {{ $worlds->links() }}
    @else
        <p><i>@lang('admin/worlds.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createWorld" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/worlds.crud.create_world')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/worlds.crud.name')</label>
                        <div class="control">
                            <input class="input @error('world.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="world.name" required>
                        </div>
                        @error('world.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="width">@lang('admin/worlds.crud.width')</label>
                                <div class="control">
                                    <input class="input @error('world.width') is-danger @enderror" type="number" step="0.001" id="width"
                                        wire:model.defer="world.width" required>
                                </div>
                                @error('world.width') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="height">@lang('admin/worlds.crud.height')</label>
                                <div class="control">
                                    <input class="input @error('world.height') is-danger @enderror" type="number" step="0.001" id="height"
                                        wire:model.defer="world.height" required>
                                </div>
                                @error('world.height') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="gravity">@lang('admin/worlds.crud.gravity')</label>
                                <div class="control">
                                    <input class="input @error('world.gravity') is-danger @enderror" type="number" step="0.001" id="gravity"
                                        wire:model.defer="world.gravity" required>
                                </div>
                                @error('world.gravity') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_position_x">@lang('admin/worlds.crud.spawn_position_x')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_position_x') is-danger @enderror" type="number" step="0.001" id="spawn_position_x"
                                        wire:model.defer="world.spawn_position_x" required>
                                </div>
                                @error('world.spawn_position_x') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_position_y">@lang('admin/worlds.crud.spawn_position_y')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_position_y') is-danger @enderror" type="number" step="0.001" id="spawn_position_y"
                                        wire:model.defer="world.spawn_position_y" required>
                                </div>
                                @error('world.spawn_position_y') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_position_z">@lang('admin/worlds.crud.spawn_position_z')</label>
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
                                <label class="label" for="spawn_rotation_x">@lang('admin/worlds.crud.spawn_rotation_x')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_rotation_x') is-danger @enderror" type="number" step="0.001" id="spawn_rotation_x"
                                        wire:model.defer="world.spawn_rotation_x" required>
                                </div>
                                @error('world.spawn_rotation_x') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_rotation_y">@lang('admin/worlds.crud.spawn_rotation_y')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_rotation_y') is-danger @enderror" type="number" step="0.001" id="spawn_rotation_y"
                                        wire:model.defer="world.spawn_rotation_y" required>
                                </div>
                                @error('world.spawn_rotation_y') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="spawn_rotation_z">@lang('admin/worlds.crud.spawn_rotation_z')</label>
                                <div class="control">
                                    <input class="input @error('world.spawn_rotation_z') is-danger @enderror" type="number" step="0.001" id="spawn_rotation_z"
                                        wire:model.defer="world.spawn_rotation_z" required>
                                </div>
                                @error('world.spawn_rotation_z') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <livewire:components.texture-chooser name="texture" includeInactive="true" />

                    <div class="field">
                        <label class="label" for="active">@lang('admin/worlds.crud.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="world.active">
                            @lang('admin/worlds.crud.active_world')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/worlds.crud.create_world')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/worlds.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
