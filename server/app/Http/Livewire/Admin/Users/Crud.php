<?php

namespace App\Http\Livewire\Admin\Users;

use App\Http\Livewire\PaginationComponent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class Crud extends PaginationComponent
{
    use WithFileUploads;

    public $role;
    public $user;
    public $avatar;
    public $isCreating;

    public $rules = [
        'user.firstname' => 'required|min:2|max:48',
        'user.insertion' => 'nullable|max:16',
        'user.lastname' => 'required|min:2|max:48',
        'user.gender' => 'nullable|integer|digits_between:' . User::GENDER_MALE . ',' . User::GENDER_OTHER,
        'user.birthday' => 'nullable|date',
        'user.email' => 'required|email|max:255|unique:users,email',
        'user.phone' => 'nullable|max:255',
        'user._password' => 'required|min:6',
        'user.password_confirmation' => 'required|same:user._password',
        'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
        'user.role' => 'required|integer|digits_between:' . User::ROLE_NORMAL . ',' . User::ROLE_ADMIN,
        'user.theme' => 'required|integer|digits_between:' . User::THEME_LIGHT . ',' . User::THEME_DARK
    ];

    public function __construct()
    {
        parent::__construct();
        $this->queryString['role'] = ['except' => ''];
    }

    public function mount()
    {
        if ($this->role != 'normal' && $this->role != 'admin') {
            $this->role = null;
        }

        $this->user = new User();
        $this->user->role = User::ROLE_NORMAL;
        $this->user->theme = User::THEME_DARK;
        $this->avatar = null;
        $this->isCreating = false;
    }

    public function search()
    {
        if ($this->role != 'normal' && $this->role != 'admin') {
            $this->role = null;
        }
        $this->resetPage();
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
        if ($this->role != null) {
            if ($this->role == 'normal') $role = User::ROLE_NORMAL;
            if ($this->role == 'admin') $role = User::ROLE_ADMIN;
            $users = $users->where('role', $role);
        }
        return view('livewire.admin.users.crud', [
            'users' => $users->orderByRaw('active DESC, LOWER(IF(lastname != \'\', IF(insertion != NULL, CONCAT(lastname, \', \', insertion, \' \', firstname), CONCAT(lastname, \' \', firstname)), firstname))')
                ->paginate(4 * 4)->withQueryString()
        ])->layout('layouts.app', ['title' => __('admin/users.crud.title')]);
    }
}
