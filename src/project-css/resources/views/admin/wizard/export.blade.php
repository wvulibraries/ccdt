@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" name="adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('/admin/wizard/export') }}">Export Tables and Collections</a></h1>
      <p>Your data. Your control.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Dashboard cards -->
<div class="container cardsWrapper" role="main">
  <div class="row">

    <a href="{{ url('admin/wizard/export/table') }}">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="well dashCard">
          <div class="dashCardHeading">
            <span class="glyphicon glyphicon-duplicate visible-xs visible-sm smallIcon" aria-hidden="true"></span>
            <span class="navigation-cards"> Table Export </span>
          </div>
          <div class="icon hidden-xs hidden-sm">
            <span class="glyphicon glyphicon-import" aria-hidden="true"></span>
          </div>
        </div>
      </div>
    </a>

    <a href="{{ url('admin/wizard/export/collection') }}">
      <div class="col-xs-12 col-sm-6 col-md-3">
        <div class="well dashCard">
          <div class="dashCardHeading">
            <span class="glyphicon glyphicon-duplicate visible-xs visible-sm smallIcon" aria-hidden="true"></span>
            <span class="navigation-cards"> Collection Export </span>
          </div>
          <div class="icon hidden-xs hidden-sm">
            <span class="glyphicon glyphicon-import" aria-hidden="true"></span>
          </div>
        </div>
      </div>
    </a>

  </div>
</div>
@endsection
