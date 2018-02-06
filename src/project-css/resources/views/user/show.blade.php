@extends('layouts.default')

<!-- Heading -->
@section('content')

<!-- Search engine -->
@include('user/searchbox')

@inject('strhelper', \App\Libraries\CustomStringHelper)

<!-- Separation -->
<hr/>

<div class="dataWrppr">
    <div class="container" role="main">

      <!-- Separation -->
      <hr/>

      @foreach($rcrds as $key => $rcrd)
        @foreach($clmnNmes as $key => $clmnNme)

          <!-- string contains a \ that may indicate a file path -->
          @if ((strpos($rcrd->$clmnNme, '\\') !== FALSE) && (strpos($clmnNme, 'index') == false))
            <!-- check if string contains a ^ may indicate that multiple files exists-->
            @if (strpos($rcrd->$clmnNme, '^') !== FALSE)

              @php
                $filesArray = $strhelper->separateFiles($rcrd->$clmnNme)
              @endphp

              @if (count($filesArray) > 0)
                <div class="col-xs-12 col-sm-12 col-md-12">
                  <h4><strong>{{$clmnNme}}</strong>:
                    @for ($arrayPos = 0; $arrayPos < count($filesArray); $arrayPos++)
                      </br>{{$filesArray[$arrayPos]}}

                      @if ($strhelper->fileExistsInFolder($tblNme, $filesArray[$arrayPos]))
                        <a href="{{ url('/data', ['curTable' => $tblId, 'recId' => $curId, 'view' => 'view', 'subfolder' => $strhelper->getFolderName($filesArray[$arrayPos]), 'filename' => $strhelper->getFilename($filesArray[$arrayPos])])}}">
                          <span> View</span>
                        </a>
                      @endif
                    @endfor
                  </h4>
                </div>
              @endif
            @else
              <div class="col-xs-12 col-sm-12 col-md-12">
                <h4><strong>{{$clmnNme}}</strong>: {{$rcrd->$clmnNme}}

                @if ($strhelper->fileExistsInFolder($tblNme, $rcrd->$clmnNme))
                  <a href="{{ url('/data', ['curTable' => $tblId, 'recId' => $curId, 'view' => 'view', 'subfolder' => $strhelper->getFolderName($rcrd->$clmnNme), 'filename' => $strhelper->getFilename($rcrd->$clmnNme)])}}">
                    <span> View</span>
                  </a>
                @endif
                </h4>
              </div>
            @endif
          <!-- string contains / may indicate a file path -->
          @elseif ((strpos($rcrd->$clmnNme, '/') !== FALSE) && (strpos($clmnNme, 'index') == false))
            <div class="col-xs-12 col-sm-12 col-md-12">
              <h4><strong>{{$clmnNme}}</strong>: {{$rcrd->$clmnNme}}</h4>
            </div>

            @php
              $filesArray = $strhelper->checkForFilenames($rcrd->$clmnNme)
            @endphp

            @if (count($filesArray) > 0)
              <div class="col-xs-12 col-sm-12 col-md-12">
                <h4><strong>Filename(s) detected in {{$clmnNme}}</strong>:
                  @for ($arrayPos = 0; $arrayPos < count($filesArray); $arrayPos++)
                    </br>{{$filesArray[$arrayPos]}}

                    @if ($strhelper->fileExists($tblNme, 'formletters/'.$filesArray[$arrayPos].'.txt'))
                      <a href="{{ url('/data', ['curTable' => $tblId, 'recId' => $curId, 'view' => 'view', 'subfolder' => 'formletters', 'filename' => $filesArray[$arrayPos].'.txt'])}}">
                        <span> View</span>
                      </a>
                    @endif
                  @endfor
                </h4>
              </div>
            @endif
          @else
            <div class="col-xs-12 col-sm-12 col-md-12">
              <h4><strong>{{$clmnNme}}</strong>: {{$rcrd->$clmnNme}}</h4>
            </div>
          @endif
        @endforeach
      @endforeach

      <div class="col-xs-12 col-sm-12 col-md-12">
          <a href="{{ url('/data', [$tblId]) }}" class="btn btn-primary">Return To Table</a>
      </div>

   </div>
</div>

@endsection
