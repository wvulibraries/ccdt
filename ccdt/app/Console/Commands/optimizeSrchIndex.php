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
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class optimizeSrchIndex extends Command
{
    /**
     * The name and signature of the console command.
     * describes command name and params expected.
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
     * handle() is the main routine of php artisan commands 
     * The command first verifies table exists then dispatch's a 
     * OptimizeSearchIndex job on the high job queue for processing. 
     * If table isn't found a error is set which is displayed in the 
     * terminal when the command completes it execution.
     *
     * @return mixed
     */
    public function handle()
    {
        // insure table exists
        if (Schema::hasTable($this->argument('tablename'))) { 
            dispatch(new OptimizeSearchIndex($this->argument('tablename')))->onQueue('high');

            $this->info('Job has been created to Optimize Search Index');   
            return; 
        }

        $this->error('Table Doesn\'t Exist.');   
        return;    
    }
}
