@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('admin/failedjobs') }}">Failed Job(s)</a></h2>
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
    @endif
    @foreach($FailedJobs as $job)
      <?php echo '<pre>'; var_dump($job); echo '</pre>'; ?>
    @endforeach
  </div>
</div>

@endsection
