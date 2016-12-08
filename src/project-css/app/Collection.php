<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
  /**
  * Define the one to many relationship with App\Collection
  */
  public function tables(){
    return $this->hasMany('App\Table');
  }
}
