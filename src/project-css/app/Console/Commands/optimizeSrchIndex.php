<?php

namespace App\Console\Commands;

use App\Jobs\OptimizeSearchIndex;
use App\Adapters\OptimizeSearchAdapter;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

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
        dispatch(new OptimizeSearchIndex($this->argument('tablename')))->onQueue('high');
    }
}
