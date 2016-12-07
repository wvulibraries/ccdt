<!-- Alerts to show the errors and notifications -->
@if(count($errors) > 0)
  @foreach ($errors->all() as $error)
  <div class="alert alert-danger alrtArea">
    <h4>{{ $error }}</h4>
  </div>
  @endforeach
@endif
