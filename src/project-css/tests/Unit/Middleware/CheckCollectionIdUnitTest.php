<?php
  # app/tests/Unit/middleware/CheckCollectionIdUnitTest.php

  use App\Http\Middleware\CheckCollectionId;
  use Illuminate\Http\Request;

  class CheckCollectionIdUnitTest extends TestCase {

    public function setUp(): void {
         parent::setUp();

         \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',[$this->testHelper->generateCollectionName(), true, true]);
         \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',[$this->testHelper->generateCollectionName(), false, false]);
    }

    /** @test */
    public function collection_id_is_set()
    {
        $request = new Request;

        $request->merge([
            'curCol' => 1
        ]);

        $middleware = new CheckCollectionId;

        $middleware->handle($request, function ($req) {
            $this->assertEquals(1, $req->curCol);
        });
    }

    public function message_set_disabled_collection()
    {
        $request = new Request;

        $request->merge([
            'curCol' => 2
        ]);

        $middleware = new CheckCollectionId;

        $middleware->handle($request, function ($req) {
            $this->assertEquals('Collection is disabled', $req->message);
        });
    }

    public function message_set_invalid_collection()
    {
        $request = new Request;

        $request->merge([
            'curCol' => 3
        ]);

        $middleware = new CheckCollectionId;

        $middleware->handle($request, function ($req) {
            $this->assertEquals('Collection id is invalid', $req->message);
        });
    }

    // public function test_handler()
    // {
    //     $request = new Request;

    //     $request->merge([
    //         'curCol' => 2
    //     ]);

    //     $middleware = new CheckCollectionId;

    //     $response = $middleware->handle($request, function ($req) {
    //         $this->assertEquals('Collection id is invalid', $req->message);
    //     });

    //     var_dump($response);
    // }

    public function testIsValidCollection() {
        $response =  (new CheckCollectionId)->isValidCollection('invalid');
        $this->assertFalse($response);
        $response =  (new CheckCollectionId)->isValidCollection(0);
        $this->assertFalse($response);
        $response =  (new CheckCollectionId)->isValidCollection(1);
        $this->assertTrue($response);
    }

    public function testHasAccess() {
        $response =  (new CheckCollectionId)->hasAccess(0);
        $this->assertFalse($response);
        $response =  (new CheckCollectionId)->hasAccess(1);
        $this->assertTrue($response);
    }    

  }
?>
