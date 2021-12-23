<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameObject extends Model
{
    use SoftDeletes;

    protected $table = 'objects';

    // A object can be a sprite, a cube, a cylinder, a sphere or a pyramid
    public const TYPE_SPRITE = 0;
    public const TYPE_CUBE = 1;
    public const TYPE_CYLINDER = 2;
    public const TYPE_SPHERE = 3;
    public const TYPE_PYRAMID = 4;

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
        return $this->hasOne(Texture::class, 'id', 'texture_id');
    }

    // A object can have many child objects
    public function objects()
    {
        return $this->belongsToMany(GameObject::class, 'object_object', 'parent_object_id', 'object_id')
            ->withPivot('id', 'name', 'position_x', 'position_y', 'position_z', 'rotation_x', 'rotation_y', 'rotation_z')->withTimestamps();
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
