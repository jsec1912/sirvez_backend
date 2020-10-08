<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [
    ];

    public function rooms(){
        return $this->hasMany('App\Site_room','department_id');
    }
}
