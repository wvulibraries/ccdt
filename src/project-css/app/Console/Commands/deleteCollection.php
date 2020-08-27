<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;
use App\Models\Collection;

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

        // skip verify of items if force is set
        if ($this->option('force') == false) {
            // verify collection exists
            if ($helper->isCollection($this->argument('collectioname')) == false) {
                return $this->error('Collection ' . $this->argument('collectioname') . ' Doesn\'t Exist');
            }

            // find the collection
            $thisClctn = Collection::where('clctnName', $this->argument('collectioname'))->first();            

            // Verify That No files exist in the Storage Folder for the Collection
            if ($thisClctn->hasFiles()) {
                return $this->error('Unable to remove Collection ' . $this->argument('collectioname') . '. Files Exist in Storage Folder.');
            }

            // Verify That No Tables are associated with this collection
            if ($thisClctn->hasTables()) {
                return $this->error('Unable to remove Collection ' . $this->argument('collectioname') . '. Tables are Associated With the Collection.');
            }
        }

        // call helper delete collection
        $helper->deleteCollection($this->argument('collectioname'));

        return $this->info('Collection Has been Deleted.');          
    }
}
