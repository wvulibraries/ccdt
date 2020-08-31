<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\CreateSearchIndex;
use App\Jobs\FileImport;
use App\Jobs\OptimizeSearchIndex;
use App\Jobs\UpdateSearchIndex;

use App\Models\Collection;
use App\Models\CMSRecords;
use App\Models\Table;
use App\Helpers\CSVHelper;
use App\Helpers\CMSHelper;
use App\Helpers\CustomStringHelper;

/**
 * Table Helper
 *
 * These are various functions that help with dynamically
 * creating and modifying tables without migrations.
 *
 */
class TableHelper {
    /** 
    * Inserts New String field into the passed table
    *
    * @param table $table - Current Table object
    * @param string $curColNme - String name of new field
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */     
    public function createStringField($table, $curColNme, $curColSze) {
      switch ($curColSze) {
          case 'medium':
              // For String medium is 150 characters
              $table->string($curColNme, 150)->default("Null");
              break;
          case 'big':
              // For String big is 500 characters
              $table->string($curColNme, 500)->default("Null");
              break;
          default:
              // For String default is 30 characters
              $table->string($curColNme, 30)->default("Null");                  
      }                 
    }

    /** 
    * Inserts New Text field into the passed table
    *
    * @param table $table - Current Table object
    * @param string $curColNme - String name of new field
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */     
    public function createTextField($table, $curColNme, $curColSze) {
      switch ($curColSze) {
          case 'medium':
              // For text medium is mediumtext type
              $table->mediumText($curColNme);
              break;
          case 'big':
              // For text big is longtext type
              $table->longText($curColNme);
              break;
          default:
              // For text default is text type
              $table->text($curColNme);                  
      }             
    }    
    
    /** 
    * Inserts New Integer field into the passed table
    *
    * @param table $table - Current Table object
    * @param string $curColNme - String name of new field
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */ 
    public function createIntegerField($table, $curColNme, $curColSze) {
      switch ($curColSze) {
          case 'medium':
              // For Integer medium is medium integer
              $table->mediumInteger($curColNme)->default(0);
              break;
          case 'big':
              // For Integer big is big integer
              $table->bigInteger($curColNme)->default(0);
              break;
          default:
              // For Integer default integer type
              $table->integer($curColNme)->default(0);                  
      }                 
    }     

    /** 
    * Function checks $curColType and calls the correct function to create
    * the field.
    *
    * @param string $table - current table object  
    * @param string $curColNme - String name of field
    * @param string $curColType - String type of field    
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */      
    public function setupTableField($table, $curColNme, $curColType, $curColSze) {
        // Call correct create function based on $curColType
        switch ($curColType) {
            case 'string':
                $this->createStringField($table, $curColNme, $curColSze);              
                break;
            case 'text':
                $this->createTextField($table, $curColNme, $curColSze);              
                break;
            case 'integer':
                $this->createIntegerField($table, $curColNme, $curColSze);
                break;                         
        }
    }   
     
