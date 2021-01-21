<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site_room extends Model
{
    protected $guarded = [
    ];
    public function point(){
        return $this->hasMany('App\LocationPoint','room_id');
    }
}
