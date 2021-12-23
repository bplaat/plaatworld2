<div class="container">
    <h2 class="title">@lang('admin/textures.crud.header')</h2>

    <x-search-header :itemName="__('admin/textures.crud.textures')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/textures.crud.create_texture')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/textures.crud.name_asc')</option>
            <option value="name_desc">@lang('admin/textures.crud.name_desc')</option>
            <option value="created_at_desc">@lang('admin/textures.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/textures.crud.created_at_asc')</option>
        </x-slot>
    </x-search-header>

    @if ($textures->count() > 0)
        {{ $textures->links() }}

        <div class="columns is-multiline">
            @foreach ($textures as $texture)
                <livewire:admin.textures.item :texture="$texture" :wire:key="$texture->id" />
            @endforeach
        </div>

        {{ $textures->links() }}
    @else
        <p><i>@lang('admin/textures.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createTexture" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/textures.crud.create_texture')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="name">@lang('admin/textures.crud.name')</label>
                        <div class="control">
                            <input class="input @error('texture.name') is-danger @enderror" type="text" id="name"
                                wire:model.defer="texture.name" required>
                        </div>
                        @error('texture.name') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="image">@lang('admin/textures.crud.image')</label>
                        <div class="control">
                            <input class="input @error('image') is-danger @enderror" type="file" accept=".jpg,.jpeg,.png" id="image" wire:model="image">
                        </div>
                        @error('image')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/textures.crud.image_help')</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="transparent">@lang('admin/textures.crud.transparent')</label>
                        <label class="checkbox" for="transparent">
                            <input type="checkbox" id="transparent" wire:model.defer="texture.transparent">
                            @lang('admin/textures.crud.transparent_texture')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/textures.crud.create_texture')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/textures.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
