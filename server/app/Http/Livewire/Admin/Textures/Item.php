<?php

namespace App\Http\Livewire\Admin\Textures;

use App\Models\Texture;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Item extends Component
{
    use WithFileUploads;

    public $texture;
    public $image;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'texture.name' => 'required|min:2|max:48',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        'texture.active' => 'nullable|boolean'
    ];

    public function editTexture()
    {
        $this->validate();

        if ($this->image != null) {
            $imageName = Texture::generateImageName($this->image->extension());
            $this->image->storeAs('public/textures', $imageName);

            if ($this->texture->image != null) {
                Storage::delete('public/textures/' . $this->texture->image);
            }
            $this->texture->image = $imageName;
            $this->image = null;
        }

        $this->texture->save();
        $this->isEditing = false;
        $this->emitUp('refresh');
    }

    public function deleteImage()
    {
        if ($this->texture->image != null) {
            Storage::delete('public/textures/' . $this->texture->image);
        }
        $this->texture->image = null;
        $this->texture->save();
        $this->emitUp('refresh');
    }

    public function deleteTexture()
    {
        $this->texture->delete();
        $this->isDeleting = false;
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.textures.item');
    }
}
