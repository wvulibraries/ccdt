<?php

namespace App\Console\Commands;

use App\Jobs\CreateSearchIndex;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class createSrchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:create:search {tablename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Search Index for all records in the table';

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
        dispatch(new CreateSearchIndex($this->argument('tablename')));
    }
}
