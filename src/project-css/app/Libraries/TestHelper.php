<?php

namespace App\Libraries;

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
}
