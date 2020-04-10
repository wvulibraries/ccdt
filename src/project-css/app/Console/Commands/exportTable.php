<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class exportTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:export {tablename} {fields*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Table';

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
        // var_dump($this->argument('tablename'));
        // var_dump($this->argument('fields'));
        
        // //$columns = array('zip', 'in_date', 'in_topic');
        // $file = fopen('storage/app/exports/' . $this->argument('tablename') .'.csv', 'w');
        // fputcsv($file, $this->argument('fields'));
        // $records = DB::table($this->argument('tablename'))->get();

        // foreach ($records as $record) {
        //      //fputcsv($file, array($record->zip, $record->in_date, $record->in_topic));
        //     var_dump($record);
        //     die();
        // }
        // fclose($file);
    }
}
