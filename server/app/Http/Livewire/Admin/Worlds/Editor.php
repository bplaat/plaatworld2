<?php

namespace App\Http\Livewire\Admin\Worlds;

use App\Models\World;
use App\Models\WorldObject;
use Livewire\Component;

class Editor extends Component
{
    public $world;

    public function mount(World $world) {
        $this->world = $world;
    }

    public function saveWorld($objects) {
        $this->world->objects()->detach();
        foreach ($objects as $object) {
            $worldObject = new WorldObject();
            $worldObject->world_id = $this->world->id;
            $worldObject->object_id = $object['object_id'];
            $worldObject->name = $object['name'];
            $worldObject->position_x = $object['position']['x'];
            $worldObject->position_y = $object['position']['y'];
            $worldObject->position_z = $object['position']['z'];
            $worldObject->rotation_x = $object['rotation']['x'];
            $worldObject->rotation_y = $object['rotation']['y'];
            $worldObject->rotation_z = $object['rotation']['z'];
            $worldObject->save();
        }
    }

    public function render()
    {
        return view('livewire.admin.worlds.editor', [
            'worldObjects' => $this->world->objects->map(fn ($worldObject) => [
                'object_id' => $worldObject->id, 'name' => $worldObject->pivot->name, 'position' => [
                    'x' => $worldObject->pivot->position_x,
                    'y' => $worldObject->pivot->position_y,
                    'z' => $worldObject->pivot->position_z
                ], 'rotation' => [
                    'x' => $worldObject->pivot->rotation_x,
                    'y' => $worldObject->pivot->rotation_y,
                    'z' => $worldObject->pivot->rotation_z
                ]]
            )
        ])->layout('layouts.app', ['title' => __('admin/worlds.editor.title', ['world.name' => $this->world->name]), 'immersive' => true, 'threejs' => true]);
    }
}
