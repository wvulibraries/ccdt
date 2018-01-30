@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" name="adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('/upload', $tblId) }}">Upload Linked File(s)</a></h1>
      <p>Please upload files that will be linked to this table.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Upload Form -->
<div class="container" role="main">
  
    @include('admin/messages')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="panel panel-default formPanel">
                <div class="panel-heading"><h2>Upload files to {{$tblNme}} Table</h2></div>
                  <div class="panel-body">
                      <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{ url('/upload', ['curTable' => $tblId]) }}">
                          {{ csrf_field() }}
                          <!-- upload folder name -->
                          <div class="form-group{{ $errors->has('upFldNme') ? ' has-error' : '' }}">
                              <label for="upFldNme" class="col-md-4 control-label">Upload Folder Name</label>

                              <div class="col-md-6">
                                  <input id="upFldNme" type="text" class="form-control" name="upFldNme" value="{{ old('upFldNme') }}" required autofocus>
                              </div>
                          </div>

                          <!-- Select Files -->
                          <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                              <label for="file" class="col-md-4 control-label">Upload</label>
                              <div class="col-md-6">
                                  <input id="file" type="file" class="form-control" name="attachments[]" multiple/ required autofocus>
                              </div>
                          </div>
                          <div class="form-group">
                              <div class="col-md-6 col-md-offset-6">
                                  <button type="submit" class="btn btn-primary">
                                      Upload
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
