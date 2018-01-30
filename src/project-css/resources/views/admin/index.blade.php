@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" name="adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('/home') }}">Dashboard</a></h1>
      <p>Your data. Your control.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Dashboard cards -->
<div class="container cardsWrapper" role="main">
  <div class="row">
    <a href="{{ url('/users') }}">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="well dashCard">
          <div class="dashCardHeading">
            <span class="glyphicon glyphicon-user visible-xs visible-sm smallIcon" aria-hidden="true"></span>
            <span class="navigation-cards"> User(s) </span>
          </div>
          <div class="icon hidden-xs hidden-sm">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
          </div>
          <div class="desc hidden-xs">
            <var>{{$usrCnt}}</var>
            <span> User(s) </span>
          </div>
        </div>
      </div>
    </a>
    <a href="{{ url('/collection') }}">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="well dashCard">
          <div class="dashCardHeading">
            <span class="glyphicon glyphicon-book visible-xs visible-sm smallIcon" aria-hidden="true"></span>
            <span class="navigation-cards"> Collection(s) </span>
          </div>
          <div class="icon hidden-xs hidden-sm">
            <span class="glyphicon glyphicon-book" aria-hidden="true"></span>
          </div>
          <div class="desc hidden-xs">
            <var>{{$cllctCnt}}</var>
            <span> Collection(s) </span>
          </div>
        </div>
      </div>
    </a>
    <a href="{{ url('table') }}">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="well dashCard">
          <div class="dashCardHeading">
            <span class="glyphicon glyphicon-duplicate visible-xs visible-sm smallIcon" aria-hidden="true"></span>
            <span class="navigation-cards"> Table(s) </span>
          </div>
          <div class="icon hidden-xs hidden-sm">
            <span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span>
          </div>
          <div class="desc hidden-xs">
            <var>{{$tblCnt}}</var>
            <span> Source </span>
          </div>
        </div>
      </div>
    </a>
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="dashCardHeading">
          <span class="glyphicon glyphicon-cog visible-xs visible-sm smallIcon" aria-hidden="true"></span>
          <span class="navigation-cards"> Admin(s) </span>
        </div>
        <div class="icon hidden-xs hidden-sm">
          <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
        </div>
        <div class="desc hidden-xs">
          <var>{{$admnCnt}}</var>
          <span> Admin(s) </span>
        </div>
      </div>
    </div>
</div>
</div>
@endsection
