<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Sound extends Model
{
    use SoftDeletes;

    // Fields
    protected $hidden = [
        'deleted_at'
    ];

    protected $attributes = [
        'text' => 'Unkown sound',
        'active' => true
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Generate a random audio name
    public static function generateAudioName($extension)
    {
        $audio = Str::random(32) . '.' . $extension;
        if (static::where('audio', $audio)->count() > 0) {
            return static::generateAudioName($extension);
        }
        return $audio;
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('text', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
