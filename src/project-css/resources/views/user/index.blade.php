@extends('layouts.default')


@section('content')
<div class="container search">
    <div class="row">
        <div class="col-md-12">
          <form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
              {{ csrf_field() }}

              <div class="form-group{{ $errors->has('search') ? ' has-error' : '' }}">
                  <div class="col-md-10">
                      <input id="search" type="search" class="form-control" name="search" value="{{ old('search') }}" required autofocus>

                      @if ($errors->has('search'))
                          <span class="help-block">
                              <strong>{{ $errors->first('search') }}</strong>
                          </span>
                      @endif
                  </div>

                  <div class="col-md-2">
                      <button type="submit" class="btn btn-primary">
                          Search
                      </button>
                  </div>
              </div>
          </form>
        </div>
    </div>
</div>
@endsection
