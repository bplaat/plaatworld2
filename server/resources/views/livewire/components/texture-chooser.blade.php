@if (!$inline)
<div class="field">
    <label class="label" for="textureName">@lang('components.texture_chooser.texture')</label>
@endif
    <div @class(['dropdown', 'is-active' => $isOpen, 'control']) style="width: 100%;">
        <div class="dropdown-trigger control has-icons-left" style="width: 100%;">
            <input @class(['input', 'is-danger' => !$valid]) type="text" placeholder="@lang($relationship ? 'components.texture_chooser.search_by_texture' : 'components.texture_chooser.search_texture')"
                wire:model="textureName" id="textureName" autocomplete="off" wire:keydown.enter.prevent="selectFirstTexture"
                wire:focus="$set('isOpen', true)" wire:blur.debounce.100ms="$set('isOpen', false)">
            <span class="icon is-small is-left">
                <div class="image is-small is-rounded" style="background-image: url(/storage/textures/{{ $texture != null && $texture->image != null ? $texture->image : 'default.png' }});"></div>
            </span>
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div class="dropdown-content">
                @forelse ($filteredTextures as $texture)
                    <a wire:click.prevent="selectTexture({{ $texture->id }})" class="dropdown-item" wire:key="{{ $texture->id }}">
                        <div class="image is-small is-rounded is-inline" style="background-image: url(/storage/textures/{{ $texture->image ?? 'default.png' }});"></div>
                        {!! $textureName != '' ? str_replace(' ', '&nbsp;', preg_replace('#(' . preg_quote($textureName) . ')#i', '<b>$1</b>', $texture->name)) : $texture->name !!}
                    </a>
                @empty
                    <div class="dropdown-item"><i>@lang('components.texture_chooser.empty')</i></div>
                @endforelse
            </div>
        </div>
    </div>
@if (!$inline)
    @if (!$valid) <p class="help is-danger">@lang('components.texture_chooser.empty_error')</p> @endif
</div>
@endif
