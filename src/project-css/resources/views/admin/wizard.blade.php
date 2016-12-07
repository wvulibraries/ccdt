@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('table/create') }}">Create Table(s) Wizard</a></h2>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Wizard Form -->
<div class="container">
  <h3 class="text-center">Step1: Select or Import</h3>
  <!-- Separator -->
  <hr/>
    <div class="row">
      <!--Panel to create the table with flat file -->
        <div class="col-md-6">
            <div class="panel panel-default formPanel">
                <div class="panel-heading"><h3>Import from flat file</h3></div>
                <div class="panel-body">
                    <form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ url('/table/create') }}">
                        {{ csrf_field() }}
                        <!-- Table name -->
                        <div class="form-group{{ $errors->has('tblNme') ? ' has-error' : '' }}">
                            <label for="tblNme" class="col-md-4 control-label">Table Name</label>

                            <div class="col-md-6">
                                <input id="tblNme" type="text" class="form-control" name="tblNme" value="{{ old('tblNme') }}" required autofocus>
                            </div>
                        </div>
                        <!-- Select File -->
                        <div class="form-group{{ $errors->has('fltFile') ? ' has-error' : '' }}">
                            <label for="fltFile" class="col-md-4 control-label">Import</label>

                            <div class="col-md-6">
                                <input id="fltFile" type="file" class="form-control" name="fltFile" required autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-6">
                                <button type="submit" class="btn btn-primary">
                                    Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--Panel to create the table with flat file -->
          <div class="col-md-6">
              <div class="panel panel-default formPanel">
                  <div class="panel-heading"><h3>Select from directory</h3></div>
                  <div class="panel-body">
                      <form class="form-horizontal" enctype="multipart/form-data" role="form" method="POST" action="{{ url('/table/create') }}">
                          {{ csrf_field() }}
                          <!-- Table name -->
                          <div class="form-group{{ $errors->has('tblNme') ? ' has-error' : '' }}">
                              <label for="tblNme" class="col-md-4 control-label">Table Name</label>

                              <div class="col-md-6">
                                  <input id="tblNme" type="text" class="form-control" name="tblNme" value="{{ old('tblNme') }}" required autofocus>
                              </div>
                          </div>
                          <!-- Select File -->
                          <div class="form-group{{ $errors->has('fltFile') ? ' has-error' : '' }}">
                              <label for="fltFile" class="col-md-4 control-label">Import</label>

                              <div class="col-md-6">
                                  <input id="fltFile" type="file" class="form-control" name="fltFile" required autofocus>
                              </div>
                          </div>
                          <div class="form-group">
                              <div class="col-md-6 col-md-offset-6">
                                  <button type="submit" class="btn btn-primary">
                                      Import
                                  </button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
          </div>
    </div>
</div>

@endsection
