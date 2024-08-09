<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;

/**
 * Artisan Command for unsetting the cms option on a collection
 *
 * Command execution example 'php artisan collection:cms:unset collection1'
 * command unsets the cms option on a collection.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class unsetCmsCollection extends Command
{
    /**
     * The name and signature of the console command.
     * describes command name and params expected. 
     *
     * @var string
     */
    protected $signature = 'collection:cms:unset {collectioname}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set CMS to False for Collection';

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

        // Using Collection Helper unset cms option
        $helper->setCMS($this->argument('collectioname'), false); 
        
        $this->info('Collection is set as Flatfile');
        return;        
    }
}
