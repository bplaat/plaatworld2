<?php

namespace App\Http\Livewire\Components;

use App\Models\Texture;

class TextureChooser extends InputComponent
{
    // Props
    public $textureId;
    public $inline = false;
    public $relationship = false;
    public $includeInactive = false;

    // State
    public $textures;
    public $filteredTextures;
    public $textureName;
    public $texture;
    public $isOpen = false;

    // Lifecycle
    public function mount()
    {
        $textures = Texture::select();
        if (!$this->includeInactive) {
            $textures = $textures->where('active', true);
        }
        $this->textures = $textures->orderByRaw('LOWER(name)')->get();
        $this->filterTextures();

        if ($this->textureId != null) {
            $this->selectTexture($this->textureId);
        }
    }

    public function filterTextures()
    {
        $this->filteredTextures = $this->textures
            ->filter(fn ($texture) => strlen($this->textureName) == 0 || stripos($texture->name, $this->textureName) !== false)
            ->slice(0, 10);
    }

    public function emitValue()
    {
        $this->emitUp('inputValue', $this->name, $this->texture != null ? $this->texture->id : null);
    }

    public function render()
    {
        return view('livewire.components.texture-chooser');
    }

    // Events
    public function inputValidate($name)
    {
        if ($this->name == $name) {
            $this->valid = $this->texture != null;
        }
    }

    public function inputClear($name)
    {
        if ($this->name == $name) {
            $this->textureName = '';
            $this->texture = null;
            $this->emitValue();
            $this->filterTextures();
            $this->isOpen = false;
        }
    }

    // Listeners
    public function updatedTextureName()
    {
        $this->isOpen = true;
        if ($this->texture != null && $this->textureName != $this->texture->name) {
            $this->texture = null;
            $this->emitValue();
        }
        $this->filterTextures();
    }

    // Actions
    public function selectFirstTexture()
    {
        if ($this->filteredTextures->count() > 0) {
            $this->selectTexture($this->filteredTextures->first()->id);
        }
    }

    public function selectTexture($textureId)
    {
        $this->texture = $this->textures->firstWhere('id', $textureId);
        $this->textureName = $this->texture->name;
        $this->emitValue();
        $this->filterTextures();
        $this->isOpen = false;
    }
}
