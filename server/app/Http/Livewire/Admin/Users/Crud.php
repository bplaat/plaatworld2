<?php

namespace App\Http\Livewire\Admin\Users;

use App\Http\Livewire\PaginationComponent;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class Crud extends PaginationComponent
{
    use WithFileUploads;

    public $user;
    public $avatar;
    public $isCreating;

    public $rules = [
        'user.username' => 'required|min:2|max:32',
        'user.email' => 'required|email|max:255|unique:users,email',
        'user._password' => 'required|min:6',
        'user.password_confirmation' => 'required|same:user._password',
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        'user.role' => 'required|integer|digits_between:' . User::ROLE_NORMAL . ',' . User::ROLE_ADMIN,
        'user.language' => 'required|integer|in:' . User::LANGUAGE_ENGLISH,
        'user.theme' => 'required|integer|digits_between:' . User::THEME_LIGHT . ',' . User::THEME_DARK
    ];

    public function mount()
    {
        if ($this->sort_by != 'username_desc' && $this->sort_by != 'created_at_desc' && $this->sort_by != 'created_at') {
            $this->sort_by = null;
        }

        $this->user = new User();
        $this->avatar = null;
        $this->isCreating = false;
    }

    public function createUser()
    {
        $this->validate();

        $this->user->password = Hash::make($this->user->_password);
        unset($this->user->_password);
        unset($this->user->password_confirmation);

        if ($this->avatar != null) {
            $avatarName = User::generateAvatarName($this->avatar->extension());
            $this->avatar->storeAs('public/avatars', $avatarName);
            $this->user->avatar = $avatarName;
        }

        $this->user->save();
        $this->mount();
    }

    public function render()
    {
        $users = User::search(User::select(), $this->query);

        if ($this->sort_by == null) {
            $users = $users->orderByRaw('active DESC, username');
        }
        if ($this->sort_by == 'username_desc') {
            $users = $users->orderByRaw('active DESC, username DESC');
        }
        if ($this->sort_by == 'created_at_desc') {
            $users = $users->orderBy('created_at', 'DESC');
        }
        if ($this->sort_by == 'created_at') {
            $users = $users->orderBy('created_at');
        }

        return view('livewire.admin.users.crud', [
            'users' => $users->paginate(4 * 4)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/users.crud.title')]);
    }
}
