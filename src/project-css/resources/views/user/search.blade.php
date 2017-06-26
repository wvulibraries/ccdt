@extends('layouts.default')


@section('content')

<!-- Search engine -->
@include('user/searchbox');

<!-- Records -->
<hr/>

<!-- Records -->
<div class="container">
    <div class="row">
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
    </div>
</div>

@endsection
