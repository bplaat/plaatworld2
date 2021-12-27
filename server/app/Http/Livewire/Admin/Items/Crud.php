<?php

namespace App\Http\Livewire\Admin\Items;

use App\Http\Livewire\PaginationComponent;
use App\Models\Item;

class Crud extends PaginationComponent
{
    public $item;
    public $isCreating;

    public $rules = [
        'item.name' => 'required|min:1|max:32',
        'item.stackability' => 'required|integer|min:1',
        'item.texture_id' => 'nullable|integer|exists:textures,id',
        'item.active' => 'nullable|boolean'
    ];

    public function __construct() {
        parent::__construct();
        $this->listeners[] = 'inputValue';
    }

    public function mount()
    {
        if ($this->sort_by != 'name_desc' && $this->sort_by != 'created_at_desc' && $this->sort_by != 'created_at') {
            $this->sort_by = null;
        }

        $this->item = new Item();
        $this->isCreating = false;
    }

    public function inputValue($name, $value)
    {
        if ($name == 'texture') {
            $this->item->texture_id = $value;
        }
    }

    public function createItem()
    {
        $this->validate();
        $this->item->save();
        $this->mount();
    }

    public function render()
    {
        $items = Item::search(Item::select(), $this->query);

        if ($this->sort_by == null) {
            $items = $items->orderByRaw('LOWER(name)');
        }
        if ($this->sort_by == 'name_desc') {
            $items = $items->orderByRaw('LOWER(name) DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $items = $items->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $items = $items->orderBy('created_at');
        }

        return view('livewire.admin.items.crud', [
            'items' => $items->paginate(4 * config('pagination.web'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/items.crud.title')]);
    }
}
