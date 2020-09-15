<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Artisan Command for truncating a table
 *
 * Command execution example 'php artisan table:truncate table1' 
 * The truncate command will remove all records from the specified table
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class truncateTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:truncate {tablename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate all Records in a table';

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
        if (!Schema::hasTable($this->argument('tablename'))) { 
            $this->error('Table Doesn\'t Exist.');
            return;
        }

        DB::table($this->argument('tablename'))->truncate();

        $this->info('Table Has been Truncated.');
        return;        
    }
}
