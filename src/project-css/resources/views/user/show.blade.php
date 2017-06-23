@extends('layouts.default')


@section('content')

<!-- Search engine -->
@include('user/search');

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
