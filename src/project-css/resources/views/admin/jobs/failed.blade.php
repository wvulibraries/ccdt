@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('admin/jobs/failed') }}">Failed Job(s)</a></h2>
      <p>Manage failed jobs here.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<div class="jobsWrapper">
  <div class="container">
    @if($JobCount == 0 )
     <span class="text-center">
      <p>No Failed Job(s).</p>
     </span>
    @else
      <table class="table">
        <tr>
          <th>id</th>
          <th>connection</th>
          <th>queue</th>
          <th>exception</th>
          <th>failed_at</th>
          <th></th>
          <th></th>
        </tr>
        @foreach($FailedJobs as $job)
        <tr>
          <th>{{$job->id}}</th>
          <th>{{$job->connection}}</th>
          <th>{{$job->queue}}</th>
          <th>{{ str_limit($job->exception, $limit = 100, $end = '...') }}</th>
          {{-- -18000 is the offset for EST time zone --}}
          <th><?php if ($job->failed_at > 0) { echo $job->failed_at; }?></th>
          <th><a href="{{ url('admin/jobs/retry', [$job->id]) }}" class="btn btn-primary">Retry Job</a></th>
          <th><a href="{{ url('admin/jobs/forget', [$job->id]) }}" class="btn btn-primary">Delete Job</a></th>
        </tr>
        @endforeach
      </table>
      <a href="{{ url('admin/jobs/retryall') }}" class="btn btn-primary">Retry All Job(s)</a>
      <a href="{{ url('admin/jobs/flush') }}" class="btn btn-primary">Remove All Job(s)</a>
    @endif
  </div>
</div>


@endsection
