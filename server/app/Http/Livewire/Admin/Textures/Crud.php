<?php

namespace App\Http\Livewire\Admin\Textures;

use App\Http\Livewire\PaginationComponent;
use App\Models\Texture;
use Livewire\WithFileUploads;

class Crud extends PaginationComponent
{
    use WithFileUploads;

    public $texture;
    public $image;
    public $isCreating;

    public $rules = [
        'texture.name' => 'required|min:2|max:48',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:1024'
    ];

    public function mount()
    {
        if ($this->sort_by != 'name_desc' && $this->sort_by != 'created_at_desc' && $this->sort_by != 'created_at') {
            $this->sort_by = null;
        }

        $this->texture = new Texture();
        $this->isCreating = false;
    }

    public function createTexture()
    {
        $this->validate();

        if ($this->image != null) {
            $imageName = Texture::generateImageName($this->image->extension());
            $this->image->storeAs('public/textures', $imageName);
            $this->texture->image = $imageName;
        }

        $this->texture->save();
        $this->mount();
    }

    public function render()
    {
        $textures = Texture::search(Texture::select(), $this->query);

        if ($this->sort_by == null) {
            $textures = $textures->orderByRaw('LOWER(name)');
        }
        if ($this->sort_by == 'name_desc') {
            $textures = $textures->orderByRaw('LOWER(name) DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $textures = $textures->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $textures = $textures->orderBy('created_at');
        }

        return view('livewire.admin.textures.crud', [
            'textures' => $textures->paginate(4 * 4)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/textures.crud.title')]);
    }
}
