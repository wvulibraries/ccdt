<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
    * Show the collection index page
    */
    public function jobs() {
        // Sent the current authenticated user
        $AuthUsr = \Auth::user();
        $jobCount = \DB::table('jobs')->count();
        $jobArray = \DB::table('jobs')->get();
        // check if the user is admin
        return view('admin/jobs')->with('AuthUsr', $AuthUsr)
                                 ->with('JobCount', $jobCount)
                                 ->with('CurrentJobs', $jobArray);
    }

    public function failedjobs() {
        // Sent the current authenticated user
        $AuthUsr = \Auth::user();
        $jobCount = \DB::table('failed_jobs')->count();
        $jobArray = \DB::table('failed_jobs')->get();
        // check if the user is admin
        return view('admin/failedjobs')->with('AuthUsr', $AuthUsr)
                                 ->with('JobCount', $jobCount)
                                 ->with('FailedJobs', $jobArray);
    }
}
