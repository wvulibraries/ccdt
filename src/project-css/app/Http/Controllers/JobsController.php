<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jobs;
use Auth;

class JobsController extends Controller
{
    private $Jobs;

    public function __construct() {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');
      $this->Jobs = new Jobs();
    }
    /**
    * Show the collection index page
    */
    public function pending() {
        return view('admin/jobs/pending')->with('AuthUsr', Auth::user())
                                 ->with('JobCount', $this->Jobs->getPendingJobsCount())
                                 ->with('CurrentJobs', $this->Jobs->getAllPendingJobs());
    }

    public function failed() {
        return view('admin/jobs/failed')->with('AuthUsr', Auth::user())
                                 ->with('JobCount', $this->Jobs->getFailedJobsCount())
                                 ->with('FailedJobs', $this->Jobs->getAllFailedJobs());
    }

    public function retry($id) {
        $this->Jobs->retryFailedJob($id);
        return $this->failed();
    }

    public function retryAll() {
        $this->Jobs->retryAllFailedJobs();
        return $this->failed();
    }

    public function forget($id) {
        $this->Jobs->forgetFailedJob($id);
        return $this->failed();
    }

    public function flush() {
        $this->Jobs->forgetAllFailedJobs();
        return $this->failed();
    }
}
