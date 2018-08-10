<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
  /**
  * Define the one to many relationship with App\Collection
  */
  public function tables() {
    // Establish the relationship
    return $this->hasMany('App\Models\Table');
  }

  public function setCMSId($collectionID, $cmsId) {
    // Set CMS Id in collection
    \DB::table('collections')
            ->where('id', $collectionID)
            ->update(['cmsId' => $cmsId]);
  }

}
