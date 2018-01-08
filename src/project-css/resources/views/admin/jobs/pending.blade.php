@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('admin/jobs/pending') }}">Job(s)</a></h2>
      <p>View pending imports.</p>
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
        <p>No Pending Job(s).</p>
       </span>
      @else
        <table class="table">
          <tr>
            <th>id</th>
            <th>queue</th>
            <th>attempts</th>
            <th>reserved_at</th>
            <th>available_at</th>
            <th>created_at</th>
          </tr>
        @foreach($CurrentJobs as $job)
          <tr>
            <th>{{$job->id}}</th>
            <th>{{$job->queue}}</th>
            <th>{{$job->attempts}}</th>
            <th>{{$job->reserved_at}}</th>
            <th>{{$job->available_at}}</th>
            <th>{{$job->created_at}}</th>
          </tr>
        @endforeach
        </table>
      @endif
  </div>
</div>

@endsection
