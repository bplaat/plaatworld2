<?php

namespace App\View\Components;

use Illuminate\View\Component;

class UserInventory extends Component
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        unset($this->user->items);
        return view('components.user-inventory');
    }
}
