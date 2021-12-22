<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Item extends Component
{
    use WithFileUploads;

    public $user;
    public $newPassword;
    public $newPasswordConfirmation;
    public $avatar;
    public $isShowing = false;
    public $isEditing = false;
    public $isDeleting = false;

    public function rules()
    {
        return [
            'user.username' => 'required|min:2|max:32',
            'user.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user->email, 'email')
            ],
            'newPassword' => 'nullable|min:6',
            'newPasswordConfirmation' => 'nullable|same:user._password',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'user.role' => 'required|integer|digits_between:' . User::ROLE_NORMAL . ',' . User::ROLE_ADMIN,
            'user.language' => 'required|integer|in:' . User::LANGUAGE_ENGLISH,
            'user.theme' => 'required|integer|digits_between:' . User::THEME_LIGHT . ',' . User::THEME_DARK,
            'user.active' => 'nullable|boolean'
        ];
    }

    public function editUser()
    {
        $this->validate();

        if ($this->newPassword != null) {
            $this->user->password = Hash::make($this->newPassword);
        }

        if ($this->avatar != null) {
            $avatarName = User::generateAvatarName($this->avatar->extension());
            $this->avatar->storeAs('public/avatars', $avatarName);

            if ($this->user->avatar != null) {
                Storage::delete('public/avatars/' . $this->user->avatar);
            }
            $this->user->avatar = $avatarName;
            $this->avatar = null;
        }

        $this->isEditing = false;
        $this->user->save();
        $this->emitUp('refresh');

        $this->newPassword = null;
        $this->newPasswordConfirmation = null;
    }

    public function hijackUser()
    {
        Auth::login($this->user, true);
        return redirect()->route('home');
    }

    public function deleteAvatar()
    {
        if ($this->user->avatar != null) {
            Storage::delete('public/avatars/' . $this->user->avatar);
        }
        $this->user->avatar = null;
        $this->user->save();
        $this->emitUp('refresh');
    }

    public function deleteUser()
    {
        $this->user->delete();
        $this->isDeleting = false;
        $this->emitUp('refresh');
    }

    public function render()
    {
        return view('livewire.admin.users.item');
    }
}
