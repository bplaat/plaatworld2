<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Taunt extends Model
{
    use SoftDeletes;

    // Fields
    protected $hidden = [
        'deleted_at'
    ];

    protected $attributes = [
        'taunt' => '?',
        'text_en' => 'Unkown taunt',
        'active' => true
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    // Search by a query
    public static function search($query, $searchQuery)
    {
        return $query->where('text_en', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('created_at', 'LIKE', '%' . $searchQuery . '%');
    }
}
