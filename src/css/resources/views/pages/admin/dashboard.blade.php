@extends('layouts.sidemenu')

@section('content')
  <div class="row">
    {{Request::url()}}
  </div>
@stop
