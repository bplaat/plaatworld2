<?php

namespace App\Http\Livewire\Admin\Sounds;

use App\Http\Livewire\PaginationComponent;
use App\Models\Sound;
use Livewire\WithFileUploads;

class Crud extends PaginationComponent
{
    use WithFileUploads;

    public $sound;
    public $audio;
    public $isCreating;

    public $rules = [
        'sound.text' => 'required|min:2|max:191',
        'audio' => 'nullable|file|mimes:wav,mp3,m4a|max:1024',
        'sound.active' => 'nullable|boolean'
    ];

    public function mount()
    {
        if ($this->sort_by != 'text_desc' && $this->sort_by != 'created_at_desc' && $this->sort_by != 'created_at') {
            $this->sort_by = null;
        }

        $this->sound = new Sound();
        $this->isCreating = false;
    }

    public function createSound()
    {
        $this->validate();

        if ($this->audio != null) {
            $audioName = Sound::generateAudioName($this->audio->extension());
            $this->audio->storeAs('public/sounds', $audioName);
            $this->sound->audio = $audioName;
        }

        $this->sound->save();
        $this->mount();
    }

    public function render()
    {
        $sounds = Sound::search(Sound::select(), $this->query);

        if ($this->sort_by == null) {
            $sounds = $sounds->orderByRaw('LOWER(text)');
        }
        if ($this->sort_by == 'text_desc') {
            $sounds = $sounds->orderByRaw('LOWER(text) DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $sounds = $sounds->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $sounds = $sounds->orderBy('created_at');
        }

        return view('livewire.admin.sounds.crud', [
            'sounds' => $sounds->paginate(3 * config('pagination.web'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/sounds.crud.title')]);
    }
}
