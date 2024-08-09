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
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class disableCollection extends Command
{
    /**
     * The name and signature of the console command.
     * describes command name and params expected.
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

    private $helper;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->helper = new CollectionHelper;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // verify collection exists
        if ($this->helper->isCollection($this->argument('collectioname')) == false) {
            $this->error('Collection ' . $this->argument('collectioname') . ' Doesn\'t Exist');
            return; 
        }

        // Using Collection Helper Disable collection
        $this->helper->disable($this->argument('collectioname')); 
        
        $this->info('Collection Has been Disabled.');  
        return; 
    }
}
