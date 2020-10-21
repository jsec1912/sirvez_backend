<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = [
    ];
    public function rooms(){
        return $this->hasMany('App\room','project_id');
    }
}
