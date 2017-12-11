
@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('/data',$tblId) }}">{{$tblNme}} Records</a></h2>
      <p>Browse through the records or search here.</p>
    </span>
  </div>
</div>

<!-- Search engine -->
@include('user/searchbox');

<!-- Separation -->
<hr/>

<div class="dataWrppr">
    <div class="container">

      <!-- Separation -->
      <hr/>

      <p>{{ $fileContents }}</p>

      <div class="col-xs-12 col-sm-12 col-md-12">
          <a href="{{ url('/data', [$tblId, $recId]) }}" class="btn btn-primary">Return To Record</a>
      </div>

   </div>

</div>

@endsection
