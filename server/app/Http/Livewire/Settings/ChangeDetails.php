<?php

namespace App\Http\Livewire\Settings;

use App\Models\Setting;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ChangeDetails extends Component
{
    public $user;
    public $isChanged = false;

    public function rules()
    {
        return [
            'user.username' => 'required|min:2|max:32',
            'user.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore(Auth::user()->email, 'email')
            ],
            'user.theme' => 'required|integer|digits_between:' . User::THEME_LIGHT . ',' . User::THEME_DARK
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function changeDetails()
    {
        $this->validate();
        $this->user->save();
        $this->isChanged = true;
    }

    public function render()
    {
        return view('livewire.settings.change-details');
    }
}
