<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * migration creates the recordtypes table. Record Types 
 * are used to when importing a cms table into a collection.
 * cms files have no headers so we store them here.
 * 
 */ 
class CreateRecordTypesTable extends Migration
{
    /**
     * Run the migration will create a table called recordtypes
     * with the fields below.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recordtypes', function(Blueprint $table) {
          $table->increments('id');
          $table->integer('cmsId');
          $table->string('recordType');
          $table->integer('fieldCount');
          $table->text('fieldNames');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migration removes the table from the database
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recordtypes');
    }
}
