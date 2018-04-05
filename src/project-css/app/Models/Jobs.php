<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Jobs extends Model
{
    public function getPendingJobsCount() {
        return DB::table('jobs')->count();
    }

    public function getAllPendingJobs() {
        return DB::table('jobs')->get();
    }

    public function getFailedJobsCount() {
        return DB::table('failed_jobs')->count();
    }

    public function getAllFailedJobs() {
        return DB::table('failed_jobs')->get();
    }

    public function retryFailedJob($id) {
      if (is_numeric($id)) {
        $queueRetry = \Artisan::call('queue:retry', ['id' => [$id]]);
        \Log::info($queueRetry);
      }
    }

    public function retryAllFailedJobs() {
      $jobArray = $this->getAllFailedJobs();
      foreach ($jobArray as $job) {
        $this->retryFailedJob($job->id);
      }
    }

    public function forgetFailedJob($id) {
      if (is_numeric($id)) {
        $queueForget = \Artisan::call('queue:forget', ['id' => [$id]]);
        \Log::info($queueForget);
      }
    }

    public function forgetAllFailedJobs() {
      $queueFlush = \Artisan::call('queue:flush');
      \Log::info($queueFlush);
    }

}
