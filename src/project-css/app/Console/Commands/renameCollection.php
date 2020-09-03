<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Collection;
use App\Helpers\CollectionHelper;

/**
 * Artisan Command for renaming collection
 *
 * Command execution example 'php artisan collection:rename collection1 collection2'
 * command renames a existing collection and associated storage folder.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class renameCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:rename {collectioname} {newname}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename Collection';

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

        if ($helper->isCollection($this->argument('newname'))) {
          return $this->error('Collection ' . $this->argument('newname') . ' Already Exists');
        }

        // find the collection
        $thisClctn = Collection::where('clctnName', $this->argument('collectioname'))->first();

        if ($thisClctn == null) {
          return $this->error('Collection ' . $this->argument('collectioname') . ' Doesn\'t Exist');
        }
    
        // Get required fields for collection
        $data = [
            'id' => $thisClctn->id,
            'name' => $this->argument('newname'),
            'isCms' => $thisClctn->isCms
        ];
    
        // Using Collection Helper Update collection
        $helper->update($data);  

        return $this->info('Collection Has been Renamed.');    
    }
}
