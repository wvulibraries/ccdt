@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('admin/wizard/export/table') }}">Export Table Wizard</a></h1>
      <p>Export Table</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Create or select option -->
<div class="collectionWrapper" role="main">
  <div class="container">

  <h2 class="text-center">Step1: Select or Import</h2>
  <!-- Separator -->
  <hr/>

  <div class="row">

  </div>
</div>

@endsection

