<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;
use App\Helpers\TableHelper;
use App\Models\Collection;
use Illuminate\Support\Facades\Schema;


/**
 * Artisan Command for importing a table
 *
 * Command execution example 'php artisan table:import collection1 table1 file.csv' 
 * the above command will import a file into a new table. It will also create the collection
 * if it doesn't exist.
 * 
 */
class importTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:import {collectioname} {tablename} {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import File into a table and create collection if missing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Schema::hasTable($this->argument('tablename'))) { 
            return $this->error('Table Name Already Exists.');
        }

        // get collection
        $collection = Collection::where('clctnName', '=', $this->argument('collectioname'))->first();

        // if collection is null create the collection
        if ($collection == null) {
            // Get required fields for collection
            $data = [
                'isCms' => false,
                'name' => $this->argument('collectioname')
            ];
        
            // Using Collection Helper Create a new collection
            $collection = (new CollectionHelper)->create($data);        
        }

        // set storage location
        $storageFolder = 'flatfiles';

        // create table and queue file for import
        (new TableHelper)->importFile($storageFolder, $this->argument('filename'), $this->argument('tablename'), $collection->id, $collection->isCms);
        
        return $this->info('File ' . $this->argument('filename') . ' Has Been Queued For Import.');
    }
}
