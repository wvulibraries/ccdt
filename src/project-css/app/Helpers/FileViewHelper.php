<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Helpers;

use Response;
use Storage;
use App\Models\Collection;
use App\Models\Table;
use App\Libraries\CustomStringHelper;
use App\Libraries\TikaConvert;

class FileViewHelper {
    /**
     * File View Helper
     *
     * These are various functions that help locate files
     * that are saved to the database
     *
     */

     /**
      * checks if files exists in storage under the folder
      * for the table
      *
      * @param       string  $tblNme    Input string
      * @param       string  $str    Input string
      * @return      boolean
      */
     public function fileExists($tblNme, $str) {
         return Storage::exists($tblNme.'/'.$str);
     }

     /**
      * Determines if the file currently exists in the storage
      * folder for the current collection that table is set to
      * and it uses the last folder and filename in the $str 
      * that is passed.
      *
      * @param       string  $tblNme Input string
      * @param       string  $str    Input string
      * @return      boolean
      */
     public function fileExistsInFolder($colNme, $str) {
         return Storage::exists($this->buildFileLink($colNme, $str));
     }

     /**
      * Takes a string with a windows style path
      * and returns only the last folder before the
      * filename
      * @param       string  $str    Input string
      * @return      string
      */
     public function getFolderName($str) {
         // explode string
         $tokens = explode('\\', $str);
         // get filename from end of string
         $filename = end($tokens);
         // return prev($tokens) this contains the folder
         return prev($tokens);
     }

     /**
      * Takes a string with a windows style path to a file
      * and returns only the filename
      * @param       string  $str    Input string
      * @return      string
      */
     public function getFilename($str) {
         // explode string
         $tokens = explode('\\', $str);
         // get filename from end of string
         $filename = end($tokens);
         // return filename
         return $filename;
     }

     /**
      * Given Collection name and a path we return a new valid
      * path to our file.
      * @param       string  $colNme    Input string
      * @param       string  $originalFilePath Input string
      * @return      string
      */
     public function buildFileLink($colNme, $originalFilePath) {
       if ($this->getFolderName($originalFilePath) != '') {
         // return new path with folder tblNme/folder/filename
         return ($colNme.'/'.$this->getFolderName($originalFilePath).'/'.$this->getFilename($originalFilePath));
       }

       // return new path with folder tblNme/folder/filename
       return ($colNme.'/'.$this->getFilename($originalFilePath));
     }

      /**
      * return returns string of record that contains the filename
      * @param       integer $curTable Input integer
      * @param       integer $recId    Input integer
      * @param       string  $filename Input string
      * @return      string or null
      */        
     public function getOriginalPath($curTable, $recId, $filename) {
       // Get the table entry in meta table "tables"
       $table = Table::findOrFail($curTable);

       // query database for record
       $rcrds = $table->getRecord($recId);
      
       // no results return null
       if (count($rcrds) > 0) {
        foreach ($rcrds[0] as &$value) {
            if (strpos($value, $filename) !== false) {
                return $value;
            }
        }
       }
       
       return null;
     }

     /**
      * return true if passed mime type is supported
      * @param       string  $fileMimeType    Input string
      * @return      boolean
      */     
     public function isSupportedMimeType($fileMimeType) {
       $mimeTypes = array('text/plain', 'message/rfc822', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/rtf');
       return in_array($fileMimeType, $mimeTypes);
     }

     /**
      * return contents of file by runningfile though tika and 
      * then redating anything that looks like a social security number
      * @param       string  $source    Input string
      * @return      string
      */        
     public function getFileContents($source) {
       $matches = "/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/";
       // convert to text using tika
       $fileContents = preg_replace($matches, "", (new tikaConvert)->convert($source));
       return Response::make((new customStringHelper)->ssnRedact($fileContents));
     }

      /**
      * return returns string with correct path to file
      * @param       integer $tableId   Input integer
      * @param       string  $filename  Input string
      * @return      string
      */       
     public function locateFile($tableId, $filename) {
       // Get the table entry in meta table "tables"
       $table = Table::findOrFail($tableId);

       // find the collection
       $collection = Collection::findorFail($table->collection_id);       

       // Check for file in root of the table
       if (Storage::exists($collection->clctnName.'/'.$filename)) {
         return($collection->clctnName.'/'.$filename);
       }

       // get all subfolder
       $ffs = Storage::disk('local')->directories($collection->clctnName);
       foreach ($ffs as &$value) {
        if (Storage::exists($value.'/'.$filename)) {
          return($value .'/'.$filename);
        }
       }

       return false;
     }
     
      /**
      * return returns string with correct path to file
      * @param       integer $curTable Input integer
      * @param       integer $recId    Input integer
      * @param       string  $filename Input string
      * @return      string
      */   
     public function getFilePath($curTable, $recId, $filename) {
       // Get the table entry in meta table "tables"
       $table = Table::findOrFail($curTable);

       $originalFilePath = $this->getOriginalPath($curTable, $recId, $filename);

       if ($originalFilePath == null) {
         return $this->locateFile($curTable, $filename);
       }

       // find the collection
       $collection = Collection::findorFail($table->collection_id);        

       return $this->buildFileLink($collection->clctnName, $originalFilePath);
     }
}
