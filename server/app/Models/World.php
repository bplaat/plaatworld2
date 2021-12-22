<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class World extends Model
{
    use SoftDeletes;

    // Fields
    protected $attributes = [
        'name' => 'Untitled world',
        'width' => 50,
        'height' => 50,
        'spawn_position_x' => 0,
        'spawn_position_y' => 0,
        'spawn_position_z' => 0,
        'spawn_rotation_x' => 0,
        'spawn_rotation_y' => 0,
        'spawn_rotation_z' => 0,
        'active' => true
    ];

    protected $casts = [
        'width' => 'double',
        'height' => 'double',
        'spawn_position_x' => 'double',
        'spawn_position_y' => 'double',
        'spawn_position_z' => 'double',
        'spawn_rotation_x' => 'double',
        'spawn_rotation_y' => 'double',
        'spawn_rotation_z' => 'double',
        'active' => 'boolean'
    ];

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
