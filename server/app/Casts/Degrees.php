<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Degrees implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        return floatval($value) * 180 / pi();
    }

    public function set($model, $key, $value, $attributes)
    {
        return ($value * pi()) / 180;
    }
}
