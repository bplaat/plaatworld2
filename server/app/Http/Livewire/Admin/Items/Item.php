<?php

namespace App\Http\Livewire\Admin\Items;

use Livewire\Component;

class Item extends Component
{
    public $item;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'item.name' => 'required|min:1|max:32',
        'item.stackability' => 'required|integer|min:1',
        'item.texture_id' => 'nullable|integer|exists:textures,id',
        'item.active' => 'nullable|boolean'
    ];

    public $listeners = ['inputValue'];

    public function inputValue($name, $value)
    {
        if ($name == 'item_texture') {
            $this->item->texture_id = $value;
        }
    }

    public function editItem()
    {
        $this->validate();
        $this->item->save();
        $this->isEditing = false;
        $this->emitUp('refresh');
    }

    public function deleteItem()
    {
        $this->item->delete();
        $this->isDeleting = false;
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.items.item');
    }
}
