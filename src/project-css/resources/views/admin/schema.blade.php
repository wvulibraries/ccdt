@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('table/schema') }}">Edit Schema</a></h1>
      <p>Please create the schema that fits your database schema. Usually Correspondence data are included with a readme file which can be used to create the schema</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Form to show the schema -->
<div class="container schemaWrapper" role="main">
  <div class="table-responsive">
    <table id="table" name="table" class="table" role="table">
      <caption class="hidden">Schema Table</caption>
      <!-- Form to submit the final schema -->
      <form class="form-horizontal" role="form" method="POST" action="{{url('table/create/finalize')}}">
        <!-- CSRF Fields -->
        {{ csrf_field() }}
        <!-- Form group for the column name -->
        <label class="hidden" for="kCnt"> Collection Count </label>
        <input id="kCnt" type="hidden" class="form-control" name="kCnt" value="{{count($schema)}}">
        <label class="hidden" for="tblNme"> Table Name </label>
        <input id="tblNme" type="hidden" class="form-control" name="tblNme" value="{{$tblNme}}">
        <label class="hidden" for="collctnId"> Collection ID </label>
        <input id="collctnId" type="hidden" class="form-control" name="collctnId" value="{{$collctnId}}">
        <label class="hidden" for="fltFile"> FlatFile </label>
        <input id="fltFile" type="hidden" class="form-control" name="fltFile" value="{{$fltFile}}">
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
                  <input id="col-{{$key}}-name" aria-label="{{$col}}" type="text" class="form-control" name="col-{{$key}}-name" value="{{$col}}" required autofocus>
                </div>
              </td>
              <td>
                <!-- Show the data type to choose from -->
                <div class="form-group{{$errors->has('col-'.$key.'-data') ? ' has-error' : ''}}">
                  <select id="col-{{$key}}-data" aria-label="{{$col}} Data Type" type="text" class="form-control" name="col-{{$key}}-data" required autofocus>
                    <option value="integer">Integer</option>
                    <option selected="selected" value="string">String</option>
                    <option value="text">Text</option>
                  </select>
                </div>
              </td>
              <td>
                <!-- Show the data size to choose from -->
                <div class="form-group{{$errors->has('col-'.$key.'-size') ? ' has-error' : ''}}">
                  <select id="col-{{$key}}-size" aria-label="{{$col}} Data Size" type="text" class="form-control" name="col-{{$key}}-size" required autofocus>
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
              <button type="button" data-toggle="modal" data-target="#crteTbl" class="btn btn-primary">
                Submit
              </button>

              <!-- Create Table Modal -->
              <div id="crteTbl" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content-->
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <h3 class="modal-title">Confirm Schema</h3>
                    </div>

                    <div class="modal-body">
                      <p class="text-info">
                        Modifying schema in future is a tedious process.
                        Please make sure you have reviewed the schema and data options.
                      </p>
                    </div>

                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Confirm</button>
                      <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                  </div>

                </div>
              </div>

            </td>
          </tr>
        </tbody>
      </form>
    </table>
  </div>
</div>

@endsection
