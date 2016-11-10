@extends('layouts.default')


@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h3><a href="{{ url('/home') }}">Dashboard</a></h3>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Dashboard cards -->
<div class="container cardsWrapper">
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="icon">
          <i class="glyphicon glyphicon-user"></i>
        </div>
        <div class="desc">
          <var>3</var>
          <label class="text-muted">Users</label>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="icon">
          <i class="glyphicon glyphicon-user"></i>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="icon">
          <i class="glyphicon glyphicon-user"></i>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="icon">
          <i class="glyphicon glyphicon-user"></i>
        </div>
      </div>
    </div>
</div>
@endsection
