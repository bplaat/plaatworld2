<?php

namespace App\Http\Livewire\Admin\Objects;

use App\Http\Livewire\PaginationComponent;
use App\Models\GameObject;

class Crud extends PaginationComponent
{
    public $object;
    public $isCreating;

    public $rules = [
        'object.type' => 'required|integer|in:' . GameObject::TYPE_SPRITE,
        'object.name' => 'required|min:2|max:48',
        'object.width' => 'required|numeric|min:0.001',
        'object.height' => 'required|numeric|min:0.001',
        'object.texture_id' => 'nullable|integer|exists:textures,id'
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

        $this->object = new GameObject();
        $this->isCreating = false;
    }

    public function inputValue($name, $value)
    {
        if ($name == 'texture') {
            $this->object->texture_id = $value;
        }
    }

    public function createObject()
    {
        $this->validate();
        $this->object->save();
        $this->mount();
    }

    public function render()
    {
        $objects = GameObject::search(GameObject::select(), $this->query);

        if ($this->sort_by == null) {
            $objects = $objects->orderByRaw('LOWER(name)');
        }
        if ($this->sort_by == 'name_desc') {
            $objects = $objects->orderByRaw('LOWER(name) DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $objects = $objects->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $objects = $objects->orderBy('created_at');
        }

        return view('livewire.admin.objects.crud', [
            'objects' => $objects->paginate(4 * 4)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/objects.crud.title')]);
    }
}
