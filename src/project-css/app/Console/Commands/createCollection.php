<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Collection;
use App\Helpers\CollectionHelper;

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
        // verify collection doesn't exist
        if (Collection::where('clctnName', '=', $this->argument('collectioname'))->count() == 0) {
            // Get required fields for collection
            $data = [
                'isCms' => $this->option('iscms'),
                'collectionName' => $this->argument('collectioname')
            ];
        
            // Using Collection Helper Create a new collection
            (new CollectionHelper)->create($data);        
        }
        else {
            $this->error('Collection ' . $this->argument('collectioname') . ' Already Exists');
        }
    }
}
