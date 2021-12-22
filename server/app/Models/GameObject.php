<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GameObject extends Model
{
    use SoftDeletes;

    protected $table = 'objects';

    // A object can be a sprite
    public const TYPE_SPRITE = 0;

    // Fields
    protected $attributes = [
        'type' => GameObject::TYPE_SPRITE,
        'name' => 'Untitled object',
        'width' => 1,
        'height' => 1,
        'active' => true
    ];

    protected $casts = [
        'width' => 'double',
        'height' => 'double',
        'active' => 'boolean'
    ];

    // A object has one texture
    public function texture()
    {
        return $this->belongsTo(Texture::class);
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
