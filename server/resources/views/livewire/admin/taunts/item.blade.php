<div class="column is-one-third">
    <div class="card">
        <div class="card-content content">
            <h4 class="mb-0">
                {{ $taunt->taunt }}: {{ $taunt->text_en }}
                @if (!$taunt->active)
                    <span class="tag is-warning is-pulled-right">{{ Str::upper(__('admin/taunts.item.inactive')) }}</span>
                @endif
            </h4>
        </div>

        <div class="card-footer">
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/taunts.item.edit')</a>
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/taunts.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editTaunt" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/taunts.item.edit_taunt')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="taunt">@lang('admin/taunts.item.taunt')</label>
                        <div class="control">
                            <input class="input @error('taunt.taunt') is-danger @enderror" type="text" id="taunt"
                                wire:model.defer="taunt.taunt" required>
                        </div>
                        @error('taunt.taunt') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="text_en">@lang('admin/taunts.item.text_en')</label>
                        <div class="control">
                            <input class="input @error('taunt.text_en') is-danger @enderror" type="text" id="text_en"
                                wire:model.defer="taunt.text_en" required>
                        </div>
                        @error('taunt.text_en') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <livewire:components.sound-chooser name="item_sound" :soundId="$taunt->sound_id" includeInactive="true" />

                    <div class="field">
                        <label class="label" for="active">@lang('admin/taunts.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="taunt.active">
                            @lang('admin/taunts.item.active_taunt')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/taunts.item.edit_taunt')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/taunts.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/taunts.item.delete_taunt')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/taunts.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteTaunt()" wire:loading.attr="disabled">@lang('admin/taunts.item.delete_taunt')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/taunts.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
