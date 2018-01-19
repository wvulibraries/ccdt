<?php

namespace App\Jobs;

use App\Http\Controllers\TableController;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class FileImport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $fltFle;

    /**
     * Create a new job instance.
     */
    public function __construct($tblNme, $fltFle)
    {
        $this->tblNme = $tblNme;
        $this->fltFle = $fltFle;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $TableController = (new TableController);
        $TableController->process($this->tblNme, $this->fltFle);
    }

}
