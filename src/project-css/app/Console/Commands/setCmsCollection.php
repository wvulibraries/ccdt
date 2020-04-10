<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\CollectionHelper;

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

        // Using Collection Helper set CMS in collection to false
        $helper->setCMS($this->argument('collectioname'), true);         
    }
}
