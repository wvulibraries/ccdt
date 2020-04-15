<?php
use App\Helpers\CollectionHelper;
use App\Helpers\TableHelper;
use App\Models\Table;
use App\Libraries\CustomStringHelper;

class TableHelperTest extends BrowserKitTestCase
{
    public $tableHelper;
    public $collectionHelper;

    public function setUp(): void {
        parent::setUp();
        $this->tableHelper = new TableHelper;  
        $this->collectionHelper = new CollectionHelper;
    }

    public function testCMSTableImport() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = '1A-random.tab';  

        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', true);     
        
        // Test Table Name
        $tableName = 'test'.time();

        // Create Table and Dispatch file Import
        $this->tableHelper->importFile($storageFolder, $fileName, $tableName, $collection->id, $collection->isCms);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        // assert field count is equal to 13
        $this->assertEquals($table->getOrgCount(), 13);       
        
        // drop test table
        Schema::dropIfExists($tableName);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collection->clctnName);           
    }     

    public function testFlatfileTableImport() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'zillow.csv'; 

        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);
        
        // Test Table Name
        $tableName = 'test'.time();

        // Create Table and Dispatch file Import
        $this->tableHelper->importFile($storageFolder, $fileName, $tableName, $collection->id, $collection->isCms);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        // assert field count is equal to 7
        $this->assertEquals($table->getOrgCount(), 7);   
        
        // drop test table
        Schema::dropIfExists($tableName);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collection->clctnName);          
    } 

    public function testSetTableInCollection() {
        // Create Test Collection(s)        
        $collection1 = $this->createTestCollection('TestCollection1', false);
        $collection2 = $this->createTestCollection('TestCollection2', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection1);

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
        
        // drop test table
        Schema::dropIfExists($tableName);

        // Delete Test Collection(s)
        $this->collectionHelper->deleteCollection($collection1->clctnName);     
        $this->collectionHelper->deleteCollection($collection2->clctnName);
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

    private function checkFieldChange($field, $type, $size) {
        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection);

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
        
        // drop test table
        Schema::dropIfExists($tableName);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collection->clctnName);  
    }

    private function createTestTable($collection) {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'zillow.csv'; 
        
        // Test Table Name
        $tableName = 'test'.time();

        // Create Table and Dispatch file Import
        $this->tableHelper->importFile($storageFolder, $fileName, $tableName, $collection->id, $collection->isCms);
        
        // return table name
        return ($tableName);
    }
    
    private function createTestCollection($name, $isCms) {
        // Create Collection Test Data Array
        $data = [
          'isCms' => $isCms,
          'name' => $name
        ];

        // Call collection helper create
        return($this->collectionHelper->create($data));         
    }

}
