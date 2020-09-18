<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * migration adds the isCms field to the collections
 * table. Used to set if the collection is cms. If the
 * collection is cms we can look if there is a corresponding 
 * cms header when we import tables.
 * 
 */  
class AddIscmsToCollections extends Migration
{
    /**
     * Run the migration adding the field to the table.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->boolean('isCms')->default(false);
        });
    }

    /**
     * Reverse the migration remove the field from the table
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            $table->dropColumn('isCms');
        });
    }
}

