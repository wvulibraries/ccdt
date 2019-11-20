<?php

namespace App\Libraries;

use App\Libraries\CMSHelper;
use App\Libraries\CSVHelper;
use App\Libraries\TableHelper;
use App\Models\Collection;

class TestHelper {
    /**
     * test Helper
     *
     * These are various functions created to assist in Testing
     * the application
     *
     */

     /**
      * creates a collection used for Testing
      *
      * @param string $name name of collection to use for test
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      */
     public function createCollection($name) {
          $collection = factory(Collection::class)->create([
               'clctnName' => $name,
          ]);
          return $collection;
     }

     public function createDisabledCollection($name) {
          $collection = factory(Collection::class)->create([
               'clctnName' => $name,
               'isEnabled' => false,
          ]);
          return $collection;
     }
     

     public function cleanupTestTables($files = []) {
       $tables = \DB::table('tables')->get();

       foreach ($tables as $table)
       {
          \Storage::deleteDirectory($table->tblNme);
          \Schema::drop($table->tblNme);
       }

       foreach ($files as $file)
       {
          \Storage::delete('/flatfiles/'.$file);
       }
     }

     public function createTable($storageFolder, $fileName, $headerRowExists) {
       // Test Table Name
       $tableName = 'test'.time();

       // create test collection
       $collection = $this->createCollection(time());

       //pass true if contains header row, file location, number of rows to check
       $fieldTypes = (new CSVHelper)->determineTypes($headerRowExists, $storageFolder.'/'.$fileName, 10);

       // get header from csv file
       $schema = (new CSVHelper)->schema($storageFolder.'/'.$fileName);

       if (!$headerRowExists) {
          $schema = (new CMSHelper)->getCMSFields($collection->id, $schema[0], count($fieldTypes));

          if ($schema == null) {
            // generate header using number of detected fields
            $schema = (new CMSHelper)->generateHeader(count($fieldTypes));
          }
       }

       // create new test table
       (new TableHelper)->createTable($storageFolder, $fileName, $tableName, $schema, $fieldTypes, $collection->id);

       // return name of test table created
       return $tableName;
     }

     public function createCollectionWithTable($collection, $table) {
       \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',[$collection, true, true]);

       //insert record into table for testing
       \DB::insert('insert into tables (tblNme, collection_id, hasAccess) values(?, ?, ?)',[$table, 1, true]);
     }

     public function createDisabledCollectionWithTable($collection, $table) {
       \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',[$collection, false, false]);

       //insert record into table for testing
       \DB::insert('insert into tables (tblNme, collection_id, hasAccess) values(?, ?, ?)',[$table, 1, true]);
     }
}
