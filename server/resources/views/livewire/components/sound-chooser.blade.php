@if (!$inline)
<div class="field">
    <label class="label" for="soundText">@lang('components.sound_chooser.sound')</label>
@endif
    <div @class(['dropdown', 'is-active' => $isOpen, 'control']) style="width: 100%;">
        <div class="dropdown-trigger control" style="width: 100%;">
            <input @class(['input', 'is-danger' => !$valid]) type="text" placeholder="@lang($relationship ? 'components.sound_chooser.search_by_sound' : 'components.sound_chooser.search_sound')"
                wire:model="soundText" id="soundText" autocomplete="off" wire:keydown.enter.prevent="selectFirstSound"
                wire:focus="$set('isOpen', true)" wire:blur.debounce.100ms="$set('isOpen', false)">
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div class="dropdown-content">
                @forelse ($filteredSounds as $sound)
                    <a wire:click.prevent="selectSound({{ $sound->id }})" class="dropdown-item" wire:key="{{ $sound->id }}">
                        {!! $soundText != '' ? str_replace(' ', '&nbsp;', preg_replace('#(' . preg_quote($soundText) . ')#i', '<b>$1</b>', $sound->text)) : $sound->text !!}
                    </a>
                @empty
                    <div class="dropdown-item"><i>@lang('components.sound_chooser.empty')</i></div>
                @endforelse
            </div>
        </div>
    </div>
@if (!$inline)
    @if (!$valid) <p class="help is-danger">@lang('components.sound_chooser.empty_error')</p> @endif
</div>
@endif
