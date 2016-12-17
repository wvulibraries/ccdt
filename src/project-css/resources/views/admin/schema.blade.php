@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('table/schema') }}">Edit Schema</a></h2>
      <p>Please create the schema that fits your database schema.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>


@endsection
