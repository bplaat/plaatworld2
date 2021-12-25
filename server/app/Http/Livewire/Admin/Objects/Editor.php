<?php

namespace App\Http\Livewire\Admin\Objects;

use App\Models\GameObject;
use App\Models\ObjectObject;
use App\Models\ObjectEditorUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Editor extends Component
{
    public $editorUser;
    public $object;

    public function mount(GameObject $object) {
        // When object is no a group show 404 page
        if ($object->type != GameObject::TYPE_GROUP) {
            abort(404);
        }

        // Get or create object editor user data model
        $this->editorUser = ObjectEditorUser::where('object_id', $object->id)->where('user_id', Auth::id())->first();
        if ($this->editorUser == null) {
            $this->editorUser = new ObjectEditorUser();
            $this->editorUser->object_id = $object->id;
            $this->editorUser->user_id = Auth::id();
            $this->editorUser->camera_position_x = $object->width;
            $this->editorUser->camera_position_y = $object->height;
            $this->editorUser->camera_position_z = $object->depth;
            $this->editorUser->camera_rotation_x = 0;
            $this->editorUser->camera_rotation_y = 0;
            $this->editorUser->camera_rotation_z = 0;
            $this->editorUser->save();
        }

        // Get child objects
        $this->object = $object;
        $this->object->objects;
    }

    public function saveObject($data)
    {
        // Update existing objects and delete deleted onces
        $objects = collect($data['object']['objects']);
        $objectPivots = ObjectObject::where('parent_object_id', $this->object->id)->get();
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
            $objectPivot = new ObjectObject();
            $objectPivot->parent_object_id = $this->object->id;
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
        if (count($newObjectIds) > 0) {
            $this->emit('updateObjectIds', $newObjectIds);
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
        $this->editorUser->save();
    }

    public function render()
    {
        return view('livewire.admin.objects.editor', [
            'object' => $this->object
        ])->layout('layouts.app', [
            'title' => __('admin/objects.editor.title', ['object.name' => $this->object->name]),
            'immersive' => true, 'vuejs' => true, 'threejs' => true, 'statsjs' => true, 'orbitcontrolsjs' => true
        ]);
    }
}
