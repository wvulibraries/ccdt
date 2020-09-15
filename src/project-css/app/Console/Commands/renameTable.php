<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Artisan Command for renaming a table
 *
 * Command execution example 'php artisan table:rename table1 table2' 
 * the above command will rename table.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class renameTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:rename {tablename} {newname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename Table From Database';

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

        // insure new table name doesn't exist
        if (Schema::hasTable($this->argument('newname'))) { 
            $this->error($this->argument('newname') . ' Already Exists.');
            return; 
        }

        // Rename Table
        Schema::rename($this->argument('tablename'), $this->argument('newname'));
        
        // Rename table name in tables
        DB::table('tables')->where('tblNme', '=', $this->argument('tablename'))->update(['tblNme' => $this->argument('newname')]);

        $this->info('Table Has been Renamed.');
        return; 
    }
}
