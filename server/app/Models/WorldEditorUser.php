<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorldEditorUser extends Model
{
    protected $table = 'world_editor_user';

    // Fields
    protected $casts = [
        'camera_position_x' => 'double',
        'camera_position_y' => 'double',
        'camera_position_z' => 'double',
        'camera_rotation_x' => 'double',
        'camera_rotation_y' => 'double',
        'camera_rotation_z' => 'double',
        'skybox' => 'boolean'
    ];
}
