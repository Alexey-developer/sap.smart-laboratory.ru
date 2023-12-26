<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class NeuralNetwork extends Model
{
    use SoftDeletes;

    //relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function entities()
    {
        return $this->hasMany(Entity::class);
    }
    public function models()
    {
        return $this->hasMany(NNModel::class);
    }
}
