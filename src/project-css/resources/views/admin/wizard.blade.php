@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('table/create') }}">Create Table(s) Wizard</a></h1>
      <p>Please upload the flat file using the import option or use select if you have already copied the file.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Wizard Form -->
<div class="container" role="main">
  <h2 class="text-center">Step1: Select or Import</h2>
  <!-- Separator -->
  <hr/>
    <div class="row">
      <!--Panel to create the table with flat file -->
        <div class="col-md-6">
            <div class="panel panel-default formPanel">
                <div class="panel-heading"><a data-toggle="collapse" href="#imprtPnlBdy"><h3>Import from flat file</h3></a></div>
                <div id="imprtPnlBdy" class="panel-collapse collapse">
                  <div class="panel-body">
                      <form class="form-horizontal" name="uploadFltFile" aria-label="uploadFltFile" role="form" method="POST" enctype="multipart/form-data" action="{{ url('/table/create/import') }}">
                          {{ csrf_field() }}
                          <!-- Table name -->
                          <div class="form-group{{ $errors->has('imprtTblNme') ? ' has-error' : '' }}">
                              <label for="imprtTblNme" class="col-md-4 control-label">Table Name</label>

                              <div class="col-md-6">
                                  <input id="imprtTblNme" type="text" class="form-control" name="imprtTblNme" value="{{ old('imprtTblNme') }}" required autofocus>
                              </div>
                          </div>
                          <!-- Select Collection Name -->
                          <div class="form-group{{ $errors->has('colID') ? ' has-error' : '' }}">
                              <label for="colID" class="col-md-4 control-label">Select Collection</label>

                              <div class="col-md-6">
                                <select id="colID" type="text" class="form-control" name="colID" value="{{ old('colID') }}" required autofocus>
                                  @foreach($collcntNms as $collcntNm)
                                    @if($collcntNm->isEnabled)
                                      <option value="{{$collcntNm->id}}">{{$collcntNm->clctnName}}</option>
                                    @endif
                                  @endforeach
                                </select>
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
        <!--Panel to create the table with flat file -->
          <div class="col-md-6">
              <div class="panel panel-default formPanel{{count($fltFleList)>0 ? '' : ' dsbld' }}">
                  <div class="panel-heading"><a data-toggle="collapse" href="#slctPnlBdy"><h3>Select from directory</h3></a></div>
                  <div id="slctPnlBdy" class="panel-collapse collapse">
                    <div class="panel-body">
                        <form class="form-horizontal" name="selectFltFile" aria-label="selectFltFile" role="form" method="POST" action="{{ url('/table/create/select') }}">
                            {{ csrf_field() }}
                            <!-- Table name -->
                            <div class="form-group{{ $errors->has('slctTblNme') ? ' has-error' : '' }}">
                                <label for="slctTblNme" class="col-md-4 control-label">Table Name</label>

                                <div class="col-md-6">
                                    <input id="slctTblNme" type="text" class="form-control" name="slctTblNme" value="{{ old('slctTblNme') }}" required autofocus>
                                </div>
                            </div>
                            <!-- Select Collection Name -->
                            <div class="form-group{{ $errors->has('colID2') ? ' has-error' : '' }}">
                                <label for="colID2" class="col-md-4 control-label">Select Collection</label>

                                <div class="col-md-6">
                                  <select id="colID2" type="text" class="form-control" name="colID2" value="{{ old('colID2') }}" required autofocus>
                                    @foreach($collcntNms as $collcntNm)
                                      @if($collcntNm->isEnabled)
                                        <option value="{{$collcntNm->id}}">{{$collcntNm->clctnName}}</option>
                                      @endif
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                            <!-- Choose File -->
                            <div class="form-group{{ $errors->has('fltFile') ? ' has-error' : '' }}">
                                <label for="fltFile2" class="col-md-4 control-label">Import</label>

                                <div class="col-md-6">
                                  <select id="fltFile2" type="text" class="form-control" name="fltFile2" value="{{ old('fltFile2') }}" required autofocus>
                                    @foreach($fltFleList as $fltFile)
                                      @if($fltFile)
                                        <option value="{{$fltFile}}">{{$fltFile}}</option>
                                      @endif
                                    @endforeach
                                  </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-6">
                                    <button type="submit" class="btn btn-primary">
                                        Select
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                  </div>
              </div>
          </div>
    </div>
</div>

@endsection
