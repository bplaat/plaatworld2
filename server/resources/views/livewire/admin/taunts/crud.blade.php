<div class="container">
    <h2 class="title">@lang('admin/taunts.crud.header')</h2>

    <x-search-header :itemName="__('admin/taunts.crud.taunts')">
        <div class="buttons">
            <button class="button is-link" wire:click="$set('isCreating', true)" wire:loading.attr="disabled">@lang('admin/taunts.crud.create_taunt')</button>
        </div>

        <x-slot name="sorters">
            <option value="">@lang('admin/taunts.crud.text_asc')</option>
            <option value="text_desc">@lang('admin/taunts.crud.text_desc')</option>
            <option value="created_at_desc">@lang('admin/taunts.crud.created_at_desc')</option>
            <option value="created_at">@lang('admin/taunts.crud.created_at_asc')</option>
        </x-slot>
    </x-search-header>

    @if ($taunts->count() > 0)
        {{ $taunts->links() }}

        <div class="columns is-multiline">
            @foreach ($taunts as $taunt)
                <livewire:admin.taunts.item :taunt="$taunt" :wire:key="$taunt->id" />
            @endforeach
        </div>

        {{ $taunts->links() }}
    @else
        <p><i>@lang('admin/taunts.crud.empty')</i></p>
    @endif

    @if ($isCreating)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isCreating', false)"></div>

            <form wire:submit.prevent="createTaunt" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/taunts.crud.create_taunt')</p>
                    <button type="button" class="delete" wire:click="$set('isCreating', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="taunt">@lang('admin/taunts.crud.taunt')</label>
                        <div class="control">
                            <input class="input @error('taunt.taunt') is-danger @enderror" type="text" id="taunt"
                                wire:model.defer="taunt.taunt" required>
                        </div>
                        @error('taunt.taunt') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="text_en">@lang('admin/taunts.crud.text_en')</label>
                        <div class="control">
                            <input class="input @error('taunt.text_en') is-danger @enderror" type="text" id="text_en"
                                wire:model.defer="taunt.text_en" required>
                        </div>
                        @error('taunt.text_en') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <livewire:components.sound-chooser name="sound" includeInactive="true" />

                    <div class="field">
                        <label class="label" for="active">@lang('admin/taunts.crud.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="taunt.active">
                            @lang('admin/taunts.crud.active_taunt')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/taunts.crud.create_taunt')</button>
                    <button type="button" class="button" wire:click="$set('isCreating', false)" wire:loading.attr="disabled">@lang('admin/taunts.crud.cancel')</button>
                </div>
            </form>
        </div>
    @endif
</div>
