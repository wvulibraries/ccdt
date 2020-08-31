<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Artisan Command for exporting a table to a csv file
 *
 * Command execution example 'php artisan table:export table1 --field=id --field=name' 
 * the above command will generate a csv file from table1 called table1.csv in the 
 * exports folder. The new file in this example will only export 2 fields id and name
 * to the new file.
 * 
 */
class exportTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'table:export {tablename : name of table} 
                                         {--field=* : requested fields of table}';

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
        if (!Schema::hasTable($this->argument('tablename'))) { 
            return $this->error('Table Doesn\'t Exist.');
        }

        // allowed fields
        $allowed = $this->option('field');        

        $file = fopen('./storage/app/exports/' . $this->argument('tablename') .'.csv', 'w');
        fputcsv($file, $allowed);
        $records = DB::table($this->argument('tablename'))->get();

        foreach ($records as $record) {
            $filtered = array_filter(
                (array) $record,
                function ($key) use ($allowed) {
                    return in_array($key, $allowed);
                },
                ARRAY_FILTER_USE_KEY
            );

            fputcsv($file, $filtered);
        }

        fclose($file);

        return $this->info('Table ' . $this->argument('tablename') . ' Has Been Exported.');            
    }
}
