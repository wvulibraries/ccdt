<?php

use App\Helpers\CollectionHelper;
use App\Helpers\TableHelper;
use App\Models\Collection;
use Illuminate\Support\Facades\Schema;

class TestHelper {
   // location of test files
   private $filePath = './storage/app';

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
    * @param integer $isEnabled default 1
    * @param boolean $isCms default false
    * @param integer $cmsId default null
    *
    * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
    */
    public function createCollection($name, $isEnabled = 1, $isCms = false, $cmsId = null) {
      // Create Collection Test Data Array
      $data = [
         'isCms' => $isCms,
         'clctnName' => $name,
         'isEnabled' => $isEnabled,        
      ];

      // include $cmsId if not null
      if ($cmsId != null) {
         $data['cmsId'] = $cmsId;
      }      

      $collection = factory(Collection::class)->create($data);

      // Create Collection Storage Folder          
      if (Storage::exists($name) == FALSE) {
         Storage::makeDirectory($name, 0664);
      }

      return $collection;      
    }     

   /**
    * Calls Collection Helper delete collection
    *
    * @param string $name name of collection to use for test
    *
    * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
    */    
   public function deleteCollection($name) {
      // Call collection helper create
      return((new CollectionHelper)->deleteCollection($name));    
   }   

   public function deleteTestCollections() {
      $helper = new CollectionHelper;
      $collections = \DB::table('collections')->get();

      foreach ($collections as $collection)
      {
        $helper->deleteCollection($collection->clctnName); 
      }
   }

   public function createEmptyTestTable($tblNme, $collection_id = 1, $hasAccess = 1) {
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
      $insertString = "insert into $tblNme (firstname, lastname) values(?, ?)";

      //insert record into table for testing
      \DB::insert($insertString,[$firstName, $lastName]);        
   }    

   public function seedTestTable($tblNme, $numItems) {
      $faker = Faker\Factory::create();  
      for ($x = 0; $x <= $numItems; $x++) {
         //insert record into table for testing
         $this->insertTestRecord($tblNme, $faker->firstName, $faker->lastName);
      } 
   }  
   
   public function createTestCSV($fileName, $numItems) {
      $faker = Faker\Factory::create();  

      $columns = array('StringDefault', 'StringMedium', 'StringBig', 'TextDefault', 'TextMedium', 'TextBig', 'IntegerDefault', 'IntegerMedium', 'IntegerBig');

      $file = fopen('storage/app/flatfiles/' . $fileName, 'w');
      fputcsv($file, $columns);

      for ($x = 0; $x <= $numItems; $x++) {
         // faker cannot make a number as larage as we need to we join 2 together
         $integerBig = $faker->randomNumber(8, true) . $faker->randomNumber(mt_rand(1, 2), true);     

         fputcsv($file, array($faker->word, $faker->words(10, true), $faker->text(500), $faker->text(1000), $faker->text(3000), $faker->text(9000), $faker->randomDigit(), $faker->randomNumber(7, true), $integerBig));
      }

      fclose($file);      
   }   

   public function createCollectionWithTable($colNme, $tblNme) {
      // create test collection
      $this->createCollection($colNme);

      // create empty table
      $this->createEmptyTestTable($tblNme);
   }

   public function createCollectionWithTableAndRecords($colNme, $tblNme) {
      // create test collection
      $this->createCollection($colNme);

      // create empty table        
      $this->createEmptyTestTable($tblNme);

      // populate table
      $this->seedTestTable($tblNme, 100);
   }

   public function createDisabledCollectionWithTable($colNme, $tblNme) {
      // create test collection          
      $this->createCollection($colNme, 0);

      // create empty table with disabled access
      $this->createEmptyTestTable($tblNme, 1, 0);
   }
     
   public function cleanupTestTablesAndFiles($files = []) {
      $this->cleanupTestTables();

      // remove uploaded files
      $this->cleanupTestUploads($files);
   }

   public function cleanupTestTables() {
      $tables = \DB::table('tables')->get();

      foreach ($tables as $table)
      {
         \Schema::drop($table->tblNme);
      }
   }

   public function cleanupTestUploads($files = []) {
      foreach ($files as $file)
      {
         \Storage::delete('/flatfiles/'.$file);
      }      
   }

   public function generateCollectionName() {
      $helper = new CollectionHelper;

      // Create Test Table Name
      $colName = time();

      // Loop until we generate one that is not in use
      while($helper->isCollection($colName)) {
         $colName = time();
      } 
        
      return $colName;
   }

   public function createTableName() {
      // Create Test Table Name
      $tableName = 'importtest'.mt_rand();

      // Create New Name if tableName exists
      // Loop until we generate one that is not in use
      while(Schema::hasTable($tableName)) {
         $tableName = 'importtest'.mt_rand();
      }       

      return $tableName;
   }

   public function createTestTable($collection, $fileName = 'zillow.csv') {
      // set storage location
      $storageFolder = 'files/test';
        
      // Create Test Table Name
      $tableName = $this->createTableName();

      // Create Table and Dispatch file Import
      (new TableHelper)->importFile($storageFolder, $fileName, $tableName, $collection->id, $collection->isCms);

      // return table name
      return ($tableName);
   }
   
}
