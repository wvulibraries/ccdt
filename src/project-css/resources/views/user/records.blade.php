@extends('layouts.default')


@section('content')

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

<!-- Records -->
<hr/>

<!-- Records -->
<div class="container">
    <div class="row">
      Records
    </div>
</div>

@endsection
