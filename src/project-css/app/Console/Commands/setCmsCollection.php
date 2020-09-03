<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;

/**
 * Artisan Command for setting the cms option on a collection
 *
 * Command execution example 'php artisan collection:cms:set collection1'
 * command sets the collection as a cms collection.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class setCmsCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:cms:set {collectioname}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set Collection for CMS';

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

        // Using Collection Helper set collection cms option
        $helper->setCMS($this->argument('collectioname'), true);   

        return $this->info('Collection is set as CMS');        
    }
}
