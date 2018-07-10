<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecordTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recordtypes', function(Blueprint $table) {
          $table->increments('id');
          $table->string('tblNme', 191)->unique();
          $table->string('recordType');  
          $table->integer('fieldCount');
          $table->text('fieldNames');
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recordtypes');
    }
}
