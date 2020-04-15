<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Models\Collection;

class CollectionHelper {
    /**
     * Collection Helper
     *
     * These are various functions that help with creating and modifying 
     * collections.
     *
     */

     public function create($data) {
        // Create the collection name
        $thisClctn = new Collection;
        $thisClctn->clctnName = $data['name'];
        $thisClctn->isCms = $data['isCms'];
        $thisClctn->save();

        // create folder in storage that will contain any additional files associated to the collection
        if (Storage::exists($thisClctn->clctnName) == FALSE) {
          Storage::makeDirectory($thisClctn->clctnName, 0775);
        }

        return $thisClctn;
     }

     public function update($data) {
        // find the collection
        $thisClctn = Collection::findorFail($data['id']);

      if ($thisClctn->clctnName != $data['name']) {
        // Rename Storage Folder
        if ((Storage::exists($data['name']) == FALSE) && (Storage::exists($thisClctn->clctnName))) {
          Storage::move($thisClctn->clctnName, $data['name']);
        }  

        // Set new Collection Name
        $thisClctn->clctnName = $data['name'];
      }

      if ($thisClctn->isCms != $data['isCms']) {
        // Set CMS State
        $thisClctn->isCms = $data['isCms'];
      }

      // Save Updated items
      $thisClctn->save();            
     }

     public function disable($name) {
        // verify collection exists
        if ($this->isCollection($name)) {
          // find the collection
          $thisClctn = Collection::where('clctnName', $name)->first();

          // reset collection to disabled
          $this->updateCollectionFlag($thisClctn->id, false);

          return true;
        }

        return false;
    }

    public function enable($name) {
      // verify collection exists
      if ($this->isCollection($name)) {
        // find the collection
        $thisClctn = Collection::where('clctnName', $name)->first();

        // reset collection to enabled
        $this->updateCollectionFlag($thisClctn->id, true);

        return true;
      }

      return false;
     }

     public function isCollection($name) {
        if (Collection::where('clctnName', '=', $name)->count() == 1) {
          return true;
        }
        return false;
     }

     public function setCMS($name, $option = false) {
        // find the collection
        $thisClctn = Collection::where('clctnName', $name)->first();

        // Set isCms
        $thisClctn->isCms = $option;

        // Save Updated items
        $thisClctn->save();  
     }


     // check and see if collection has Tables
     // associated to it.
     public function hasTables($name) {
        // find the collection
        $thisClctn = Collection::where('clctnName', $name)->first();

        // Get all the tables of this collection
        $thisClctnTbls = $thisClctn->tables()->get();

        if ($thisClctnTbls->count() > 0) {
          return true;
        }
        return false;
     }

    /**
    * Returns true if files exist in collection. False otherwise.
    *
    * @param  string  $name    
    * @return boolean
    */         
     public function hasFiles($name) {
        $files = Storage::allFiles($name);
        if (empty($files)) {
          return false;
        }
        return true;
     }

     /**
      * Sets the the state of the collection to the value in $flag
      * then calls updateTableAccess to update all tables in the 
      * collection
      *
      * @param  integer $id   
      * @param  boolean  $flag   
      */
     public function updateCollectionFlag($id, $flag) {
        // Create the collection name
        $thisClctn = Collection::findorFail($id);

        // Updated all Tables in collection
        $this->updateTableAccess($thisClctn, $flag);

        // update status of the collection
        $thisClctn->isEnabled = $flag;

        // Save the Collection
        $thisClctn->save();
      }

      /**
      * Sets hasAccess on all tables in collection
      */  
      private function updateTableAccess($collection, $access) {
        // Get all the tables of this collection
        $thisClctnTbls = $collection->tables()->get();

        // Update all the tables of this collection
        foreach ($thisClctnTbls as $tbl) {
          $tbl->hasAccess = $access;
          $tbl->save();
        }
      }    

      /**
       * Delete a collection
       */
      public function deleteCollection($name) {
        // find the collection
        $thisClctn = Collection::where('clctnName', $name)->first();

        // delete the collection
        $thisClctn->delete();

        // delete storage folder
        Storage::deleteDirectory($name);
      }
}
