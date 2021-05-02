<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    public function result()
    {
        return $this->belongsTo('App\result');
    }
}
