<?php

namespace App\Console\Commands;

use App\Jobs\OptimizeSearchIndex;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Schema;

/**
 * Artisan Command for optimizing the search index of a table
 *
 * Command execution example 'php artisan table:optimize:search table1'
 * command dispatches job that performs various function on the search
 * index such removing common words, duplicate words and single characters
 * that full text search ignores.
 *
 */
class optimizeSrchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:optimize:search {tablename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize Search Index In Records';

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
        // insure table exists
        if (Schema::hasTable($this->argument('tablename'))) { 
            dispatch(new OptimizeSearchIndex($this->argument('tablename')))->onQueue('high');

            return $this->info('Job has been created to Optimize Search Index');   
        }

        return $this->error('Table Doesn\'t Exist.');      
    }
}
