<?php

namespace App\Http\Livewire\Admin\Objects;

use App\Models\GameObject;
use Livewire\Component;

class Item extends Component
{
    public $object;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'object.type' => 'required|integer|digits_between:' . GameObject::TYPE_GROUP . ',' . GameObject::TYPE_PYRAMID,
        'object.name' => 'required|min:2|max:48',
        'object.width' => 'required|numeric|min:0.001',
        'object.height' => 'required|numeric|min:0.001',
        'object.depth' => 'required|numeric|min:0',
        'object.texture_id' => 'nullable|integer|exists:textures,id',
        'object.texture_repeat_x' => 'required|integer|min:1',
        'object.texture_repeat_y' => 'required|integer|min:1',
        'object.item_id' => 'nullable|integer|exists:items,id',
        'object.item_chance' => 'required|integer|min:1',
        'object.item_amount' => 'required|integer|min:1',
        'object.active' => 'nullable|boolean'
    ];

    public $listeners = ['inputValue'];

    public function inputValue($name, $value)
    {
        if ($name == 'item_texture') {
            $this->object->texture_id = $value;
        }
        if ($name == 'item_item') {
            $this->object->item_id = $value;
        }
    }

    public function editObject()
    {
        $this->validate();
        $this->object->save();
        $this->isEditing = false;
        $this->emitUp('refresh');
    }

    public function deleteObject()
    {
        $this->object->delete();
        $this->isDeleting = false;
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.objects.item');
    }
}
