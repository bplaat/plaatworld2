<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorldUser extends Model
{
    protected $table = 'world_user';

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
