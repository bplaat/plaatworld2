<?php

namespace App\Http\Livewire\Components;

use App\Models\Sound;

class SoundChooser extends InputComponent
{
    // Props
    public $soundId;
    public $inline = false;
    public $relationship = false;
    public $includeInactive = false;

    // State
    public $sounds;
    public $filteredSounds;
    public $soundText;
    public $sound;
    public $isOpen = false;

    // Lifecycle
    public function mount()
    {
        $sounds = Sound::select();
        if (!$this->includeInactive) {
            $sounds = $sounds->where('active', true);
        }
        $this->sounds = $sounds->orderByRaw('LOWER(text)')->get();
        $this->filterSounds();

        if ($this->soundId != null) {
            $this->selectSound($this->soundId);
        }
    }

    public function filterSounds()
    {
        $this->filteredSounds = $this->sounds
            ->filter(fn ($sound) => strlen($this->soundText) == 0 || stripos($sound->text, $this->soundText) !== false)
            ->slice(0, 10);
    }

    public function emitValue()
    {
        $this->emitUp('inputValue', $this->name, $this->sound != null ? $this->sound->id : null);
    }

    public function render()
    {
        return view('livewire.components.sound-chooser');
    }

    // Events
    public function inputValidate($name)
    {
        if ($this->name == $name) {
            $this->valid = $this->sound != null;
        }
    }

    public function inputClear($name)
    {
        if ($this->name == $name) {
            $this->soundText = '';
            $this->sound = null;
            $this->emitValue();
            $this->filterSounds();
            $this->isOpen = false;
        }
    }

    // Listeners
    public function updatedSoundText()
    {
        $this->isOpen = true;
        if ($this->sound != null && $this->soundText != $this->sound->text) {
            $this->sound = null;
            $this->emitValue();
        }
        $this->filterSounds();
    }

    // Actions
    public function selectFirstSound()
    {
        if ($this->filteredSounds->count() > 0) {
            $this->selectSound($this->filteredSounds->first()->id);
        }
    }

    public function selectSound($soundId)
    {
        $this->sound = $this->sounds->firstWhere('id', $soundId);
        $this->soundText = $this->sound->text;
        $this->emitValue();
        $this->filterSounds();
        $this->isOpen = false;
    }
}
