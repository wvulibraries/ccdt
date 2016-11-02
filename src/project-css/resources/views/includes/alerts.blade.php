<!-- Alerts to show the errors and notifications -->
@if(count($errors) > 0)
  @foreach ($errors->all() as $error)
  <div class="alert alert-danger">
    {{ $error }}
  </div>
  @endforeach
@endif
