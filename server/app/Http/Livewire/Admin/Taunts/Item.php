<?php

namespace App\Http\Livewire\Admin\Taunts;

use App\Models\Taunt;
use Livewire\Component;

class Item extends Component
{
    public $taunt;
    public $isEditing = false;
    public $isDeleting = false;

    public $rules = [
        'taunt.taunt' => 'required|min:1|max:32',
        'taunt.text_en' => 'required|min:2|max:191',
        'taunt.sound_id' => 'nullable|integer|exists:sounds,id',
        'taunt.active' => 'nullable|boolean'
    ];

    public $listeners = ['inputValue'];

    public function inputValue($name, $value)
    {
        if ($name == 'item_sound') {
            $this->taunt->sound_id = $value;
        }
    }

    public function editTaunt()
    {
        $this->validate();
        $this->taunt->save();
        $this->isEditing = false;
        $this->emitUp('refresh');
    }

    public function deleteTaunt()
    {
        $this->taunt->delete();
        $this->isDeleting = false;
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.taunts.item');
    }
}
