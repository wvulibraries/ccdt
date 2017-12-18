@extends('layouts.default')

<!-- Heading -->
@section('content')

<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('table/load') }}">Load Data</a></h2>
      <p>Additional options to ensure the data is handled just the way you want.</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Main form to load data -->
<div class="frmWrappr">
  <div class="container">
    <div class="panel-body">
      <form class="form-horizontal" role="form" method="POST" action="{{url('table/load/store')}}">
        <!-- CSRF Fields -->
        {{ csrf_field() }}
        <!-- Select Table -->
        <div class="form-group{{ $errors->has('tblNme') ? ' has-error' : '' }}">
            <label for="tblNme" class="col-md-4 control-label">Select Table:</label>

            <div class="col-md-6">
              <select id="tblNme" type="text" class="form-control" name="tblNme" value="{{ old('tblNme') }}" required autofocus>
                @foreach($tblNms as $tblNm)
                  @if($tblNm->hasAccess)
                    <option value="{{$tblNm->tblNme}}">{{$tblNm->tblNme}}</option>
                  @endif
                @endforeach
              </select>
            </div>
        </div>

        <!-- Select File -->
        <div class="form-group{{ $errors->has('fltFle') ? ' has-error' : '' }}">
            <label for="fltFle" class="col-md-4 control-label">Select File:</label>

            <div class="col-md-6">
              <select id="fltFle" type="text" class="form-control" name="fltFle" value="{{ old('fltFle') }}" required autofocus>
                @foreach($fltFleList as $fltFle)
                  @if($fltFle)
                    <option value="{{$fltFle}}">{{$fltFle}}</option>
                  @endif
                @endforeach
              </select>
            </div>
        </div>

        <!-- Submit button -->
        <div class="form-group">
            <div class="col-md-8 col-md-offset-4">
                <button type="submit" class="btn btn-primary">
                    Load Data
                </button>
            </div>

            <div class="progress-bar">
              <span class="progress-message"> Please wait while we import your file into the database</span>
              <progress value="0" max="100"> </progress>
            </div>

        </div>

      </form>
    </div>
  </div>

</div>
@endsection
