@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('/home') }}">Dashboard</a></h2>
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
        <div class="dashCardHeading">
          <span class="glyphicon glyphicon-user visible-xs visible-sm smallIcon"></span><label>User(s)</label>
        </div>
        <div class="icon hidden-xs hidden-sm">
          <i class="glyphicon glyphicon-user"></i>
        </div>
        <div class="desc hidden-xs">
          <var>{{$usrCnt}}</var>
          <label class="text-muted">User(s)</label>
        </div>
      </div>
    </div>
    <a href="{{ url('/collection') }}">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="well dashCard">
          <div class="dashCardHeading">
            <span class="glyphicon glyphicon-book visible-xs visible-sm smallIcon"></span><label>Collection(s)</label>
          </div>
          <div class="icon hidden-xs hidden-sm">
            <i class="glyphicon glyphicon-book"></i>
          </div>
          <div class="desc hidden-xs">
            <var>{{$cllctCnt}}</var>
            <label class="text-muted">Collection(s)</label>
          </div>
        </div>
      </div>
    </a>
    <a href="{{ url('table') }}">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="well dashCard">
          <div class="dashCardHeading">
            <span class="glyphicon glyphicon-duplicate visible-xs visible-sm smallIcon"></span><label>Table(s)</label>
          </div>
          <div class="icon hidden-xs hidden-sm">
            <i class="glyphicon glyphicon-duplicate"></i>
          </div>
          <div class="desc hidden-xs">
            <var>1</var>
            <label class="text-muted">Table(s)</label>
          </div>
        </div>
      </div>
    </a>
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="dashCardHeading">
          <span class="glyphicon glyphicon-cog visible-xs visible-sm smallIcon"></span><label>Admin(s)</label>
        </div>
        <div class="icon hidden-xs hidden-sm">
          <i class="glyphicon glyphicon-cog"></i>
        </div>
        <div class="desc hidden-xs">
          <var>{{$admnCnt}}</var>
          <label class="text-muted">Admin(s)</label>
        </div>
      </div>
    </div>
</div>
</div>
@endsection
