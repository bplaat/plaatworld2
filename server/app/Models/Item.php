<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use SoftDeletes;

    // Fields
    protected $hidden = [
        'deleted_at'
    ];

    protected $attributes = [
        'name' => 'Untitled item',
        'stackability' => 64,
        'active' => true
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // A item has one texture
    public function texture()
    {
        return $this->hasOne(Texture::class, 'id', 'texture_id');
    }

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('name', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
