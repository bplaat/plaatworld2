<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Register extends Component
{
    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.app', ['title' => __('auth.register.title')]);
    }
}
