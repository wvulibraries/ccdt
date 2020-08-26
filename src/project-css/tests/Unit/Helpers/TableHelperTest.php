<?php
use App\Helpers\CollectionHelper;
use App\Helpers\CustomStringHelper;
use App\Helpers\TableHelper;
use App\Models\Table;
use Illuminate\Database\Schema\Blueprint;

class TableHelperTest extends TestCase
{
    public $tableHelper;
    public $collectionHelper;
    private $colName;  
    private $tableName;  

    public function setUp(): void {
      parent::setUp();
      $this->tableHelper = new TableHelper;  
      $this->collectionHelper = new CollectionHelper;

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();  

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();          
    }

    protected function tearDown(): void {
      // test tables, files and folders that were created
      $this->testHelper->cleanupTestTablesAndFiles(); 

      // Delete Test Collections
      $this->testHelper->deleteTestCollections();   
      parent::tearDown();
    }     

    public function testCMSTableImport() {
      // set storage location
      $storageFolder = 'files/test';

      // set location of file
      $fileName = '1A-random.tab';  

      // Create Test Collection        
      $collection = $this->testHelper->createCollection($this->colName, 1, true);     

      // Create Table and Dispatch file Import
      $this->tableHelper->importFile($storageFolder, $fileName, $this->tableName, $collection->id, $collection->isCms);

      // get newly created table
      $table = Table::where('tblNme', $this->tableName)->first();

      // assert field count is equal to 13
      $this->assertEquals($table->getOrgCount(), 13);          
    }     

    public function testFlatfileTableImport() {
      // set storage location
      $storageFolder = 'files/test';

      // set location of file
      $fileName = 'zillow.csv'; 

      // Create Test Collection        
      $collection = $this->testHelper->createCollection($this->colName, 1, false);

      // Create Table and Dispatch file Import
      $this->tableHelper->importFile($storageFolder, $fileName, $this->tableName, $collection->id, $collection->isCms);

      // get newly created table
      $table = Table::where('tblNme', $this->tableName)->first();

      // assert field count is equal to 7
      $this->assertEquals($table->getOrgCount(), 7);           
    } 

    public function testSetTableInCollection() {
      // Create Test Collection(s)        
      $collection1 = $this->testHelper->createCollection($this->colName, 1, false);
      $collection2 = $this->testHelper->createCollection($this->testHelper->generateCollectionName(), 1, false);

      // Create Test Table
      $tableName = $this->testHelper->createTestTable($collection1);

      // get newly created table
      $table = Table::where('tblNme', $tableName)->first();

      // Verify Table Is Associated to First Collection 
      $this->assertEquals($table->collection_id, $collection1->id); 

      // Set Existing table to different collection
      $this->tableHelper->setTblInCollctn($table->id, $collection2->id);

      // get updated table
      $table = Table::where('tblNme', $tableName)->first(); 

      // Verify Table Is Associated to Second Collection 
      $this->assertEquals($table->collection_id, $collection2->id);  
    }

    public function testStoreUploadAndImportWithInvalidFile() {
        // Create Test Collection(s)        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        $path = './storage/app/files/test/';
        $file = 'images.png';

        copy($path.$file, sys_get_temp_dir().'/'.$file);

        $file = new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false);

        $data = [
            'strDir' => $path,
            'colID' => $collection->id,
            'fltFile' => $file,
            'cms' => false,
            'tableName' => $this->tableName
        ];

