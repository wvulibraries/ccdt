@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading">
    <span class="text-center">
      <h2><a href="{{ url('table') }}">Table(s)</a></h2>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Create -->
<div class="tableWrapper">
  <div class="container">

    <!-- Head Table Cards -->
    <a href="{{url('table/create')}}" data-toggle="modal">
      <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="colHeadCard">
          <div class="icon hidden-xs hidden-sm">
            <i class="glyphicon glyphicon-plus"></i>
          </div>
          <h4>Create Table(s)</h4>
        </div>
      </div>
    </a>

    <a href="{{url('table/load')}}" data-toggle="modal">
      <div class="col-xs-6 col-sm-6 col-md-6">
        <div class="colHeadCard">
          <div class="icon hidden-xs hidden-sm">
            <i class="glyphicon glyphicon-import"></i>
          </div>
          <h4>Load Data</h4>
        </div>
      </div>
    </a>

    <!-- Show existing tables -->
    @foreach($tbls as $key=>$tbl)
      <!-- Check for the access -->
      @if($tbl->hasAccess)
      <!-- SHow thw tables -->
        <div class="col-xs-12 col-sm-12 col-md-12">
          <div class="colCard">
            <!-- Display the collection name -->
            <div class="col-xs-6 col-sm-4 col-md-4">
              <p class="colCardName">{{$key+1}}. {{$tbl->tblNme}} belongs to {{$tbl->collection->clctnName}} Collection</p>
            </div>
            <!-- Options for the collection -->
            <div class="col-xs-6 col-sm-8 col-md-8">

              <!-- Option 1 Show Records  -->
              <div class="colCardOpts">
                <p>{{DB::table($tbl->tblNme)->count()}} <br/> Records</p>
              </div>
              <!-- Option 2 Load data  -->
              <div class="colCardOpts">
                <a href="#" data-toggle="modal" data-target="#rstrctAccsTbl{{$tbl->id}}">
                  <div class="icon hidden-xs hidden-sm">
                    <i class="glyphicon glyphicon-trash"></i>
                  </div>
                  <p>Disable</p>
                </a>
              </div>

            </div>
          </div>
        </div>
      @endif
    @endforeach

    <!-- Modals -->
    <!-- Restrict Access to Table -->
    <div id="rstrctAccsTbl{{$tbl->id}}" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h3 class="modal-title">Restrict Access to Table</h3>
          </div>

          <div class="modal-body">
            <p>
              Are you sure you want to restrict access to <b>{{$tbl->tblNme}}</b> table?
            </p>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('table/restrict') }}">
                {{ csrf_field() }}

                <input id="id" name="id" type="hidden" value="{{$tbl->id}}" />

                <div class="form-group{{ $errors->has('tblNme') ? ' has-error' : '' }}">
                  <div class="modal-footer">
                    <div class="col-md-offset-8 col-md-2">
                          <button type="submit" class="btn btn-primary">
                              Confirm
                          </button>
                        </div>
                        <div class="col-md-2">
                          <button type="button" class="btn btn-primary" data-dismiss="modal">
                            Close
                          </button>
                      </div>
                  </div>
                </div>
            </form>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>


@endsection
