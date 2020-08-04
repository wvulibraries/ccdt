<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            $this->error('Table Doesn\'t Exist.');
            return;
        }

        // Drop Table if Exists
        Schema::dropIfExists($this->argument('tablename'));
        // Drop Entry in tables table
        DB::table('tables')->where('tblNme', '=', $this->argument('tablename'))->delete();

        $this->info('Table Has been Deleted.');
    }
}
