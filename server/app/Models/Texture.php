<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Texture extends Model
{
    use SoftDeletes;

    // Fields
    protected $attributes = [
        'name' => 'Untitled texture',
        'active' => true
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Generate a random image name
    public static function generateImageName($extension)
    {
        if ($extension == 'jpeg') {
            $extension = 'jpg';
        }
        $image = Str::random(32) . '.' . $extension;
        if (static::where('image', $image)->count() > 0) {
            return static::generateImageName($extension);
        }
        return $image;
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
