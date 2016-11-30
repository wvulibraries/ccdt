@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('collection') }}">Collection(s)</a></h2>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Create or select option -->
<div class="collectionWrapper">
  <div class="container text-center">
    <a href="#" data-toggle="modal" data-target="#crteCllctn">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="colCard">
          <div class="icon hidden-xs hidden-sm">
            <i class="glyphicon glyphicon-plus"></i>
          </div>
          <h4>Create Collection(s)</h4>
        </div>
      </div>
    </a>

    <!-- Modal -->
    <div id="crteCllctn" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Create Collection(s)</h3>
          </div>
          <div class="modal-body">

            <!-- Create collections -->
            <div class="col-xs-12 col-sm-12 col-md-12 createCollection">

              <!-- Form to create collections -->
              <div class="panel panel-default formPanel">
                  <div class="panel-heading"><h3>Create Collection(s)</h3></div>
                  <div class="panel-body">
                      <form class="form-horizontal" role="form" method="POST" action="{{ url('collection/create') }}">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">
                              <label for="clctnName" class="col-md-3 control-label">Collection Name</label>

                              <div class="col-md-6">
                                  <input id="clctnName" type="text" class="form-control" name="clctnName" value="{{ old('clctnName') }}" required autofocus>
                              </div>
                              <div class="col-md-3">
                                  <button type="submit" class="btn btn-primary">
                                      Create
                                  </button>
                              </div>
                          </div>
                      </form>
                  </div>
              </div>
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

    <!-- Select Collections -->
    <div class="col-xs-12 col-sm-12 col-md-12 selectCollection">

      <!-- Form to select collections -->
      <div class="panel panel-default formPanel">
          <div class="panel-heading"><h3>Select Collection</h3></div>
          <div class="panel-body">
              <form class="form-horizontal" role="form" method="POST" action="{{ url('/collection/select') }}">
                  {{ csrf_field() }}

                  <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">

                      <div class="col-md-8">
                        <select id="clctnName" type="text" class="form-control" name="clctnName" value="{{ old('clctnName') }}" required autofocus>
                          <option>
                            Rockefeller CSS
                          </option>
                        </select>
                      </div>
                      <div class="col-md-4">
                          <button type="submit" class="btn btn-primary">
                              Select
                          </button>
                      </div>
                  </div>
              </form>
          </div>
      </div>

    </div>

    <h3>-- or --</h3>

    <!-- Create collections -->
    <div class="col-xs-12 col-sm-12 col-md-12 createCollection">

      <!-- Form to create collections -->
      <div class="panel panel-default formPanel">
          <div class="panel-heading"><h3>Create Collection</h3></div>
          <div class="panel-body">
              <form class="form-horizontal" role="form" method="POST" action="{{ url('collection/create') }}">
                  {{ csrf_field() }}

                  <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">
                      <label for="clctnName" class="col-md-3 control-label">Collection Name</label>

                      <div class="col-md-6">
                          <input id="clctnName" type="text" class="form-control" name="clctnName" value="{{ old('clctnName') }}" required autofocus>
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary">
                              Create
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
