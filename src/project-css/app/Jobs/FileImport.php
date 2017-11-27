<?php

namespace App\Jobs;

use App\Http\Controllers\TableController;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
     *
     * @return void
     */
    public function __construct($tblNme, $fltFle)
    {
      $this->tblNme = $tblNme;
      $this->fltFle = $fltFle;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
      $TableController = (new TableController);
      $TableController->process($this->tblNme, $this->fltFle);
    }
}
