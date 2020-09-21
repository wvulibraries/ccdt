@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('collection') }}">CMS Import Wizard</a></h1>
      <p>Import CMS Collection</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Create or select option -->
<div class="collectionWrapper" role="main">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
          <div class="panel panel-default formPanel">
              <div class="panel-heading"><a data-toggle="collapse" href="#importCMSDISPanel"><h3>Import from CMS File</h3></a></div>
              <div id="importCMSDISPanel" class="panel-collapse collapse">
                <div class="panel-body">
                  <form class="form-horizontal" name="uploadCMSDISFiles" aria-label="uploadCMSDISFiles" role="form" method="POST" enctype="multipart/form-data" action="{{ url('/admin/wizard/cms/upload') }}">
                      {{ csrf_field() }}
                      <!-- Select Collection Name -->
                      <div class="form-group{{ $errors->has('colID') ? ' has-error' : '' }}">
                          <label for="colID" class="col-md-4 control-label">Select Collection</label>

                          <div class="col-md-6">
                            <select id="colID" type="text" class="form-control" name="colID" value="{{ old('colID') }}" required autofocus>
                              @foreach($collcntNms as $collcntNm)
                                @if($collcntNm->isEnabled)
                                  <option value="{{$collcntNm->id}}"
                                  @if ($collcntNm->id == $colID)
                                      selected="selected"
                                  @endif
                                  >{{$collcntNm->clctnName}}</option>
                                @endif
                              @endforeach                                 
                            </select>
                          </div>
                      </div>
                      <!-- Select File -->
                      <div class="form-group{{ $errors->has('multipleFiles') ? ' has-error' : '' }}">
                          <label for="cmsFile" class="col-md-4 control-label">Import CMS File</label>

                          <div class="col-md-6">
                              <input id="cmsFile" type="file" class="form-control" name="cmsFile" required autofocus>
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
      <div class="col-md-6">
          <div class="panel panel-default formPanel">
              <div class="panel-heading"><a data-toggle="collapse" href="#selectCMSFilePanel"><h3>Select CMS Files from directory</h3></a></div>
              <div id="selectCMSFilePanel" class="panel-collapse collapse">
                <div class="panel-body">
                  <form class="form-horizontal" name="selectCMSDISFile" aria-label="selectCMSDISFile" role="form" method="POST" action="{{ url('/admin/wizard/cms/select') }}">
                      {{ csrf_field() }}
                      <!-- Select Collection Name -->
                      <div class="form-group{{ $errors->has('colID2') ? ' has-error' : '' }}">
                          <label for="colID2" class="col-md-4 control-label">Select Collection</label>

                          <div class="col-md-6">
                            <select id="colID2" type="text" class="form-control" name="colID2" value="{{ old('colID') }}" required autofocus>
                              @foreach($collcntNms as $collcntNm)
                                @if($collcntNm->isEnabled)
                                  <option value="{{$collcntNm->id}}"
                                  @if ($collcntNm->id == $colID)
                                      selected="selected"
                                  @endif
                                  >{{$collcntNm->clctnName}}</option>
                                @endif
                              @endforeach                               
                            </select>
                          </div>
                      </div>
                      <!-- Choose File -->
                      <div class="form-group{{ $errors->has('fltFile') ? ' has-error' : '' }}">
                          <label for="fltFile2" class="col-md-4 control-label">Import</label>

                          <div class="col-md-6">
                            <select id="cmsFile2" type="text" class="form-control" name="cmsFile2" required autofocus>
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
</div>

@endsection