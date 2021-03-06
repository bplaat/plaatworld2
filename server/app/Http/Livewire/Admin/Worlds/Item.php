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
        'world.width' => 'required|numeric|min:1',
        'world.height' => 'required|numeric|min:1',
        'world.gravity' => 'required|numeric|min:0.001',
        'world.spawn_position_x' => 'required|numeric|min:0',
        'world.spawn_position_y' => 'required|numeric|min:0',
        'world.spawn_position_z' => 'required|numeric|min:0',
        'world.spawn_rotation_x' => 'required|numeric|min:0',
        'world.spawn_rotation_y' => 'required|numeric|min:0',
        'world.spawn_rotation_z' => 'required|numeric|min:0',
        'world.sky_texture_id' => 'nullable|integer|exists:textures,id',
        'world.active' => 'nullable|boolean'
    ];

    public $listeners = ['inputValue'];

    public function inputValue($name, $value)
    {
        if ($name == 'item_texture') {
            $this->world->sky_texture_id = $value;
        }
    }

    public function editWorld()
    {
        $this->validate();
        $this->world->save();
        $this->isEditing = false;
        $this->emit('refresh');
    }

    public function deleteWorld()
    {
        $this->world->delete();
        $this->isDeleting = false;
        $this->emit('refresh');
    }

    public function render()
    {
        return view('livewire.admin.worlds.item');
    }
}
