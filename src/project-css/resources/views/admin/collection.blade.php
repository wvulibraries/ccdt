@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h1><a href="{{ url('collection') }}">Collection(s)</a></h1>
      <p>Create, import and manage collections here.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Create or select option -->
<div class="collectionWrapper">
  <div class="container">

    <!-- Head Collection Card -->
    <a href="#" data-toggle="modal" data-target="#crteCllctn">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="colHeadCard">
          <div class="icon hidden-xs hidden-sm">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
          </div>
          <span class="title">Create Collection(s)</span>
        </div>
      </div>
    </a>

    @foreach($collcntNms as $collcntNm)
      <!-- Show the currently enabled cololections -->
      @if($collcntNm->isEnabled)
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="colCard">
          <!-- Display the collection name -->
          <div class="col-xs-6 col-sm-4 col-md-4">
            <p class="colCardName">{{$collcntNm->clctnName}}</p>
          </div>
          <!-- Options for the collection -->
          <div class="col-xs-6 col-sm-8 col-md-8">
            <!-- Option 1 Add tables -->
            <div class="colCardOpts">
              <a href="{{url('table/create')}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                </div>
                <p>Add Tables</p>
              </a>
            </div>
            <!-- Option 2 Edit Collection -->
            <div class="colCardOpts">
              <a href="#" data-toggle="modal" data-target="#editCllctn{{$collcntNm->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                </div>
                <p>Edit</p>
              </a>
            </div>
            <!-- Option 3 Disable Collection -->
            <div class="colCardOpts">
              <a href="#" data-toggle="modal" data-target="#dsbleCllctn{{$collcntNm->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                </div>
                <p>Disable</p>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Modals -->

      <!-- Edit Collection modal -->
      <div id="editCllctn{{$collcntNm->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <span class="modal-title">Edit Collection</span>
            </div>

            <div class="modal-body">
              <form class="form-horizontal" role="form" method="POST" action="{{ url('collection/edit') }}">
                  {{ csrf_field() }}

                  <input id="id" name="id" type="hidden" value="{{$collcntNm->id}}" />

                  <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">
                      <span for="clctnName" class="col-md-3 control-label">Collection Name</span>

                      <div class="col-md-6">
                          <input id="clctnName" type="text" class="form-control" name="clctnName" value="{{$collcntNm->clctnName}}" required autofocus>
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary">
                              Save
                          </button>
                      </div>
                  </div>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      <!-- Disable Collection -->
      <div id="dsbleCllctn{{$collcntNm->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <span class="modal-title">Disable Collection</span>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to disable <b>{{$collcntNm->clctnName}}</b> collection? All the tables associated with this collection will be disabled as well. Please enter collection name below to confirm.
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('collection/disable') }}">
                  {{ csrf_field() }}
                  <label for="id"> Collection ID </label>
                  <input id="id" name="id" type="hidden" value="{{$collcntNm->id}}" />

                  <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">

                      <div class="col-md-6">
                          <label for="clctnName"> Collection Name </label>
                          <input id="clctnName" type="text" class="form-control" name="clctnName" required autofocus>
                      </div>
                      <div class="col-md-3">
                          <button type="submit" class="btn btn-primary">
                              Confirm
                          </button>
                      </div>
                  </div>
              </form>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      @endif
    @endforeach

    <!-- Disabled collections are shown here -->
    <!-- Iterate to show the existing collection -->
    @foreach($collcntNms as $collcntNm)
      <!-- Show the currently enabled cololections -->
      @if(!($collcntNm->isEnabled))
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="dsbldColCard">
          <!-- Display the collection name -->
          <div class="col-xs-6 col-sm-4 col-md-4">
            <p class="colCardName">{{$collcntNm->clctnName}}</p>
          </div>
          <!-- Options for the collection -->
          <div class="col-xs-6 col-sm-8 col-md-8">
            <!-- Option 1 Enable Collection -->
            <div class="colCardOpts">
              <a href="#" data-toggle="modal" data-target="#enblCllctn{{$collcntNm->id}}">
                <div class="icon hidden-xs hidden-sm">
                  <span class="glyphicon glyphicon-fire"></span>
                </div>
                <p>Enable</p>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Modals -->
      <!-- Enable Collection -->
      <div id="enblCllctn{{$collcntNm->id}}" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <span class="modal-title">Enable Collection</span>
            </div>

            <div class="modal-body">
              <p>
                Are you sure you want to enable <b>{{$collcntNm->clctnName}}</b> collection?
              </p>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('collection/enable') }}">
                  {{ csrf_field() }}
                  <label for="id" class="col-md-3 control-label">Collection ID</label>
                  <input id="id" name="id" type="hidden" value="{{$collcntNm->id}}" />

                  <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">
                    <div class="modal-footer">
                      <div class="col-md-offset-8 col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Confirm
                            </button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">
                              Close
                            </button>
                        </div>
                    </div>
                  </div>
              </form>
            </div>
          </div>

        </div>
      </div>
      @endif
    @endforeach

    <!-- Create Collection modal -->
    <div id="crteCllctn" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <span class="modal-title">Create Collection(s)</span>
          </div>

          <div class="modal-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('collection/create') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('clctnName') ? ' has-error' : '' }}">
                    <label for="clctnName" class="col-md-3 control-label">Collection Name</label>

                    <div class="col-md-6">
                        <input id="clctnName" type="text" class="form-control" name="clctnName" required autofocus>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            Create
                        </button>
                    </div>
                </div>
            </form>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

@endsection
