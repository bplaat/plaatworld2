@if (!$inline)
<div class="field">
    <label class="label" for="itemName">@lang('components.item_chooser.item')</label>
@endif
    <div @class(['dropdown', 'is-active' => $isOpen, 'control']) style="width: 100%;">
        <div class="dropdown-trigger control has-icons-left" style="width: 100%;">
            <input @class(['input', 'is-danger' => !$valid]) type="text" placeholder="@lang($relationship ? 'components.item_chooser.search_by_item' : 'components.item_chooser.search_item')"
                wire:model="itemName" id="itemName" autocomplete="off" wire:keydown.enter.prevent="selectFirstItem"
                wire:focus="$set('isOpen', true)" wire:blur.debounce.100ms="$set('isOpen', false)">
            <span class="icon is-small is-left">
                <div class="image is-small" style="background-image: url(/storage/textures/{{ $item != null && $item->texture != null && $item->texture->image != null ? $item->texture->image : 'default.png' }});"></div>
            </span>
        </div>
        <div class="dropdown-menu" style="width: 100%;">
            <div class="dropdown-content">
                @forelse ($filteredItems as $item)
                    <a wire:click.prevent="selectItem({{ $item->id }})" class="dropdown-item" wire:key="{{ $item->id }}">
                        <div class="image is-small is-inline" style="background-image: url(/storage/textures/{{ $item->texture != null && $item->texture->image != null ? $item->texture->image : 'default.png' }});"></div>
                        {!! $itemName != '' ? str_replace(' ', '&nbsp;', preg_replace('#(' . preg_quote($itemName) . ')#i', '<b>$1</b>', $item->name)) : $item->name !!}
                    </a>
                @empty
                    <div class="dropdown-item"><i>@lang('components.item_chooser.empty')</i></div>
                @endforelse
            </div>
        </div>
    </div>
@if (!$inline)
    @if (!$valid) <p class="help is-danger">@lang('components.item_chooser.empty_error')</p> @endif
</div>
@endif
