<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameObject extends Model
{
    use SoftDeletes;

    protected $table = 'objects';

    // A object can be a sprite or an cube
    public const TYPE_SPRITE = 0;
    public const TYPE_CUBE = 1;

    // Fields
    protected $attributes = [
        'type' => GameObject::TYPE_SPRITE,
        'name' => 'Untitled object',
        'width' => 1,
        'height' => 1,
        'depth' => 0,
        'active' => true
    ];

    protected $casts = [
        'width' => 'double',
        'height' => 'double',
        'depth' => 'double',
        'active' => 'boolean'
    ];

    // A object has one texture
    public function texture()
    {
        return $this->belongsTo(Texture::class);
    }

    // A object belongs to many worlds
    public function worlds()
    {
        return $this->belongsToMany(GameObject::class, 'world_object', 'object_id', 'world_id')
            ->withPivot('name', 'position_x', 'position_y', 'position_z', 'rotation_x', 'rotation_y', 'rotation_z')->withTimestamps();
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
