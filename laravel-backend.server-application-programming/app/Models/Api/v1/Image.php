<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use SoftDeletes;

    //relations
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
