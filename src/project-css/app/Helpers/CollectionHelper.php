<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use App\Models\Collection;

/**
 * Collection Helper
 *
 * These are various functions that help with creating and modifying 
 * collections.
 *
 */
class CollectionHelper {

     /**
      * creates takes the passed data array the data array
      * should contain the name and isCMS. The cmsId can be
      * included but it generally doesn't exist outside of testing. 
      *
      * @param array $data includes items name and isCMS to be used to create the collection
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      * @return object collection
      */    
     public function create($data) {
        // Create the collection name
        $thisClctn = new Collection;
        $thisClctn->clctnName = $data['name'];
        $thisClctn->isCms = $data['isCms'];

        // set cmsId if it exists
        if (array_key_exists('cmsId', $data)) {
          $thisClctn->cmsId = $data['cmsId'];
        }

        $thisClctn->save();

        // create folder in storage that will contain any additional files associated to the collection
        if (Storage::exists($thisClctn->clctnName) == FALSE) {
          Storage::makeDirectory($thisClctn->clctnName, 0664);
        }

        return $thisClctn;
     }

     /**
      * update takes the passed data array the data array
      * should contain the collection id. Other fields that 
      * may or may not exist ie name and isCMS are optional.
      *
      * @param array $data items to be updated in the collection
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      * @return void
      */  
     public function update($data) : void {
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

     /**
      * disables collection
      *
      * @param string $name of collection to be disabled
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      * @return boolean 
      */ 
     public function disable($name) : bool {
       if ($this->isCollection($name) == false) {   
         return false;
       }

       // find the collection
       $thisClctn = Collection::where('clctnName', $name)->first();

       // update collection to disabled
       $this->updateCollectionFlag($thisClctn->id, false);

       return true;
    }

    /**
     * enables a disabled collection
     *
     * @param string $name of collection to be disabled
     *
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
     * @return boolean 
     */     
    public function enable($name) : bool {
      if ($this->isCollection($name) == false) {   
        return false;
      }

      // find the collection
      $thisClctn = Collection::where('clctnName', $name)->first();

      // reset collection to enabled
      $this->updateCollectionFlag($thisClctn->id, true);

      return true;
     }

    /**
     * checks if collection exists
     *
     * @param string $name of collection
     *
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
     * @return boolean 
     */      
     public function isCollection($name) : bool {
        if (Collection::where('clctnName', '=', $name)->count() == 1) {
          return true;
        }
        return false;
     }

    /**
     * sets the CMS option on a collection.
     *
     * @param string $name of collection
     * @param boolean $option - true if cms false otherwise
     *
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
     * @return void 
     */       
    public function setCMS($name, $option = false) : void {
       // find the collection
       $thisClctn = Collection::where('clctnName', $name)->first();

       // Set isCms
       $thisClctn->isCms = $option;

       // Save Updated items
       $thisClctn->save();  
    }

    /**
     * check and see if collection has Tables
     * associated to it
     *
     * @param string $name of collection
     *
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
     * @return boolean 
     */       
    public function hasTables($name) : bool {
      if ($this->isCollection($name) == false) {   
        return false;
      }
        
      // find the collection
      $thisClctn = Collection::where('clctnName', $name)->first();

      return $thisClctn->hasTables();
    }

    /**
     * checks storage folder for files
     *
     * @param  string  $name  
     *
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>  
     * @return boolean
     */         
    public function hasFiles($name) : bool {
      if ($this->isCollection($name) == false) {   
        return false;
      }       

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
     * @param  boolean $flag  
     *
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>  
     * @return void      
     */
    public function updateCollectionFlag($id, $flag) : void {
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
     *
     * @param  object $collection   
     * @param  boolean $access       
     *  
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
     *   
     * @return void      
     */  
    public function updateTableAccess($collection, $access) : void {
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
     *
     * @param  string  $name    
     *  
     * @author Tracy A. McCormick <tam0013@mail.wvu.edu>  
     * @return boolean      
     */  
    public function deleteCollection($name) : bool {
      // find the collection
      $thisClctn = Collection::where('clctnName', $name)->first();

      if ( $thisClctn != null ) {
        // delete the collection
        $thisClctn->delete();

        // delete storage folder
        Storage::deleteDirectory($name);

        return true;
      }

      return false;
    }
}
