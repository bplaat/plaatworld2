<?php

namespace App\Http\Livewire\Components;

use App\Models\Item;

class ItemChooser extends InputComponent
{
    // Props
    public $itemId;
    public $inline = false;
    public $relationship = false;
    public $includeInactive = false;

    // State
    public $items;
    public $filteredItems;
    public $itemName;
    public $item;
    public $isOpen = false;

    // Lifecycle
    public function mount()
    {
        $items = Item::select();
        if (!$this->includeInactive) {
            $items = $items->where('active', true);
        }
        $this->items = $items->orderByRaw('LOWER(name)')->get();
        $this->filterItems();

        if ($this->itemId != null) {
            $this->selectItem($this->itemId);
        }
    }

    public function filterItems()
    {
        $this->filteredItems = $this->items
            ->filter(fn ($item) => strlen($this->itemName) == 0 || stripos($item->name, $this->itemName) !== false)
            ->slice(0, 10);
    }

    public function emitValue()
    {
        $this->emitUp('inputValue', $this->name, $this->item != null ? $this->item->id : null);
    }

    public function render()
    {
        return view('livewire.components.item-chooser');
    }

    // Events
    public function inputValidate($name)
    {
        if ($this->name == $name) {
            $this->valid = $this->item != null;
        }
    }

    public function inputClear($name)
    {
        if ($this->name == $name) {
            $this->itemName = '';
            $this->item = null;
            $this->emitValue();
            $this->filterItems();
            $this->isOpen = false;
        }
    }

    // Listeners
    public function updatedItemName()
    {
        $this->isOpen = true;
        if ($this->item != null && $this->itemName != $this->item->name) {
            $this->item = null;
            $this->emitValue();
        }
        $this->filterItems();
    }

    // Actions
    public function selectFirstItem()
    {
        if ($this->filteredItems->count() > 0) {
            $this->selectItem($this->filteredItems->first()->id);
        }
    }

    public function selectItem($itemId)
    {
        $this->item = $this->items->firstWhere('id', $itemId);
        $this->itemName = $this->item->name;
        $this->emitValue();
        $this->filterItems();
        $this->isOpen = false;
    }
}
