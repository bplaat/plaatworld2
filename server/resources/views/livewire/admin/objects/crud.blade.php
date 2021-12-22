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
                        <label class="label" for="type">@lang('admin/objects.item.type')</label>
                        <div class="control">
                            <div class="select is-fullwidth @error('object.type') is-danger @enderror">
                                <select id="type" wire:model.defer="object.type">
                                    <option value="{{ App\Models\GameObject::TYPE_SPRITE }}">@lang('admin/objects.crud.type_sprite')</option>
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
                    </div>

                    <livewire:components.texture-chooser name="texture" includeInactive="true" />
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/objects.crud.create_object')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/objects.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
