<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * migration adds the cmdId field to the collections
 * table. The cmdId is used to correctly determine which
 * set of cms headers to use when importing tables into
 * a cms collection.
 * 
 */  
class AddCmsidToCollections extends Migration
{
    /**
     * Run the migration adding the field to the table.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('collections', function($table) {
          $table->integer('cmsId')->default(null);
      });
    }

    /**
     * Reverse the migration remove the field from the table
     *
     * @return void
     */
    public function down()
    {
      Schema::table('collections', function($table) {
          $table->dropColumn('cmsId');
      });
    }
}
