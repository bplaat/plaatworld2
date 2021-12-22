<?php

namespace App\Http\Livewire\Admin\Worlds;

use App\Http\Livewire\PaginationComponent;
use App\Models\World;

class Crud extends PaginationComponent
{
    public $world;
    public $isCreating;

    public $rules = [
        'world.name' => 'required|min:2|max:48',
        'world.width' => 'required|integer|min:1',
        'world.height' => 'required|integer|min:1'
    ];

    public function mount()
    {
        if ($this->sort_by != 'name_desc' && $this->sort_by != 'created_at_desc' && $this->sort_by != 'created_at') {
            $this->sort_by = null;
        }

        $this->world = new World();
        $this->isCreating = false;
    }

    public function createWorld()
    {
        $this->validate();
        $this->world->save();
        $this->mount();
    }

    public function render()
    {
        $worlds = World::search(World::select(), $this->query);

        if ($this->sort_by == null) {
            $worlds = $worlds->orderByRaw('LOWER(name)');
        }
        if ($this->sort_by == 'name_desc') {
            $worlds = $worlds->orderByRaw('LOWER(name) DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $worlds = $worlds->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $worlds = $worlds->orderBy('created_at');
        }

        return view('livewire.admin.worlds.crud', [
            'worlds' => $worlds->paginate(4 * 3)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/worlds.crud.title')]);
    }
}
