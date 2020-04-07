<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class exportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Fields from Correspondence table';

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
        $columns = array('zip', 'in_date', 'in_topic');
        $file = fopen('storage/app/exports/output.csv', 'w');
        fputcsv($file, $columns);
        $records = DB::table('correspondence')->get();
        foreach ($records as $record) {
            fputcsv($file, array($record->zip, $record->in_date, $record->in_topic));
        }
        fclose($file);
    }
}
