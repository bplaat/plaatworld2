<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameObject extends Model
{
    use SoftDeletes;

    protected $table = 'objects';

    // A object can be a group, a sprite, a fixed sprite, a cube, a cylinder, a sphere or a pyramid
    public const TYPE_GROUP = 0;
    public const TYPE_SPRITE = 1;
    public const TYPE_FIXED_SPRITE = 2;
    public const TYPE_CUBE = 3;
    public const TYPE_CYLINDER = 4;
    public const TYPE_SPHERE = 5;
    public const TYPE_PYRAMID = 6;

    // Fields
    protected $hidden = [
        'deleted_at'
    ];

    protected $attributes = [
        'type' => GameObject::TYPE_SPRITE,
        'name' => 'Untitled object',
        'width' => 1,
        'height' => 1,
        'depth' => 0,
        'texture_repeat_x' => 1,
        'texture_repeat_y' => 1,
        'item_chance' => 1,
        'item_amount' => 1,
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
            ->withPivot('id', 'name', 'position_x', 'position_y', 'position_z', 'rotation_x', 'rotation_y', 'rotation_z', 'scale_x', 'scale_y', 'scale_z')->withTimestamps();
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
