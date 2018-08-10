@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('table/schema') }}">CMS Data Interchange Standard</a></h1>
      <p>CMS Data Interchange Files detected</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Form to show the schema -->
<div class="container schemaWrapper" role="main">
  <div class="table-responsive">
    <table id="table" name="table" class="table" role="table">
      <caption class="hidden">Record Types</caption>
      <!-- Form to submit the final schema -->
      <form class="form-horizontal" role="form" method="POST" action="{{url('table/create/finalize')}}">
        <!-- CSRF Fields -->
        {{ csrf_field() }}
        <!-- Form group for the column name -->

        <label class="hidden" for="collctnId"> Collection ID </label>
        <input id="collctnId" type="hidden" class="form-control" name="collctnId" value="{{$collctnId}}">

        <!-- Heading for the table -->
        <thead>
          <tr>
            <th>#</th>
            <th>Column Count</th>
            <th>Record Type</th>
          </tr>
        </thead>

        <!-- Column data from the schema -->
        <tbody>
          @foreach($cmsFileList as $key => $table)
            <tr>
              <th>{{ $table }}</th>
              <th>{{ count($schemaList[$key]) }}</th>
              <th>{{ $schemaList[$key][0] }}</th>
            </tr>
          @endforeach
        </tbody>
      </form>
    </table>

  </div>
</div>

@endsection
