<?php

namespace App\Console\Commands;

use App\Http\Controllers\TableController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class importData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop Table Records and Import CSV to Table';

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
        // clear all records in table
        \DB::table('Records')->delete();
        // Import csv into table
        (new TableController)->process('Records', 'flatfiles', '10000 Sales Records.csv');
    }
}
