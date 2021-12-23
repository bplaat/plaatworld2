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
        'object.type' => 'required|integer|digits_between:' . GameObject::TYPE_SPRITE . ',' . GameObject::TYPE_PYRAMID,
        'object.name' => 'required|min:2|max:48',
        'object.width' => 'required|numeric|min:0.001',
        'object.height' => 'required|numeric|min:0.001',
        'object.depth' => 'required|numeric|min:0',
        'object.texture_id' => 'nullable|integer|exists:textures,id',
        'object.active' => 'nullable|boolean'
    ];

    public $listeners = ['inputValue'];

    public function inputValue($name, $value)
    {
        if ($name == 'item_texture') {
            $this->object->texture_id = $value;
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
        // unset($this->object->texture);
        return view('livewire.admin.objects.item');
    }
}
