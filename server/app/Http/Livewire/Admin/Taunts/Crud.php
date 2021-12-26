<?php

namespace App\Http\Livewire\Admin\Taunts;

use App\Http\Livewire\PaginationComponent;
use App\Models\Taunt;

class Crud extends PaginationComponent
{
    public $taunt;
    public $isCreating;

    public $rules = [
        'taunt.taunt' => 'required|min:1|max:32',
        'taunt.text_en' => 'required|min:2|max:191',
        'taunt.sound_id' => 'nullable|integer|exists:sounds,id',
        'taunt.active' => 'nullable|boolean'
    ];

    public function __construct() {
        parent::__construct();
        $this->listeners[] = 'inputValue';
    }

    public function mount()
    {
        if ($this->sort_by != 'text_desc' && $this->sort_by != 'created_at_desc' && $this->sort_by != 'created_at') {
            $this->sort_by = null;
        }

        $this->taunt = new Taunt();
        $this->isCreating = false;
    }

    public function inputValue($name, $value)
    {
        if ($name == 'sound') {
            $this->taunt->sound_id = $value;
        }
    }

    public function createTaunt()
    {
        $this->validate();
        $this->taunt->save();
        $this->mount();
    }

    public function render()
    {
        $taunts = Taunt::search(Taunt::select(), $this->query);

        if ($this->sort_by == null) {
            $taunts = $taunts->orderByRaw('LOWER(text_en)');
        }
        if ($this->sort_by == 'text_desc') {
            $taunts = $taunts->orderByRaw('LOWER(text_en) DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $taunts = $taunts->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $taunts = $taunts->orderBy('created_at');
        }

        return view('livewire.admin.taunts.crud', [
            'taunts' => $taunts->paginate(4 * config('pagination.web'))->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/taunts.crud.title')]);
    }
}
