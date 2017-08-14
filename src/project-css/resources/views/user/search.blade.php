
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
@include('user/searchbox');

<!-- Separation -->
<hr/>

<div class="dataWrppr">
    <div class="container">

      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="rcrdOptnsCrd-top">
          <!-- <div class="btn-group btn-group-lg"> -->
            <div class="col-xs-6 col-sm-6 col-md-6 text-left">
              @if($page != 1)
              <a class="btn btn-primary left-button" href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page-1, 'driver' => $driver, 'column' => $column, 'cache' => $cache])}}">
                <span>
                  <i class="glyphicon glyphicon-chevron-left"></i>
                </span>
                <span>previous page</span>
              </a>
              @endif
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 text-right">
              @if($page != $lastPage)
              <a class="btn btn-primary pull-right" href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page+1, 'driver' => $driver, 'column' => $column, 'cache' => $cache])}}">
                <span>next page</span>
                <span>
                  <span class="glyphicon glyphicon-chevron-right"></span>
                </span>
              </a>
              @endif
            </div>
          <!-- </div> -->
        </div>
      </div>

      <!-- Separation -->
      <hr/>

      @foreach($rcrds as $key => $rcrd)
      <div class="col-xs-12 col-sm-4 col-md-4">
        <a href="{{ url('/data', [$tblId, $rcrd->id]) }}">
          <div class="dataCard">
              @foreach($clmnNmes as $key => $clmnNme)
                @if($key < 5)
                  <h4><b>{{$clmnNme}}</b>: {{$rcrd->$clmnNme}}</h4>
                @endif
              @endforeach
          </div>
        </a>
      </div>
      @endforeach

      <div class="col-xs-12 col-sm-12 col-md-12">
        @if(1 <= $page and $page <= $lastPage)
        <div class="rcrdOptnsCrd text-center">
          <ul class="pagination pagination-lg">
            @if($page != 1)<li><a href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page-1])}}"><i class="glyphicon glyphicon-chevron-left"></i></a></li>@endif
            @if($page != 1)<li><a href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => 1])}}">first</a></li>@endif
            @if($page-2 >= 1)<li><a href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page-2])}}">{{$page-2}}</a></li>@endif
            <li class="active"><a href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page])}}">{{$page}}</a></li>
            @if($morepages and $page+2 <= $lastPage)<li><a href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page+2])}}">{{$page+2}}</a></li>@endif
            @if($page != $lastPage)<li><a href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $lastPage])}}">last</a></li>@endif
            @if($page != $lastPage)<li><a href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page+1])}}"><i class="glyphicon glyphicon-chevron-right"></i></a></li>@endif
          </ul>
        </div>
        @else
        <p class="text-center">Page {{$page}} doesn't exist.</p>
        @endif
      </div>

    </div>
</div>
@endsection
