<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;
use App\Models\Collection;

/**
 * Artisan Command for deleting a collection
 *
 * Command execution example 'php artisan collection:delete collection1'
 * command removes passed collection if it exists. The use of '--force' 
 * bypasses checking for attached files or tables.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class deleteCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:delete {collectioname} {--force}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a Collection';

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

    public function validate_collection() {
            // boolean used to see if we need to return after error checking
            // so we can display all errors upon exit of command
            $error = false;

            // find the collection
            $thisClctn = Collection::where('clctnName', $this->argument('collectioname'))->first();            

            // Verify That No files exist in the Storage Folder for the Collection
            if ($thisClctn->hasFiles()) {
                // set error
                $this->error('Unable to remove Collection ' . $this->argument('collectioname') . '. Files Exist in Storage Folder.');
                $error = true;
            }

            // Verify That No Tables are associated with this collection
            if ($thisClctn->hasTables()) {
                // set error
                $this->error('Unable to remove Collection ' . $this->argument('collectioname') . '. Tables are Associated With the Collection.');
                $error = true;
            }

            // return $error
            return $error;       
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

        // skip verify of items if force is set
        if (($this->option('force') == false) && ($this->validate_collection())) {
            // errors were found preventing deletion of collection
            return;   
        }

        // call helper delete collection
        $this->helper->deleteCollection($this->argument('collectioname'));

        $this->info('Collection Has been Deleted.');  
        return;         
    }
}
