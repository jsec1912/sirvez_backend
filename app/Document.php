<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $guarded = [
    ];
    public function pages(){
        return $this->hasMany('App\DocumentPage','page_id');
    }
}
