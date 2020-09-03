<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Artisan Command for removing a table
 *
 * Command execution example 'php artisan table:drop table1'
 * command removes a table from the mysql database and from
 * the tables table that ccdt uses to keep track of tables
 * and associated collections.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class dropTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:drop {tablename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop Table From Database';

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

        // Drop Table if Exists
        Schema::dropIfExists($this->argument('tablename'));

        // Remove Entry in tables table
        DB::table('tables')->where('tblNme', '=', $this->argument('tablename'))->delete();

        return $this->info('Table Has been Deleted.');
    }
}
