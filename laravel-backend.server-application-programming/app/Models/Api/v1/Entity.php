<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use SoftDeletes;

    //relations
    public function neural_network()
    {
        return $this->belongsTo(NeuralNetwork::class);
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
