<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Libraries;
use App\Jobs\FileImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Models\Collection;
use App\Models\CMSRecords;
use App\Models\Table;
use App\Libraries\CSVHelper;
use App\Libraries\CMSHelper;
use App\Libraries\CustomStringHelper;

class TableHelper {
    /**
     * Table Helper
     *
     * These are various functions that help with dynamically
     * creating and modifying tables without migrations.
     *
     */

     public function createStringField($table, $curColNme, $curColSze) {
        // to do verify $curColNme is not already used
        
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

     public function createTextField($table, $curColNme, $curColSze) {
        // to do verify $curColNme is not already used
        
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
     
     public function createIntegerField($table, $curColNme, $curColSze) {
        // to do verify $curColNme is not already used

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
     
    public function setupTableField($table, $curColNme, $curColType, $curColSze) {
      // Filter the data type and size and create the column
      // Check for Strings
      if (str_is($curColType, 'string')) {
        // Check for the data type
        $this->createStringField($table, $curColNme, $curColSze);
      }

      // Check for Text data type
      if (str_is($curColType, 'text')) {
        // Check for the data type
        $this->createTextField($table, $curColNme, $curColSze);
      }

      // Check for Integer
      if (str_is($curColType, 'integer')) {
        // Check for the data type
        $this->createIntegerField($table, $curColNme, $curColSze);
      }

     }

    //  public function changeToStringField($tblNme, $curColNme, $curColSze) {          
    //     switch ($curColSze) {
    //         case 'medium':
    //             $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` VARCHAR(150)";
    //             break;
    //         case 'big':
    //             $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` VARCHAR(500)";              
    //             break;
    //         default:
    //             $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` VARCHAR(30)";                               
    //     }    
    //     return DB::connection()->statement($statement);  
    //  }

    //  public function changeToTextField($tblNme, $curColNme, $curColSze) {       
    //     switch ($curColSze) {
    //         case 'medium':
    //             $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` MEDIUMTEXT";               
    //             break;
    //         case 'big':
    //             $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` LONGTEXT";               
    //             break;
    //         default:
    //             $statement = "ALTER TABLE `{$tblNme}` MODIFY COLUMN `{$curColNme}` TEXT";                              
    //     }   
    //     return DB::connection()->statement($statement); 
    //  }    
     
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

     public function schemaChangeToStringField($tblNme, $curColNme, $curColSze) {    
      Schema::table($tblNme, function ($table) use ($curColNme, $curColType, $curColSze) {             
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

     public function schemaChangeToTextField($tblNme, $curColNme, $curColSze) {  
      Schema::table($tblNme, function ($table) use ($curColNme, $curColType, $curColSze) {
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
     
    //  public function schemaChangeToIntegerField($tblNme, $curColNme, $curColSze) {
    //     Schema::table($tblNme, function ($table) use ($curColNme, $curColType, $curColSze) {
    //       switch ($curColSze) {
    //           case 'medium':
    //             // For Integer medium is medium integer
    //             $table->mediumInteger($curColNme)->change();
    //             break;
    //           case 'big':
    //             // For Integer big is big integer
    //             $table->bigInteger($curColNme)->change();
    //             break;
    //           default:
    //             // For Integer default integer type
    //             $table->integer($curColNme)->change();                  
    //       } 
    //     });         
    //  } 

    public function changeTableField($tblNme, $curColNme, $curColType, $curColSze) {        
        switch (strtolower($curColType)) {
            case 'string':
                // $this->changeToStringField($tblNme, $curColNme, $curColSze);
                $this->schemaChangeToStringField($tblNme, $curColNme, $curColSze);
                break;
            case 'text':
                // $this->changeToTextField($tblNme, $curColNme, $curColSze);
                $this->schemaChangeToTextField($tblNme, $curColNme, $curColSze);
                break;
            case 'integer':
                $this->changeToIntegerField($tblNme, $curColNme, $curColSze);
                //$this->schemaChangeToIntegerField($table, $curColNme, $curColSze);
                break;
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

     public function fileImport($tblNme, $fltFlePath, $fltFle) {
       // set messages array to empty
       $messages = [];

       Log::info('File Import has been requested for table '.$tblNme.' using flat file '.$fltFle);
       // add job to queue
       dispatch(new FileImport($tblNme, $fltFlePath, $fltFle));
       $message = [
         'content'  =>  $fltFle.' has been queued for import to '.$tblNme.' table. It will be available shortly.',
         'level'    =>  'success',
       ];
       array_push($messages, $message);
       session()->flash('messages', $messages);
     }   

     public function createTable($filepath, $fileName, $tblNme, $fieldNames, $fieldTypes, $collctnId) {
         $fieldCount = count($fieldTypes);

         Schema::connection('mysql')->create($tblNme, function(Blueprint $table) use($fieldNames, $fieldTypes, $fieldCount, $collctnId) {

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

         // Finally create the table
         // Save the table upon the schema
         $this->crteTblInCollctn($tblNme, $collctnId);

         // queue job for import
         $this->fileImport($tblNme, $filepath, $fileName);
     }

     // $strDir is the storage folder
     // $colID is the collection id
     // $array of files
     // $cms true if uploaded files are apart of a cms set
     // $tblNme new table name to be used
     public function storeUploadsAndImport($data) {
        // Get the list of files in the directory
        $fltFleList = Storage::allFiles($data['strDir']);

        $errors = [];

        // Loop over them
        foreach ($data['flatFiles'] as $file) {
          $thisFltFileNme = $file->getClientOriginalName();
          if (in_array($data['strDir'].'/'.$thisFltFileNme, $fltFleList)) {
            array_push($errors, $thisFltFileNme . ' File already exists. Please select the file or rename and re-upload.');
          }
          else {
            // Store in the directory inside storage/app
            $file->storeAs($data['strDir'], $thisFltFileNme);
            $result = $this->importFile($data['strDir'], $thisFltFileNme, $data['tableName'], $data['colID'], $data['cms']);

            // if error in importing push returned error to $errors
            if ($result['error']) {
              array_push($errors, $result['errorList']);
            }   
          }
       }
       return redirect()->route('tableIndex')->withErrors($errors);
    }

     // $strDir is the storage folder
     // $colID is the collection id
     // $array of files
     public function selectFilesAndImport($data) {
        // array for keeping errors that we will send to the user
        $errors = [];

        // Loop over them
        foreach ($data['flatFiles'] as $file) {
           $result = $this->importFile($data['strDir'], $file, $data['tableName'], $data['colID'], $data['cms']);
           
           // if error in importing push returned error to $errors
           if ($result['error']) {
            array_push($errors, $result['errorList']);
           }   
        }
        return redirect()->route('tableIndex')->withErrors($errors);
     }

     public function importFile($strDir, $file, $tblNme, $colID, $cms) {
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
        elseif ($cms) {
          // filter record string
          $filteredType = filter_var($schema[0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
          // create table name
          $tblNme = $filteredType . time();
          // pass values to create file
          (new CMSHelper)->createCMSTable($strDir, $file, $colID, $tblNme);
        }
        else {
            $csvHelper->createFlatTable($strDir, $file, $colID, $tblNme);;
        }

        $errorArray = [
          'error' => false,
          'errorList' => []
        ];

        return ($errorArray);
      }     
}
