<div class="container">
    <h2 class="title">@lang('admin/objects.crud.header')</h2>

    <x-search-header :itemName="__('admin/objects.crud.objects')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/objects.crud.create_object')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/objects.crud.name_asc')</option>
            <option value="name_desc">@lang('admin/objects.crud.name_desc')</option>
            <option value="created_at_desc">@lang('admin/objects.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/objects.crud.created_at_asc')</option>
        </x-slot>
    </x-search-header>

    @if ($objects->count() > 0)
        {{ $objects->links() }}

        <div class="columns is-multiline">
            @foreach ($objects as $object)
                <livewire:admin.objects.item :object="$object" :wire:key="$object->id" />
            @endforeach
        </div>

        {{ $objects->links() }}
    @else
        <p><i>@lang('admin/objects.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createObject" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/objects.crud.create_object')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="type">@lang('admin/objects.crud.type')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('object.type') is-danger @enderror">
                                <select id="type" wire:model.defer="object.type">
                                    <option value="{{ App\Models\GameObject::TYPE_GROUP }}">@lang('admin/objects.crud.type_group')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_SPRITE }}">@lang('admin/objects.crud.type_sprite')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_FIXED_SPRITE }}">@lang('admin/objects.crud.type_fixed_sprite')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_CUBE }}">@lang('admin/objects.crud.type_cube')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_CYLINDER }}">@lang('admin/objects.crud.type_cylinder')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_SPHERE }}">@lang('admin/objects.crud.type_sphere')</option>
                                    <option value="{{ App\Models\GameObject::TYPE_PYRAMID }}">@lang('admin/objects.crud.type_pyramid')</option>
                                </select>
                            </div>
                        </div>
                        @error('object.type') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="name">@lang('admin/objects.crud.name')</label>
                        <div class="control">
                            <input class="input @error('object.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="object.name" required>
                        </div>
                        @error('object.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="columns">
                        <div class="column">
                            <div class="field">
                                <label class="label" for="width">@lang('admin/objects.crud.width')</label>
                                <div class="control">
                                    <input class="input @error('object.width') is-danger @enderror" type="number" step="0.001" id="width"
                                        wire:model.defer="object.width" required>
                                </div>
                                @error('object.width') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="height">@lang('admin/objects.crud.height')</label>
                                <div class="control">
                                    <input class="input @error('object.height') is-danger @enderror" type="number" step="0.001" id="height"
                                        wire:model.defer="object.height" required>
                                </div>
                                @error('object.height') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="column">
                            <div class="field">
                                <label class="label" for="depth">@lang('admin/objects.crud.depth')</label>
                                <div class="control">
                                    <input class="input @error('object.depth') is-danger @enderror" type="number" step="0.001" id="depth"
                                        wire:model.defer="object.depth" required>
                                </div>
                                @error('object.depth') <p class="help is-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <livewire:components.texture-chooser name="texture" includeInactive="true" />

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

                    <livewire:components.item-chooser name="item" includeInactive="true" />

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
                        <label class="label" for="active">@lang('admin/objects.crud.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="object.active">
                            @lang('admin/objects.crud.active_object')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/objects.crud.create_object')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/objects.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
