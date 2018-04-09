<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Jobs extends Model
{
    public static function getPendingJobsCount() {
        return DB::table('jobs')->count();
    }

    public static function getAllPendingJobs() {
        return DB::table('jobs')->get();
    }

    public static function getFailedJobsCount() {
        return DB::table('failed_jobs')->count();
    }

    public static function getAllFailedJobs() {
        return DB::table('failed_jobs')->get();
    }

    public static function retryFailedJob($id) {
      if (is_numeric($id)) {
        $queueRetry = \Artisan::call('queue:retry', ['id' => [$id]]);
        \Log::info($queueRetry);
      }
    }

    public static function retryAllFailedJobs() {
      $jobArray = Jobs::getAllFailedJobs();
      foreach ($jobArray as $job) {
        Jobs::retryFailedJob($job->id);
      }
    }

    public static function forgetFailedJob($id) {
      if (is_numeric($id)) {
        $queueForget = \Artisan::call('queue:forget', ['id' => [$id]]);
        \Log::info($queueForget);
      }
    }

    public static function forgetAllFailedJobs() {
      $queueFlush = \Artisan::call('queue:flush');
      \Log::info($queueFlush);
    }

}
