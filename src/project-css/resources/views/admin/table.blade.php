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

<!-- Create or select option -->
<div class="collectionWrapper">
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

  </div>
</div>

<!-- Global models -->
<!-- Create Table modal -->
<div id="crteTabl" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 class="modal-title">Create Table(s)</h3>
      </div>

      <div class="modal-body">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('collection/create') }}">
            {{ csrf_field() }}

            <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">
                <label for="clctnName" class="col-md-3 control-label">Collection Name</label>

                <div class="col-md-6">
                    <input id="clctnName" type="text" class="form-control" name="clctnName" required autofocus>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        Create
                    </button>
                </div>
            </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

@endsection
