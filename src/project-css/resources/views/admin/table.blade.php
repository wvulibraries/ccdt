@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('table') }}">Table(s)</a></h2>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Create -->
<div class="tableWrapper">
  <div class="container">

    <!-- Head Table Card -->
    <a href="{{url('table/create')}}" data-toggle="modal">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="colHeadCard">
          <div class="icon hidden-xs hidden-sm">
            <i class="glyphicon glyphicon-plus"></i>
          </div>
          <h4>Create Table(s)</h4>
        </div>
      </div>
    </a>

    <!-- Show existing tables -->
    @foreach($tbls as $tbl)
      <!-- Check for the access -->
      @if($tbl->hasAccess)
      <!-- SHow thw tables -->
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="colCard">
            <!-- Display the collection name -->
            <div class="col-xs-6 col-sm-4 col-md-4">
              <p class="colCardName">{{$tbl->tblNme}}</p>
            </div>
            <!-- Options for the collection -->
            <div class="col-xs-6 col-sm-8 col-md-8">
              <!-- Option 1 Add tables -->
              <div class="colCardOpts">
                <a href="{{url('table/create')}}">
                  <div class="icon hidden-xs hidden-sm">
                    <i class="glyphicon glyphicon-plus"></i>
                  </div>
                  <p>Add Tables</p>
                </a>
              </div>
            </div>
          </div>
        </div>
      @endif
    @endforeach

  </div>
</div>



@endsection
