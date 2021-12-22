<?php

namespace App\Http\Livewire\Admin\Worlds;

use App\Models\World;
use Livewire\Component;

class Item extends Component
{
    public $world;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'world.name' => 'required|min:2|max:48',
        'world.width' => 'required|integer|min:1',
        'world.height' => 'required|integer|min:1',
        'world.active' => 'nullable|boolean'
    ];

    public function editWorld()
    {
        $this->validate();
        $this->world->save();
        $this->isEditing = false;
        $this->emitUp('refresh');
    }

    public function deleteWorld()
    {
        $this->isDeleting = false;
        $this->world->deleted = true;
        $this->world->save();
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.worlds.item');
    }
}
