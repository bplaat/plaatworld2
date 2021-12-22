<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create admin account
        $user = new User();
        $user->username = 'bplaat';
        $user->email = 'bastiaan.v.d.plaat@gmail.com';
        $user->password = Hash::make('admin123');
        $user->role = User::ROLE_ADMIN;
        $user->save();
    }
}
