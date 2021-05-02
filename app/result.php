<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class result extends Model
{
    protected $guarded = [];

    public function iska()
    {
        return $this->belongsTo('App\user', 'Sid');
    }
    public function images()
    {
        return $this->hasMany('App\image');
    }
    public function paper()
    {
        return $this->belongsTo('App\paper', 'paper_id');
    }
}
