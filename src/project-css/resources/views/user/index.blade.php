@extends('layouts.default')

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

<div class="container clckSlctWrppr">
  <div class="row">
    @if(count($cllctns)>0)
      @foreach($cllctns as $curCllctn)
        @if($curCllctn->isEnabled)
        <div class="col-xs-12 col-sm-12 col-md-12">

          <div class="collctnTblWrppr">

            <div class="cllctnCntr">
              <h3>
                <span class="cllcntIcn"><i class="glyphicon glyphicon-book"></i></span>
                <span class="cllcntHdr">{{$curCllctn->clctnName}}</span>
              </h3>
            </div>

            <hr/>

            <div class="tblCntr">
              @if(count($curCllctn->tables)>0)
                @foreach($curCllctn->tables as $curTabl)
                <a href="{{ url('/data',['tableId' => $curTabl->id]) }}">
                  <div class="col-xs-4">
                    <div class="well dashCard">
                      <div class="dashCardHeading">
                        <span class="glyphicon glyphicon-duplicate visible-xs visible-sm smallIcon"></span>
                        <label>{{$curTabl->tblNme}}</label>
                      </div>
                      <div class="icon hidden-xs hidden-sm">
                        <i class="glyphicon glyphicon-duplicate"></i>
                      </div>
                      <div class="desc hidden-xs">
                        <var>{{DB::table($curTabl->tblNme)->count()}}</var>
                        <label class="text-muted">Record(s)</label>
                      </div>
                    </div>
                  </div>
                </a>
                @endforeach
              @else
                <p class="text-center">No tables exist.</p>
              @endif
            </div>

          </div>

        </div>
        @else
          <p class="text-center">One of the collection is disabled.</p>
        @endif
      @endforeach
    @else
      <p class="text-center">No collections exist.</p>
    @endif

  </div>
</div>


@endsection
