<?php
use App\Models\Collection;
use App\Models\Table;
use App\Helpers\CollectionHelper;
use App\Helpers\TableHelper;

class CollectionHelperTest extends BrowserKitTestCase
{
    public $helper;

    private $colName;
    private $colName2;
    private $tableName;       

    public function setUp(): void {
        parent::setUp();
        $this->helper = new CollectionHelper; 
        
        // Generate Collection Name
        $this->colName = $this->testHelper->generateCollectionName();
        $this->colName2 = $this->colName + 1;

        // Generate Table Name
        $this->tableName = $this->testHelper->createTableName();         
    }

    protected function tearDown(): void {
        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTables();

        // Delete Test Collections
        $this->testHelper->deleteTestCollections();         

        parent::tearDown();
    }     

     public function testCreateCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // assert collection was created
        $this->seeInDatabase('collections', ['clctnName' => $data['name']]);

        // assert collection storage was created
        $this->assertTrue(Storage::exists($data['name']));        
     }

     public function testCreateCollectionWithCMSid() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'cmsId' => 1,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // assert collection was created
        $this->seeInDatabase('collections', ['clctnName' => $data['name'], 'cmsId' => 1]);

        // assert collection storage was created
        $this->assertTrue(Storage::exists($data['name']));        
     }     

     public function testUpdateCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Set required fields for collection update
        $data = [
          'isCms' => true,
          'id' => $collection->id,
          'name' => $this->colName2
        ];
        
        // Call helper update
        $collection = $this->helper->update($data);    
        
        // check if collection was renamed
        $collection = Collection::find($data['id']);
        $this->assertEquals($data['name'], $collection->clctnName);

        // check if collection isCms set to 1
        $this->assertEquals($collection->isCms, 1);

        // assert collection storage is set to new name
        $this->assertTrue(Storage::exists($data['name']));        
     }
        
     public function testDisableCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => 1,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);
        
        // Call helper disable
        $this->assertTrue($this->helper->disable($data['name']));    

        // Cleanup Test Collection
        $this->helper->deleteCollection($data['name']); 
        
        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name'])); 
        
        // Call helper disable collection that doesn't exist
        $this->assertFalse($this->helper->disable($data['name']));         
     }

     public function testEnableCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => 0,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);   

        // Call helper enable
        $this->assertTrue($this->helper->enable($data['name']));    

        // Cleanup Test Collection
        $this->helper->deleteCollection($data['name']); 
        
        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name'])); 
        
        // Call helper enable collection that doesn't exist
        $this->assertFalse($this->helper->enable($data['name']));         
     }     

     public function testIsCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call helper disable
        $this->assertTrue($this->helper->isCollection($data['name']));         
     }

     public function testCollectionHasNoTables() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call helper to see that no tables are assoicated to the collection
        $this->assertFalse($this->helper->hasTables($data['name']));        
     }

     public function testCollectionHasTables() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = '1A-random.tab';  

        // Call helper on a collection that hasn't been created
        $this->assertFalse($this->helper->hasTables($this->colName));          

        // Create Test Data Array
        $data = [
          'isCms' => true,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);       

        // Create/Import New Table
        (new TableHelper)->importFile($storageFolder, $fileName, $this->tableName, $collection->id, $collection->isCms);
       
        // Call helper to see that no tables are assoicated to the collection
        $this->assertTrue($this->helper->hasTables($collection->clctnName));         
     }     

     public function testHasFiles() {
        // Call helper should return false on collection that doesn't exist
        $this->assertFalse($this->helper->hasFiles($this->colName));

        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call helper to see that no files exist
        $this->assertFalse($this->helper->hasFiles($this->colName));   

        // create a empty file in collection
        $emptyFile = './storage/app/' . $this->colName . '/empty.txt';
        touch($emptyFile);        

        // Call helper to see that files exist
        $this->assertTrue($this->helper->hasFiles($this->colName));     
     }     

     public function testSetCMS() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call setCMS
        $this->helper->setCMS($data['name'], true);    
        
        // get current collection since it was updated
        $collection = Collection::find($collection->id);

        // check if collection isCms set to 1
        $this->assertEquals($collection->isCms, true);       
     }

     public function testunSetCMS() {
        // Create Test Data Array
        $data = [
          'isCms' => true,
          'name' => $this->colName
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call setCMS
        $this->helper->setCMS($data['name'], false);    
        
        // get current collection since it was updated
        $collection = Collection::find($collection->id);

        // check if collection isCms set to 1
        $this->assertEquals($collection->isCms, false);       
     }
}
