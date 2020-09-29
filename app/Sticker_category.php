<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sticker_category extends Model
{
    protected $guarded = [
    ];
    public function stickers(){
        return $this->hasMany('App\Sticker','category_id');
    }
}
