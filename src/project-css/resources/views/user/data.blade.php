@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('/data',$tblId) }}">{{$tblNme}} Records</a></h2>
      <p>Browse through the records or search here.</p>
    </span>
  </div>
</div>

<!-- Search engine -->
<div class="searchBarWrapper">
  <div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
          <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
              {{ csrf_field() }}

              <div class="form-group{{ $errors->has('search') ? ' has-error' : '' }}">
                  <div class="col-md-10 col-sm-10 col-xs-8">
                      <input id="search" type="search" class="form-control searchBar" name="search" value="{{ old('search') }}" required autofocus>

                      @if ($errors->has('search'))
                          <span class="help-block">
                              <strong>{{ $errors->first('search') }}</strong>
                          </span>
                      @endif
                  </div>

                  <div class="col-md-2 col-sm-2 col-xs-4">
                      <button type="submit searchButton" class="btn btn-primary">
                          Search
                      </button>
                  </div>
              </div>
          </form>
        </div>
    </div>
  </div>
</div>

<!-- Separation -->
<hr/>

<div class="dataWrppr">
    <div class="container">

      @foreach($rcrds as $key => $rcrd)
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="rcrdCard">

            <div class="col-xs-8 col-sm-8 col-md-8">
              {{$rcrd->first}}
            </div>

            <div class="col-xs-2 col-sm-2 col-md-2">
              {{$rcrd->id}}
            </div>
          </div>
        </div>
      @endforeach

    </div>
</div>
@endsection
