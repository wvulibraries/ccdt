<?php

use App\Libraries\CMSHelper;
use App\Libraries\CSVHelper;
use App\Helpers\TableHelper;
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
     public function createCollection($name, $isCms = false) {
          $collection = factory(Collection::class)->create([
            'clctnName' => $name,
            'isCms' => $isCms,
          ]);

          // Create Collection Storage Folder
          if (Storage::exists($name) == FALSE) {
            Storage::makeDirectory($name, 0775);
          }

          return $collection;
     }

     public function createDisabledCollection($name) {
          $collection = factory(Collection::class)->create([
               'clctnName' => $name,
               'isEnabled' => '0',
          ]);

          // Create Collection Storage Folder          
          if (Storage::exists($name) == FALSE) {
            Storage::makeDirectory($name, 0775);
          }

          return $collection;
     }

     public function createTestTable($tblNme, $collection_id = 1, $hasAccess = 1) {
        //insert record into table for testing
        \DB::insert('insert into tables (tblNme, collection_id, hasAccess) values(?, ?, ?)',[$tblNme, $collection_id, $hasAccess]);

        // define testing table
        $createTableSqlString =
          "CREATE TABLE $tblNme (
               id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
               firstname TEXT,
               lastname TEXT, 
               srchindex LONGTEXT,
               created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
               updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
          )
          COLLATE='utf8_general_ci'
          ENGINE=InnoDB
          AUTO_INCREMENT=1;";
     
        // insert testing table
        \DB::statement($createTableSqlString);

        $alterTable = "ALTER TABLE $tblNme ADD FULLTEXT index_name(srchindex);";
        \DB::statement($alterTable);
     }

     public function insertTestRecord($tblNme, $firstName, $lastName) {
        $insertString = "insert into $tblNme (firstname, lastname, srchindex) values(?, ?, ?)";

        //insert record into table for testing
        \DB::insert($insertString,[$firstName, $lastName, $firstName . ' ' . $lastName]);
     }    

     public function seedTestTable($tblNme, $numItems) {
        $faker = Faker\Factory::create();  
        for ($x = 0; $x <= $numItems; $x++) {
            //insert record into table for testing
            $this->insertTestRecord($tblNme, $faker->firstName, $faker->lastName);
        } 
     }    

     public function createCollectionWithTable($colNme, $tblNme) {
        // create test collection
        $this->createCollection($colNme);

        // create empty table
        $this->createTestTable($tblNme);
     }

     public function createCollectionWithTableAndRecords($colNme, $tblNme) {
        // create test collection
        $this->createCollection($colNme);

        // create empty table        
        $this->createTestTable($tblNme);

        // populate table
        $this->seedTestTable($tblNme, 100);
     }

     public function createDisabledCollectionWithTable($colNme, $tblNme) {
        // create test collection          
        $this->createDisabledCollection($colNme);

        // create empty table with disabled access
        $this->createTestTable($tblNme, 1, 0);
     }
     
     public function cleanupTestTables($files = []) {
       $tables = \DB::table('tables')->get();

       foreach ($tables as $table)
       {
          //\Storage::deleteDirectory($table->tblNme);
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

}
