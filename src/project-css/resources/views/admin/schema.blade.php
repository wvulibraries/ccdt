@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('table/schema') }}">Edit Schema</a></h2>
      <p>Please create the schema that fits your database schema. Usually Correspondence data are included with a readme file which can be used to create the schema</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Form to show the schema -->
<div class="container schemaWrapper">
  <div class="table-responsive">
    <table class="table">
      <!-- Form to submit the final schema -->
      <form class="form-horizontal" role="form" method="POST" action="{{url('table/create/finalize')}}">
        <!-- CSRF Fields -->
        {{ csrf_field() }}
        <!-- Form group for the column name -->
        <!-- Heading for the table -->
        <thead>
          <tr>
            <th>#</th>
            <th>Column Name</th>
            <th>Data Type</th>
            <th>Size</th>
          </tr>
        </thead>
        <!-- Column data from the schema -->
        <tbody>
          @foreach($schema as $key => $col)
            <tr>
              <td><p>{{$key+1}}</p></td>
              <td>
                <div class="form-group{{ $errors->has('col-'.$key.'-name') ? ' has-error' : '' }}">
                  <input id="col-{{$key}}-name" type="text" class="form-control" name="col-{{$key}}-name" value="{{$col}}" required autofocus>
                </div>
              </td>
              <td><p>{{$col}}</p></td>
              <td><p>{{$col}}</p></td>
            </tr>
          @endforeach
        </tbody>
      </form>
    </table>
  </div>
</div>

@endsection
