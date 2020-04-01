<?php
use \Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Collection;
use App\Models\Table;
use App\Libraries\CSVHelper;
use App\Libraries\CMSHelper;
use App\Libraries\FileViewHelper;
use App\Libraries\TableHelper;

class TableHelperTest extends BrowserKitTestCase
{
    public function setUp(): void {
        parent::setUp();
        //Artisan::call('migrate:fresh --seed');
    }

    protected function tearDown(): void {
        //Artisan::call('migrate:rollback');
        parent::tearDown();
    }

    public function testCreateTableWithHeader() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'zillow.csv';

        $tableName = (new TestHelper)->createTable($storageFolder, $fileName, true);

        $this->assertTrue(Schema::hasTable($tableName));

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        // assert field count is equal to 7
        $this->assertEquals($table->getOrgCount(), 7);

        // drop test table
        Schema::dropIfExists($tableName);  
        
        // find the collection
        $collection = Collection::findorFail($table->collection_id); 
        
        // remove folder that was created for the collection
        rmdir('./storage/app'.'/'.$collection->clctnName);        
    }

    public function testCreateCMSTableWithoutHeader() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = '1A-random.tab';

        $tableName = (new TestHelper)->createTable($storageFolder, $fileName, false);

        $this->assertTrue(Schema::hasTable($tableName));

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        // assert field count is equal to 7
        $this->assertEquals($table->getOrgCount(), 13);       

        // drop test table
        Schema::dropIfExists($tableName);

        // find the collection
        $collection = Collection::findorFail($table->collection_id); 
        
        // remove folder that was created for the collection
        rmdir('./storage/app'.'/'.$collection->clctnName);
    }

}
