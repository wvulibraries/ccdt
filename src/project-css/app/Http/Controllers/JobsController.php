<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function __construct() {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');
    }
    /**
    * Show the collection index page
    */
    public function pending() {
        // Sent the current authenticated user
        $AuthUsr = \Auth::user();
        $jobCount = \DB::table('jobs')->count();
        $jobArray = \DB::table('jobs')->get();
        // check if the user is admin
        return view('admin/jobs/pending')->with('AuthUsr', $AuthUsr)
                                 ->with('JobCount', $jobCount)
                                 ->with('CurrentJobs', $jobArray);
    }

    public function failed() {
        // Sent the current authenticated user
        $AuthUsr = \Auth::user();
        $jobCount = \DB::table('failed_jobs')->count();
        $jobArray = \DB::table('failed_jobs')->get();
        // check if the user is admin
        return view('admin/jobs/failed')->with('AuthUsr', $AuthUsr)
                                 ->with('JobCount', $jobCount)
                                 ->with('FailedJobs', $jobArray);
    }

    public function retry($id) {
        if (is_numeric($id)) {
          $queueRetry = \Artisan::call('queue:retry', ['id' => [$id]]);
          \Log::info($queueRetry);
        }
        return $this->failed();
    }

    public function retryAll() {
        $jobArray = \DB::table('failed_jobs')->get();
        foreach ($jobArray as $job) {
          if (is_numeric($job->id)) {
            $queueRetry = \Artisan::call('queue:retry', ['id' => [$job->id]]);
            \Log::info($queueRetry);
          }
        }
        return $this->failed();
    }

    public function forget($id) {
        if (is_numeric($id)) {
          $queueForget = \Artisan::call('queue:forget', ['id' => [$id]]);
          \Log::info($queueForget);
        }
        return $this->failed();
    }

    public function flush() {
        $queueFlush = \Artisan::call('queue:flush');
        \Log::info($queueFlush);
        return $this->failed();
    }

}
