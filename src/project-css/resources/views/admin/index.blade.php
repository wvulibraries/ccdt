@extends('layouts.default')


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
          <label>User(s)</label>
        </div>
        <div class="icon hidden-xs hidden-sm">
          <i class="glyphicon glyphicon-user"></i>
        </div>
        <div class="desc hidden-xs">
          <var>3</var>
          <label class="text-muted">User(s)</label>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="dashCardHeading">
          <label>Collection(s)</label>
        </div>
        <div class="icon hidden-xs hidden-sm">
          <i class="glyphicon glyphicon-book"></i>
        </div>
        <div class="desc hidden-xs">
          <var>1</var>
          <label class="text-muted">Collection(s)</label>
        </div>
        <div class="cardButton">
          <a href="{{ url('/collection') }}" class="btn btn-lg btn-primary">Create Collection</a>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="dashCardHeading">
          <label>Table(s)</label>
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
    <div class="col-xs-12 col-sm-6 col-md-3">
      <div class="well dashCard">
        <div class="dashCardHeading">
          <label>Admin(s)</label>
        </div>
        <div class="icon hidden-xs hidden-sm">
          <i class="glyphicon glyphicon-cog"></i>
        </div>
        <div class="desc hidden-xs">
          <var>1</var>
          <label class="text-muted">Admin(s)</label>
        </div>
      </div>
    </div>
</div>
</div>
@endsection
