@extends('layouts.default')

<!-- Heading -->
@section('content')
<div class="headingWrapper">
  <!-- Heading -->
  <div class="container adminHeading" name="adminHeading" role="banner">
    <span class="text-center">
      <h1><a href="{{ url('/creator', $cmsID) }}">Select CMS Table(s)</a></h1>
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
                      <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="{{ url('/upload', ['curCol' => $cmsID]) }}">
                          {{ csrf_field() }}


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
