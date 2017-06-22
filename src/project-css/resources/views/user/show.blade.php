@extends('layouts.default')


@section('content')

<!-- Search engine -->
<div class="searchBarWrapper">
  <div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
          <form class="form-horizontal" role="search" method="get" action="{{ url('/data',$tblId) }}">
              {{ csrf_field() }}
              <div class="form-group{{ $errors->has('search') ? ' has-error' : '' }}">
                  <div class="col-md-10 col-sm-10 col-xs-8">
                    <!-- <label for="tblCol">Table Columns</label> -->
                    <select id="tblCol" name="tblCol" class="form-control">
                      @foreach($clmnNmes as $clmnNme)
                        <option value="{{ $clmnNme }}">{{ $clmnNme }}</option>
                      @endforeach
                    </select>
                    <!-- <label for="search">Search</label> -->
                    <input id="search" type="text" class="form-control searchBar" name="search" placeholder="Search..." required autofocus>

                    @if ($errors->has('search'))
                      <span class="fa fa-search">
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

      <!-- Separation -->
      <hr/>

      @foreach($rcrds as $key => $rcrd)
      <div class="col-xs-12 col-sm-12 col-md-12">
          @foreach($clmnNmes as $key => $clmnNme)
            <h4><b>{{$clmnNme}}</b>: {{$rcrd->$clmnNme}}</h4>
          @endforeach
      </div>
      @endforeach

    </div>
</div>

@endsection
