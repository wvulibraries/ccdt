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
        <input id="kCnt" type="hidden" class="form-control" name="kCnt" value="{{count($schema)}}">
        <input id="tblNme" type="hidden" class="form-control" name="tblNme" value="{{$tblNme}}">
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
                <!-- Show the columns for edit -->
                <div class="form-group{{ $errors->has('col-'.$key.'-name') ? ' has-error' : '' }}">
                  <input id="col-{{$key}}-name" type="text" class="form-control" name="col-{{$key}}-name" value="{{$col}}" required autofocus>
                </div>
              </td>
              <td>
                <!-- Show the data type to choose from -->
                <div class="form-group{{$errors->has('col-'.$key.'-data') ? ' has-error' : ''}}">
                  <select id="col-{{$key}}-data" type="text" class="form-control" name="col-{{$key}}-data" required autofocus>
                    <option value="string">String</option>
                    <option value="integer">Integer</option>
                  </select>
                </div>
              </td>
              <td>
                <!-- Show the data size to choose from -->
                <div class="form-group{{$errors->has('col-'.$key.'-size') ? ' has-error' : ''}}">
                  <select id="col-{{$key}}-size" type="text" class="form-control" name="col-{{$key}}-size" required autofocus>
                    <option value="default">Default</option>
                    <option value="medium">Medium</option>
                    <option value="big">Big</option>
                  </select>
                </div>
              </td>
            </tr>
          @endforeach
          <tr>
            <!-- Submit for the schema -->
            <td colspan=4>
              <button type="submit" class="btn btn-primary">
                Submit
              </button>
            </td>
          </tr>
        </tbody>
      </form>
    </table>
  </div>
</div>

@endsection
