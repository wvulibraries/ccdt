<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobsController extends Controller
{
    public function __construct() {
      // Protection to make sure this only accessible to admin
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
        shell_exec('php artisan queue:retry '.$id);
      }
      return $this->failed();
    }

    public function retryAll() {
      shell_exec('php artisan queue:retry all');
      return $this->failed();
    }

    public function forget($id) {
      if (is_numeric($id)) {
        shell_exec('php artisan queue:forget '.$id);
      }
      return $this->failed();
    }

    public function flush() {
      shell_exec('php artisan queue:flush');
      return $this->failed();
    }

}
