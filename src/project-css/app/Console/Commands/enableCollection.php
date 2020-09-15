<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;

/**
 * Artisan Command for enabling a disabled collection
 *
 * Command execution example 'php artisan collection:enable collection1'
 * command enables a previously disabled collection. Once the collection 
 * is enabled the users will be able to view the associated tables.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class enableCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:enable {collectioname}';


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
            $this->error('Collection ' . $this->argument('collectioname') . ' Doesn\'t Exist');
            return; 
        }

        // Using Collection Helper Disable collection
        $helper->enable($this->argument('collectioname'));   
        
        $this->info('Collection Has been Enabled.'); 
        return;         
    }
}
