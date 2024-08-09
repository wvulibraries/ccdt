<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Jobs model is used to view, restart and remove jobs.
 * 
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class Jobs extends Model
{
    /**
     * query jobs table returning current job count
     * 
     * @return      integer
     */     
    public static function getPendingJobsCount() {
        return DB::table('jobs')->count();
    }

    /**
     * query jobs table returning current jobs 
     * 
     * @return      array
     */       
    public static function getAllPendingJobs() {
        return DB::table('jobs')->get();
    }

    /**
     * query failed_jobs table returning current failed_jobs count
     * 
     * @return      integer
     */      
    public static function getFailedJobsCount() {
        return DB::table('failed_jobs')->count();
    }

    /**
     * query failed_jobs table returning failed_jobs 
     * 
     * @return      array
     */    
    public static function getAllFailedJobs() {
        return DB::table('failed_jobs')->get();
    }

     /**
     * restarts failed job
     * 
     * @param       integer $id Input integer
     */         
    public static function retryFailedJob($id) {
      if (is_numeric($id)) {
        $queueRetry = \Artisan::call('queue:retry', ['id' => [$id]]);
        \Log::info($queueRetry);
      }
    }

     /**
     * restarts all failed jobs
     * 
     */          
    public static function retryAllFailedJobs() {
      $jobArray = Jobs::getAllFailedJobs();
      foreach ($jobArray as $job) {
        Jobs::retryFailedJob($job->id);
      }
    }

     /**
     * remove failed job from table
     * 
     * @param       integer $id Input integer
     */      
    public static function forgetFailedJob($id) {
      if (is_numeric($id)) {
        $queueForget = \Artisan::call('queue:forget', ['id' => [$id]]);
        \Log::info($queueForget);
      }
    }

     /**
     * remove all failed jobs from table
     */       
    public static function forgetAllFailedJobs() {
      $queueFlush = \Artisan::call('queue:flush');
      \Log::info($queueFlush);
    }

}
