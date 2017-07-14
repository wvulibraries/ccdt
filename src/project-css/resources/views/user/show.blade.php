@extends('layouts.default')

@section('content')

<!-- Search engine -->
@include('user/searchbox');

<!-- Separation -->
<hr/>

<div class="dataWrppr">
    <div class="container">

      <!-- Separation -->
      <hr/>

      @foreach($rcrds as $key => $rcrd)
      <div class="col-xs-12 col-sm-12 col-md-12">
          @foreach($clmnNmes as $key => $clmnNme)
            @if ((strpos($rcrd->$clmnNme, '\\') !== FALSE) && (strpos($clmnNme, 'index') == false))
              <h4><b>{{$clmnNme}}</b>: {{$rcrd->$clmnNme}}
              @php
                $tokens = explode('\\',$rcrd->$clmnNme);
                $filename = end($tokens);
              @endphp

              @foreach($fileList as $key => $item)
                @if (basename($item) == $filename)
                  <a href="{{ url('/data', ['curTable' => $tblId, 'view' => 'view', 'filename' => $filename])}}">
                    <span>View</span>
                  </a>
                @endif
              @endforeach
              </h4>
            @else
              <h4><b>{{$clmnNme}}</b>: {{$rcrd->$clmnNme}}</h4>
            @endif
          @endforeach
      </div>
      @endforeach

    </div>
</div>

@endsection
