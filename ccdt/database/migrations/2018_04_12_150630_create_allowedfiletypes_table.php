<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * migration creates the allowedfiletypes table. allowedfilestypes
 * is a white list of file types that can be used by ccdt.
 * 
 */ 
class CreateAllowedFileTypesTable extends Migration
{
    /**
     * Run the migration will create a table called allowedfiletypes
     * with the fields below.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowedfiletypes', function(Blueprint $table) {
          $table->increments('id');
          $table->string('extension');
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
        Schema::dropIfExists('allowedfiletypes');
    }
}
