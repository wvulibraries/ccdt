<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
  /**
  * Define the many to one relationship with App\Collection
  */
  public function collection(){
    return $this->belongsTo('App\Collection');
  }
}
