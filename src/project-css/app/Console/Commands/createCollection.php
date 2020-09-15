<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Collection;
use App\Helpers\CollectionHelper;

/**
 * Artisan Command for creating a collection
 *
 * Command execution example 'php artisan collection:create collection1 --iscms'
 * command creates a new collection called collection1 and sets it as a cms collection.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class createCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collection:create {collectioname} {--iscms}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Collection';

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
        // verify collection doesn't exist
        if ($this->helper->isCollection($this->argument('collectioname'))) {
            // Set Error
            $this->error('Collection ' . $this->argument('collectioname') . ' Already Exists');
            return; 
        }

        // Get required fields for collection
        $data = [
            'isCms' => $this->option('iscms'),
            'name' => $this->argument('collectioname')
        ];
    
        // Using Collection Helper Create a new collection
        $this->helper->create($data);  

        if ($this->helper->isCollection($this->argument('collectioname'))) {
            // Verify Collection Has been Created
            $this->info('Collection Has been Created.');
        }
        else {
            // Set Error
            $this->error('Unknown Error when creating Collection');                
        }
 
        return;              
    }
}
