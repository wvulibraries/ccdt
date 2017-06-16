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
          <form class="form-horizontal" role="search" method="POST" action="">
              {{ csrf_field() }}
              <div class="form-group{{ $errors->has('search') ? ' has-error' : '' }}">
                  <div class="col-md-10 col-sm-10 col-xs-8">
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

        <div class="rcrdOptnsCrd-top">
          <div class="col-xs-12 col-sm-12 col-md-12">
          <!-- <div class="btn-group btn-group-lg"> -->
            <div class="col-xs-6 col-sm-6 col-md-6 text-left">
              @if($rcrds->currentPage() != 1)
              <a class="btn btn-primary left-button" href="{{$rcrds->previousPageUrl()}}">
                <span>
                  <i class="glyphicon glyphicon-chevron-left"></i>
                </span>
                <span>previous page</span>
              </a>
              @endif
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 text-right">
              @if($rcrds->currentPage() != $rcrds->lastPage())
              <a class="btn btn-primary pull-right" href="{{$rcrds->nextPageUrl()}}">
                <span>next page</span>
                <span>
                  <i class="glyphicon glyphicon-chevron-right"></i>
                </span>
              </a>
              @endif
            </div>
          <!-- </div> -->
        </div>
      </div>

      <base href="/" target="_top">
      <link rel="stylesheet" href="{{ URL::asset('css/card.css') }}" type="text/css">
      <div class="cardCont">
        @foreach($rcrds as $key => $rcrd)
          <div class="card">
            <div class="container">
              @foreach($rcrd as $field => $field_value)
                @if(strlen($field_value) > 0)<p>{{$field}} {{$field_value}}</p>@endif
              @endforeach
            </div>
          </div>
          <br>
        @endforeach
      </div>

      <div class="col-xs-12 col-sm-12 col-md-12">
        @if(1 <= $rcrds->currentPage() and $rcrds->currentPage() <= $rcrds->lastPage())
        <div class="rcrdOptnsCrd text-center">
          <ul class="pagination pagination-lg">
            @if($rcrds->currentPage() != 1)<li><a href="{{$rcrds->previousPageUrl()}}"><i class="glyphicon glyphicon-chevron-left"></i></a></li>@endif
            @if($rcrds->currentPage() != 1)<li><a href="{{$rcrds->url(1)}}">first</a></li>@endif
            @if($rcrds->currentPage()-2 >= 1)<li><a href="{{$rcrds->url($rcrds->currentPage()-2)}}">{{$rcrds->currentPage()-2}}</a></li>@endif
            <li class="active"><a href="{{$rcrds->url($rcrds->currentPage())}}">{{$rcrds->currentPage()}}</a></li>
            @if($rcrds->hasMorePages() and $rcrds->currentPage()+2 <= $rcrds->lastPage())<li><a href="{{$rcrds->url($rcrds->currentPage()+2)}}">{{$rcrds->currentPage()+2}}</a></li>@endif
            @if($rcrds->currentPage() != $rcrds->lastPage())<li><a href="{{$rcrds->url($rcrds->lastPage())}}">last</a></li>@endif
            @if($rcrds->currentPage() != $rcrds->lastPage())<li><a href="{{$rcrds->nextPageUrl()}}"><i class="glyphicon glyphicon-chevron-right"></i></a></li>@endif
          </ul>
        </div>
        @else
        <p class="text-center">Page {{$rcrds->currentPage()}} doesn't exist.</p>
        @endif
      </div>

    </div>
</div>
@endsection