        $response = $this->tableHelper->storeUploadAndImport($data);
        $this->assertEquals($response[0], "The selected flat file must be of type: text/plain");
        $this->assertEquals($response[1], "The selected flat file should not be empty");
        $this->assertEquals($response[2], "File is deleted for security reasons");
    }

    public function testSelectFileAndImportWithInvalidFile() {
        // Create Test Collection(s)        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        $path = './storage/app/files/test/';
        $file = 'images.png';

        $data = [
            'strDir' => $path,
            'colID' => $collection->id,
            'fltFile' => $file,
            'cms' => false,
            'tableName' => $this->tableName
        ];

        $response = $this->tableHelper->selectFileAndImport($data);
        $this->assertEquals($response[0], "The selected flat file must be of type: text/plain");
        $this->assertEquals($response[1], "The selected flat file should not be empty");
        $this->assertEquals($response[2], "File is deleted for security reasons");
    }         

    public function testCreateIntegerFieldString() {
      // Create Test Collection(s)        
      $collection = $this->testHelper->createCollection($this->colName, 1, false);

      $this->checkFieldCreate($collection->id, 'testBig', 'integer', 'big');
      $this->checkFieldCreate($collection->id, 'testMedium', 'integer', 'medium');
      $this->checkFieldCreate($collection->id, 'testDefault', 'integer', 'default');            
    } 

    public function testCreateTableFieldString() {
      // Create Test Collection(s)        
      $collection = $this->testHelper->createCollection($this->colName, 1, false);

      $this->checkFieldCreate($collection->id, 'testBig', 'string', 'big');
      $this->checkFieldCreate($collection->id, 'testMedium', 'string', 'medium');
      $this->checkFieldCreate($collection->id, 'testDefault', 'string', 'default');            
    } 

    public function testCreateTableFieldText() {
      // Create Test Collection(s)        
      $collection = $this->testHelper->createCollection($this->colName, 1, false);

      $this->checkFieldCreate($collection->id, 'testBig', 'text', 'big');
      $this->checkFieldCreate($collection->id, 'testMedium', 'text', 'medium');
      $this->checkFieldCreate($collection->id, 'testDefault', 'text', 'default');            
    } 

    public function testChangeTableFieldToString() {
      $this->checkFieldChange("Living Space (sq ft)", 'string', 'big');
      $this->checkFieldChange("Living Space (sq ft)", 'string', 'medium');
      $this->checkFieldChange("Living Space (sq ft)", 'string', 'default');            
    }   
    
    public function testChangeTableFieldToText() {
      $this->checkFieldChange("Living Space (sq ft)", 'text', 'big');
      $this->checkFieldChange("Living Space (sq ft)", 'text', 'medium');
      $this->checkFieldChange("Living Space (sq ft)", 'text', 'default');        
    }  
    
    public function testChangeTableFieldToInteger() {
      $this->checkFieldChange("Living Space (sq ft)", 'integer', 'big');
      $this->checkFieldChange("Living Space (sq ft)", 'integer', 'medium');
      $this->checkFieldChange("Living Space (sq ft)", 'integer', 'default');
    }       

    public function checkFieldCreate($colId, $field, $type, $size) {
      // create the table
      $tableName = $this->testHelper->createTableName();
      Schema::connection('mysql')->create($tableName, function(Blueprint $table) use($field, $type, $size) {
        // Default primary key
        $table->increments('id');

        (new TableHelper)->setupTableField($table, $field, $type, $size);

        // search index
        $table->longText('srchindex');

        // Time stamps
        $table->timestamps();
      });

      // Set table collection id
      (new TableHelper)->crteTblInCollctn($tableName, $colId);      

      // get newly created table
      $table = Table::where('tblNme', $tableName)->first();

      // get update field info
      $response = $table->getColumnType($field);

      // Verify type 
      $this->assertEquals($response['type'], $type); 

      // Verify size 
      $this->assertEquals($response['size'], $size); 
    } 

    private function checkFieldChange($field, $type, $size) {
      // Create Test Collection        
      $collection = $this->testHelper->createCollection($this->testHelper->generateCollectionName(), 1, false);

      // Create Test Table
      $tableName = $this->testHelper->createTestTable($collection);

      // get newly created table
      $table = Table::where('tblNme', $tableName)->first();

      // Format Field Name
      $curColNme = (new CustomStringHelper)->formatFieldName($field);

      // update field
      $this->tableHelper->changeTableField($tableName, $curColNme, $type, $size);

      // get update field info
      $response = $table->getColumnType($curColNme);

      // Verify type changed 
      $this->assertEquals($response['type'], $type); 

      // Verify size changed 
      $this->assertEquals($response['size'], $size);  
    }   

}
