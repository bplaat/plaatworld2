<div class="column is-one-third">
    <div class="card">
        <div class="card-content content">
            <h4 class="mb-0">
                {{ $sound->text }}
                @if (!$sound->active)
                    <span class="tag is-warning is-pulled-right">{{ Str::upper(__('admin/sounds.item.inactive')) }}</span>
                @endif
            </h4>
        </div>

        <div class="card-footer">
            @if ($sound->audio != null)
                <a class="card-footer-item" onclick="new Audio('/storage/sounds/{{ $sound->audio }}').play()">@lang('admin/sounds.item.play')</a>
            @endif
            <a class="card-footer-item" wire:click.prevent="$set('isEditing', true)">@lang('admin/sounds.item.edit')</a>
            <a class="card-footer-item has-text-danger" wire:click.prevent="$set('isDeleting', true)">@lang('admin/sounds.item.delete')</a>
        </div>
    </div>

    @if ($isEditing)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isEditing', false)"></div>

            <form wire:submit.prevent="editSound" class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/sounds.item.edit_sound')</p>
                    <button type="button" class="delete" wire:click="$set('isEditing', false)"></button>
                </div>

                <div class="modal-card-body">
                    <div class="field">
                        <label class="label" for="text">@lang('admin/sounds.item.text')</label>
                        <div class="control">
                            <input class="input @error('sound.text') is-danger @enderror" type="text" id="text"
                                wire:model.defer="sound.text" tabindex="1" required>
                        </div>
                        @error('sound.text') <p class="help is-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="label" for="audio">@lang('admin/sounds.item.audio')</label>
                        @if ($sound->audio != null)
                            <div class="box">
                                <audio src="/storage/sounds/{{ $sound->audio }}" controls style="width: 100%;"></audio>
                            </div>
                        @endif
                    </div>

                    <div class="field">
                        <div class="control">
                            <input class="input @error('audio') is-danger @enderror" type="file" accept=".wav,.mp3,.m4a" id="audio" wire:model="audio">
                        </div>
                        @error('audio')
                            <p class="help is-danger">{{ $message }}</p>
                        @else
                            <p class="help">@lang('admin/sounds.item.audio_help')</p>
                        @enderror
                    </div>

                    @if ($sound->audio != null)
                        <div class="field">
                            <div class="control">
                                <button type="button" class="button is-danger" wire:click="deleteAudio" wire:loading.attr="disabled">@lang('admin/sounds.item.delete_audio')</button>
                            </div>
                        </div>
                    @endif

                    <div class="field">
                        <label class="label" for="active">@lang('admin/sounds.item.active')</label>
                        <label class="checkbox" for="active">
                            <input type="checkbox" id="active" wire:model.defer="sound.active">
                            @lang('admin/sounds.item.active_sound')
                        </label>
                    </div>
                </div>

                <div class="modal-card-foot">
                    <button type="submit" class="button is-link">@lang('admin/sounds.item.edit_sound')</button>
                    <button type="button" class="button" wire:click="$set('isEditing', false)" wire:loading.attr="disabled">@lang('admin/sounds.item.cancel')</button>
                </div>
            </form>
        </div>
    @endif

    @if ($isDeleting)
        <div class="modal is-active">
            <div class="modal-background" wire:click="$set('isDeleting', false)"></div>

            <div class="modal-card">
                <div class="modal-card-head">
                    <p class="modal-card-title">@lang('admin/sounds.item.delete_sound')</p>
                    <button type="button" class="delete" wire:click="$set('isDeleting', false)"></button>
                </div>

                <div class="modal-card-body">
                    <p>@lang('admin/sounds.item.delete_description')</p>
                </div>

                <div class="modal-card-foot">
                    <button class="button is-danger" wire:click="deleteSound()" wire:loading.attr="disabled">@lang('admin/sounds.item.delete_sound')</button>
                    <button class="button" wire:click="$set('isDeleting', false)" wire:loading.attr="disabled">@lang('admin/sounds.item.cancel')</button>
                </div>
            </div>
        </div>
    @endif
</div>
