<!-- Alerts to show the errors and notifications -->
@if(count($errors) > 0)
  <div class="alert alert-danger alrtArea" data-toggle="collapse" data-target="#alrtsWrappr">
    <h4>One or more errors exists. Please click to reveal.</h4>
  </div>

  <div id="alrtsWrappr" class="collapse">
    @foreach ($errors->all() as $error)
      <div class="alert alert-danger alrtArea">
        <h4>{{ $error }}</h4>
      </div>
    @endforeach
  </div>
@endif
