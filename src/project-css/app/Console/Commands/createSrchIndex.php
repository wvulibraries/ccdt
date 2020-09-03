<?php

namespace App\Console\Commands;

use App\Jobs\CreateSearchIndex;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Schema;

/**
 * Artisan Command for creating a the search index of a table
 *
 * Command execution example 'php artisan table:create:search table1'
 * command dispatches job that creates the search index on table1
 *
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
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
        if (!Schema::hasTable($this->argument('tablename'))) { 
            return $this->error('Table Doesn\'t Exist.');
        }

        dispatch(new CreateSearchIndex($this->argument('tablename')))->onQueue('importQueue');

        return $this->info('Job has been created to Create Search Index');          
    }
}
