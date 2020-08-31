<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Collection;
use App\Helpers\CollectionHelper;

/**
 * Artisan Command for creating a collection
 *
 * Command execution example 'php artisan collection:create collection1 --iscms'
 * command creates a new collection called collection1 and sets it as a cms collection.
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

        // verify collection doesn't exist
        if ($helper->isCollection($this->argument('collectioname'))) {
          return $this->error('Collection ' . $this->argument('collectioname') . ' Already Exists');
        }

        // Get required fields for collection
        $data = [
            'isCms' => $this->option('iscms'),
            'name' => $this->argument('collectioname')
        ];
    
        // Using Collection Helper Create a new collection
        $helper->create($data);  

        return $this->info('Collection Has been Created.');              
    }
}
