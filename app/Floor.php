<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $guarded = [
    ];

    public function rooms(){
        return $this->hasMany('App\Site_room','floor_id');
    }
}
