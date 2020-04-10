<?php
// use \Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Collection;
// use App\Models\Table;
// use App\Libraries\CSVHelper;
// use App\Libraries\CMSHelper;
// use App\Libraries\FileViewHelper;
use App\Helpers\CollectionHelper;

class CollectionHelperTest extends BrowserKitTestCase
{
    public $helper;

    public function setUp(): void {
        parent::setUp();
        $this->helper = new CollectionHelper;  
    }

     public function testCreateCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => 'TestCollection'
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // assert collection was created
        $this->seeInDatabase('collections', ['clctnName' => $data['name']]);

        // assert collection storage was created
        $this->assertTrue(Storage::exists($data['name']));
       
        // Cleanup Test Collection
        $this->helper->deleteCollection($data['name']);

        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name']));         
     }

     public function testUpdateCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => 'TestCollection'
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Set required fields for collection update
        $data = [
          'isCms' => true,
          'id' => $collection->id,
          'name' => 'TestCollection2'
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

        // Cleanup Test Collection
        $this->helper->deleteCollection($data['name']);  
        
        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name']));         
     }
        
     public function testDisableEnableCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => 'TestCollection'
        ];

        // Call helper create
        $collection = $this->helper->create($data);
        
        // Call helper disable
        $this->assertTrue($this->helper->disable($data['name']));    

        // Call helper enable
        $this->assertTrue($this->helper->enable($data['name']));    

        // Cleanup Test Collection
        $this->helper->deleteCollection($data['name']); 
        
        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name']));         
     }

     public function testIsCollection() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => 'TestCollection'
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call helper disable
        $this->assertTrue($this->helper->isCollection($data['name']));         

        // Delete Test Collection
        $this->helper->deleteCollection($data['name']); 
        
        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name'])); 
     }

     public function testHasTables() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => 'TestCollection'
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call helper to see that no tables are assoicated to the collection
        $this->assertFalse($this->helper->hasTables($data['name']));   
        
        // Delete Test Collection
        $this->helper->deleteCollection($data['name']);   
        
        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name']));        
     }

     public function testHasFiles() {
        // Create Test Data Array
        $data = [
          'isCms' => false,
          'name' => 'TestCollection'
        ];

        // Call helper create
        $collection = $this->helper->create($data);

        // Call helper to see that no files exist
        $this->assertFalse($this->helper->hasFiles($data['name']));   
        
        // Delete Test Collection
        $this->helper->deleteCollection($data['name']);
        
        // Call helper isCollection Verify Collection Doesn't Exist
        $this->assertFalse($this->helper->isCollection($data['name']));         
     }     

}
