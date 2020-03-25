@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('table') }}">Table(s)</a></h1>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Create -->
<div class="tableWrapper">
  <div class="container" role="main">

    @include('admin/messages')

    <!-- Head Table Cards -->
    <div class="row">
      <a href="{{url('table/create')}}" data-toggle="modal" class="col-xs-6 col-sm-6 col-md-6">
          <div class="well text-center">
            <div class="icon hidden-xs hidden-sm">
              <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            </div>
            <span class="title">Create Table(s)</span>
          </div>
      </a>

      <a href="{{url('table/load')}}" data-toggle="modal" class="col-xs-6 col-sm-6 col-md-6">
          <div class="well text-center">
            <div class="icon hidden-xs hidden-sm">
              <span class="glyphicon glyphicon-import" aria-hidden="true"></span>
            </div>
            <span class="title">Load Data</span>
          </div>
      </a>
    </div>

    <!-- Show existing tables -->
    @foreach($tbls as $key => $tbl)
      <!-- Check for the access -->
      @if($tbl->hasAccess)
      <!-- Show the tables -->
      <div class="row table-list" id="{{$tbl->tblNme}}">
        <div class="table-item col-xs-12 col-sm-8 col-md-8 well">
            <p>
              <strong>{{$key+1}}.</strong>
              <strong>{{$tbl->tblNme}}</strong> belongs to
              <strong>{{$tbl->collection->clctnName}}</strong>
            </p>
        </div>

        <a href="{{ url('/table/edit',['tableId' => $tbl->id]) }}" class="table-item col-xs-12 col-sm-2 col-md-2 well dashCard">
          <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
          <div><br/> Edit Table</div>
        </a>  

        <a href="{{ url('/data',['tableId' => $tbl->id]) }}" class="table-item col-xs-12 col-sm-2 col-md-2 well dashCard">
          <span class="glyphicon glyphicon-cd" aria-hidden="true"></span>
          <div>{{DB::table($tbl->tblNme)->count()}} </div>
          <div> Records </div>
        </a>
      </div>
      @endif
    @endforeach

  </div>
</div>


@endsection
