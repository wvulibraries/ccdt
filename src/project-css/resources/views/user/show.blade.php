@extends('layouts.default')

@section('content')

<!-- Search engine -->
@include('user/searchbox')

@inject('helper', \App\Libraries\Helper)

<!-- Separation -->
<hr/>

<div class="dataWrppr">
    <div class="container">

      <!-- Separation -->
      <hr/>

      @foreach($rcrds as $key => $rcrd)
        @foreach($clmnNmes as $key => $clmnNme)

          <!-- string contains a \ that may indicate a file path -->
          @if ((strpos($rcrd->$clmnNme, '\\') !== FALSE) && (strpos($clmnNme, 'index') == false))
            <!-- check if string contains a ^ may indicate that multiple files exists-->
            @if (strpos($rcrd->$clmnNme, '^') !== FALSE)

              @php
                $filesArray = $helper->separateFiles($rcrd->$clmnNme)
              @endphp

              @if (count($filesArray) > 0)
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <h4><b>{{$clmnNme}}</b>:
                    @for ($arrayPos = 0; $arrayPos < count($filesArray); $arrayPos++)
                      </br>{{$filesArray[$arrayPos]}}

                      <!-- check filename to see it it exists in $fileList -->
                      <!-- if found show a View link -->

                      @if ($helper->fileExists($tblNme, $filesArray[$arrayPos]))
                        <a href="{{ url('/data', ['curTable' => $tblId, 'view' => 'view', 'filename' => $filesArray[$arrayPos]])}}">
                          <span> View</span>
                        </a>
                      @endif
                    @endfor
                  </h4>
                </div>
              @endif
            @else
              <div class="col-xs-12 col-sm-12 col-md-12">
                <h4><b>{{$clmnNme}}</b>: {{$rcrd->$clmnNme}}

                <!-- check filename to see it it exists in $fileList -->
                <!--  if found show a View link -->
                {{$filename = $helper->getFilename($rcrd->$clmnNme)}}
                @if ($helper->fileExists($tblNme, $filename))
                  <a href="{{ url('/data', ['curTable' => $tblId, 'view' => 'view', 'filename' => $filename])}}">
                    <span> View</span>
                  </a>
                @endif
                </h4>
              </div>
            @endif
          @else
            <div class="col-xs-12 col-sm-12 col-md-12">
              <h4><b>{{$clmnNme}}</b>: {{$rcrd->$clmnNme}}</h4>
            </div>
          @endif
        @endforeach
      @endforeach
   </div>
</div>

@endsection
