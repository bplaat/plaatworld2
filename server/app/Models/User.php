<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    // A user can be normal or an admin
    public const ROLE_NORMAL = 0;
    public const ROLE_ADMIN = 1;

    // A user can select the english language
    public const LANGUAGE_ENGLISH = 0;

    // A user can select a light and a dark theme
    public const THEME_LIGHT = 0;
    public const THEME_DARK = 1;

    // Fields
    protected $hidden = [
        'email_verified_at',
        'password',
        'remember_token',
        'deleted_at'
    ];

    protected $attributes = [
        'role' => User::ROLE_NORMAL,
        'language' => User::LANGUAGE_ENGLISH,
        'theme' => User::THEME_DARK,
        'active' => true
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean'
    ];

    // Generate a random avatar name
    public static function generateAvatarName($extension)
    {
        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }
        $avatar = Str::random(32) . '.' . $extension;
        if (static::where('avatar', $avatar)->count() > 0) {
            return static::generateAvatarName($extension);
        }
        return $avatar;
    }

    // Get the current used api token from session storage or create a new one
    public function authToken() {
        if (session('auth_token') == null) {
            session(['auth_token' => $this->createToken('API auth token for website')->plainTextToken]);
        }
        return session('auth_token');
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('username', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('email', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
