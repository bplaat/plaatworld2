<?php

namespace App\Http\Livewire\Admin\Sounds;

use App\Models\Sound;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Item extends Component
{
    use WithFileUploads;

    public $sound;
    public $audio;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'sound.text' => 'required|min:2|max:48',
        'audio' => 'nullable|file|mimes:wav,mp3,m4a|max:1024',
        'sound.active' => 'nullable|boolean'
    ];

    public function editSound()
    {
        $this->validate();

        if ($this->audio != null) {
            $audioName = Sound::generateAudioName($this->audio->extension());
            $this->audio->storeAs('public/sounds', $audioName);

            if ($this->sound->audio != null) {
                Storage::delete('public/sounds/' . $this->sound->audio);
            }
            $this->sound->audio = $audioName;
            $this->audio = null;
        }

        $this->sound->save();
        $this->isEditing = false;
        $this->emitUp('refresh');
    }

    public function deleteAudio()
    {
        if ($this->sound->audio != null) {
            Storage::delete('public/sounds/' . $this->sound->audio);
        }
        $this->sound->audio = null;
        $this->sound->save();
        $this->emitUp('refresh');
    }

    public function deleteSound()
    {
        $this->sound->delete();
        $this->isDeleting = false;
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.sounds.item');
    }
}
