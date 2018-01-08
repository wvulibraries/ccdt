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

<!-- Display controls and cards here -->
<div class="usrWrapper">
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
      </tr>
      @foreach($FailedJobs as $job)
      <tr>
        <th>{{$job->id}}</th>
        <th>{{$job->connection}}</th>
        <th>{{$job->queue}}</th>
        <th>{{$job->exception}}</th>
        <th>{{$job->failed_at}}</th>
      </tr>
      @endforeach
    </table>
    @endif
  </div>
</div>


@endsection
