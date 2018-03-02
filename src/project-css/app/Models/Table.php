<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    /**
    * Define the many to one relationship with App\Collection
    */
    public function collection() {
      // Allow querying the collections
      return $this->belongsTo('App\Models\Collection');
    }
}
