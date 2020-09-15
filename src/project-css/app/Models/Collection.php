<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Collection model is used to allow the grouping
 * and organization of tables and associated files.
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class Collection extends Model
{
  /**
  * Define the one to many relationship with App\Collection
  */
  public function tables() {
    // Establish the relationship
    return $this->hasMany('App\Models\Table');
  }

  public function hasTables() {
    $tbls = $this->tables()->get();
    return ($tbls->count() > 0);
  } 
  
  public function hasFiles() {
    $files = \Storage::allFiles($this->clctnName);
    if (empty($files)) {
      return false;
    }
    return true;
  }

  public function setCMSId($collectionID, $cmsId) {
    // Set CMS Id in collection
    \DB::table('collections')
            ->where('id', $collectionID)
            ->update(['cmsId' => $cmsId]);
  }

}
