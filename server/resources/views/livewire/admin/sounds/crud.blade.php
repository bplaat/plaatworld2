<div class="container">
    <h2 class="title">@lang('admin/sounds.crud.header')</h2>

    <x-search-header :itemName="__('admin/sounds.crud.sounds')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/sounds.crud.create_sound')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/sounds.crud.text_asc')</option>
            <option value="text_desc">@lang('admin/sounds.crud.text_desc')</option>
            <option value="created_at_desc">@lang('admin/sounds.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/sounds.crud.created_at_asc')</option>
        </x-slot>
    </x-search-header>

    @if ($sounds->count() > 0)
        {{ $sounds->links() }}

        <div class="columns is-multiline">
            @foreach ($sounds as $sound)
                <livewire:admin.sounds.item :sound="$sound" :wire:key="$sound->id" />
            @endforeach
        </div>

        {{ $sounds->links() }}
    @else
        <p><i>@lang('admin/sounds.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createSound" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/sounds.crud.create_sound')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="text">@lang('admin/sounds.crud.text')</label>
                        <div class="control">
                            <input class="input @error('sound.text') is-danger @enderror" type="text" id="text"
                                wire:model.defer="sound.text" required>
                        </div>
                        @error('sound.text') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="audio">@lang('admin/sounds.crud.audio')</label>
                        <div class="control">
                            <input class="input @error('audio') is-danger @enderror" type="file" accept=".wav,.mp3,.m4a" id="audio" wire:model="audio">
                        </div>
                        @error('audio')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/sounds.crud.audio_help')</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="active">@lang('admin/sounds.crud.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="sound.active">
                            @lang('admin/sounds.crud.active_sound')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/sounds.crud.create_sound')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/sounds.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
