<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $user;

    public $rules = [
        'user.username' => 'required|min:2|max:32',
        'user.email' => 'required|email|max:255|unique:users,email',
        'user._password' => 'required|min:6',
        'user.password_confirmation' => 'required|same:user._password'
    ];

    public function mount()
    {
        $this->user = new User();
    }

    public function register()
    {
        // Validate input
        $this->validate();

        // Create new user
        $this->user->password = Hash::make($this->user->_password);
        unset($this->user->_password);
        unset($this->user->password_confirmation);
        $this->user->save();

        // Login to that user and remember in cookie
        Auth::login($this->user, true);
        session()->regenerate();
        return redirect()->intended(route('home'));
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.app', ['title' => __('auth.register.title')]);
    }
}
