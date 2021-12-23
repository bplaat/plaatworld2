<?php

namespace App\Http\Livewire\Admin\Objects;

use App\Models\GameObject;
use Livewire\Component;

class Editor extends Component
{
    public $object;

    public function mount(GameObject $object) {
        $this->object = $object;
        $this->object->objects;
    }

    public function render()
    {
        return view('livewire.admin.objects.editor', [
            'object' => $this->object
        ])->layout('layouts.app', [
            'title' => __('admin/objects.editor.title', ['object.name' => $this->object->name]),
            'immersive' => true, 'threejs' => true, 'statsjs' => true, 'orbitcontrolsjs' => true
        ]);
    }
}
