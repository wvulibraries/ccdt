<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\exportTable::class,
        Commands\importTable::class,
        Commands\dropTable::class,
        Commands\renameTable::class,
        Commands\truncateTable::class,
        Commands\createCollection::class,
        Commands\renameCollection::class, 
        Commands\enableCollection::class, 
        Commands\disableCollection::class,                        
        Commands\deleteCollection::class,
        Commands\unsetCmsCollection::class,
        Commands\setCmsCollection::class,
        Commands\createSrchIndex::class,
    ];

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
