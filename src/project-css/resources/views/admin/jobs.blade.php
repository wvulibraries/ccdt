@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('admin/jobs') }}">Job(s)</a></h2>
      <p>Manage queued jobs here.</p>
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
      @endif
      @foreach($CurrentJobs as $job)
        <?php echo '<pre>'; var_dump($job); echo '</pre>'; ?>
      @endforeach
  </div>
</div>

@endsection
