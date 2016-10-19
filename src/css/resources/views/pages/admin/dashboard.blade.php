@extends('layouts.sidemenu')

@section('content')
  <div class="container-fluid">
    {{Request::url()}}
  </div>
@stop
