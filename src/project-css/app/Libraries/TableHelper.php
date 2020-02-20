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

class TableHelper {
    /**
     * Table Helper
     *
     * These are various functions that help with dynamically
     * creating tables that can be searched.
     *
     */

     public function setupTableField($table, $curColNme, $curColType, $curColSze) {
        // Filter the data type and size and create the column
        // Check for Strings
        if (str_is($curColType, 'string')) {
          // Check for the data type
          // Default
          if (str_is($curColSze, 'default')) {
            // For String default is 30 characters
            $table->string($curColNme, 30)->default("Null");
          }
          // Medium
          if (str_is($curColSze, 'medium')) {
            // For String medium is 150 characters
            $table->string($curColNme, 150)->default("Null");
          }
          // Big
          if (str_is($curColSze, 'big')) {
            // For String big is 500 characters
            $table->string($curColNme, 500)->default("Null");
          }
        }

        // Check for Text data type
        if (str_is($curColType, 'text')) {
          // Check for the data type
          // Default
          if (str_is($curColSze, 'default')) {
            // For text default is text type
            $table->text($curColNme);
          }
          // Medium
          if (str_is($curColSze, 'medium')) {
            // For text medium is mediumtext type
            $table->mediumText($curColNme);
          }
          // Big
          if (str_is($curColSze, 'big')) {
            // For text big is longtext type
            $table->longText($curColNme);
          }
        }

        // Check for Integer
        if (str_is($curColType, 'integer')) {
          // Check for the data type
          // Default
          if (str_is($curColSze, 'default')) {
            // For Integer default integer type
            $table->integer($curColNme)->default(0);
          }
          // Medium
          if (str_is($curColSze, 'medium')) {
            // For Integer medium is medium integer
            $table->mediumInteger($curColNme)->default(0);
          }
          // Big
          if (str_is($curColSze, 'big')) {
            // For Integer big is big integer
            $table->bigInteger($curColNme)->default(0);
          }
        }

        return $table;
     }

     /**
     * Simple function to create the table within the collections
     * @param string $tblNme
     * @param string $collctnId
     */
     public function crteTblInCollctn($tblNme, $collctnId) {
       // declare a new table instance
       $thisTabl = new Table;
       // Assign the table name and collctn id
       $thisTabl->tblNme = $tblNme;
       $thisTabl->collection_id = $collctnId;
       // Save the collection
       $thisTabl->save();
     }

     public function fileImport($tblNme, $fltFlePath, $fltFle) {
       // set messages array to empty
       $messages = [ ];

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
             $table = $this->setupTableField($table, $fieldNames[$i], $fieldTypes[$i][0], $fieldTypes[$i][1]);
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

         // create folder in storage that will contain any additional files associated to the table
         if (Storage::exists($tblNme) == FALSE) {
           Storage::makeDirectory($tblNme, 0775);
         }

         // queue job for import
         $this->fileImport($tblNme, $filepath, $fileName);
     }

     // $strDir is the storage folder
     // $colID is the collection id
     // $array of files
     public function storeUploadsAndImport($strDir, $colID, $files) {
       // Get the list of files in the directory
       $fltFleList = Storage::allFiles($strDir);

       $errors = [];

       // Loop over them
       foreach ($files as $file) {
         $thisFltFileNme = $file->getClientOriginalName();
         if (in_array($strDir.'/'.$thisFltFileNme, $fltFleList)) {
           array_push($errors, $thisFltFileNme . ' File already exists. Please select the file or rename and re-upload.');
         }
         else {
           // Store in the directory inside storage/app
           $file->storeAs($strDir, $thisFltFileNme);
           $fltFleAbsPth = $strDir.'/'.$thisFltFileNme;
           $schema = (new CSVHelper)->schema($fltFleAbsPth);
           if (!$schema) {
             Storage::delete($fltFleAbsPth);
             array_push($errors, [ 'The selected flat file must be of type: text/plain', 'The selected flat file should not be empty', 'File is deleted for security reasons' ]);
           }
           else {
             // filter record string
             $filteredType = filter_var($schema[0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);

             // create table name
             $tblNme = $filteredType . time();

             // pass values to create file
             (new CMSHelper)->createCMSTable($strDir, $thisFltFileNme, $colID, $tblNme);
           }
         }
       }

       return redirect()->route('tableIndex')->withErrors($errors);
     }

     // $strDir is the storage folder
     // $colID is the collection id
     // $array of files
     public function selectFilesAndImport($strDir, $colID, $files) {
        // array for keeping errors that we will send to the user
        $errors = [];

        // Loop over them
        foreach ($files as $file) {
          $fltFleAbsPth = $strDir.'/'.$file;
          // Calling schema will return an array containing the
          // tokenized first row of our file to be imported
          $schema = (new CSVHelper)->schema($fltFleAbsPth);
          // if the array is not valid we will delete the file
          // and push a error to the $errors array
          if (!$schema) {
            Storage::delete($fltFleAbsPth);
            array_push($errors, [ 'The selected flat file must be of type: text/plain', 'The selected flat file should not be empty', 'File is deleted for security reasons' ]);
          }
          else {
            // filter record string
            $filteredType = filter_var($schema[0], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
            // create table name
            $tblNme = $filteredType . time();
            // pass values to create file
            (new CMSHelper)->createCMSTable($strDir, $file, $colID, $tblNme);
          }
        }
        return redirect()->route('tableIndex')->withErrors($errors);
     }

}
