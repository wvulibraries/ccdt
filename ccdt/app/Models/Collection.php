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

  /**
   * hasTable gets all associated tables
   * to the collection and checks the count
   * returning true if count is greater than
   * 0 false otherwise.
   * 
   * @return      boolean
   */     
  public function hasTables() {
    $tbls = $this->tables()->get();
    return ($tbls->count() > 0);
  } 
  
  /**
   * hasFiles gets all associated files
   * returns true if files are found 
   * false otherwise.
   * 
   * @return      boolean
   */    
  public function hasFiles() {
    $files = \Storage::allFiles($this->clctnName);
    return (count($files) > 0);
  }

   /**
   * update the current collections cmdId.
   * 
   * @param       integer ($cmsId used to determine
   * the correct cms header to use when importing cms
   * tables)
   * @return      boolean
   */  
  public function setCMSId($cmsId) {
    // Set CMS Id in collection
    \DB::table('collections')
            ->where('id', $this->id)
            ->update(['cmsId' => $cmsId]);
  }

}
