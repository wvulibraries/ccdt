@extends('layouts.default')


@section('content')
<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('/home') }}">Dashboard</a></h2>
      <p>Please kindly select the collection and table to view the records</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>
<!-- Panels to show the current collections and tables within each collections -->
<div class="container cllctnWrppr">
  <!-- Show the panel of the collections -->
  <div class="col-xs-12 col-sm-12 col-md-12">
    <div class="panel-group" id="ColTablList">
      @foreach($cllctns as $curCllctn)
        <!-- check if the collection is enabled -->
        @if($curCllctn->isEnabled)
        <div class="panel panel-default formPanel">
          <div class="panel-heading"><a data-toggle="collapse" href="#cllctn{{$curCllctn->id}}PnlBdy" data-parent="#ColTablList"><h3>{{$curCllctn->clctnName}}</h3></a></div>
          <div id="cllctn{{$curCllctn->id}}PnlBdy" class="panel-collapse collapse">
            <ul class="list-group">
              @foreach($curCllctn->tables as $curTabl)
                <li class="list-group-item tblList">{{$curTabl->tblNme}}</li>
              @endforeach
            </ul>
          </div>
        </div>
        @endif
      @endforeach
    </div>
  </div>
</div>

@endsection
