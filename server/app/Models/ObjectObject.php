<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectObject extends Model
{
    protected $table = 'object_object';

    // Fields
    protected $casts = [
        'position_x' => 'double',
        'position_y' => 'double',
        'position_z' => 'double',
        'rotation_x' => 'double',
        'rotation_y' => 'double',
        'rotation_z' => 'double'
    ];
}
