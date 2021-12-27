<div class="container">
    <h2 class="title">@lang('admin/items.crud.header')</h2>

    <x-search-header :itemName="__('admin/items.crud.items')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/items.crud.create_item')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/items.crud.name_asc')</option>
            <option value="name_desc">@lang('admin/items.crud.name_desc')</option>
            <option value="created_at_desc">@lang('admin/items.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/items.crud.created_at_asc')</option>
        </x-slot>
    </x-search-header>

    @if ($items->count() > 0)
        {{ $items->links() }}

        <div class="columns is-multiline">
            @foreach ($items as $item)
                <livewire:admin.items.item :item="$item" :wire:key="$item->id" />
            @endforeach
        </div>

        {{ $items->links() }}
    @else
        <p><i>@lang('admin/items.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createItem" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/items.crud.create_item')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/items.crud.name')</label>
                        <div class="control">
                            <input class="input @error('item.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="item.name" required>
                        </div>
                        @error('item.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="stackability">@lang('admin/items.crud.stackability')</label>
                        <div class="control">
                            <input class="input @error('item.stackability') is-danger @enderror" type="number" step="1" id="stackability"
                                wire:model.defer="item.stackability" required>
                        </div>
                        @error('item.stackability') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <livewire:components.texture-chooser name="texture" includeInactive="true" />

                    <div class="field">
                        <label class="label" for="active">@lang('admin/items.crud.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="item.active">
                            @lang('admin/items.crud.active_item')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/items.crud.create_item')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/items.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
