<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jobs;
use Auth;


/**
 * Jobs Controller manages the file import that loads data into the tables.
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class JobsController extends Controller
{
    public function __construct() {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');
    }

    /**
     * Return the pending jobs page
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */ 
    public function pending() {
        return view('admin/jobs/pending')->with('AuthUsr', Auth::user())
                                 ->with('JobCount', Jobs::getPendingJobsCount())
                                 ->with('CurrentJobs', Jobs::getAllPendingJobs());
    }

    /**
     * Return the failed jobs page
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */ 
    public function failed() {
        return view('admin/jobs/failed')->with('AuthUsr', Auth::user())
                                 ->with('JobCount', Jobs::getFailedJobsCount())
                                 ->with('FailedJobs', Jobs::getAllFailedJobs());
    }

    /**
     * Calls the model function to retry specific job from the table.
     *
     * @param  int  $id
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response (listing failed jobs)
     */ 
    public function retry($id) {
        Jobs::retryFailedJob($id);
        return $this->failed();
    }

    /**
     * Calls the model function to retry all failed jobs from the table.
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response (listing failed jobs)
     */ 
    public function retryAll() {
        Jobs::retryAllFailedJobs();
        return $this->failed();
    }

    /**
     * Calls the model function to clear specific job from the table.
     *
     * @param  int  $id
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response (listing failed jobs)
     */ 
    public function forget($id) {
        Jobs::forgetFailedJob($id);
        return $this->failed();
    }

    /**
     * Calls the model function to clear all failed jobs from the table.
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response (listing failed jobs)
     */ 
    public function flush() {
        Jobs::forgetAllFailedJobs();
        return $this->failed();
    }
}
