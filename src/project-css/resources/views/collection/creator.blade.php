@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" name="adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('/creator', $colID) }}">Select CMS Table(s)</a></h1>
      <p>Please add tables to generate a CMS View</p>
    </span>
  </div>
</div>

<!-- Separation -->
<hr/>

<div class="container" role="main">
  
    @include('admin/messages')

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="panel panel-default formPanel">
                <div class="panel-heading"><h2>Create CMS View for {{$clctnName}} Collection</h2></div>
                  <div class="panel-body">
                      <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{ url('/collection/creator', ['curCol' => $colID]) }}">
                          {{ csrf_field() }}

                          <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">CMS View Name</label>

                            <div class="col-md-6">
                                <input id="name" type="name" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            </div>
                          </div>

                          <div class="form-group{{ $errors->has('tbls') ? ' has-error' : '' }}">
                            <label for="tblmain" class="col-md-4 control-label">Select Main Table</label>
                            <div class="col-md-6">
                              <select id="tblmain" type="text" class="form-control" name="tblmain" value="{{ old('tblmain') }}" required autofocus>
                                @foreach($tbls as $key => $tbl)
                                  @if($tbl->hasAccess)
                                    <option value="{{$tbl->id}}"
                                    >{{$tbl->tblNme}}</option>
                                  @endif
                                @endforeach                               
                              </select>
                            </div>
                          </div>   

                          <div class="form-group{{ $errors->has('tbls') ? ' has-error' : '' }}">
                            <label for="tbl2nd" class="col-md-4 control-label">Select 2nd Table</label>
                            <div class="col-md-6">
                              <select id="tbl2nd" type="text" class="form-control" name="tbl2nd" value="{{ old('tbl2nd') }}" required autofocus>
                                @foreach($tbls as $key => $tbl)
                                  @if($tbl->hasAccess)
                                    <option value="{{$tbl->id}}"
                                    >{{$tbl->tblNme}}</option>
                                  @endif
                                @endforeach                               
                              </select>
                            </div>
                          </div>                                                    

                          <div class="form-group">
                              <div class="col-md-6 col-md-offset-6">
                                  <button type="submit" class="btn btn-primary">
                                      Create
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
