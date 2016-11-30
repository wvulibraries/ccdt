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
  <div class="container">

    <!-- Head Coolection Card -->
    <a href="#" data-toggle="modal" data-target="#crteCllctn">
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="colHeadCard">
          <div class="icon hidden-xs hidden-sm">
            <i class="glyphicon glyphicon-plus"></i>
          </div>
          <h4>Create Collection(s)</h4>
        </div>
      </div>
    </a>

    <!-- Iterate to show the existing collection -->
    @foreach($collcntNms as $collcntNm)
      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="colCard">
          <div class="col-xs-2 col-sm-2 col-md-2">
            <span class="colCardId">{{$collcntNm->id}}</span>
          </div>
          <div class="col-xs-6 col-sm-6 col-md-6">
            <span class="colCardName">{{$collcntNm->clctnName}}</span>
          </div>
          <div class="col-xs-4 col-sm-4 col-md-4">
            <span class="colCardPerm">
              @if($collcntNm->hasAccess)
              <div class="icon hidden-xs hidden-sm">
                <i class="glyphicon glyphicon-eye-close"></i>
              </div>
              <span>Restrict Access</span>
              @else
              <div class="icon hidden-xs hidden-sm">
                <i class="glyphicon glyphicon-eye-open"></i>
              </div>
              <span>Allow Access</span>
              @endif
            </span>
          </div>
        </div>
      </div>
    @endforeach

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

          <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

@endsection
