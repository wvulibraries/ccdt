@extends('layouts.sidemenu')

@section('content')
<div class="container-fluid">
  <div class="text-center">
    <h2 class="text-center">Import Data</h2>
    <p>This wizard will help you to import the data to collection.</p>
  </div>
  <hr>
  <h3>Step 1: Create/Select Collection</h3>

  <form method="POST" action="importData/collection" class="form-inline">
    <input type="hidden" name="_token" value="{{csrf_token()}}"/>
    <div class="form-group" role="form">
      <label for="collectionName">Create a new collection:</label>
      <input type="text" class="form-control" name="collectionName" id="collectionName">
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-default">Create</button>
    </div>
  </form>

  <div class="text-center">
    <h3>--or--</h3>
  </div>

  <form role="form">
    <div class="form-group" role="form">
      <div class="list-group">
        <label for="collectionRadio">Select a collection</label>
        <div class="radio list-group-item" id="collectionRadio">
          <label><input type="radio" name="collectionName"> Collection 1</label>
        </div>
        <div class="radio list-group-item">
          <label><input type="radio" name="collectionName"> Collection 2</label>
        </div>
      </div>
    </div>
    <div class="form-group pull-right">
      <button type="submit" class="btn btn-primary">Next</button>
    </div>
  </form>

</div>
@stop
