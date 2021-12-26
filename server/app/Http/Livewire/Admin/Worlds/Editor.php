<?php

namespace App\Http\Livewire\Admin\Worlds;

use App\Models\GameObject;
use App\Models\World;
use App\Models\WorldObject;
use App\Models\WorldEditorUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Editor extends Component
{
    public $editorUser;
    public $objects;
    public $world;

    public function mount(World $world) {
        // Get or create object editor user data model
        $this->editorUser = WorldEditorUser::where('world_id', $world->id)->where('user_id', Auth::id())->first();
        if ($this->editorUser == null) {
            $this->editorUser = new WorldEditorUser();
            $this->editorUser->world_id = $world->id;
            $this->editorUser->user_id = Auth::id();
            $this->editorUser->camera_position_x = $world->width / 2;
            $this->editorUser->camera_position_y = max($world->width, $world->height) / 2;
            $this->editorUser->camera_position_z = $world->height / 2;
            $this->editorUser->camera_rotation_x = 0;
            $this->editorUser->camera_rotation_y = 0;
            $this->editorUser->camera_rotation_z = 0;
            $this->editorUser->skybox = false;
            $this->editorUser->save();
        }

        // Get all object and it objects and textures
        $this->objects = GameObject::where('active', true)->orderByRaw('LOWER(name)')->get();
        for ($i = 0; $i < $this->objects->count(); $i++) {
            if ($this->objects[$i]->type == GameObject::TYPE_GROUP) {
                for ($j = 0; $j < $this->objects[$i]->objects->count(); $j++) {
                    $this->objects[$i]->objects[$j]->texture;
                }
            } else {
                $this->objects[$i]->texture;
            }
        }

        // Get world object and its objects
        $this->world = $world;
        for ($i = 0; $i < $this->world->objects->count(); $i++) {
            if ($this->world->objects[$i]->type == GameObject::TYPE_GROUP) {
                $this->world->objects[$i]->objects;
            }
        }
    }

    public function saveWorld($data)
    {
        // Update existing objects and delete deleted onces
        $objects = collect($data['world']['objects']);
        $objectPivots = WorldObject::where('world_id', $this->world->id)->get();
        foreach ($objectPivots as $objectPivot) {
            $object = $objects->first(fn ($otherObject) => $otherObject['pivot']['id'] == $objectPivot->id);
            if ($object != null) {
                $objectPivot->name = $object['pivot']['name'];
                $objectPivot->position_x = $object['pivot']['position_x'];
                $objectPivot->position_y = $object['pivot']['position_y'];
                $objectPivot->position_z = $object['pivot']['position_z'];
                $objectPivot->rotation_x = $object['pivot']['rotation_x'];
                $objectPivot->rotation_y = $object['pivot']['rotation_y'];
                $objectPivot->rotation_z = $object['pivot']['rotation_z'];
                $objectPivot->save();
            } else {
                if ($this->editorUser->selected_object_id == $objectPivot->id) {
                    $this->editorUser->selected_object_id = null;
                    $this->editorUser->save();
                }
                $objectPivot->delete();
            }
            $objects = $objects->filter(fn ($otherObject) => $otherObject['pivot']['id'] != $objectPivot->id);
        }

        // Add new objects
        $newObjectIds = [];
        foreach ($objects as $object) {
            $objectPivot = new WorldObject();
            $objectPivot->world_id = $this->world->id;
            $objectPivot->object_id = $object['id'];
            $objectPivot->name = $object['pivot']['name'];
            $objectPivot->position_x = $object['pivot']['position_x'];
            $objectPivot->position_y = $object['pivot']['position_y'];
            $objectPivot->position_z = $object['pivot']['position_z'];
            $objectPivot->rotation_x = $object['pivot']['rotation_x'];
            $objectPivot->rotation_y = $object['pivot']['rotation_y'];
            $objectPivot->rotation_z = $object['pivot']['rotation_z'];
            $objectPivot->save();
            $newObjectIds[$object['pivot']['id']] = $objectPivot->id;
        }

        // Update editor user
        $this->editorUser->camera_position_x = $data['editorUser']['camera_position_x'];
        $this->editorUser->camera_position_y = $data['editorUser']['camera_position_y'];
        $this->editorUser->camera_position_z = $data['editorUser']['camera_position_z'];
        $this->editorUser->camera_rotation_x = $data['editorUser']['camera_rotation_x'];
        $this->editorUser->camera_rotation_y = $data['editorUser']['camera_rotation_y'];
        $this->editorUser->camera_rotation_z = $data['editorUser']['camera_rotation_z'];
        $selected_object_id = $data['editorUser']['selected_object_id'];
        $this->editorUser->selected_object_id = isset($newObjectIds[$selected_object_id]) ? $newObjectIds[$selected_object_id] : $selected_object_id;
        $this->editorUser->skybox = $data['editorUser']['skybox'];
        $this->editorUser->save();

        // Emit new object ids message to JavaScript editor
        if (count($newObjectIds) > 0) {
            $this->emit('updateObjectIds', $newObjectIds);
        }
    }

    public function render()
    {
        return view('livewire.admin.worlds.editor')->layout('layouts.app', [
            'title' => __('admin/worlds.editor.title', ['world.name' => $this->world->name]),
            'immersive' => true, 'vuejs' => true, 'threejs' => true, 'statsjs' => true, 'objectviewerjs' => true, 'worldeditorjs' => true
        ]);
    }
}
