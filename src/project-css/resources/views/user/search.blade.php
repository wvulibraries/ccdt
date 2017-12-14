
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
              <a class="btn btn-primary left-button" href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page-1])}}">
                <span>
                  <i class="glyphicon glyphicon-chevron-left"></i>
                </span>
                <span>previous page</span>
              </a>
              @endif
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 text-right">
              @if($page != $lastPage)
              <a class="btn btn-primary pull-right" href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page+1])}}">
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
    </div>
      <!-- Separation -->
      <hr/>
      <div class="container">

      @foreach($rcrds as $key => $rcrd)
      <div class="dataCard">
        <a href="{{ url('/data', [$tblId, $rcrd->id]) }}">
            @foreach($clmnNmes as $key => $clmnNme)
              @if($key < 5)
                <span class="card-items"><b>{{$clmnNme}}</b>: {{$rcrd->$clmnNme}}</span>
              @endif
            @endforeach
        </a>
      </div>
      @endforeach

      <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="rcrdOptnsCrd-top">
          <!-- <div class="btn-group btn-group-lg"> -->
            <div class="col-xs-6 col-sm-6 col-md-6 text-left">
              @if($page != 1)
              <a class="btn btn-primary left-button" href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page-1])}}">
                <span>
                  <i class="glyphicon glyphicon-chevron-left"></i>
                </span>
                <span>previous page</span>
              </a>
              @endif
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 text-right">
              @if($page != $lastPage)
              <a class="btn btn-primary pull-right" href="{{ url('/data', ['curTable' => $tblId, 'search' => $search, 'page' => $page+1])}}">
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

    </div>
</div>
@endsection