    /** 
    * Basic Switch function to call correct update 
    * function to change type of field in the table.
    *
    * @param string $tblNme - String name of table   
    * @param string $curColNme - String name of field
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */  
    public function changeToIntegerField($tblNme, $curColNme, $curColSze) {
        switch ($curColSze) {
            case 'medium':
                $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` MEDIUMINT";               
                break;
            case 'big':
                $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` BIGINT";               
                break;
            default:
                $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` INT";                           
        }

        return DB::connection()->statement($statement);     
    } 

    /** 
    * Basic Switch function to call correct update 
    * function to change type of field in the table.
    *
    * @param string $tblNme - String name of table   
    * @param string $curColNme - String name of field
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */         
    public function schemaChangeToStringField($tblNme, $curColNme, $curColSze) {    
      Schema::table($tblNme, function ($table) use ($curColNme, $curColSze) {             
        switch ($curColSze) {
            case 'medium':
              // For String medium is 150 characters
              $table->string($curColNme, 150)->change();
              break;
            case 'big':
              // For String big is 500 characters
              $table->string($curColNme, 500)->change();
              break;
            default:
              // For String default is 30 characters
              $table->string($curColNme, 30)->change();                  
        }
      });
    }

    /** 
    * Basic Switch function to call correct update 
    * function to change type of field in the table.
    *
    * @param string $tblNme - String name of table   
    * @param string $curColNme - String name of field
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */      
    public function schemaChangeToTextField($tblNme, $curColNme, $curColSze) {  
      Schema::table($tblNme, function ($table) use ($curColNme, $curColSze) {
        switch ($curColSze) {
            case 'medium':
              // For text medium is mediumtext type
              $table->mediumText($curColNme)->change();
              break;
            case 'big':
              // For text big is longtext type
              $table->longText($curColNme)->change();
              break;
            default:
              // For text default is text type
              $table->text($curColNme)->change();                  
        }    
      });           
    }    

    /** 
    * Basic Switch function to call correct update 
    * function to change type of field in the table.
    *
    * @param string $tblNme - String name of table   
    * @param string $curColNme - String name of field
    * @param string $curColType - String type that field will be changed to   
    * @param string $curColSze - String Size either 'default', 'medium' or 'big'
    * @author Tracy A McCormick
    */      
    public function changeTableField($tblNme, $curColNme, $curColType, $curColSze) {        
        switch (strtolower($curColType)) {
            case 'string':
                $this->schemaChangeToStringField($tblNme, $curColNme, $curColSze);
                break;
            case 'text':
                $this->schemaChangeToTextField($tblNme, $curColNme, $curColSze);
                break;
            default:
                $this->changeToIntegerField($tblNme, $curColNme, $curColSze);
        }
    }

     /**
     * Simple function to create the table within the collections
     * @param string $tblNme
     * @param string $collctnId
     */
     public function crteTblInCollctn($tblNme, $collctnId) {
       // declare a new table instance
       $thisTabl = new Table;
       // Assign the table name
       $thisTabl->tblNme = $tblNme;
       // Set table to the collection
       $thisTabl->collection_id = $collctnId;
       // Save the table
       $thisTabl->save();
     }

     /**
     * Simple function to set the table to a collection
     * @param integer $tblId
     * @param string $collctnId
     */
     public function setTblInCollctn($tblId, $collctnId) {
       // Get the table entry in meta table "tables"
       $table = Table::findOrFail($tblId);
       // Set collection id
       $table->collection_id = $collctnId;
       // Save the table
       $table->save();
     }     

     /**
     * Function takes the tablename, filepath and the filename
     * and calls dispatch on the FileImport function to queue 
     * the file for import into the table.
     * 
     * @param string $tblNme - table name
     * @param string $fltFlePath - path to file to be imported
     * @param string $fltFle - filename of file 
     */     
     public function dispatchImportJob($tblNme, $fltFlePath, $fltFle) {
       // set messages array to empty
       $messages = [];

       Log::info('File Import has been requested for table '.$tblNme.' using flat file '.$fltFle);

       // add job to queue
       dispatch(new FileImport($tblNme, $fltFlePath, $fltFle))->onQueue('high');
       dispatch(new CreateSearchIndex($tblNme))->onQueue('high');
       dispatch(new OptimizeSearchIndex($tblNme))->onQueue('high');

       $message = [
         'content'  =>  $fltFle.' has been queued for import to '.$tblNme.' table. It will be available shortly.',
         'level'    =>  'success',
       ];
       array_push($messages, $message);
       session()->flash('messages', $messages);
     }      

    /** 
    * 
    *
    * @param string $filepath
    * @param string $fileName
    * @param string $tblNme
    * @param array $fieldNames
    * @param array $fieldTypes
    * @param integer $collctnId - the collection id  
    * @author Tracy A McCormick
    * @return redirect to table index
    */    
    public function createTable($filepath, $fileName, $tblNme, $fieldNames, $fieldTypes, $collctnId) {
      $fieldCount = count($fieldTypes);

      // create the table
      Schema::connection('mysql')->create($tblNme, function(Blueprint $table) use($fieldNames, $fieldTypes, $fieldCount, $collctnId) {        
        // // if index exist rename to id
        // if ($fieldNames[0] == 'index') {
        //   $fieldNames[0] = 'id';
        // }

        // Default primary key
        $table->increments('id');

        // Add all the dynamic columns
        for ($i = 0; $i<$fieldCount; $i++) {
          // Create Field from current column name, type and size
          $this->setupTableField($table, (new CustomStringHelper)->formatFieldName($fieldNames[$i]), $fieldTypes[$i][0], $fieldTypes[$i][1]);
        }

        // search index
        $table->longText('srchindex');

        // Time stamps
        $table->timestamps();
      });

      // modify table for fulltext search using the srchindex column
      DB::connection()->getPdo()->exec('ALTER TABLE `'.$tblNme.'` ADD FULLTEXT fulltext_index (srchindex)');

      // Set table collection id
      $this->crteTblInCollctn($tblNme, $collctnId);
    }

    /** 
    * Store file in the $data['fltFile'] and queue 
    * for import.
    *
    * @param array $data - array of values used by function
    *
    * List of fields in $data that are expected
    *
    * @param string $data['strDir'] - is the storage folder
    * @param string $data['fltFile'] - file to be imported
    * @param string $data['tableName'] - new table name to be used
    * @param boolean $data['cms'] - true if uploaded files are apart of a cms set
    * @author Tracy A McCormick
    * @return redirect to table index
    */      
    public function storeUploadAndImport($data) {
      $errors = [];

      // Get the list of files in the directory
      $fltFleList = Storage::allFiles($data['strDir']);

      $thisFltFileNme = $data['fltFile']->getClientOriginalName();

      // set error if file currently exists
      if (in_array($data['strDir'].'/'.$thisFltFileNme, $fltFleList)) {
        // save error to $errors array
        $errors = [$thisFltFileNme . ' File already exists. Please select the file or rename and re-upload.'];
      }
      else {
        // Store in the directory inside storage/app
        $data['fltFile']->storeAs($data['strDir'], $thisFltFileNme);
        $result = $this->importFile($data['strDir'], $thisFltFileNme, $data['tableName'], $data['colID'], $data['cms']);

        // if error(s) save errorlist to $errors
        if ($result['error']) {
          $errors = $result['errorList'];
        }   
      }

      // return error array so they can be displayed to the user in the view
      return $errors;
    }

    /** 
    * Queue file in the $data['fltFile'] for import.
    *
    * @param array $data - array of values used by function
    *
    * List of fields in $data that are expected
    *
    * @param string $data['strDir'] - is the storage folder
    * @param string $data['fltFile'] - file to be imported
    * @param integer $data['colID'] - the collection id    
    * @author Tracy A McCormick
    * @return error array
    */         
    public function selectFileAndImport($data) {
      // array for keeping errors that we will send to the user
      $errors = [];

      $result = $this->importFile($data['strDir'], $data['fltFile'], $data['tableName'], $data['colID'], $data['cms']);
      
      // if error(s) save errorlist to $errors
      if ($result['error']) {
        $errors = $result['errorList'];
      }    

      // return error array so they can be displayed to the user in the view
      return $errors;
    }

    /** 
    * @param string $strDir - is the storage folder
    * @param string $file - file to be imported
    * @param string $tblNme - new table name to be used
    * @param integer $colID - the collection id 
    * @param boolean $cms - true if uploaded files are apart of a cms set   
    * @author Tracy A McCormick
    * @return $errorArray
    */    
    public function setupNewTable($strDir, $file, $tblNme, $colID, $cms = false) {
      $csvHelper = (new CSVHelper);

      $fltFleAbsPth = $strDir.'/'.$file;
      // Calling schema will return an array containing the
      // tokenized first row of our file to be imported
      $schema = $csvHelper->schema($fltFleAbsPth);
      // if the array is not valid we will delete the file
      // and push a error to the $errors array
      if (!$schema) {
        Storage::delete($fltFleAbsPth);

        $errorArray = [
          'error' => true,
          'errorList' => [ 'The selected flat file must be of type: text/plain', 'The selected flat file should not be empty', 'File is deleted for security reasons' ]
        ];

        return ($errorArray);
      }

      // Generate Table Name if $tblNme is null
      if ($tblNme == null) {
        // filter record string
        $filteredType = filter_var($schema[0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

        // create table name
        $tblNme = $filteredType . time();
      }

      $fieldTypes = $csvHelper->determineTypes(!$cms, $fltFleAbsPth, 10000);
     
      $fieldNames = ($cms ? (new CMSHelper)->cmsHeader($colID, $schema[0], count($fieldTypes)) : $schema);

      $this->createTable($strDir, $file, $tblNme, $fieldNames, $fieldTypes, $colID);

      $result = [
        'error' => false,
        'tblNme' => $tblNme
      ];

      return ($result);
    }

    /** 
    * Import file into correct collection. Create a tablename if one is 
    * not provided.
    *
    * @param string $strDir - is the storage folder
    * @param string $file - file to be imported
    * @param string $tblNme - new table name to be used
    * @param integer $colID - the collection id 
    * @param boolean $cms - true if uploaded files are apart of a cms set       
    * @author Tracy A McCormick
    * @return $errorArray
    */       
    public function importFile($strDir, $file, $tblNme = null, $colID, $cms) {
      $result = $this->setupNewTable($strDir, $file, $tblNme, $colID, $cms);
      if ($result['error'] == true) { 
        $errorArray = [
          'error' => true,
          'errorList' => $result['errorList']
        ];
        return $errorArray; 
      }

      // queue job for import
      $this->dispatchImportJob($result['tblNme'], $strDir, $file);
    }

    /** 
    * Function is used to create array of 2 items
    * type and size used in the view. The mysql database
    * defines strings as varchar and has a length.
    *
    * @param string $type - mysql type string  
    * @author Tracy A McCormick
    * @return array type info
    */      
    public function setVarchar($size) {
      switch ($size) { 
        case $size <= 30:
            return (['type' => 'string', 'size' => 'default']); 
            break;                     
        case $size <= 150:
            return (['type' => 'string', 'size' => 'medium']);
            break;
        default:
            return (['type' => 'string', 'size' => 'big']);
      }
    }

    /** 
    * Function is used to create array of 2 items
    * type and size used in the view. The mysql database
    * defines fields differently ie (mediumint, bigint, integer)
    * than how they are used in the views.
    *
    * @param string $type - mysql type string  
    * @author Tracy A McCormick
    * @return array type info
    */      
    public function setInteger($type) {
      switch ($type) {
        case 'mediumint':
          return (['type' => 'integer', 'size' => 'medium']);
          break;
        case 'bigint':
          return (['type' => 'integer', 'size' => 'big']); 
          break;
        default:  
          return (['type' => 'integer', 'size' => 'default']);      
      }      
    }    

    /** 
    * Function is used to create array of 2 items
    * type and size used in the view. The mysql database
    * defines fields differently ie (mediumtext, longtext, text)
    * than how they are used in the views.
    *
    * @param string $type - mysql type string  
    * @author Tracy A McCormick
    * @return array type info
    */      
    public function setText($type) {
      switch ($type) {
        case 'mediumtext':  
          return (['type' => 'text', 'size' => 'medium']);
          break;
        case 'longtext':
          return (['type' => 'text', 'size' => 'big']);
          break;
        default:
          return (['type' => 'text', 'size' => 'default']);
      }      
    }      

}
