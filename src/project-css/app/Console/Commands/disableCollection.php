<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;

/**
 * Artisan Command for disabling a collection
 *
 * Command execution example 'php artisan collection:disable collection1'
 * command disables the collection. Once the collection is disabled the users
 * cannot access any table(s) that have been created in the collection.
 *
 */
class disableCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:disable {collectioname}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable a Collection';

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
        $helper = new CollectionHelper;

        // verify collection exists
        if ($helper->isCollection($this->argument('collectioname')) == false) {
            return $this->error('Collection ' . $this->argument('collectioname') . ' Doesn\'t Exist');
        }

        // Using Collection Helper Disable collection
        $helper->disable($this->argument('collectioname')); 
        
        return $this->info('Collection Has been Disabled.');  
    }
}
