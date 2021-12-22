<div class="column is-one-quarter">
    <div class="card">
        <div class="card-image">
            <div class="image is-square" style="background-image: url(/storage/textures/{{ $texture->image ?? 'default.png' }});"></div>

            <div class="card-image-tags">
                @if (!$texture->active)
                    <span class="tag is-warning">{{ Str::upper(__('admin/textures.item.inactive')) }}</span>
                @endif
            </div>
        </div>

        <div class="card-content content">
            <h4 class="mb-0">{{ $texture->name }}</h4>
        </div>

        <div class="card-footer">
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/textures.item.edit')</a>
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/textures.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editTexture" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/textures.item.edit_texture')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/textures.item.name')</label>
                        <div class="control">
                            <input class="input @error('texture.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="texture.name" tabindex="1" required>
                        </div>
                        @error('texture.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="image">@lang('admin/textures.item.image')</label>
                        @if ($texture->image != null)
                            <div class="box" style="width: 50%;">
                                <div class="image is-square is-rounded" style="background-image: url(/storage/textures/{{ $texture->image }});"></div>
                            </div>
                        @endif
                    </div>

                    <div class="field">
                        <div class="control">
                            <input class="input @error('image') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png" id="image" wire:model="image">
                        </div>
                        @error('image')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/textures.item.image_help')</p>
                        @enderror
                    </div>

                    @if ($texture->image != null)
                        <div class="field">
                            <div class="control">
                                <button type="button" class="button is-danger" wire:click="deleteImage" wire:loading.attr="disabled">@lang('admin/textures.item.delete_image')</button>
                            </div>
                        </div>
                    @endif

                    <div class="field">
                        <label class="label" for="active">@lang('admin/textures.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="texture.active">
                            @lang('admin/textures.item.active_texture')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/textures.item.edit_texture')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/textures.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/textures.item.delete_texture')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/textures.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteTexture()" wire:loading.attr="disabled">@lang('admin/textures.item.delete_texture')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/textures.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
