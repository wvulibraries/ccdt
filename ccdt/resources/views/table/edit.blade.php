@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('table/edit') }}">Edit Table</a></h1>
      <p>Verify or Update the following items</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<!-- Form to edit the table -->
<div class="container" role="main">
  <h2 class="text-center">Confirm or Update Table Item(s)</h2>
  <!-- Separator -->
  <hr/>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default formPanel">
                <div class="panel-heading"><h2>Update Table</h2></div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/table/update') }}">
                        {{ csrf_field() }}
                        <input id="tblId" name="tblId" type="hidden" value="{{ $tblId }}">

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ $tblNme }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('colID') ? ' has-error' : '' }}">
                            <label for="colID2" class="col-md-4 control-label">Select Collection</label>
                            <div class="col-md-6">
                              <select id="colID2" type="text" class="form-control" name="colID" value="{{ old('colID') }}" required autofocus>
                                @foreach($collcntNms as $collcntNm)
                                  @if($collcntNm->isEnabled)
                                    <option value="{{$collcntNm->id}}"
                                    @if ($collcntNm->id == $colID)
                                        selected="selected"
                                    @endif
                                    >{{$collcntNm->clctnName}}</option>
                                  @endif
                                @endforeach                               
                              </select>
                            </div>
                        </div>                        

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
