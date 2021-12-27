<div class="column is-one-quarter">
    <div class="card">
        <div class="card-image">
            <div class="image is-square" style="background-image: url(/storage/textures/{{ $item->texture != null && $item->texture->image != null ? $item->texture->image : 'default.png' }});"></div>

            <div class="card-image-tags">
                @if (!$item->active)
                    <span class="tag is-warning">{{ Str::upper(__('admin/items.item.inactive')) }}</span>
                @endif
            </div>
        </div>

        <div class="card-content content">
            <h4 class="mb-0">{{ $item->name }}</h4>
        </div>

        <div class="card-footer">
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/items.item.edit')</a>
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/items.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editItem" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/items.item.edit_item')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/items.item.name')</label>
                        <div class="control">
                            <input class="input @error('item.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="item.name" required>
                        </div>
                        @error('item.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="stackability">@lang('admin/items.item.stackability')</label>
                        <div class="control">
                            <input class="input @error('item.stackability') is-danger @enderror" type="number" step="1" id="stackability"
                                wire:model.defer="item.stackability" required>
                        </div>
                        @error('item.stackability') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <livewire:components.texture-chooser name="item_texture" :textureId="$item->texture_id" includeInactive="true" />

                    <div class="field">
                        <label class="label" for="active">@lang('admin/items.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="item.active">
                            @lang('admin/items.item.active_item')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/items.item.edit_item')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/items.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/items.item.delete_item')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/items.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteItem()" wire:loading.attr="disabled">@lang('admin/items.item.delete_item')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/items.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
